<?php

namespace Database\Seeders;

use App\Models\FormSection;
use Illuminate\Database\Seeder;

class FormSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Initialize default sections for all form types
        FormSection::initializeDefaults('raf');
        FormSection::initializeDefaults('dar');
        FormSection::initializeDefaults('dcr');
        FormSection::initializeDefaults('srf');
    }
}
