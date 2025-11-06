<?php

namespace Database\Seeders;

use App\Models\DataAccessRequestForm;
use App\Models\DataCorrectionRequestForm;
use App\Models\RemittanceApplicationForm;
use App\Models\ServiceRequestForm;
use App\Models\RafFormField;
use App\Models\DarFormField;
use App\Models\DcrFormField;
use App\Models\SrfFormField;
use Illuminate\Database\Seeder;

class FormFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed RAF Form Fields
        $this->seedRafFields();
        
        // Seed DAR Form Fields
        $this->seedDarFields();
        
        // Seed DCR Form Fields
        $this->seedDcrFields();
        
        // Seed SRF Form Fields
        $this->seedSrfFields();
    }

    /**
     * Determine grid column position based on field type
     * Full-width types: textarea, radio, checkbox, file
     * Left/Right: text, email, phone, number, date, select, currency
     */
    private function getGridColumn($fieldType, $hasOptions = false): string
    {
        $fullWidthTypes = ['textarea', 'radio', 'checkbox', 'file'];
        
        if (in_array($fieldType, $fullWidthTypes)) {
            return 'full';
        }
        
        if ($hasOptions && in_array($fieldType, ['radio', 'checkbox'])) {
            return 'full';
        }
        
        // Default to left for text inputs, right can be set manually if needed
        return 'left';
    }

    /**
     * Process field array and add grid_column if not set
     */
    private function processField(array $field): array
    {
        if (!isset($field['grid_column'])) {
            $hasOptions = isset($field['field_options']) && !empty($field['field_options']);
            $field['grid_column'] = $this->getGridColumn($field['field_type'], $hasOptions);
        }
        return $field;
    }

    /**
     * Seed RAF (Remittance Application Form) Fields
     * Based on Appendix I - Remittance Application Form (RAF) V5.0.pdf
     */
    private function seedRafFields(): void
    {
        $form = RemittanceApplicationForm::first();
        if (!$form) {
            $this->command->warn('No RAF form found. Skipping RAF fields seeding.');
            return;
        }

        $fields = [
            // ========== APPLICANT INFORMATION SECTION ==========
            [
                'raf_form_id' => $form->id,
                'field_section' => 'applicant_info',
                'field_name' => 'applicant_name',
                'field_label' => 'Applicant Name',
                'field_type' => 'text',
                'field_placeholder' => 'Enter full name as per ID',
                'field_description' => 'Please enter your name exactly as it appears on your identification document',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'validation_rules' => ['required' => true, 'min' => 2, 'max' => 100],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'applicant_info',
                'field_name' => 'applicant_phone',
                'field_label' => 'Phone Number',
                'field_type' => 'phone',
                'field_placeholder' => '+60 12-345 6789',
                'field_description' => 'Include country code',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'applicant_info',
                'field_name' => 'applicant_email',
                'field_label' => 'Email Address',
                'field_type' => 'email',
                'field_placeholder' => 'your.email@example.com',
                'field_description' => 'We will send confirmation to this email',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'validation_rules' => ['required' => true, 'email' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'applicant_info',
                'field_name' => 'applicant_address',
                'field_label' => 'Address',
                'field_type' => 'textarea',
                'field_placeholder' => 'Enter complete address',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'applicant_info',
                'field_name' => 'applicant_city',
                'field_label' => 'City',
                'field_type' => 'text',
                'field_placeholder' => 'Enter city',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'applicant_info',
                'field_name' => 'applicant_state',
                'field_label' => 'State/Province',
                'field_type' => 'text',
                'field_placeholder' => 'Enter state or province',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 6,
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'applicant_info',
                'field_name' => 'applicant_postal_code',
                'field_label' => 'Postal Code',
                'field_type' => 'text',
                'field_placeholder' => 'Enter postal code',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 7,
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'applicant_info',
                'field_name' => 'applicant_country',
                'field_label' => 'Country',
                'field_type' => 'text',
                'field_placeholder' => 'Enter country',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 8,
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'applicant_info',
                'field_name' => 'applicant_id_type',
                'field_label' => 'ID Type',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 9,
                'field_options' => [
                    'passport' => 'Passport',
                    'national_id' => 'National ID',
                    'drivers_license' => "Driver's License",
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'applicant_info',
                'field_name' => 'applicant_id_number',
                'field_label' => 'ID Number',
                'field_type' => 'text',
                'field_placeholder' => 'Enter ID number',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 10,
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'applicant_info',
                'field_name' => 'applicant_id_expiry_date',
                'field_label' => 'ID Expiry Date',
                'field_type' => 'date',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 11,
            ],

            // ========== REMITTANCE DETAILS SECTION ==========
            [
                'raf_form_id' => $form->id,
                'field_section' => 'remittance_details',
                'field_name' => 'remittance_amount',
                'field_label' => 'Remittance Amount',
                'field_type' => 'currency',
                'field_placeholder' => '0.00',
                'field_description' => 'Enter the amount to be remitted',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'field_settings' => ['currency' => 'MYR', 'min' => 100],
                'validation_rules' => ['required' => true, 'numeric' => true, 'min' => 100],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'remittance_details',
                'field_name' => 'remittance_currency',
                'field_label' => 'Currency',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'field_options' => [
                    'USD' => 'USD - US Dollar',
                    'EUR' => 'EUR - Euro',
                    'GBP' => 'GBP - British Pound',
                    'MYR' => 'MYR - Malaysian Ringgit',
                    'SGD' => 'SGD - Singapore Dollar',
                    'AUD' => 'AUD - Australian Dollar',
                    'JPY' => 'JPY - Japanese Yen',
                    'CNY' => 'CNY - Chinese Yuan',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'remittance_details',
                'field_name' => 'remittance_purpose',
                'field_label' => 'Purpose of Remittance',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'field_options' => [
                    'family_support' => 'Family Support',
                    'education' => 'Education',
                    'medical' => 'Medical Expenses',
                    'business' => 'Business',
                    'investment' => 'Investment',
                    'property' => 'Property Purchase',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'remittance_details',
                'field_name' => 'remittance_purpose_description',
                'field_label' => 'Purpose Description',
                'field_type' => 'textarea',
                'field_placeholder' => 'Provide additional details about the purpose',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 4,
                'is_conditional' => true,
                'conditional_logic' => [
                    'show_if' => [
                        'field' => 'remittance_purpose',
                        'operator' => 'equals',
                        'value' => 'other',
                    ],
                ],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'remittance_details',
                'field_name' => 'remittance_frequency',
                'field_label' => 'Frequency',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
                'field_options' => [
                    'one_time' => 'One Time',
                    'monthly' => 'Monthly',
                    'quarterly' => 'Quarterly',
                    'semi_annual' => 'Semi-Annual',
                    'annual' => 'Annual',
                ],
                'validation_rules' => ['required' => true],
            ],

            // ========== BENEFICIARY INFORMATION SECTION ==========
            [
                'raf_form_id' => $form->id,
                'field_section' => 'beneficiary_info',
                'field_name' => 'beneficiary_name',
                'field_label' => 'Beneficiary Name',
                'field_type' => 'text',
                'field_placeholder' => 'Enter beneficiary full name',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'beneficiary_info',
                'field_name' => 'beneficiary_relationship',
                'field_label' => 'Relationship to Beneficiary',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'field_options' => [
                    'spouse' => 'Spouse',
                    'parent' => 'Parent',
                    'child' => 'Child',
                    'sibling' => 'Sibling',
                    'relative' => 'Relative',
                    'friend' => 'Friend',
                    'business_partner' => 'Business Partner',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'beneficiary_info',
                'field_name' => 'beneficiary_address',
                'field_label' => 'Beneficiary Address',
                'field_type' => 'textarea',
                'field_placeholder' => 'Enter complete beneficiary address',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'beneficiary_info',
                'field_name' => 'beneficiary_city',
                'field_label' => 'City',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'beneficiary_info',
                'field_name' => 'beneficiary_state',
                'field_label' => 'State/Province',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'beneficiary_info',
                'field_name' => 'beneficiary_postal_code',
                'field_label' => 'Postal Code',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 6,
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'beneficiary_info',
                'field_name' => 'beneficiary_country',
                'field_label' => 'Country',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 7,
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'beneficiary_info',
                'field_name' => 'beneficiary_phone',
                'field_label' => 'Phone Number',
                'field_type' => 'phone',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'beneficiary_info',
                'field_name' => 'beneficiary_email',
                'field_label' => 'Email Address',
                'field_type' => 'email',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'beneficiary_info',
                'field_name' => 'beneficiary_bank_name',
                'field_label' => 'Bank Name',
                'field_type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'beneficiary_info',
                'field_name' => 'beneficiary_bank_account',
                'field_label' => 'Bank Account Number',
                'field_type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'beneficiary_info',
                'field_name' => 'beneficiary_bank_swift',
                'field_label' => 'SWIFT Code',
                'field_type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 12,
            ],

            // ========== PAYMENT INFORMATION SECTION ==========
            [
                'raf_form_id' => $form->id,
                'field_section' => 'payment_info',
                'field_name' => 'payment_method',
                'field_label' => 'Payment Method',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'field_options' => [
                    'bank_transfer' => 'Bank Transfer',
                    'cash' => 'Cash',
                    'check' => 'Check',
                    'credit_card' => 'Credit Card',
                    'debit_card' => 'Debit Card',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'payment_info',
                'field_name' => 'payment_source',
                'field_label' => 'Payment Source',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'field_options' => [
                    'personal_savings' => 'Personal Savings',
                    'salary' => 'Salary',
                    'business_income' => 'Business Income',
                    'investment' => 'Investment Returns',
                    'loan' => 'Loan',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'raf_form_id' => $form->id,
                'field_section' => 'payment_info',
                'field_name' => 'payment_currency',
                'field_label' => 'Payment Currency',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'field_options' => [
                    'USD' => 'USD',
                    'EUR' => 'EUR',
                    'GBP' => 'GBP',
                    'MYR' => 'MYR',
                    'SGD' => 'SGD',
                ],
                'validation_rules' => ['required' => true],
            ],
        ];

        foreach ($fields as $field) {
            $field = $this->processField($field);
            RafFormField::firstOrCreate(
                [
                    'raf_form_id' => $field['raf_form_id'],
                    'field_name' => $field['field_name'],
                ],
                $field
            );
        }

        $this->command->info('RAF form fields seeded successfully. (' . count($fields) . ' fields)');
    }

    /**
     * Seed DAR (Data Access Request) Form Fields
     * Based on DATA ACCESS REQUEST FORM (DAR).pdf
     */
    private function seedDarFields(): void
    {
        $form = DataAccessRequestForm::first();
        if (!$form) {
            $this->command->warn('No DAR form found. Skipping DAR fields seeding.');
            return;
        }

        $fields = [
            // ========== REQUESTER INFORMATION SECTION ==========
            [
                'dar_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_name',
                'field_label' => 'Requester Name',
                'field_type' => 'text',
                'field_placeholder' => 'Enter full name',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'validation_rules' => ['required' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_phone',
                'field_label' => 'Phone Number',
                'field_type' => 'phone',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'validation_rules' => ['required' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_email',
                'field_label' => 'Email Address',
                'field_type' => 'email',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'validation_rules' => ['required' => true, 'email' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_address',
                'field_label' => 'Address',
                'field_type' => 'textarea',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
                'validation_rules' => ['required' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_city',
                'field_label' => 'City',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
                'validation_rules' => ['required' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_state',
                'field_label' => 'State/Province',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 6,
                'validation_rules' => ['required' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_postal_code',
                'field_label' => 'Postal Code',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 7,
                'validation_rules' => ['required' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_country',
                'field_label' => 'Country',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 8,
                'validation_rules' => ['required' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_id_type',
                'field_label' => 'ID Type',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 9,
                'field_options' => [
                    'passport' => 'Passport',
                    'national_id' => 'National ID',
                    'drivers_license' => "Driver's License",
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_id_number',
                'field_label' => 'ID Number',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 10,
                'validation_rules' => ['required' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_id_expiry_date',
                'field_label' => 'ID Expiry Date',
                'field_type' => 'date',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_organization',
                'field_label' => 'Organization/Company',
                'field_type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_position',
                'field_label' => 'Position/Job Title',
                'field_type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 13,
            ],

            // ========== DATA SUBJECT INFORMATION SECTION ==========
            [
                'dar_form_id' => $form->id,
                'field_section' => 'data_subject_info',
                'field_name' => 'data_subject_name',
                'field_label' => 'Data Subject Name',
                'field_type' => 'text',
                'field_description' => 'Name of the person whose data is being requested',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'validation_rules' => ['required' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'data_subject_info',
                'field_name' => 'data_subject_phone',
                'field_label' => 'Phone Number',
                'field_type' => 'phone',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'data_subject_info',
                'field_name' => 'data_subject_email',
                'field_label' => 'Email Address',
                'field_type' => 'email',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'data_subject_info',
                'field_name' => 'data_subject_id_type',
                'field_label' => 'ID Type',
                'field_type' => 'select',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 4,
                'field_options' => [
                    'passport' => 'Passport',
                    'national_id' => 'National ID',
                    'drivers_license' => "Driver's License",
                    'other' => 'Other',
                ],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'data_subject_info',
                'field_name' => 'data_subject_id_number',
                'field_label' => 'ID Number',
                'field_type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'data_subject_info',
                'field_name' => 'relationship_to_data_subject',
                'field_label' => 'Relationship to Data Subject',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 6,
                'field_options' => [
                    'self' => 'Self',
                    'legal_guardian' => 'Legal Guardian',
                    'authorized_representative' => 'Authorized Representative',
                    'parent' => 'Parent',
                    'spouse' => 'Spouse',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],

            // ========== REQUEST DETAILS SECTION ==========
            [
                'dar_form_id' => $form->id,
                'field_section' => 'request_details',
                'field_name' => 'request_type',
                'field_label' => 'Request Type',
                'field_type' => 'radio',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'field_options' => [
                    'access' => 'Access',
                    'rectification' => 'Rectification',
                    'erasure' => 'Erasure',
                    'portability' => 'Data Portability',
                    'restriction' => 'Restriction of Processing',
                    'objection' => 'Objection to Processing',
                    'complaint' => 'Complaint',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'request_details',
                'field_name' => 'request_description',
                'field_label' => 'Request Description',
                'field_type' => 'textarea',
                'field_placeholder' => 'Please provide detailed description of what data you are requesting',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'validation_rules' => ['required' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'request_details',
                'field_name' => 'data_categories',
                'field_label' => 'Data Categories',
                'field_type' => 'checkbox',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'field_options' => [
                    'personal_info' => 'Personal Information',
                    'financial_data' => 'Financial Data',
                    'transaction_history' => 'Transaction History',
                    'account_details' => 'Account Details',
                    'contact_info' => 'Contact Information',
                    'identification' => 'Identification Documents',
                    'communication' => 'Communication Records',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'request_details',
                'field_name' => 'data_period_from',
                'field_label' => 'Data Period From',
                'field_type' => 'date',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'request_details',
                'field_name' => 'data_period_to',
                'field_label' => 'Data Period To',
                'field_type' => 'date',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'request_details',
                'field_name' => 'urgency_level',
                'field_label' => 'Urgency Level',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 6,
                'field_options' => [
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                    'urgent' => 'Urgent',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'request_details',
                'field_name' => 'legal_basis',
                'field_label' => 'Legal Basis',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 7,
                'field_options' => [
                    'consent' => 'Consent',
                    'contract' => 'Contract',
                    'legal_obligation' => 'Legal Obligation',
                    'vital_interests' => 'Vital Interests',
                    'public_task' => 'Public Task',
                    'legitimate_interests' => 'Legitimate Interests',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'dar_form_id' => $form->id,
                'field_section' => 'request_details',
                'field_name' => 'legal_basis_description',
                'field_label' => 'Legal Basis Description',
                'field_type' => 'textarea',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 8,
            ],
        ];

        foreach ($fields as $field) {
            $field = $this->processField($field);
            DarFormField::firstOrCreate(
                [
                    'dar_form_id' => $field['dar_form_id'],
                    'field_name' => $field['field_name'],
                ],
                $field
            );
        }

        $this->command->info('DAR form fields seeded successfully. (' . count($fields) . ' fields)');
    }

    /**
     * Seed DCR (Data Correction Request) Form Fields
     * Based on DATA CORRECTION REQUEST FORM (DCR).pdf
     */
    private function seedDcrFields(): void
    {
        $form = DataCorrectionRequestForm::first();
        if (!$form) {
            $this->command->warn('No DCR form found. Skipping DCR fields seeding.');
            return;
        }

        $fields = [
            // ========== REQUESTER INFORMATION SECTION ==========
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_name',
                'field_label' => 'Requester Name',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'validation_rules' => ['required' => true],
            ],
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_phone',
                'field_label' => 'Phone Number',
                'field_type' => 'phone',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'validation_rules' => ['required' => true],
            ],
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_email',
                'field_label' => 'Email Address',
                'field_type' => 'email',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'validation_rules' => ['required' => true, 'email' => true],
            ],
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_address',
                'field_label' => 'Address',
                'field_type' => 'textarea',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
                'validation_rules' => ['required' => true],
            ],
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_city',
                'field_label' => 'City',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
                'validation_rules' => ['required' => true],
            ],
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_state',
                'field_label' => 'State/Province',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 6,
                'validation_rules' => ['required' => true],
            ],
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_postal_code',
                'field_label' => 'Postal Code',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 7,
                'validation_rules' => ['required' => true],
            ],
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_country',
                'field_label' => 'Country',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 8,
                'validation_rules' => ['required' => true],
            ],
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_id_type',
                'field_label' => 'ID Type',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 9,
                'field_options' => [
                    'passport' => 'Passport',
                    'national_id' => 'National ID',
                    'drivers_license' => "Driver's License",
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'requester_info',
                'field_name' => 'requester_id_number',
                'field_label' => 'ID Number',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 10,
                'validation_rules' => ['required' => true],
            ],

            // ========== DATA SUBJECT INFORMATION SECTION ==========
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'data_subject_info',
                'field_name' => 'data_subject_name',
                'field_label' => 'Data Subject Name',
                'field_type' => 'text',
                'field_description' => 'Name of the person whose data needs correction',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'validation_rules' => ['required' => true],
            ],
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'data_subject_info',
                'field_name' => 'relationship_to_data_subject',
                'field_label' => 'Relationship to Data Subject',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'field_options' => [
                    'self' => 'Self',
                    'legal_guardian' => 'Legal Guardian',
                    'authorized_representative' => 'Authorized Representative',
                    'parent' => 'Parent',
                    'spouse' => 'Spouse',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],

            // ========== CORRECTION DETAILS SECTION ==========
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'correction_details',
                'field_name' => 'correction_type',
                'field_label' => 'Correction Type',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'field_options' => [
                    'personal_info' => 'Personal Information',
                    'contact_info' => 'Contact Information',
                    'financial_info' => 'Financial Information',
                    'demographic_info' => 'Demographic Information',
                    'preferences' => 'Preferences',
                    'account_info' => 'Account Information',
                    'transaction_data' => 'Transaction Data',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'correction_details',
                'field_name' => 'correction_description',
                'field_label' => 'Correction Description',
                'field_type' => 'textarea',
                'field_placeholder' => 'Please describe what data needs to be corrected',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'validation_rules' => ['required' => true],
            ],
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'correction_details',
                'field_name' => 'reason_for_correction',
                'field_label' => 'Reason for Correction',
                'field_type' => 'textarea',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'correction_details',
                'field_name' => 'urgency_level',
                'field_label' => 'Urgency Level',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
                'field_options' => [
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                    'urgent' => 'Urgent',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'dcr_form_id' => $form->id,
                'field_section' => 'correction_details',
                'field_name' => 'legal_basis',
                'field_label' => 'Legal Basis',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
                'field_options' => [
                    'consent' => 'Consent',
                    'contract' => 'Contract',
                    'legal_obligation' => 'Legal Obligation',
                    'data_accuracy_obligation' => 'Data Accuracy Obligation',
                    'vital_interests' => 'Vital Interests',
                    'public_task' => 'Public Task',
                    'legitimate_interests' => 'Legitimate Interests',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
        ];

        foreach ($fields as $field) {
            $field = $this->processField($field);
            DcrFormField::firstOrCreate(
                [
                    'dcr_form_id' => $field['dcr_form_id'],
                    'field_name' => $field['field_name'],
                ],
                $field
            );
        }

        $this->command->info('DCR form fields seeded successfully. (' . count($fields) . ' fields)');
    }

    /**
     * Seed SRF (Service Request Form) Fields
     * Based on Service Request Form (SRF)_v16.0_DEPOSIT.xlsx
     */
    private function seedSrfFields(): void
    {
        $form = ServiceRequestForm::first();
        if (!$form) {
            $this->command->warn('No SRF form found. Skipping SRF fields seeding.');
            return;
        }

        $fields = [
            // ========== CUSTOMER INFORMATION SECTION ==========
            [
                'srf_form_id' => $form->id,
                'field_section' => 'customer_info',
                'field_name' => 'customer_name',
                'field_label' => 'Customer Name',
                'field_type' => 'text',
                'field_placeholder' => 'Enter full name',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'validation_rules' => ['required' => true],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'customer_info',
                'field_name' => 'customer_phone',
                'field_label' => 'Phone Number',
                'field_type' => 'phone',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'validation_rules' => ['required' => true],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'customer_info',
                'field_name' => 'customer_email',
                'field_label' => 'Email Address',
                'field_type' => 'email',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'validation_rules' => ['required' => true, 'email' => true],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'customer_info',
                'field_name' => 'customer_address',
                'field_label' => 'Address',
                'field_type' => 'textarea',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
                'validation_rules' => ['required' => true],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'customer_info',
                'field_name' => 'customer_city',
                'field_label' => 'City',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
                'validation_rules' => ['required' => true],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'customer_info',
                'field_name' => 'customer_state',
                'field_label' => 'State/Province',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 6,
                'validation_rules' => ['required' => true],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'customer_info',
                'field_name' => 'customer_postal_code',
                'field_label' => 'Postal Code',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 7,
                'validation_rules' => ['required' => true],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'customer_info',
                'field_name' => 'customer_country',
                'field_label' => 'Country',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 8,
                'validation_rules' => ['required' => true],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'customer_info',
                'field_name' => 'customer_id_type',
                'field_label' => 'ID Type',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 9,
                'field_options' => [
                    'passport' => 'Passport',
                    'national_id' => 'National ID',
                    'drivers_license' => "Driver's License",
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'customer_info',
                'field_name' => 'customer_id_number',
                'field_label' => 'ID Number',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 10,
                'validation_rules' => ['required' => true],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'customer_info',
                'field_name' => 'customer_dob',
                'field_label' => 'Date of Birth',
                'field_type' => 'date',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'customer_info',
                'field_name' => 'customer_gender',
                'field_label' => 'Gender',
                'field_type' => 'select',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 12,
                'field_options' => [
                    'male' => 'Male',
                    'female' => 'Female',
                    'other' => 'Other',
                    'prefer_not_to_say' => 'Prefer Not to Say',
                ],
            ],

            // ========== ACCOUNT INFORMATION SECTION ==========
            [
                'srf_form_id' => $form->id,
                'field_section' => 'account_info',
                'field_name' => 'account_number',
                'field_label' => 'Account Number',
                'field_type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'account_info',
                'field_name' => 'account_type',
                'field_label' => 'Account Type',
                'field_type' => 'select',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 2,
                'field_options' => [
                    'savings' => 'Savings',
                    'checking' => 'Checking',
                    'business' => 'Business',
                    'current' => 'Current',
                    'other' => 'Other',
                ],
            ],

            // ========== SERVICE DETAILS SECTION ==========
            [
                'srf_form_id' => $form->id,
                'field_section' => 'service_details',
                'field_name' => 'service_type',
                'field_label' => 'Service Type',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'field_options' => [
                    'deposit' => 'Deposit',
                    'withdrawal' => 'Withdrawal',
                    'transfer' => 'Transfer',
                    'account_opening' => 'Account Opening',
                    'account_closure' => 'Account Closure',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'service_details',
                'field_name' => 'service_category',
                'field_label' => 'Service Category',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'field_options' => [
                    'banking' => 'Banking',
                    'investment' => 'Investment',
                    'insurance' => 'Insurance',
                    'loan' => 'Loan',
                    'credit_card' => 'Credit Card',
                    'foreign_exchange' => 'Foreign Exchange',
                    'international_transfer' => 'International Transfer',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'service_details',
                'field_name' => 'service_description',
                'field_label' => 'Service Description',
                'field_type' => 'textarea',
                'field_placeholder' => 'Please provide detailed description of the service requested',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'validation_rules' => ['required' => true],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'service_details',
                'field_name' => 'service_amount',
                'field_label' => 'Service Amount',
                'field_type' => 'currency',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 4,
                'field_settings' => ['currency' => 'MYR'],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'service_details',
                'field_name' => 'urgency_level',
                'field_label' => 'Urgency Level',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
                'field_options' => [
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                    'urgent' => 'Urgent',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'service_details',
                'field_name' => 'preferred_completion_date',
                'field_label' => 'Preferred Completion Date',
                'field_type' => 'date',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 6,
            ],

            // ========== DEPOSIT SPECIFIC FIELDS (for DEPOSIT type) ==========
            [
                'srf_form_id' => $form->id,
                'field_section' => 'deposit_details',
                'field_name' => 'deposit_type',
                'field_label' => 'Deposit Type',
                'field_type' => 'select',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 1,
                'field_options' => [
                    'cash' => 'Cash',
                    'check' => 'Check',
                    'wire_transfer' => 'Wire Transfer',
                    'ach_transfer' => 'ACH Transfer',
                    'mobile_deposit' => 'Mobile Deposit',
                    'atm_deposit' => 'ATM Deposit',
                    'in_person' => 'In Person',
                    'online' => 'Online',
                    'other' => 'Other',
                ],
                'is_conditional' => true,
                'conditional_logic' => [
                    'show_if' => [
                        'field' => 'service_type',
                        'operator' => 'equals',
                        'value' => 'deposit',
                    ],
                ],
            ],
            [
                'srf_form_id' => $form->id,
                'field_section' => 'deposit_details',
                'field_name' => 'deposit_method',
                'field_label' => 'Deposit Method',
                'field_type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 2,
                'is_conditional' => true,
                'conditional_logic' => [
                    'show_if' => [
                        'field' => 'service_type',
                        'operator' => 'equals',
                        'value' => 'deposit',
                    ],
                ],
            ],
        ];

        foreach ($fields as $field) {
            $field = $this->processField($field);
            SrfFormField::firstOrCreate(
                [
                    'srf_form_id' => $field['srf_form_id'],
                    'field_name' => $field['field_name'],
                ],
                $field
            );
        }

        $this->command->info('SRF form fields seeded successfully. (' . count($fields) . ' fields)');
    }
}
