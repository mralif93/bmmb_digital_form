<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use PDO;

class BranchSeeder extends Seeder
{
    /**
     * Seed branches from MAP database.
     */
    public function run(): void
    {
        $this->command->info('Seeding branches from MAP...');

        $mapDbPath = base_path('../FinancingApp/FinancingApp_Backend/FinancingApp/db.sqlite3');

        if (!file_exists($mapDbPath)) {
            $this->command->error("MAP database not found at: {$mapDbPath}");
            return;
        }

        try {
            $mapDb = new PDO("sqlite:{$mapDbPath}");
            $mapDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $mapDb->query("
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
            $branches = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $count = 0;
            foreach ($branches as $branch) {
                // Skip if ti_agent_code already exists (avoid duplicates)
                $existing = Branch::where('ti_agent_code', $branch['ti_agent_code'])->first();

                if ($existing) {
                    $existing->update([
                        'branch_name' => $branch['branch_name'],
                        'address' => $branch['address'],
                        'email' => $branch['email'],
                        'state_id' => $branch['state_id'],
                        'region_id' => $branch['region_id'],
                        'weekend_start_day' => $branch['weekend_start_day'] ?: 'SATURDAY',
                    ]);
                } else {
                    Branch::create([
                        'branch_name' => $branch['branch_name'],
                        'ti_agent_code' => $branch['ti_agent_code'],
                        'address' => $branch['address'],
                        'email' => $branch['email'],
                        'state_id' => $branch['state_id'],
                        'region_id' => $branch['region_id'],
                        'weekend_start_day' => $branch['weekend_start_day'] ?: 'SATURDAY',
                    ]);
                }
                $count++;
            }

            $this->command->info("✓ Seeded {$count} branches");

        } catch (\Exception $e) {
            $this->command->error("Error: " . $e->getMessage());
            Log::error('Branch seeding error', ['error' => $e->getMessage()]);
        }
    }
}
