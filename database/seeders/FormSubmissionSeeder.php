<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Form;
use App\Models\FormField;
use App\Models\FormSection;
use App\Models\FormSubmission;
use App\Models\FormSubmissionData;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FormSubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active forms (dynamic - works with any forms in the database)
        $forms = Form::where('status', 'active')->get();

        if ($forms->isEmpty()) {
            $this->command->warn('No active forms found. Please run FormSeeder first.');
            return;
        }

        $this->command->info("Found {$forms->count()} active form(s) to seed submissions for:");

        // Get branches and users
        $branches = Branch::all();
        $allUsers = User::all();

        if ($branches->isEmpty() || $allUsers->isEmpty()) {
            $this->command->warn('No branches or users found. Please seed branches and users first.');
            return;
        }

        // Status options - FORCE SUBMITTED
        // $statuses = ['draft', 'submitted', 'pending_process', 'under_review', 'approved', 'rejected', 'completed', 'in_progress'];

        foreach ($forms as $form) {
            $this->command->info("  Seeding submissions for: {$form->name} ({$form->slug})");

            // Load form with sections and fields
            $form->load([
                'sections.fields' => function ($query) {
                    $query->where('is_active', true)->ordered();
                }
            ]);

            // Get branch users (BM, ABM, OO) for testing takeup and complete functions
            $branchUsers = $allUsers->whereNotNull('branch_id')->whereIn('role', ['branch_manager', 'assistant_branch_manager', 'operation_officer']);

            // Create test submissions for takeup and complete functions
            if ($branchUsers->isNotEmpty()) {
                $testBranchUser = $branchUsers->random();
                $testBranch = $branches->where('id', $testBranchUser->branch_id)->first() ?? $branches->random();

                // Create 2-3 submissions with 'submitted' status for testing takeup
                $takeupCount = rand(2, 3);
                for ($i = 1; $i <= $takeupCount; $i++) {
                    $this->createTestSubmission($form, $testBranchUser, $testBranch, 'submitted', $form->sections);
                }

                // Create 2-3 submissions with 'submitted' status (formerly pending_process)
                $completeCount = rand(2, 3);
                for ($i = 1; $i <= $completeCount; $i++) {
                    $this->createTestSubmission($form, $testBranchUser, $testBranch, 'submitted', $form->sections);
                }

            }

            // Create 10-15 random submissions per form
            $submissionCount = rand(10, 15);

            for ($i = 1; $i <= $submissionCount; $i++) {
                $startedAt = now()->subDays(rand(1, 90))->subHours(rand(1, 23));
                // Mix of statuses - some will have staff sections
                $statusOptions = ['submitted', 'submitted', 'submitted', 'pending_process', 'completed', 'approved'];
                $status = $statusOptions[array_rand($statusOptions)];
                $submittedAt = in_array($status, ['draft'])
                    ? null
                    : $startedAt->copy()->addMinutes(rand(15, 120));

                // Strategy: Create submissions that match the branch filtering logic
                // BM/ABM/OO users should see submissions from their branch
                // Admin/HQ users can see all submissions

                // Get branch users (BM, ABM, OO) and non-branch users (Admin, HQ)
                $branchUsers = $allUsers->whereNotNull('branch_id'); // BM, ABM, OO
                $nonBranchUsers = $allUsers->whereNull('branch_id'); // Admin, HQ

                // 70% chance: Create submission for a branch user (from their branch)
                // 30% chance: Create submission for admin/HQ (any branch)
                if ($branchUsers->isNotEmpty() && rand(0, 9) < 7) {
                    // Select a branch user and use their branch for the submission
                    $user = $branchUsers->random();
                    $branch = $branches->where('id', $user->branch_id)->first();

                    // Fallback: if user's branch doesn't exist, use random branch
                    if (!$branch) {
                        $branch = $branches->random();
                    }
                } else {
                    // Use admin/HQ user, submission can be from any branch
                    $user = $nonBranchUsers->isNotEmpty()
                        ? $nonBranchUsers->random()
                        : $allUsers->random();
                    $branch = $branches->random();
                }

                // Reviewer can be any user (admin/HQ typically review)
                $reviewedBy = in_array($status, ['under_review', 'approved', 'rejected', 'completed'])
                    ? $allUsers->random()
                    : null;

                // Generate submission data based on form fields
                $submissionData = [];
                $fieldResponses = [];
                $fileUploads = [];

                foreach ($form->sections as $section) {
                    foreach ($section->fields as $field) {
                        if (!$field->is_active) {
                            continue;
                        }

                        $fieldValue = $this->generateFieldValue($field);
                        $fieldName = $field->field_name;

                        $submissionData[$fieldName] = $fieldValue;
                        $fieldResponses[$fieldName] = $fieldValue;

                        // Handle file uploads
                        if (in_array($field->field_type, ['file', 'image'])) {
                            $fileUploads[] = [
                                'field_name' => $fieldName,
                                'field_label' => $field->field_label,
                                'name' => 'document_' . uniqid() . '.pdf',
                                'path' => 'uploads/' . $form->slug . '/' . uniqid() . '.pdf',
                                'size' => rand(100000, 5000000),
                                'mime_type' => 'application/pdf',
                            ];
                        }
                    }
                }

                // Create submission
                // Generate staff section data (Part F & Part G) for some submissions
                $hasStaffSections = in_array($status, ['pending_process', 'completed', 'approved']) && rand(0, 1);
                $staffUsers = $allUsers->whereIn('role', ['bm', 'abm', 'oo', 'branch_manager']);
                $staffUser = $hasStaffSections && $staffUsers->isNotEmpty() ? $staffUsers->random() : null;

                $submission = FormSubmission::create([
                    'form_id' => $form->id,
                    'user_id' => $user->id,
                    'branch_id' => $branch->id,
                    'submission_token' => strtolower($form->slug) . '_' . uniqid() . '_' . time(),
                    'reference_number' => FormSubmission::generateReferenceNumber(),
                    'status' => $status,
                    'submission_data' => $submissionData,
                    'field_responses' => $fieldResponses,
                    'file_uploads' => !empty($fileUploads) ? $fileUploads : null,
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                    'session_id' => Str::random(40),
                    'started_at' => $startedAt,
                    'submitted_at' => $submittedAt,
                    'last_modified_at' => $submittedAt ?? $startedAt,
                    'reviewed_by' => $reviewedBy?->id,
                    'reviewed_at' => $reviewedBy ? ($submittedAt ? $submittedAt->copy()->addHours(rand(1, 48)) : null) : null,
                    'review_notes' => in_array($status, ['approved', 'rejected']) ? fake()->sentence() : null,
                    'rejection_reason' => $status === 'rejected' ? fake()->sentence() : null,
                    // Part F: Acknowledgment Receipt (populated for pending_process, completed, approved)
                    'acknowledgment_received_by' => $hasStaffSections ? $staffUser->full_name : null,
                    'acknowledgment_date_received' => $hasStaffSections ? $submittedAt->copy()->addHours(rand(1, 12)) : null,
                    'acknowledgment_staff_name' => $hasStaffSections ? $staffUser->full_name : null,
                    'acknowledgment_designation' => $hasStaffSections ? $staffUser->role_display : null,
                    'acknowledgment_stamp' => $hasStaffSections ? 'BMMB ' . $branch->branch_code . ' Official Stamp' : null,
                    // Part G: Verification (only for completed & approved)
                    'verification_verified_by' => in_array($status, ['completed', 'approved']) && $hasStaffSections ? $staffUser->full_name : null,
                    'verification_date' => in_array($status, ['completed', 'approved']) && $hasStaffSections ? $submittedAt->copy()->addHours(rand(24, 72)) : null,
                    'verification_staff_name' => in_array($status, ['completed', 'approved']) && $hasStaffSections ? $staffUser->full_name : null,
                    'verification_designation' => in_array($status, ['completed', 'approved']) && $hasStaffSections ? $staffUser->role_display : null,
                    'verification_stamp' => in_array($status, ['completed', 'approved']) && $hasStaffSections ? 'BMMB ' . $branch->branch_code . ' Verification Stamp' : null,
                    'audit_trail' => [
                        [
                            'action' => 'created',
                            'timestamp' => $startedAt->toIso8601String(),
                            'user_id' => $user->id,
                        ],
                        $submittedAt ? [
                            'action' => 'submitted',
                            'timestamp' => $submittedAt->toIso8601String(),
                            'user_id' => $user->id,
                        ] : null,
                    ],
                    'compliance_checks' => in_array($status, ['approved', 'rejected']) ? [
                        'aml_check' => $status === 'approved' ? 'passed' : 'failed',
                        'kyc_check' => $status === 'approved' ? 'passed' : 'pending',
                        'sanctions_check' => $status === 'approved' ? 'passed' : 'pending',
                    ] : null,
                    'internal_notes' => rand(0, 1) ? fake()->paragraph() : null,
                ]);

                // Create submission data entries for each field
                foreach ($form->sections as $section) {
                    foreach ($section->fields as $field) {
                        if (!$field->is_active) {
                            continue;
                        }

                        $fieldValue = $submissionData[$field->field_name] ?? null;

                        if ($fieldValue !== null) {
                            $isJsonField = in_array($field->field_type, ['checkbox', 'multiselect']);

                            // Find file path if this is a file field
                            $filePath = null;
                            if (in_array($field->field_type, ['file', 'image']) && !empty($fileUploads)) {
                                $fileIndex = array_search($field->field_name, array_column($fileUploads, 'field_name'));
                                if ($fileIndex !== false && isset($fileUploads[$fileIndex]['path'])) {
                                    $filePath = $fileUploads[$fileIndex]['path'];
                                }
                            }

                            FormSubmissionData::create([
                                'submission_id' => $submission->id,
                                'field_id' => $field->id,
                                'field_value' => $isJsonField ? null : (string) $fieldValue,
                                'field_value_json' => $isJsonField ? (is_array($fieldValue) ? $fieldValue : [$fieldValue]) : null,
                                'file_path' => $filePath,
                            ]);
                        }
                    }
                }
            }

        }

        // Show summary
        $totalSubmissions = FormSubmission::count();
        $this->command->info("\n✓ Seeding completed!");
        $this->command->info("  Total submissions in database: {$totalSubmissions}");
    }

    /**
     * Generate a realistic value based on field type
     */
    private function generateFieldValue($field): mixed
    {
        $name = strtolower($field->field_name);
        $label = strtolower($field->field_label);

        switch ($field->field_type) {
            case 'text':
            case 'textarea':
                if (str_contains($name, 'email') || str_contains($label, 'email')) {
                    return fake()->email();
                }
                if (str_contains($name, 'phone') || str_contains($label, 'phone') || str_contains($name, 'tel') || str_contains($label, 'tel')) {
                    return fake()->phoneNumber();
                }
                if (str_contains($name, 'name') || str_contains($label, 'name')) {
                    return fake()->name();
                }
                if (str_contains($name, 'address') || str_contains($label, 'address')) {
                    return fake()->address();
                }
                if (str_contains($name, 'ic') || str_contains($label, 'ic') || str_contains($name, 'nric') || str_contains($label, 'nric')) {
                    return fake()->numerify('######-##-####');
                }
                if (str_contains($name, 'cif') || str_contains($label, 'cif')) {
                    return fake()->numerify('########');
                }
                if (str_contains($name, 'company') || str_contains($label, 'company') || str_contains($name, 'entity') || str_contains($label, 'entity')) {
                    return fake()->company();
                }

                // DCR Account fields
                if ($name === 'field_4_2' || $name === 'field_4_4') { // Account Type
                    return 'Savings Account-i';
                }
                if ($name === 'field_4_3' || $name === 'field_4_5') { // Account No
                    return fake()->numerify('##########');
                }

                if ($field->field_type === 'textarea') {
                    return fake()->paragraph();
                }
                return fake()->words(3, true);

            case 'email':
                return fake()->email();

            case 'phone':
            case 'tel':
                return fake()->phoneNumber();

            case 'number':
                return (string) rand(1000, 99999);

            case 'date':
                if ($name === 'field_4_6') { // Effective Date
                    return date('d/m/Y');
                }
                return fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d');

            case 'time':
                return fake()->time('H:i');

            case 'radio':
                if ($name === 'field_4_1') { // Update Scope
                    return rand(0, 1) ? 'all' : 'specific';
                }
            // Fallthrough to standard select/radio handling
            case 'select':

                if ($field->field_options && is_array($field->field_options)) {
                    $options = array_keys($field->field_options);
                    if (!empty($options)) {
                        return $options[array_rand($options)];
                    }
                }
                return 'Option 1'; // Fallback if no options defined

            case 'checkbox':
            case 'multiselect':
                // Special handling for DCR/DAR form checkboxes which might not have options configured
                if (
                    in_array($name, [
                        // DCR/DAR Part A checkboxes
                        'field_2_1', // Customer
                        'field_2_2', // Third Party
                        // DCR/DAR Part C checkboxes
                        'field_3_8', // Minor
                        'field_3_9', // Incapable
                        'field_3_10', // Deceased
                        'field_3_11', // Authorised
                        'field_3_12', // Others
                        'field_3_13', // NRIC copy
                        'field_3_14', // Court Order
                        'field_3_15', // Auth Letter
                        'field_3_16', // Others (Proof)
                        // DAR Part D - Account Type checkboxes
                        'field_4_1', // Savings Account
                        'field_4_2', // Current Account
                        'field_4_3', // FCy Current Account
                        'field_4_4', // Fixed Term Account
                        'field_4_5', // Credit Card Account
                        'field_4_6', // Financing Account
                        'field_4_7', // Ar Rahnu Account
                        'field_4_8', // Others
                        // DAR Part D - Personal Data Type checkboxes
                        'field_4_10', // Mandatee
                        'field_4_11', // Image of Signature
                        'field_4_12', // Name
                        'field_4_13', // IC/Passport
                        'field_4_14', // Residence Address
                        'field_4_15', // Contact Details
                        'field_4_16', // Gender
                        'field_4_17', // Race
                        'field_4_18', // Nationality
                        'field_4_19', // Country of Tax Residence
                        'field_4_20', // Name of Employer
                        'field_4_21', // Customer's Consent
                        'field_4_22', // Others
                        'field_4_23', // Confirm personal data
                        'field_4_24', // Supply me with copy
                        // DAR Part E - Method of Delivery checkboxes
                        'field_5_1', // Mail to address
                        'field_5_2', // Collect personally
                    ])
                ) {
                    return ['Yes'];
                }

                if ($field->field_options && is_array($field->field_options)) {

                    $options = array_keys($field->field_options);
                    if (!empty($options)) {
                        $count = rand(1, count($options));
                        shuffle($options);
                        return array_slice($options, 0, $count);
                    }
                }
                return ['Option 1']; // Fallback

            case 'boolean':
            case 'yes_no':
                return rand(0, 1) ? 'yes' : 'no';

            case 'file':
            case 'image':
                return 'document_' . uniqid() . '.pdf';

            case 'signature':
                return 'signature_' . uniqid() . '.png';

            case 'divider':
            case 'static_text':
                return null;

            default:
                return fake()->words(3, true);
        }
    }

    /**
     * Create a test submission for testing takeup and complete functions
     */
    private function createTestSubmission($form, $user, $branch, $status, $sections): void
    {
        $startedAt = now()->subDays(rand(1, 30))->subHours(rand(1, 23));
        $submittedAt = $startedAt->copy()->addMinutes(rand(15, 120));

        // Generate submission data based on form fields
        $submissionData = [];
        $fieldResponses = [];
        $fileUploads = [];

        foreach ($sections as $section) {
            foreach ($section->fields as $field) {
                if (!$field->is_active) {
                    continue;
                }

                $fieldValue = $this->generateFieldValue($field);
                $fieldName = $field->field_name;

                $submissionData[$fieldName] = $fieldValue;
                $fieldResponses[$fieldName] = $fieldValue;

                // Handle file uploads
                if (in_array($field->field_type, ['file', 'image'])) {
                    $fileUploads[] = [
                        'field_name' => $fieldName,
                        'field_label' => $field->field_label,
                        'name' => 'document_' . uniqid() . '.pdf',
                        'path' => 'uploads/' . $form->slug . '/' . uniqid() . '.pdf',
                        'size' => rand(100000, 5000000),
                        'mime_type' => 'application/pdf',
                    ];
                }
            }
        }

        // Set taken_up_by and taken_up_at if status is pending_process
        $takenUpBy = null;
        $takenUpAt = null;
        if ($status === 'pending_process') {
            $takenUpBy = $user->id;
            $takenUpAt = $submittedAt->copy()->addHours(rand(1, 24));
        }

        // Generate staff section data for test submissions
        $hasStaffSections = $status === 'pending_process';

        // Create submission
        $submission = FormSubmission::create([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'submission_token' => strtolower($form->slug) . '_test_' . uniqid() . '_' . time(),
            'reference_number' => FormSubmission::generateReferenceNumber(),
            'status' => $status,
            'submission_data' => $submissionData,
            'field_responses' => $fieldResponses,
            'file_uploads' => !empty($fileUploads) ? $fileUploads : null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'session_id' => \Illuminate\Support\Str::random(40),
            'started_at' => $startedAt,
            'submitted_at' => $submittedAt,
            'last_modified_at' => $takenUpAt ?? $submittedAt,
            'taken_up_by' => $takenUpBy,
            'taken_up_at' => $takenUpAt,
            // Part F: Acknowledgment Receipt (populated for pending_process status)
            'acknowledgment_received_by' => $hasStaffSections ? $user->full_name : null,
            'acknowledgment_date_received' => $hasStaffSections ? $takenUpAt : null,
            'acknowledgment_staff_name' => $hasStaffSections ? $user->full_name : null,
            'acknowledgment_designation' => $hasStaffSections ? $user->role_display : null,
            'acknowledgment_stamp' => $hasStaffSections ? 'BMMB ' . $branch->branch_code . ' Official Stamp' : null,
            // Part G: Verification (not populated for test submissions - will be tested manually)
            'verification_verified_by' => null,
            'verification_date' => null,
            'verification_staff_name' => null,
            'verification_designation' => null,
            'verification_stamp' => null,
            'audit_trail' => [
                [
                    'action' => 'created',
                    'timestamp' => $startedAt->toIso8601String(),
                    'user_id' => $user->id,
                ],
                [
                    'action' => 'submitted',
                    'timestamp' => $submittedAt->toIso8601String(),
                    'user_id' => $user->id,
                ],
                $takenUpAt ? [
                    'action' => 'taken_up',
                    'timestamp' => $takenUpAt->toIso8601String(),
                    'user_id' => $takenUpBy,
                ] : null,
            ],
            'internal_notes' => $status === 'pending_process' ? 'Test submission for completion testing' : 'Test submission for takeup testing',
        ]);

        // Create submission data entries for each field
        foreach ($sections as $section) {
            foreach ($section->fields as $field) {
                if (!$field->is_active) {
                    continue;
                }

                $fieldValue = $submissionData[$field->field_name] ?? null;

                if ($fieldValue !== null) {
                    $isJsonField = in_array($field->field_type, ['checkbox', 'multiselect']);

                    // Find file path if this is a file field
                    $filePath = null;
                    if (in_array($field->field_type, ['file', 'image']) && !empty($fileUploads)) {
                        $fileIndex = array_search($field->field_name, array_column($fileUploads, 'field_name'));
                        if ($fileIndex !== false && isset($fileUploads[$fileIndex]['path'])) {
                            $filePath = $fileUploads[$fileIndex]['path'];
                        }
                    }

                    FormSubmissionData::create([
                        'submission_id' => $submission->id,
                        'field_id' => $field->id,
                        'field_value' => $isJsonField ? null : (string) $fieldValue,
                        'field_value_json' => $isJsonField ? (is_array($fieldValue) ? $fieldValue : [$fieldValue]) : null,
                        'file_path' => $filePath,
                    ]);
                }
            }
        }
    }
}

