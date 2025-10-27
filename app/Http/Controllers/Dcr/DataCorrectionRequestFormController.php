<?php

namespace App\Http\Controllers\Dcr;

use App\Http\Controllers\Controller;
use App\Models\DataCorrectionRequestForm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class DataCorrectionRequestFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = DataCorrectionRequestForm::with(['user', 'processedBy'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('correction_type')) {
            $query->where('correction_type', $request->correction_type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                  ->orWhere('data_subject_name', 'like', "%{$search}%")
                  ->orWhere('data_subject_email', 'like', "%{$search}%")
                  ->orWhere('organization_name', 'like', "%{$search}%");
            });
        }

        $forms = $query->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'forms' => $forms,
                'filters' => $request->only(['status', 'correction_type', 'priority', 'date_from', 'date_to', 'search'])
            ]);
        }

        return view('admin.dcr.forms.index', compact('forms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $users = User::where('status', 'active')->get();
        $correctionTypes = DataCorrectionRequestForm::getCorrectionTypes();
        $priorities = ['low', 'medium', 'high', 'urgent'];

        return view('admin.dcr.forms.create', compact('users', 'correctionTypes', 'priorities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'correction_type' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent',
            'data_subject_name' => 'required|string|max:255',
            'data_subject_email' => 'required|email|max:255',
            'data_subject_phone' => 'nullable|string|max:20',
            'data_subject_address' => 'required|string',
            'data_subject_id_type' => 'required|string|max:50',
            'data_subject_id_number' => 'required|string|max:50',
            'data_subject_id_expiry_date' => 'nullable|date|after:today',
            'organization_name' => 'required|string|max:255',
            'organization_type' => 'required|string|max:100',
            'organization_address' => 'required|string',
            'organization_contact_person' => 'required|string|max:255',
            'organization_contact_email' => 'required|email|max:255',
            'organization_contact_phone' => 'required|string|max:20',
            'incorrect_data_description' => 'required|string',
            'incorrect_data_location' => 'required|string',
            'incorrect_data_fields' => 'required|array',
            'incorrect_data_fields.*' => 'string|max:100',
            'correct_data_description' => 'required|string',
            'correct_data_fields' => 'required|array',
            'correct_data_fields.*' => 'string|max:100',
            'correction_justification' => 'required|string',
            'supporting_documents' => 'nullable|array',
            'supporting_documents.*' => 'string|max:500',
            'consent_obtained' => 'required|boolean',
            'consent_method' => 'required_if:consent_obtained,true|string|max:100',
            'consent_date' => 'required_if:consent_obtained,true|date',
            'consent_document_path' => 'nullable|string|max:500',
            'data_subject_authorization_document_path' => 'nullable|string|max:500',
            'legal_basis' => 'required|string|max:100',
            'legal_basis_description' => 'required|string',
            'compliance_requirements' => 'nullable|array',
            'compliance_requirements.*' => 'string|max:100',
            'risk_assessment' => 'nullable|string',
            'security_measures' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $form = DataCorrectionRequestForm::create($validated);

        return redirect()->route('admin.dcr.forms.show', $form)
            ->with('success', 'Data Correction Request Form created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataCorrectionRequestForm $form): View
    {
        $form->load(['user', 'processedBy', 'formFields', 'submissions', 'correctionActions', 'verificationRecords']);
        
        return view('admin.dcr.forms.show', compact('form'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataCorrectionRequestForm $form): View
    {
        $users = User::where('status', 'active')->get();
        $correctionTypes = DataCorrectionRequestForm::getCorrectionTypes();
        $priorities = ['low', 'medium', 'high', 'urgent'];

        return view('admin.dcr.forms.edit', compact('form', 'users', 'correctionTypes', 'priorities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataCorrectionRequestForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:draft,submitted,under_review,approved,rejected,completed',
            'correction_type' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent',
            'data_subject_name' => 'required|string|max:255',
            'data_subject_email' => 'required|email|max:255',
            'data_subject_phone' => 'nullable|string|max:20',
            'data_subject_address' => 'required|string',
            'data_subject_id_type' => 'required|string|max:50',
            'data_subject_id_number' => 'required|string|max:50',
            'data_subject_id_expiry_date' => 'nullable|date|after:today',
            'organization_name' => 'required|string|max:255',
            'organization_type' => 'required|string|max:100',
            'organization_address' => 'required|string',
            'organization_contact_person' => 'required|string|max:255',
            'organization_contact_email' => 'required|email|max:255',
            'organization_contact_phone' => 'required|string|max:20',
            'incorrect_data_description' => 'required|string',
            'incorrect_data_location' => 'required|string',
            'incorrect_data_fields' => 'required|array',
            'incorrect_data_fields.*' => 'string|max:100',
            'correct_data_description' => 'required|string',
            'correct_data_fields' => 'required|array',
            'correct_data_fields.*' => 'string|max:100',
            'correction_justification' => 'required|string',
            'supporting_documents' => 'nullable|array',
            'supporting_documents.*' => 'string|max:500',
            'consent_obtained' => 'required|boolean',
            'consent_method' => 'required_if:consent_obtained,true|string|max:100',
            'consent_date' => 'required_if:consent_obtained,true|date',
            'consent_document_path' => 'nullable|string|max:500',
            'data_subject_authorization_document_path' => 'nullable|string|max:500',
            'legal_basis' => 'required|string|max:100',
            'legal_basis_description' => 'required|string',
            'compliance_requirements' => 'nullable|array',
            'compliance_requirements.*' => 'string|max:100',
            'risk_assessment' => 'nullable|string',
            'security_measures' => 'nullable|string',
            'compliance_verified' => 'boolean',
            'legal_review_completed' => 'boolean',
            'data_protection_officer_approval' => 'boolean',
            'compliance_notes' => 'nullable|string',
            'processed_by' => 'nullable|exists:users,id',
            'rejection_reason' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        // Update timestamps based on status changes
        if ($form->status !== $validated['status']) {
            switch ($validated['status']) {
                case 'submitted':
                    $validated['submitted_at'] = now();
                    break;
                case 'under_review':
                    $validated['reviewed_at'] = now();
                    break;
                case 'approved':
                    $validated['approved_at'] = now();
                    break;
                case 'completed':
                    $validated['completed_at'] = now();
                    break;
            }
        }

        $form->update($validated);

        return redirect()->route('admin.dcr.forms.show', $form)
            ->with('success', 'Data Correction Request Form updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataCorrectionRequestForm $form): RedirectResponse
    {
        $form->delete();

        return redirect()->route('admin.dcr.forms.index')
            ->with('success', 'Data Correction Request Form deleted successfully.');
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, DataCorrectionRequestForm $form): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,submitted,under_review,approved,rejected,completed',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $form->status;
        $form->update($validated);

        // Log the status change
        $form->addAuditEntry('status_change', [
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'form' => $form->fresh()
        ]);
    }

    /**
     * Assign the form to a user.
     */
    public function assign(Request $request, DataCorrectionRequestForm $form): JsonResponse
    {
        $validated = $request->validate([
            'processed_by' => 'required|exists:users,id',
        ]);

        $form->update($validated);

        // Log the assignment
        $form->addAuditEntry('assignment', [
            'assigned_to' => $validated['processed_by'],
            'assigned_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Form assigned successfully.',
            'form' => $form->fresh(['processedBy'])
        ]);
    }

    /**
     * Export forms to CSV.
     */
    public function export(Request $request)
    {
        $query = DataCorrectionRequestForm::with(['user', 'processedBy']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('correction_type')) {
            $query->where('correction_type', $request->correction_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        $forms = $query->get();

        $filename = 'dcr_forms_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($forms) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Request Number',
                'Status',
                'Correction Type',
                'Priority',
                'Data Subject Name',
                'Data Subject Email',
                'Organization Name',
                'Submitted At',
                'Processed By',
                'Created At'
            ]);

            // CSV data
            foreach ($forms as $form) {
                fputcsv($file, [
                    $form->request_number,
                    $form->status,
                    $form->correction_type,
                    $form->priority,
                    $form->data_subject_name,
                    $form->data_subject_email,
                    $form->organization_name,
                    $form->submitted_at?->format('Y-m-d H:i:s'),
                    $form->processedBy?->name,
                    $form->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}