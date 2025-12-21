@extends('layouts.public')

@section('title', 'Forgot Password - BMMB Digital Forms')

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
<!-- Forgot Password Section -->
<section class="relative min-h-screen flex items-start bg-gray-100 dark:bg-gray-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="max-w-md mx-auto">
            <!-- Forgot Password Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-lg">
                <!-- Header -->
                <div class="text-center mb-6">
                    <div class="inline-block mb-3 px-3 py-1.5 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-full text-xs font-semibold">
                        <i class='bx bx-key mr-1.5'></i>Reset Password
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                        Forgot Password?
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 text-xs">
                        Enter your email address and we'll send you a link to reset your password
                    </p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-5 p-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg">
                        <div class="flex items-center">
                            <i class='bx bx-check-circle text-green-600 dark:text-green-400 text-base mr-2'></i>
                            <p class="text-green-800 dark:text-green-300 text-xs">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Reset Form -->
                <form class="space-y-4" action="{{ route('password.email') }}" method="POST">
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
                    
                    <!-- Send Reset Link Button -->
                    <button type="submit" 
                            class="w-full flex justify-center items-center py-2.5 px-4 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-lg font-semibold hover:bg-primary-200 dark:hover:bg-primary-900/50 transition-all duration-300 transform hover:scale-[1.02] text-sm shadow-md hover:shadow-lg">
                        Send Reset Link
                    </button>
                </form>
                
                <!-- Divider -->
                <div class="relative my-5">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-3 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                            Remember your password?
                        </span>
                    </div>
                </div>
                
                <!-- Back to Login -->
                <a href="{{ route('login') }}" 
                   class="block w-full border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-2.5 px-4 rounded-lg font-semibold hover:border-primary-300 dark:hover:border-primary-600 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-all duration-300 text-center text-sm">
                    Back to Login
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
