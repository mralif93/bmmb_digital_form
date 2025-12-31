@php
    $timezoneHelper = app(\App\Helpers\TimezoneHelper::class);
@endphp

<!-- User Information -->
<div class="space-y-4">
    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
            <i class='bx bx-info-circle mr-2 text-primary-600 dark:text-primary-400'></i>
            User Information
        </h3>
        <dl class="space-y-3">
            <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    First Name
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white font-semibold flex-1 text-left">
                    {{ $user->first_name }}
                </dd>
            </div>
            <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    Last Name
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white font-semibold flex-1 text-left">
                    {{ $user->last_name }}
                </dd>
            </div>

            <!-- Staff ID & MAP ID -->
            <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    Staff ID
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                    {{ $user->username ?: 'Not provided' }}
                </dd>
            </div>
            @if($user->map_staff_id)
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt
                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        MAP Profile ID
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $user->map_staff_id }}
                    </dd>
                </div>
            @endif
            <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    Email
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                    <a href="mailto:{{ $user->email }}" class="text-primary-600 dark:text-primary-400 hover:underline">
                        {{ $user->email }}
                    </a>
                </dd>
            </div>
            <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    Phone
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                    {{ $user->phone ?: 'Not provided' }}
                </dd>
            </div>
            <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
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
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    Status
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                    @if($user->status === 'active')
                        <div class="flex flex-col">
                            <span class="inline-flex items-center text-green-600 dark:text-green-400 font-medium">
                                <i class='bx bx-check-circle mr-1.5'></i>
                                Active
                            </span>
                        </div>
                    @elseif($user->status === 'inactive')
                        <span class="inline-flex items-center text-red-600 dark:text-red-400">
                            <i class='bx bx-x-circle mr-1.5'></i>
                            Inactive
                        </span>
                    @else
                        <span class="inline-flex items-center text-red-600 dark:text-red-400">
                            <i class='bx bx-x-circle mr-1.5'></i>
                            {{ ucfirst($user->status) }}
                        </span>
                    @endif
                </dd>
            </div>
            <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
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
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    Email Verified
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                    @if($user->email_verified_at)
                        <div class="flex flex-col">
                            <span class="inline-flex items-center text-green-600 dark:text-green-400 font-medium">
                                <i class='bx bx-check-circle mr-1.5'></i>
                                Verified
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ \App\Helpers\TimezoneHelper::toSystemTimezone($user->email_verified_at)->format($dateFormat . ' ' . $timeFormat) }}
                            </span>
                        </div>
                    @else
                        <span class="inline-flex items-center text-red-600 dark:text-red-400">
                            <i class='bx bx-x-circle mr-1.5'></i>
                            Not verified
                        </span>
                    @endif
                </dd>
            </div>
            <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    Last Login
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                    {{ $user->last_login_at ? \App\Helpers\TimezoneHelper::toSystemTimezone($user->last_login_at)->format($dateFormat . ' ' . $timeFormat) : 'Never' }}
                </dd>
            </div>
            <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    Created At
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                    {{ \App\Helpers\TimezoneHelper::toSystemTimezone($user->created_at)->format($dateFormat . ' ' . $timeFormat) }}
                </dd>
            </div>
            <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                <dt
                    class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                    Updated At
                </dt>
                <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                    {{ \App\Helpers\TimezoneHelper::toSystemTimezone($user->updated_at)->format($dateFormat . ' ' . $timeFormat) }}
                </dd>
            </div>
            @if($user->bio)
                <div class="flex items-start border-b border-gray-200 dark:border-gray-700 pb-3 last:border-0 gap-4">
                    <dt
                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider flex-shrink-0 w-1/3">
                        Bio
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white flex-1 text-left">
                        {{ $user->bio }}
                    </dd>
                </div>
            @endif
        </dl>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-end space-x-2 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button type="button" onclick="handleResetPassword({{ $user->id }})"
            class="inline-flex items-center justify-center px-4 py-2 bg-purple-100 hover:bg-purple-200 text-purple-700 dark:bg-purple-900/30 dark:hover:bg-purple-900/50 dark:text-purple-400 rounded-lg text-xs font-semibold transition-colors">
            Reset Password
        </button>
        @if(!$user->email_verified_at)
            <button type="button" onclick="handleVerifyEmail({{ $user->id }})"
                class="inline-flex items-center justify-center px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 dark:bg-green-900/30 dark:hover:bg-green-900/50 dark:text-green-400 rounded-lg text-xs font-semibold transition-colors">
                Verify Email
            </button>
        @else
            <button type="button" onclick="handleUnverifyEmail({{ $user->id }})"
                class="inline-flex items-center justify-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg text-xs font-semibold transition-colors">
                Unverify Email
            </button>
        @endif
        <form id="toggleStatusForm" action="{{ route('admin.users.toggle-status', $user) }}" method="POST"
            class="inline">
            @csrf
            @method('PATCH')
            <button type="button" onclick="handleToggleStatus({{ $user->id }}, '{{ $user->status }}')" class="inline-flex items-center justify-center px-4 py-2 rounded-lg text-xs font-semibold transition-colors
                    @if($user->status === 'active')
                        bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-900/30 dark:hover:bg-gray-900/50 dark:text-gray-400
                    @else
                        bg-green-100 hover:bg-green-200 text-green-700 dark:bg-green-900/30 dark:hover:bg-green-900/50 dark:text-green-400
                    @endif">
                {{ $user->status === 'active' ? 'Set Inactive' : 'Set Active' }}
            </button>
        </form>
    </div>
</div>