@extends('layouts.admin-minimal')

@section('title', 'DCR Submission Details - BMMB Digital Forms')
@section('page-title', 'DCR Submission #' . $submission->id)
@section('page-description', 'View Data Correction Request submission details')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.submissions.dcr') }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
        <i class='bx bx-arrow-back mr-1.5'></i>
        Back to List
    </a>
</div>

@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Submission Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class='bx bx-info-circle mr-2 text-primary-600 dark:text-primary-400'></i>
                Submission Information
            </h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Submission ID
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white font-semibold">
                        #{{ $submission->id }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Status
                    </dt>
                    <dd class="text-sm">
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
                                'submitted' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'under_review' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                'completed' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$submission->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Submitted By
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $submission->user ? $submission->user->name : 'Guest' }}
                        @if($submission->user)
                            <span class="text-gray-500 dark:text-gray-400">({{ $submission->user->email }})</span>
                        @endif
                    </dd>
                </div>
                @if($submission->branch)
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Branch
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $submission->branch->branch_name }}
                    </dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Submitted At
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $submission->submitted_at ? $timezoneHelper->convert($submission->submitted_at)?->format('M d, Y h:i A') : 'Not submitted' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Created At
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $timezoneHelper->convert($submission->created_at)?->format('M d, Y h:i A') }}
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Form Field Responses -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class='bx bx-file-blank mr-2 text-primary-600 dark:text-primary-400'></i>
                Form Responses
            </h3>
            
            @php
                $fieldResponses = $submission->field_responses ?? [];
                $submissionData = $submission->submission_data ?? [];
                $allData = array_merge($fieldResponses, $submissionData);
            @endphp

            @if(count($allData) > 0)
            <dl class="space-y-4">
                @foreach($allData as $key => $value)
                <div class="border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        {{ ucwords(str_replace('_', ' ', $key)) }}
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        @if(is_array($value))
                            <pre class="bg-gray-50 dark:bg-gray-900 p-2 rounded text-xs overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @elseif(is_bool($value))
                            {{ $value ? 'Yes' : 'No' }}
                        @else
                            {{ $value ?? 'N/A' }}
                        @endif
                    </dd>
                </div>
                @endforeach
            </dl>
            @else
            <p class="text-sm text-gray-500 dark:text-gray-400 italic">No form data available</p>
            @endif
        </div>

        <!-- File Uploads -->
        @if($submission->file_uploads && count($submission->file_uploads) > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class='bx bx-paperclip mr-2 text-primary-600 dark:text-primary-400'></i>
                File Uploads
            </h3>
            <div class="space-y-2">
                @foreach($submission->file_uploads as $file)
                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-900 rounded">
                    <span class="text-sm text-gray-900 dark:text-white">{{ $file['name'] ?? 'Unknown file' }}</span>
                    @if(isset($file['path']))
                    <a href="{{ asset('storage/' . $file['path']) }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline text-xs">
                        <i class='bx bx-download mr-1'></i>Download
                    </a>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Review Notes -->
        @if($submission->review_notes)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class='bx bx-note mr-2 text-primary-600 dark:text-primary-400'></i>
                Review Notes
            </h3>
            <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ $submission->review_notes }}</p>
            @if($submission->reviewedBy && $submission->reviewed_at)
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                Reviewed by {{ $submission->reviewedBy->name }} on {{ $timezoneHelper->convert($submission->reviewed_at)?->format('M d, Y h:i A') }}
            </p>
            @endif
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Status Update -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Update Status</h3>
            <form action="{{ route('admin.submissions.status.update', ['type' => 'dcr', 'id' => $submission->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="draft" {{ $submission->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ $submission->status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="under_review" {{ $submission->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="approved" {{ $submission->status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $submission->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="completed" {{ $submission->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ $submission->review_notes }}</textarea>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    Update Status
                </button>
            </form>
        </div>

        <!-- Submission Metadata -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Metadata</h3>
            <dl class="space-y-2 text-xs">
                @if($submission->submission_token)
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Token</dt>
                    <dd class="text-gray-900 dark:text-white font-mono">{{ $submission->submission_token }}</dd>
                </div>
                @endif
                @if($submission->ip_address)
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">IP Address</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $submission->ip_address }}</dd>
                </div>
                @endif
                @if($submission->started_at)
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Started At</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $timezoneHelper->convert($submission->started_at)?->format('M d, Y h:i A') }}</dd>
                </div>
                @endif
                @if($submission->last_modified_at)
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Last Modified</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $timezoneHelper->convert($submission->last_modified_at)?->format('M d, Y h:i A') }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>
</div>
@endsection


