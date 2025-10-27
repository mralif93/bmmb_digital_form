@extends('layouts.admin-minimal')

@section('title', 'Users Management - BMMB Digital Forms')
@section('page-title', 'Users Management')
@section('page-description', 'Manage all system users and their permissions')

@section('content')
<!-- Users Table -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">All Users</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage user accounts and permissions</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class='bx bx-plus mr-2'></i>
                Add New User
            </a>
        </div>
    </div>

    <!-- Table Content -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Last Login</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-gray-400 to-gray-500 rounded-lg flex items-center justify-center">
                                <i class='bx bx-user text-white text-lg'></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->full_name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($user->role === 'admin') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                            @elseif($user->role === 'moderator') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                            @else bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                            @endif">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($user->status === 'active') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                            @elseif($user->status === 'inactive') bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400
                            @else bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                            @endif">
                            {{ ucfirst($user->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <!-- Toggle Status Button -->
                            <button type="button" 
                                    onclick="confirmToggleStatus({{ $user->id }}, '{{ $user->full_name }}', '{{ $user->status }}')"
                                    class="inline-flex items-center px-3 py-2 text-xs font-medium rounded-lg transition-all duration-200 hover:scale-105 {{ $user->status === 'active' ? 'text-red-700 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-800/40' : 'text-green-700 bg-green-100 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-300 dark:hover:bg-green-800/40' }}" 
                                    title="{{ $user->status === 'active' ? 'Deactivate User' : 'Activate User' }}">
                                <i class='bx bx-power-off mr-1 text-sm'></i>
                                {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                            </button>
                            
                            <!-- View Button -->
                            <a href="{{ route('admin.users.show', $user) }}" 
                               class="inline-flex items-center px-3 py-2 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:hover:bg-blue-800/40 transition-all duration-200 hover:scale-105" 
                               title="View Details">
                                <i class='bx bx-show mr-1 text-sm'></i>
                                View
                            </a>
                            
                            <!-- Edit Button -->
                            <a href="{{ route('admin.users.edit', $user) }}" 
                               class="inline-flex items-center px-3 py-2 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-lg hover:bg-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-800/40 transition-all duration-200 hover:scale-105" 
                               title="Edit User">
                                <i class='bx bx-edit mr-1 text-sm'></i>
                                Edit
                            </a>
                            
                            <!-- Delete Button -->
                            <button type="button" 
                                    onclick="confirmDelete({{ $user->id }}, '{{ $user->full_name }}')"
                                    class="inline-flex items-center px-3 py-2 text-xs font-medium text-red-700 bg-red-100 rounded-lg hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-800/40 transition-all duration-200 hover:scale-105" 
                                    title="Delete User">
                                <i class='bx bx-trash mr-1 text-sm'></i>
                                Delete
                            </button>
                            
                            <!-- Hidden forms -->
                            <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            
                            <form id="toggle-status-form-{{ $user->id }}" action="{{ route('admin.users.toggle-status', $user) }}" method="POST" style="display: none;">
                                @csrf
                                @method('PATCH')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="text-gray-500 dark:text-gray-400">
                            <i class='bx bx-user-x text-4xl mb-4'></i>
                            <p class="text-lg font-medium">No users found</p>
                            <p class="text-sm">Get started by creating your first user.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination - Disabled for now -->
    {{-- @if($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $users->links() }}
    </div>
    @endif --}}
</div>

<!-- SweetAlert2 Confirmation Scripts -->
<script>
function confirmDelete(userId, userName) {
    Swal.fire({
        title: 'Are you sure?',
        html: `You are about to delete <strong>${userName}</strong><br><br>This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete user',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + userId).submit();
        }
    });
}

function confirmToggleStatus(userId, userName, currentStatus) {
    const action = currentStatus === 'active' ? 'deactivate' : 'activate';
    const icon = currentStatus === 'active' ? 'question' : 'success';
    const confirmColor = currentStatus === 'active' ? '#ef4444' : '#10b981';
    
    Swal.fire({
        title: `${action === 'activate' ? 'Activate' : 'Deactivate'} user?`,
        html: `You are about to ${action} <strong>${userName}</strong><br><br>This will change their account status and access permissions.`,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: confirmColor,
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Yes, ${action} user`,
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('toggle-status-form-' + userId).submit();
        }
    });
}
</script>
@endsection
