<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\FormSection;
use Illuminate\Database\Seeder;

class FormSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get forms by slug and initialize default sections
        $forms = Form::whereIn('slug', ['raf', 'dar', 'dcr', 'srf'])->get();
        
        foreach ($forms as $form) {
            // Initialize default sections for each form
            FormSection::initializeDefaults($form->id, $form->slug);
        }
    }
}
