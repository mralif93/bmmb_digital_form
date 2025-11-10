<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\DataAccessRequestForm;
use App\Models\DataCorrectionRequestForm;
use App\Models\RemittanceApplicationForm;
use App\Models\ServiceRequestForm;
use App\Models\RafFormSubmission;
use App\Models\DarFormSubmission;
use App\Models\DcrFormSubmission;
use App\Models\SrfFormSubmission;
use App\Models\FormSubmission;
use App\Models\Form;
use App\Services\FormRendererService;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormSubmissionController extends Controller
{
    use LogsAuditTrail;
    private $formConfig = [
        'raf' => [
            'form_model' => RemittanceApplicationForm::class,
            'submission_model' => RafFormSubmission::class,
            'form_id_field' => 'raf_form_id',
            'title' => 'Remittance Application Form',
        ],
        'dar' => [
            'form_model' => DataAccessRequestForm::class,
            'submission_model' => DarFormSubmission::class,
            'form_id_field' => 'dar_form_id',
            'title' => 'Data Access Request Form',
        ],
        'dcr' => [
            'form_model' => DataCorrectionRequestForm::class,
            'submission_model' => DcrFormSubmission::class,
            'form_id_field' => 'dcr_form_id',
            'title' => 'Data Correction Request Form',
        ],
        'srf' => [
            'form_model' => ServiceRequestForm::class,
            'submission_model' => SrfFormSubmission::class,
            'form_id_field' => 'srf_form_id',
            'title' => 'Service Request Form',
        ],
    ];

    /**
     * Store a public form submission.
     */
    public function store(Request $request, $type)
    {
        if (!isset($this->formConfig[$type])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid form type.'
            ], 404);
        }

        $config = $this->formConfig[$type];
        $formModel = $config['form_model'];
        $submissionModel = $config['submission_model'];

        // Get the first active form of this type (or create one if none exists)
        $form = $formModel::where('status', '!=', 'draft')->first();
        
        if (!$form) {
            // If no active form exists, get the first available form
            $form = $formModel::first();
        }

        if (!$form) {
            return response()->json([
                'success' => false,
                'message' => 'No form available for submission.'
            ], 404);
        }

        // Get dynamic validation rules from FormRendererService
        $formRenderer = app(FormRendererService::class);
        $validationRules = $formRenderer->getValidationRules($form->id, $type);

        // Validate form data using dynamic rules
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

        // Prepare submission data
        $submissionData = [
            $config['form_id_field'] => $form->id,
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

        // Create submission
        $submission = $submissionModel::create($submissionData);

        // Log audit trail (public submissions don't have authenticated users)
        // Note: user_id will be null for public submissions
        try {
            $this->logAuditTrail(
                action: 'create',
                description: "Public form submission created: {$config['title']}",
                modelType: get_class($submission),
                modelId: $submission->id,
                newValues: [
                    'form_type' => $type,
                    'form_id' => $form->id,
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
            'message' => $config['title'] . ' submitted successfully!',
            'submission_token' => $submissionToken,
            'submission_id' => $submission->id,
        ]);
    }
}

