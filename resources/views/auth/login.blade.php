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
<section class="relative min-h-screen flex items-center bg-gray-100 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="max-w-md mx-auto">
            <!-- Login Card -->
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-lg">
                <!-- Header -->
                <div class="text-center mb-6">
                    <div class="inline-block mb-3 px-3 py-1.5 bg-primary-100 text-primary-600 rounded-full text-xs font-semibold">
                        <i class='bx bx-shield-alt-2 mr-1.5'></i>Secure Login
                    </div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                        Welcome Back
                    </h1>
                    <p class="text-gray-600 text-xs">
                        Sign in to your account to continue
                    </p>
                </div>

                <!-- Login Form -->
                <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
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
                    
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Password
                        </label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                               class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-300 @error('password') border-red-500 @enderror" 
                               placeholder="Enter your password">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" 
                                   class="h-3.5 w-3.5 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-xs text-gray-700">
                                Remember me
                            </label>
                        </div>

                        <div class="text-xs">
                            <a href="{{ route('password.request') }}" class="font-medium text-primary-600 hover:text-primary-700">
                                Forgot password?
                            </a>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full flex justify-center items-center py-2.5 px-4 bg-primary-100 text-primary-600 rounded-lg font-semibold hover:bg-primary-200 transition-all duration-300 transform hover:scale-[1.02] text-sm">
                        Sign in
                    </button>
                </form>

                <!-- Demo Credentials -->
                <div class="mt-5 p-3 bg-primary-50 rounded-lg border border-primary-100">
                    <h3 class="text-xs font-semibold text-primary-900 mb-1.5 flex items-center">
                        <i class='bx bx-info-circle mr-1.5 text-primary-600 text-sm'></i>Demo Credentials
                    </h3>
                    <div class="text-xs text-gray-700 space-y-0.5">
                        <p><strong class="text-primary-600">Admin:</strong> admin@bmmb.com / password</p>
                        <p><strong class="text-primary-600">User:</strong> john.doe@example.com / password</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
