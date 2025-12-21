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
                s.position,
                s.branch_id,
                b.ti_agent_code as branch_code
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
                    $this->line("  <fg=green>+</> Created: {$mapUser['username']} ({$mapUser['email']})");
                } elseif ($result === 'updated') {
                    $updated++;
                    $this->line("  <fg=yellow>~</> Updated: {$mapUser['username']} ({$mapUser['email']})");
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
        // Skip inactive users that don't exist in eForm yet (don't create new inactive users)
        $isActiveInMap = (bool) $mapUser['is_active'];

        // Check if user exists
        $existingUser = User::withTrashed()
            ->where('map_user_id', $mapUser['map_user_id'])
            ->orWhere('username', $mapUser['username'])
            ->orWhere('email', $mapUser['email'])
            ->first();

        // Determine if we need to create or update
        $isNew = !$existingUser;

        // Skip if user is inactive in MAP and doesn't exist in eForm
        if (!$isActiveInMap && $isNew) {
            return 'skipped';
        }

        // Determine expected status based on MAP is_active
        $expectedStatus = $isActiveInMap ? 'active' : 'inactive';

        // Check if anything changed
        if ($existingUser && !$existingUser->trashed()) {
            $hasChanges =
                $existingUser->email !== $mapUser['email'] ||
                $existingUser->first_name !== $mapUser['first_name'] ||
                $existingUser->last_name !== $mapUser['last_name'] ||
                $existingUser->map_position !== ($mapUser['position'] ?? null) ||
                $existingUser->role !== $this->mapPositionToRole($mapUser['position'] ?? '1') ||
                $existingUser->status !== $expectedStatus;

            if (!$hasChanges) {
                return 'skipped';
            }
        }

        if ($dryRun) {
            return $isNew ? 'created' : 'updated';
        }

        // Restore if trashed
        if ($existingUser && $existingUser->trashed()) {
            $existingUser->restore();
        }

        // Map position to role
        $role = $this->mapPositionToRole($mapUser['position'] ?? '1');

        // Resolve branch ID
        $branchId = $this->resolveBranchId($mapUser['branch_id'], $mapUser['branch_code']);

        // Update or create
        $user = User::withTrashed()->updateOrCreate(
            ['map_user_id' => $mapUser['map_user_id']],
            [
                'username' => $mapUser['username'],
                'email' => $mapUser['email'],
                'first_name' => $mapUser['first_name'],
                'last_name' => $mapUser['last_name'],
                'map_position' => $mapUser['position'] ?? '1',
                'role' => $role,
                'branch_id' => $branchId,
                'status' => $isActiveInMap ? 'active' : 'inactive',
                'map_last_sync' => now(),
                'is_map_synced' => true,
                'deleted_at' => null,
                'password' => $existingUser?->password ?? bcrypt(Str::random(32)),
            ]
        );

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
    private function resolveBranchId(?int $mapBranchId, ?string $branchCode): ?int
    {
        if (!$mapBranchId && !$branchCode) {
            return null;
        }

        // Try to find by branch code (ti_agent_code)
        if ($branchCode) {
            $branch = Branch::where('ti_agent_code', $branchCode)->first();
            if ($branch) {
                return $branch->id;
            }
        }

        // Fallback to direct ID if branches match between systems
        return $mapBranchId;
    }

    /**
     * Get MAP database path
     */
    private function getMapDatabasePath(): string
    {
        return config(
            'map.database_path',
            base_path('../FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3')
        );
    }
}
