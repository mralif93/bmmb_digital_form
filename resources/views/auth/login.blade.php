@extends('layouts.public')

@section('title', 'Login - BMMB Digital Forms')

@push('styles')
<style>
    body {
        overflow-y: auto !important;
    }
    main {
        min-height: auto !important;
    }
</style>
@endpush

@section('content')
<!-- Login Section -->
<section class="relative min-h-screen flex items-start bg-gray-100 dark:bg-gray-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="max-w-md mx-auto">
            <!-- Login Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-lg">
                <!-- Header -->
                <div class="text-center mb-6">
                    <div class="inline-block mb-3 px-3 py-1.5 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-full text-xs font-semibold">
                        <i class='bx bx-shield-alt-2 mr-1.5'></i>Secure Login
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        Welcome Back
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 text-xs">
                        Sign in to your account to continue
                    </p>
                </div>

                <!-- Login Form -->
                <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Email address
                        </label>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                               class="w-full px-3 py-2 text-xs bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 @error('email') border-red-500 @enderror" 
                               placeholder="Enter your email">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Password
                        </label>
                        <div class="relative" x-data="{ showPassword: false }">
                            <input id="password" name="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" required 
                                   class="w-full px-3 py-2 pr-10 text-xs bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 @error('password') border-red-500 @enderror" 
                                   placeholder="Enter your password">
                            <button type="button" @click="showPassword = !showPassword" 
                                    class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center justify-center text-gray-400 dark:text-gray-500 hover:text-primary-600 dark:hover:text-primary-400 transition-colors focus:outline-none">
                                <i class='bx text-base' :class="showPassword ? 'bx-hide' : 'bx-show'"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" 
                                   class="h-3.5 w-3.5 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700">
                            <label for="remember" class="ml-2 block text-xs text-gray-700 dark:text-gray-300">
                                Remember me
                            </label>
                        </div>

                        <div class="text-xs">
                            <a href="{{ route('password.request') }}" class="font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-500">
                                Forgot password?
                            </a>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full flex justify-center items-center py-2.5 px-4 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-lg font-semibold hover:bg-primary-200 dark:hover:bg-primary-900/50 transition-all duration-300 transform hover:scale-[1.02] text-sm shadow-md hover:shadow-lg">
                        Sign in
                    </button>
                </form>

                <!-- Demo Credentials -->
                <div class="mt-5 p-3 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-100 dark:border-primary-800">
                    <h3 class="text-xs font-semibold text-primary-900 dark:text-primary-300 mb-1.5 flex items-center">
                        <i class='bx bx-info-circle mr-1.5 text-primary-600 dark:text-primary-400 text-sm'></i>Demo Credentials
                    </h3>
                    <div class="text-xs text-gray-700 dark:text-gray-300 space-y-0.5">
                        <p><strong class="text-primary-600 dark:text-primary-400">Admin:</strong> admin@bmmb.com / password</p>
                        <p><strong class="text-primary-600 dark:text-primary-400">User:</strong> john.doe@example.com / password</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
