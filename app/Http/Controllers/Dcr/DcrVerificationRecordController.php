<?php

namespace App\Http\Controllers\Dcr;

use App\Http\Controllers\Controller;
use App\Models\DataCorrectionRequestForm;
use App\Models\DcrFormSubmission;
use App\Models\DcrVerificationRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class DcrVerificationRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DataCorrectionRequestForm $form, DcrFormSubmission $submission): View|JsonResponse
    {
        $query = $submission->verificationRecords()
            ->with(['submission', 'verifiedBy'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('verification_type')) {
            $query->where('verification_type', $request->verification_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('verification_description', 'like', "%{$search}%")
                  ->orWhere('verification_notes', 'like', "%{$search}%")
                  ->orWhere('verification_result', 'like', "%{$search}%");
            });
        }

        $verificationRecords = $query->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'verification_records' => $verificationRecords,
                'form' => $form,
                'submission' => $submission,
                'filters' => $request->only(['verification_type', 'status', 'date_from', 'date_to', 'search'])
            ]);
        }

        return view('admin.dcr.verification-records.index', compact('verificationRecords', 'form', 'submission'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(DataCorrectionRequestForm $form, DcrFormSubmission $submission): View
    {
        $verificationTypes = DcrVerificationRecord::getVerificationTypes();
        $statuses = ['pending', 'in_progress', 'completed', 'failed', 'cancelled'];

        return view('admin.dcr.verification-records.create', compact('form', 'submission', 'verificationTypes', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, DataCorrectionRequestForm $form, DcrFormSubmission $submission): RedirectResponse
    {
        $validated = $request->validate([
            'verification_type' => 'required|string|max:100',
            'verification_description' => 'required|string',
            'verification_method' => 'required|string|max:100',
            'verification_criteria' => 'required|string',
            'verification_data' => 'nullable|array',
            'verification_result' => 'nullable|string',
            'verification_notes' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,failed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_completion_date' => 'nullable|date|after:today',
            'actual_completion_date' => 'nullable|date|after:today',
            'verification_evidence' => 'nullable|array',
            'verification_evidence.*' => 'string|max:500',
            'compliance_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $validated['dcr_form_submission_id'] = $submission->id;
        $validated['verified_by'] = auth()->id();

        $verificationRecord = DcrVerificationRecord::create($validated);

        return redirect()->route('admin.dcr.forms.submissions.verification-records.show', [$form, $submission, $verificationRecord])
            ->with('success', 'Verification record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataCorrectionRequestForm $form, DcrFormSubmission $submission, DcrVerificationRecord $verificationRecord): View
    {
        $verificationRecord->load(['submission', 'verifiedBy']);
        
        return view('admin.dcr.verification-records.show', compact('form', 'submission', 'verificationRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataCorrectionRequestForm $form, DcrFormSubmission $submission, DcrVerificationRecord $verificationRecord): View
    {
        $verificationTypes = DcrVerificationRecord::getVerificationTypes();
        $statuses = ['pending', 'in_progress', 'completed', 'failed', 'cancelled'];

        return view('admin.dcr.verification-records.edit', compact('form', 'submission', 'verificationRecord', 'verificationTypes', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataCorrectionRequestForm $form, DcrFormSubmission $submission, DcrVerificationRecord $verificationRecord): RedirectResponse
    {
        $validated = $request->validate([
            'verification_type' => 'required|string|max:100',
            'verification_description' => 'required|string',
            'verification_method' => 'required|string|max:100',
            'verification_criteria' => 'required|string',
            'verification_data' => 'nullable|array',
            'verification_result' => 'nullable|string',
            'verification_notes' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,failed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_completion_date' => 'nullable|date|after:today',
            'actual_completion_date' => 'nullable|date|after:today',
            'verification_evidence' => 'nullable|array',
            'verification_evidence.*' => 'string|max:500',
            'compliance_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        // Update completion date if status changed to completed
        if ($verificationRecord->status !== 'completed' && $validated['status'] === 'completed') {
            $validated['actual_completion_date'] = now();
        }

        $verificationRecord->update($validated);

        return redirect()->route('admin.dcr.forms.submissions.verification-records.show', [$form, $submission, $verificationRecord])
            ->with('success', 'Verification record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataCorrectionRequestForm $form, DcrFormSubmission $submission, DcrVerificationRecord $verificationRecord): RedirectResponse
    {
        $verificationRecord->delete();

        return redirect()->route('admin.dcr.forms.submissions.verification-records.index', [$form, $submission])
            ->with('success', 'Verification record deleted successfully.');
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, DataCorrectionRequestForm $form, DcrFormSubmission $submission, DcrVerificationRecord $verificationRecord): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,failed,cancelled',
            'verification_notes' => 'nullable|string',
        ]);

        $oldStatus = $verificationRecord->status;
        
        // Update completion date if status changed to completed
        if ($oldStatus !== 'completed' && $validated['status'] === 'completed') {
            $validated['actual_completion_date'] = now();
        }

        $verificationRecord->update($validated);

        // Log the status change
        $verificationRecord->addAuditEntry('status_change', [
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'verification_notes' => $validated['verification_notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'verification_record' => $verificationRecord->fresh()
        ]);
    }

    /**
     * Assign the verification record to a user.
     */
    public function assign(Request $request, DataCorrectionRequestForm $form, DcrFormSubmission $submission, DcrVerificationRecord $verificationRecord): JsonResponse
    {
        $validated = $request->validate([
            'verified_by' => 'required|exists:users,id',
        ]);

        $verificationRecord->update($validated);

        // Log the assignment
        $verificationRecord->addAuditEntry('assignment', [
            'assigned_to' => $validated['verified_by'],
            'assigned_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Verification record assigned successfully.',
            'verification_record' => $verificationRecord->fresh(['verifiedBy'])
        ]);
    }

    /**
     * Export verification records to CSV.
     */
    public function export(Request $request, DataCorrectionRequestForm $form, DcrFormSubmission $submission)
    {
        $query = $submission->verificationRecords()->with(['verifiedBy']);

        // Apply same filters as index
        if ($request->filled('verification_type')) {
            $query->where('verification_type', $request->verification_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $verificationRecords = $query->get();

        $filename = 'dcr_verification_records_' . $submission->id . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($verificationRecords) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Verification Type',
                'Verification Method',
                'Status',
                'Priority',
                'Verification Result',
                'Verified By',
                'Created At',
                'Completed At'
            ]);

            // CSV data
            foreach ($verificationRecords as $record) {
                fputcsv($file, [
                    $record->verification_type,
                    $record->verification_method,
                    $record->status,
                    $record->priority,
                    $record->verification_result,
                    $record->verifiedBy?->name,
                    $record->created_at->format('Y-m-d H:i:s'),
                    $record->actual_completion_date?->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get verification record statistics.
     */
    public function statistics(DataCorrectionRequestForm $form, DcrFormSubmission $submission): JsonResponse
    {
        $stats = [
            'total_verification_records' => $submission->verificationRecords()->count(),
            'verification_records_by_type' => $submission->verificationRecords()
                ->selectRaw('verification_type, COUNT(*) as count')
                ->groupBy('verification_type')
                ->pluck('count', 'verification_type'),
            'verification_records_by_status' => $submission->verificationRecords()
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'verification_records_by_priority' => $submission->verificationRecords()
                ->selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority'),
            'average_completion_time' => $submission->verificationRecords()
                ->whereNotNull('actual_completion_date')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, actual_completion_date)) as avg_hours')
                ->value('avg_hours'),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk update verification records.
     */
    public function bulkUpdate(Request $request, DataCorrectionRequestForm $form, DcrFormSubmission $submission): JsonResponse
    {
        $validated = $request->validate([
            'verification_record_ids' => 'required|array',
            'verification_record_ids.*' => 'exists:dcr_verification_records,id',
            'action' => 'required|in:complete,mark_failed,cancel,assign,delete',
            'verified_by' => 'required_if:action,assign|exists:users,id',
            'verification_notes' => 'nullable|string',
        ]);

        $verificationRecords = DcrVerificationRecord::whereIn('id', $validated['verification_record_ids'])
            ->where('dcr_form_submission_id', $submission->id)
            ->get();

        $updated = 0;

        foreach ($verificationRecords as $record) {
            switch ($validated['action']) {
                case 'complete':
                    $record->update([
                        'status' => 'completed',
                        'actual_completion_date' => now(),
                        'verification_notes' => $validated['verification_notes']
                    ]);
                    $updated++;
                    break;
                case 'mark_failed':
                    $record->update([
                        'status' => 'failed',
                        'verification_notes' => $validated['verification_notes']
                    ]);
                    $updated++;
                    break;
                case 'cancel':
                    $record->update([
                        'status' => 'cancelled',
                        'verification_notes' => $validated['verification_notes']
                    ]);
                    $updated++;
                    break;
                case 'assign':
                    $record->update([
                        'verified_by' => $validated['verified_by'],
                        'verification_notes' => $validated['verification_notes']
                    ]);
                    $updated++;
                    break;
                case 'delete':
                    $record->delete();
                    $updated++;
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully processed {$updated} verification records.",
            'updated_count' => $updated
        ]);
    }
}