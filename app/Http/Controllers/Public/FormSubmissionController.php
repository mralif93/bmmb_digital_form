<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\FormField;
use App\Services\FormRendererService;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormSubmissionController extends Controller
{
    use LogsAuditTrail;

    /**
     * Store a public form submission using the new form management system.
     */
    public function store(Request $request, $type)
    {
        // Get form from new Form model using slug
        $form = Form::where('slug', $type)
            ->where('status', 'active')
            ->where('is_public', true)
            ->first();

        if (!$form) {
            return response()->json([
                'success' => false,
                'message' => 'Form not found or not available for submission.'
            ], 404);
        }

        // Check submission limit if set
        if ($form->submission_limit) {
            $submissionCount = FormSubmission::where('form_id', $form->id)
                ->where('status', '!=', 'cancelled')
                ->count();
            
            if ($submissionCount >= $form->submission_limit) {
                return response()->json([
                    'success' => false,
                    'message' => 'This form has reached its submission limit.'
                ], 403);
            }
        }

        // Check if multiple submissions are allowed
        if (!$form->allow_multiple_submissions) {
            $branchId = session('submission_branch_id');
            $existingSubmission = FormSubmission::where('form_id', $form->id)
                ->where('branch_id', $branchId)
                ->where('status', '!=', 'cancelled')
                ->first();
            
            if ($existingSubmission) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already submitted this form. Multiple submissions are not allowed.'
                ], 403);
            }
        }

        // Get dynamic validation rules from FormRendererService
        $formRenderer = app(FormRendererService::class);
        $formType = $form->settings['type'] ?? $form->slug;
        $validationRules = $formRenderer->getValidationRules($form->id, $formType);

        // Build validation rules from form fields
        $fields = FormField::where('form_id', $form->id)
            ->where('is_active', true)
            ->get();

        foreach ($fields as $field) {
            $fieldName = $field->field_name;
            $fieldRules = [];

            if ($field->is_required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Add type-specific validation
            switch ($field->field_type) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'file':
                    $fieldRules[] = 'file';
                    break;
            }

            // Add custom validation rules if defined
            if ($field->validation_rules && is_array($field->validation_rules)) {
                $fieldRules = array_merge($fieldRules, $field->validation_rules);
            }

            if (!empty($fieldRules)) {
                $validationRules[$fieldName] = implode('|', $fieldRules);
            }
        }

        // Validate form data
        if (!empty($validationRules)) {
            $validated = $request->validate($validationRules);
        } else {
            // Fallback: validate all submitted fields
            $validated = $request->except(['_token', 'terms_agreement']);
        }

        // Get form data
        $formData = $validated;
        
        // Extract field responses
        $fieldResponses = [];
        foreach ($formData as $key => $value) {
            if ($value !== null && $value !== '') {
                $fieldResponses[$key] = $value;
            }
        }

        // Get branch_id from session if available
        $branchId = session('submission_branch_id');

        // Generate unique submission token
        $submissionToken = Str::random(32) . '-' . time();

        // Prepare submission data using new FormSubmission model
        $submissionData = [
            'form_id' => $form->id,
            'user_id' => null, // Public submissions don't require user login
            'branch_id' => $branchId,
            'submission_token' => $submissionToken,
            'status' => 'submitted',
            'submission_data' => $formData,
            'field_responses' => $fieldResponses,
            'file_uploads' => [], // Handle file uploads separately if needed
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'started_at' => now(),
            'submitted_at' => now(),
        ];

        // Create submission using new FormSubmission model
        $submission = FormSubmission::create($submissionData);

        // Log audit trail (public submissions don't have authenticated users)
        // Note: user_id will be null for public submissions
        try {
            $this->logAuditTrail(
                action: 'create',
                description: "Public form submission created: {$form->name}",
                modelType: FormSubmission::class,
                modelId: $submission->id,
                newValues: [
                    'form_id' => $form->id,
                    'form_name' => $form->name,
                    'form_slug' => $form->slug,
                    'branch_id' => $branchId,
                    'submission_token' => $submissionToken,
                    'status' => 'submitted',
                    'ip_address' => $request->ip(),
                ]
            );
        } catch (\Exception $e) {
            // Log error but don't fail the submission
            \Log::warning('Failed to log audit trail for public form submission: ' . $e->getMessage());
        }

        // Clear branch session
        session()->forget('submission_branch_id');

        return response()->json([
            'success' => true,
            'message' => $form->name . ' submitted successfully!',
            'submission_token' => $submissionToken,
            'submission_id' => $submission->id,
        ]);
    }
}

