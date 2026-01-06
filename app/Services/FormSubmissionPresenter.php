<?php

namespace App\Services;

use App\Models\FormSubmission;
use Illuminate\Support\Facades\Cache;

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

            // Section 2: Account Type Services
            'field_1' => '1. Transfer Fund',
            'field_1_1' => 'To account no.',
            'field_1_2' => 'Under the name or company of',
            'field_1_3' => 'Amount',

            'field_2' => '2. Cancellation or Repurchase of Cashier\'s Order',
            'field_2_1' => 'Cheque No.',
            'field_2_2' => 'Amount',
            'field_2_3' => 'Reason',

            'field_3' => '3. Stop payment on cheque',
            'field_3_1' => 'Cheque No.',
            'field_3_2' => 'Under the name or company of',
            'field_3_3' => 'Reason',

            'field_4' => '4. Bank account statement for the month',
            'field_4_1' => 'Month',

            'field_5' => '5. Closing of Savings or Current or Investment Account-i',

            'field_6' => '6. Conversion of Qard account to Tawarruq account',

            'field_7' => '7. Conversion of Savings Account-i / Current Account-i (Tawarruq / Qard) to Mudarabah Unrestricted Investment Account (SURIA)',

            'field_8' => '8. Zakat on Savings Auto Deduction',
            'field_8_1' => 'My/our savings account : and/or',
            'field_8_2' => 'My/our current account',
            'field_8_3' => 'On behalf of me/us and hereby appoint BMMB as my/our agent under a Wakalah arrangement by transferring the zakat payable to',

            'field_9' => '9. Cancellation or Stop Zakat on savings Auto Deduction with immediate effect',

            'field_10' => '10. Zakat on Gold Account Auto Calculation and Deduction',
            'field_10_1' => 'Conversion to Malaysian Ringgit (MYR)',
            'field_10_2' => 'Transfer in gold form (in gram)',
            'field_10_3' => 'On behalf of me/us and hereby appoint BMMB as my/our agent under a Wakalah arrangement by transferring the zakat payable to',

            'field_11' => '11. Cancellation or Stop Zakat on gold account Auto Deduction with immediate effect.',

            'field_12' => '12. Physical Delivery of Purchased Commodity',
            'field_12_1' => 'I hereby place the sum of RM',

            'field_13' => '13. Others',
            'field_13_1' => 'Others',

            // Section 3: Consent
            'content_1' => 'Yes, I agree to receive marketing promotions.',
            'content_2' => 'No, I do not agree to receive marketing promotions.',

            // Section C: Third Party Requestor
            'section_c_1' => 'Beneficiary Name',
            'section_c_2' => 'NRIC / Passport No.',
            'section_c_3' => 'Relationship with Account Holder',
            'section_c_4' => 'Address',
            'section_c_5' => 'Mobile No.',
            'section_c_6' => 'Email Address',
            'section_c_7' => 'Purpose of request',
            'section_c_8' => 'Supporting Documents',
            'section_c_8_1' => 'Death certificate',
            'section_c_8_2' => 'Birth Certificate',
            'section_c_8_3' => 'Marriage certificate',
            'section_c_8_4' => 'Others',

            // Section D: Confirmation
            'section_d_1' => 'I/We declare(s) that the above information is correct',
            'section_d_2' => 'Signature',
            'section_d_3' => 'Date',
        ],

        // Data Access Request (DAR) Field Mappings
        'dar' => [
            // Section 2: About Yourself
            'field_2_1' => 'I am a customer / former customer',
            'field_2_2' => 'I am a Third Party Requestor',

            // Section 3: Particulars of Data Subject (Account Holder)
            'field_3_1' => 'Full Name (as per NRIC)',
            'field_3_2' => 'NRIC / Passport No.',
            'field_3_3' => 'Address',
            'field_3_4' => 'Postcode',
            'field_3_5' => 'Email Address',
            'field_3_6' => 'Telephone No. (Office/Home)',
            'field_3_7' => 'Mobile No.',

            // Section 3: Third Party Requestor Details
            'field_3_8' => 'Data Subject is a minor',
            'field_3_9' => 'Data Subject is incapable of managing affairs',
            'field_3_10' => 'Data Subject had passed away',
            'field_3_11' => 'Data Subject authorised me in writing',
            'field_3_12' => 'Others',
            'field_3_12_1' => 'Other Reason',
            'field_3_13' => 'Copy of NRIC/MyKid/Birth Certificate',
            'field_3_14' => 'Original of Court Order/Power of Attorney',
            'field_3_15' => 'Original of Authorisation Letter',
            'field_3_16' => 'Other Documents',
            'field_3_16_1' => 'Other Documents (Specify)',

            // Section 4: Personal Data Requested
            'field_4_1' => 'Savings Account',
            'field_4_2' => 'Current Account',
            'field_4_3' => 'FCy Current Account',
            'field_4_4' => 'Fixed Term Account',
            'field_4_5' => 'Credit Card Account',
            'field_4_6' => 'Financing Account',
            'field_4_7' => 'Ar Rahnu Account',
            'field_4_8' => 'Other Products/Services',
            'field_4_8_1' => 'Products/Services (Please specify)',
            'field_4_9' => 'Account No.',
            'field_4_10' => 'Mandatee',
            'field_4_11' => 'Image of Signature(s)',
            'field_4_11_1' => 'Specify Name',
            'field_4_12' => 'Name',
            'field_4_13' => 'IC/Passport/Other Identification',
            'field_4_14' => 'Residence/Mailing Address',
            'field_4_15' => 'Contact Details',
            'field_4_16' => 'Gender',
            'field_4_17' => 'Race',
            'field_4_18' => 'Nationality',
            'field_4_19' => 'Country of Tax Residence',
            'field_4_20' => 'Name of Employer',
            'field_4_21' => 'Customer\'s Consent',
            'field_4_22' => 'Other Personal Data',
            'field_4_22_1' => 'Other Personal Data (Specify)',
            'field_4_23' => 'Confirm Personal Data Only',
            'field_4_24' => 'Supply Me with Copy of Personal Data',

            // Section 5: Method of Delivery
            'field_5_1' => 'Mail to Address',
            'field_5_2' => 'Collect from Branch',
            'field_5_2_1' => 'Office/Branch',

            // Section 6: Declaration
            'field_6_1' => 'Full Name (as per NRIC)',
            'field_6_2' => 'NRIC / Passport No.',
            'field_6_3' => 'Signature',
        ],

        // Data Correction Request (DCR) Field Mappings
        'dcr' => [
            // Section 2: About Yourself
            'field_2_1' => 'I am a customer / former customer',
            'field_2_2' => 'I am a Third Party Requestor',

            // Section 3: Particulars of Data Subject (Account Holder)
            'field_3_1' => 'Full Name (as per NRIC)',
            'field_3_2' => 'NRIC / Passport No.',
            'field_3_3' => 'Address',
            'field_3_4' => 'Postcode',
            'field_3_5' => 'Email Address',
            'field_3_6' => 'Telephone No. (Office/Home)',
            'field_3_7' => 'Mobile No.',

            // Section 3: Third Party Requestor Details
            'field_3_8' => 'Data Subject is a minor',
            'field_3_9' => 'Data Subject is incapable of managing affairs',
            'field_3_10' => 'Data Subject had passed away',
            'field_3_11' => 'Data Subject authorised me in writing',
            'field_3_12' => 'Others',
            'field_3_12_1' => 'Other Reason',
            'field_3_13' => 'Copy of NRIC/MyKid/Birth Certificate',
            'field_3_14' => 'Original of Court Order/Power of Attorney',
            'field_3_15' => 'Original of Authorisation Letter',
            'field_3_16' => 'Other Documents',
            'field_3_16_1' => 'Other Documents (Specify)',

            // Section 4: Correction Details
            'field_4_1' => 'Update Scope',
            'field_4_2' => 'Account Type 1',
            'field_4_3' => 'Account No. 1',
            'field_4_4' => 'Account Type 2',
            'field_4_5' => 'Account No. 2',
            'field_4_6' => 'Effective Date',
            'field_4_7 ' => 'Name of Data Subject',
            'field_4_8' => 'Action for Name',
            'field_4_9' => 'Old IC No.',
            'field_4_10' => 'Action for Old IC',
            'field_4_11' => 'New IC No.',
            'field_4_12' => 'Action for New IC',
            'field_4_13' => 'Passport No.',
            'field_4_14' => 'Action for Passport',
            'field_4_15' => 'Residential/Mailing Address',
            'field_4_16' => 'Action for Address',
            'field_4_17' => 'Postcode',
            'field_4_18' => 'Action for Postcode',
            'field_4_19' => 'Account Number',
            'field_4_20' => 'Action for Account Number',
            'field_4_21' => 'Telephone No. (House)',
            'field_4_22' => 'Action for Tel (House)',
            'field_4_23' => 'Telephone No. (Office)',
            'field_4_24' => 'Action for Tel (Office)',
            'field_4_25' => 'Mobile Phone Number',
            'field_4_26' => 'Action for Mobile',
            'field_4_35' => 'Email Address',
            'field_4_36' => 'Action for Email',
            'field_4_27' => 'Nationality',
            'field_4_28' => 'Action for Nationality',
            'field_4_29' => 'Occupation',
            'field_4_30' => 'Action for Occupation',
            'field_4_31' => 'Name of Employer',
            'field_4_32' => 'Action for Employer',
            'field_4_33' => 'Others (Please specify)',
            'field_4_34' => 'Action for Others',

            // Section 5: Declaration
            'field_5_1' => 'Full Name (as per NRIC)',
            'field_5_2' => 'NRIC/Passport No.',
            'field_5_3' => 'Signature',
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
                    'type' => $data->field->field_type ?? self::detectFieldType($fieldName, $value),
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
        // Format DCR Action fields (A = Add, D = Delete, R = Revise)
        if ($formSlug === 'dcr' && str_starts_with($fieldName, 'field_4_') && str_ends_with($fieldName, '0')) {
            // field_4_8, field_4_10, field_4_12, field_4_14, field_4_16, field_4_18, field_4_20, etc.
            // These are "Action for X" fields
            if ($value === 'A' || $value === 'a') {
                return 'Add';
            } elseif ($value === 'D' || $value === 'd') {
                return 'Delete';
            } elseif ($value === 'R' || $value === 'r') {
                return 'Revise';
            }
        }

        // Format Requester Type for DAR/DCR (field_2_1 is now checkbox, not the requester type)
        if (($formSlug === 'dar' || $formSlug === 'dcr') && $fieldName === 'field_2_1') {
            if ($value == '1' || $value === 1 || $value === true || $value === '✓') {
                return 'Customer / Former Customer';
            }
        }

        if (($formSlug === 'dar' || $formSlug === 'dcr') && $fieldName === 'field_2_2') {
            if ($value == '1' || $value === 1 || $value === true || $value === '✓') {
                return 'Third Party Requestor';
            }
        }

        // Format Update Scope for DCR
        if ($formSlug === 'dcr' && $fieldName === 'field_4_1') {
            if ($value === 'all') {
                return 'Update ALL accounts';
            } elseif ($value === 'specific') {
                return 'Update ONLY specific accounts';
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
                return $formSlug === 'dcr' ? 'Declaration & Signature' : 'Additional Information';
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
            (is_string($value) && str_contains($value, 'signatures/')) ||
            (is_string($value) && str_starts_with($value, 'data:image'))
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
                if (str_starts_with($value, 'data:image')) {
                    // Render Base64 image
                    return '<div class="signature-display"><img src="' . $value . '" alt="Signature" class="max-w-xs border border-gray-300 dark:border-gray-600 rounded"></div>';
                } else {
                    // Legacy: file path
                    $url = asset('storage/' . $value);
                    return '<div class="signature-display"><img src="' . $url . '" alt="Signature" class="max-w-xs border border-gray-300 dark:border-gray-600 rounded"></div>';
                }

            case 'file':
                $url = asset('storage/' . $value);
                return '<a href="' . $url . '" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline inline-flex items-center"><i class="bx bx-download mr-1"></i>Download File</a>';

            case 'boolean':
                return ($value == '1' || $value === true) ? '<span class="text-green-600 dark:text-green-400">✓ Yes</span>' : '<span class="text-gray-500 dark:text-gray-400">✗ No</span>';

            case 'array':
                return '<pre class="bg-gray-50 dark:bg-gray-900 p-2 rounded text-xs overflow-x-auto">' . json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';

            case 'date':
                if (is_string($value)) {
                    $settings = Cache::get('system_settings', []);
                    $dateFormat = $settings['date_format'] ?? 'Y-m-d';
                    $timeFormat = $settings['time_format'] ?? 'H:i';

                    $convertedDate = \App\Helpers\TimezoneHelper::toSystemTimezone($value);

                    if ($convertedDate) {
                        // Simple check if it's a full timestamp or just date
                        // If it contains ':', it likely has time
                        if (str_contains($value, ':')) {
                            return $convertedDate->format("$dateFormat $timeFormat");
                        }
                        return $convertedDate->format($dateFormat);
                    }
                }
                return $value;

            default:
                if (is_array($value)) {
                    return '<pre class="bg-gray-50 dark:bg-gray-900 p-2 rounded text-xs overflow-x-auto">' . json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
                }
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
