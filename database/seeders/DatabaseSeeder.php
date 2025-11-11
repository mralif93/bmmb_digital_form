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
            BranchSeeder::class, // Must run first - creates branches
            UserSeeder::class, // Runs after branches to assign users to branches
            QrCodeSeeder::class, // Requires branches
            FormManagementSeeder::class, // Creates Forms → Sections → Fields for all 4 forms
            FormSubmissionSeeder::class, // Creates form submissions for dynamic forms (requires forms, branches, users)
            // Old form seeders removed - they use deprecated models (RemittanceApplicationForm, etc.)
            // These models no longer exist as we've migrated to the new Form system
            // If you need old form data, use FormSubmissionSeeder which works with the new system
        ]);
    }
}
