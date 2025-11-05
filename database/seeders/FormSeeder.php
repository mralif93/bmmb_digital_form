<?php

namespace Database\Seeders;

use App\Models\DataAccessRequestForm;
use App\Models\DataCorrectionRequestForm;
use App\Models\RemittanceApplicationForm;
use App\Models\ServiceRequestForm;
use App\Models\User;
use App\Traits\UsesSystemTimezone;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    use UsesSystemTimezone;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('role', 'admin')->first();
        $year = date('Y');

        // Get all RAF forms and find the highest sequence number
        $allRafForms = RemittanceApplicationForm::where('application_number', 'like', 'RAF-' . $year . '-%')->get();
        $maxSequence = 0;
        
        foreach ($allRafForms as $form) {
            if (preg_match('/RAF-' . $year . '-(\d+)$/', $form->application_number, $matches)) {
                $sequence = (int) $matches[1];
                $maxSequence = max($maxSequence, $sequence);
                
                // Normalize old format to 6 digits if needed
                $normalizedNumber = 'RAF-' . $year . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
                if ($form->application_number !== $normalizedNumber) {
                    // Check if normalized number already exists
                    $existing = RemittanceApplicationForm::where('application_number', $normalizedNumber)
                        ->where('id', '!=', $form->id)
                        ->first();
                    if (!$existing) {
                        $form->application_number = $normalizedNumber;
                        $form->save();
                    } else {
                        // Delete the old format duplicate
                        $form->delete();
                    }
                }
            }
        }
        
        $startSequence = $maxSequence + 1;

        // Create RAF Forms (create 5 sample forms)
        for ($i = 0; $i < 5; $i++) {
            $sequence = str_pad($startSequence + $i, 6, '0', STR_PAD_LEFT);
            $applicationNumber = 'RAF-' . $year . '-' . $sequence;
            
            RemittanceApplicationForm::firstOrCreate(
                ['application_number' => $applicationNumber],
                [
                'user_id' => $adminUser?->id,
                'application_number' => $applicationNumber,
                'status' => 'draft',
                'version' => '5.0',
                'applicant_name' => 'Sample Applicant ' . $i,
                'applicant_phone' => '+60123456789',
                'applicant_email' => 'applicant@example.com',
                'applicant_address' => '123 Sample Street',
                'applicant_city' => 'Kuala Lumpur',
                'applicant_state' => 'Wilayah Persekutuan',
                'applicant_postal_code' => '50000',
                'applicant_country' => 'Malaysia',
                'applicant_id_type' => 'National ID',
                'applicant_id_number' => 'A12345678',
                'remittance_amount' => 1000.00,
                'remittance_currency' => 'MYR',
                'remittance_purpose' => 'Family Support',
                'remittance_frequency' => 'One-time',
                'beneficiary_name' => 'Sample Beneficiary',
                'beneficiary_relationship' => 'Family',
                'beneficiary_address' => '456 Beneficiary Street',
                'beneficiary_city' => 'Singapore',
                'beneficiary_state' => 'Singapore',
                'beneficiary_postal_code' => '123456',
                'beneficiary_country' => 'Singapore',
                'payment_method' => 'Bank Transfer',
                'payment_source' => 'Personal Savings',
                'payment_currency' => 'MYR',
                'total_amount' => 1050.00,
                'risk_level' => 'low',
                ]
            );
        }
        $this->command->info('Created/verified RAF forms.');

        // Get all DAR forms and find the highest sequence number
        $allDarForms = DataAccessRequestForm::where('request_number', 'like', 'DAR-' . $year . '-%')->get();
        $maxSequence = 0;
        
        foreach ($allDarForms as $form) {
            if (preg_match('/DAR-' . $year . '-(\d+)$/', $form->request_number, $matches)) {
                $sequence = (int) $matches[1];
                $maxSequence = max($maxSequence, $sequence);
                
                // Normalize old format to 6 digits if needed
                $normalizedNumber = 'DAR-' . $year . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
                if ($form->request_number !== $normalizedNumber) {
                    // Check if normalized number already exists
                    $existing = DataAccessRequestForm::where('request_number', $normalizedNumber)
                        ->where('id', '!=', $form->id)
                        ->first();
                    if (!$existing) {
                        $form->request_number = $normalizedNumber;
                        $form->save();
                    } else {
                        // Delete the old format duplicate
                        $form->delete();
                    }
                }
            }
        }
        
        $startSequence = $maxSequence + 1;

        // Create DAR Forms (create 5 sample forms)
        for ($i = 0; $i < 5; $i++) {
            $sequence = str_pad($startSequence + $i, 6, '0', STR_PAD_LEFT);
            $requestNumber = 'DAR-' . $year . '-' . $sequence;
            
            DataAccessRequestForm::firstOrCreate(
                ['request_number' => $requestNumber],
                [
                'user_id' => $adminUser?->id,
                'request_number' => $requestNumber,
                'status' => 'draft',
                'version' => '1.0',
                'requester_name' => 'Sample Requester ' . $i,
                'requester_phone' => '+60123456789',
                'requester_email' => 'requester@example.com',
                'requester_address' => '123 Sample Street',
                'requester_city' => 'Kuala Lumpur',
                'requester_state' => 'Wilayah Persekutuan',
                'requester_postal_code' => '50000',
                'requester_country' => 'Malaysia',
                'requester_id_type' => 'National ID',
                'requester_id_number' => 'A12345678',
                'data_subject_name' => 'Sample Data Subject',
                'data_subject_phone' => '+60123456790',
                'data_subject_email' => 'datasubject@example.com',
                'data_subject_address' => '456 Data Subject Street',
                'data_subject_city' => 'Kuala Lumpur',
                'data_subject_state' => 'Wilayah Persekutuan',
                'data_subject_postal_code' => '50000',
                'data_subject_country' => 'Malaysia',
                'data_subject_id_type' => 'National ID',
                'data_subject_id_number' => 'A87654321',
                'relationship_to_data_subject' => 'Self',
                'request_type' => 'access',
                'request_description' => 'Request for access to personal data',
                'legal_basis' => 'consent',
                ]
            );
        }
        $this->command->info('Created/verified DAR forms.');

        // Get all DCR forms and find the highest sequence number
        $allDcrForms = DataCorrectionRequestForm::where('request_number', 'like', 'DCR-' . $year . '-%')->get();
        $maxSequence = 0;
        
        foreach ($allDcrForms as $form) {
            if (preg_match('/DCR-' . $year . '-(\d+)$/', $form->request_number, $matches)) {
                $sequence = (int) $matches[1];
                $maxSequence = max($maxSequence, $sequence);
                
                // Normalize old format to 6 digits if needed
                $normalizedNumber = 'DCR-' . $year . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
                if ($form->request_number !== $normalizedNumber) {
                    // Check if normalized number already exists
                    $existing = DataCorrectionRequestForm::where('request_number', $normalizedNumber)
                        ->where('id', '!=', $form->id)
                        ->first();
                    if (!$existing) {
                        $form->request_number = $normalizedNumber;
                        $form->save();
                    } else {
                        // Delete the old format duplicate
                        $form->delete();
                    }
                }
            }
        }
        
        $startSequence = $maxSequence + 1;

        // Create DCR Forms (create 5 sample forms)
        for ($i = 0; $i < 5; $i++) {
            $sequence = str_pad($startSequence + $i, 6, '0', STR_PAD_LEFT);
            $requestNumber = 'DCR-' . $year . '-' . $sequence;
            
            DataCorrectionRequestForm::firstOrCreate(
                ['request_number' => $requestNumber],
                [
                'user_id' => $adminUser?->id,
                'request_number' => $requestNumber,
                'status' => 'draft',
                'version' => '1.0',
                'requester_name' => 'Sample Requester ' . $i,
                'requester_phone' => '+60123456789',
                'requester_email' => 'requester@example.com',
                'requester_address' => '123 Sample Street',
                'requester_city' => 'Kuala Lumpur',
                'requester_state' => 'Wilayah Persekutuan',
                'requester_postal_code' => '50000',
                'requester_country' => 'Malaysia',
                'requester_id_type' => 'National ID',
                'requester_id_number' => 'A12345678',
                'data_subject_name' => 'Sample Data Subject',
                'data_subject_phone' => '+60123456790',
                'data_subject_email' => 'datasubject@example.com',
                'data_subject_address' => '456 Data Subject Street',
                'data_subject_city' => 'Kuala Lumpur',
                'data_subject_state' => 'Wilayah Persekutuan',
                'data_subject_postal_code' => '50000',
                'data_subject_country' => 'Malaysia',
                'data_subject_id_type' => 'National ID',
                'data_subject_id_number' => 'A87654321',
                'relationship_to_data_subject' => 'Self',
                'correction_type' => 'personal_info',
                'correction_description' => 'Request to correct personal information',
                'legal_basis' => 'data_accuracy_obligation',
                ]
            );
        }
        $this->command->info('Created/verified DCR forms.');

        // Get all SRF forms and find the highest sequence number
        $allSrfForms = ServiceRequestForm::where('request_number', 'like', 'SRF-' . $year . '-%')->get();
        $maxSequence = 0;
        
        foreach ($allSrfForms as $form) {
            if (preg_match('/SRF-' . $year . '-(\d+)$/', $form->request_number, $matches)) {
                $sequence = (int) $matches[1];
                $maxSequence = max($maxSequence, $sequence);
                
                // Normalize old format to 6 digits if needed
                $normalizedNumber = 'SRF-' . $year . '-' . str_pad($sequence, 6, '0', STR_PAD_LEFT);
                if ($form->request_number !== $normalizedNumber) {
                    // Check if normalized number already exists
                    $existing = ServiceRequestForm::where('request_number', $normalizedNumber)
                        ->where('id', '!=', $form->id)
                        ->first();
                    if (!$existing) {
                        $form->request_number = $normalizedNumber;
                        $form->save();
                    } else {
                        // Delete the old format duplicate
                        $form->delete();
                    }
                }
            }
        }
        
        $startSequence = $maxSequence + 1;

        // Create SRF Forms (create 5 sample forms)
        for ($i = 0; $i < 5; $i++) {
            $sequence = str_pad($startSequence + $i, 6, '0', STR_PAD_LEFT);
            $requestNumber = 'SRF-' . $year . '-' . $sequence;
            
            ServiceRequestForm::firstOrCreate(
                ['request_number' => $requestNumber],
                [
                'user_id' => $adminUser?->id,
                'request_number' => $requestNumber,
                'status' => 'draft',
                'version' => '16.0',
                'customer_name' => 'Sample Customer ' . $i,
                'customer_phone' => '+60123456789',
                'customer_email' => 'customer@example.com',
                'customer_address' => '123 Sample Street',
                'customer_city' => 'Kuala Lumpur',
                'customer_state' => 'Wilayah Persekutuan',
                'customer_postal_code' => '50000',
                'customer_country' => 'Malaysia',
                'customer_id_type' => 'National ID',
                'customer_id_number' => 'A12345678',
                'service_description' => 'Service request for deposit',
                'service_category' => 'banking',
                ]
            );
        }
        $this->command->info('Created/verified SRF forms.');
    }
}
