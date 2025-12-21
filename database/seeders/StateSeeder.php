<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use PDO;

class StateSeeder extends Seeder
{
    /**
     * Seed states from MAP database.
     */
    public function run(): void
    {
        $this->command->info('Seeding states from MAP...');

        $mapDbPath = base_path('../FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3');

        if (!file_exists($mapDbPath)) {
            $this->command->error("MAP database not found at: {$mapDbPath}");
            return;
        }

        try {
            $mapDb = new PDO("sqlite:{$mapDbPath}");
            $mapDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $mapDb->query("SELECT id, name FROM Application_state ORDER BY id");
            $states = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $count = 0;
            foreach ($states as $state) {
                State::updateOrCreate(
                    ['id' => $state['id']],
                    ['name' => $state['name']]
                );
                $count++;
            }

            $this->command->info("✓ Seeded {$count} states");

        } catch (\Exception $e) {
            $this->command->error("Error: " . $e->getMessage());
            Log::error('State seeding error', ['error' => $e->getMessage()]);
        }
    }
}
