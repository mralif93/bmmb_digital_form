@extends('layouts.admin-minimal')

@section('title', 'User Details - BMMB Digital Forms')
@section('page-title', 'User Details')
@section('page-description', 'View detailed information about this user')

@section('content')
<div class="space-y-6">
    <!-- User Profile Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class='bx bx-user text-white text-3xl'></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->full_name }}</h2>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $user->email }}</p>
                        <div class="flex items-center space-x-3 mt-3">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold
                                @if($user->role === 'admin') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                @elseif($user->role === 'moderator') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                                @else bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold
                                @if($user->status === 'active') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                @elseif($user->status === 'inactive') bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400
                                @else bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                @endif">
                                {{ ucfirst($user->status) }}
                            </span>
                            @if($user->email_verified_at)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                <i class='bx bx-check-circle mr-1'></i>
                                Verified
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-md hover:shadow-lg">
                        <i class='bx bx-edit mr-2'></i>
                        Edit User
                    </a>
                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="inline-flex items-center px-5 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg {{ $user->status === 'active' ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white' }}">
                            <i class='bx {{ $user->status === 'active' ? 'bx-user-x' : 'bx-check-circle' }} mr-2'></i>
                            {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    <button type="button" 
                            onclick="confirmDeleteUser({{ $user->id }}, '{{ $user->full_name }}')"
                            class="inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-md hover:shadow-lg">
                        <i class='bx bx-trash mr-2'></i>
                        Delete
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-5 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-lg transition-colors">
                        <i class='bx bx-arrow-back mr-2'></i>
                        Back
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Member Since</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $user->created_at->format('M Y') }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Login</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ ucfirst($user->status) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email Status</p>
                    <p class="text-lg font-bold mt-1 {{ $user->email_verified_at ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Personal Information -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Personal Information</h3>
            </div>
            <div class="px-6 py-4">
                <dl class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">First Name</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->first_name }}</dd>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">Last Name</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->last_name }}</dd>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">Email Address</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->email }}</dd>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">Phone Number</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->phone ?: 'Not provided' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Account Details Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Account Details</h3>
            </div>
            <div class="px-6 py-4">
                <dl class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">User ID</dt>
                        <dd class="text-sm font-mono font-medium text-gray-900 dark:text-white">{{ $user->id }}</dd>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">Role</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                @if($user->role === 'admin') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                @elseif($user->role === 'moderator') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                                @else bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                @endif">
                                {{ ucfirst($user->role) }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">Account Status</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                @if($user->status === 'active') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                @elseif($user->status === 'inactive') bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400
                                @else bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                @endif">
                                {{ ucfirst($user->status) }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-700">
                        <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">Created</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300">Last Updated</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->updated_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Bio Section (if exists) -->
    @if($user->bio)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Biography</h3>
        </div>
        <div class="px-6 py-4">
            <p class="text-gray-700 dark:text-gray-300">{{ $user->bio }}</p>
        </div>
    </div>
    @endif

    <!-- Hidden delete form -->
    <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
</div>

<!-- SweetAlert2 Delete Confirmation Script -->
<script>
function confirmDeleteUser(userId, userName) {
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
</script>
@endsection
