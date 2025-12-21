<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * Order of seeding:
     * 1. Regions - from MAP database
     * 2. States - from MAP database  
     * 3. Branches - from MAP database (requires regions & states)
     * 4. Users - from MAP database (requires branches)
     * 5. Forms - from JSON exports (DAR, DCR, SRF)
     * 6. QR Codes - generated for each branch + form combination
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('===========================================');
        $this->command->info('  Starting eForm Database Seeding');
        $this->command->info('===========================================');
        $this->command->info('');

        $this->call([
                // 1. Regions from MAP
            RegionSeeder::class,

                // 2. States from MAP
            StateSeeder::class,

                // 3. Branches from MAP (requires regions & states)
            BranchSeeder::class,

                // 4. Users from MAP (requires branches)
            UserSeeder::class,

                // 5. Forms from JSON exports
            FormSeeder::class,

                // 6. QR Codes for branches (requires forms & branches)
            QrCodeSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('===========================================');
        $this->command->info('  ✓ Database Seeding Completed!');
        $this->command->info('===========================================');
        $this->command->info('');
    }
}
