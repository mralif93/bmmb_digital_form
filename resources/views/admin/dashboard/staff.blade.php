<!-- BM/ABM/OO Dashboard: My Submissions -->
<!-- Welcome Message -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Welcome back, {{ $user->first_name }}!</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Here's an overview of your form submissions</p>
        </div>
        <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
            <i class='bx bx-user text-xl text-primary-600 dark:text-primary-400'></i>
        </div>
    </div>
</div>

<!-- My Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">My Submissions</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['my_submissions'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                    Total submitted
                </p>
            </div>
            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center shadow-sm">
                <i class='bx bx-clipboard text-base text-primary-600 dark:text-primary-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Approved</p>
                <p class="text-xl font-bold text-green-600 mt-1">{{ $stats['my_approved'] ?? 0 }}</p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-1.5 font-medium">
                    {{ $stats['my_conversion_rate'] ?? 0 }}% success rate
                </p>
            </div>
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center shadow-sm">
                <i class='bx bx-check-circle text-base text-green-600 dark:text-green-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Pending</p>
                <p class="text-xl font-bold text-yellow-600 mt-1">{{ $stats['my_pending'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                    Awaiting review
                </p>
            </div>
            <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center shadow-sm">
                <i class='bx bx-time text-base text-yellow-600 dark:text-yellow-400'></i>
            </div>
        </div>
    </div>
</div>

<!-- Additional My Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">In Progress</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['my_in_progress'] ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-loader-circle text-base text-blue-600 dark:text-blue-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Completed</p>
                <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['my_completed'] ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-check-double text-base text-purple-600 dark:text-purple-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Rejected</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['my_rejected'] ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-x-circle text-base text-red-600 dark:text-red-400'></i>
            </div>
        </div>
    </div>
</div>

<!-- My Submissions by Form Type -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">My Submissions by Form Type</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Breakdown of your form submissions</p>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.submissions.index', 'raf') }}" class="p-4 bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-lg border border-orange-100 dark:border-orange-800/30 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <i class='bx bx-money text-xl text-orange-600 dark:text-orange-400'></i>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $submissionCounts['raf'] ?? 0 }}</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">RAF</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Remittance Application</p>
        </a>
        <a href="{{ route('admin.submissions.index', 'dar') }}" class="p-4 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg border border-green-100 dark:border-green-800/30 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <i class='bx bx-data text-xl text-green-600 dark:text-green-400'></i>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $submissionCounts['dar'] ?? 0 }}</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">DAR</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Data Access Request</p>
        </a>
        <a href="{{ route('admin.submissions.index', 'dcr') }}" class="p-4 bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-lg border border-purple-100 dark:border-purple-800/30 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <i class='bx bx-edit text-xl text-purple-600 dark:text-purple-400'></i>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $submissionCounts['dcr'] ?? 0 }}</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">DCR</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Data Correction Request</p>
        </a>
        <a href="{{ route('admin.submissions.index', 'srf') }}" class="p-4 bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-lg border border-blue-100 dark:border-blue-800/30 hover:shadow-md transition-all">
            <div class="flex items-center justify-between mb-2">
                <i class='bx bx-cog text-xl text-blue-600 dark:text-blue-400'></i>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $submissionCounts['srf'] ?? 0 }}</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">SRF</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Service Request Form</p>
        </a>
    </div>
</div>

<!-- My Recent Submissions -->
@if($mySubmissions->isNotEmpty())
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">My Recent Submissions</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Your latest form submissions</p>
        </div>
        <a href="{{ route('admin.submissions.index', 'raf') }}" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
            View All
        </a>
    </div>
    <div class="space-y-3">
        @foreach($mySubmissions as $submission)
            <a href="{{ route('admin.submissions.show', [$submission->form->slug, $submission->id]) }}" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                        <i class='bx bx-file text-sm text-primary-600 dark:text-primary-400'></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ $submission->form->name ?? 'N/A' }} #{{ $submission->id }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            @if($submission->branch)
                                {{ $submission->branch->name }}
                            @else
                                No branch assigned
                            @endif
                            • {{ $submission->created_at->format('M d, Y') }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        @if($submission->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                        @elseif($submission->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                        @elseif(in_array($submission->status, ['submitted', 'under_review'])) bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                        @elseif($submission->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                        @elseif($submission->status === 'completed') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400
                        @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                    </span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $submission->created_at->diffForHumans() }}
                    </p>
                </div>
            </a>
        @endforeach
    </div>
</div>
@else
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700">
    <div class="text-center py-8">
        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-3">
            <i class='bx bx-clipboard text-2xl text-gray-400 dark:text-gray-500'></i>
        </div>
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No Submissions Yet</h4>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">You haven't submitted any forms yet</p>
        <div class="flex items-center justify-center space-x-2">
            @php
                $forms = \App\Models\Form::where('status', 'active')->orderBy('sort_order')->get();
            @endphp
            @foreach($forms->take(2) as $form)
                <a href="{{ route('admin.submissions.index', $form->slug) }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    <i class='bx bx-plus mr-1.5'></i>
                    View {{ strtoupper($form->slug) }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif

