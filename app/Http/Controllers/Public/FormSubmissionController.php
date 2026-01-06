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
            $branchId = session('submission_branch_id') ?? $request->input('branch_id');
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
                case 'repeater':
                    // Repeater fields are stored as JSON, validate as JSON string
                    $fieldRules[] = 'json';
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

        // Process signatures and file uploads
        $fileUploads = [];
        $fieldResponses = [];

        foreach ($fields as $field) {
            $fieldName = $field->field_name;
            $value = $formData[$fieldName] ?? null;

            // Handle signature fields - store base64 directly in database
            if ($field->field_type === 'signature' && !empty($value)) {
                // Ensure it's a valid base64 image string
                if (preg_match('/^data:image\/(\w+);base64,/', $value)) {
                    $fieldResponses[$fieldName] = $value;
                    $formData[$fieldName] = $value;
                }
            }
            // Handle regular file uploads
            elseif ($field->field_type === 'file' && $request->hasFile($fieldName)) {
                $file = $request->file($fieldName);
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('submissions/' . $form->slug, $fileName, 'public');

                $fileUploads[] = [
                    'field_name' => $fieldName,
                    'field_label' => $field->field_label,
                    'name' => $file->getClientOriginalName(),
                    'path' => $filePath,
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'type' => 'file',
                ];

                $value = $filePath;
                $fieldResponses[$fieldName] = $value;
                $formData[$fieldName] = $value;
            }
            // Handle repeater fields - convert JSON string to array
            elseif ($field->field_type === 'repeater' && !empty($value)) {
                $repeaterData = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($repeaterData)) {
                    $fieldResponses[$fieldName] = $repeaterData;
                    $formData[$fieldName] = $repeaterData;
                } else {
                    // Invalid JSON, store as string
                    $fieldResponses[$fieldName] = $value;
                    $formData[$fieldName] = $value;
                }
            }
            // Handle other field types
            elseif ($value !== null && $value !== '') {
                $fieldResponses[$fieldName] = $value;
            }
        }

        // Get branch_id from session if available, otherwise try request input (from hidden field)
        $branchId = session('submission_branch_id') ?? $request->input('branch_id');

        // Generate unique submission token (keep for internal use)
        $submissionToken = Str::random(32) . '-' . time();

        // Generate human-readable reference number
        $referenceNumber = FormSubmission::generateReferenceNumber();

        // Prepare submission data using new FormSubmission model
        $submissionData = [
            'form_id' => $form->id,
            'user_id' => null, // Public submissions don't require user login
            'branch_id' => $branchId,
            'submission_token' => $submissionToken,
            'reference_number' => $referenceNumber,
            'status' => 'submitted',
            'submission_data' => $formData,
            'field_responses' => $fieldResponses,
            'file_uploads' => $fileUploads,
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

    /**
     * Show the submission success page
     */
    public function success($submissionToken)
    {
        // Find the submission by token
        $submission = FormSubmission::where('submission_token', $submissionToken)->first();

        if (!$submission) {
            abort(404, 'Submission not found');
        }

        // Get the form details
        $form = $submission->form;

        return view('public.forms.success', [
            'submissionToken' => $submissionToken,
            'referenceNumber' => $submission->reference_number,
            'submissionId' => $submission->id,
            'formName' => $form->name,
            'formSlug' => $form->slug,
            'submission' => $submission,
        ]);
    }

    /**
     * Show PDF preview for public (stream in browser)
     */
    public function pdfPreview($submissionToken)
    {
        $submission = FormSubmission::where('submission_token', $submissionToken)
            ->with(['form', 'user', 'branch'])
            ->first();

        if (!$submission) {
            abort(404, 'Submission not found');
        }

        // Get system settings for date/time format
        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'd/m/Y';
        $timeFormat = $settings['time_format'] ?? 'H:i';

        // Generate PDF using DomPDF
        $pdf = \PDF::loadView('admin.pdf.submission', compact('submission', 'dateFormat', 'timeFormat'));
        $pdf->setPaper('A4', 'portrait');

        // Stream PDF in browser
        return $pdf->stream($submission->reference_number . '.pdf');
    }

    /**
     * Download PDF for public
     */
    public function pdfDownload($submissionToken)
    {
        $submission = FormSubmission::where('submission_token', $submissionToken)
            ->with(['form', 'user', 'branch'])
            ->first();

        if (!$submission) {
            abort(404, 'Submission not found');
        }

        // Get system settings for date/time format
        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'd/m/Y';
        $timeFormat = $settings['time_format'] ?? 'H:i';

        $pdf = \PDF::loadView('admin.pdf.submission', compact('submission', 'dateFormat', 'timeFormat'));
        $pdf->setPaper('A4', 'portrait');

        // Download PDF file
        return $pdf->download($submission->reference_number . '.pdf');
    }
}

