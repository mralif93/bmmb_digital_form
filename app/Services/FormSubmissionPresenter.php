<?php

namespace App\Services;

use App\Models\FormSubmission;

/**
 * Service to present form submissions in a human-readable format
 * Maps technical field names to user-friendly labels
 */
class FormSubmissionPresenter
{
    /**
     * Field label mappings for each form type
     * Maps field_name => human-readable label
     */
    private static $fieldMappings = [
        // Service Request Form (SRF) Field Mappings
        'srf' => [
            // Section 1: Customer Information
            'header_1' => 'Customer / Company Name',
            'header_2' => 'Account Holder',
            'header_3' => 'ID No. / Business Registration No.',
            'header_4' => 'Account No.',

            // Service Fields
            'field_1' => 'Cash Withdrawal Service',
            'field_1_1' => 'Account Number',
            'field_1_2' => 'Currency',
            'field_1_3' => 'Amount',

            'field_2' => 'Foreign Currency Exchange',
            'field_2_1' => 'From Currency',
            'field_2_2' => 'To Currency',
            'field_2_3' => 'Amount',

            'field_3' => 'Cheque Book Request',
            'field_3_1' => 'Account Number',
            'field_3_2' => 'Number of Leaves',
            'field_3_3' => 'Collection Method',

            'field_4' => 'Statement Request',
            'field_4_1' => 'Period/Date Range',

            'field_5' => 'Balance Inquiry',
            'field_6' => 'Account Opening',
            'field_7' => 'Account Closure',

            'field_8' => 'Fixed Deposit',
            'field_8_1' => 'Amount',
            'field_8_2' => 'Tenure (Months)',
            'field_8_3' => 'Special Instructions',

            'field_9' => 'Loan Application',

            'field_10' => 'Credit/Debit Card',
            'field_10_1' => 'Card Type',
            'field_10_2' => 'Action (New/Replace/Cancel)',
            'field_10_3' => 'Reason',

            'field_11' => 'Internet Banking Registration',

            'field_12' => 'Update Personal Information',
            'field_12_1' => 'Details to Update',

            'field_13' => 'Others',
            'field_13_1' => 'Specify Service',

            // Content/Agreement
            'content_1' => 'Terms and Conditions Agreement',
            'content_2' => 'Privacy Policy Agreement',

            // Section C: Remittance Details
            'section_c_1' => 'Beneficiary Name',
            'section_c_2' => 'Beneficiary IC/Passport',
            'section_c_3' => 'Relationship to Customer',
            'section_c_4' => 'Beneficiary Address',
            'section_c_5' => 'Beneficiary Phone',
            'section_c_6' => 'Beneficiary Email',
            'section_c_7' => 'Purpose of Remittance',
            'section_c_8' => 'Remittance Amount',
            'section_c_8_1' => 'Currency',
            'section_c_8_2' => 'Bank Name',
            'section_c_8_3' => 'Bank Account Number',
            'section_c_8_4' => 'Swift Code/Bank Code',

            // Section D: Declaration
            'section_d_1' => 'Declaration Confirmation',
            'section_d_2' => 'Applicant Signature',
            'section_d_3' => 'Date',
        ],

        // Data Access Request (DAR) Field Mappings
        'dar' => [
            // Section 2: About You
            'field_2_1' => 'Requester Type',
            'field_3_1' => 'Full Name (as per NRIC)',
            'field_3_2' => 'NRIC / Passport No.',
            'field_3_3' => 'Address',
            'field_3_4' => 'Postcode',
            'field_3_5' => 'Email Address',
            'field_3_6' => 'Telephone No. (Office/Home)',
            'field_3_7' => 'Mobile No.',

            // Request Details (Section 4)
            'field_4_1' => 'Account Number',
            'field_4_9' => 'Personal Information',
            'field_4_10' => 'Account Status/Details',
            'field_4_12' => 'Transaction History',
            'field_4_13' => 'Other Information',

            // Additional Details (Section 5)
            'field_5_1' => 'Purpose of Request',

            // Declaration (Section 6)
            'field_6_1' => 'Full Name (as per NRIC)',
            'field_6_2' => 'NRIC / Passport No.',
            'field_6_3' => 'Applicant Signature',
        ],

        // Data Correction Request (DCR) Field Mappings
        'dcr' => [
            // Section 2: About You
            'field_2_1' => 'Requester Type',

            // Personal Information (Section 3)
            'field_3_1' => 'Full Name (as per NRIC)',
            'field_3_2' => 'NRIC / Passport No.',
            'field_3_3' => 'Address',
            'field_3_4' => 'Postcode',
            'field_3_5' => 'Email Address',
            'field_3_6' => 'Telephone No. (Office/Home)',
            'field_3_7' => 'Mobile No.',

            // Correction Details (Section 4)
            'field_4_1' => 'Account Number',
            'field_4_6' => 'Data Field to Correct',
            'field_4_7' => 'Current (Incorrect) Information',
            'field_4_8' => 'Correct Information',

            // Additional Details (Section 5)
            'field_5_1' => 'Reason for Correction',
            'field_5_2' => 'Supporting Documents',
        ],
    ];

    /**
     * Section title mappings for each form type
     */
    private static $sectionMappings = [
        'srf' => [
            'customer' => 'Customer Information',
            'services' => 'Service Request Details',
            'remittance' => 'Remittance Details (if applicable)',
            'declaration' => 'Declaration & Signature',
        ],
        'dar' => [
            'personal' => 'Personal Information',
            'request' => 'Data Access Request Details',
            'declaration' => 'Declaration & Signature',
        ],
        'dcr' => [
            'personal' => 'Personal Information',
            'correction' => 'Data Correction Details',
            'declaration' => 'Declaration & Signature',
        ],
    ];

    /**
     * Get human-readable label for a field
     */
    public static function getFieldLabel(string $formSlug, string $fieldName): string
    {
        $mappings = self::$fieldMappings[$formSlug] ?? [];
        return $mappings[$fieldName] ?? ucwords(str_replace('_', ' ', $fieldName));
    }

    /**
     * Format submission data with proper labels and sections
     */
    public static function formatSubmissionData(FormSubmission $submission): array
    {
        $formSlug = $submission->form->slug ?? 'unknown';
        $grouped = [];

        // Check if we have submissionData relationship loaded with fields
        if ($submission->submissionData && $submission->submissionData->count() > 0) {
            foreach ($submission->submissionData as $data) {
                // Skip inactive fields or internal fields if necessary
                if ($data->field && $data->field->is_active === 0)
                    continue;

                $fieldName = $data->field->field_name ?? $data->field_id;
                $value = $data->field_value_json ?? $data->file_path ?? $data->field_value;

                // Use label from database if available
                $label = $data->field->field_label ?? self::getFieldLabel($formSlug, $fieldName);

                // Use section from database if available (though currently we infer section from name)
                $section = self::determineSectionForField($fieldName, $formSlug);

                if (!isset($grouped[$section])) {
                    $grouped[$section] = [];
                }

                $grouped[$section][] = [
                    'field_name' => $fieldName,
                    'label' => $label,
                    'value' => self::formatFieldValue($formSlug, $fieldName, $value),
                    'type' => self::detectFieldType($fieldName, $value),
                ];
            }

            // If we successfully grouped data from relation, return it
            if (!empty($grouped)) {
                return $grouped;
            }
        }

        // Fallback to JSON columns (Legacy behavior)
        $fieldResponses = $submission->field_responses ?? [];

        foreach ($fieldResponses as $fieldName => $value) {
            $label = self::getFieldLabel($formSlug, $fieldName);
            $section = self::determineSectionForField($fieldName, $formSlug);

            if (!isset($grouped[$section])) {
                $grouped[$section] = [];
            }

            $grouped[$section][] = [
                'field_name' => $fieldName,
                'label' => $label,
                'value' => self::formatFieldValue($formSlug, $fieldName, $value),
                'type' => self::detectFieldType($fieldName, $value),
            ];
        }

        return $grouped;
    }

    /**
     * Format field value for display
     */
    private static function formatFieldValue(string $formSlug, string $fieldName, $value)
    {
        // Format Requester Type for DAR/DCR
        if (($formSlug === 'dar' || $formSlug === 'dcr') && $fieldName === 'field_2_1') {
            if ($value == '1' || $value === 1) {
                return 'Customer';
            } elseif ($value == '2' || $value === 2) {
                return 'Third Party Requestor';
            }
        }

        return $value;
    }

    /**
     * Determine which section a field belongs to based on its name
     */
    private static function determineSectionForField(string $fieldName, string $formSlug = 'unknown'): string
    {
        // SRF-specific sections
        if ($formSlug === 'srf') {
            if (str_starts_with($fieldName, 'header_')) {
                return 'Customer Information';
            } elseif (str_starts_with($fieldName, 'field_')) {
                return 'Service Request Details';
            } elseif (str_starts_with($fieldName, 'section_c_')) {
                return 'Remittance Details';
            } elseif (str_starts_with($fieldName, 'section_d_')) {
                return 'Declaration & Signature';
            } elseif (str_starts_with($fieldName, 'content_')) {
                return 'Agreements';
            }
        }

        // DAR/DCR specific sections
        if ($formSlug === 'dar' || $formSlug === 'dcr') {
            if (str_starts_with($fieldName, 'field_2_') || str_starts_with($fieldName, 'field_3_')) {
                return 'Personal Information';
            } elseif (str_starts_with($fieldName, 'field_4_')) {
                return $formSlug === 'dar' ? 'Data Access Request Details' : 'Data Correction Details';
            } elseif (str_starts_with($fieldName, 'field_5_')) {
                return 'Additional Information';
            } elseif (str_starts_with($fieldName, 'field_6_')) {
                return 'Declaration & Signature';
            }
        }

        // Generic fallback
        return 'Other Information';
    }

    /**
     * Detect field type based on name and value
     */
    private static function detectFieldType(string $fieldName, $value): string
    {
        // Check if it's a signature field
        if (
            str_contains($fieldName, 'signature') ||
            (is_string($value) && str_contains($value, 'signatures/'))
        ) {
            return 'signature';
        }

        // Check if it's a file upload
        if (
            is_string($value) && (
                str_contains($value, 'submissions/') ||
                str_contains($value, 'uploads/')
            )
        ) {
            return 'file';
        }

        // Check if it's a date
        if (str_contains($fieldName, 'date') || str_contains($fieldName, '_at')) {
            return 'date';
        }

        // Check if it's a boolean (but exclude requester type field)
        if (($fieldName !== 'field_2_1') && (is_bool($value) || $value === '1' || $value === '0')) {
            return 'boolean';
        }

        // Check if it's an array
        if (is_array($value)) {
            return 'array';
        }

        return 'text';
    }

    /**
     * Render field value based on type
     */
    public static function renderFieldValue(string $type, $value): string
    {
        switch ($type) {
            case 'signature':
                $url = asset('storage/' . $value);
                return '<div class="signature-display"><img src="' . $url . '" alt="Signature" class="max-w-xs border border-gray-300 dark:border-gray-600 rounded"></div>';

            case 'file':
                $url = asset('storage/' . $value);
                return '<a href="' . $url . '" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline inline-flex items-center"><i class="bx bx-download mr-1"></i>Download File</a>';

            case 'boolean':
                return ($value == '1' || $value === true) ? '<span class="text-green-600 dark:text-green-400">✓ Yes</span>' : '<span class="text-gray-500 dark:text-gray-400">✗ No</span>';

            case 'array':
                return '<pre class="bg-gray-50 dark:bg-gray-900 p-2 rounded text-xs overflow-x-auto">' . json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';

            case 'date':
                return is_string($value) ? date('d M Y', strtotime($value)) : $value;

            default:
                return $value ?? '<span class="text-gray-400 italic">N/A</span>';
        }
    }

    /**
     * Check if a field should be displayed (hide if value is 0 for service checkboxes)
     */
    public static function shouldDisplayField(string $fieldName, $value): bool
    {
        // Hide service checkboxes that are not selected (value = 0 or empty)
        if (preg_match('/^field_\d+$/', $fieldName) && ($value === '0' || $value === 0 || empty($value))) {
            return false;
        }

        // Hide agreement checkboxes that are checked (value = 1) - we assume they're always checked
        if (str_starts_with($fieldName, 'content_') && $value === '1') {
            return false;
        }

        return true;
    }
}
