<?php

namespace App\Http\Controllers\Dar;

use App\Http\Controllers\Controller;
use App\Models\DataAccessRequestForm;
use App\Models\DarFormSubmission;
use App\Models\DarResponseData;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class DarResponseDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, DataAccessRequestForm $form, DarFormSubmission $submission): View|JsonResponse
    {
        $query = $submission->responseData()
            ->with(['submission', 'createdBy'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('data_type')) {
            $query->where('data_type', $request->data_type);
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
                $q->where('data_title', 'like', "%{$search}%")
                  ->orWhere('data_description', 'like', "%{$search}%")
                  ->orWhere('data_source', 'like', "%{$search}%");
            });
        }

        $responseData = $query->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'response_data' => $responseData,
                'form' => $form,
                'submission' => $submission,
                'filters' => $request->only(['data_type', 'status', 'date_from', 'date_to', 'search'])
            ]);
        }

        return view('admin.dar.response-data.index', compact('responseData', 'form', 'submission'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(DataAccessRequestForm $form, DarFormSubmission $submission): View
    {
        $dataTypes = DarResponseData::getDataTypes();
        $statuses = ['pending', 'processing', 'ready', 'delivered', 'rejected'];

        return view('admin.dar.response-data.create', compact('form', 'submission', 'dataTypes', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, DataAccessRequestForm $form, DarFormSubmission $submission): RedirectResponse
    {
        $validated = $request->validate([
            'data_type' => 'required|string|max:100',
            'data_title' => 'required|string|max:255',
            'data_description' => 'required|string',
            'data_source' => 'required|string|max:255',
            'data_format' => 'required|string|max:50',
            'data_size' => 'nullable|integer|min:0',
            'data_location' => 'required|string|max:500',
            'access_method' => 'required|string|max:100',
            'access_credentials' => 'nullable|string',
            'data_retention_period' => 'required|string|max:100',
            'data_sharing_restrictions' => 'nullable|string',
            'compliance_notes' => 'nullable|string',
            'status' => 'required|in:pending,processing,ready,delivered,rejected',
            'delivery_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $validated['dar_form_submission_id'] = $submission->id;
        $validated['created_by'] = auth()->id();

        $responseData = DarResponseData::create($validated);

        return redirect()->route('admin.dar.forms.submissions.response-data.show', [$form, $submission, $responseData])
            ->with('success', 'Response data created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataAccessRequestForm $form, DarFormSubmission $submission, DarResponseData $responseData): View
    {
        $responseData->load(['submission', 'createdBy']);
        
        return view('admin.dar.response-data.show', compact('form', 'submission', 'responseData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataAccessRequestForm $form, DarFormSubmission $submission, DarResponseData $responseData): View
    {
        $dataTypes = DarResponseData::getDataTypes();
        $statuses = ['pending', 'processing', 'ready', 'delivered', 'rejected'];

        return view('admin.dar.response-data.edit', compact('form', 'submission', 'responseData', 'dataTypes', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataAccessRequestForm $form, DarFormSubmission $submission, DarResponseData $responseData): RedirectResponse
    {
        $validated = $request->validate([
            'data_type' => 'required|string|max:100',
            'data_title' => 'required|string|max:255',
            'data_description' => 'required|string',
            'data_source' => 'required|string|max:255',
            'data_format' => 'required|string|max:50',
            'data_size' => 'nullable|integer|min:0',
            'data_location' => 'required|string|max:500',
            'access_method' => 'required|string|max:100',
            'access_credentials' => 'nullable|string',
            'data_retention_period' => 'required|string|max:100',
            'data_sharing_restrictions' => 'nullable|string',
            'compliance_notes' => 'nullable|string',
            'status' => 'required|in:pending,processing,ready,delivered,rejected',
            'delivery_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $responseData->update($validated);

        return redirect()->route('admin.dar.forms.submissions.response-data.show', [$form, $submission, $responseData])
            ->with('success', 'Response data updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataAccessRequestForm $form, DarFormSubmission $submission, DarResponseData $responseData): RedirectResponse
    {
        $responseData->delete();

        return redirect()->route('admin.dar.forms.submissions.response-data.index', [$form, $submission])
            ->with('success', 'Response data deleted successfully.');
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, DataAccessRequestForm $form, DarFormSubmission $submission, DarResponseData $responseData): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,ready,delivered,rejected',
            'delivery_notes' => 'nullable|string',
        ]);

        $oldStatus = $responseData->status;
        $responseData->update($validated);

        // Log the status change
        $responseData->addAuditEntry('status_change', [
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'delivery_notes' => $validated['delivery_notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'response_data' => $responseData->fresh()
        ]);
    }

    /**
     * Download the response data.
     */
    public function download(DataAccessRequestForm $form, DarFormSubmission $submission, DarResponseData $responseData)
    {
        if ($responseData->status !== 'ready' && $responseData->status !== 'delivered') {
            return redirect()->back()
                ->with('error', 'Data is not ready for download.');
        }

        // Log the download
        $responseData->addAuditEntry('download', [
            'downloaded_by' => auth()->id(),
            'downloaded_at' => now(),
        ]);

        // In a real implementation, you would return the actual file
        // For now, we'll return a JSON response with the data location
        return response()->json([
            'data_location' => $responseData->data_location,
            'access_method' => $responseData->access_method,
            'access_credentials' => $responseData->access_credentials,
            'download_url' => route('admin.dar.forms.submissions.response-data.download-file', [$form, $submission, $responseData])
        ]);
    }

    /**
     * Download the actual file.
     */
    public function downloadFile(DataAccessRequestForm $form, DarFormSubmission $submission, DarResponseData $responseData)
    {
        if ($responseData->status !== 'ready' && $responseData->status !== 'delivered') {
            abort(403, 'Data is not ready for download.');
        }

        // In a real implementation, you would return the actual file
        // For now, we'll return a placeholder response
        return response()->json([
            'message' => 'File download would be implemented here',
            'data_location' => $responseData->data_location,
            'data_format' => $responseData->data_format,
            'data_size' => $responseData->data_size
        ]);
    }

    /**
     * Export response data to CSV.
     */
    public function export(Request $request, DataAccessRequestForm $form, DarFormSubmission $submission)
    {
        $query = $submission->responseData()->with(['createdBy']);

        // Apply same filters as index
        if ($request->filled('data_type')) {
            $query->where('data_type', $request->data_type);
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

        $responseData = $query->get();

        $filename = 'dar_response_data_' . $submission->id . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($responseData) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Data Type',
                'Data Title',
                'Data Source',
                'Data Format',
                'Data Size',
                'Status',
                'Created By',
                'Created At'
            ]);

            // CSV data
            foreach ($responseData as $data) {
                fputcsv($file, [
                    $data->data_type,
                    $data->data_title,
                    $data->data_source,
                    $data->data_format,
                    $data->data_size,
                    $data->status,
                    $data->createdBy?->name,
                    $data->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get response data statistics.
     */
    public function statistics(DataAccessRequestForm $form, DarFormSubmission $submission): JsonResponse
    {
        $stats = [
            'total_response_data' => $submission->responseData()->count(),
            'response_data_by_type' => $submission->responseData()
                ->selectRaw('data_type, COUNT(*) as count')
                ->groupBy('data_type')
                ->pluck('count', 'data_type'),
            'response_data_by_status' => $submission->responseData()
                ->selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'total_data_size' => $submission->responseData()
                ->sum('data_size'),
            'average_data_size' => $submission->responseData()
                ->avg('data_size'),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk update response data.
     */
    public function bulkUpdate(Request $request, DataAccessRequestForm $form, DarFormSubmission $submission): JsonResponse
    {
        $validated = $request->validate([
            'response_data_ids' => 'required|array',
            'response_data_ids.*' => 'exists:dar_response_data,id',
            'action' => 'required|in:mark_ready,mark_delivered,reject,delete',
            'delivery_notes' => 'nullable|string',
        ]);

        $responseData = DarResponseData::whereIn('id', $validated['response_data_ids'])
            ->where('dar_form_submission_id', $submission->id)
            ->get();

        $updated = 0;

        foreach ($responseData as $data) {
            switch ($validated['action']) {
                case 'mark_ready':
                    $data->update([
                        'status' => 'ready',
                        'delivery_notes' => $validated['delivery_notes']
                    ]);
                    $updated++;
                    break;
                case 'mark_delivered':
                    $data->update([
                        'status' => 'delivered',
                        'delivery_notes' => $validated['delivery_notes']
                    ]);
                    $updated++;
                    break;
                case 'reject':
                    $data->update([
                        'status' => 'rejected',
                        'delivery_notes' => $validated['delivery_notes']
                    ]);
                    $updated++;
                    break;
                case 'delete':
                    $data->delete();
                    $updated++;
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully processed {$updated} response data items.",
            'updated_count' => $updated
        ]);
    }
}