<?php

namespace App\Http\Controllers\Dar;

use App\Http\Controllers\Controller;
use App\Models\DataAccessRequestForm;
use App\Models\DarFormSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class DarFormSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DataAccessRequestForm $form): View|JsonResponse
    {
        $query = $form->submissions()
            ->with(['user', 'reviewedBy'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
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
                $q->where('submission_number', 'like', "%{$search}%")
                  ->orWhere('data_subject_name', 'like', "%{$search}%")
                  ->orWhere('data_subject_email', 'like', "%{$search}%");
            });
        }

        $submissions = $query->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'submissions' => $submissions,
                'form' => $form,
                'filters' => $request->only(['status', 'user_id', 'date_from', 'date_to', 'search'])
            ]);
        }

        return view('admin.dar.submissions.index', compact('submissions', 'form'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(DataAccessRequestForm $form): View
    {
        $users = User::where('status', 'active')->get();
        $form->load('formFields');

        return view('admin.dar.submissions.create', compact('form', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, DataAccessRequestForm $form): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'submission_data' => 'required|array',
            'submission_metadata' => 'nullable|array',
            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string|max:500',
            'submission_notes' => 'nullable|string',
        ]);

        // Generate submission number
        $submissionNumber = 'DAR-SUB-' . date('Y') . '-' . str_pad(
            DarFormSubmission::whereYear('created_at', date('Y'))->count() + 1,
            6,
            '0',
            STR_PAD_LEFT
        );

        $validated['dar_form_id'] = $form->id;
        $validated['submission_number'] = $submissionNumber;
        $validated['status'] = 'submitted';
        $validated['submitted_at'] = now();

        $submission = DarFormSubmission::create($validated);

        return redirect()->route('admin.dar.forms.submissions.show', [$form, $submission])
            ->with('success', 'Form submission created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataAccessRequestForm $form, DarFormSubmission $submission): View
    {
        $submission->load(['user', 'reviewedBy', 'form', 'responseData']);
        
        return view('admin.dar.submissions.show', compact('form', 'submission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataAccessRequestForm $form, DarFormSubmission $submission): View
    {
        $users = User::where('status', 'active')->get();
        $form->load('formFields');

        return view('admin.dar.submissions.edit', compact('form', 'submission', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataAccessRequestForm $form, DarFormSubmission $submission): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:submitted,under_review,approved,rejected,completed',
            'submission_data' => 'required|array',
            'submission_metadata' => 'nullable|array',
            'review_notes' => 'nullable|string',
            'reviewed_by' => 'nullable|exists:users,id',
            'reviewed_at' => 'nullable|date',
            'submission_notes' => 'nullable|string',
        ]);

        // Update timestamps based on status changes
        if ($submission->status !== $validated['status']) {
            switch ($validated['status']) {
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

        $submission->update($validated);

        return redirect()->route('admin.dar.forms.submissions.show', [$form, $submission])
            ->with('success', 'Form submission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataAccessRequestForm $form, DarFormSubmission $submission): RedirectResponse
    {
        $submission->delete();

        return redirect()->route('admin.dar.forms.submissions.index', $form)
            ->with('success', 'Form submission deleted successfully.');
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, DataAccessRequestForm $form, DarFormSubmission $submission): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:submitted,under_review,approved,rejected,completed',
            'review_notes' => 'nullable|string',
            'reviewed_by' => 'nullable|exists:users,id',
        ]);

        $oldStatus = $submission->status;
        
        // Update timestamps based on status changes
        switch ($validated['status']) {
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

        $submission->update($validated);

        // Log the status change
        $submission->addAuditEntry('status_change', [
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'review_notes' => $validated['review_notes'] ?? null,
            'reviewed_by' => $validated['reviewed_by'] ?? auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'submission' => $submission->fresh(['reviewedBy'])
        ]);
    }

    /**
     * Assign the submission to a user for review.
     */
    public function assign(Request $request, DataAccessRequestForm $form, DarFormSubmission $submission): JsonResponse
    {
        $validated = $request->validate([
            'reviewed_by' => 'required|exists:users,id',
        ]);

        $submission->update($validated);

        // Log the assignment
        $submission->addAuditEntry('assignment', [
            'assigned_to' => $validated['reviewed_by'],
            'assigned_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Submission assigned successfully.',
            'submission' => $submission->fresh(['reviewedBy'])
        ]);
    }

    /**
     * Export submissions to CSV.
     */
    public function export(Request $request, DataAccessRequestForm $form)
    {
        $query = $form->submissions()->with(['user', 'reviewedBy']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        $submissions = $query->get();

        $filename = 'dar_submissions_' . $form->id . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($submissions) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Submission Number',
                'Status',
                'Data Subject Name',
                'Data Subject Email',
                'Submitted At',
                'Reviewed By',
                'Review Notes',
                'Created At'
            ]);

            // CSV data
            foreach ($submissions as $submission) {
                fputcsv($file, [
                    $submission->submission_number,
                    $submission->status,
                    $submission->submission_data['data_subject_name'] ?? '',
                    $submission->submission_data['data_subject_email'] ?? '',
                    $submission->submitted_at?->format('Y-m-d H:i:s'),
                    $submission->reviewedBy?->name,
                    $submission->review_notes,
                    $submission->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get submission statistics.
     */
    public function statistics(DataAccessRequestForm $form): JsonResponse
    {
        $stats = [
            'total_submissions' => $form->submissions()->count(),
            'submissions_by_status' => $form->submissions()
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'submissions_by_month' => $form->submissions()
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month'),
            'average_processing_time' => $form->submissions()
                ->whereNotNull('completed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, submitted_at, completed_at)) as avg_hours')
                ->value('avg_hours'),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk update submissions.
     */
    public function bulkUpdate(Request $request, DataAccessRequestForm $form): JsonResponse
    {
        $validated = $request->validate([
            'submission_ids' => 'required|array',
            'submission_ids.*' => 'exists:dar_form_submissions,id',
            'action' => 'required|in:approve,reject,assign,delete',
            'reviewed_by' => 'required_if:action,assign|exists:users,id',
            'review_notes' => 'nullable|string',
        ]);

        $submissions = DarFormSubmission::whereIn('id', $validated['submission_ids'])
            ->where('dar_form_id', $form->id)
            ->get();

        $updated = 0;

        foreach ($submissions as $submission) {
            switch ($validated['action']) {
                case 'approve':
                    $submission->update([
                        'status' => 'approved',
                        'reviewed_by' => $validated['reviewed_by'] ?? auth()->id(),
                        'reviewed_at' => now(),
                        'approved_at' => now(),
                        'review_notes' => $validated['review_notes']
                    ]);
                    $updated++;
                    break;
                case 'reject':
                    $submission->update([
                        'status' => 'rejected',
                        'reviewed_by' => $validated['reviewed_by'] ?? auth()->id(),
                        'reviewed_at' => now(),
                        'review_notes' => $validated['review_notes']
                    ]);
                    $updated++;
                    break;
                case 'assign':
                    $submission->update([
                        'reviewed_by' => $validated['reviewed_by'],
                        'review_notes' => $validated['review_notes']
                    ]);
                    $updated++;
                    break;
                case 'delete':
                    $submission->delete();
                    $updated++;
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully processed {$updated} submissions.",
            'updated_count' => $updated
        ]);
    }
}