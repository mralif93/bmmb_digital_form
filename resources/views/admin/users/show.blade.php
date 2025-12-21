@extends('layouts.admin-minimal')

@section('title', 'User Details - BMMB Digital Forms')
@section('page-title', 'User Details: ' . $user->full_name)
@section('page-description', 'View user information')

@section('content')
<div class="mb-4 flex items-center justify-end">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
        <i class='bx bx-arrow-back mr-1.5'></i>
        Back to List
    </a>
</div>

@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

<div class="w-full">
    <!-- Main Details -->
    <div class="space-y-6">
        <!-- User Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class='bx bx-info-circle mr-2 text-primary-600 dark:text-primary-400'></i>
                User Information
            </h3>
            <dl class="space-y-3">
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        First Name
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white font-semibold flex-1 text-left">
                        {{ $user->first_name }}
                    </dd>
                </div>
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Last Name
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white font-semibold flex-1 text-left">
                        {{ $user->last_name }}
                    </dd>
                </div>
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Email
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        <a href="mailto:{{ $user->email }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                            {{ $user->email }}
                        </a>
                    </dd>
                </div>
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Phone
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $user->phone ?: 'Not provided' }}
                    </dd>
                </div>
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Role
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($user->role === 'admin') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                            @elseif($user->role === 'branch_manager') bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400
                            @elseif($user->role === 'assistant_branch_manager') bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400
                            @elseif($user->role === 'operation_officer') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                            @elseif($user->role === 'headquarters') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                            @elseif($user->role === 'iam') bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400
                            @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400
                            @endif">
                            {{ $user->role_display }}
                        </span>
                    </dd>
                </div>
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Status
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($user->status === 'active') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                            @elseif($user->status === 'inactive') bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400
                            @else bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                            @endif">
                            {{ ucfirst($user->status) }}
                        </span>
                    </dd>
                </div>
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Branch
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        @if($user->branch)
                            {{ $user->branch->name }} ({{ $user->branch->code }})
                        @else
                            <span class="text-gray-400 dark:text-gray-500">No branch assigned</span>
                        @endif
                    </dd>
                </div>
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Email Verified
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        @if($user->email_verified_at)
                            <span class="text-green-600 dark:text-green-400">Verified</span>
                        @else
                            <span class="text-red-600 dark:text-red-400">Not verified</span>
                        @endif
                    </dd>
                </div>
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Last Login
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $user->last_login_at ? $timezoneHelper->convert($user->last_login_at)?->format('M d, Y h:i A') : 'Never' }}
                    </dd>
                </div>
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Created At
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $timezoneHelper->convert($user->created_at)?->format('M d, Y h:i A') }}
                    </dd>
                </div>
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Updated At
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $timezoneHelper->convert($user->updated_at)?->format('M d, Y h:i A') }}
                    </dd>
                </div>
                @if($user->bio)
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Bio
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $user->bio }}
                    </dd>
                </div>
                @endif
            </dl>
        </div>
    </div>
</div>
@endsection
