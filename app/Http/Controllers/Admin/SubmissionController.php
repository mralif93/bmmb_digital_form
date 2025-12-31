<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\FormSection;
use App\Models\FormField;
use App\Models\User;
use App\Models\Branch;
use App\Traits\LogsAuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    use LogsAuditTrail;

    /**
     * Display submissions for a specific form (dynamic)
     */
    public function index(Request $request, $formSlug)
    {
        $form = Form::where('slug', $formSlug)->firstOrFail();
        $user = auth()->user();

        // Only show active (non-deleted) submissions
        $query = FormSubmission::where('form_id', $form->id);

        // Filter by role: BM/ABM/OO can only see submissions from their branch
        // Admin and HQ can see all submissions
        if (!$user->isAdmin() && !$user->isHQ()) {
            // BM, ABM, OO: Only submissions from their branch
            if ($user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            } else {
                // If user has no branch assigned, show no submissions
                $query->whereRaw('1 = 0');
            }
        }

        // Load relationships - use withTrashed for related models if needed
        $query->with([
            'user' => function ($q) {
                $q->withTrashed();
            },
            'branch' => function ($q) {
                $q->withTrashed();
            },
            'form' => function ($q) {
                $q->withTrashed();
            },
            'reviewedBy' => function ($q) {
                $q->withTrashed();
            }
        ]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('submission_token', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('branch', function ($branchQuery) use ($search) {
                        $branchQuery->where('branch_name', 'like', "%{$search}%")
                            ->orWhere('ti_agent_code', 'like', "%{$search}%");
                    })
                    ->orWhereJsonContains('field_responses', $search)
                    ->orWhereJsonContains('submission_data', $search);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by branch (only for Admin and HQ)
        if ($request->filled('branch_id') && ($user->isAdmin() || $user->isHQ())) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $submissions = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $branches = Branch::orderBy('branch_name')->get();

        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $timeFormat = $settings['time_format'] ?? 'H:i';

        return view('admin.submissions.index', compact('submissions', 'branches', 'form', 'dateFormat', 'timeFormat'));
    }

    /**
     * Display trashed (deleted) submissions for a specific form
     */
    public function trashed(Request $request, $formSlug)
    {
        $form = Form::where('slug', $formSlug)->firstOrFail();
        $user = auth()->user();

        // Only show trashed submissions
        $query = FormSubmission::onlyTrashed()->where('form_id', $form->id);

        // Filter by role: BM/ABM/OO can only see trashed submissions from their branch
        // Admin and HQ can see all trashed submissions
        if (!$user->isAdmin() && !$user->isHQ()) {
            // BM, ABM, OO: Only trashed submissions from their branch
            if ($user->branch_id) {
                $query->where('branch_id', $user->branch_id);
            } else {
                // If user has no branch assigned, show no submissions
                $query->whereRaw('1 = 0');
            }
        }

        // Load relationships - use withTrashed for related models if needed
        $query->with([
            'user' => function ($q) {
                $q->withTrashed();
            },
            'branch' => function ($q) {
                $q->withTrashed();
            },
            'form' => function ($q) {
                $q->withTrashed();
            },
            'reviewedBy' => function ($q) {
                $q->withTrashed();
            }
        ]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhere('submission_token', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('branch', function ($branchQuery) use ($search) {
                        $branchQuery->where('branch_name', 'like', "%{$search}%")
                            ->orWhere('ti_agent_code', 'like', "%{$search}%");
                    })
                    ->orWhereJsonContains('field_responses', $search)
                    ->orWhereJsonContains('submission_data', $search);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by branch (only for Admin and HQ)
        if ($request->filled('branch_id') && ($user->isAdmin() || $user->isHQ())) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('deleted_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('deleted_at', '<=', $request->date_to);
        }

        $submissions = $query->orderBy('deleted_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $branches = Branch::orderBy('branch_name')->get();

        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $timeFormat = $settings['time_format'] ?? 'H:i';

        return view('admin.submissions.trashed', compact('submissions', 'branches', 'form', 'dateFormat', 'timeFormat'));
    }

    /**
     * Show the form for creating a new submission (admin only)
     */
    public function create($formSlug)
    {
        $form = Form::where('slug', $formSlug)->firstOrFail();

        // Get form sections with fields
        $sections = FormSection::where('form_id', $form->id)
            ->with([
                'fields' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                }
            ])
            ->orderBy('sort_order')
            ->get();

        // Get users and branches for selection
        $users = User::where('status', 'active')->orderBy('first_name')->get();
        $branches = Branch::orderBy('branch_name')->get();

        return view('admin.submissions.create', compact('form', 'sections', 'users', 'branches'));
    }

    /**
     * Store a new submission (admin only)
     */
    public function store(Request $request, $formSlug)
    {
        $form = Form::where('slug', $formSlug)->firstOrFail();

        // Get all form fields for validation
        $fields = FormField::where('form_id', $form->id)
            ->where('is_active', true)
            ->get();

        // Build validation rules
        $rules = [
            'user_id' => 'nullable|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:draft,submitted,pending_process,under_review,approved,rejected,completed,expired,in_progress,cancelled',
        ];

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
                case 'currency':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'datetime':
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

            // Add custom validation rules if any
            if (!empty($field->validation_rules) && is_array($field->validation_rules)) {
                $fieldRules = array_merge($fieldRules, $field->validation_rules);
            }

            $rules[$fieldName] = implode('|', $fieldRules);
        }

        $validated = $request->validate($rules);

        // Prepare submission data
        $fieldResponses = [];
        $submissionDataArray = [];
        $fileUploads = [];

        foreach ($fields as $field) {
            $fieldName = $field->field_name;
            $value = $validated[$fieldName] ?? null;

            // Handle signature fields - convert base64 to image file
            if ($field->field_type === 'signature' && !empty($value)) {
                try {
                    // Decode base64 signature
                    if (preg_match('/^data:image\/(\w+);base64,/', $value, $matches)) {
                        $imageType = $matches[1]; // png, jpeg, etc.
                        $base64Data = substr($value, strpos($value, ',') + 1);
                        $imageData = base64_decode($base64Data);

                        if ($imageData !== false) {
                            // Generate unique filename
                            $fileName = 'signature_' . $fieldName . '_' . time() . '_' . \Illuminate\Support\Str::random(8) . '.png';
                            $filePath = 'submissions/' . $form->slug . '/signatures/' . $fileName;

                            // Ensure directory exists
                            $fullPath = storage_path('app/public/' . $filePath);
                            $directory = dirname($fullPath);
                            if (!file_exists($directory)) {
                                mkdir($directory, 0755, true);
                            }

                            // Save image file
                            file_put_contents($fullPath, $imageData);

                            // Add to file uploads array
                            $fileUploads[] = [
                                'field_name' => $fieldName,
                                'field_label' => $field->field_label,
                                'name' => $fileName,
                                'path' => $filePath,
                                'size' => strlen($imageData),
                                'mime' => 'image/png',
                                'type' => 'signature',
                            ];

                            // Store file path instead of base64 data
                            $value = $filePath;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Error processing signature field ' . $fieldName . ': ' . $e->getMessage());
                    // Fallback: keep base64 if file save fails
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
            }
            // Handle repeater fields - convert JSON string to array
            elseif ($field->field_type === 'repeater' && !empty($value)) {
                $repeaterData = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($repeaterData)) {
                    $value = $repeaterData;
                } else {
                    // Invalid JSON, keep as string
                    \Log::warning('Invalid JSON for repeater field ' . $fieldName);
                }
            }

            if ($value !== null) {
                $fieldResponses[$fieldName] = $value;
                $submissionDataArray[$fieldName] = $value;
            }
        }

        // Generate unique submission token
        $submissionToken = \Illuminate\Support\Str::random(32) . '-' . time();

        // Generate human-readable reference number
        $referenceNumber = FormSubmission::generateReferenceNumber();

        // Create submission
        $submission = FormSubmission::create([
            'form_id' => $form->id,
            'user_id' => $validated['user_id'] ?? auth()->id(),
            'branch_id' => $validated['branch_id'] ?? null,
            'submission_token' => $submissionToken,
            'reference_number' => $referenceNumber,
            'status' => $validated['status'] ?? 'draft',
            'field_responses' => $fieldResponses,
            'submission_data' => $submissionDataArray,
            'file_uploads' => $fileUploads,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => session()->getId(),
            'started_at' => now(),
            'submitted_at' => $validated['status'] === 'submitted' ? now() : null,
            'last_modified_at' => now(),
        ]);

        // Log audit trail
        $this->logAuditTrail(
            action: 'create',
            description: "Created new submission #{$submission->id} for form '{$form->name}'",
            modelType: get_class($submission),
            modelId: $submission->id,
            newValues: [
                'form_id' => $form->id,
                'user_id' => $submission->user_id,
                'branch_id' => $submission->branch_id,
                'status' => $submission->status,
                'field_responses' => $fieldResponses,
            ]
        );

        return redirect()
            ->route('admin.submissions.show', [$form->slug, $submission->id])
            ->with('success', 'Submission created successfully.');
    }

    /**
     * Show specific submission details (dynamic)
     */
    public function show($formSlug, $id)
    {
        $form = Form::where('slug', $formSlug)->firstOrFail();
        $user = auth()->user();

        $submission = FormSubmission::withTrashed()
            ->where('form_id', $form->id)
            ->with(['user', 'branch', 'form', 'reviewedBy', 'takenUpBy', 'completedBy', 'submissionData.field'])
            ->findOrFail($id);

        // Check access: BM/ABM/OO can only view submissions from their branch
        // Admin and HQ can view all submissions
        if (!$user->isAdmin() && !$user->isHQ()) {
            if ($user->branch_id && $submission->branch_id !== $user->branch_id) {
                abort(403, 'You can only view submissions from your branch.');
            } elseif (!$user->branch_id) {
                abort(403, 'You are not assigned to a branch.');
            }
        }

        return view('admin.submissions.show', compact('submission', 'form'));
    }

    /**
     * Get submission details for modal (AJAX)
     */
    public function details($formSlug, $id)
    {
        $form = Form::where('slug', $formSlug)->firstOrFail();
        $user = auth()->user();

        $submission = FormSubmission::withTrashed()
            ->where('form_id', $form->id)
            ->with(['user', 'branch', 'form', 'reviewedBy', 'takenUpBy', 'completedBy', 'submissionData.field'])
            ->findOrFail($id);

        // Check access: BM/ABM/OO can only view submissions from their branch
        // Admin and HQ can view all submissions
        if (!$user->isAdmin() && !$user->isHQ()) {
            if ($user->branch_id && $submission->branch_id !== $user->branch_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only view submissions from your branch.'
                ], 403);
            } elseif (!$user->branch_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not assigned to a branch.'
                ], 403);
            }
        }

        // Render the submission details partial
        $html = view('admin.submissions.modal-content', compact('submission', 'form'))->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Display DAR submissions
     */
    public function dar(Request $request)
    {
        $user = auth()->user();
        // Try to get submissions from new dynamic form system first
        $form = Form::where('slug', 'dar')->first();
        if ($form) {
            $query = FormSubmission::where('form_id', $form->id)
                ->with(['user', 'branch', 'form', 'reviewedBy']);

            // Filter by role: BM/ABM/OO can only see submissions from their branch
            if (!$user->isAdmin() && !$user->isHQ()) {
                if ($user->branch_id) {
                    $query->where('branch_id', $user->branch_id);
                } else {
                    // If user has no branch assigned, show no submissions
                    $query->whereRaw('1 = 0');
                }
            }

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhere('submission_token', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('branch', function ($branchQuery) use ($search) {
                            $branchQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereJsonContains('field_responses', $search)
                        ->orWhereJsonContains('submission_data', $search);
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by branch (only for Admin and HQ)
            if ($request->filled('branch_id') && ($user->isAdmin() || $user->isHQ())) {
                $query->where('branch_id', $request->branch_id);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $submissions = $query->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString();
        } else {
            // Form not found - redirect to forms list
            return redirect()->route('admin.forms.index')
                ->with('error', 'Form not found. Please create the form first.');
        }
        $branches = Branch::orderBy('branch_name')->get();

        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $timeFormat = $settings['time_format'] ?? 'H:i';

        return view('admin.submissions.index', compact('submissions', 'branches', 'form', 'dateFormat', 'timeFormat'));
    }

    /**
     * Display DCR submissions
     */
    public function dcr(Request $request)
    {
        $user = auth()->user();
        // Try to get submissions from new dynamic form system first
        $form = Form::where('slug', 'dcr')->first();
        if ($form) {
            $query = FormSubmission::where('form_id', $form->id)
                ->with(['user', 'branch', 'form', 'reviewedBy']);

            // Filter by role: BM/ABM/OO can only see submissions from their branch
            if (!$user->isAdmin() && !$user->isHQ()) {
                if ($user->branch_id) {
                    $query->where('branch_id', $user->branch_id);
                } else {
                    // If user has no branch assigned, show no submissions
                    $query->whereRaw('1 = 0');
                }
            }

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhere('submission_token', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('branch', function ($branchQuery) use ($search) {
                            $branchQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereJsonContains('field_responses', $search)
                        ->orWhereJsonContains('submission_data', $search);
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by branch (only for Admin and HQ)
            if ($request->filled('branch_id') && ($user->isAdmin() || $user->isHQ())) {
                $query->where('branch_id', $request->branch_id);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $submissions = $query->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString();
        } else {
            // Form not found - redirect to forms list
            return redirect()->route('admin.forms.index')
                ->with('error', 'Form not found. Please create the form first.');
        }
        $branches = Branch::orderBy('branch_name')->get();

        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $timeFormat = $settings['time_format'] ?? 'H:i';

        return view('admin.submissions.index', compact('submissions', 'branches', 'form', 'dateFormat', 'timeFormat'));
    }

    /**
     * Display RAF submissions
     */
    public function raf(Request $request)
    {
        $user = auth()->user();
        // Try to get submissions from new dynamic form system first
        $form = Form::where('slug', 'raf')->first();
        if ($form) {
            $query = FormSubmission::where('form_id', $form->id)
                ->with(['user', 'branch', 'form', 'reviewedBy']);

            // Filter by role: BM/ABM/OO can only see submissions from their branch
            if (!$user->isAdmin() && !$user->isHQ()) {
                if ($user->branch_id) {
                    $query->where('branch_id', $user->branch_id);
                } else {
                    // If user has no branch assigned, show no submissions
                    $query->whereRaw('1 = 0');
                }
            }

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhere('submission_token', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('branch', function ($branchQuery) use ($search) {
                            $branchQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereJsonContains('field_responses', $search)
                        ->orWhereJsonContains('submission_data', $search);
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by branch (only for Admin and HQ)
            if ($request->filled('branch_id') && ($user->isAdmin() || $user->isHQ())) {
                $query->where('branch_id', $request->branch_id);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $submissions = $query->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString();
        } else {
            // Form not found - redirect to forms list
            return redirect()->route('admin.forms.index')
                ->with('error', 'Form not found. Please create the form first.');
        }
        $branches = Branch::orderBy('branch_name')->get();

        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $timeFormat = $settings['time_format'] ?? 'H:i';

        return view('admin.submissions.index', compact('submissions', 'branches', 'form', 'dateFormat', 'timeFormat'));
    }

    /**
     * Display SRF submissions
     */
    public function srf(Request $request)
    {
        $user = auth()->user();
        // Try to get submissions from new dynamic form system first
        $form = Form::where('slug', 'srf')->first();
        if ($form) {
            $query = FormSubmission::where('form_id', $form->id)
                ->with(['user', 'branch', 'form', 'reviewedBy']);

            // Filter by role: BM/ABM/OO can only see submissions from their branch
            if (!$user->isAdmin() && !$user->isHQ()) {
                if ($user->branch_id) {
                    $query->where('branch_id', $user->branch_id);
                } else {
                    // If user has no branch assigned, show no submissions
                    $query->whereRaw('1 = 0');
                }
            }

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%")
                        ->orWhere('submission_token', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('branch', function ($branchQuery) use ($search) {
                            $branchQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        })
                        ->orWhereJsonContains('field_responses', $search)
                        ->orWhereJsonContains('submission_data', $search);
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by branch (only for Admin and HQ)
            if ($request->filled('branch_id') && ($user->isAdmin() || $user->isHQ())) {
                $query->where('branch_id', $request->branch_id);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $submissions = $query->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString();
        } else {
            // Form not found - redirect to forms list
            return redirect()->route('admin.forms.index')
                ->with('error', 'Form not found. Please create the form first.');
        }
        $branches = Branch::orderBy('branch_name')->get();

        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $dateFormat = $settings['date_format'] ?? 'Y-m-d';
        $timeFormat = $settings['time_format'] ?? 'H:i';

        return view('admin.submissions.index', compact('submissions', 'branches', 'form', 'dateFormat', 'timeFormat'));
    }

    /**
     * Show specific submission details
     */
    public function showDar($id)
    {
        $form = Form::where('slug', 'dar')->firstOrFail();
        $submission = FormSubmission::where('form_id', $form->id)
            ->with(['user', 'branch', 'form', 'reviewedBy', 'submissionData.field'])
            ->findOrFail($id);
        return view('admin.submissions.show', compact('submission', 'form'));
    }

    /**
     * Show specific submission details
     */
    public function showDcr($id)
    {
        $form = Form::where('slug', 'dcr')->firstOrFail();
        $submission = FormSubmission::where('form_id', $form->id)
            ->with(['user', 'branch', 'form', 'reviewedBy', 'submissionData.field'])
            ->findOrFail($id);
        return view('admin.submissions.show', compact('submission', 'form'));
    }

    /**
     * Show specific submission details
     */
    public function showRaf($id)
    {
        $form = Form::where('slug', 'raf')->firstOrFail();
        $submission = FormSubmission::where('form_id', $form->id)
            ->with(['user', 'branch', 'form', 'reviewedBy', 'submissionData.field'])
            ->findOrFail($id);
        return view('admin.submissions.show', compact('submission', 'form'));
    }

    /**
     * Show specific submission details
     */
    public function showSrf($id)
    {
        $form = Form::where('slug', 'srf')->firstOrFail();
        $submission = FormSubmission::where('form_id', $form->id)
            ->with(['user', 'branch', 'form', 'reviewedBy', 'submissionData.field'])
            ->findOrFail($id);
        return view('admin.submissions.show', compact('submission', 'form'));
    }

    /**
     * Show the form for editing a submission (admin only)
     */
    public function edit($formSlug, $id)
    {
        $form = Form::where('slug', $formSlug)->firstOrFail();

        $submission = FormSubmission::where('form_id', $form->id)
            ->with(['user', 'branch', 'form', 'reviewedBy', 'submissionData.field'])
            ->findOrFail($id);

        // Prevent editing deleted submissions
        if ($submission->trashed()) {
            return redirect()
                ->route('admin.submissions.show', [$form->slug, $submission->id])
                ->with('error', 'Cannot edit a deleted submission. Please restore it first.');
        }

        // Get form sections with fields
        $sections = FormSection::where('form_id', $form->id)
            ->with([
                'fields' => function ($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                }
            ])
            ->orderBy('sort_order')
            ->get();

        // Get current submission data from multiple sources
        // Priority: field_responses > submission_data > submissionData relationship
        $submissionData = array_merge(
            $submission->submission_data ?? [],
            $submission->field_responses ?? []
        );

        // Also extract from submissionData relationship (new storage method)
        foreach ($submission->submissionData as $data) {
            $fieldName = $data->field->field_name ?? null;
            if ($fieldName && !isset($submissionData[$fieldName])) {
                $submissionData[$fieldName] = $data->field_value;
            }
        }

        // Get users and branches for selection
        $users = User::where('status', 'active')->orderBy('first_name')->get();
        $branches = Branch::orderBy('branch_name')->get();

        return view('admin.submissions.edit', compact('submission', 'form', 'sections', 'submissionData', 'users', 'branches'));
    }

    /**
     * Update a submission (admin only)
     */
    public function update(Request $request, $formSlug, $id)
    {
        $form = Form::where('slug', $formSlug)->firstOrFail();
        $submission = FormSubmission::where('form_id', $form->id)->findOrFail($id);

        // Get all form fields for validation
        $fields = FormField::where('form_id', $form->id)
            ->where('is_active', true)
            ->get();

        // Build validation rules
        $rules = [];
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
                case 'currency':
                    $fieldRules[] = 'numeric';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'datetime':
                    $fieldRules[] = 'date';
                    break;
                case 'file':
                    // File is optional if already exists, but if provided must be a file
                    if ($field->is_required && !isset($submission->field_responses[$fieldName])) {
                        // Only require if no existing file
                    } else {
                        // Make file optional if editing
                        $fieldRules = array_filter($fieldRules, fn($r) => $r !== 'required');
                        $fieldRules[] = 'nullable';
                    }
                    $fieldRules[] = 'file';
                    break;
            }

            // Add custom validation rules if any
            if (!empty($field->validation_rules) && is_array($field->validation_rules)) {
                $fieldRules = array_merge($fieldRules, $field->validation_rules);
            }

            $rules[$fieldName] = implode('|', $fieldRules);
        }

        $validated = $request->validate($rules);

        // Store old values for audit trail
        $oldValues = [
            'field_responses' => $submission->field_responses,
            'submission_data' => $submission->submission_data,
            'user_id' => $submission->user_id,
            'branch_id' => $submission->branch_id,
            'status' => $submission->status,
        ];

        // Prepare submission data
        $fieldResponses = [];
        $submissionDataArray = [];
        $fileUploads = $submission->file_uploads ?? [];

        foreach ($fields as $field) {
            $fieldName = $field->field_name;
            $value = $validated[$fieldName] ?? null;

            // Handle signature fields - convert base64 to image file
            if ($field->field_type === 'signature' && !empty($value)) {
                try {
                    // Decode base64 signature
                    if (preg_match('/^data:image\/(\w+);base64,/', $value, $matches)) {
                        $imageType = $matches[1]; // png, jpeg, etc.
                        $base64Data = substr($value, strpos($value, ',') + 1);
                        $imageData = base64_decode($base64Data);

                        if ($imageData !== false) {
                            // Generate unique filename
                            $fileName = 'signature_' . $fieldName . '_' . time() . '_' . \Illuminate\Support\Str::random(8) . '.png';
                            $filePath = 'submissions/' . $form->slug . '/signatures/' . $fileName;

                            // Ensure directory exists
                            $fullPath = storage_path('app/public/' . $filePath);
                            $directory = dirname($fullPath);
                            if (!file_exists($directory)) {
                                mkdir($directory, 0755, true);
                            }

                            // Save image file
                            file_put_contents($fullPath, $imageData);

                            // Add to file uploads array
                            $fileUploads[] = [
                                'field_name' => $fieldName,
                                'field_label' => $field->field_label,
                                'name' => $fileName,
                                'path' => $filePath,
                                'size' => strlen($imageData),
                                'mime' => 'image/png',
                                'type' => 'signature',
                            ];

                            // Store file path instead of base64 data
                            $value = $filePath;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Error processing signature field ' . $fieldName . ': ' . $e->getMessage());
                    // Fallback: keep base64 if file save fails
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
            }
            // Handle repeater fields - convert JSON string to array
            elseif ($field->field_type === 'repeater' && !empty($value)) {
                $repeaterData = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($repeaterData)) {
                    $value = $repeaterData;
                } else {
                    // Invalid JSON, keep as string
                    \Log::warning('Invalid JSON for repeater field ' . $fieldName);
                }
            }

            if ($value !== null) {
                $fieldResponses[$fieldName] = $value;
                $submissionDataArray[$fieldName] = $value;
            }
        }

        // Update submission
        $submission->field_responses = $fieldResponses;
        $submission->submission_data = $submissionDataArray;
        $submission->file_uploads = $fileUploads;

        // Update submission metadata (user, branch, status)
        // Allow null values for optional fields
        $submission->user_id = $request->input('user_id') ?: null;
        $submission->branch_id = $request->input('branch_id') ?: null;
        $submission->status = $request->input('status', $submission->status);

        // Update staff sections (Part F & Part G) - editable by staff
        $submission->acknowledgment_received_by = $request->input('acknowledgment_received_by');
        $submission->acknowledgment_date_received = $request->input('acknowledgment_date_received');
        $submission->acknowledgment_staff_name = $request->input('acknowledgment_staff_name');
        $submission->acknowledgment_designation = $request->input('acknowledgment_designation');
        $submission->acknowledgment_stamp = $request->input('acknowledgment_stamp');

        $submission->verification_verified_by = $request->input('verification_verified_by');
        $submission->verification_date = $request->input('verification_date');
        $submission->verification_staff_name = $request->input('verification_staff_name');
        $submission->verification_designation = $request->input('verification_designation');
        $submission->verification_stamp = $request->input('verification_stamp');

        $submission->last_modified_at = now();
        $submission->save();

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: "Edited submission #{$submission->id} for form '{$form->name}'",
            modelType: get_class($submission),
            modelId: $submission->id,
            oldValues: $oldValues,
            newValues: [
                'field_responses' => $fieldResponses,
                'submission_data' => $submissionDataArray,
                'user_id' => $submission->user_id,
                'branch_id' => $submission->branch_id,
                'status' => $submission->status,
            ]
        );

        return redirect()
            ->route('admin.submissions.show', [$form->slug, $submission->id])
            ->with('success', 'Submission updated successfully.');
    }

    /**
     * Update submission status (dynamic)
     */
    public function updateStatus(Request $request, $formSlug, $id)
    {
        // Only admin can update status
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can update submission status.');
        }

        $request->validate([
            'status' => 'required|in:draft,submitted,pending_process,under_review,approved,rejected,completed,expired,in_progress,cancelled',
            'notes' => 'nullable|string',
        ]);

        $form = Form::where('slug', $formSlug)->firstOrFail();
        $submission = FormSubmission::where('form_id', $form->id)->findOrFail($id);

        $oldStatus = $submission->status;
        $submission->status = $request->status;
        if ($request->has('notes')) {
            $submission->review_notes = $request->notes;
        }

        // Update reviewed_by and reviewed_at when admin updates status
        $user = auth()->user();
        if ($user->isAdmin()) {
            $submission->reviewed_by = $user->id;
            $submission->reviewed_at = now();
        }

        $submission->last_modified_at = now();
        $submission->save();

        // Log audit trail
        $this->logAuditTrail(
            action: 'update',
            description: "Updated submission status from '{$oldStatus}' to '{$request->status}'",
            modelType: get_class($submission),
            modelId: $submission->id,
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => $request->status, 'notes' => $request->notes ?? null]
        );

        return back()->with('success', 'Submission status updated successfully.');
    }

    /**
     * Delete a submission (admin only)
     */
    public function destroy($formSlug, $id)
    {
        $form = Form::where('slug', $formSlug)->firstOrFail();
        $submission = FormSubmission::where('form_id', $form->id)->findOrFail($id);

        // Store old values for audit trail
        $oldValues = [
            'id' => $submission->id,
            'form_id' => $submission->form_id,
            'user_id' => $submission->user_id,
            'branch_id' => $submission->branch_id,
            'status' => $submission->status,
            'submission_token' => $submission->submission_token,
        ];

        // Delete uploaded files if any
        if ($submission->file_uploads && is_array($submission->file_uploads)) {
            foreach ($submission->file_uploads as $file) {
                if (isset($file['path']) && $file['path']) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }

        $submissionId = $submission->id;
        $submission->delete();

        // Log audit trail
        $this->logAuditTrail(
            action: 'delete',
            description: "Deleted submission #{$submissionId} for form '{$form->name}'",
            modelType: FormSubmission::class,
            modelId: $submissionId,
            oldValues: $oldValues
        );

        return redirect()
            ->route('admin.submissions.index', $form->slug)
            ->with('success', 'Submission deleted successfully.');
    }

    /**
     * Restore a soft-deleted submission (admin only)
     */
    public function restore($formSlug, $id)
    {
        $form = Form::where('slug', $formSlug)->firstOrFail();
        $submission = FormSubmission::withTrashed()
            ->where('form_id', $form->id)
            ->findOrFail($id);

        if (!$submission->trashed()) {
            return redirect()
                ->route('admin.submissions.show', [$form->slug, $submission->id])
                ->with('error', 'Submission is not deleted.');
        }

        $submission->restore();

        // Log audit trail
        $this->logAuditTrail(
            action: 'restore',
            description: "Restored submission #{$submission->id} for form '{$form->name}'",
            modelType: FormSubmission::class,
            modelId: $submission->id,
            newValues: ['restored_at' => now()]
        );

        return redirect()
            ->route('admin.submissions.show', [$form->slug, $submission->id])
            ->with('success', 'Submission restored successfully.');
    }

    /**
     * Permanently delete a submission (admin only)
     */
    public function forceDelete($formSlug, $id)
    {
        $form = Form::where('slug', $formSlug)->firstOrFail();
        $submission = FormSubmission::withTrashed()
            ->where('form_id', $form->id)
            ->findOrFail($id);

        // Store old values for audit trail
        $oldValues = [
            'id' => $submission->id,
            'form_id' => $submission->form_id,
            'user_id' => $submission->user_id,
            'branch_id' => $submission->branch_id,
            'status' => $submission->status,
            'submission_token' => $submission->submission_token,
        ];

        // Delete uploaded files if any
        if ($submission->file_uploads && is_array($submission->file_uploads)) {
            foreach ($submission->file_uploads as $file) {
                if (isset($file['path']) && $file['path']) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }

        $submissionId = $submission->id;
        $submission->forceDelete();

        // Log audit trail
        $this->logAuditTrail(
            action: 'force_delete',
            description: "Permanently deleted submission #{$submissionId} for form '{$form->name}'",
            modelType: FormSubmission::class,
            modelId: $submissionId,
            oldValues: $oldValues
        );

        return redirect()
            ->route('admin.submissions.index', $form->slug)
            ->with('success', 'Submission permanently deleted.');
    }

    /**
     * CFE or BM takes up a submitted application (changes status from 'submitted' to 'pending_process')
     */
    public function takeUp($formSlug, $id)
    {
        $user = auth()->user();

        // Only CFE, BM, ABM, and OO can take up submissions
        if (!$user->isCFE() && !$user->isBM() && !$user->isABM() && !$user->isOO()) {
            abort(403, 'Only CFE, Branch Managers, ABM, and Operations Officers can take up submissions.');
        }

        $form = Form::where('slug', $formSlug)->firstOrFail();
        $submission = FormSubmission::where('form_id', $form->id)->findOrFail($id);

        // Check if submission belongs to user's branch
        if ($user->branch_id && $submission->branch_id !== $user->branch_id) {
            abort(403, 'You can only take up submissions from your branch.');
        }

        // Only allow taking up submissions with status 'submitted'
        if ($submission->status !== 'submitted') {
            return back()->with('error', 'Only submitted applications can be taken up.');
        }

        $oldStatus = $submission->status;
        $submission->status = 'pending_process';
        $submission->taken_up_by = $user->id;
        $submission->taken_up_at = now();
        $submission->last_modified_at = now();

        // Auto-populate Part F: Acknowledgment Receipt
        if (!$submission->acknowledgment_received_by) {
            $submission->acknowledgment_received_by = $user->full_name;
            $submission->acknowledgment_date_received = now()->toDateString();
            $submission->acknowledgment_staff_name = $user->full_name;
            $submission->acknowledgment_designation = $user->role_display ?? $user->role;
        }

        $submission->save();

        // Log audit trail
        $roleDisplay = $user->role_display;
        $this->logAuditTrail(
            action: 'update',
            description: "{$roleDisplay} ({$user->full_name}) took up submission #{$submission->id} for form '{$form->name}'",
            modelType: get_class($submission),
            modelId: $submission->id,
            oldValues: ['status' => $oldStatus],
            newValues: ['status' => 'pending_process', 'taken_up_by' => $user->id, 'taken_up_at' => now()]
        );

        return back()->with('success', 'Submission taken up successfully. Status changed to pending process.');
    }

    /**
     * CFE or BM marks a submission as complete (changes status from 'pending_process' to 'completed')
     */
    public function complete(Request $request, $formSlug, $id)
    {
        $user = auth()->user();

        // Only CFE, BM, ABM, and OO can complete submissions
        if (!$user->isCFE() && !$user->isBM() && !$user->isABM() && !$user->isOO()) {
            abort(403, 'Only CFE, Branch Managers, ABM, and Operations Officers can complete submissions.');
        }

        $form = Form::where('slug', $formSlug)->firstOrFail();
        $submission = FormSubmission::where('form_id', $form->id)->findOrFail($id);

        // Check if submission belongs to user's branch
        if ($user->branch_id && $submission->branch_id !== $user->branch_id) {
            abort(403, 'You can only complete submissions from your branch.');
        }

        // Only allow completing submissions with status 'pending_process'
        if ($submission->status !== 'pending_process') {
            return back()->with('error', 'Only pending process submissions can be completed.');
        }

        // Validate request
        $request->validate([
            'completion_notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $submission->status;
        $submission->status = 'completed';
        $submission->completed_by = $user->id;
        $submission->completed_at = now();
        $submission->completion_notes = $request->input('completion_notes');
        $submission->last_modified_at = now();

        // Auto-populate Part G: Verification
        if (!$submission->verification_verified_by) {
            $submission->verification_verified_by = $user->full_name;
            $submission->verification_date = now()->toDateString();
            $submission->verification_staff_name = $user->full_name;
            $submission->verification_designation = $user->role_display ?? $user->role;
        }

        $submission->save();

        // Log audit trail
        $roleDisplay = $user->role_display;
        $description = "{$roleDisplay} ({$user->full_name}) completed submission #{$submission->id} for form '{$form->name}'";
        if ($request->input('completion_notes')) {
            $description .= " with notes: " . substr($request->input('completion_notes'), 0, 100);
        }

        $this->logAuditTrail(
            action: 'update',
            description: $description,
            modelType: get_class($submission),
            modelId: $submission->id,
            oldValues: ['status' => $oldStatus],
            newValues: [
                'status' => 'completed',
                'completed_by' => $user->id,
                'completed_at' => now(),
                'completion_notes' => $request->input('completion_notes')
            ]
        );

        // If AJAX request, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Submission marked as completed successfully.'
            ]);
        }

        return back()->with('success', 'Submission marked as completed successfully.');
    }

    /**
     * Show PDF preview for admin (stream in browser)
     */
    public function pdfPreview($formSlug, $id)
    {
        $form = Form::where('slug', $formSlug)->firstOrFail();
        $user = auth()->user();

        $submission = FormSubmission::withTrashed()
            ->where('form_id', $form->id)
            ->with(['user', 'branch', 'form', 'reviewedBy', 'takenUpBy', 'completedBy'])
            ->findOrFail($id);

        // Check access: BM/ABM/OO can only view submissions from their branch
        // Admin and HQ can view all submissions
        if (!$user->isAdmin() && !$user->isHQ()) {
            if ($user->branch_id && $submission->branch_id !== $user->branch_id) {
                abort(403, 'You can only view submissions from your branch.');
            } elseif (!$user->branch_id) {
                abort(403, 'You are not assigned to a branch.');
            }
        }

        // Generate PDF using DomPDF
        $pdf = \PDF::loadView('admin.pdf.submission', compact('submission'));
        $pdf->setPaper('A4', 'portrait');

        // Stream PDF in browser (for preview)
        return $pdf->stream($submission->reference_number . '.pdf');
    }
}
