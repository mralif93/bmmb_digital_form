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
            FormManagementSeeder::class, // Creates Forms → Sections → Fields for all 4 forms
            FormSubmissionSeeder::class, // Creates form submissions for dynamic forms
            // Old form seeders (kept for backward compatibility if needed)
            // FormSeeder::class, // Create main form records for all 4 forms (old models)
            // FormSectionSeeder::class, // Initialize form sections for all form types
            // FormFieldsSeeder::class, // Add form fields for dynamic forms (old models)
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
