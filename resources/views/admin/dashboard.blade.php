@extends('layouts.admin-minimal')

@section('title', 'Dashboard - BMMB Digital Forms')
@section('page-title', 'Dashboard')
@section('page-description', 'Overview of your form management system')

@section('content')
    @if(session('success'))
        <div
            class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400 flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()"
                class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">
                <i class='bx bx-x text-lg'></i>
            </button>
        </div>
    @endif

    <!-- User Information Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
        <div class="flex items-center justify-between">
            <!-- Left Side: Avatar & Name -->
            <div class="flex items-center space-x-4">
                <div class="relative flex-shrink-0">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center shadow-md">
                        <i class='bx bx-user text-2xl text-white'></i>
                    </div>
                    <div
                        class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 rounded-full border-2 border-white dark:border-gray-800 shadow-sm">
                    </div>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ auth()->user()->role_display ?? ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                    </p>
                </div>
            </div>

            <!-- Right Side: Branch Info -->
            @if(auth()->user()->branch)
                <div class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class='bx bx-building text-lg text-blue-600 dark:text-blue-400'></i>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Branch</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->branch->name }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Dashboard Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @if(auth()->user()->isCFE() || auth()->user()->isOO())
            <!-- CFE/OO Stats -->
            <!-- Available to Take Up -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Available</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ $stats['available_to_take_up'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">To Take Up</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx bx-list-ul text-base text-gray-600 dark:text-gray-400'></i>
                    </div>
                </div>
            </div>

            <!-- Pending Process -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Processing
                        </p>
                        <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['pending_process'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">Pending Process</p>
                    </div>
                    <div
                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx bx-time-five text-base text-blue-600 dark:text-blue-400'></i>
                    </div>
                </div>
            </div>

            <!-- Taken Up By Me -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">My Processing
                        </p>
                        <p class="text-xl font-bold text-cyan-600 dark:text-cyan-400 mt-1">{{ $stats['taken_up_by_me'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">Taken Up By Me</p>
                    </div>
                    <div
                        class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx bx-user-check text-base text-cyan-600 dark:text-cyan-400'></i>
                    </div>
                </div>
            </div>

            <!-- Completed By Me -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">My
                            Completions</p>
                        <p class="text-xl font-bold text-green-600 dark:text-green-400 mt-1">
                            {{ $stats['completed_by_me'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">This Month</p>
                    </div>
                    <div
                        class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx bx-check-double text-base text-green-600 dark:text-green-400'></i>
                    </div>
                </div>
            </div>

            <!-- Branch Manager / Assistant Branch Manager / OO / CFE Section -->
        @elseif(auth()->user()->isBM() || auth()->user()->isABM() || auth()->user()->isOO() || auth()->user()->isCFE())
            <div class="col-span-full mb-6 flex justify-end">
                <a href="{{ route('branch.qr-display') }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-200 font-medium">
                    <i class='bx bx-qr-scan text-xl'></i>
                    <span>Show Counter Display</span>
                </a>
            </div>
            <!-- BM/ABM Stats -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total
                            Submissions</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['branch_submissions'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">Branch Total</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx bx-file-blank text-base text-gray-600 dark:text-gray-400'></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Pending</p>
                        <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">
                            {{ $stats['branch_pending'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">Under Review</p>
                    </div>
                    <div
                        class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx bx-time text-base text-yellow-600 dark:text-yellow-400'></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">In Progress
                        </p>
                        <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                            {{ $stats['branch_in_progress'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">Being Processed</p>
                    </div>
                    <div
                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx bx-loader-alt text-base text-blue-600 dark:text-blue-400'></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Completed</p>
                        <p class="text-xl font-bold text-green-600 dark:text-green-400 mt-1">
                            {{ $stats['branch_completed'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">Processed</p>
                    </div>
                    <div
                        class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx bx-check-circle text-base text-green-600 dark:text-green-400'></i>
                    </div>
                </div>
            </div>

        @else
            <!-- Admin/HQ Stats -->
            <!-- Total Forms Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Forms
                        </p>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_forms'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                            All forms
                        </p>
                    </div>
                    <div
                        class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx bx-file-blank text-base text-primary-600 dark:text-primary-400'></i>
                    </div>
                </div>
            </div>

            <!-- Total Active Forms Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Active
                            Forms</p>
                        <p class="text-xl font-bold text-green-600 dark:text-green-400 mt-1">
                            {{ $stats['total_active_forms'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                            Currently active
                        </p>
                    </div>
                    <div
                        class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx bx-check-circle text-base text-green-600 dark:text-green-400'></i>
                    </div>
                </div>
            </div>

            <!-- Total Submissions Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total
                            Submissions</p>
                        <p class="text-xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                            {{ $stats['total_form_submissions'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                            All submissions
                        </p>
                    </div>
                    <div
                        class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx bx-clipboard text-base text-blue-600 dark:text-blue-400'></i>
                    </div>
                </div>
            </div>

            <!-- Total Completed Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total
                            Completed</p>
                        <p class="text-xl font-bold text-purple-600 dark:text-purple-400 mt-1">
                            {{ $stats['total_completed_submissions'] ?? 0 }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
                            Completed submissions
                        </p>
                    </div>
                    <div
                        class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx bx-check-double text-base text-purple-600 dark:text-purple-400'></i>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if(isset($availableSubmissions) && (auth()->user()->isCFE() || auth()->user()->isOO() || auth()->user()->isBM() || auth()->user()->isABM()))
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Available to Take Up -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 dark:text-white">Available to Take Up</h3>
                    <span
                        class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">{{ $availableSubmissions->count() }}
                        new</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-900 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">REF</th>
                                <th class="px-4 py-3">Form</th>
                                <th class="px-4 py-3">Applicant</th>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($availableSubmissions as $submission)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-medium">{{ $submission->reference_number ?? '#' . $submission->id }}
                                    </td>
                                    <td class="px-4 py-3">{{ $submission->form->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        @if($submission->user)
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $submission->user->first_name }}
                                                {{ $submission->user->last_name }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $submission->user->email }}</div>
                                        @else
                                            @php
                                                $data = $submission->submission_data ?? [];
                                                $name = $data['name'] ?? $data['full_name'] ?? $data['customer_name'] ?? $data['applicant_name'] ?? null;
                                                $id = $data['ic_no'] ?? $data['nric'] ?? $data['customer_id'] ?? $data['business_reg_no'] ?? null;
                                            @endphp
                                            @if($name)
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $name }}</div>
                                                @if($id)
                                                <div class="text-xs text-gray-500">{{ $id }}</div> @endif
                                            @elseif($id)
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $id }}</div>
                                            @else
                                                <span class="text-gray-400 italic">Guest</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">{{ $submission->created_at->format('d M H:i') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <form
                                            action="{{ route('admin.submissions.take-up', [$submission->form->slug, $submission->id]) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs">
                                                Take Up
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No new submissions available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- My Processing -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 dark:text-white">My Processing</h3>
                    <span
                        class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $pendingProcessSubmissions->count() }}
                        pending</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 dark:bg-gray-900 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="px-4 py-3">REF</th>
                                <th class="px-4 py-3">Form</th>
                                <th class="px-4 py-3">Applicant</th>
                                <th class="px-4 py-3">Taken At</th>
                                <th class="px-4 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($pendingProcessSubmissions as $submission)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-3 font-medium">{{ $submission->reference_number ?? '#' . $submission->id }}
                                    </td>
                                    <td class="px-4 py-3">{{ $submission->form->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        @if($submission->user)
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $submission->user->first_name }}
                                                {{ $submission->user->last_name }}
                                            </div>
                                            <div class="text-xs text-gray-500">{{ $submission->user->email }}</div>
                                        @else
                                            @php
                                                $data = $submission->submission_data ?? [];
                                                $name = $data['name'] ?? $data['full_name'] ?? $data['customer_name'] ?? $data['applicant_name'] ?? null;
                                                $id = $data['ic_no'] ?? $data['nric'] ?? $data['customer_id'] ?? $data['business_reg_no'] ?? null;
                                            @endphp
                                            @if($name)
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $name }}</div>
                                                @if($id)
                                                <div class="text-xs text-gray-500">{{ $id }}</div> @endif
                                            @elseif($id)
                                                <div class="font-medium text-gray-900 dark:text-white">{{ $id }}</div>
                                            @else
                                                <span class="text-gray-400 italic">Guest</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">
                                        {{ $submission->taken_up_at ? $submission->taken_up_at->format('d M H:i') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button onclick="openCompleteModal('{{ $submission->form->slug }}', {{ $submission->id }})"
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">
                                            Complete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No submissions in progress.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div id="searchFilterSection"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Search & Filter</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">
                        @if(auth()->user()->isIAM())
                            Filter users
                        @else
                            Filter form submissions
                        @endif
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.dashboard') }}" class="space-y-3">
                @if(auth()->user()->isIAM())
                    <!-- IAM User Filters: Search Box, Role -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <!-- Search Box -->
                        <div class="md:col-span-6">
                            <label for="search"
                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                            <div class="relative">
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    placeholder="Search by name, email, phone..."
                                    class="w-full pl-10 pr-4 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <i
                                    class='bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500'></i>
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="md:col-span-6">
                            <label for="role"
                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                            <select name="role" id="role"
                                class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="branch_manager" {{ request('role') === 'branch_manager' ? 'selected' : '' }}>Branch
                                    Manager</option>
                                <option value="assistant_branch_manager" {{ request('role') === 'assistant_branch_manager' ? 'selected' : '' }}>Assistant Branch Manager</option>
                                <option value="operation_officer" {{ request('role') === 'operation_officer' ? 'selected' : '' }}>
                                    Operations Officer</option>
                                <option value="headquarters" {{ request('role') === 'headquarters' ? 'selected' : '' }}>
                                    Headquarters</option>
                                <option value="iam" {{ request('role') === 'iam' ? 'selected' : '' }}>Identity & Access Management
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Branch, Status -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <!-- Branch -->
                        <div class="md:col-span-6">
                            <label for="branch_id"
                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                            <select name="branch_id" id="branch_id"
                                class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">All Branches</option>
                                @foreach($branches ?? [] as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="md:col-span-6">
                            <label for="status"
                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" id="status"
                                class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Buttons Row -->
                    <div class="flex items-center justify-between">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }}
                            results
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
                @else
                    <!-- Non-IAM User Filters: Search Box, Forms -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <!-- Search Box -->
                        <div class="md:col-span-6">
                            <label for="search"
                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                            <div class="relative">
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    placeholder="Search by ID, name, email, branch..."
                                    class="w-full pl-10 pr-4 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <i
                                    class='bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500'></i>
                            </div>
                        </div>

                        <!-- Forms -->
                        <div class="md:col-span-6">
                            <label for="form_id"
                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Form</label>
                            <select name="form_id" id="form_id"
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
                                <label for="branch_id"
                                    class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                                <select name="branch_id" id="branch_id"
                                    class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">All Branches</option>
                                    @foreach($branches ?? [] as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Status -->
                        <div
                            class="{{ (auth()->user()->isAdmin() || auth()->user()->isHQ()) ? 'md:col-span-6' : 'md:col-span-12' }}">
                            <label for="status"
                                class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" id="status"
                                class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted
                                </option>
                                <option value="pending_process" {{ request('status') === 'pending_process' ? 'selected' : '' }}>
                                    Pending Process</option>
                                <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>Under
                                    Review</option>
                                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In
                                    Progress</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved
                                </option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected
                                </option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed
                                </option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Buttons Row -->
                    <div class="flex items-center justify-between">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Showing {{ $submissions->firstItem() ?? 0 }} to {{ $submissions->lastItem() ?? 0 }} of
                            {{ $submissions->total() }} results
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
                @endif
            </form>
        </div>
    </div>

    @if(auth()->user()->isIAM())
        <!-- Users Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Users</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">All system users</p>
                    </div>
                    <button onclick="openUserCreateModal()"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition-colors">
                        <i class='bx bx-plus mr-1.5'></i>
                        Create New
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                User</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Role</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Branch</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Last Login</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $user->first_name }} {{ $user->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $user->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($user->branch)
                                        <div>{{ $user->branch->name }}</div>
                                        @if($user->branch->ti_agent_code)
                                            <div class="text-xs text-gray-400">{{ $user->branch->ti_agent_code }}</div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full 
                                                                                                                                            @if($user->status === 'active') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                                                                                                                            @elseif($user->status === 'inactive') bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                                                                                                                            @elseif($user->status === 'suspended') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                                                                                                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                                                                                                                            @endif">
                                        {{ ucfirst($user->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($user->last_login_at)
                                        <div>{{ $user->last_login_at->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-400">{{ $user->last_login_at->format('h:i A') }}</div>
                                    @else
                                        <span class="text-gray-400">Never</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                                    <button onclick="openUserViewModal({{ $user->id }})"
                                        class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 rounded-lg transition-colors mr-2">
                                        <i class='bx bx-show mr-1'></i>
                                        View
                                    </button>
                                    <button onclick="openUserEditModal({{ $user->id }})"
                                        class="inline-flex items-center justify-center px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 dark:bg-yellow-900/30 dark:hover:bg-yellow-900/50 dark:text-yellow-400 rounded-lg transition-colors">
                                        <i class='bx bx-edit mr-1'></i>
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-3">
                                            <i class='bx bx-user text-2xl text-gray-400 dark:text-gray-500'></i>
                                        </div>
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No Users Found</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">No users match your search criteria</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    @else
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
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                ID</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Form</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                User</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Branch</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Date</th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Actions</th>
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
                                        <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                                            {{ strtoupper($submission->form->slug) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($submission->user)
                                        <div>{{ $submission->user->first_name }} {{ $submission->user->last_name }}</div>
                                        <div class="text-xs text-gray-400">{{ $submission->user->email }}</div>
                                    @else
                                        @php
                                            $data = $submission->submission_data ?? [];
                                            $name = $data['name'] ?? $data['full_name'] ?? $data['customer_name'] ?? $data['applicant_name'] ?? null;
                                            $id = $data['ic_no'] ?? $data['nric'] ?? $data['customer_id'] ?? $data['business_reg_no'] ?? null;
                                        @endphp
                                        @if($name)
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $name }}</div>
                                            @if($id)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $id }}</div> @endif
                                        @elseif($id)
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $id }}</div>
                                            <span class="text-gray-400 text-xs">Guest</span>
                                        @else
                                            <span class="text-gray-400">Guest</span>
                                        @endif
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
                                    <span
                                        class="px-2 py-1 text-xs font-semibold rounded-full 
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
                                        <div
                                            class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-3">
                                            <i class='bx bx-clipboard text-2xl text-gray-400 dark:text-gray-500'></i>
                                        </div>
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No Submissions Found
                                        </h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">No form submissions have been made yet
                                        </p>
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
    @endif

    <!-- Submission Details Modal -->
    <div id="submissionModal" class="fixed inset-0 hidden z-50 flex items-center justify-center p-2 sm:p-4">
        <div class="fixed inset-0 bg-black bg-opacity-0 transition-opacity duration-300" id="modalBackdrop"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0"
            id="modalContent">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Submission Details</h3>
                    <button onclick="closeSubmissionModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
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

    @if(auth()->user()->isIAM())
        <!-- User View Modal -->
        <div id="userViewModal" class="fixed inset-0 hidden z-50 flex items-center justify-center p-2 sm:p-4">
            <div class="fixed inset-0 bg-black bg-opacity-0 transition-opacity duration-300" id="userViewModalBackdrop"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0"
                id="userViewModalContentWrapper">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Details</h3>
                        <button onclick="closeUserViewModal()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>
                    <div id="userViewModalContent">
                        <!-- User details will be loaded here -->
                        <div class="flex items-center justify-center py-12">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Edit Modal -->
        <div id="userEditModal" class="fixed inset-0 hidden z-50 flex items-center justify-center p-2 sm:p-4">
            <div class="fixed inset-0 bg-black bg-opacity-0 transition-opacity duration-300" id="userEditModalBackdrop"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0"
                id="userEditModalContentWrapper">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit User</h3>
                        <button onclick="closeUserEditModal()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>
                    <div id="userEditModalContent">
                        <!-- User edit form will be loaded here -->
                        <div class="flex items-center justify-center py-12">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Create Modal -->
        <div id="userCreateModal" class="fixed inset-0 hidden z-50 flex items-center justify-center p-2 sm:p-4">
            <div class="fixed inset-0 bg-black bg-opacity-0 transition-opacity duration-300" id="userCreateModalBackdrop"></div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0"
                id="userCreateModalContentWrapper">
                <div class="p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create New User</h3>
                        <button onclick="closeUserCreateModal()"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class='bx bx-x text-2xl'></i>
                        </button>
                    </div>
                    <div id="userCreateModalContent">
                        <!-- User create form will be loaded here -->
                        <div class="flex items-center justify-center py-12">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Success Notification Toast -->
    <div id="successToast" class="fixed top-4 right-4 z-[100] transform translate-x-full transition-transform duration-300">
        <div
            class="bg-green-500 dark:bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3 min-w-[300px]">
            <div class="flex-shrink-0">
                <i class='bx bx-check-circle text-2xl'></i>
            </div>
            <div class="flex-1">
                <p class="font-semibold" id="successToastTitle">Success!</p>
                <p class="text-sm text-green-100" id="successToastMessage">Operation completed successfully.</p>
            </div>
            <button onclick="hideSuccessToast()" class="flex-shrink-0 text-white hover:text-green-100">
                <i class='bx bx-x text-xl'></i>
            </button>
        </div>
    </div>

    @push('scripts')
        <script>
               // Success Toast Functions
                    function showSuccessToast(title, message) {
                        const toast = document.getElementById('successToast');
                        const toastTitle = document.getElementById('successToastTitle');
                        const toastMessage = document.getElementById('successToastMessage');

                        toastTitle.textContent = title;
                        toastMessage.textContent = message;

                        toast.classList.remove('translate-x-full');
                        toast.classList.add('translate-x-0');

                        // Auto hide after 3 seconds
                        setTimeout(() => {
                            hideSuccessToast();
                        }, 3000);
                    }

                    function hideSuccessToast() {
                        const toast = document.getElementById('successToast');
                        toast.classList.remove('translate-x-0');
                        toast.classList.add('translate-x-full');
                    }
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
                        fetch(`{{ url('/admin/submissions') }}/${formSlug}/${submissionId}/details`)
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
                    document.getElementById('modalBackdrop')?.addEventListener('click', function () {
                        closeSubmissionModal();
                    });

                    // Close modal on Escape key
                    document.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape') {
                            const submissionModal = document.getElementById('submissionModal');
                            const userViewModal = document.getElementById('userViewModal');
                            const userEditModal = document.getElementById('userEditModal');
                            const userCreateModal = document.getElementById('userCreateModal');

                            if (submissionModal && !submissionModal.classList.contains('hidden')) {
                                closeSubmissionModal();
                            } else if (userViewModal && !userViewModal.classList.contains('hidden')) {
                                closeUserViewModal();
                            } else if (userEditModal && !userEditModal.classList.contains('hidden')) {
                                closeUserEditModal();
                            } else if (userCreateModal && !userCreateModal.classList.contains('hidden')) {
                                closeUserCreateModal();
                            }
                        }
                    });

                    @if(auth()->user()->isIAM())
                        // User View Modal Functions
                        function openUserViewModal(userId) {
                            const modal = document.getElementById('userViewModal');
                            const content = document.getElementById('userViewModalContent');
                            const backdrop = document.getElementById('userViewModalBackdrop');
                            const modalContent = document.getElementById('userViewModalContentWrapper');

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

                            // Fetch user details
                            fetch(`{{ route('admin.users.details', ':id') }}`.replace(':id', userId))
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        content.innerHTML = data.html;
                                    } else {
                                        content.innerHTML = '<div class="text-center py-12 text-red-600 dark:text-red-400">Error loading user details</div>';
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    content.innerHTML = '<div class="text-center py-12 text-red-600 dark:text-red-400">Error loading user details</div>';
                                });
                        }

                        function closeUserViewModal() {
                            const modal = document.getElementById('userViewModal');
                            const backdrop = document.getElementById('userViewModalBackdrop');
                            const modalContent = document.getElementById('userViewModalContentWrapper');

                            backdrop.classList.remove('bg-opacity-50');
                            backdrop.classList.add('bg-opacity-0');
                            modalContent.classList.remove('scale-100', 'opacity-100');
                            modalContent.classList.add('scale-95', 'opacity-0');
                            setTimeout(() => {
                                modal.classList.add('hidden');
                            }, 300);
                        }

                        // User Edit Modal Functions
                        function openUserEditModal(userId) {
                            const modal = document.getElementById('userEditModal');
                            const content = document.getElementById('userEditModalContent');
                            const backdrop = document.getElementById('userEditModalBackdrop');
                            const modalContent = document.getElementById('userEditModalContentWrapper');

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

                            // Fetch user edit form
                            fetch(`{{ route('admin.users.edit-modal', ':id') }}`.replace(':id', userId))
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        content.innerHTML = data.html;
                                        // Attach form submit handler
                                        const form = document.getElementById('userEditForm');
                                        if (form) {
                                            form.addEventListener('submit', handleUserEditFormSubmit);
                                        }
                                    } else {
                                        content.innerHTML = '<div class="text-center py-12 text-red-600 dark:text-red-400">Error loading edit form</div>';
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    content.innerHTML = '<div class="text-center py-12 text-red-600 dark:text-red-400">Error loading edit form</div>';
                                });
                        }

                        function closeUserEditModal() {
                            const modal = document.getElementById('userEditModal');
                            const backdrop = document.getElementById('userEditModalBackdrop');
                            const modalContent = document.getElementById('userEditModalContentWrapper');

                            backdrop.classList.remove('bg-opacity-50');
                            backdrop.classList.add('bg-opacity-0');
                            modalContent.classList.remove('scale-100', 'opacity-100');
                            modalContent.classList.add('scale-95', 'opacity-0');
                            setTimeout(() => {
                                modal.classList.add('hidden');
                            }, 300);
                        }

                        function handleUserEditFormSubmit(e) {
                            e.preventDefault();
                            const form = e.target;
                            const formData = new FormData(form);
                            const submitButton = form.querySelector('button[type="submit"]');
                            const originalText = submitButton.textContent;
                            const content = document.getElementById('userEditModalContent');

                            // Disable submit button and show loading
                            submitButton.disabled = true;
                            submitButton.textContent = 'Updating...';

                            fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                }
                            })
                                .then(response => {
                                    return response.json().then(data => {
                                        if (response.ok && data.success) {
                                            // Success - close modal, then redirect
                                            closeUserEditModal();

                                            // If redirecting to dashboard, redirect immediately (session message will be shown)
                                            if (data.redirect && data.redirect.includes('/dashboard')) {
                                                window.location.href = data.redirect;
                                            } else {
                                                // For other redirects, show toast then redirect
                                                showSuccessToast('User Updated', data.message || 'User has been updated successfully!');
                                                setTimeout(() => {
                                                    if (data.redirect) {
                                                        window.location.href = data.redirect;
                                                    } else {
                                                        window.location.reload();
                                                    }
                                                }, 1500);
                                            }
                                        } else {
                                            // Handle validation errors or other errors
                                            submitButton.disabled = false;
                                            submitButton.textContent = originalText;

                                            if (data.errors) {
                                                // Display validation errors
                                                let errorHtml = '<div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-800 dark:text-red-400"><ul class="list-disc list-inside space-y-1">';
                                                Object.keys(data.errors).forEach(field => {
                                                    data.errors[field].forEach(error => {
                                                        errorHtml += `<li>${error}</li>`;
                                                    });
                                                });
                                                errorHtml += '</ul></div>';

                                                // Insert errors at the top of the form
                                                const formContainer = form.parentElement;
                                                const existingErrors = formContainer.querySelector('.validation-errors');
                                                if (existingErrors) {
                                                    existingErrors.remove();
                                                }
                                                const errorDiv = document.createElement('div');
                                                errorDiv.className = 'validation-errors';
                                                errorDiv.innerHTML = errorHtml;
                                                formContainer.insertBefore(errorDiv, form);
                                            } else {
                                                // Show general error message
                                                const errorDiv = document.createElement('div');
                                                errorDiv.className = 'mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-800 dark:text-red-400';
                                                errorDiv.textContent = data.message || 'Error updating user. Please try again.';
                                                const formContainer = form.parentElement;
                                                const existingErrors = formContainer.querySelector('.validation-errors');
                                                if (existingErrors) {
                                                    existingErrors.remove();
                                                }
                                                formContainer.insertBefore(errorDiv, form);
                                            }
                                        }
                                    });
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    submitButton.disabled = false;
                                    submitButton.textContent = originalText;

                                    const errorDiv = document.createElement('div');
                                    errorDiv.className = 'mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-800 dark:text-red-400';
                                    errorDiv.textContent = 'An error occurred while updating the user. Please try again.';
                                    const formContainer = form.parentElement;
                                    const existingErrors = formContainer.querySelector('.validation-errors');
                                    if (existingErrors) {
                                        existingErrors.remove();
                                    }
                                    formContainer.insertBefore(errorDiv, form);
                                });
                        }

                        // User Create Modal Functions
                        function openUserCreateModal() {
                            const modal = document.getElementById('userCreateModal');
                            const content = document.getElementById('userCreateModalContent');
                            const backdrop = document.getElementById('userCreateModalBackdrop');
                            const modalContent = document.getElementById('userCreateModalContentWrapper');

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

                            // Fetch user create form
                            fetch(`{{ route('admin.users.create-modal') }}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                }
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        content.innerHTML = data.html;
                                        // Attach form submit handler
                                        const form = document.getElementById('userCreateForm');
                                        if (form) {
                                            form.addEventListener('submit', handleUserCreateFormSubmit);
                                        }
                                    } else {
                                        content.innerHTML = `<div class="text-center py-12 text-red-600 dark:text-red-400">Error loading create form: ${data.message || 'Unknown error'}</div>`;
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    content.innerHTML = `<div class="text-center py-12 text-red-600 dark:text-red-400">Error loading create form: ${error.message}</div>`;
                                });
                        }

                        function closeUserCreateModal() {
                            const modal = document.getElementById('userCreateModal');
                            const backdrop = document.getElementById('userCreateModalBackdrop');
                            const modalContent = document.getElementById('userCreateModalContentWrapper');

                            backdrop.classList.remove('bg-opacity-50');
                            backdrop.classList.add('bg-opacity-0');
                            modalContent.classList.remove('scale-100', 'opacity-100');
                            modalContent.classList.add('scale-95', 'opacity-0');
                            setTimeout(() => {
                                modal.classList.add('hidden');
                            }, 300);
                        }

                        function handleUserCreateFormSubmit(e) {
                            e.preventDefault();
                            const form = e.target;
                            const formData = new FormData(form);
                            const submitButton = form.querySelector('button[type="submit"]');
                            const originalText = submitButton.textContent;
                            const content = document.getElementById('userCreateModalContent');

                            // Disable submit button and show loading
                            submitButton.disabled = true;
                            submitButton.textContent = 'Creating...';

                            fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                }
                            })
                                .then(response => {
                                    return response.json().then(data => {
                                        if (response.ok && data.success) {
                                            // Success - close modal, then redirect to dashboard
                                            closeUserCreateModal();

                                            // If redirecting to dashboard, redirect immediately (session message will be shown)
                                            if (data.redirect && data.redirect.includes('/dashboard')) {
                                                window.location.href = data.redirect;
                                            } else {
                                                // For other redirects, show toast then redirect
                                                showSuccessToast('User Created', data.message || 'User has been created successfully!');
                                                setTimeout(() => {
                                                    if (data.redirect) {
                                                        window.location.href = data.redirect;
                                                    } else {
                                                        window.location.reload();
                                                    }
                                                }, 1500);
                                            }
                                        } else {
                                            // Handle validation errors or other errors
                                            submitButton.disabled = false;
                                            submitButton.textContent = originalText;

                                            if (data.errors) {
                                                // Display validation errors
                                                let errorHtml = '<div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-800 dark:text-red-400"><ul class="list-disc list-inside space-y-1">';
                                                Object.keys(data.errors).forEach(field => {
                                                    data.errors[field].forEach(error => {
                                                        errorHtml += `<li>${error}</li>`;
                                                    });
                                                });
                                                errorHtml += '</ul></div>';

                                                // Insert errors at the top of the form
                                                const formContainer = form.parentElement;
                                                const existingErrors = formContainer.querySelector('.validation-errors');
                                                if (existingErrors) {
                                                    existingErrors.remove();
                                                }
                                                const errorDiv = document.createElement('div');
                                                errorDiv.className = 'validation-errors';
                                                errorDiv.innerHTML = errorHtml;
                                                formContainer.insertBefore(errorDiv, form);
                                            } else {
                                                // Show general error message
                                                const errorDiv = document.createElement('div');
                                                errorDiv.className = 'mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-800 dark:text-red-400';
                                                errorDiv.textContent = data.message || 'Error creating user. Please try again.';
                                                const formContainer = form.parentElement;
                                                const existingErrors = formContainer.querySelector('.validation-errors');
                                                if (existingErrors) {
                                                    existingErrors.remove();
                                                }
                                                formContainer.insertBefore(errorDiv, form);
                                            }
                                        }
                                    });
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    submitButton.disabled = false;
                                    submitButton.textContent = originalText;

                                    const errorDiv = document.createElement('div');
                                    errorDiv.className = 'mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-800 dark:text-red-400';
                                    errorDiv.textContent = 'An error occurred while creating the user. Please try again.';
                                    const formContainer = form.parentElement;
                                    const existingErrors = formContainer.querySelector('.validation-errors');
                                    if (existingErrors) {
                                        existingErrors.remove();
                                    }
                                    formContainer.insertBefore(errorDiv, form);
                                });
                        }

                        // Handle toggle status from modal
                        function handleToggleStatus(userId, currentStatus) {
                            const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
                            const actionText = currentStatus === 'active' ? 'inactive' : 'active';

                            Swal.fire({
                                title: `Set User to ${actionText.charAt(0).toUpperCase() + actionText.slice(1)}?`,
                                html: `
                                                                                                                        <div class="text-center">
                                                                                                                            <p class="mb-2">Are you sure you want to set this user to <strong>${actionText}</strong>?</p>
                                                                                                                        </div>
                                                                                                                    `,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: `Yes, Set ${actionText.charAt(0).toUpperCase() + actionText.slice(1)}`,
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: newStatus === 'active' ? '#10b981' : '#6b7280',
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
                                    const form = document.getElementById('toggleStatusForm');
                                    if (!form) return;

                                    const submitButton = form.querySelector('button[type="button"]');
                                    const originalText = submitButton.textContent;
                                    submitButton.disabled = true;
                                    submitButton.textContent = 'Updating...';

                                    // Create form data
                                    const formData = new FormData();
                                    formData.append('_token', '{{ csrf_token() }}');
                                    formData.append('_method', 'PATCH');

                                    fetch(form.action, {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                        }
                                    })
                                        .then(response => {
                                            return response.json().then(data => {
                                                if (response.ok && data.success) {
                                                    // Show success message
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Status Updated',
                                                        text: data.message || 'User status has been updated successfully.',
                                                        confirmButtonColor: '#10b981',
                                                        timer: 1500,
                                                        showConfirmButton: false
                                                    }).then(() => {
                                                        // Reload the modal content to show updated status
                                                        openUserViewModal(userId);
                                                    });
                                                } else {
                                                    submitButton.disabled = false;
                                                    submitButton.textContent = originalText;
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Error',
                                                        text: data.message || 'Failed to update user status. Please try again.',
                                                        confirmButtonColor: '#dc2626'
                                                    });
                                                }
                                            });
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            submitButton.disabled = false;
                                            submitButton.textContent = originalText;
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: 'An error occurred while updating the status. Please try again.',
                                                confirmButtonColor: '#dc2626'
                                            });
                                        });
                                }
                            });
                        }

                        // Handle reset password (placeholder function)
                        function handleResetPassword(userId) {
                            // Placeholder function - functionality to be implemented later
                            Swal.fire({
                                icon: 'info',
                                title: 'Reset Password',
                                text: 'Reset password functionality will be implemented here.',
                                confirmButtonColor: '#FE8000'
                            });
                        }

                        // Handle verify email
                        function handleVerifyEmail(userId) {
                            Swal.fire({
                                title: 'Verify Email?',
                                html: `
                                                                                                                        <div class="text-center">
                                                                                                                            <p class="mb-2">Are you sure you want to verify this user's email address?</p>
                                                                                                                            <p class="text-sm text-gray-600">This will mark the email as verified.</p>
                                                                                                                        </div>
                                                                                                                    `,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, Verify Email',
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: '#10b981',
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
                                    // Create form data
                                    const formData = new FormData();
                                    formData.append('_token', '{{ csrf_token() }}');
                                    formData.append('_method', 'POST');

                                    fetch(`{{ route('admin.users.verify-email', ':id') }}`.replace(':id', userId), {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                        }
                                    })
                                        .then(response => {
                                            return response.json().then(data => {
                                                if (response.ok && data.success) {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Email Verified',
                                                        text: data.message || 'Email has been verified successfully.',
                                                        confirmButtonColor: '#10b981',
                                                        timer: 2000,
                                                        showConfirmButton: false
                                                    }).then(() => {
                                                        // Reload the modal content to show updated status
                                                        openUserViewModal(userId);
                                                    });
                                                } else {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Error',
                                                        text: data.message || 'Failed to verify email. Please try again.',
                                                        confirmButtonColor: '#dc2626'
                                                    });
                                                }
                                            });
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: 'An error occurred while verifying the email. Please try again.',
                                                confirmButtonColor: '#dc2626'
                                            });
                                        });
                                }
                            });
                        }

                        // Handle unverify email
                        function handleUnverifyEmail(userId) {
                            Swal.fire({
                                title: 'Unverify Email?',
                                html: `
                                                                                                                        <div class="text-center">
                                                                                                                            <p class="mb-2">Are you sure you want to unverify this user's email address?</p>
                                                                                                                            <p class="text-sm text-gray-600">This will mark the email as not verified.</p>
                                                                                                                        </div>
                                                                                                                    `,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, Unverify Email',
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
                                    // Create form data
                                    const formData = new FormData();
                                    formData.append('_token', '{{ csrf_token() }}');
                                    formData.append('_method', 'POST');

                                    fetch(`{{ route('admin.users.unverify-email', ':id') }}`.replace(':id', userId), {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                        }
                                    })
                                        .then(response => {
                                            return response.json().then(data => {
                                                if (response.ok && data.success) {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Email Unverified',
                                                        text: data.message || 'Email has been unverified successfully.',
                                                        confirmButtonColor: '#10b981',
                                                        timer: 2000,
                                                        showConfirmButton: false
                                                    }).then(() => {
                                                        // Reload the modal content to show updated status
                                                        openUserViewModal(userId);
                                                    });
                                                } else {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Error',
                                                        text: data.message || 'Failed to unverify email. Please try again.',
                                                        confirmButtonColor: '#dc2626'
                                                    });
                                                }
                                            });
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: 'An error occurred while unverifying the email. Please try again.',
                                                confirmButtonColor: '#dc2626'
                                            });
                                        });
                                }
                            });
                        }

                        // Close modals when clicking outside
                        document.getElementById('userViewModalBackdrop')?.addEventListener('click', function () {
                            closeUserViewModal();
                        });

                        document.getElementById('userEditModalBackdrop')?.addEventListener('click', function () {
                            closeUserEditModal();
                        });

                        document.getElementById('userCreateModalBackdrop')?.addEventListener('click', function () {
                            closeUserCreateModal();
                        });
                    @endif

                    // Handle submission completion
                    function openCompleteModal(formSlug, submissionId) {
                        Swal.fire({
                            title: 'Complete Submission?',
                            text: "Are you sure you want to mark this submission as completed?",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#10b981',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Yes, Complete it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Submit a form programmatically
                                const form = document.createElement('form');
                                form.method = 'POST';
                                form.action = `/eform/admin/submissions/${formSlug}/${submissionId}/complete`;

                                const csrfInput = document.createElement('input');
                                csrfInput.type = 'hidden';
                                csrfInput.name = '_token';
                                csrfInput.value = '{{ csrf_token() }}';
                                form.appendChild(csrfInput);

                                document.body.appendChild(form);
                                form.submit();
                            }
                        });
                    }

                    // Auto-scroll to filter section if filters are active and section is not visible
                    document.addEventListener('DOMContentLoaded', function () {
                        const filterSection = document.getElementById('searchFilterSection');
                        const hasActiveFilters = {{ request()->hasAny(['search', 'status', 'branch_id', 'form_id']) ? 'true' : 'false' }};

                        if (hasActiveFilters && filterSection) {
                            // Check if filter section is visible in viewport (with some tolerance)
                            const rect = filterSection.getBoundingClientRect();
                            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
                            const viewportWidth = window.innerWidth || document.documentElement.clientWidth;

                            // Consider element visible if it's at least partially in viewport
                            const isVisible = (
                                rect.top < viewportHeight &&
                                rect.bottom > 0 &&
                                rect.left < viewportWidth &&
                                rect.right > 0
                            );

                            // If not visible or only partially visible at the bottom, scroll to it smoothly
                            if (!isVisible || rect.top < 100) {
                                setTimeout(() => {
                                    // Calculate offset for fixed header (if any)
                                    const offset = 20; // Small offset from top
                                    const elementPosition = filterSection.getBoundingClientRect().top;
                                    const offsetPosition = elementPosition + window.pageYOffset - offset;

                                    window.scrollTo({
                                        top: offsetPosition,
                                        behavior: 'smooth'
                                    });
                                }, 100);
                            }
                        }
                    });
                </script>
    @endpush
@endsection