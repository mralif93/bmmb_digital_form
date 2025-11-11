@extends('layouts.admin-minimal')

@section('title', 'Dashboard - BMMB Digital Forms')
@section('page-title', 'Dashboard')
@section('page-description', 'Overview of your form management system')

@section('content')
<!-- Dashboard Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Forms Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Forms</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_forms'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                    All forms
                </p>
            </div>
            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center shadow-sm">
                <i class='bx bx-file-blank text-base text-primary-600 dark:text-primary-400'></i>
            </div>
        </div>
    </div>

    <!-- Total Active Forms Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Active Forms</p>
                <p class="text-xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $stats['total_active_forms'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                    Currently active
                </p>
            </div>
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center shadow-sm">
                <i class='bx bx-check-circle text-base text-green-600 dark:text-green-400'></i>
            </div>
        </div>
    </div>

    <!-- Total Submissions Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Submissions</p>
                <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['total_form_submissions'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                    All submissions
                </p>
            </div>
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center shadow-sm">
                <i class='bx bx-clipboard text-base text-blue-600 dark:text-blue-400'></i>
            </div>
        </div>
    </div>

    <!-- Total Completed Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Completed</p>
                <p class="text-xl font-bold text-purple-600 dark:text-purple-400 mt-1">{{ $stats['total_completed_submissions'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                    Completed submissions
                </p>
            </div>
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center shadow-sm">
                <i class='bx bx-check-double text-base text-purple-600 dark:text-purple-400'></i>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 mb-6">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Search & Filter</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Filter form submissions</p>
            </div>
        </div>
        
        <form method="GET" action="{{ route('admin.dashboard') }}" class="space-y-3">
            <!-- Row 1: Search Box, Forms -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <!-- Search Box -->
                <div class="md:col-span-6">
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
                
                <!-- Forms -->
                <div class="md:col-span-6">
                    <label for="form_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Form</label>
                    <select name="form_id" 
                            id="form_id"
                            class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Forms</option>
                        @foreach($forms ?? [] as $form)
                            <option value="{{ $form->id }}" {{ request('form_id') == $form->id ? 'selected' : '' }}>
                                {{ $form->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Row 2: Branch, Status -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <!-- Branch (Admin and HQ only) -->
                @if(auth()->user()->isAdmin() || auth()->user()->isHQ())
                <div class="md:col-span-6">
                    <label for="branch_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                    <select name="branch_id" 
                            id="branch_id"
                            class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">All Branches</option>
                        @foreach($branches ?? [] as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                <!-- Empty space for non-Admin/HQ users -->
                <div class="md:col-span-6"></div>
                @endif
                
                <!-- Status -->
                <div class="md:col-span-6">
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
            </div>
            
            <!-- Buttons Row -->
            <div class="flex items-center justify-between">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    Showing {{ $submissions->firstItem() ?? 0 }} to {{ $submissions->lastItem() ?? 0 }} of {{ $submissions->total() }} results
                </div>
                <div class="flex items-center space-x-2">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-lg transition-colors">
                        <i class='bx bx-x mr-2'></i>
                        Clear
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition-colors">
                        <i class='bx bx-search mr-2'></i>
                        Search
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Form Submissions Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Form Submissions</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Latest form submissions</p>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Form</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Branch</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($submissions as $submission)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            #{{ $submission->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $submission->form->name ?? 'N/A' }}
                            @if($submission->form)
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ strtoupper($submission->form->slug) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($submission->user)
                                <div>{{ $submission->user->first_name }} {{ $submission->user->last_name }}</div>
                                <div class="text-xs text-gray-400">{{ $submission->user->email }}</div>
                            @else
                                <span class="text-gray-400">Guest</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($submission->branch)
                                <div>{{ $submission->branch->name }}</div>
                                @if($submission->branch->ti_agent_code)
                                    <div class="text-xs text-gray-400">{{ $submission->branch->ti_agent_code }}</div>
                                @endif
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($submission->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                @elseif($submission->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                @elseif(in_array($submission->status, ['submitted', 'under_review'])) bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                @elseif($submission->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                @elseif($submission->status === 'completed') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400
                                @elseif($submission->status === 'pending_process') bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                @endif">
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
                            @if($submission->form)
                                <button onclick="openSubmissionModal('{{ $submission->form->slug }}', {{ $submission->id }})" 
                                        class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 rounded-lg transition-colors">
                                    <i class='bx bx-show mr-1'></i>
                                    View
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-3">
                                    <i class='bx bx-clipboard text-2xl text-gray-400 dark:text-gray-500'></i>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No Submissions Found</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">No form submissions have been made yet</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($submissions->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $submissions->links() }}
    </div>
    @endif
</div>

<!-- Submission Details Modal -->
<div id="submissionModal" class="fixed inset-0 hidden z-50 flex items-center justify-center p-2 sm:p-4">
    <div class="fixed inset-0 bg-black bg-opacity-0 transition-opacity duration-300" id="modalBackdrop"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0" id="modalContent">
        <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Submission Details</h3>
                <button onclick="closeSubmissionModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class='bx bx-x text-2xl'></i>
                </button>
            </div>
            <div id="submissionModalContent">
                <!-- Submission details will be loaded here -->
                <div class="flex items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openSubmissionModal(formSlug, submissionId) {
        const modal = document.getElementById('submissionModal');
        const content = document.getElementById('submissionModalContent');
        const backdrop = document.getElementById('modalBackdrop');
        const modalContent = document.getElementById('modalContent');
        
        // Show modal with animation
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.remove('bg-opacity-0');
            backdrop.classList.add('bg-opacity-50');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
        
        // Show loading
        content.innerHTML = '<div class="flex items-center justify-center py-12"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div></div>';
        
        // Fetch submission details
        fetch(`/admin/submissions/${formSlug}/${submissionId}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    content.innerHTML = data.html;
                } else {
                    content.innerHTML = '<div class="text-center py-12 text-red-600 dark:text-red-400">Error loading submission details</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                content.innerHTML = '<div class="text-center py-12 text-red-600 dark:text-red-400">Error loading submission details</div>';
            });
    }
    
    function closeSubmissionModal() {
        const modal = document.getElementById('submissionModal');
        const backdrop = document.getElementById('modalBackdrop');
        const modalContent = document.getElementById('modalContent');
        
        backdrop.classList.remove('bg-opacity-50');
        backdrop.classList.add('bg-opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }
    
    // Close modal when clicking outside
    document.getElementById('modalBackdrop')?.addEventListener('click', function() {
        closeSubmissionModal();
    });
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('submissionModal');
            if (!modal.classList.contains('hidden')) {
                closeSubmissionModal();
            }
        }
    });
</script>
@endpush
@endsection
