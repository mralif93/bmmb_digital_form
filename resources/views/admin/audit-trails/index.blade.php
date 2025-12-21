@extends('layouts.admin-minimal')

@section('title', 'Audit Trail - BMMB Digital Forms')
@section('page-title', 'Audit Trail')
@section('page-description', 'Track all user actions and system events')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

<div class="mb-4 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
            <i class='bx bx-history text-purple-600 dark:text-purple-400 text-xl'></i>
        </div>
        <div>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Audit Trail</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400">Total: {{ $auditTrails->total() }} records</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="mb-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
    <form method="GET" action="{{ route('admin.audit-trails.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
        <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Action</label>
            <select name="action" class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="">All Actions</option>
                @foreach($actions as $action)
                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ ucfirst($action) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">User</label>
            <select name="user_id" class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="">All Users</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Model Type</label>
            <select name="model_type" class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                <option value="">All Models</option>
                @foreach($modelTypes as $modelType)
                <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>{{ class_basename($modelType) }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Date From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Date To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
        </div>

        <div class="lg:col-span-5">
            <div class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by description..." class="flex-1 px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                <button type="submit" class="px-4 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    <i class='bx bx-search mr-1'></i>
                    Filter
                </button>
                <a href="{{ route('admin.audit-trails.index') }}" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-xs font-semibold rounded-lg transition-colors">
                    <i class='bx bx-refresh mr-1'></i>
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Audit Trails Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Date & Time
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        User
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Action
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Description
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Model
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        IP Address
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($auditTrails as $auditTrail)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-xs text-gray-900 dark:text-white">
                            {{ $timezoneHelper->convert($auditTrail->created_at)?->format('Y-m-d') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $timezoneHelper->convert($auditTrail->created_at)?->format('H:i:s') }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-xs font-semibold text-gray-900 dark:text-white">
                            {{ $auditTrail->user ? $auditTrail->user->full_name : 'System' }}
                        </div>
                        @if($auditTrail->user)
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $auditTrail->user->email }}
                        </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @php
                            $actionColors = [
                                'create' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                'update' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'delete' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                'login' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                'logout' => 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400',
                            ];
                            $color = $actionColors[$auditTrail->action] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400';
                        @endphp
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold {{ $color }}">
                            {{ $auditTrail->action_display }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-xs text-gray-900 dark:text-white max-w-xs truncate">
                            {{ $auditTrail->description ?? 'N/A' }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($auditTrail->model_type)
                        <div class="text-xs text-gray-900 dark:text-white">
                            {{ class_basename($auditTrail->model_type) }}
                        </div>
                        @if($auditTrail->model_id)
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            ID: {{ $auditTrail->model_id }}
                        </div>
                        @endif
                        @else
                        <span class="text-xs text-gray-400 dark:text-gray-500">N/A</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-xs text-gray-600 dark:text-gray-400">
                            {{ $auditTrail->ip_address ?? 'N/A' }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.audit-trails.show', $auditTrail->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 rounded-lg text-xs transition-colors">
                                View
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class='bx bx-history text-2xl text-gray-400 dark:text-gray-500'></i>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No audit trails found</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400">No user actions have been logged yet</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($auditTrails->hasPages())
    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
        {{ $auditTrails->links() }}
    </div>
    @endif
</div>
@endsection
