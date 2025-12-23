<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MapAuthService
{
    /**
     * MAP API endpoint for session verification
     */
    private const MAP_VERIFY_URL = 'https://map.muamalat.com.my/api/eform/verify/';

    /**
     * Timeout for MAP API requests (in seconds)
     */
    private const REQUEST_TIMEOUT = 10;

    /**
     * Verify user session with MAP and sync user data
     *
     * @param string $sessionCookie Session cookie from MAP login
     * @return User|null User object if verification successful, null otherwise
     */
    public function verifyAndSyncUser(string $sessionCookie): ?User
    {
        try {
            // Call MAP API to verify session
            $response = Http::timeout(self::REQUEST_TIMEOUT)
                ->withCookies(['sessionid' => $sessionCookie], parse_url(self::MAP_VERIFY_URL, PHP_URL_HOST))
                ->get(self::MAP_VERIFY_URL);

            if (!$response->successful()) {
                Log::warning('MAP session verification failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            $mapData = $response->json();

            // Validate required fields
            if (!$this->validateMapData($mapData)) {
                Log::error('Invalid MAP data received', ['data' => $mapData]);
                return null;
            }

            // Sync user data to local database
            $user = $this->syncUserToDatabase($mapData);

            Log::info('User synced from MAP', [
                'map_user_id' => $mapData['id'],
                'username' => $mapData['username'],
                'action' => $user->wasRecentlyCreated ? 'created' : 'updated'
            ]);

            return $user;

        } catch (\Exception $e) {
            Log::error('Error verifying/syncing user from MAP', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Validate MAP data has all required fields
     *
     * @param array $mapData Data from MAP API
     * @return bool True if valid, false otherwise
     */
    private function validateMapData(array $mapData): bool
    {
        $requiredFields = ['id', 'username', 'email', 'first_name', 'last_name', 'position', 'staff_id'];

        foreach ($requiredFields as $field) {
            if (!isset($mapData[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sync user data from MAP to local database
     *
     * @param array $mapData User data from MAP API
     * @return User Synced user model
     */
    private function syncUserToDatabase(array $mapData): User
    {
        // Map MAP position to eform role
        $role = $this->mapPositionToRole($mapData['position']);

        // Resolve Branch ID using robust logic (Code > Name > ID)
        $mapBranchId = $mapData['branch_id'] ?? null;
        $branchId = $this->resolveMapBranchId($mapBranchId);

        // Handle email attributes and collisions
        $username = trim($mapData['username']);
        // If email is empty string or null, set to null
        $email = !empty($mapData['email']) ? trim($mapData['email']) : null;
        $mapUserId = $mapData['id'];

        $emailToUse = $email;

        // Only check collision if we actually have an email
        if ($emailToUse) {
            // Check collision with DIFFERENT user
            $existingUser = User::withTrashed()->where('map_user_id', $mapUserId)->first();
            $emailTaken = User::withTrashed()
                ->where('email', $emailToUse)
                ->when($existingUser, function ($q) use ($existingUser) {
                    $q->where('id', '!=', $existingUser->id);
                })
                ->exists();

            if ($emailTaken) {
                // If collision happens, we set email to null since it's not unique
                // Alternatively, we could log warning, but for now null is safer than invalid sync or fake email per request
                $emailToUse = null;
                Log::warning("Email collision detected for user {$username} (MAP ID: {$mapUserId}). Email '{$email}' is taken. Setting email to NULL.");
            }
        }


        // OVERRIDE: If is_superuser in MAP (passed in mapData if available)
        $isSuperuser = !empty($mapData['is_superuser']) && $mapData['is_superuser'] == 1;
        if ($isSuperuser) {
            $role = 'admin';
        }

        // Determine is_access_eform
        // Default logic: active users have access, unless restricted
        $isAccessEform = true;

        // Restriction: Superusers default to NO access, unless whitelisted
        if ($isSuperuser) {
            $whitelist = ['mralif93', 'naziha', 'zaki', 'zaid', 'digital'];
            if (!in_array($username, $whitelist)) {
                $isAccessEform = false;
            }
        }

        // REFACTOR: Retrieve user first to check role preservation
        $existingUser = User::withTrashed()->where('map_user_id', $mapUserId)->first();
        if ($existingUser) {
            // Preserve 'admin' role if already assigned, OR if new role (from is_superuser) is admin
            if ($role === 'admin') {
                // Role is admin (either forced by IS_SUPERUSER or calculated)
            } elseif ($existingUser->role === 'admin') {
                $role = 'admin'; // Preserve existing admin role
            }
        }

        $user = User::updateOrCreate(
            ['map_user_id' => $mapUserId],
            [
                'map_staff_id' => $mapData['staff_id'],
                'username' => $username,
                'email' => $emailToUse,
                'first_name' => trim($mapData['first_name']),
                'last_name' => trim($mapData['last_name']),
                'map_position' => $mapData['position'],
                'role' => $role,
                'branch_id' => $branchId,
                'phone' => $mapData['phone'] ?? null,
                'status' => 'active',
                'map_last_sync' => Carbon::now(),
                'is_map_synced' => true,
                'is_access_eform' => $isAccessEform,
                'last_login_at' => Carbon::now(),
            ]
        );

        // Handle password for new users only (if password is null/empty)
        if (!$user->password) {
            $user->password = bcrypt(\Illuminate\Support\Str::random(32));
            $user->save();
        }

        return $user;
    }

    /**
     * Resolve MAP Branch ID to eForm Branch ID by looking up Code/Name from MAP DB.
     */
    public function resolveMapBranchId(?int $mapBranchId): ?int
    {
        if (!$mapBranchId) {
            return null;
        }

        // 1. Try to fetch branch details from MAP DB
        $mapBranchDetails = $this->getMapBranchDetails($mapBranchId);

        if ($mapBranchDetails) {
            $branchCode = $mapBranchDetails['ti_agent_code'] ?? null;
            $branchName = $mapBranchDetails['title'] ?? null;

            // 2. Try match by Code
            if ($branchCode) {
                $branch = \App\Models\Branch::where('ti_agent_code', $branchCode)->first();
                if ($branch)
                    return $branch->id;
            }

            // 3. Try match by Name
            if ($branchName) {
                $branch = \App\Models\Branch::where('branch_name', $branchName)->first();
                if ($branch)
                    return $branch->id;
            }
        }

        // 4. Fallback: match by ID (if exists)
        if (\App\Models\Branch::where('id', $mapBranchId)->exists()) {
            return $mapBranchId;
        }

        return null;
    }

    /**
     * Fetch branch details from MAP SQLite DB
     */
    private function getMapBranchDetails(int $mapBranchId): ?array
    {
        try {
            $dbPath = config('map.database_path', base_path('../FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3'));

            if (!file_exists($dbPath)) {
                return null;
            }

            $pdo = new \PDO("sqlite:{$dbPath}");
            $stmt = $pdo->prepare("SELECT ti_agent_code, title FROM Application_branch WHERE id = ?");
            $stmt->execute([$mapBranchId]);

            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            Log::warning("Failed to fetch MAP branch details: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Map MAP position code to eform role
     *
     * @param string $mapPosition MAP position code (1, 2, 3, etc.)
     * @return string Eform role
     */
    private function mapPositionToRole(string $mapPosition): string
    {
        return match ($mapPosition) {
            '1' => 'hq', // HQ
            '2' => 'bm', // BM → Branch Manager
            '3' => 'cfe', // CFE → Customer Finance Executive
            '4' => 'cod', // COD → Credit Operations Department
            '5' => 'crr', // CRR → Credit Risk Review
            '6' => 'cso', // CSO → Credit Support Officer
            '7' => 'cfe_hq', // CFE-HQ → Customer Finance Executive (HQ)
            '8' => 'ccq', // CCQ → Credit Control Quality
            '9' => 'abm', // ABM → Assistant Branch Manager
            '10' => 'oo', // OO → Operation Officer
            default => 'hq',
        };
    }

    /**
     * Check if session cookie exists in request
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null Session cookie value or null
     */
    public function getSessionCookie($request): ?string
    {
        return $request->cookie('sessionid');
    }
}