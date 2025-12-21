<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SyncMapUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:sync-users 
                            {--branch=* : Specific branch IDs to sync}
                            {--position=* : Specific MAP positions to sync (1=HQ, 2=BM, 3=CFE)}
                            {--dry-run : Show what would be synced without making changes}
                            {--force : Force sync even if recently synced}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync users from MAP API to eForm database';

    /**
     * MAP API endpoint for getting all users
     */
    private const MAP_USERS_API = 'https://map.muamalat.com.my/api/eform/users/';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting MAP user sync...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $branchFilter = $this->option('branch');
        $positionFilter = $this->option('position');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Try to fetch users from MAP API
        $mapUsers = $this->fetchMapUsers();

        if ($mapUsers === null) {
            $this->error('Failed to fetch users from MAP API. Check logs for details.');
            $this->info('Falling back to local MAP-synced users update...');

            // Fallback: Update roles for existing MAP users based on their map_position
            return $this->updateExistingMapUsers($dryRun);
        }

        // Filter users if needed
        if (!empty($branchFilter)) {
            $mapUsers = array_filter($mapUsers, fn($u) => in_array($u['branch_id'] ?? null, $branchFilter));
        }

        if (!empty($positionFilter)) {
            $mapUsers = array_filter($mapUsers, fn($u) => in_array($u['position'] ?? '', $positionFilter));
        }

        $this->info('Found ' . count($mapUsers) . ' users to sync');
        $this->newLine();

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar(count($mapUsers));
        $progressBar->start();

        foreach ($mapUsers as $mapUser) {
            try {
                $result = $this->syncUser($mapUser, $dryRun, $force);

                switch ($result) {
                    case 'created':
                        $created++;
                        break;
                    case 'updated':
                        $updated++;
                        break;
                    case 'skipped':
                        $skipped++;
                        break;
                }
            } catch (\Exception $e) {
                $errors++;
                Log::error('Error syncing user', [
                    'map_user_id' => $mapUser['id'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
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
     * Fetch users from MAP API
     */
    private function fetchMapUsers(): ?array
    {
        try {
            // Get API credentials from config
            $apiKey = config('map.api_key');
            $apiSecret = config('map.api_secret');

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'X-API-Secret' => $apiSecret,
                ])
                ->get(self::MAP_USERS_API);

            if (!$response->successful()) {
                Log::warning('MAP API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            return $response->json('users') ?? $response->json();

        } catch (\Exception $e) {
            Log::error('Error fetching MAP users', [
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Sync a single user from MAP data
     */
    private function syncUser(array $mapUser, bool $dryRun, bool $force): string
    {
        // Check if user exists
        $existingUser = User::where('map_user_id', $mapUser['id'])->first();

        // Skip if recently synced (within last hour) unless forced
        if ($existingUser && !$force) {
            $lastSync = $existingUser->map_last_sync;
            if ($lastSync && Carbon::parse($lastSync)->diffInHours(now()) < 1) {
                return 'skipped';
            }
        }

        if ($dryRun) {
            return $existingUser ? 'updated' : 'created';
        }

        // Map position to role
        $role = $this->mapPositionToRole($mapUser['position'] ?? '1');

        // Resolve branch_id
        $branchId = $this->resolveBranchId($mapUser['branch_id'] ?? null, $mapUser['branch_code'] ?? null);

        $user = User::updateOrCreate(
            ['map_user_id' => $mapUser['id']],
            [
                'map_staff_id' => $mapUser['staff_id'] ?? null,
                'username' => $mapUser['username'] ?? null,
                'email' => $mapUser['email'],
                'first_name' => $mapUser['first_name'],
                'last_name' => $mapUser['last_name'],
                'map_position' => $mapUser['position'] ?? '1',
                'role' => $role,
                'branch_id' => $branchId,
                'phone' => $mapUser['phone'] ?? null,
                'status' => $mapUser['is_active'] ?? true ? 'active' : 'inactive',
                'map_last_sync' => now(),
                'is_map_synced' => true,
                'password' => $existingUser?->password ?? bcrypt(Str::random(32)),
            ]
        );

        return $user->wasRecentlyCreated ? 'created' : 'updated';
    }

    /**
     * Update existing MAP users (fallback when API is unavailable)
     */
    private function updateExistingMapUsers(bool $dryRun): int
    {
        $users = User::whereNotNull('map_user_id')
            ->where('is_map_synced', true)
            ->get();

        $this->info('Found ' . $users->count() . ' existing MAP users');
        $this->newLine();

        $updated = 0;

        foreach ($users as $user) {
            $oldRole = $user->role;
            $newRole = $this->mapPositionToRole($user->map_position ?? '1');

            if ($oldRole !== $newRole) {
                $this->line("  User {$user->email}: {$oldRole} → {$newRole}");

                if (!$dryRun) {
                    $user->role = $newRole;
                    $user->map_last_sync = now();
                    $user->save();
                }

                $updated++;
            }
        }

        $this->newLine();
        $this->info("Updated {$updated} users' roles based on MAP position");

        if ($dryRun && $updated > 0) {
            $this->warn('This was a dry run. Run without --dry-run to apply changes.');
        }

        return Command::SUCCESS;
    }

    /**
     * Map MAP position code to eform role
     */
    private function mapPositionToRole(string $mapPosition): string
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
     * Resolve branch ID from either direct ID or branch code
     */
    private function resolveBranchId(?int $branchId, ?string $branchCode): ?int
    {
        if ($branchId) {
            return $branchId;
        }

        if ($branchCode) {
            $branch = Branch::where('ti_agent_code', $branchCode)->first();
            return $branch?->id;
        }

        return null;
    }
}
