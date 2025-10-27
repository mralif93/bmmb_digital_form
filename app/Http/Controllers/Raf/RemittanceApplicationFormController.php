<?php

namespace App\Http\Controllers\Raf;

use App\Http\Controllers\Controller;
use App\Models\RemittanceApplicationForm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class RemittanceApplicationFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = RemittanceApplicationForm::with(['user', 'processedBy'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('currency')) {
            $query->where('remittance_currency', $request->currency);
        }

        if ($request->filled('amount_min')) {
            $query->where('remittance_amount', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('remittance_amount', '<=', $request->amount_max);
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
                $q->where('application_number', 'like', "%{$search}%")
                  ->orWhere('applicant_name', 'like', "%{$search}%")
                  ->orWhere('beneficiary_name', 'like', "%{$search}%")
                  ->orWhere('applicant_email', 'like', "%{$search}%");
            });
        }

        $forms = $query->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'forms' => $forms,
                'filters' => $request->only(['status', 'currency', 'amount_min', 'amount_max', 'date_from', 'date_to', 'search'])
            ]);
        }

        return view('admin.raf.forms.index', compact('forms'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $users = User::where('status', 'active')->get();
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CAD', 'AUD', 'CHF', 'CNY'];
        $purposes = [
            'family_support' => 'Family Support',
            'education' => 'Education',
            'medical' => 'Medical',
            'business' => 'Business',
            'investment' => 'Investment',
            'other' => 'Other'
        ];

        return view('admin.raf.forms.create', compact('users', 'currencies', 'purposes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'applicant_name' => 'required|string|max:255',
            'applicant_phone' => 'required|string|max:20',
            'applicant_email' => 'required|email|max:255',
            'applicant_address' => 'required|string',
            'applicant_city' => 'required|string|max:100',
            'applicant_state' => 'required|string|max:100',
            'applicant_postal_code' => 'required|string|max:20',
            'applicant_country' => 'required|string|max:100',
            'applicant_id_type' => 'required|string|max:50',
            'applicant_id_number' => 'required|string|max:50',
            'applicant_id_expiry_date' => 'nullable|date|after:today',
            'remittance_amount' => 'required|numeric|min:0.01',
            'remittance_currency' => 'required|string|max:3',
            'remittance_purpose' => 'required|string|max:100',
            'remittance_purpose_description' => 'nullable|string',
            'remittance_frequency' => 'required|string|max:50',
            'beneficiary_name' => 'required|string|max:255',
            'beneficiary_relationship' => 'required|string|max:100',
            'beneficiary_address' => 'required|string',
            'beneficiary_city' => 'required|string|max:100',
            'beneficiary_state' => 'required|string|max:100',
            'beneficiary_postal_code' => 'required|string|max:20',
            'beneficiary_country' => 'required|string|max:100',
            'beneficiary_phone' => 'nullable|string|max:20',
            'beneficiary_email' => 'nullable|email|max:255',
            'beneficiary_bank_name' => 'nullable|string|max:255',
            'beneficiary_bank_account' => 'nullable|string|max:50',
            'beneficiary_bank_routing' => 'nullable|string|max:20',
            'beneficiary_bank_swift' => 'nullable|string|max:20',
            'payment_method' => 'required|string|max:50',
            'payment_source' => 'required|string|max:100',
            'payment_currency' => 'required|string|max:3',
            'exchange_rate' => 'nullable|numeric|min:0',
            'service_fee' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0.01',
            'risk_level' => 'required|in:low,medium,high',
            'internal_notes' => 'nullable|string',
        ]);

        // Calculate total amount if not provided
        if (empty($validated['total_amount'])) {
            $validated['total_amount'] = $validated['remittance_amount'] + ($validated['service_fee'] ?? 0);
        }

        $form = RemittanceApplicationForm::create($validated);

        return redirect()->route('admin.raf.forms.show', $form)
            ->with('success', 'Remittance Application Form created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RemittanceApplicationForm $form): View
    {
        $form->load(['user', 'processedBy', 'formFields', 'submissions']);
        
        return view('admin.raf.forms.show', compact('form'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RemittanceApplicationForm $form): View
    {
        $users = User::where('status', 'active')->get();
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'CAD', 'AUD', 'CHF', 'CNY'];
        $purposes = [
            'family_support' => 'Family Support',
            'education' => 'Education',
            'medical' => 'Medical',
            'business' => 'Business',
            'investment' => 'Investment',
            'other' => 'Other'
        ];

        return view('admin.raf.forms.edit', compact('form', 'users', 'currencies', 'purposes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RemittanceApplicationForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:draft,submitted,under_review,approved,rejected,completed',
            'applicant_name' => 'required|string|max:255',
            'applicant_phone' => 'required|string|max:20',
            'applicant_email' => 'required|email|max:255',
            'applicant_address' => 'required|string',
            'applicant_city' => 'required|string|max:100',
            'applicant_state' => 'required|string|max:100',
            'applicant_postal_code' => 'required|string|max:20',
            'applicant_country' => 'required|string|max:100',
            'applicant_id_type' => 'required|string|max:50',
            'applicant_id_number' => 'required|string|max:50',
            'applicant_id_expiry_date' => 'nullable|date|after:today',
            'remittance_amount' => 'required|numeric|min:0.01',
            'remittance_currency' => 'required|string|max:3',
            'remittance_purpose' => 'required|string|max:100',
            'remittance_purpose_description' => 'nullable|string',
            'remittance_frequency' => 'required|string|max:50',
            'beneficiary_name' => 'required|string|max:255',
            'beneficiary_relationship' => 'required|string|max:100',
            'beneficiary_address' => 'required|string',
            'beneficiary_city' => 'required|string|max:100',
            'beneficiary_state' => 'required|string|max:100',
            'beneficiary_postal_code' => 'required|string|max:20',
            'beneficiary_country' => 'required|string|max:100',
            'beneficiary_phone' => 'nullable|string|max:20',
            'beneficiary_email' => 'nullable|email|max:255',
            'beneficiary_bank_name' => 'nullable|string|max:255',
            'beneficiary_bank_account' => 'nullable|string|max:50',
            'beneficiary_bank_routing' => 'nullable|string|max:20',
            'beneficiary_bank_swift' => 'nullable|string|max:20',
            'payment_method' => 'required|string|max:50',
            'payment_source' => 'required|string|max:100',
            'payment_currency' => 'required|string|max:3',
            'exchange_rate' => 'nullable|numeric|min:0',
            'service_fee' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0.01',
            'risk_level' => 'required|in:low,medium,high',
            'aml_verified' => 'boolean',
            'kyc_verified' => 'boolean',
            'sanctions_checked' => 'boolean',
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

        return redirect()->route('admin.raf.forms.show', $form)
            ->with('success', 'Remittance Application Form updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RemittanceApplicationForm $form): RedirectResponse
    {
        $form->delete();

        return redirect()->route('admin.raf.forms.index')
            ->with('success', 'Remittance Application Form deleted successfully.');
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, RemittanceApplicationForm $form): JsonResponse
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
    public function assign(Request $request, RemittanceApplicationForm $form): JsonResponse
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
        $query = RemittanceApplicationForm::with(['user', 'processedBy']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('currency')) {
            $query->where('remittance_currency', $request->currency);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        $forms = $query->get();

        $filename = 'raf_forms_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($forms) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Application Number',
                'Status',
                'Applicant Name',
                'Applicant Email',
                'Remittance Amount',
                'Currency',
                'Beneficiary Name',
                'Beneficiary Country',
                'Submitted At',
                'Processed By',
                'Created At'
            ]);

            // CSV data
            foreach ($forms as $form) {
                fputcsv($file, [
                    $form->application_number,
                    $form->status,
                    $form->applicant_name,
                    $form->applicant_email,
                    $form->remittance_amount,
                    $form->remittance_currency,
                    $form->beneficiary_name,
                    $form->beneficiary_country,
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