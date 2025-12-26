@extends('layouts.admin-minimal')

@section('title', $form->name . ' Submission Details - BMMB Digital Forms')
@section('page-title', $form->name . ' Submission #' . $submission->id)
@section('page-description', 'View ' . $form->name . ' submission details')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/print-submission.css') }}">
@endpush

@section('content')
<div class="mb-4 flex items-center justify-between">
    <div></div>
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.submissions.pdf', [$form->slug, $submission->id]) }}" target="_blank" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors no-print">
            <i class='bx bx-show mr-1.5'></i>
            Preview PDF
        </a>
        <a href="{{ route('admin.submissions.index', $form->slug) }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors no-print">
            <i class='bx bx-arrow-back mr-1.5'></i>
            Back to List
        </a>
        @if(auth()->user()->isAdmin())
            @if($submission->trashed())
                <div class="px-3 py-2 text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg">
                    <i class='bx bx-error-circle mr-1.5'></i>
                    This submission has been deleted
                </div>
                <button onclick="restoreSubmission('{{ $form->slug }}', {{ $submission->id }})" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                    <i class='bx bx-refresh mr-1.5'></i>
                    Restore Submission
                </button>
                <button onclick="forceDeleteSubmission('{{ $form->slug }}', {{ $submission->id }})" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                    <i class='bx bx-trash mr-1.5'></i>
                    Delete Permanently
                </button>
            @else
                <a href="{{ route('admin.submissions.edit', [$form->slug, $submission->id]) }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors">
                    <i class='bx bx-edit mr-1.5'></i>
                    Edit Submission
                </a>
                <button onclick="deleteSubmission('{{ $form->slug }}', {{ $submission->id }})" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                    <i class='bx bx-trash mr-1.5'></i>
                    Delete Submission
                </button>
            @endif
        @endif
    </div>
</div>

@if($submission->trashed())
<div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-800 dark:text-red-400 flex items-center">
    <i class='bx bx-error-circle mr-2 text-lg'></i>
    <div>
        <strong>This submission has been deleted.</strong>
        <p class="text-xs mt-1">Deleted on {{ $submission->deleted_at->format('M d, Y h:i A') }}</p>
    </div>
</div>
@endif

@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-800 dark:text-red-400">
    {{ session('error') }}
</div>
@endif

<div class="grid grid-cols-1 {{ auth()->user()->isAdmin() ? 'lg:grid-cols-3' : '' }} gap-6">
    <!-- Main Details -->
    <div class="{{ auth()->user()->isAdmin() ? 'lg:col-span-2' : '' }} space-y-6">
        <!-- Submission Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class='bx bx-info-circle mr-2 text-primary-600 dark:text-primary-400'></i>
                Submission Information
            </h3>
            <div class="space-y-3">
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Submission ID
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white font-semibold flex-1 text-left">
                        #{{ $submission->id }}
                    </dd>
                </div>
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Reference Number
                    </dt>
                    <dd class="text-sm flex-1 text-left">
                        @if($submission->reference_number)
                            <div class="flex items-center space-x-2">
                                <span class="font-mono text-primary-600 dark:text-primary-400 font-semibold">
                                    {{ $submission->reference_number }}
                                </span>
                                <button onclick="copyToClipboard('{{ $submission->reference_number }}')"
                                        class="text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
                                        title="Copy reference number">
                                    <i class='bx bx-copy'></i>
                                </button>
                            </div>
                        @else
                            <span class="text-gray-400 dark:text-gray-500 italic">No reference number</span>
                        @endif
                    </dd>
                </div>
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Status
                    </dt>
                    <dd class="text-sm flex-1 text-left">
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
                                'submitted' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'pending_process' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400',
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
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Submitted By
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        @php
                            $responses = $submission->field_responses ?? [];
                            $formSlug = $submission->form->slug ?? 'unknown';
                            
                            // Extract customer information based on form type
                            if ($formSlug === 'srf') {
                                // SRF: header_1=Name, header_3=IC, header_4=Phone
                                $customerName = $responses['header_1'] ?? null;
                                $customerIC = $responses['header_3'] ?? null;
                                $customerContact = $responses['header_4'] ?? null; // Phone
                            } elseif ($formSlug === 'dar' || $formSlug === 'dcr') {
                                // DAR/DCR: field_3_1=Name, field_3_2=IC Number, field_3_6 or field_3_7=Email/Phone
                                $customerName = $responses['field_3_1'] ?? null;
                                $customerIC = $responses['field_3_2'] ?? null;
                                $customerContact = $responses['field_3_6'] ?? $responses['field_3_7'] ?? null; // Email or Phone
                            } else {
                                // Generic fallback
                                $customerName = $responses['header_1'] ?? $responses['field_3_1'] ?? null;
                                $customerIC = $responses['header_2'] ?? $responses['header_3'] ?? $responses['field_3_2'] ?? null;
                                $customerContact = $responses['header_3'] ?? $responses['header_4'] ?? $responses['field_3_6'] ?? null;
                            }
                        @endphp
                        
                        @if($submission->user)
                            {{ $submission->user->first_name . ' ' . $submission->user->last_name }}
                            <span class="text-gray-500 dark:text-gray-400">({{ $submission->user->email }})</span>
                        @elseif($customerName)
                            {{ $customerName }}
                            @if($customerIC)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">IC: {{ $customerIC }}</div>
                            @endif
                            @if($customerContact)
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $customerContact }}</div>
                            @endif
                            <div class="text-xs text-gray-400 mt-1 italic">(Public Submission)</div>
                        @else
                            Guest <span class="text-xs text-gray-400 italic">(No customer info available)</span>
                        @endif
                    </dd>
                </div>
                @if($submission->branch)
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Branch
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $submission->branch->name }} ({{ $submission->branch->code }})
                    </dd>
                </div>
                @endif
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Submitted At
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y h:i A') : 'Not submitted' }}
                    </dd>
                </div>
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Created At
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $submission->created_at->format('M d, Y h:i A') }}
                    </dd>
                </div>
                @if($submission->taken_up_by && $submission->taken_up_at)
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Taken Up By
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $submission->takenUpBy ? $submission->takenUpBy->first_name . ' ' . $submission->takenUpBy->last_name : 'N/A' }}
                        <span class="text-gray-500 dark:text-gray-400 text-xs block mt-1">on {{ $submission->taken_up_at->format('M d, Y h:i A') }}</span>
                    </dd>
                </div>
                @endif
                @if($submission->completed_by && $submission->completed_at)
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Completed By
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $submission->completedBy ? $submission->completedBy->first_name . ' ' . $submission->completedBy->last_name : 'N/A' }}
                        <span class="text-gray-500 dark:text-gray-400 text-xs block mt-1">on {{ $submission->completed_at->format('M d, Y h:i A') }}</span>
                    </dd>
                </div>
                @endif
                @if($submission->completion_notes)
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Completion Notes
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left whitespace-pre-wrap">
                        {{ $submission->completion_notes }}
                    </dd>
                </div>
                @endif
            </div>
        </div>

        <!-- Form Field Responses -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class='bx bx-file-blank mr-2 text-primary-600 dark:text-primary-400'></i>
                Form Responses
            </h3>
            
            @php
                use App\Services\FormSubmissionPresenter;
                $groupedData = FormSubmissionPresenter::formatSubmissionData($submission);
            @endphp

            @if(count($groupedData) > 0)
                @foreach($groupedData as $sectionTitle => $fields)
                    <!-- Section Group -->
                    <div class="mb-6 last:mb-0">
                        <h4 class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-3 pb-2 border-b-2 border-primary-500 dark:border-primary-400">
                            {{ $sectionTitle }}
                        </h4>
                        
                        <div class="space-y-3">
                            @foreach($fields as $field)
                                @if(FormSubmissionPresenter::shouldDisplayField($field['field_name'], $field['value']))
                                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                                        {{ $field['label'] }}
                                    </dt>
                                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                                        {!! FormSubmissionPresenter::renderFieldValue($field['type'], $field['value']) !!}
                                    </dd>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
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
            <div class="space-y-3">
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Notes
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left whitespace-pre-wrap">
                        {{ $submission->review_notes }}
                    </dd>
                </div>
                @if($submission->reviewedBy && $submission->reviewed_at)
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Reviewed By
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $submission->reviewedBy->first_name . ' ' . $submission->reviewedBy->last_name }} on {{ $submission->reviewed_at->format('M d, Y h:i A') }}
                    </dd>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Staff-Only Sections (Read-Only Display) --}}
        @if($submission->acknowledgment_received_by || $submission->verification_verified_by)
        <div class="bg-amber-50 dark:bg-amber-900/10 rounded-lg shadow-sm border-2 border-amber-300 dark:border-amber-700 p-6">
            <h3 class="text-sm font-bold text-amber-900 dark:text-amber-100 mb-4 uppercase tracking-wider">
                FOR BMMB OFFICE USE ONLY
            </h3>

            {{-- Part F: Acknowledgment Receipt --}}
            @if($submission->acknowledgment_received_by)
            <div class="mb-6">
                <h4 class="text-xs font-semibold text-gray-900 dark:text-white mb-3">
                    PART F: ACKNOWLEDGMENT RECEIPT
                </h4>
                <div class="space-y-3">
                    <div class="flex items-start border-b border-amber-200 dark:border-amber-800 pb-2 gap-4">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                            Received by
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white flex-1">
                            {{ $submission->acknowledgment_received_by }}
                        </dd>
                    </div>
                    @if($submission->acknowledgment_date_received)
                    <div class="flex items-start border-b border-amber-200 dark:border-amber-800 pb-2 gap-4">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                            Date Received
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white flex-1">
                            {{ $submission->acknowledgment_date_received->format('M d, Y') }}
                        </dd>
                    </div>
                    @endif
                    @if($submission->acknowledgment_staff_name)
                    <div class="flex items-start border-b border-amber-200 dark:border-amber-800 pb-2 gap-4">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                            Name
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white flex-1">
                            {{ $submission->acknowledgment_staff_name }}
                        </dd>
                    </div>
                    @endif
                    @if($submission->acknowledgment_designation)
                    <div class="flex items-start border-b border-amber-200 dark:border-amber-800 pb-2 gap-4">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                            Designation
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white flex-1">
                            {{ $submission->acknowledgment_designation }}
                        </dd>
                    </div>
                    @endif
                    @if($submission->acknowledgment_stamp)
                    <div class="flex items-start pb-2 gap-4">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                            Official Rubber Stamp
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white flex-1">
                            {{ $submission->acknowledgment_stamp }}
                        </dd>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Part G: Verification --}}
            @if($submission->verification_verified_by)
            <div class="{{ $submission->acknowledgment_received_by ? 'pt-4 border-t border-amber-300 dark:border-amber-700' : '' }}">
                <h4 class="text-xs font-semibold text-gray-900 dark:text-white mb-3">
                    PART G: VERIFICATION
                </h4>
                <div class="space-y-3">
                    <div class="flex items-start border-b border-amber-200 dark:border-amber-800 pb-2 gap-4">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                            Verified by
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white flex-1">
                            {{ $submission->verification_verified_by }}
                        </dd>
                    </div>
                    @if($submission->verification_date)
                    <div class="flex items-start border-b border-amber-200 dark:border-amber-800 pb-2 gap-4">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                            Date
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white flex-1">
                            {{ $submission->verification_date->format('M d, Y') }}
                        </dd>
                    </div>
                    @endif
                    @if($submission->verification_staff_name)
                    <div class="flex items-start border-b border-amber-200 dark:border-amber-800 pb-2 gap-4">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                            Name
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white flex-1">
                            {{ $submission->verification_staff_name }}
                        </dd>
                    </div>
                    @endif
                    @if($submission->verification_designation)
                    <div class="flex items-start border-b border-amber-200 dark:border-amber-800 pb-2 gap-4">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                            Designation
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white flex-1">
                            {{ $submission->verification_designation }}
                        </dd>
                    </div>
                    @endif
                    @if($submission->verification_stamp)
                    <div class="flex items-start pb-2 gap-4">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                            Official Rubber Stamp
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white flex-1">
                            {{ $submission->verification_stamp }}
                        </dd>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>

    @if(auth()->user()->isAdmin())
    <!-- Sidebar (Admin Only) -->
    <div class="space-y-6">
        <!-- Status Update (Admin Only) -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Update Status</h3>
            <form action="{{ route('admin.submissions.status.update', [$form->slug, $submission->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" id="status-select" class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="draft" {{ $submission->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ $submission->status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="pending_process" {{ $submission->status == 'pending_process' ? 'selected' : '' }}>Pending Process</option>
                        <option value="under_review" {{ $submission->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="approved" {{ $submission->status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ $submission->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="completed" {{ $submission->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="expired" {{ $submission->status == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="in_progress" {{ $submission->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="cancelled" {{ $submission->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ $submission->review_notes }}</textarea>
                </div>
                <button type="button" onclick="confirmStatusUpdate()" class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    Update Status
                </button>
            </form>
        </div>

        <!-- Submission Metadata (Admin Only) -->
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
                    <dd class="text-gray-900 dark:text-white">{{ $submission->started_at->format('M d, Y h:i A') }}</dd>
                </div>
                @endif
                @if($submission->last_modified_at)
                <div>
                    <dt class="text-gray-500 dark:text-gray-400">Last Modified</dt>
                    <dd class="text-gray-900 dark:text-white">{{ $submission->last_modified_at->format('M d, Y h:i A') }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
@php
    $settings = \Illuminate\Support\Facades\Cache::get('system_settings', [
        'primary_color' => '#FE8000',
    ]);
    $primaryColor = $settings['primary_color'] ?? '#FE8000';
@endphp

function confirmStatusUpdate() {
    const statusSelect = document.getElementById('status-select');
    const currentStatus = '{{ $submission->status }}';
    const newStatus = statusSelect.value;
    const statusLabels = {
        'draft': 'Draft',
        'submitted': 'Submitted',
        'pending_process': 'Pending Process',
        'under_review': 'Under Review',
        'approved': 'Approved',
        'rejected': 'Rejected',
        'completed': 'Completed',
        'expired': 'Expired',
        'in_progress': 'In Progress',
        'cancelled': 'Cancelled'
    };
    
    if (currentStatus === newStatus) {
        Swal.fire({
            icon: 'info',
            title: 'No Change',
            text: 'The status is already set to ' + statusLabels[newStatus] + '.',
            confirmButtonColor: '{{ $primaryColor }}',
            customClass: {
                popup: 'rounded-lg'
            }
        });
        return;
    }
    
    Swal.fire({
        title: 'Update Status?',
        html: `
            <div class="text-center">
                <p class="mb-2">Are you sure you want to update the submission status?</p>
                <p class="text-sm text-gray-600 mb-2">
                    From: <strong>${statusLabels[currentStatus] || currentStatus}</strong><br>
                    To: <strong>${statusLabels[newStatus] || newStatus}</strong>
                </p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Update Status',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '{{ $primaryColor }}',
        cancelButtonColor: '#6b7280',
        customClass: {
            popup: 'rounded-lg',
            htmlContainer: 'text-center',
            confirmButton: 'rounded-lg',
            cancelButton: 'rounded-lg'
        },
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.querySelector('form[action*="status.update"]').submit();
        }
    });
}

function deleteSubmission(formSlug, submissionId) {
    Swal.fire({
        title: 'Delete Submission?',
        html: `
            <div class="text-center">
                <p class="mb-2">Are you sure you want to delete this submission?</p>
                <p class="text-sm text-gray-600">This action can be undone by restoring it from the trashed submissions.</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        customClass: {
            popup: 'rounded-lg',
            htmlContainer: 'text-center',
            confirmButton: 'rounded-lg',
            cancelButton: 'rounded-lg'
        },
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('admin.submissions.destroy', [':formSlug', ':id']) }}`.replace(':formSlug', formSlug).replace(':id', submissionId);
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function restoreSubmission(formSlug, submissionId) {
    Swal.fire({
        title: 'Restore Submission?',
        html: `
            <div class="text-center">
                <p class="mb-2">Are you sure you want to restore this submission?</p>
                <p class="text-sm text-gray-600">The submission will be restored and available in the active submissions list.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Restore',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '{{ $primaryColor }}',
        cancelButtonColor: '#6b7280',
        customClass: {
            popup: 'rounded-lg',
            htmlContainer: 'text-center',
            confirmButton: 'rounded-lg',
            cancelButton: 'rounded-lg'
        },
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('admin.submissions.restore', [':formSlug', ':id']) }}`.replace(':formSlug', formSlug).replace(':id', submissionId);
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function forceDeleteSubmission(formSlug, submissionId) {
    Swal.fire({
        title: 'Permanently Delete Submission?',
        html: `
            <div class="text-center">
                <p class="mb-2"><strong>Warning: This action cannot be undone!</strong></p>
                <p class="mb-2">Are you sure you want to permanently delete this submission?</p>
                <p class="text-sm text-red-600">This will permanently remove all data including uploaded files.</p>
            </div>
        `,
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete Permanently',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        customClass: {
            popup: 'rounded-lg',
            htmlContainer: 'text-center',
            confirmButton: 'rounded-lg',
            cancelButton: 'rounded-lg'
        },
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('admin.submissions.force-delete', [':formSlug', ':id']) }}`.replace(':formSlug', formSlug).replace(':id', submissionId);
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Copy to clipboard function
function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Reference number copied!',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }).catch(err => {
            console.error('Failed to copy:', err);
        });
    }
}
</script>
@endpush
@endsection

