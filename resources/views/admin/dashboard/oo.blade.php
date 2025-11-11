<!-- OO Dashboard: Operations Officer Dashboard -->
<!-- Branch Information -->
@if($user->branch)
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->branch->name }}</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Operations Officer Dashboard</p>
        </div>
        <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
            <i class='bx bx-cog text-xl text-primary-600 dark:text-primary-400'></i>
        </div>
    </div>
</div>
@endif

<!-- OO Action Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Available to Take Up</p>
                <p class="text-xl font-bold text-cyan-600 mt-1">{{ $stats['available_to_take_up'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                    Submitted status
                </p>
            </div>
            <div class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg flex items-center justify-center shadow-sm">
                <i class='bx bx-check text-base text-cyan-600 dark:text-cyan-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Pending Process</p>
                <p class="text-xl font-bold text-yellow-600 mt-1">{{ $stats['pending_process'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                    Ready to complete
                </p>
            </div>
            <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center shadow-sm">
                <i class='bx bx-time text-base text-yellow-600 dark:text-yellow-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Completed by Me</p>
                <p class="text-xl font-bold text-green-600 mt-1">{{ $stats['completed_by_me'] ?? 0 }}</p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-1.5 font-medium">
                    This month
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
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Taken Up by Me</p>
                <p class="text-xl font-bold text-blue-600 mt-1">{{ $stats['taken_up_by_me'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                    In progress
                </p>
            </div>
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center shadow-sm">
                <i class='bx bx-loader-circle text-base text-blue-600 dark:text-blue-400'></i>
            </div>
        </div>
    </div>
</div>

<!-- Additional OO Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Branch Submissions</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['branch_submissions'] ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-clipboard text-base text-primary-600 dark:text-primary-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">My Completion Rate</p>
                <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['completion_rate'] ?? 0 }}%</p>
            </div>
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-trending-up text-base text-purple-600 dark:text-purple-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Completed</p>
                <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['total_completed'] ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-check-double text-base text-green-600 dark:text-green-400'></i>
            </div>
        </div>
    </div>
</div>

<!-- Available Submissions to Take Up -->
@if($availableSubmissions->isNotEmpty())
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Available to Take Up</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Submissions ready for processing</p>
        </div>
        <a href="{{ route('admin.submissions.index', 'raf') }}" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
            View All
        </a>
    </div>
    <div class="space-y-3">
        @foreach($availableSubmissions as $submission)
            <a href="{{ route('admin.submissions.show', [$submission->form->slug, $submission->id]) }}" class="flex items-center justify-between p-3 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg border border-cyan-100 dark:border-cyan-800/30 hover:bg-cyan-100 dark:hover:bg-cyan-900/30 transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg flex items-center justify-center">
                        <i class='bx bx-check text-sm text-cyan-600 dark:text-cyan-400'></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ $submission->form->name ?? 'N/A' }} #{{ $submission->id }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            @if($submission->user)
                                {{ $submission->user->first_name }} {{ $submission->user->last_name }}
                            @else
                                Guest
                            @endif
                            • {{ $submission->created_at->format('M d, Y') }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400">
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
@endif

<!-- Pending Process Submissions (Ready to Complete) -->
@if($pendingProcessSubmissions->isNotEmpty())
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pending Process (Ready to Complete)</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Submissions taken up and ready for completion</p>
        </div>
        <a href="{{ route('admin.submissions.index', 'raf') }}" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
            View All
        </a>
    </div>
    <div class="space-y-3">
        @foreach($pendingProcessSubmissions as $submission)
            <a href="{{ route('admin.submissions.show', [$submission->form->slug, $submission->id]) }}" class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-100 dark:border-yellow-800/30 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                        <i class='bx bx-time text-sm text-yellow-600 dark:text-yellow-400'></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ $submission->form->name ?? 'N/A' }} #{{ $submission->id }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            @if($submission->takenUpBy)
                                Taken up by: {{ $submission->takenUpBy->first_name }} {{ $submission->takenUpBy->last_name }}
                            @endif
                            @if($submission->taken_up_at)
                                • {{ $submission->taken_up_at->format('M d, Y') }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                        {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                    </span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        @if($submission->taken_up_at)
                            {{ $submission->taken_up_at->diffForHumans() }}
                        @endif
                    </p>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif

<!-- My Recent Completions -->
@if($myCompletions->isNotEmpty())
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">My Recent Completions</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Submissions you've completed</p>
        </div>
        <a href="{{ route('admin.submissions.index', 'raf') }}" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
            View All
        </a>
    </div>
    <div class="space-y-3">
        @foreach($myCompletions as $submission)
            <a href="{{ route('admin.submissions.show', [$submission->form->slug, $submission->id]) }}" class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-100 dark:border-green-800/30 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i class='bx bx-check-circle text-sm text-green-600 dark:text-green-400'></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ $submission->form->name ?? 'N/A' }} #{{ $submission->id }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            @if($submission->completed_at)
                                Completed: {{ $submission->completed_at->format('M d, Y') }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                        Completed
                    </span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        @if($submission->completed_at)
                            {{ $submission->completed_at->diffForHumans() }}
                        @endif
                    </p>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif

<!-- Branch Submissions by Form Type -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Branch Submissions by Form Type</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Overview of submissions from your branch</p>
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

