<?php

namespace App\Http\Controllers\Srf;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequestForm;
use App\Models\SrfFormSubmission;
use App\Models\SrfServiceAction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class SrfServiceActionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ServiceRequestForm $form, SrfFormSubmission $submission): View|JsonResponse
    {
        $query = $submission->serviceActions()
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
                  ->orWhere('action_notes', 'like', "%{$search}%")
                  ->orWhere('action_result', 'like', "%{$search}%");
            });
        }

        $serviceActions = $query->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'service_actions' => $serviceActions,
                'form' => $form,
                'submission' => $submission,
                'filters' => $request->only(['action_type', 'status', 'date_from', 'date_to', 'search'])
            ]);
        }

        return view('admin.srf.service-actions.index', compact('serviceActions', 'form', 'submission'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ServiceRequestForm $form, SrfFormSubmission $submission): View
    {
        $actionTypes = SrfServiceAction::getActionTypes();
        $statuses = ['pending', 'in_progress', 'completed', 'failed', 'cancelled'];

        return view('admin.srf.service-actions.create', compact('form', 'submission', 'actionTypes', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ServiceRequestForm $form, SrfFormSubmission $submission): RedirectResponse
    {
        $validated = $request->validate([
            'action_type' => 'required|string|max:100',
            'action_description' => 'required|string',
            'action_notes' => 'nullable|string',
            'action_result' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,failed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_completion_date' => 'nullable|date|after:today',
            'actual_completion_date' => 'nullable|date|after:today',
            'service_requirements' => 'nullable|string',
            'service_deliverables' => 'nullable|string',
            'service_timeline' => 'nullable|string',
            'service_cost' => 'nullable|numeric|min:0',
            'service_currency' => 'nullable|string|max:3',
            'service_payment_terms' => 'nullable|string',
            'service_warranty' => 'nullable|string',
            'service_support' => 'nullable|string',
            'compliance_requirements' => 'nullable|array',
            'compliance_requirements.*' => 'string|max:100',
            'quality_standards' => 'nullable|string',
            'risk_assessment' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $validated['srf_form_submission_id'] = $submission->id;
        $validated['performed_by'] = auth()->id();

        $serviceAction = SrfServiceAction::create($validated);

        return redirect()->route('admin.srf.forms.submissions.service-actions.show', [$form, $submission, $serviceAction])
            ->with('success', 'Service action created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceRequestForm $form, SrfFormSubmission $submission, SrfServiceAction $serviceAction): View
    {
        $serviceAction->load(['submission', 'performedBy']);
        
        return view('admin.srf.service-actions.show', compact('form', 'submission', 'serviceAction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceRequestForm $form, SrfFormSubmission $submission, SrfServiceAction $serviceAction): View
    {
        $actionTypes = SrfServiceAction::getActionTypes();
        $statuses = ['pending', 'in_progress', 'completed', 'failed', 'cancelled'];

        return view('admin.srf.service-actions.edit', compact('form', 'submission', 'serviceAction', 'actionTypes', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceRequestForm $form, SrfFormSubmission $submission, SrfServiceAction $serviceAction): RedirectResponse
    {
        $validated = $request->validate([
            'action_type' => 'required|string|max:100',
            'action_description' => 'required|string',
            'action_notes' => 'nullable|string',
            'action_result' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,failed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'estimated_completion_date' => 'nullable|date|after:today',
            'actual_completion_date' => 'nullable|date|after:today',
            'service_requirements' => 'nullable|string',
            'service_deliverables' => 'nullable|string',
            'service_timeline' => 'nullable|string',
            'service_cost' => 'nullable|numeric|min:0',
            'service_currency' => 'nullable|string|max:3',
            'service_payment_terms' => 'nullable|string',
            'service_warranty' => 'nullable|string',
            'service_support' => 'nullable|string',
            'compliance_requirements' => 'nullable|array',
            'compliance_requirements.*' => 'string|max:100',
            'quality_standards' => 'nullable|string',
            'risk_assessment' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        // Update completion date if status changed to completed
        if ($serviceAction->status !== 'completed' && $validated['status'] === 'completed') {
            $validated['actual_completion_date'] = now();
        }

        $serviceAction->update($validated);

        return redirect()->route('admin.srf.forms.submissions.service-actions.show', [$form, $submission, $serviceAction])
            ->with('success', 'Service action updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceRequestForm $form, SrfFormSubmission $submission, SrfServiceAction $serviceAction): RedirectResponse
    {
        $serviceAction->delete();

        return redirect()->route('admin.srf.forms.submissions.service-actions.index', [$form, $submission])
            ->with('success', 'Service action deleted successfully.');
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, ServiceRequestForm $form, SrfFormSubmission $submission, SrfServiceAction $serviceAction): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,failed,cancelled',
            'action_notes' => 'nullable|string',
        ]);

        $oldStatus = $serviceAction->status;
        
        // Update completion date if status changed to completed
        if ($oldStatus !== 'completed' && $validated['status'] === 'completed') {
            $validated['actual_completion_date'] = now();
        }

        $serviceAction->update($validated);

        // Log the status change
        $serviceAction->addAuditEntry('status_change', [
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'action_notes' => $validated['action_notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'service_action' => $serviceAction->fresh()
        ]);
    }

    /**
     * Assign the service action to a user.
     */
    public function assign(Request $request, ServiceRequestForm $form, SrfFormSubmission $submission, SrfServiceAction $serviceAction): JsonResponse
    {
        $validated = $request->validate([
            'performed_by' => 'required|exists:users,id',
        ]);

        $serviceAction->update($validated);

        // Log the assignment
        $serviceAction->addAuditEntry('assignment', [
            'assigned_to' => $validated['performed_by'],
            'assigned_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service action assigned successfully.',
            'service_action' => $serviceAction->fresh(['performedBy'])
        ]);
    }

    /**
     * Export service actions to CSV.
     */
    public function export(Request $request, ServiceRequestForm $form, SrfFormSubmission $submission)
    {
        $query = $submission->serviceActions()->with(['performedBy']);

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

        $serviceActions = $query->get();

        $filename = 'srf_service_actions_' . $submission->id . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($serviceActions) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Action Type',
                'Action Description',
                'Status',
                'Priority',
                'Service Cost',
                'Currency',
                'Performed By',
                'Created At',
                'Completed At'
            ]);

            // CSV data
            foreach ($serviceActions as $action) {
                fputcsv($file, [
                    $action->action_type,
                    $action->action_description,
                    $action->status,
                    $action->priority,
                    $action->service_cost,
                    $action->service_currency,
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
     * Get service action statistics.
     */
    public function statistics(ServiceRequestForm $form, SrfFormSubmission $submission): JsonResponse
    {
        $stats = [
            'total_actions' => $submission->serviceActions()->count(),
            'actions_by_type' => $submission->serviceActions()
                ->selectRaw('action_type, COUNT(*) as count')
                ->groupBy('action_type')
                ->pluck('count', 'action_type'),
            'actions_by_status' => $submission->serviceActions()
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'actions_by_priority' => $submission->serviceActions()
                ->selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority'),
            'total_service_cost' => $submission->serviceActions()
                ->sum('service_cost'),
            'average_service_cost' => $submission->serviceActions()
                ->avg('service_cost'),
            'average_completion_time' => $submission->serviceActions()
                ->whereNotNull('actual_completion_date')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, actual_completion_date)) as avg_hours')
                ->value('avg_hours'),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk update service actions.
     */
    public function bulkUpdate(Request $request, ServiceRequestForm $form, SrfFormSubmission $submission): JsonResponse
    {
        $validated = $request->validate([
            'service_action_ids' => 'required|array',
            'service_action_ids.*' => 'exists:srf_service_actions,id',
            'action' => 'required|in:complete,mark_failed,cancel,assign,delete',
            'performed_by' => 'required_if:action,assign|exists:users,id',
            'action_notes' => 'nullable|string',
        ]);

        $serviceActions = SrfServiceAction::whereIn('id', $validated['service_action_ids'])
            ->where('srf_form_submission_id', $submission->id)
            ->get();

        $updated = 0;

        foreach ($serviceActions as $action) {
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
            'message' => "Successfully processed {$updated} service actions.",
            'updated_count' => $updated
        ]);
    }
}