<?php

namespace Database\Seeders;

use App\Models\Form;
use App\Models\FormSection;
use App\Models\FormField;
use Illuminate\Database\Seeder;

class FormManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates forms, sections, and fields based on the 4 default forms
     * Following the flow: Forms → Sections → Fields
     */
    public function run(): void
    {
        $this->command->info('Starting Form Management Seeder...');

        // Seed RAF Form
        $this->seedRafForm();
        
        // Seed DAR Form
        $this->seedDarForm();
        
        // Seed DCR Form
        $this->seedDcrForm();
        
        // Seed SRF Form
        $this->seedSrfForm();

        $this->command->info('Form Management Seeder completed successfully!');
    }

    /**
     * Seed RAF (Remittance Application Form)
     */
    private function seedRafForm(): void
    {
        $this->command->info('Seeding RAF Form...');

        // Create or get RAF form
        $form = Form::updateOrCreate(
            ['slug' => 'raf'],
            [
                'name' => 'Remittance Application Form',
                'description' => 'Submit your remittance application for international money transfers and financial transactions.',
                'status' => 'active',
                'is_public' => true,
                'allow_multiple_submissions' => true,
                'submission_limit' => null,
                'settings' => [
                    'type' => 'raf',
                    'version' => '5.0',
                ],
            ]
        );

        // Create sections
        $sections = $this->createSections($form->id, 'raf', [
            'applicant_info' => 'Applicant Information',
            'remittance_details' => 'Remittance Details',
            'beneficiary_info' => 'Beneficiary Information',
            'payment_info' => 'Payment Information',
            'documents' => 'Supporting Documents',
            'compliance' => 'Compliance & Verification',
        ]);

        // Create fields
        $this->createRafFields($form->id, $sections);

        $this->command->info("RAF Form seeded: {$form->name} (ID: {$form->id})");
    }

    /**
     * Seed DAR (Data Access Request Form)
     */
    private function seedDarForm(): void
    {
        $this->command->info('Seeding DAR Form...');

        $form = Form::updateOrCreate(
            ['slug' => 'dar'],
            [
                'name' => 'Data Access Request Form',
                'description' => 'Request access to your personal data and information in compliance with data protection regulations.',
                'status' => 'active',
                'is_public' => true,
                'allow_multiple_submissions' => true,
                'submission_limit' => null,
                'settings' => [
                    'type' => 'dar',
                    'version' => '1.0',
                ],
            ]
        );

        $sections = $this->createSections($form->id, 'dar', [
            'requester_info' => 'Requester Information',
            'data_subject_info' => 'Data Subject Information',
            'request_details' => 'Request Details',
            'legal_basis' => 'Legal Basis',
            'data_processing' => 'Data Processing Information',
            'documents' => 'Supporting Documents',
            'compliance' => 'Compliance & Verification',
        ]);

        $this->createDarFields($form->id, $sections);

        $this->command->info("DAR Form seeded: {$form->name} (ID: {$form->id})");
    }

    /**
     * Seed DCR (Data Correction Request Form)
     */
    private function seedDcrForm(): void
    {
        $this->command->info('Seeding DCR Form...');

        $form = Form::updateOrCreate(
            ['slug' => 'dcr'],
            [
                'name' => 'Data Correction Request Form',
                'description' => 'Request correction of your personal data in compliance with data protection regulations.',
                'status' => 'active',
                'is_public' => true,
                'allow_multiple_submissions' => true,
                'submission_limit' => null,
                'settings' => [
                    'type' => 'dcr',
                    'version' => '1.0',
                ],
            ]
        );

        $sections = $this->createSections($form->id, 'dcr', [
            'requester_info' => 'Requester Information',
            'data_subject_info' => 'Data Subject Information',
            'correction_details' => 'Correction Details',
            'legal_basis' => 'Legal Basis',
            'verification' => 'Verification Process',
            'implementation' => 'Implementation Plan',
            'documents' => 'Supporting Documents',
            'compliance' => 'Compliance & Verification',
        ]);

        $this->createDcrFields($form->id, $sections);

        $this->command->info("DCR Form seeded: {$form->name} (ID: {$form->id})");
    }

    /**
     * Seed SRF (Service Request Form)
     */
    private function seedSrfForm(): void
    {
        $this->command->info('Seeding SRF Form...');

        $form = Form::updateOrCreate(
            ['slug' => 'srf'],
            [
                'name' => 'Service Request Form',
                'description' => 'Submit your service request for banking and financial services.',
                'status' => 'active',
                'is_public' => true,
                'allow_multiple_submissions' => true,
                'submission_limit' => null,
                'settings' => [
                    'type' => 'srf',
                    'version' => '16.0',
                ],
            ]
        );

        $sections = $this->createSections($form->id, 'srf', [
            'customer_info' => 'Customer Information',
            'account_info' => 'Account Information',
            'service_details' => 'Service Details',
            'financial_info' => 'Financial Information',
            'compliance' => 'Compliance & Risk',
            'documents' => 'Supporting Documents',
            'delivery' => 'Service Delivery',
        ]);

        $this->createSrfFields($form->id, $sections);

        $this->command->info("SRF Form seeded: {$form->name} (ID: {$form->id})");
    }

    /**
     * Create sections for a form
     */
    private function createSections($formId, $formType, array $sectionData): array
    {
        $sections = [];
        $sortOrder = 1;

        foreach ($sectionData as $key => $label) {
            $section = FormSection::updateOrCreate(
                [
                    'form_id' => $formId,
                    'section_key' => $key,
                ],
                [
                    'section_label' => $label,
                    'sort_order' => $sortOrder++,
                    'is_active' => true,
                ]
            );
            $sections[$key] = $section->id;
        }

        return $sections;
    }

    /**
     * Determine grid column position based on field type
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
     * Create RAF fields
     */
    private function createRafFields($formId, array $sections): void
    {
        $fields = [
            // ========== APPLICANT INFORMATION SECTION ==========
            [
                'form_id' => $formId,
                'section_id' => $sections['applicant_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['applicant_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['applicant_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['applicant_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['applicant_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['applicant_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['applicant_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['applicant_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['applicant_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['applicant_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['applicant_info'],
                'field_name' => 'applicant_id_expiry_date',
                'field_label' => 'ID Expiry Date',
                'field_type' => 'date',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 11,
            ],

            // ========== REMITTANCE DETAILS SECTION ==========
            [
                'form_id' => $formId,
                'section_id' => $sections['remittance_details'],
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
                'form_id' => $formId,
                'section_id' => $sections['remittance_details'],
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
                'form_id' => $formId,
                'section_id' => $sections['remittance_details'],
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
                'form_id' => $formId,
                'section_id' => $sections['remittance_details'],
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
                'form_id' => $formId,
                'section_id' => $sections['remittance_details'],
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
                'form_id' => $formId,
                'section_id' => $sections['beneficiary_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['beneficiary_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['beneficiary_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['beneficiary_info'],
                'field_name' => 'beneficiary_city',
                'field_label' => 'City',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['beneficiary_info'],
                'field_name' => 'beneficiary_state',
                'field_label' => 'State/Province',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['beneficiary_info'],
                'field_name' => 'beneficiary_postal_code',
                'field_label' => 'Postal Code',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 6,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['beneficiary_info'],
                'field_name' => 'beneficiary_country',
                'field_label' => 'Country',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 7,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['beneficiary_info'],
                'field_name' => 'beneficiary_phone',
                'field_label' => 'Phone Number',
                'field_type' => 'phone',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['beneficiary_info'],
                'field_name' => 'beneficiary_email',
                'field_label' => 'Email Address',
                'field_type' => 'email',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['beneficiary_info'],
                'field_name' => 'beneficiary_bank_name',
                'field_label' => 'Bank Name',
                'field_type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['beneficiary_info'],
                'field_name' => 'beneficiary_bank_account',
                'field_label' => 'Bank Account Number',
                'field_type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['beneficiary_info'],
                'field_name' => 'beneficiary_bank_swift',
                'field_label' => 'SWIFT Code',
                'field_type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 12,
            ],

            // ========== PAYMENT INFORMATION SECTION ==========
            [
                'form_id' => $formId,
                'section_id' => $sections['payment_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['payment_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['payment_info'],
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
            FormField::updateOrCreate(
                [
                    'form_id' => $field['form_id'],
                    'field_name' => $field['field_name'],
                ],
                $field
            );
        }

        $this->command->info('RAF fields created: ' . count($fields) . ' fields');
    }

    /**
     * Create DAR fields
     */
    private function createDarFields($formId, array $sections): void
    {
        $fields = [
            // ========== REQUESTER INFORMATION SECTION ==========
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_phone',
                'field_label' => 'Phone Number',
                'field_type' => 'phone',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_email',
                'field_label' => 'Email Address',
                'field_type' => 'email',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'validation_rules' => ['required' => true, 'email' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_address',
                'field_label' => 'Address',
                'field_type' => 'textarea',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_city',
                'field_label' => 'City',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_state',
                'field_label' => 'State/Province',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 6,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_postal_code',
                'field_label' => 'Postal Code',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 7,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_country',
                'field_label' => 'Country',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 8,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
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
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_id_number',
                'field_label' => 'ID Number',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 10,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_id_expiry_date',
                'field_label' => 'ID Expiry Date',
                'field_type' => 'date',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_organization',
                'field_label' => 'Organization',
                'field_type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_position',
                'field_label' => 'Position',
                'field_type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 13,
            ],

            // ========== DATA SUBJECT INFORMATION SECTION ==========
            [
                'form_id' => $formId,
                'section_id' => $sections['data_subject_info'],
                'field_name' => 'data_subject_name',
                'field_label' => 'Data Subject Name',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['data_subject_info'],
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
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['data_subject_info'],
                'field_name' => 'data_subject_phone',
                'field_label' => 'Phone Number',
                'field_type' => 'phone',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['data_subject_info'],
                'field_name' => 'data_subject_email',
                'field_label' => 'Email Address',
                'field_type' => 'email',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['data_subject_info'],
                'field_name' => 'data_subject_address',
                'field_label' => 'Address',
                'field_type' => 'textarea',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['data_subject_info'],
                'field_name' => 'data_subject_id_type',
                'field_label' => 'ID Type',
                'field_type' => 'select',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 6,
                'field_options' => [
                    'passport' => 'Passport',
                    'national_id' => 'National ID',
                    'drivers_license' => "Driver's License",
                    'other' => 'Other',
                ],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['data_subject_info'],
                'field_name' => 'data_subject_id_number',
                'field_label' => 'ID Number',
                'field_type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 7,
            ],

            // ========== REQUEST DETAILS SECTION ==========
            [
                'form_id' => $formId,
                'section_id' => $sections['request_details'],
                'field_name' => 'request_type',
                'field_label' => 'Request Type',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'field_options' => [
                    'access' => 'Access',
                    'rectification' => 'Rectification',
                    'erasure' => 'Erasure',
                    'portability' => 'Portability',
                    'restriction' => 'Restriction',
                    'objection' => 'Objection',
                    'complaint' => 'Complaint',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['request_details'],
                'field_name' => 'request_description',
                'field_label' => 'Request Description',
                'field_type' => 'textarea',
                'field_placeholder' => 'Describe what data you are requesting access to',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['request_details'],
                'field_name' => 'data_categories',
                'field_label' => 'Data Categories',
                'field_type' => 'multiselect',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 3,
                'field_options' => [
                    'personal_info' => 'Personal Information',
                    'contact_info' => 'Contact Information',
                    'financial_info' => 'Financial Information',
                    'transaction_history' => 'Transaction History',
                    'account_info' => 'Account Information',
                    'other' => 'Other',
                ],
            ],
        ];

        foreach ($fields as $field) {
            $field = $this->processField($field);
            FormField::updateOrCreate(
                [
                    'form_id' => $field['form_id'],
                    'field_name' => $field['field_name'],
                ],
                $field
            );
        }

        $this->command->info('DAR fields created: ' . count($fields) . ' fields');
    }

    /**
     * Create DCR fields
     */
    private function createDcrFields($formId, array $sections): void
    {
        $fields = [
            // ========== REQUESTER INFORMATION SECTION ==========
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_name',
                'field_label' => 'Requester Name',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_phone',
                'field_label' => 'Phone Number',
                'field_type' => 'phone',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_email',
                'field_label' => 'Email Address',
                'field_type' => 'email',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'validation_rules' => ['required' => true, 'email' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_address',
                'field_label' => 'Address',
                'field_type' => 'textarea',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_id_type',
                'field_label' => 'ID Type',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
                'field_options' => [
                    'passport' => 'Passport',
                    'national_id' => 'National ID',
                    'drivers_license' => "Driver's License",
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['requester_info'],
                'field_name' => 'requester_id_number',
                'field_label' => 'ID Number',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 6,
                'validation_rules' => ['required' => true],
            ],

            // ========== DATA SUBJECT INFORMATION SECTION ==========
            [
                'form_id' => $formId,
                'section_id' => $sections['data_subject_info'],
                'field_name' => 'data_subject_name',
                'field_label' => 'Data Subject Name',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['data_subject_info'],
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
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],

            // ========== CORRECTION DETAILS SECTION ==========
            [
                'form_id' => $formId,
                'section_id' => $sections['correction_details'],
                'field_name' => 'incorrect_data_field',
                'field_label' => 'Incorrect Data Field',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['correction_details'],
                'field_name' => 'incorrect_data_value',
                'field_label' => 'Incorrect Data Value',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['correction_details'],
                'field_name' => 'correct_data_value',
                'field_label' => 'Correct Data Value',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['correction_details'],
                'field_name' => 'correction_reason',
                'field_label' => 'Reason for Correction',
                'field_type' => 'textarea',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['correction_details'],
                'field_name' => 'supporting_evidence',
                'field_label' => 'Supporting Evidence',
                'field_type' => 'file',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($fields as $field) {
            $field = $this->processField($field);
            FormField::updateOrCreate(
                [
                    'form_id' => $field['form_id'],
                    'field_name' => $field['field_name'],
                ],
                $field
            );
        }

        $this->command->info('DCR fields created: ' . count($fields) . ' fields');
    }

    /**
     * Create SRF fields
     */
    private function createSrfFields($formId, array $sections): void
    {
        $fields = [
            // ========== CUSTOMER INFORMATION SECTION ==========
            [
                'form_id' => $formId,
                'section_id' => $sections['customer_info'],
                'field_name' => 'customer_name',
                'field_label' => 'Customer Name',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['customer_info'],
                'field_name' => 'customer_phone',
                'field_label' => 'Phone Number',
                'field_type' => 'phone',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['customer_info'],
                'field_name' => 'customer_email',
                'field_label' => 'Email Address',
                'field_type' => 'email',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
                'validation_rules' => ['required' => true, 'email' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['customer_info'],
                'field_name' => 'customer_address',
                'field_label' => 'Address',
                'field_type' => 'textarea',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['customer_info'],
                'field_name' => 'customer_id_type',
                'field_label' => 'ID Type',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
                'field_options' => [
                    'passport' => 'Passport',
                    'national_id' => 'National ID',
                    'drivers_license' => "Driver's License",
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['customer_info'],
                'field_name' => 'customer_id_number',
                'field_label' => 'ID Number',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 6,
                'validation_rules' => ['required' => true],
            ],

            // ========== ACCOUNT INFORMATION SECTION ==========
            [
                'form_id' => $formId,
                'section_id' => $sections['account_info'],
                'field_name' => 'account_number',
                'field_label' => 'Account Number',
                'field_type' => 'text',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['account_info'],
                'field_name' => 'account_type',
                'field_label' => 'Account Type',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'field_options' => [
                    'savings' => 'Savings Account',
                    'current' => 'Current Account',
                    'fixed_deposit' => 'Fixed Deposit',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],

            // ========== SERVICE DETAILS SECTION ==========
            [
                'form_id' => $formId,
                'section_id' => $sections['service_details'],
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
                    'statement' => 'Statement Request',
                    'other' => 'Other',
                ],
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['service_details'],
                'field_name' => 'service_description',
                'field_label' => 'Service Description',
                'field_type' => 'textarea',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
                'validation_rules' => ['required' => true],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['service_details'],
                'field_name' => 'service_amount',
                'field_label' => 'Service Amount',
                'field_type' => 'currency',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 3,
                'field_settings' => ['currency' => 'MYR'],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['service_details'],
                'field_name' => 'service_priority',
                'field_label' => 'Priority',
                'field_type' => 'select',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
                'field_options' => [
                    'low' => 'Low',
                    'normal' => 'Normal',
                    'high' => 'High',
                    'urgent' => 'Urgent',
                ],
                'validation_rules' => ['required' => true],
            ],
        ];

        foreach ($fields as $field) {
            $field = $this->processField($field);
            FormField::updateOrCreate(
                [
                    'form_id' => $field['form_id'],
                    'field_name' => $field['field_name'],
                ],
                $field
            );
        }

        $this->command->info('SRF fields created: ' . count($fields) . ' fields');
    }
}

