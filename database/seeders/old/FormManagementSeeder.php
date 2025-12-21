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
     * Creates forms, sections, and fields (currently only SRF enabled for testing)
     * Following the flow: Forms → Sections → Fields
     * Note: RAF, DAR, and DCR forms are disabled - uncomment to enable them
     */
    public function run(): void
    {
        // Seed RAF Form - DISABLED (only SRF enabled for testing)
        // $this->seedRafForm();

        // Seed DAR Form (form only, no sections/fields)
        $this->seedDarForm();

        // Seed DCR Form (form only, no sections/fields)
        $this->seedDcrForm();

        // Seed SRF Form
        $this->seedSrfForm();

    }

    /**
     * Seed RAF (Remittance Application Form)
     */
    private function seedRafForm(): void
    {
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
                'sort_order' => 1,
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

    }


    /**
     * Seed DAR (Data Access Request Form)
     */
    private function seedDarForm(): void
    {
        $form = Form::updateOrCreate(
            ['slug' => 'dar'],
            [
                'name' => 'Personal Data Access Request Form',
                'description' => 'Request access to your personal data and information in compliance with data protection regulations.',
                'status' => 'active',
                'is_public' => true,
                'allow_multiple_submissions' => true,
                'submission_limit' => null,
                'sort_order' => 2,
                'settings' => [
                    'type' => 'dar',
                    'version' => '1.0',
                ],
            ]
        );

        // Read sections from JSON export
        $jsonData = json_decode(file_get_contents(__DIR__ . '/exports/dar_form_export.json'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return;
        }

        // Create sections and fields from JSON
        if (isset($jsonData['sections']) && is_array($jsonData['sections'])) {
            foreach ($jsonData['sections'] as $sectionData) {
                $section = FormSection::updateOrCreate(
                    [
                        'form_id' => $form->id,
                        'section_key' => $sectionData['key'],
                    ],
                    [
                        'section_label' => $sectionData['description'] ?? $sectionData['title'] ?? '',
                        'sort_order' => $sectionData['sort_order'] ?? 1,
                        'is_active' => $sectionData['is_active'] ?? true,
                    ]
                );

                // Create fields for this section
                if (isset($sectionData['fields']) && is_array($sectionData['fields'])) {
                    foreach ($sectionData['fields'] as $fieldData) {
                        FormField::updateOrCreate(
                            [
                                'form_id' => $form->id,
                                'section_id' => $section->id,
                                'field_name' => $fieldData['name'],
                            ],
                            [
                                'field_label' => $fieldData['label'] ?? '',
                                'field_type' => $fieldData['type'] ?? 'text',
                                'field_placeholder' => $fieldData['placeholder'] ?? null,
                                'field_description' => $fieldData['description'] ?? null,
                                'field_help_text' => $fieldData['help_text'] ?? null,
                                'is_required' => $fieldData['required'] ?? false,
                                'is_active' => $fieldData['active'] ?? true,
                                'sort_order' => $fieldData['sort_order'] ?? 1,
                                'grid_column' => $fieldData['grid_column'] ?? 'full',
                                'is_conditional' => $fieldData['conditional'] ?? false,
                                'conditional_logic' => $fieldData['conditional_logic'] ?? null,
                                'field_options' => $fieldData['options'] ?? null,
                                'field_settings' => $fieldData['settings'] ?? null,
                                'validation_rules' => $fieldData['validation'] ?? null,
                            ]
                        );
                    }
                }
            }
        }
    }

    /**
     * Seed DCR (Data Correction Request Form)
     */
    private function seedDcrForm(): void
    {
        $form = Form::updateOrCreate(
            ['slug' => 'dcr'],
            [
                'name' => 'Personal Data Correction Request Form',
                'description' => 'Request correction of your personal data in compliance with data protection regulations.',
                'status' => 'active',
                'is_public' => true,
                'allow_multiple_submissions' => true,
                'submission_limit' => null,
                'sort_order' => 3,
                'settings' => [
                    'type' => 'dcr',
                    'version' => '1.0',
                ],
            ]
        );

        // Read sections from JSON export
        $jsonData = json_decode(file_get_contents(__DIR__ . '/exports/dcr_form_export.json'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return;
        }

        // Create sections and fields from JSON
        if (isset($jsonData['sections']) && is_array($jsonData['sections'])) {
            foreach ($jsonData['sections'] as $sectionData) {
                $section = FormSection::updateOrCreate(
                    [
                        'form_id' => $form->id,
                        'section_key' => $sectionData['key'],
                    ],
                    [
                        'section_label' => $sectionData['description'] ?? $sectionData['title'] ?? '',
                        'sort_order' => $sectionData['sort_order'] ?? 1,
                        'is_active' => $sectionData['is_active'] ?? true,
                    ]
                );

                // Create fields for this section
                if (isset($sectionData['fields']) && is_array($sectionData['fields'])) {
                    foreach ($sectionData['fields'] as $fieldData) {
                        FormField::updateOrCreate(
                            [
                                'form_id' => $form->id,
                                'section_id' => $section->id,
                                'field_name' => $fieldData['name'],
                            ],
                            [
                                'field_label' => $fieldData['label'] ?? '',
                                'field_type' => $fieldData['type'] ?? 'text',
                                'field_placeholder' => $fieldData['placeholder'] ?? null,
                                'field_description' => $fieldData['description'] ?? null,
                                'field_help_text' => $fieldData['help_text'] ?? null,
                                'is_required' => $fieldData['required'] ?? false,
                                'is_active' => $fieldData['active'] ?? true,
                                'sort_order' => $fieldData['sort_order'] ?? 1,
                                'grid_column' => $fieldData['grid_column'] ?? 'full',
                                'is_conditional' => $fieldData['conditional'] ?? false,
                                'conditional_logic' => $fieldData['conditional_logic'] ?? null,
                                'field_options' => $fieldData['options'] ?? null,
                                'field_settings' => $fieldData['settings'] ?? null,
                                'validation_rules' => $fieldData['validation'] ?? null,
                            ]
                        );
                    }
                }
            }
        }
    }

    /**
     * Seed SRF (Service Request Form)
     */
    private function seedSrfForm(): void
    {

        $form = Form::updateOrCreate(
            ['slug' => 'srf'],
            [
                'name' => 'Service Request Form',
                'description' => 'Service Request Form (SRF_Deposit/V16.0.2024)',
                'status' => 'active',
                'is_public' => true,
                'allow_multiple_submissions' => true,
                'submission_limit' => null,
                'sort_order' => 4,
                'settings' => [
                    'type' => 'srf',
                    'version' => '16.0',
                ],
            ]
        );

        $sections = $this->createSections($form->id, 'srf', [
            'customer_info' => 'Customer',
            'account_type' => 'Account',
            'consent' => 'Consent',
            'section_c' => 'Third Party',
            'confirmation' => 'Confirmation',
        ]);

        $this->createSrfFields($form->id, $sections);

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
                'field_name' => 'applicant_other_id_type',
                'field_label' => 'Other ID Type Specification',
                'field_type' => 'text',
                'field_placeholder' => 'Please specify the ID type',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 11,
                'is_conditional' => true,
                'conditional_logic' => [
                    'show_if' => [
                        'field' => 'applicant_id_type',
                        'operator' => 'equals',
                        'value' => 'other',
                    ],
                ],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['applicant_info'],
                'field_name' => 'applicant_id_expiry_date',
                'field_label' => 'ID Expiry Date',
                'field_type' => 'date',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['applicant_info'],
                'field_name' => 'preferred_contact_method',
                'field_label' => 'Preferred Contact Method',
                'field_type' => 'checkbox',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 13,
                'field_options' => [
                    'email' => 'Email',
                    'phone' => 'Phone',
                    'sms' => 'SMS',
                ],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['applicant_info'],
                'field_name' => 'alternate_email',
                'field_label' => 'Alternate Email Address',
                'field_type' => 'email',
                'field_placeholder' => 'alternate.email@example.com',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 14,
                'is_conditional' => true,
                'conditional_logic' => [
                    'show_if' => [
                        'field' => 'preferred_contact_method',
                        'operator' => 'equals',
                        'value' => 'email',
                    ],
                ],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['applicant_info'],
                'field_name' => 'alternate_phone',
                'field_label' => 'Alternate Phone Number',
                'field_type' => 'phone',
                'field_placeholder' => '+60 12-345 6789',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 15,
                'is_conditional' => true,
                'conditional_logic' => [
                    'show_if' => [
                        'field' => 'preferred_contact_method',
                        'operator' => 'equals',
                        'value' => 'phone',
                    ],
                ],
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
            [
                'form_id' => $formId,
                'section_id' => $sections['payment_info'],
                'field_name' => 'payment_method_other',
                'field_label' => 'Other Payment Method Details',
                'field_type' => 'text',
                'field_placeholder' => 'Please specify the payment method',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 4,
                'is_conditional' => true,
                'conditional_logic' => [
                    'show_if' => [
                        'field' => 'payment_method',
                        'operator' => 'equals',
                        'value' => 'other',
                    ],
                ],
            ],
            [
                'form_id' => $formId,
                'section_id' => $sections['payment_info'],
                'field_name' => 'payment_source_other',
                'field_label' => 'Other Payment Source Details',
                'field_type' => 'text',
                'field_placeholder' => 'Please specify the payment source',
                'is_required' => false,
                'is_active' => true,
                'sort_order' => 5,
                'is_conditional' => true,
                'conditional_logic' => [
                    'show_if' => [
                        'field' => 'payment_source',
                        'operator' => 'equals',
                        'value' => 'other',
                    ],
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

    }


    /**
     * Create SRF fields
     * 
     * This method reads the complete SRF form structure from exports/srf_form_export.json
     * The JSON file is automatically generated from the current database structure.
     * 
     * Supported field types: text, email, phone, number, textarea, select, radio, 
     * checkbox, date, file, signature, currency, multiselect, time, datetime, repeater
     * 
     * To update the seeder:
     * 1. Make changes to the form in the admin panel
     * 2. Run: php artisan tinker (then use export script)
     * 3. Or manually update exports/srf_form_export.json
     * 4. Run: php artisan db:seed --class=FormManagementSeeder
     */
    private function createSrfFields($formId, array $sections): void
    {
        // Read form structure from JSON export file
        $fields = json_decode(file_get_contents(__DIR__ . '/exports/srf_form_export.json'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Error reading srf_form_export.json: ' . json_last_error_msg());
            return;
        }

        // Convert JSON structure to seeder format
        $seederFields = [];
        foreach ($fields['sections'] as $section) {
            foreach ($section['fields'] as $field) {
                $fieldData = [
                    'form_id' => $formId,
                    'section_id' => $sections[$section['key']],
                    'field_name' => $field['name'],
                    'field_label' => $field['label'],
                    'field_type' => $field['type'],
                    'is_required' => $field['required'],
                    'is_active' => $field['active'],
                    'sort_order' => $field['sort_order'],
                    'grid_column' => $field['grid_column'],
                ];

                if (!empty($field['placeholder'])) {
                    $fieldData['field_placeholder'] = $field['placeholder'];
                }

                if (!empty($field['description']) && $field['description'] !== '<p><br></p>') {
                    $fieldData['field_description'] = $field['description'];
                }

                if (!empty($field['help_text'])) {
                    $fieldData['field_help_text'] = $field['help_text'];
                }

                if (!empty($field['options'])) {
                    $fieldData['field_options'] = $field['options'];
                }

                // Handle field settings (includes description_position, repeater columns, etc.)
                if (!empty($field['settings'])) {
                    $fieldData['field_settings'] = $field['settings'];
                }

                // Handle conditional logic
                if ($field['conditional']) {
                    $fieldData['is_conditional'] = true;
                    if (!empty($field['conditional_logic'])) {
                        $fieldData['conditional_logic'] = $field['conditional_logic'];
                    }
                }

                // Handle validation rules
                if (!empty($field['validation'])) {
                    $fieldData['validation_rules'] = $field['validation'];
                }

                $seederFields[] = $fieldData;
            }
        }

        foreach ($seederFields as $field) {
            $field = $this->processField($field);
            FormField::updateOrCreate(
                [
                    'form_id' => $field['form_id'],
                    'field_name' => $field['field_name'],
                ],
                $field
            );
        }

    }
}

