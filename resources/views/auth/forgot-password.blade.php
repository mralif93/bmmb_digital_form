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
<section class="relative min-h-screen flex items-center bg-gray-100 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="max-w-md mx-auto">
            <!-- Forgot Password Card -->
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-lg">
                <!-- Header -->
                <div class="text-center mb-6">
                    <div class="inline-block mb-3 px-3 py-1.5 bg-primary-100 text-primary-600 rounded-full text-xs font-semibold">
                        <i class='bx bx-key mr-1.5'></i>Reset Password
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                        Forgot Password?
                    </h1>
                    <p class="text-gray-600 text-xs">
                        Enter your email address and we'll send you a link to reset your password
                    </p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-5 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <i class='bx bx-check-circle text-green-600 text-base mr-2'></i>
                            <p class="text-green-800 text-xs">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                <!-- Reset Form -->
                <form class="space-y-4" action="{{ route('password.email') }}" method="POST">
                    @csrf
                    
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Email address
                        </label>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                               class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 @error('email') border-red-500 @enderror" 
                               placeholder="Enter your email">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Send Reset Link Button -->
                    <button type="submit" 
                            class="w-full flex justify-center items-center py-2.5 px-4 bg-primary-100 text-primary-600 rounded-lg font-semibold hover:bg-primary-200 transition-all duration-300 transform hover:scale-[1.02] text-sm">
                        Send Reset Link
                    </button>
                </form>
                
                <!-- Divider -->
                <div class="relative my-5">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-3 bg-white text-gray-500">
                            Remember your password?
                        </span>
                    </div>
                </div>
                
                <!-- Back to Login -->
                <a href="{{ route('login') }}" 
                   class="block w-full border-2 border-gray-300 text-gray-700 py-2.5 px-4 rounded-lg font-semibold hover:border-primary-300 hover:text-primary-600 hover:bg-primary-50 transition-all duration-300 text-center text-sm">
                    Back to Login
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
