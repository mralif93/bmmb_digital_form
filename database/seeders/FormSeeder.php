<?php

namespace Database\Seeders;

use App\Models\DataAccessRequestForm;
use App\Models\DataCorrectionRequestForm;
use App\Models\RemittanceApplicationForm;
use App\Models\ServiceRequestForm;
use App\Models\User;
use Illuminate\Database\Seeder;

class FormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates the main form records for all 4 form types.
     */
    public function run(): void
    {
        // Get admin user (first user or create one)
        $admin = User::first();
        if (!$admin) {
            $this->command->warn('No user found. Please run UserSeeder first.');
            return;
        }

        // Create RAF (Remittance Application Form)
        $rafForm = RemittanceApplicationForm::firstOrCreate(
            ['application_number' => 'RAF-' . date('Y') . '-000001'],
            [
                'user_id' => $admin->id,
                'status' => 'submitted',
                'version' => '5.0',
                'applicant_name' => 'System Default',
                'applicant_phone' => '+60123456789',
                'applicant_email' => 'admin@bmmb.com',
                'applicant_address' => 'Default Address',
                'applicant_city' => 'Kuala Lumpur',
                'applicant_state' => 'Wilayah Persekutuan',
                'applicant_postal_code' => '50000',
                'applicant_country' => 'Malaysia',
                'applicant_id_type' => 'national_id',
                'applicant_id_number' => '000000000000',
                'remittance_amount' => 1000.00,
                'remittance_currency' => 'MYR',
                'remittance_purpose' => 'family_support',
                'remittance_frequency' => 'one_time',
                'beneficiary_name' => 'Default Beneficiary',
                'beneficiary_relationship' => 'other',
                'beneficiary_address' => 'Default Address',
                'beneficiary_city' => 'Kuala Lumpur',
                'beneficiary_state' => 'Wilayah Persekutuan',
                'beneficiary_postal_code' => '50000',
                'beneficiary_country' => 'Malaysia',
                'payment_method' => 'bank_transfer',
                'payment_source' => 'personal_savings',
                'payment_currency' => 'MYR',
                'total_amount' => 1000.00,
                'aml_verified' => false,
                'kyc_verified' => false,
                'sanctions_checked' => false,
                'risk_level' => 'low',
            ]
        );

        $this->command->info('RAF form created/verified: ' . $rafForm->application_number);

        // Create DAR (Data Access Request Form)
        $darForm = DataAccessRequestForm::firstOrCreate(
            ['request_number' => 'DAR-' . date('Y') . '-000001'],
            [
                'user_id' => $admin->id,
                'status' => 'submitted',
                'version' => '1.0',
                'request_type' => 'access', // enum: access, rectification, erasure, portability, restriction, objection, complaint, other
                'requester_name' => 'System Default',
                'requester_email' => 'admin@bmmb.com',
                'requester_phone' => '+60123456789',
                'requester_address' => 'Default Address',
                'requester_city' => 'Kuala Lumpur',
                'requester_state' => 'Wilayah Persekutuan',
                'requester_postal_code' => '50000',
                'requester_country' => 'Malaysia',
                'requester_id_type' => 'national_id',
                'requester_id_number' => '000000000000',
                'data_subject_name' => 'Default Data Subject',
                'data_subject_id_type' => 'national_id',
                'data_subject_id_number' => '000000000001',
                'relationship_to_data_subject' => 'self',
                'request_description' => 'Default data access request',
                'data_categories' => ['personal_info', 'financial_data'],
                'urgency_level' => 'low',
                'legal_basis' => 'consent', // enum: consent, contract, legal_obligation, vital_interests, public_task, legitimate_interests, other
                'consent_obtained' => true,
                'identity_verified' => false,
                'authorization_verified' => false,
                'legal_basis_verified' => false,
                'data_existence_confirmed' => false,
                'risk_level' => 'low',
            ]
        );

        $this->command->info('DAR form created/verified: ' . $darForm->request_number);

        // Create DCR (Data Correction Request Form)
        $dcrForm = DataCorrectionRequestForm::firstOrCreate(
            ['request_number' => 'DCR-' . date('Y') . '-000001'],
            [
                'user_id' => $admin->id,
                'status' => 'submitted',
                'version' => '1.0',
                'requester_name' => 'System Default',
                'requester_email' => 'admin@bmmb.com',
                'requester_phone' => '+60123456789',
                'requester_address' => 'Default Address',
                'requester_city' => 'Kuala Lumpur',
                'requester_state' => 'Wilayah Persekutuan',
                'requester_postal_code' => '50000',
                'requester_country' => 'Malaysia',
                'requester_id_type' => 'national_id',
                'requester_id_number' => '000000000000',
                'data_subject_name' => 'Default Data Subject',
                'data_subject_id_type' => 'national_id',
                'data_subject_id_number' => '000000000001',
                'relationship_to_data_subject' => 'self',
                'correction_type' => 'personal_info', // enum: personal_info, contact_info, financial_info, demographic_info, preferences, account_info, transaction_data, other
                'correction_description' => 'Default correction description',
                'incorrect_data_items' => ['field1' => 'incorrect value'],
                'corrected_data_items' => ['field1' => 'correct value'],
                'reason_for_correction' => 'Default reason',
                'urgency_level' => 'low',
                'legal_basis' => 'data_accuracy_obligation', // enum: consent, contract, legal_obligation, vital_interests, public_task, legitimate_interests, data_accuracy_obligation, other
                'consent_obtained' => true,
                'identity_verified' => false,
                'verification_status' => 'pending', // enum: pending, in_progress, completed, failed
                'risk_level' => 'low',
            ]
        );

        $this->command->info('DCR form created/verified: ' . $dcrForm->request_number);

        // Create SRF (Service Request Form)
        $srfForm = ServiceRequestForm::firstOrCreate(
            ['request_number' => 'SRF-' . date('Y') . '-000001'],
            [
                'user_id' => $admin->id,
                'status' => 'submitted',
                'version' => '16.0',
                'service_type' => 'deposit',
                'customer_name' => 'System Default',
                'customer_email' => 'admin@bmmb.com',
                'customer_phone' => '+60123456789',
                'customer_address' => 'Default Address',
                'customer_city' => 'Kuala Lumpur',
                'customer_state' => 'Wilayah Persekutuan',
                'customer_postal_code' => '50000',
                'customer_country' => 'Malaysia',
                'customer_id_type' => 'national_id',
                'customer_id_number' => '000000000000',
                'account_number' => '0000000000',
                'account_type' => 'savings',
                'service_description' => 'Default service request',
                'service_category' => 'banking', // enum: banking, investment, insurance, loan, credit_card, foreign_exchange, international_transfer, other
                'urgency_level' => 'low', // enum: low, medium, high, urgent
                'preferred_completion_date' => now()->addDays(7),
                'aml_verified' => false,
                'kyc_verified' => false,
                'sanctions_checked' => false,
                'risk_level' => 'low',
            ]
        );

        $this->command->info('SRF form created/verified: ' . $srfForm->request_number);

        $this->command->info('✅ All 4 forms created/verified successfully!');
        $this->command->info('Forms are ready for Form Builder configuration.');
    }
}

