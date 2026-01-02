@extends('layouts.admin-minimal')

@section('title', 'Users Management - BMMB Digital Forms')
@section('page-title', 'Users Management')
@section('page-description', 'Manage all system users and their permissions')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400 flex items-center justify-between">
    <span>{{ session('success') }}</span>
    <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">
        <i class='bx bx-x text-lg'></i>
    </button>
</div>
@endif

<div class="mb-4 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
            <i class='bx bx-user text-orange-600 dark:text-orange-400 text-xl'></i>
        </div>
        <div>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Users Management</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400">Total: {{ $users->total() }} records</p>
        </div>
    </div>
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.users.trashed') }}" class="inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 text-xs font-semibold rounded-lg transition-colors">
            <i class='bx bx-trash mr-1.5'></i>
            Trashed Users
        </a>
            <button onclick="confirmResync('{{ route('admin.users.resync') }}')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                <i class='bx bx-refresh mr-1.5'></i>
                Resync
            </button>
            <button onclick="openUserCreateModal()" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
            <i class='bx bx-plus mr-1.5'></i>
            Create New
        </button>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="mb-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
    <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <!-- Search Input -->
            <div class="md:col-span-2">
                <label for="search" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <div class="relative">
                    <input type="text" 
                            name="search" 
                           id="search" 
                           value="{{ request('search') }}"
                           placeholder="Search by name, email, Staff ID or phone..."
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
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                <i class='bx bx-search mr-1.5'></i>
                Search
            </button>
            @if(request()->hasAny(['search', 'role', 'status']))
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-xs font-semibold rounded-lg transition-colors">
                    <i class='bx bx-x mr-1.5'></i>
                    Clear
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Users</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">All system users</p>
            </div>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Staff ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Branch</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Login</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
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
                        <div class="text-sm text-gray-900 dark:text-white font-medium">
                            {{ $user->username ?: '-' }}
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
                        @if($user->last_login_at)
                            <div>{{ $user->last_login_at->format($dateFormat) }}</div>
                            <div class="text-xs text-gray-400">{{ $user->last_login_at->format($timeFormat) }}</div>
                        @else
                            <span class="text-gray-400">Never</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <button onclick="openUserViewModal({{ $user->id }})" class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 rounded-lg transition-colors">
                                View
                            </button>
                            <button onclick="openUserEditModal({{ $user->id }})" class="inline-flex items-center justify-center px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 dark:bg-yellow-900/30 dark:hover:bg-yellow-900/50 dark:text-yellow-400 rounded-lg transition-colors">
                                Edit
                            </button>
                            <button onclick="deleteUser({{ $user->id }})" class="inline-flex items-center justify-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-3">
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

<!-- User View Modal -->
<div id="userViewModal" class="fixed inset-0 hidden z-50 flex items-center justify-center p-2 sm:p-4">
    <div class="fixed inset-0 bg-black bg-opacity-0 transition-opacity duration-300" id="userViewModalBackdrop"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0" id="userViewModalContentWrapper">
        <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Details</h3>
                <button onclick="closeUserViewModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
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
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0" id="userEditModalContentWrapper">
        <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit User</h3>
                <button onclick="closeUserEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
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
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0" id="userCreateModalContentWrapper">
        <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create New User</h3>
                <button onclick="closeUserCreateModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
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


@push('scripts')
<script>
    function confirmResync(route) {
        Swal.fire({
            title: 'Resync with MAP?',
            html: `
                <div class="text-center">
                    <p class="mb-2">Are you sure you want to resync data from the MAP database?</p>
                    <p class="text-sm text-gray-600">This process might take a while depending on the amount of data.</p>
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Yes, Resync',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#2563eb',
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
                form.action = route;
                
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);
                
                document.body.appendChild(form);
                form.submit();
                
                // Show loading state
                Swal.fire({
                    title: 'Syncing...',
                    text: 'Please wait while we sync the data.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            }
        });
    }

@php
    $settings = \Illuminate\Support\Facades\Cache::get('system_settings', [
        'primary_color' => '#FE8000',
    ]);
    $primaryColor = $settings['primary_color'] ?? '#FE8000';
@endphp

function deleteUser(userId) {
    Swal.fire({
        title: 'Delete User?',
        html: `
            <div class="text-center">
                <p class="mb-2">Are you sure you want to delete this user?</p>
                <p class="text-sm text-gray-600">This action will move the user to the trash. You can restore it later if needed.</p>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
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
        form.action = `{{ route('admin.users.destroy', ':id') }}`.replace(':id', userId);
        
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
    fetch(`{{ route('admin.users.details', ':id') }}`.replace(':id', userId), {
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
            } else {
                content.innerHTML = `<div class="text-center py-12 text-red-600 dark:text-red-400">Error loading user details: ${data.message || 'Unknown error'}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = `<div class="text-center py-12 text-red-600 dark:text-red-400">Error loading user details: ${error.message}</div>`;
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
    fetch(`{{ route('admin.users.edit-modal', ':id') }}`.replace(':id', userId), {
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
                const form = document.getElementById('userEditForm');
                if (form) {
                    form.addEventListener('submit', handleUserEditFormSubmit);
                }
            } else {
                content.innerHTML = `<div class="text-center py-12 text-red-600 dark:text-red-400">Error loading edit form: ${data.message || 'Unknown error'}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = `<div class="text-center py-12 text-red-600 dark:text-red-400">Error loading edit form: ${error.message}</div>`;
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
                // Success - close modal, then redirect to users index
                closeUserEditModal();
                window.location.href = data.redirect || '{{ route("admin.users.index") }}';
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
                // Success - close modal, then redirect to users index
                closeUserCreateModal();
                window.location.href = data.redirect || '{{ route("admin.users.index") }}';
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

// Close modals when clicking outside
document.getElementById('userViewModalBackdrop')?.addEventListener('click', function() {
    closeUserViewModal();
});

document.getElementById('userEditModalBackdrop')?.addEventListener('click', function() {
    closeUserEditModal();
});

document.getElementById('userCreateModalBackdrop')?.addEventListener('click', function() {
    closeUserCreateModal();
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const userViewModal = document.getElementById('userViewModal');
        const userEditModal = document.getElementById('userEditModal');
        const userCreateModal = document.getElementById('userCreateModal');
        
        if (userViewModal && !userViewModal.classList.contains('hidden')) {
            closeUserViewModal();
        } else if (userEditModal && !userEditModal.classList.contains('hidden')) {
            closeUserEditModal();
        } else if (userCreateModal && !userCreateModal.classList.contains('hidden')) {
            closeUserCreateModal();
        }
    }
});
</script>
@endpush
@endsection
