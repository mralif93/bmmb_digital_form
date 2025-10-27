<?php

namespace App\Http\Controllers\Dcr;

use App\Http\Controllers\Controller;
use App\Models\DataCorrectionRequestForm;
use App\Models\DcrFormSubmission;
use App\Models\DcrCorrectionAction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class DcrCorrectionActionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DataCorrectionRequestForm $form, DcrFormSubmission $submission): View|JsonResponse
    {
        $query = $submission->correctionActions()
            ->with(['submission', 'performedBy'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
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
                $q->where('action_description', 'like', "%{$search}%")
                  ->orWhere('data_field', 'like', "%{$search}%")
                  ->orWhere('old_value', 'like', "%{$search}%")
                  ->orWhere('new_value', 'like', "%{$search}%");
            });
        }

        $correctionActions = $query->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'correction_actions' => $correctionActions,
                'form' => $form,
                'submission' => $submission,
                'filters' => $request->only(['action_type', 'status', 'date_from', 'date_to', 'search'])
            ]);
        }

        return view('admin.dcr.correction-actions.index', compact('correctionActions', 'form', 'submission'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(DataCorrectionRequestForm $form, DcrFormSubmission $submission): View
    {
        $actionTypes = DcrCorrectionAction::getActionTypes();
        $statuses = ['pending', 'in_progress', 'completed', 'failed', 'cancelled'];

        return view('admin.dcr.correction-actions.create', compact('form', 'submission', 'actionTypes', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, DataCorrectionRequestForm $form, DcrFormSubmission $submission): RedirectResponse
    {
        $validated = $request->validate([
            'action_type' => 'required|string|max:100',
            'action_description' => 'required|string',
            'data_field' => 'required|string|max:100',
            'old_value' => 'nullable|string',
            'new_value' => 'required|string',
            'action_justification' => 'required|string',
            'action_notes' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,failed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_completion_date' => 'nullable|date|after:today',
            'actual_completion_date' => 'nullable|date|after:today',
            'verification_required' => 'boolean',
            'verification_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $validated['dcr_form_submission_id'] = $submission->id;
        $validated['performed_by'] = auth()->id();

        $correctionAction = DcrCorrectionAction::create($validated);

        return redirect()->route('admin.dcr.forms.submissions.correction-actions.show', [$form, $submission, $correctionAction])
            ->with('success', 'Correction action created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataCorrectionRequestForm $form, DcrFormSubmission $submission, DcrCorrectionAction $correctionAction): View
    {
        $correctionAction->load(['submission', 'performedBy']);
        
        return view('admin.dcr.correction-actions.show', compact('form', 'submission', 'correctionAction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataCorrectionRequestForm $form, DcrFormSubmission $submission, DcrCorrectionAction $correctionAction): View
    {
        $actionTypes = DcrCorrectionAction::getActionTypes();
        $statuses = ['pending', 'in_progress', 'completed', 'failed', 'cancelled'];

        return view('admin.dcr.correction-actions.edit', compact('form', 'submission', 'correctionAction', 'actionTypes', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataCorrectionRequestForm $form, DcrFormSubmission $submission, DcrCorrectionAction $correctionAction): RedirectResponse
    {
        $validated = $request->validate([
            'action_type' => 'required|string|max:100',
            'action_description' => 'required|string',
            'data_field' => 'required|string|max:100',
            'old_value' => 'nullable|string',
            'new_value' => 'required|string',
            'action_justification' => 'required|string',
            'action_notes' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,failed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_completion_date' => 'nullable|date|after:today',
            'actual_completion_date' => 'nullable|date|after:today',
            'verification_required' => 'boolean',
            'verification_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        // Update completion date if status changed to completed
        if ($correctionAction->status !== 'completed' && $validated['status'] === 'completed') {
            $validated['actual_completion_date'] = now();
        }

        $correctionAction->update($validated);

        return redirect()->route('admin.dcr.forms.submissions.correction-actions.show', [$form, $submission, $correctionAction])
            ->with('success', 'Correction action updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataCorrectionRequestForm $form, DcrFormSubmission $submission, DcrCorrectionAction $correctionAction): RedirectResponse
    {
        $correctionAction->delete();

        return redirect()->route('admin.dcr.forms.submissions.correction-actions.index', [$form, $submission])
            ->with('success', 'Correction action deleted successfully.');
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, DataCorrectionRequestForm $form, DcrFormSubmission $submission, DcrCorrectionAction $correctionAction): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,failed,cancelled',
            'action_notes' => 'nullable|string',
        ]);

        $oldStatus = $correctionAction->status;
        
        // Update completion date if status changed to completed
        if ($oldStatus !== 'completed' && $validated['status'] === 'completed') {
            $validated['actual_completion_date'] = now();
        }

        $correctionAction->update($validated);

        // Log the status change
        $correctionAction->addAuditEntry('status_change', [
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'action_notes' => $validated['action_notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'correction_action' => $correctionAction->fresh()
        ]);
    }

    /**
     * Assign the correction action to a user.
     */
    public function assign(Request $request, DataCorrectionRequestForm $form, DcrFormSubmission $submission, DcrCorrectionAction $correctionAction): JsonResponse
    {
        $validated = $request->validate([
            'performed_by' => 'required|exists:users,id',
        ]);

        $correctionAction->update($validated);

        // Log the assignment
        $correctionAction->addAuditEntry('assignment', [
            'assigned_to' => $validated['performed_by'],
            'assigned_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Correction action assigned successfully.',
            'correction_action' => $correctionAction->fresh(['performedBy'])
        ]);
    }

    /**
     * Export correction actions to CSV.
     */
    public function export(Request $request, DataCorrectionRequestForm $form, DcrFormSubmission $submission)
    {
        $query = $submission->correctionActions()->with(['performedBy']);

        // Apply same filters as index
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
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

        $correctionActions = $query->get();

        $filename = 'dcr_correction_actions_' . $submission->id . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($correctionActions) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Action Type',
                'Data Field',
                'Old Value',
                'New Value',
                'Status',
                'Priority',
                'Performed By',
                'Created At',
                'Completed At'
            ]);

            // CSV data
            foreach ($correctionActions as $action) {
                fputcsv($file, [
                    $action->action_type,
                    $action->data_field,
                    $action->old_value,
                    $action->new_value,
                    $action->status,
                    $action->priority,
                    $action->performedBy?->name,
                    $action->created_at->format('Y-m-d H:i:s'),
                    $action->actual_completion_date?->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get correction action statistics.
     */
    public function statistics(DataCorrectionRequestForm $form, DcrFormSubmission $submission): JsonResponse
    {
        $stats = [
            'total_actions' => $submission->correctionActions()->count(),
            'actions_by_type' => $submission->correctionActions()
                ->selectRaw('action_type, COUNT(*) as count')
                ->groupBy('action_type')
                ->pluck('count', 'action_type'),
            'actions_by_status' => $submission->correctionActions()
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'actions_by_priority' => $submission->correctionActions()
                ->selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority'),
            'average_completion_time' => $submission->correctionActions()
                ->whereNotNull('actual_completion_date')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, actual_completion_date)) as avg_hours')
                ->value('avg_hours'),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk update correction actions.
     */
    public function bulkUpdate(Request $request, DataCorrectionRequestForm $form, DcrFormSubmission $submission): JsonResponse
    {
        $validated = $request->validate([
            'correction_action_ids' => 'required|array',
            'correction_action_ids.*' => 'exists:dcr_correction_actions,id',
            'action' => 'required|in:complete,mark_failed,cancel,assign,delete',
            'performed_by' => 'required_if:action,assign|exists:users,id',
            'action_notes' => 'nullable|string',
        ]);

        $correctionActions = DcrCorrectionAction::whereIn('id', $validated['correction_action_ids'])
            ->where('dcr_form_submission_id', $submission->id)
            ->get();

        $updated = 0;

        foreach ($correctionActions as $action) {
            switch ($validated['action']) {
                case 'complete':
                    $action->update([
                        'status' => 'completed',
                        'actual_completion_date' => now(),
                        'action_notes' => $validated['action_notes']
                    ]);
                    $updated++;
                    break;
                case 'mark_failed':
                    $action->update([
                        'status' => 'failed',
                        'action_notes' => $validated['action_notes']
                    ]);
                    $updated++;
                    break;
                case 'cancel':
                    $action->update([
                        'status' => 'cancelled',
                        'action_notes' => $validated['action_notes']
                    ]);
                    $updated++;
                    break;
                case 'assign':
                    $action->update([
                        'performed_by' => $validated['performed_by'],
                        'action_notes' => $validated['action_notes']
                    ]);
                    $updated++;
                    break;
                case 'delete':
                    $action->delete();
                    $updated++;
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully processed {$updated} correction actions.",
            'updated_count' => $updated
        ]);
    }
}