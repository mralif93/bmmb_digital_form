<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MigrateMapUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:migrate-users 
                            {--dry-run : Run without making changes}
                            {--filter= : Only migrate positions (comma-separated: 1,2,3)}
                            {--batch=100 : Number of users to process per batch}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all users from MAP database to eform database (Production-ready with batching)';

    /**
     * MAP database connection details
     */
    private $mapDb;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $positionFilter = $this->option('filter') ? explode(',', $this->option('filter')) : null;
        $batchSize = (int) $this->option('batch');
        $force = $this->option('force');

        $this->info('=== MAP User Migration (Production-Ready) ===');
        $this->info('');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->info('');
        }

        // Connect to MAP database
        if (!$this->connectToMapDatabase()) {
            return 1;
        }

        // Get total count (memory efficient)
        $totalUsers = $this->getTotalUserCount($positionFilter);

        if ($totalUsers === 0) {
            $this->error('No MAP users found to migrate');
            return 1;
        }

        $this->info("Found {$totalUsers} MAP users to migrate");
        $this->info("Batch size: {$batchSize} users per batch");
        $this->info("Estimated memory usage: ~" . round(($batchSize * 10) / 1024, 2) . " MB per batch");
        $this->info('');

        // Confirm before proceeding
        if (!$isDryRun && !$force && !$this->confirm('Do you want to proceed with migration?')) {
            $this->info('Migration cancelled');
            return 0;
        }

        // Migrate users in batches
        $stats = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        $progressBar = $this->output->createProgressBar($totalUsers);
        $progressBar->start();

        $offset = 0;
        $startTime = microtime(true);

        while ($offset < $totalUsers) {
            // Get batch of users
            $mapUsers = $this->getMapUsersBatch($positionFilter, $batchSize, $offset);

            if ($mapUsers->isEmpty()) {
                break;
            }

            // Process batch
            foreach ($mapUsers as $mapUser) {
                try {
                    $result = $this->migrateUser($mapUser, $isDryRun);
                    $stats[$result]++;
                } catch (\Exception $e) {
                    $stats['errors']++;
                    $this->error("\nError migrating user {$mapUser->username}: " . $e->getMessage());
                }
                $progressBar->advance();
            }

            $offset += $batchSize;

            // Free memory after each batch
            unset($mapUsers);
            gc_collect_cycles();
        }

        $progressBar->finish();
        $this->info("\n");

        $duration = round(microtime(true) - $startTime, 2);
        $this->info("Migration completed in {$duration} seconds");
        $this->info('');

        // Display summary
        $this->displaySummary($stats, $isDryRun);

        return 0;
    }

    /**
     * Connect to MAP SQLite database
     */
    private function connectToMapDatabase(): bool
    {
        $mapDbPath = base_path('../FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3');

        if (!file_exists($mapDbPath)) {
            $this->error("MAP database not found at: {$mapDbPath}");
            $this->error('Please update the path in the command file');
            return false;
        }

        // Configure MAP database connection
        config([
            'database.connections.map' => [
                'driver' => 'sqlite',
                'database' => $mapDbPath,
                'prefix' => '',
                'foreign_key_constraints' => true,
            ]
        ]);

        try {
            DB::connection('map')->getPdo();
            $this->info("✓ Connected to MAP database");
            return true;
        } catch (\Exception $e) {
            $this->error("Failed to connect to MAP database: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total count of MAP users (memory efficient)
     */
    private function getTotalUserCount($positionFilter = null): int
    {
        $query = DB::connection('map')
            ->table('user_user as u')
            ->join('user_staffprofile as sp', 'u.id', '=', 'sp.user_id')
            ->whereNotNull('u.email')
            ->where('u.is_active', 1);

        if ($positionFilter) {
            $query->whereIn('sp.position', $positionFilter);
        }

        return $query->count();
    }

    /**
     * Get batch of MAP users with staff profiles
     */
    private function getMapUsersBatch($positionFilter, int $limit, int $offset)
    {
        $query = DB::connection('map')
            ->table('user_user as u')
            ->join('user_staffprofile as sp', 'u.id', '=', 'sp.user_id')
            ->leftJoin('Application_branch as b', 'sp.branch_id', '=', 'b.id')
            ->select(
                'u.id as map_user_id',
                'u.username',
                'u.email',
                'u.first_name',
                'u.last_name',
                'sp.id as staff_id',
                'sp.position',
                'sp.branch_id',
                'b.title as branch_name',
                'b.ti_agent_code'
            )
            ->whereNotNull('u.email')
            ->where('u.is_active', 1);

        if ($positionFilter) {
            $query->whereIn('sp.position', $positionFilter);
        }

        return $query->limit($limit)->offset($offset)->get();
    }

    /**
     * Get all MAP users with staff profiles (kept for backward compatibility)
     */
    private function getMapUsers($positionFilter = null)
    {
        return $this->getMapUsersBatch($positionFilter, PHP_INT_MAX, 0);
    }

    /**
     * Migrate single user
     */
    private function migrateUser($mapUser, $isDryRun): string
    {
        // Map MAP position to eform role
        $role = $this->mapPositionToRole($mapUser->position);

        // Check if user already exists
        $existingUser = User::where('map_user_id', $mapUser->map_user_id)
            ->orWhere('email', $mapUser->email)
            ->first();

        if ($isDryRun) {
            if ($existingUser) {
                return 'updated';
            } else {
                return 'created';
            }
        }

        // Handle empty email - generate unique email
        $email = $mapUser->email;
        if (empty($email)) {
            // Generate unique email based on username and map_user_id
            $email = strtolower($mapUser->username) . '_' . $mapUser->map_user_id . '@map.muamalat.local';
        }

        $userData = [
            'map_user_id' => $mapUser->map_user_id,
            'map_staff_id' => $mapUser->staff_id,
            'username' => $mapUser->username,
            'email' => $email,
            'first_name' => $mapUser->first_name,
            'last_name' => $mapUser->last_name,
            'map_position' => $mapUser->position,
            'role' => $role,
            'branch_id' => $mapUser->branch_id,
            'status' => 'active',
            'map_last_sync' => now(),
            'is_map_synced' => true,
            'password' => Hash::make(Str::random(32)), // Random password
        ];

        if ($existingUser) {
            // Update existing user
            $existingUser->update($userData);
            return 'updated';
        } else {
            // Create new user
            User::create($userData);
            return 'created';
        }
    }

    /**
     * Map MAP position to eform role
     */
    private function mapPositionToRole(string $position): string
    {
        return match ($position) {
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
     * Display migration summary
     */
    private function displaySummary(array $stats, bool $isDryRun)
    {
        $this->info('=== Migration Summary ===');
        $this->info('');
        $this->line("Created: {$stats['created']}");
        $this->line("Updated: {$stats['updated']}");
        $this->line("Skipped: {$stats['skipped']}");

        if ($stats['errors'] > 0) {
            $this->error("Errors: {$stats['errors']}");
        }

        $this->info('');
        $this->info("Total processed: " . array_sum($stats));

        if ($isDryRun) {
            $this->warn('');
            $this->warn('This was a DRY RUN - no changes were made');
            $this->warn('Run without --dry-run to perform actual migration');
        }
    }
}
