@extends('layouts.admin-minimal')

@section('title', 'Project Information - BMMB Digital Forms')
@section('page-title', 'Project Information')
@section('page-description', 'System and project overview')

@section('content')
<!-- System Information -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">System Information</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Application and server details</p>
        </div>
        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
            <i class='bx bx-server text-lg text-blue-600 dark:text-blue-400'></i>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">PHP Version</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $systemInfo['php_version'] }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Laravel Version</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $systemInfo['laravel_version'] }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Database Driver</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ strtoupper($systemInfo['database_driver']) }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Database Name</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $systemInfo['database_name'] }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Server OS</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $systemInfo['server_os'] }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Timezone</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $systemInfo['timezone'] }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Environment</p>
            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full
                @if($systemInfo['environment'] === 'production') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                @endif">
                {{ strtoupper($systemInfo['environment']) }}
            </span>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Debug Mode</p>
            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full
                @if($systemInfo['debug_mode'] === 'Enabled') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                @else bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                @endif">
                {{ $systemInfo['debug_mode'] }}
            </span>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Memory Limit</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $systemInfo['memory_limit'] }}</p>
        </div>
    </div>
</div>

<!-- Project Statistics -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Forms</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $projectStats['total_forms'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $projectStats['active_forms'] }} active</p>
            </div>
            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-edit-alt text-base text-primary-600 dark:text-primary-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Form Fields</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $projectStats['total_form_fields'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $projectStats['total_form_sections'] }} sections</p>
            </div>
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-list-ul text-base text-purple-600 dark:text-purple-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Submissions</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $projectStats['total_submissions'] }}</p>
                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1 font-medium">{{ $projectStats['pending_submissions'] }} pending</p>
            </div>
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-clipboard text-base text-green-600 dark:text-green-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Users</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $projectStats['total_users'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $projectStats['active_users'] }} active</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-user text-base text-blue-600 dark:text-blue-400'></i>
            </div>
        </div>
    </div>
</div>

<!-- Additional Statistics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Branches</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $projectStats['total_branches'] }}</p>
            </div>
            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-building text-base text-indigo-600 dark:text-indigo-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">QR Codes</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $projectStats['total_qr_codes'] }}</p>
            </div>
            <div class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-qr-scan text-base text-cyan-600 dark:text-cyan-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Audit Trails</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $projectStats['total_audit_trails'] }}</p>
            </div>
            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-history text-base text-orange-600 dark:text-orange-400'></i>
            </div>
        </div>
    </div>
</div>

<!-- Database Information -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Database Information</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Database statistics and storage</p>
        </div>
        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
            <i class='bx bx-data text-lg text-green-600 dark:text-green-400'></i>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Total Tables</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $dbStats['total_tables'] }}</p>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Total Records</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($dbStats['total_records']) }}</p>
        </div>
        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Database Size</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $dbStats['database_size'] }}</p>
        </div>
    </div>
</div>

<!-- Form Type Breakdown -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Form Type Breakdown</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Statistics by form type</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 bg-gradient-to-br from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-lg border border-orange-100 dark:border-orange-800/30">
            <div class="flex items-center justify-between mb-2">
                <i class='bx bx-money text-xl text-orange-600 dark:text-orange-400'></i>
            </div>
            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">RAF</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $formTypeBreakdown['raf']['submissions'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">submissions</p>
        </div>
        
        <div class="p-4 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg border border-green-100 dark:border-green-800/30">
            <div class="flex items-center justify-between mb-2">
                <i class='bx bx-data text-xl text-green-600 dark:text-green-400'></i>
            </div>
            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">DAR</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $formTypeBreakdown['dar']['submissions'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">submissions</p>
        </div>
        
        <div class="p-4 bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-lg border border-purple-100 dark:border-purple-800/30">
            <div class="flex items-center justify-between mb-2">
                <i class='bx bx-edit text-xl text-purple-600 dark:text-purple-400'></i>
            </div>
            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">DCR</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $formTypeBreakdown['dcr']['submissions'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">submissions</p>
        </div>
        
        <div class="p-4 bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-lg border border-blue-100 dark:border-blue-800/30">
            <div class="flex items-center justify-between mb-2">
                <i class='bx bx-cog text-xl text-blue-600 dark:text-blue-400'></i>
            </div>
            <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">SRF</p>
            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $formTypeBreakdown['srf']['submissions'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">submissions</p>
        </div>
    </div>
</div>

<!-- Submission Status Breakdown -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Submission Status Breakdown</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Distribution of submission statuses</p>
        </div>
    </div>
    
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        @foreach($submissionStatusBreakdown as $status => $count)
            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">{{ ucfirst(str_replace('_', ' ', $status)) }}</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $count }}</p>
            </div>
        @endforeach
    </div>
</div>

<!-- Storage Information -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Storage Information</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Storage usage and paths</p>
        </div>
        <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
            <i class='bx bx-hdd text-lg text-yellow-600 dark:text-yellow-400'></i>
        </div>
    </div>
    
    <div class="space-y-3">
        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Storage Used</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $storageInfo['storage_used'] }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Storage Path</p>
            <p class="text-xs text-gray-600 dark:text-gray-400 font-mono break-all">{{ $storageInfo['storage_path'] }}</p>
        </div>
        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-1">Public Path</p>
            <p class="text-xs text-gray-600 dark:text-gray-400 font-mono break-all">{{ $storageInfo['public_path'] }}</p>
        </div>
    </div>
</div>

<!-- Recent Activity -->
@if($recentActivity->isNotEmpty())
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-100 dark:border-gray-700">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent Activity</h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Latest system activities</p>
        </div>
        <a href="{{ route('admin.audit-trails.index') }}" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
            View All
        </a>
    </div>
    
    <div class="space-y-3">
        @foreach($recentActivity as $activity)
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                        <i class='bx bx-history text-sm text-primary-600 dark:text-primary-400'></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ $activity->action_display }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            @if($activity->user)
                                {{ $activity->user->first_name }} {{ $activity->user->last_name }}
                            @else
                                System
                            @endif
                            @if($activity->description)
                                • {{ \Illuminate\Support\Str::limit($activity->description, 50) }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $activity->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
@endsection

