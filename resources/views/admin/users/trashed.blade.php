@extends('layouts.admin-minimal')

@section('title', 'Trashed Users - BMMB Digital Forms')
@section('page-title', 'Trashed Users')
@section('page-description', 'View and manage deleted users')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-800 dark:text-red-400">
    {{ session('error') }}
</div>
@endif

<div class="space-y-6">
    <!-- Stats Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Total Deleted Users</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $users->total() }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-trash text-2xl text-red-600'></i>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.users.trashed') }}" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <!-- Search Input -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}"
                               placeholder="Search by name, email, or phone..."
                               class="w-full pl-10 pr-4 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <i class='bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500'></i>
                    </div>
                </div>
                
                <!-- Role Filter -->
                <div>
                    <label for="role" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                    <select name="role" 
                            id="role"
                            class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="branch_manager" {{ request('role') === 'branch_manager' ? 'selected' : '' }}>Branch Manager</option>
                        <option value="assistant_branch_manager" {{ request('role') === 'assistant_branch_manager' ? 'selected' : '' }}>Assistant Branch Manager</option>
                        <option value="operation_officer" {{ request('role') === 'operation_officer' ? 'selected' : '' }}>Operations Officer</option>
                        <option value="headquarters" {{ request('role') === 'headquarters' ? 'selected' : '' }}>Headquarters</option>
                        <option value="iam" {{ request('role') === 'iam' ? 'selected' : '' }}>Identity & Access Management</option>
                    </select>
                </div>
                
                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" 
                            id="status"
                            class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                </div>
            </div>
            

            
            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-2">
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    <i class='bx bx-search mr-1.5 text-sm'></i>
                    Search
                </button>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.trashed') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-xs font-semibold rounded-lg transition-colors">
                        <i class='bx bx-x mr-1.5'></i>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Trashed Users</h3>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.users.index') }}" 
                   class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    <i class='bx bx-arrow-back mr-1.5'></i>
                    Back to Active
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Branch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deleted Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors opacity-60 bg-red-50 dark:bg-red-900/10">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $user->first_name }} {{ $user->last_name }}
                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                    Deleted
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $user->email }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($user->status === 'active') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                @elseif($user->status === 'inactive') bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                @elseif($user->status === 'suspended') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400
                                @endif">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            @if($user->deleted_at)
                                <div>{{ $user->deleted_at->format($dateFormat) }}</div>
                                <div class="text-xs text-gray-400">{{ $user->deleted_at->format($timeFormat) }}</div>
                            @else
                                <div class="text-xs text-gray-400">N/A</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 rounded-lg transition-colors">
                                    View
                                </a>
                                <button onclick="restoreUser({{ $user->id }})" class="inline-flex items-center justify-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 dark:bg-green-900/30 dark:hover:bg-green-900/50 dark:text-green-400 rounded-lg transition-colors">
                                    Restore
                                </button>
                                <button onclick="forceDeleteUser({{ $user->id }})" class="inline-flex items-center justify-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg transition-colors">
                                    Delete Permanently
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex flex-col items-center justify-center py-8">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-3">
                                    <i class='bx bx-trash text-2xl text-gray-400 dark:text-gray-500'></i>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No Deleted Users Found</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">No users have been deleted yet</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $users->links() }}
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

function restoreUser(userId) {
    Swal.fire({
        title: 'Restore User?',
        html: `
            <div class="text-center">
                <p class="mb-2">Are you sure you want to restore this user?</p>
                <p class="text-sm text-gray-600">The user will be restored and available in the active users list.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Restore',
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
            form.action = `{{ route('admin.users.restore', ':id') }}`.replace(':id', userId);
            
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

function forceDeleteUser(userId) {
    Swal.fire({
        title: 'Permanently Delete User?',
        html: `
            <div class="text-center">
                <p class="mb-2"><strong>Warning: This action cannot be undone!</strong></p>
                <p class="mb-2">Are you sure you want to permanently delete this user?</p>
                <p class="text-sm text-red-600">This will permanently remove all user data from the system.</p>
            </div>
        `,
        icon: 'error',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete Permanently',
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
            form.action = `{{ route('admin.users.force-delete', ':id') }}`.replace(':id', userId);
            
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
</script>
@endpush
@endsection

