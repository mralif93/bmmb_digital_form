<?php

namespace App\Http\Controllers\Srf;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequestForm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ServiceRequestFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = ServiceRequestForm::with(['user', 'processedBy'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
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
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('organization_name', 'like', "%{$search}%");
            });
        }

        $forms = $query->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'forms' => $forms,
                'filters' => $request->only(['status', 'service_type', 'priority', 'date_from', 'date_to', 'search'])
            ]);
        }

        return view('admin.srf.forms.index', compact('forms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $users = User::where('status', 'active')->get();
        $serviceTypes = ServiceRequestForm::getServiceTypes();
        $priorities = ['low', 'medium', 'high', 'urgent'];

        return view('admin.srf.forms.create', compact('users', 'serviceTypes', 'priorities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_type' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'required|string',
            'customer_city' => 'required|string|max:100',
            'customer_state' => 'required|string|max:100',
            'customer_postal_code' => 'required|string|max:20',
            'customer_country' => 'required|string|max:100',
            'organization_name' => 'nullable|string|max:255',
            'organization_type' => 'nullable|string|max:100',
            'organization_address' => 'nullable|string',
            'organization_contact_person' => 'nullable|string|max:255',
            'organization_contact_email' => 'nullable|email|max:255',
            'organization_contact_phone' => 'nullable|string|max:20',
            'service_description' => 'required|string',
            'service_requirements' => 'required|string',
            'service_scope' => 'required|string',
            'expected_delivery_date' => 'nullable|date|after:today',
            'budget_range' => 'nullable|string|max:100',
            'special_instructions' => 'nullable|string',
            'supporting_documents' => 'nullable|array',
            'supporting_documents.*' => 'string|max:500',
            'consent_obtained' => 'required|boolean',
            'consent_method' => 'required_if:consent_obtained,true|string|max:100',
            'consent_date' => 'required_if:consent_obtained,true|date',
            'consent_document_path' => 'nullable|string|max:500',
            'legal_basis' => 'required|string|max:100',
            'legal_basis_description' => 'required|string',
            'compliance_requirements' => 'nullable|array',
            'compliance_requirements.*' => 'string|max:100',
            'risk_assessment' => 'nullable|string',
            'security_measures' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $form = ServiceRequestForm::create($validated);

        return redirect()->route('admin.srf.forms.show', $form)
            ->with('success', 'Service Request Form created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceRequestForm $form): View
    {
        $form->load(['user', 'processedBy', 'formFields', 'submissions', 'serviceActions', 'serviceHistory']);
        
        return view('admin.srf.forms.show', compact('form'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceRequestForm $form): View
    {
        $users = User::where('status', 'active')->get();
        $serviceTypes = ServiceRequestForm::getServiceTypes();
        $priorities = ['low', 'medium', 'high', 'urgent'];

        return view('admin.srf.forms.edit', compact('form', 'users', 'serviceTypes', 'priorities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceRequestForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:draft,submitted,under_review,approved,rejected,completed',
            'service_type' => 'required|string|max:100',
            'priority' => 'required|in:low,medium,high,urgent',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'required|string',
            'customer_city' => 'required|string|max:100',
            'customer_state' => 'required|string|max:100',
            'customer_postal_code' => 'required|string|max:20',
            'customer_country' => 'required|string|max:100',
            'organization_name' => 'nullable|string|max:255',
            'organization_type' => 'nullable|string|max:100',
            'organization_address' => 'nullable|string',
            'organization_contact_person' => 'nullable|string|max:255',
            'organization_contact_email' => 'nullable|email|max:255',
            'organization_contact_phone' => 'nullable|string|max:20',
            'service_description' => 'required|string',
            'service_requirements' => 'required|string',
            'service_scope' => 'required|string',
            'expected_delivery_date' => 'nullable|date|after:today',
            'budget_range' => 'nullable|string|max:100',
            'special_instructions' => 'nullable|string',
            'supporting_documents' => 'nullable|array',
            'supporting_documents.*' => 'string|max:500',
            'consent_obtained' => 'required|boolean',
            'consent_method' => 'required_if:consent_obtained,true|string|max:100',
            'consent_date' => 'required_if:consent_obtained,true|date',
            'consent_document_path' => 'nullable|string|max:500',
            'legal_basis' => 'required|string|max:100',
            'legal_basis_description' => 'required|string',
            'compliance_requirements' => 'nullable|array',
            'compliance_requirements.*' => 'string|max:100',
            'risk_assessment' => 'nullable|string',
            'security_measures' => 'nullable|string',
            'compliance_verified' => 'boolean',
            'legal_review_completed' => 'boolean',
            'service_manager_approval' => 'boolean',
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

        return redirect()->route('admin.srf.forms.show', $form)
            ->with('success', 'Service Request Form updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceRequestForm $form): RedirectResponse
    {
        $form->delete();

        return redirect()->route('admin.srf.forms.index')
            ->with('success', 'Service Request Form deleted successfully.');
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, ServiceRequestForm $form): JsonResponse
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
    public function assign(Request $request, ServiceRequestForm $form): JsonResponse
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
        $query = ServiceRequestForm::with(['user', 'processedBy']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        $forms = $query->get();

        $filename = 'srf_forms_' . date('Y-m-d_H-i-s') . '.csv';
        
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
                'Service Type',
                'Priority',
                'Customer Name',
                'Customer Email',
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
                    $form->service_type,
                    $form->priority,
                    $form->customer_name,
                    $form->customer_email,
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