<?php

namespace App\Console\Commands;

use App\Models\Branch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MigrateBranches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:migrate-branches 
                            {--dry-run : Run without making changes}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all branches from MAP database to eform database';

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
        $force = $this->option('force');

        $this->info('=== MAP Branch Migration ===');
        $this->info('');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->info('');
        }

        // Connect to MAP database
        if (!$this->connectToMapDatabase()) {
            return 1;
        }

        // Get all MAP branches
        $mapBranches = $this->getMapBranches();

        if ($mapBranches->isEmpty()) {
            $this->error('No MAP branches found to migrate');
            return 1;
        }

        $this->info("Found {$mapBranches->count()} MAP branches to migrate");
        $this->info('');

        // Confirm before proceeding
        if (!$isDryRun && !$force && !$this->confirm('Do you want to proceed with migration?')) {
            $this->info('Migration cancelled');
            return 0;
        }

        // Migrate branches
        $stats = [
            'created' => 0,
            'updated' => 0,
            'errors' => 0,
        ];

        $progressBar = $this->output->createProgressBar($mapBranches->count());
        $progressBar->start();

        foreach ($mapBranches as $mapBranch) {
            try {
                $result = $this->migrateBranch($mapBranch, $isDryRun);
                $stats[$result]++;
            } catch (\Exception $e) {
                $stats['errors']++;
                $this->error("\nError migrating branch {$mapBranch->branch_name}: " . $e->getMessage());
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info("\n");

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
     * Get all MAP branches
     */
    private function getMapBranches()
    {
        return DB::connection('map')
            ->table('Application_branch')
            ->select(
                'id as map_branch_id',
                'title as branch_name',
                'weekend_start_day',
                'ti_agent_code',
                'address',
                'email',
                'state_id',
                'region_id'
            )
            ->get();
    }

    /**
     * Migrate single branch
     */
    private function migrateBranch($mapBranch, $isDryRun): string
    {
        // Check if branch already exists
        $existingBranch = Branch::where('id', $mapBranch->map_branch_id)
            ->orWhere('branch_name', $mapBranch->branch_name)
            ->first();

        if ($isDryRun) {
            return $existingBranch ? 'updated' : 'created';
        }

        $branchData = [
            'branch_name' => $mapBranch->branch_name,
            'weekend_start_day' => $mapBranch->weekend_start_day,
            'ti_agent_code' => $mapBranch->ti_agent_code,
            'address' => $mapBranch->address,
            'email' => $mapBranch->email,
            'state_id' => $mapBranch->state_id,
            'region_id' => $mapBranch->region_id,
        ];

        if ($existingBranch) {
            // Update existing branch
            $existingBranch->update($branchData);
            return 'updated';
        } else {
            // Create new branch with MAP ID
            Branch::create(array_merge(['id' => $mapBranch->map_branch_id], $branchData));
            return 'created';
        }
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
