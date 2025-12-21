@extends('layouts.admin-minimal')

@section('page-title', $form->name . ' Submissions')
@section('page-description', 'View and manage ' . $form->name . ' submissions')

@section('content')
@php
    use App\Services\FormSubmissionPresenter;
@endphp
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Submissions</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $submissions->total() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class='bx bx-file text-2xl text-green-600'></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $submissions->where('status', 'submitted')->count() + $submissions->where('status', 'under_review')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <i class='bx bx-time text-2xl text-yellow-600'></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Approved</p>
                    <p class="text-2xl font-bold text-green-600">{{ $submissions->where('status', 'approved')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class='bx bx-check-circle text-2xl text-green-600'></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Rejected</p>
                    <p class="text-2xl font-bold text-red-600">{{ $submissions->where('status', 'rejected')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class='bx bx-x-circle text-2xl text-red-600'></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.submissions.index', $form->slug) }}" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                <!-- Search Input -->
                <div class="{{ (auth()->user()->isAdmin() || auth()->user()->isHQ()) ? 'md:col-span-6' : 'md:col-span-9' }}">
                    <label for="search" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}"
                               placeholder="Search by ID, name, email, branch..."
                               class="w-full pl-10 pr-4 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <i class='bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500'></i>
                    </div>
                </div>
                
                <!-- Status Filter -->
                <div class="{{ (auth()->user()->isAdmin() || auth()->user()->isHQ()) ? 'md:col-span-3' : 'md:col-span-3' }}">
                    <label for="status" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" 
                            id="status"
                            class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted</option>
                        <option value="pending_process" {{ request('status') === 'pending_process' ? 'selected' : '' }}>Pending Process</option>
                        <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Under Review</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <!-- Branch Filter (Admin and HQ only) -->
                @if(auth()->user()->isAdmin() || auth()->user()->isHQ())
                <div class="md:col-span-3">
                    <label for="branch_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                    <select name="branch_id" 
                            id="branch_id"
                            class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Branches</option>
                        @foreach($branches ?? [] as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }} ({{ $branch->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Date From</label>
                    <input type="date" 
                           name="date_from" 
                           id="date_from" 
                           value="{{ request('date_from') }}"
                           class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                
                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Date To</label>
                    <input type="date" 
                           name="date_to" 
                           id="date_to" 
                           value="{{ request('date_to') }}"
                           class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-2">
                <a href="{{ route('admin.submissions.index', $form->slug) }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-xs font-semibold rounded-lg transition-colors">
                    <i class='bx bx-x mr-1.5 text-sm'></i>
                    Clear
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    <i class='bx bx-search mr-1.5 text-sm'></i>
                    Search
                </button>
            </div>
        </form>
    </div>

    <!-- Submissions Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">All {{ $form->name }} Submissions</h3>
            @if(auth()->user()->isAdmin())
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.submissions.trashed', $form->slug) }}" 
                   class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    <i class='bx bx-trash mr-1.5'></i>
                    Trashed
                </a>
                <a href="{{ route('admin.submissions.create', $form->slug) }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                    <i class='bx bx-plus mr-1.5'></i>
                    Create Submission
                </a>
            </div>
            @endif
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reference No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Branch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($submissions as $submission)
                    @php
                        $responses = $submission->field_responses ?? [];
                        $formSlug = $form->slug;
                        
                        // Extract key information based on form type
                        if ($formSlug === 'srf') {
                            // SRF: header_1=Name, header_3=IC, header_4=Phone
                            $customerName = $responses['header_1'] ?? null;
                            $customerIC = $responses['header_3'] ?? null;
                        } elseif ($formSlug === 'dar' || $formSlug === 'dcr') {
                            // DAR/DCR: field_3_1=Name, field_3_2=IC
                            $customerName = $responses['field_3_1'] ?? null;
                            $customerIC = $responses['field_3_2'] ?? null;
                        } else {
                            // Generic fallback
                            $customerName = $responses['header_1'] ?? $responses['field_3_1'] ?? null;
                            $customerIC = $responses['header_2'] ?? $responses['header_3'] ?? $responses['field_3_2'] ?? null;
                        }
                        
                        // If still no name, try user data
                        if (!$customerName && $submission->user) {
                            $customerName = $submission->user->first_name . ' ' . $submission->user->last_name;
                        }
                        
                        // Generate a brief summary of what was requested
                        $summary = '';
                        if ($formSlug === 'srf') {
                            $services = [];
                            for ($i = 1; $i <= 13; $i++) {
                                if (isset($responses["field_$i"]) && $responses["field_$i"] == '1') {
                                    $services[] = FormSubmissionPresenter::getFieldLabel('srf', "field_$i");
                                }
                            }
                            $summary = !empty($services) ? implode(', ', $services) : 'Multiple Services';
                        } elseif ($formSlug === 'dar') {
                            $summary = 'Data Access Request';
                        } elseif ($formSlug === 'dcr') {
                            $summary = 'Data Correction Request';
                        }
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            #{{ $submission->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($submission->reference_number)
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-mono text-primary-600 dark:text-primary-400">
                                        {{ $submission->reference_number }}
                                    </span>
                                    <button onclick="copyToClipboard('{{ $submission->reference_number }}')" 
                                            class="text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors"
                                            title="Copy reference number">
                                        <i class='bx bx-copy text-sm'></i>
                                    </button>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 dark:text-gray-500">No reference</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                            <div class="font-medium">{{ $customerName ?: 'Guest' }}</div>
                            @if($customerIC)
                                <div class="text-xs text-gray-500 dark:text-gray-400">IC: {{ $customerIC }}</div>
                            @endif
                            @if($summary)
                                <div class="text-xs text-primary-600 dark:text-primary-400 mt-0.5">{{ Str::limit($summary, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $submission->branch ? $submission->branch->name . ' (' . $submission->branch->code . ')' : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
                                    'submitted' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    'pending_process' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400',
                                    'under_review' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                    'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                    'completed' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                    'expired' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
                                    'in_progress' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                                    'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$submission->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($submission->submitted_at)
                                <div>{{ $submission->submitted_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $submission->submitted_at->format('h:i A') }}</div>
                            @else
                                <div>{{ $submission->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $submission->created_at->format('h:i A') }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.submissions.show', [$form->slug, $submission->id]) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 rounded-lg text-xs transition-colors">
                                    <i class='bx bx-show mr-1'></i>
                                    View
                                </a>
                                @if(auth()->user()->isOO() || auth()->user()->isABM() || auth()->user()->isBM() || auth()->user()->isCFE())
                                    @if($submission->status === 'submitted')
                                        <button onclick="confirmTakeUp('{{ $form->slug }}', {{ $submission->id }})" class="inline-flex items-center justify-center px-3 py-1.5 bg-cyan-100 hover:bg-cyan-200 text-cyan-700 dark:bg-cyan-900/30 dark:hover:bg-cyan-900/50 dark:text-cyan-400 rounded-lg text-xs transition-colors">
                                            <i class='bx bx-check mr-1'></i>
                                            Take Up
                                        </button>
                                    @elseif($submission->status === 'pending_process')
                                        <button onclick="confirmComplete('{{ $form->slug }}', {{ $submission->id }})" class="inline-flex items-center justify-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 dark:bg-green-900/30 dark:hover:bg-green-900/50 dark:text-green-400 rounded-lg text-xs transition-colors">
                                            <i class='bx bx-check-circle mr-1'></i>
                                            Complete
                                        </button>
                                    @endif
                                @endif
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.submissions.edit', [$form->slug, $submission->id]) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 dark:text-orange-400 rounded-lg text-xs transition-colors">
                                        <i class='bx bx-edit mr-1'></i>
                                        Edit
                                    </a>
                                    <button onclick="deleteSubmission('{{ $form->slug }}', {{ $submission->id }})" class="inline-flex items-center justify-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg text-xs transition-colors">
                                        <i class='bx bx-trash mr-1'></i>
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No submissions found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $submissions->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
@php
    $settings = \Illuminate\Support\Facades\Cache::get('system_settings', [
        'primary_color' => '#FE8000',
    ]);
    $primaryColor = $settings['primary_color'] ?? '#FE8000';
@endphp

function confirmTakeUp(formSlug, submissionId) {
    Swal.fire({
        title: 'Take Up Submission?',
        html: `
            <div class="text-center">
                <p class="mb-2">Are you sure you want to take up this submission?</p>
                <p class="text-sm text-gray-600">The status will change from <strong>Submitted</strong> to <strong>Pending Process</strong>.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Take Up',
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
            form.action = `{{ route('admin.submissions.take-up', [':formSlug', ':id']) }}`.replace(':formSlug', formSlug).replace(':id', submissionId);
            
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

function confirmComplete(formSlug, submissionId) {
    Swal.fire({
        title: 'Complete Submission?',
        html: `
            <div class="text-left">
                <p class="mb-3 text-sm text-gray-700 dark:text-gray-300">The status will change from <strong>Pending Process</strong> to <strong>Completed</strong>.</p>
                <div class="mb-3">
                    <label for="completion_notes" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Completion Notes (Optional)</label>
                    <textarea 
                        id="completion_notes" 
                        name="completion_notes" 
                        rows="4" 
                        class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 resize-none"
                        placeholder="Enter any notes or remarks about the completion..."
                        maxlength="1000"></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Maximum 1000 characters</p>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Complete',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '{{ $primaryColor }}',
        cancelButtonColor: '#6b7280',
        customClass: {
            popup: 'rounded-lg',
            htmlContainer: 'text-left',
            confirmButton: 'rounded-lg',
            cancelButton: 'rounded-lg'
        },
        reverseButtons: true,
        preConfirm: () => {
            const notes = document.getElementById('completion_notes').value;
            return {
                completion_notes: notes
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('admin.submissions.complete', [':formSlug', ':id']) }}`.replace(':formSlug', formSlug).replace(':id', submissionId);
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            if (result.value && result.value.completion_notes) {
                const notesInput = document.createElement('input');
                notesInput.type = 'hidden';
                notesInput.name = 'completion_notes';
                notesInput.value = result.value.completion_notes;
                form.appendChild(notesInput);
            }
            
            document.body.appendChild(form);
            form.submit();
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

