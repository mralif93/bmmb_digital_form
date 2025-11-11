<!-- HQ Dashboard: All Submissions Overview -->
<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Submissions</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_submissions'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                    All forms
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
                <p class="text-xl font-bold text-green-600 mt-1">{{ $stats['approved_submissions'] ?? 0 }}</p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-1.5 font-medium">
                    {{ $stats['conversion_rate'] ?? 0 }}% approval rate
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
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Pending Review</p>
                <p class="text-xl font-bold text-yellow-600 mt-1">{{ $stats['pending_submissions'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                    Awaiting action
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
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">In Progress</p>
                <p class="text-xl font-bold text-blue-600 mt-1">{{ $stats['in_progress_submissions'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                    Being processed
                </p>
            </div>
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center shadow-sm">
                <i class='bx bx-loader-circle text-base text-blue-600 dark:text-blue-400'></i>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Completed</p>
                <p class="text-2xl font-bold text-purple-600 mt-1">{{ $stats['completed_submissions'] ?? 0 }}</p>
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
                <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['rejected_submissions'] ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-x-circle text-base text-red-600 dark:text-red-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Conversion Rate</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['conversion_rate'] ?? 0 }}%</p>
            </div>
            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-trending-up text-base text-orange-600 dark:text-orange-400'></i>
            </div>
        </div>
    </div>
</div>

<!-- Form Type Breakdown -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Submissions by Form Type</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Overview of all form submissions</p>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-lg border border-orange-100 dark:border-orange-800/30">
            <div class="flex items-center justify-between mb-2">
                <i class='bx bx-money text-xl text-orange-600 dark:text-orange-400'></i>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $submissionCounts['raf'] ?? 0 }}</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">RAF</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Remittance Application</p>
        </div>
        <div class="p-4 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg border border-green-100 dark:border-green-800/30">
            <div class="flex items-center justify-between mb-2">
                <i class='bx bx-data text-xl text-green-600 dark:text-green-400'></i>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $submissionCounts['dar'] ?? 0 }}</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">DAR</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Data Access Request</p>
        </div>
        <div class="p-4 bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-lg border border-purple-100 dark:border-purple-800/30">
            <div class="flex items-center justify-between mb-2">
                <i class='bx bx-edit text-xl text-purple-600 dark:text-purple-400'></i>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $submissionCounts['dcr'] ?? 0 }}</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">DCR</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Data Correction Request</p>
        </div>
        <div class="p-4 bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-lg border border-blue-100 dark:border-blue-800/30">
            <div class="flex items-center justify-between mb-2">
                <i class='bx bx-cog text-xl text-blue-600 dark:text-blue-400'></i>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $submissionCounts['srf'] ?? 0 }}</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">SRF</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Service Request Form</p>
        </div>
    </div>
</div>

<!-- Top Performing Forms -->
@if($topForms->isNotEmpty())
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5 border border-gray-100 dark:border-gray-700 mb-6">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Top Performing Forms</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Forms with most submissions</p>
        </div>
    </div>
    <div class="space-y-3">
        @foreach($topForms as $index => $form)
            @php
                $formConfig = [
                    'raf' => ['icon' => 'bx-money', 'gradient' => 'from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20', 'border' => 'border-orange-100 dark:border-orange-800/30', 'bg' => 'from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30', 'iconColor' => 'text-orange-600 dark:text-orange-400', 'linkColor' => 'text-orange-600 dark:text-orange-400'],
                    'dar' => ['icon' => 'bx-data', 'gradient' => 'from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20', 'border' => 'border-green-100 dark:border-green-800/30', 'bg' => 'from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30', 'iconColor' => 'text-green-600 dark:text-green-400', 'linkColor' => 'text-green-600 dark:text-green-400'],
                    'dcr' => ['icon' => 'bx-edit', 'gradient' => 'from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20', 'border' => 'border-purple-100 dark:border-purple-800/30', 'bg' => 'from-purple-100 to-purple-200 dark:from-purple-900/30 dark:to-purple-800/30', 'iconColor' => 'text-purple-600 dark:text-purple-400', 'linkColor' => 'text-purple-600 dark:text-purple-400'],
                    'srf' => ['icon' => 'bx-cog', 'gradient' => 'from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20', 'border' => 'border-blue-100 dark:border-blue-800/30', 'bg' => 'from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30', 'iconColor' => 'text-blue-600 dark:text-blue-400', 'linkColor' => 'text-blue-600 dark:text-blue-400'],
                ];
                $config = $formConfig[$form->slug] ?? ['icon' => 'bx-file-blank', 'gradient' => 'from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20', 'border' => 'border-orange-100 dark:border-orange-800/30', 'bg' => 'from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30', 'iconColor' => 'text-orange-600 dark:text-orange-400', 'linkColor' => 'text-orange-600 dark:text-orange-400'];
            @endphp
            <div class="flex items-center justify-between p-4 bg-gradient-to-r {{ $config['gradient'] }} rounded-lg border {{ $config['border'] }}">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br {{ $config['bg'] }} rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx {{ $config['icon'] }} text-lg {{ $config['iconColor'] }}'></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $form->name }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">{{ $form->submissions_count }} {{ Str::plural('submission', $form->submissions_count) }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <a href="{{ route('admin.submissions.index', $form->slug) }}" class="text-xs {{ $config['linkColor'] }} hover:underline">
                        View Submissions
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<!-- Recent Submissions -->
@if($recentSubmissions->isNotEmpty())
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Submissions</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Latest form submissions across all branches</p>
        </div>
        <a href="{{ route('admin.submissions.index', 'raf') }}" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
            View All
        </a>
    </div>
    <div class="space-y-3">
        @foreach($recentSubmissions as $submission)
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                        <i class='bx bx-file text-sm text-primary-600 dark:text-primary-400'></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ $submission->form->name ?? 'N/A' }} #{{ $submission->id }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            @if($submission->user)
                                {{ $submission->user->first_name }} {{ $submission->user->last_name }}
                            @else
                                Guest
                            @endif
                            @if($submission->branch)
                                • {{ $submission->branch->name }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        @if($submission->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                        @elseif($submission->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                        @elseif(in_array($submission->status, ['submitted', 'under_review'])) bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                        @elseif($submission->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                        @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                    </span>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $submission->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

