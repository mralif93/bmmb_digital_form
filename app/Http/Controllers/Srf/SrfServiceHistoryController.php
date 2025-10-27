<?php

namespace App\Http\Controllers\Srf;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequestForm;
use App\Models\SrfFormSubmission;
use App\Models\SrfServiceHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class SrfServiceHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ServiceRequestForm $form, SrfFormSubmission $submission): View|JsonResponse
    {
        $query = $submission->serviceHistory()
            ->with(['submission', 'createdBy'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
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
                $q->where('event_description', 'like', "%{$search}%")
                  ->orWhere('event_notes', 'like', "%{$search}%")
                  ->orWhere('event_result', 'like', "%{$search}%");
            });
        }

        $serviceHistory = $query->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'service_history' => $serviceHistory,
                'form' => $form,
                'submission' => $submission,
                'filters' => $request->only(['event_type', 'status', 'date_from', 'date_to', 'search'])
            ]);
        }

        return view('admin.srf.service-history.index', compact('serviceHistory', 'form', 'submission'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ServiceRequestForm $form, SrfFormSubmission $submission): View
    {
        $eventTypes = SrfServiceHistory::getEventTypes();
        $statuses = ['pending', 'in_progress', 'completed', 'failed', 'cancelled'];

        return view('admin.srf.service-history.create', compact('form', 'submission', 'eventTypes', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ServiceRequestForm $form, SrfFormSubmission $submission): RedirectResponse
    {
        $validated = $request->validate([
            'event_type' => 'required|string|max:100',
            'event_description' => 'required|string',
            'event_notes' => 'nullable|string',
            'event_result' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,failed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'event_date' => 'required|date',
            'event_duration' => 'nullable|integer|min:0',
            'event_location' => 'nullable|string|max:255',
            'event_participants' => 'nullable|array',
            'event_participants.*' => 'string|max:255',
            'event_resources' => 'nullable|array',
            'event_resources.*' => 'string|max:255',
            'event_outcomes' => 'nullable|string',
            'event_lessons_learned' => 'nullable|string',
            'event_recommendations' => 'nullable|string',
            'event_follow_up_required' => 'boolean',
            'event_follow_up_notes' => 'nullable|string',
            'compliance_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $validated['srf_form_submission_id'] = $submission->id;
        $validated['created_by'] = auth()->id();

        $serviceHistory = SrfServiceHistory::create($validated);

        return redirect()->route('admin.srf.forms.submissions.service-history.show', [$form, $submission, $serviceHistory])
            ->with('success', 'Service history record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceRequestForm $form, SrfFormSubmission $submission, SrfServiceHistory $serviceHistory): View
    {
        $serviceHistory->load(['submission', 'createdBy']);
        
        return view('admin.srf.service-history.show', compact('form', 'submission', 'serviceHistory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceRequestForm $form, SrfFormSubmission $submission, SrfServiceHistory $serviceHistory): View
    {
        $eventTypes = SrfServiceHistory::getEventTypes();
        $statuses = ['pending', 'in_progress', 'completed', 'failed', 'cancelled'];

        return view('admin.srf.service-history.edit', compact('form', 'submission', 'serviceHistory', 'eventTypes', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceRequestForm $form, SrfFormSubmission $submission, SrfServiceHistory $serviceHistory): RedirectResponse
    {
        $validated = $request->validate([
            'event_type' => 'required|string|max:100',
            'event_description' => 'required|string',
            'event_notes' => 'nullable|string',
            'event_result' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed,failed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'event_date' => 'required|date',
            'event_duration' => 'nullable|integer|min:0',
            'event_location' => 'nullable|string|max:255',
            'event_participants' => 'nullable|array',
            'event_participants.*' => 'string|max:255',
            'event_resources' => 'nullable|array',
            'event_resources.*' => 'string|max:255',
            'event_outcomes' => 'nullable|string',
            'event_lessons_learned' => 'nullable|string',
            'event_recommendations' => 'nullable|string',
            'event_follow_up_required' => 'boolean',
            'event_follow_up_notes' => 'nullable|string',
            'compliance_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $serviceHistory->update($validated);

        return redirect()->route('admin.srf.forms.submissions.service-history.show', [$form, $submission, $serviceHistory])
            ->with('success', 'Service history record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceRequestForm $form, SrfFormSubmission $submission, SrfServiceHistory $serviceHistory): RedirectResponse
    {
        $serviceHistory->delete();

        return redirect()->route('admin.srf.forms.submissions.service-history.index', [$form, $submission])
            ->with('success', 'Service history record deleted successfully.');
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, ServiceRequestForm $form, SrfFormSubmission $submission, SrfServiceHistory $serviceHistory): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,failed,cancelled',
            'event_notes' => 'nullable|string',
        ]);

        $oldStatus = $serviceHistory->status;
        $serviceHistory->update($validated);

        // Log the status change
        $serviceHistory->addAuditEntry('status_change', [
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'event_notes' => $validated['event_notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'service_history' => $serviceHistory->fresh()
        ]);
    }

    /**
     * Assign the service history record to a user.
     */
    public function assign(Request $request, ServiceRequestForm $form, SrfFormSubmission $submission, SrfServiceHistory $serviceHistory): JsonResponse
    {
        $validated = $request->validate([
            'created_by' => 'required|exists:users,id',
        ]);

        $serviceHistory->update($validated);

        // Log the assignment
        $serviceHistory->addAuditEntry('assignment', [
            'assigned_to' => $validated['created_by'],
            'assigned_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service history record assigned successfully.',
            'service_history' => $serviceHistory->fresh(['createdBy'])
        ]);
    }

    /**
     * Export service history to CSV.
     */
    public function export(Request $request, ServiceRequestForm $form, SrfFormSubmission $submission)
    {
        $query = $submission->serviceHistory()->with(['createdBy']);

        // Apply same filters as index
        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
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

        $serviceHistory = $query->get();

        $filename = 'srf_service_history_' . $submission->id . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($serviceHistory) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Event Type',
                'Event Description',
                'Event Date',
                'Event Duration',
                'Status',
                'Priority',
                'Created By',
                'Created At'
            ]);

            // CSV data
            foreach ($serviceHistory as $history) {
                fputcsv($file, [
                    $history->event_type,
                    $history->event_description,
                    $history->event_date?->format('Y-m-d H:i:s'),
                    $history->event_duration,
                    $history->status,
                    $history->priority,
                    $history->createdBy?->name,
                    $history->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get service history statistics.
     */
    public function statistics(ServiceRequestForm $form, SrfFormSubmission $submission): JsonResponse
    {
        $stats = [
            'total_history_records' => $submission->serviceHistory()->count(),
            'history_records_by_type' => $submission->serviceHistory()
                ->selectRaw('event_type, COUNT(*) as count')
                ->groupBy('event_type')
                ->pluck('count', 'event_type'),
            'history_records_by_status' => $submission->serviceHistory()
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'history_records_by_priority' => $submission->serviceHistory()
                ->selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority'),
            'total_event_duration' => $submission->serviceHistory()
                ->sum('event_duration'),
            'average_event_duration' => $submission->serviceHistory()
                ->avg('event_duration'),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk update service history records.
     */
    public function bulkUpdate(Request $request, ServiceRequestForm $form, SrfFormSubmission $submission): JsonResponse
    {
        $validated = $request->validate([
            'service_history_ids' => 'required|array',
            'service_history_ids.*' => 'exists:srf_service_history,id',
            'action' => 'required|in:complete,mark_failed,cancel,assign,delete',
            'created_by' => 'required_if:action,assign|exists:users,id',
            'event_notes' => 'nullable|string',
        ]);

        $serviceHistory = SrfServiceHistory::whereIn('id', $validated['service_history_ids'])
            ->where('srf_form_submission_id', $submission->id)
            ->get();

        $updated = 0;

        foreach ($serviceHistory as $history) {
            switch ($validated['action']) {
                case 'complete':
                    $history->update([
                        'status' => 'completed',
                        'event_notes' => $validated['event_notes']
                    ]);
                    $updated++;
                    break;
                case 'mark_failed':
                    $history->update([
                        'status' => 'failed',
                        'event_notes' => $validated['event_notes']
                    ]);
                    $updated++;
                    break;
                case 'cancel':
                    $history->update([
                        'status' => 'cancelled',
                        'event_notes' => $validated['event_notes']
                    ]);
                    $updated++;
                    break;
                case 'assign':
                    $history->update([
                        'created_by' => $validated['created_by'],
                        'event_notes' => $validated['event_notes']
                    ]);
                    $updated++;
                    break;
                case 'delete':
                    $history->delete();
                    $updated++;
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully processed {$updated} service history records.",
            'updated_count' => $updated
        ]);
    }
}