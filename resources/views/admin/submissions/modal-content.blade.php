<!-- Submission Information -->
@php
    $settings = \Illuminate\Support\Facades\Cache::get('system_settings', []);
    $dateFormat = $settings['date_format'] ?? 'Y-m-d';
    $timeFormat = $settings['time_format'] ?? 'H:i';
@endphp
<div class="space-y-4">
    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
            <i class='bx bx-info-circle mr-2 text-primary-600 dark:text-primary-400'></i>
            Submission Information
        </h3>
        <div class="space-y-2">
            <div class="flex items-start gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    Submission ID
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white font-semibold flex-1 text-left">
                    #{{ $submission->id }}
                </dd>
            </div>
            <div class="flex items-start gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    Form
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                    {{ $submission->form->name ?? 'N/A' }}
                </dd>
            </div>
            <div class="flex items-start gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
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
                            'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                            'expired' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
                        ];
                    @endphp
                    <span
                        class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$submission->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                    </span>
                </dd>
            </div>
            <div class="flex items-start gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    Submitted By
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                    {{ $submission->user ? $submission->user->first_name . ' ' . $submission->user->last_name : 'Guest' }}
                    @if($submission->user)
                        <span
                            class="text-gray-500 dark:text-gray-400 text-xs block mt-1">({{ $submission->user->email }})</span>
                    @endif
                </dd>
            </div>
            @if($submission->branch)
                <div class="flex items-start gap-4">
                    <dt
                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Branch
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $submission->branch->name }}
                        @if($submission->branch->ti_agent_code)
                            <span
                                class="text-gray-500 dark:text-gray-400 text-xs block mt-1">({{ $submission->branch->ti_agent_code }})</span>
                        @endif
                    </dd>
                </div>
            @endif
            <div class="flex items-start gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    Submitted At
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                    {{ $submission->submitted_at ? $timezoneHelper->convert($submission->submitted_at)->format($dateFormat . ' ' . $timeFormat) : 'Not submitted' }}
                </dd>
            </div>
            <div class="flex items-start gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    Created At
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                    {{ $timezoneHelper->convert($submission->created_at)->format($dateFormat . ' ' . $timeFormat) }}
                </dd>
            </div>
            @if($submission->taken_up_by && $submission->taken_up_at)
                <div class="flex items-start gap-4">
                    <dt
                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Taken Up By
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $submission->takenUpBy ? $submission->takenUpBy->first_name . ' ' . $submission->takenUpBy->last_name : 'N/A' }}
                        <span class="text-gray-500 dark:text-gray-400 text-xs block mt-1">on
                            {{ $timezoneHelper->convert($submission->taken_up_at)->format($dateFormat . ' ' . $timeFormat) }}</span>
                    </dd>
                </div>
            @endif
            @if($submission->completed_by && $submission->completed_at)
                <div class="flex items-start gap-4">
                    <dt
                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Completed By
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $submission->completedBy ? $submission->completedBy->first_name . ' ' . $submission->completedBy->last_name : 'N/A' }}
                        <span class="text-gray-500 dark:text-gray-400 text-xs block mt-1">on
                            {{ $submission->completed_at->format($dateFormat . ' ' . $timeFormat) }}</span>
                    </dd>
                </div>
            @endif
            @if($submission->completion_notes)
                <div class="flex items-start gap-4">
                    <dt
                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
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
    @php
        use App\Services\FormSubmissionPresenter;
        $groupedData = FormSubmissionPresenter::formatSubmissionData($submission);
    @endphp

    @if(count($groupedData) > 0)
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                <i class='bx bx-file-blank mr-2 text-primary-600 dark:text-primary-400'></i>
                Form Responses
            </h3>
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @foreach($groupedData as $sectionTitle => $fields)
                    <!-- Section Group -->
                    <div class="last:mb-0">
                        <h4
                            class="text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-2 pb-1 border-b border-primary-500 dark:border-primary-400">
                            {{ $sectionTitle }}
                        </h4>

                        <div class="space-y-2">
                            @foreach($fields as $field)
                                @if(FormSubmissionPresenter::shouldDisplayField($field['field_name'], $field['value']))
                                    <div
                                        class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-2 last:border-0 gap-4">
                                        <dt
                                            class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                                            {{ $field['label'] }}
                                        </dt>
                                        <dd class="text-xs text-gray-900 dark:text-white flex-1 text-left break-words">
                                            {!! FormSubmissionPresenter::renderFieldValue($field['type'], $field['value']) !!}
                                        </dd>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400 italic">No form data available</p>
    @endif

    <!-- File Uploads -->
    @if($submission->file_uploads && count($submission->file_uploads) > 0)
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                <i class='bx bx-paperclip mr-2 text-primary-600 dark:text-primary-400'></i>
                File Uploads
            </h3>
            <div class="space-y-2">
                @foreach($submission->file_uploads as $file)
                    <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded">
                        <span class="text-xs text-gray-900 dark:text-white">{{ $file['name'] ?? 'Unknown file' }}</span>
                        @if(isset($file['path']))
                            <a href="{{ asset('storage/' . $file['path']) }}" target="_blank"
                                class="text-primary-600 dark:text-primary-400 hover:underline text-xs">
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
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                <i class='bx bx-note mr-2 text-primary-600 dark:text-primary-400'></i>
                Review Notes
            </h3>
            <div class="space-y-2">
                <p class="text-xs text-gray-900 dark:text-white whitespace-pre-wrap">{{ $submission->review_notes }}</p>
                @if($submission->reviewedBy && $submission->reviewed_at)
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Reviewed by {{ $submission->reviewedBy->first_name . ' ' . $submission->reviewedBy->last_name }} on
                        {{ $submission->reviewed_at->format($dateFormat . ' ' . $timeFormat) }}
                    </p>
                @endif
            </div>
        </div>
    @endif

    {{-- Staff-Only Sections (Read-Only Display) --}}
    @if($submission->acknowledgment_received_by || $submission->verification_verified_by)
        <div class="bg-amber-50 dark:bg-amber-900/10 rounded-lg border-2 border-amber-300 dark:border-amber-700 p-4">
            <h3 class="text-xs font-bold text-amber-900 dark:text-amber-100 mb-3 uppercase tracking-wider">
                FOR BMMB OFFICE USE ONLY
            </h3>

            {{-- Part F: Acknowledgment Receipt --}}
            @if($submission->acknowledgment_received_by)
                <div class="mb-4">
                    <h4 class="text-xs font-semibold text-gray-900 dark:text-white mb-2">
                        PART F: ACKNOWLEDGMENT RECEIPT
                    </h4>
                    <div class="space-y-2">
                        <div class="flex items-start gap-4">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                                Received by
                            </dt>
                            <dd class="text-xs text-gray-900 dark:text-white flex-1">
                                {{ $submission->acknowledgment_received_by }}
                            </dd>
                        </div>
                        @if($submission->acknowledgment_date_received)
                            <div class="flex items-start gap-4">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                                    Date Received
                                </dt>
                                <dd class="text-xs text-gray-900 dark:text-white flex-1">
                                    {{ $submission->acknowledgment_date_received->format($dateFormat) }}
                                </dd>
                            </div>
                        @endif
                        @if($submission->acknowledgment_staff_name)
                            <div class="flex items-start gap-4">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                                    Name
                                </dt>
                                <dd class="text-xs text-gray-900 dark:text-white flex-1">
                                    {{ $submission->acknowledgment_staff_name }}
                                </dd>
                            </div>
                        @endif
                        @if($submission->acknowledgment_designation)
                            <div class="flex items-start gap-4">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                                    Designation
                                </dt>
                                <dd class="text-xs text-gray-900 dark:text-white flex-1">
                                    {{ $submission->acknowledgment_designation }}
                                </dd>
                            </div>
                        @endif
                        @if($submission->acknowledgment_stamp)
                            <div class="flex items-start gap-4">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                                    Official Rubber Stamp
                                </dt>
                                <dd class="text-xs text-gray-900 dark:text-white flex-1">
                                    {{ $submission->acknowledgment_stamp }}
                                </dd>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Part G: Verification --}}
            @if($submission->verification_verified_by)
                <div
                    class="{{ $submission->acknowledgment_received_by ? 'pt-3 border-t border-amber-300 dark:border-amber-700' : '' }}">
                    <h4 class="text-xs font-semibold text-gray-900 dark:text-white mb-2">
                        PART G: VERIFICATION
                    </h4>
                    <div class="space-y-2">
                        <div class="flex items-start gap-4">
                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                                Verified by
                            </dt>
                            <dd class="text-xs text-gray-900 dark:text-white flex-1">
                                {{ $submission->verification_verified_by }}
                            </dd>
                        </div>
                        @if($submission->verification_date)
                            <div class="flex items-start gap-4">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                                    Date
                                </dt>
                                <dd class="text-xs text-gray-900 dark:text-white flex-1">
                                    {{ $submission->verification_date->format($dateFormat) }}
                                </dd>
                            </div>
                        @endif
                        @if($submission->verification_staff_name)
                            <div class="flex items-start gap-4">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                                    Name
                                </dt>
                                <dd class="text-xs text-gray-900 dark:text-white flex-1">
                                    {{ $submission->verification_staff_name }}
                                </dd>
                            </div>
                        @endif
                        @if($submission->verification_designation)
                            <div class="flex items-start gap-4">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                                    Designation
                                </dt>
                                <dd class="text-xs text-gray-900 dark:text-white flex-1">
                                    {{ $submission->verification_designation }}
                                </dd>
                            </div>
                        @endif
                        @if($submission->verification_stamp)
                            <div class="flex items-start gap-4">
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase flex-shrink-0 w-1/3">
                                    Official Rubber Stamp
                                </dt>
                                <dd class="text-xs text-gray-900 dark:text-white flex-1">
                                    {{ $submission->verification_stamp }}
                                </dd>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex items-center justify-end space-x-2 pt-4 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('admin.submissions.show', [$form->slug, $submission->id]) }}"
            class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition-colors">
            <i class='bx bx-show mr-2'></i>
            View Full Details
        </a>
    </div>
</div>