<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\State;
use App\Models\Region;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PDO;

class SyncMapBranchesFromDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:sync-branches 
                            {--dry-run : Show what would be synced without making changes}
                            {--include-regions : Also sync regions}
                            {--include-states : Also sync states}
                            {--all : Sync regions, states, and branches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync branches, states, and regions from MAP database to eForm';

    private PDO $mapDb;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting MAP data sync...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $syncAll = $this->option('all');
        $syncRegions = $syncAll || $this->option('include-regions');
        $syncStates = $syncAll || $this->option('include-states');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Connect to MAP database
        $mapDbPath = $this->getMapDatabasePath();

        if (!file_exists($mapDbPath)) {
            $this->error("MAP database not found at: {$mapDbPath}");
            return Command::FAILURE;
        }

        try {
            $this->mapDb = new PDO("sqlite:{$mapDbPath}");
            $this->mapDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $e) {
            $this->error("Failed to connect to MAP database: " . $e->getMessage());
            return Command::FAILURE;
        }

        // Sync in order: Regions → States → Branches
        if ($syncRegions) {
            $this->syncRegions($dryRun);
            $this->newLine();
        }

        if ($syncStates) {
            $this->syncStates($dryRun);
            $this->newLine();
        }

        $this->syncBranches($dryRun);

        $this->newLine();
        $this->info('✓ Sync completed!');

        return Command::SUCCESS;
    }

    /**
     * Sync regions from MAP
     */
    private function syncRegions(bool $dryRun): void
    {
        $this->info('Syncing Regions...');

        $stmt = $this->mapDb->query("SELECT id, name FROM Application_region ORDER BY id");
        $mapRegions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($mapRegions as $mapRegion) {
            $existing = Region::find($mapRegion['id']);

            if (!$existing) {
                if (!$dryRun) {
                    Region::create([
                        'id' => $mapRegion['id'],
                        'name' => $mapRegion['name'],
                    ]);
                }
                $created++;
                $this->line("  <fg=green>+</> Created: {$mapRegion['name']}");
            } elseif ($existing->name !== $mapRegion['name']) {
                if (!$dryRun) {
                    $existing->update(['name' => $mapRegion['name']]);
                }
                $updated++;
                $this->line("  <fg=yellow>~</> Updated: {$mapRegion['name']}");
            } else {
                $skipped++;
            }
        }

        $this->table(['Action', 'Count'], [
            ['Created', $created],
            ['Updated', $updated],
            ['Skipped', $skipped],
        ]);
    }

    /**
     * Sync states from MAP
     */
    private function syncStates(bool $dryRun): void
    {
        $this->info('Syncing States...');

        $stmt = $this->mapDb->query("SELECT id, name FROM Application_state ORDER BY id");
        $mapStates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($mapStates as $mapState) {
            $existing = State::find($mapState['id']);

            if (!$existing) {
                if (!$dryRun) {
                    State::create([
                        'id' => $mapState['id'],
                        'name' => $mapState['name'],
                    ]);
                }
                $created++;
                $this->line("  <fg=green>+</> Created: {$mapState['name']}");
            } elseif ($existing->name !== $mapState['name']) {
                if (!$dryRun) {
                    $existing->update(['name' => $mapState['name']]);
                }
                $updated++;
                $this->line("  <fg=yellow>~</> Updated: {$mapState['name']}");
            } else {
                $skipped++;
            }
        }

        $this->table(['Action', 'Count'], [
            ['Created', $created],
            ['Updated', $updated],
            ['Skipped', $skipped],
        ]);
    }

    /**
     * Sync branches from MAP
     */
    private function syncBranches(bool $dryRun): void
    {
        $this->info('Syncing Branches...');

        $stmt = $this->mapDb->query("
            SELECT 
                id,
                title as branch_name,
                ti_agent_code,
                address,
                email,
                state_id,
                region_id,
                weekend_start_day
            FROM Application_branch 
            ORDER BY id
        ");
        $mapBranches = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($mapBranches as $mapBranch) {
            try {
                // Find by ti_agent_code (unique identifier) instead of id
                $existing = Branch::withTrashed()
                    ->where('ti_agent_code', $mapBranch['ti_agent_code'])
                    ->first();

                $data = [
                    'branch_name' => $mapBranch['branch_name'],
                    'ti_agent_code' => $mapBranch['ti_agent_code'],
                    'address' => $mapBranch['address'],
                    'email' => $mapBranch['email'],
                    'state_id' => $mapBranch['state_id'],
                    'region_id' => $mapBranch['region_id'],
                    'weekend_start_day' => $mapBranch['weekend_start_day'],
                ];

                if (!$existing) {
                    if (!$dryRun) {
                        Branch::create($data);
                    }
                    $created++;
                    $this->line("  <fg=green>+</> Created: {$mapBranch['branch_name']}");
                } else {
                    // Check if anything changed
                    $hasChanges = false;
                    foreach ($data as $key => $value) {
                        if ($existing->$key != $value) {
                            $hasChanges = true;
                            break;
                        }
                    }

                    if ($hasChanges) {
                        if (!$dryRun) {
                            // Restore if trashed
                            if ($existing->trashed()) {
                                $existing->restore();
                            }
                            $existing->update($data);
                        }
                        $updated++;
                        $this->line("  <fg=yellow>~</> Updated: {$mapBranch['branch_name']}");
                    } else {
                        $skipped++;
                    }
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("  Error syncing {$mapBranch['branch_name']}: " . $e->getMessage());
                Log::error('MAP branch sync error', [
                    'branch' => $mapBranch['branch_name'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->table(['Action', 'Count'], [
            ['Created', $created],
            ['Updated', $updated],
            ['Skipped', $skipped],
            ['Errors', $errors],
        ]);
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
