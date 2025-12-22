<!-- Submission Information -->
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
                    {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y h:i A') : 'Not submitted' }}
                </dd>
            </div>
            <div class="flex items-start gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    Created At
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                    {{ $submission->created_at->format('M d, Y h:i A') }}
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
                            {{ $submission->taken_up_at->format('M d, Y h:i A') }}</span>
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
                            {{ $submission->completed_at->format('M d, Y h:i A') }}</span>
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
    @if($submission->submissionData && $submission->submissionData->count() > 0)
        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                <i class='bx bx-file-blank mr-2 text-primary-600 dark:text-primary-400'></i>
                Form Responses
            </h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @foreach($submission->submissionData as $data)
                    @if($data->field && $data->field->is_active)
                        <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-2 last:border-0 gap-4">
                            <dt
                                class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                                {{ $data->field->field_label ?? ucwords(str_replace('_', ' ', $data->field->field_name ?? 'Unknown Field')) }}
                            </dt>
                            <dd class="text-xs text-gray-900 dark:text-white flex-1 text-left">
                                @if($data->field_value_json)
                                    <pre
                                        class="bg-white dark:bg-gray-800 p-2 rounded text-xs overflow-x-auto">{{ json_encode($data->field_value_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @elseif($data->file_path)
                                    <a href="{{ asset('storage/' . $data->file_path) }}" target="_blank"
                                        class="text-primary-600 dark:text-primary-400 hover:underline text-xs">
                                        <i class='bx bx-download mr-1'></i>Download File
                                    </a>
                                @else
                                    {{ $data->field_value ?? 'N/A' }}
                                @endif
                            </dd>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @else
        {{-- Fallback for legacy submissions stored only in JSON columns --}}
        @php
            $fieldResponses = $submission->field_responses ?? [];
            $submissionData = $submission->submission_data ?? [];
            $allData = array_merge($fieldResponses, $submissionData);
        @endphp

        @if(count($allData) > 0)
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                    <i class='bx bx-file-blank mr-2 text-primary-600 dark:text-primary-400'></i>
                    Form Responses
                </h3>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($allData as $key => $value)
                        <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-2 last:border-0 gap-4">
                            <dt
                                class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                                {{ ucwords(str_replace('_', ' ', $key)) }}
                            </dt>
                            <dd class="text-xs text-gray-900 dark:text-white flex-1 text-left">
                                @if(is_array($value))
                                    <pre
                                        class="bg-white dark:bg-gray-800 p-2 rounded text-xs overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @elseif(is_bool($value))
                                    {{ $value ? 'Yes' : 'No' }}
                                @else
                                    {{ $value ?? 'N/A' }}
                                @endif
                            </dd>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
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
                        {{ $submission->reviewed_at->format('M d, Y h:i A') }}
                    </p>
                @endif
            </div>
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