<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BranchSeeder::class,
            QrCodeSeeder::class,
            FormSeeder::class,
            RafSubmissionSeeder::class,
            DarSubmissionSeeder::class,
            DcrSubmissionSeeder::class,
            SrfSubmissionSeeder::class,
            // Public form submissions (optional - run separately if needed)
            // PublicRafSubmissionSeeder::class,
            // PublicDarSubmissionSeeder::class,
            // PublicDcrSubmissionSeeder::class,
            // PublicSrfSubmissionSeeder::class,
        ]);
    }
}
