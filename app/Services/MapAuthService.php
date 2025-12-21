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

        $user = User::updateOrCreate(
            ['map_user_id' => $mapData['id']],
            [
                'map_staff_id' => $mapData['staff_id'],
                'username' => $mapData['username'],
                'email' => $mapData['email'],
                'first_name' => $mapData['first_name'],
                'last_name' => $mapData['last_name'],
                'map_position' => $mapData['position'],
                'role' => $role,
                'branch_id' => $mapData['branch_id'] ?? null,
                'phone' => $mapData['phone'] ?? null,
                'status' => 'active',
                'map_last_sync' => Carbon::now(),
                'is_map_synced' => true,
                'last_login_at' => Carbon::now(),
                // Don't overwrite password - MAP users don't have local passwords
                'password' => $user->password ?? bcrypt(\Illuminate\Support\Str::random(32)),
            ]
        );

        return $user;
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
            '1' => 'headquarters', // HQ → headquarters
            '2' => 'branch_manager', // Branch Manager → branch_manager
            '3' => 'cfe', // CFE → Customer Finance Executive
            '4' => 'headquarters', // COD → headquarters
            '9' => 'operation_officer', // Operation Officer → operation_officer
            '10' => 'operation_officer', // OO → operation_officer
            default => 'headquarters', // Default to headquarters
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