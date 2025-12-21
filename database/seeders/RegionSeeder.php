<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use PDO;

class RegionSeeder extends Seeder
{
    /**
     * Seed regions from MAP database.
     */
    public function run(): void
    {
        $this->command->info('Seeding regions from MAP...');

        $mapDbPath = base_path('../FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3');

        if (!file_exists($mapDbPath)) {
            $this->command->error("MAP database not found at: {$mapDbPath}");
            return;
        }

        try {
            $mapDb = new PDO("sqlite:{$mapDbPath}");
            $mapDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $mapDb->query("SELECT id, name FROM Application_region ORDER BY id");
            $regions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $count = 0;
            foreach ($regions as $region) {
                Region::updateOrCreate(
                    ['id' => $region['id']],
                    [
                        'name' => $region['name'],
                        'links' => '[]', // Empty JSON array for links
                    ]
                );
                $count++;
            }

            $this->command->info("✓ Seeded {$count} regions");

        } catch (\Exception $e) {
            $this->command->error("Error: " . $e->getMessage());
            Log::error('Region seeding error', ['error' => $e->getMessage()]);
        }
    }
}
