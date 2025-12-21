<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateStates extends Command
{
    protected $signature = 'map:migrate-states {--dry-run : Run without making changes}';
    protected $description = 'Migrate states from MAP database to eform';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info('=== MAP State Migration ===');
        $this->info('');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE');
            $this->info('');
        }

        if (!$this->connectToMapDatabase()) {
            return 1;
        }

        $mapStates = DB::connection('map')->table('Application_state')->get();

        if ($mapStates->isEmpty()) {
            $this->error('No states found');
            return 1;
        }

        $this->info("Found {$mapStates->count()} states");
        $this->info('');

        if (!$isDryRun && !$this->confirm('Proceed?')) {
            return 0;
        }

        $stats = ['created' => 0, 'errors' => 0];

        foreach ($mapStates as $state) {
            try {
                if (!$isDryRun) {
                    DB::table('states')->updateOrInsert(
                        ['id' => $state->id],
                        ['name' => $state->name, 'updated_at' => now()]
                    );
                }
                $stats['created']++;
            } catch (\Exception $e) {
                $this->error("Error: {$state->name} - " . $e->getMessage());
                $stats['errors']++;
            }
        }

        $this->info("\n=== Summary ===");
        $this->line("Created/Updated: {$stats['created']}");
        if ($stats['errors'] > 0)
            $this->error("Errors: {$stats['errors']}");

        return 0;
    }

    private function connectToMapDatabase(): bool
    {
        $path = base_path('../FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3');
        if (!file_exists($path)) {
            $this->error("MAP DB not found");
            return false;
        }

        config([
            'database.connections.map' => [
                'driver' => 'sqlite',
                'database' => $path,
                'prefix' => '',
                'foreign_key_constraints' => true,
            ]
        ]);

        try {
            DB::connection('map')->getPdo();
            $this->info("✓ Connected to MAP");
            return true;
        } catch (\Exception $e) {
            $this->error("Connection failed");
            return false;
        }
    }
}
