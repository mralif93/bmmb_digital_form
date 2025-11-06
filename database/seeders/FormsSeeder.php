<?php

namespace Database\Seeders;

use App\Models\Form;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FormsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates the 4 default forms (RAF, DAR, DCR, SRF) in the forms table.
     */
    public function run(): void
    {
        $forms = [
            [
                'name' => 'Remittance Application Form',
                'slug' => 'raf',
                'description' => 'Submit your remittance application for international money transfers and financial transactions.',
                'status' => 'active',
                'is_public' => true,
                'allow_multiple_submissions' => true,
                'submission_limit' => null,
                'settings' => [
                    'type' => 'raf',
                    'version' => '5.0',
                ],
            ],
            [
                'name' => 'Data Access Request Form',
                'slug' => 'dar',
                'description' => 'Request access to your personal data and information in compliance with data protection regulations.',
                'status' => 'active',
                'is_public' => true,
                'allow_multiple_submissions' => true,
                'submission_limit' => null,
                'settings' => [
                    'type' => 'dar',
                    'version' => '1.0',
                ],
            ],
            [
                'name' => 'Data Correction Request Form',
                'slug' => 'dcr',
                'description' => 'Request correction of your personal data in compliance with data protection regulations.',
                'status' => 'active',
                'is_public' => true,
                'allow_multiple_submissions' => true,
                'submission_limit' => null,
                'settings' => [
                    'type' => 'dcr',
                    'version' => '1.0',
                ],
            ],
            [
                'name' => 'Service Request Form',
                'slug' => 'srf',
                'description' => 'Submit your service request for banking and financial services.',
                'status' => 'active',
                'is_public' => true,
                'allow_multiple_submissions' => true,
                'submission_limit' => null,
                'settings' => [
                    'type' => 'srf',
                    'version' => '16.0',
                ],
            ],
        ];

        foreach ($forms as $formData) {
            Form::updateOrCreate(
                ['slug' => $formData['slug']],
                $formData
            );
        }

        $this->command->info('Created/Updated 4 default forms (RAF, DAR, DCR, SRF)');
    }
}
