@extends('layouts.admin-minimal')

@section('title', 'Admin Profile - BMMB Digital Forms')
@section('page-title', 'Profile')
@section('page-description', 'Manage your admin profile and account settings')

@section('content')
    @if(session('success'))
        <div
            class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div
            class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-800 dark:text-red-400">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="space-y-6">
        <!-- Profile Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <!-- Avatar -->
                <div class="relative">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-orange-600 to-orange-700 rounded-full flex items-center justify-center shadow-lg">
                        <i class='bx bx-user text-white text-2xl'></i>
                    </div>
                </div>

                <!-- Profile Info -->
                <div class="flex-1">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->full_name }}</h2>
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                    <div class="flex items-center space-x-3 mt-2">
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $user->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                            <i class='bx bx-check-circle mr-1'></i>
                            {{ ucfirst($user->status) }}
                        </span>
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
                            <i class='bx bx-shield-check mr-1'></i>
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Personal Information -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Basic Information -->
                <form action="{{ route('admin.profile.update') }}" method="POST"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                    @csrf
                    @method('PUT')

                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class='bx bx-user mr-2 text-orange-600 dark:text-orange-400'></i>
                        Personal Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name"
                                class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">First Name
                                *</label>
                            <input type="text" name="first_name" id="first_name"
                                value="{{ old('first_name', $user->first_name) }}"
                                class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('first_name') border-red-500 @enderror"
                                required>
                            @error('first_name')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="last_name"
                                class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Last Name
                                *</label>
                            <input type="text" name="last_name" id="last_name"
                                value="{{ old('last_name', $user->last_name) }}"
                                class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('last_name') border-red-500 @enderror"
                                required>
                            @error('last_name')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email"
                                class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Email Address
                                *</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                required>
                            @error('email')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="phone"
                                class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Phone
                                Number</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="bio"
                            class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Bio</label>
                        <textarea name="bio" id="bio" rows="3"
                            class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('bio') border-red-500 @enderror"
                            placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                            class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                            <i class='bx bx-save mr-1.5'></i>
                            Save Changes
                        </button>
                    </div>
                </form>

                <!-- Password Change -->
                <form action="{{ route('admin.profile.password.update') }}" method="POST"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                    @csrf
                    @method('PUT')

                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class='bx bx-key mr-2 text-orange-600 dark:text-orange-400'></i>
                        Change Password
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label for="current_password"
                                class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Current Password
                                *</label>
                            <input type="password" name="current_password" id="current_password"
                                class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('current_password') border-red-500 @enderror"
                                required>
                            @error('current_password')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password"
                                    class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">New Password
                                    *</label>
                                <input type="password" name="password" id="password"
                                    class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                    required>
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password_confirmation"
                                    class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Confirm New
                                    Password *</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="w-full px-3 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                            class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                            <i class='bx bx-key mr-1.5'></i>
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sidebar -->
            <div class="space-y-4">
                <!-- Account Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class='bx bx-stats mr-2 text-orange-600 dark:text-orange-400'></i>
                        Account Statistics
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                            <span class="text-xs text-gray-600 dark:text-gray-400">Member Since</span>
                            <span
                                class="text-xs font-semibold text-gray-900 dark:text-white">{{ $timezoneHelper->convert($user->created_at)?->format($dateFormat) }}</span>
                        </div>
                        @if($user->last_login_at)
                            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                <span class="text-xs text-gray-600 dark:text-gray-400">Last Login</span>
                                <span
                                    class="text-xs font-semibold text-gray-900 dark:text-white">{{ $timezoneHelper->convert($user->last_login_at)?->diffForHumans() }}</span>
                            </div>
                        @endif
                        @if($user->last_login_ip)
                            <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                <span class="text-xs text-gray-600 dark:text-gray-400">Last Login IP</span>
                                <span
                                    class="text-xs font-semibold text-gray-900 dark:text-white">{{ $user->last_login_ip }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between pb-2">
                            <span class="text-xs text-gray-600 dark:text-gray-400">Total Actions</span>
                            <span
                                class="text-xs font-semibold text-gray-900 dark:text-white">{{ $user->auditTrails()->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection