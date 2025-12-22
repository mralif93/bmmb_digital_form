<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PDO;

class SyncMapUsersFromDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:sync-from-db 
                            {--dry-run : Show what would be synced without making changes}
                            {--username= : Sync specific user by username}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync users directly from MAP database to eForm';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting MAP database sync...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $specificUser = $this->option('username');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Connect to MAP database
        $mapDbPath = $this->getMapDatabasePath();

        if (!file_exists($mapDbPath)) {
            $this->error("MAP database not found at: {$mapDbPath}");
            $this->info('Please set MAP_DATABASE_PATH in .env or ensure the path is correct.');
            return Command::FAILURE;
        }

        try {
            $mapDb = new PDO("sqlite:{$mapDbPath}");
            $mapDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $e) {
            $this->error("Failed to connect to MAP database: " . $e->getMessage());
            return Command::FAILURE;
        }

        // Build query - include all users (active and inactive)
        // Inactive users will have their status set to 'inactive' in eForm
        $sql = "
            SELECT 
                u.id as map_user_id,
                u.username,
                u.email,
                u.first_name,
                u.last_name,
                u.is_active,
                u.is_superuser,
                s.position,
                s.branch_id,
                b.ti_agent_code as branch_code,
                b.title as branch_name
            FROM user_user u
            LEFT JOIN user_staffprofile s ON u.id = s.user_id
            LEFT JOIN Application_branch b ON s.branch_id = b.id
            WHERE 1=1
        ";

        if ($specificUser) {
            $sql .= " AND u.username = :username";
        }

        $stmt = $mapDb->prepare($sql);

        if ($specificUser) {
            $stmt->bindValue(':username', $specificUser);
        }

        $stmt->execute();
        $mapUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($mapUsers)) {
            if ($specificUser) {
                $this->warn("User '{$specificUser}' not found in MAP database");
            } else {
                $this->warn("No active users found in MAP database");
            }
            return Command::SUCCESS;
        }

        $this->info('Found ' . count($mapUsers) . ' users to sync');
        $this->newLine();

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($mapUsers as $mapUser) {
            try {
                $result = $this->syncUser($mapUser, $dryRun);

                if ($result === 'created') {
                    $created++;
                    $this->line("  <fg=green>+</> Created: {$mapUser['username']} (" . ($mapUser['email'] ?: 'no email') . ")");
                } elseif ($result === 'updated') {
                    $updated++;
                    $this->line("  <fg=yellow>~</> Updated: {$mapUser['username']} (" . ($mapUser['email'] ?: 'no email') . ")");
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("  Error syncing {$mapUser['username']}: " . $e->getMessage());
                Log::error('MAP sync error', [
                    'username' => $mapUser['username'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->newLine();
        $this->info('Sync completed!');
        $this->table(
            ['Action', 'Count'],
            [
                ['Created', $created],
                ['Updated', $updated],
                ['Skipped', $skipped],
                ['Errors', $errors],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->warn('This was a dry run. Run without --dry-run to apply changes.');
        }

        return Command::SUCCESS;
    }

    /**
     * Sync a single user
     */
    private function syncUser(array $mapUser, bool $dryRun): string
    {
        // Trim data to ensure clean matching
        $username = trim($mapUser['username']);
        $email = trim($mapUser['email']);
        $firstName = trim($mapUser['first_name']);
        $lastName = trim($mapUser['last_name']);

        // Skip inactive users that don't exist in eForm yet (don't create new inactive users)
        $isActiveInMap = (bool) $mapUser['is_active'];

        // 1. Find user by map_user_id (Primary Match)
        $existingUser = User::withTrashed()->where('map_user_id', $mapUser['map_user_id'])->first();

        // 2. If not found, find by username (Secondary Match)
        if (!$existingUser) {
            $existingUser = User::withTrashed()->where('username', $username)->first();
        }

        // NOTE: Do not match by email as it is not unique in MAP

        // Determine if we need to create or update
        $isNew = !$existingUser;

        // Skip if user is inactive in MAP and doesn't exist in eForm
        if (!$isActiveInMap && $isNew) {
            return 'skipped';
        }

        // Determine expected status based on MAP is_active
        $expectedStatus = $isActiveInMap ? 'active' : 'inactive';

        // Prepare email - Handle collisions
        // If email is empty string, set to null
        $emailToUse = !empty($email) ? $email : null;

        if ($emailToUse) {
            // Check if email is taken by a DIFFERENT user
            $emailTaken = User::withTrashed()
                ->where('email', $emailToUse)
                ->when($existingUser, function ($q) use ($existingUser) {
                    $q->where('id', '!=', $existingUser->id);
                })
                ->exists();

            if ($emailTaken) {
                // If collision happens, we set email to null since it's not unique
                $emailToUse = null;
                $this->warn("  ⚠ Email collision for {$username}: '{$email}' is taken. Setting email to NULL.");
            }
        }

        // Determine is_access_eform
        // Default logic: active users have access, unless restricted
        $isAccessEform = true;

        // Restriction: Superusers default to NO access, unless whitelisted
        $isSuperuser = !empty($mapUser['is_superuser']) && $mapUser['is_superuser'] == 1;
        if ($isSuperuser) {
            $whitelist = ['mralif93', 'naziha', 'zaki', 'zaid', 'digital'];
            if (!in_array($username, $whitelist)) {
                $isAccessEform = false;
            }
        }

        // Map position to role
        $role = $this->mapPositionToRole($mapUser['position'] ?? '1');

        // OVERRIDE: If is_superuser in MAP, force admin role
        if ($isSuperuser) {
            $role = 'admin';
        }

        // Resolve branch ID
        $branchId = $this->resolveBranchId($mapUser['branch_id'], $mapUser['branch_code'], $mapUser['branch_name'] ?? null);

        // Check if anything changed
        if ($existingUser && !$existingUser->trashed()) {

            // Check role logic:
            // If MAP says superuser, we expect 'admin'.
            // If MAP says normal user, but eForm has 'admin', we preserve 'admin' (manual override scenario)

            $checkRole = $role;
            if ($existingUser->role === 'admin') {
                $checkRole = 'admin'; // Expect 'admin' if already admin
            }

            $hasChanges =
                $existingUser->map_user_id != $mapUser['map_user_id'] ||
                $existingUser->email !== $emailToUse ||
                $existingUser->first_name !== $firstName ||
                $existingUser->last_name !== $lastName ||
                $existingUser->map_position !== ($mapUser['position'] ?? '') ||
                $existingUser->role !== $checkRole ||
                $existingUser->branch_id !== $branchId ||
                $existingUser->is_access_eform !== $isAccessEform ||
                $existingUser->status !== $expectedStatus;

            if (!$hasChanges) {
                return 'skipped';
            }
        }


        if ($dryRun) {
            return $isNew ? 'created' : 'updated';
        }

        if ($existingUser) {
            // Update existing user
            $existingUser->map_user_id = $mapUser['map_user_id'];
            $existingUser->username = $username;
            $existingUser->email = $emailToUse;
            $existingUser->first_name = $firstName;
            $existingUser->last_name = $lastName;
            $existingUser->map_position = $mapUser['position'] ?? '1';

            // Preserve 'admin' role if already assigned, OR if new role (from is_superuser) is admin
            if ($role === 'admin') {
                $existingUser->role = 'admin';
            } elseif ($existingUser->role !== 'admin') {
                $existingUser->role = $role;
            }

            $existingUser->branch_id = $branchId;
            $existingUser->status = $expectedStatus;
            $existingUser->map_last_sync = now();
            $existingUser->is_map_synced = true;
            $existingUser->is_access_eform = $isAccessEform;

            // Restore if trashed
            if ($existingUser->trashed()) {
                $existingUser->restore();
            }

            $existingUser->save();
        } else {
            // Create new user
            User::create([
                'map_user_id' => $mapUser['map_user_id'],
                'username' => $username,
                'email' => $emailToUse,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'map_position' => $mapUser['position'] ?? '1',
                'role' => $role,
                'branch_id' => $branchId,
                'status' => $expectedStatus,
                'map_last_sync' => now(),
                'is_map_synced' => true,
                'is_access_eform' => $isAccessEform,
                'password' => bcrypt(Str::random(32)),
            ]);
        }

        return $isNew ? 'created' : 'updated';
    }

    /**
     * Map MAP position code to eform role
     */
    private function mapPositionToRole(?string $mapPosition): string
    {
        return match ($mapPosition) {
            '1' => 'headquarters',
            '2' => 'branch_manager',
            '3' => 'cfe',
            '4' => 'headquarters',
            '9' => 'operation_officer',
            '10' => 'operation_officer',
            default => 'headquarters',
        };
    }

    /**
     * Resolve branch ID from MAP branch_id or branch_code
     */
    private function resolveBranchId(?int $mapBranchId, ?string $branchCode, ?string $branchName): ?int
    {
        if (!$mapBranchId && !$branchCode && !$branchName) {
            return null;
        }

        // 1. Try to find by branch code (most reliable unique identifier)
        if ($branchCode) {
            $branch = Branch::where('ti_agent_code', $branchCode)->first();
            if ($branch) {
                return $branch->id;
            }
        }

        // 2. Try to find by branch name (fallback if code is missing/empty)
        if ($branchName) {
            $branch = Branch::where('branch_name', $branchName)->first();
            if ($branch) {
                return $branch->id;
            }
        }

        // 3. Last fallback: direct ID check (if branches synced 1:1)
        if ($mapBranchId) {
            // Verify it actually exists to avoid FK error
            if (Branch::where('id', $mapBranchId)->exists()) {
                return $mapBranchId;
            }
        }

        return null;
    }

    /**
     * Get MAP database path
     */
    private function getMapDatabasePath(): string
    {
        $envPath = env('MAP_DATABASE_PATH');
        if ($envPath) {
            return $envPath;
        }

        return config(
            'map.database_path',
            base_path('../FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3')
        );
    }
}
