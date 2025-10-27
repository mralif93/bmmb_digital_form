@extends('layouts.public')

@section('title', 'Login - BMMB Digital Forms')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-purple-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-xl">
        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <!-- Logo & Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class='bx bx-lock text-white text-3xl'></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">
                    Welcome Back
                </h2>
                <p class="text-gray-600">
                    Sign in to your account
                </p>
            </div>

            <!-- Login Form -->
            <form class="space-y-6" action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="space-y-5">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email address
                        </label>
                        <input id="email" name="email" type="email" autocomplete="email" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 @error('email') border-red-500 @enderror" 
                               placeholder="Enter your email">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 @error('password') border-red-500 @enderror" 
                               placeholder="Enter your password">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" value="1"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                    
                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-700 transition-colors">
                            Forgot password?
                        </a>
                    </div>
                </div>
                
                <!-- Sign In Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                    <i class='bx bx-log-in mr-2'></i>
                    Sign In
                </button>
            </form>
            
            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500">
                        Don't have an account?
                    </span>
                </div>
            </div>
            
            <!-- Register Link -->
            <a href="{{ route('register') }}" class="block w-full border-2 border-gray-300 text-gray-700 py-3 px-6 rounded-lg font-semibold hover:border-blue-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-300 text-center">
                <i class='bx bx-user-plus mr-2'></i>
                Create New Account
            </a>
            
            <!-- Demo Credentials -->
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h3 class="text-sm font-semibold text-blue-900 mb-3 flex items-center">
                    <i class='bx bx-info-circle mr-2 text-blue-600'></i>
                    Demo Credentials
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-700">Admin:</span>
                        <span class="font-mono text-blue-600">admin@bmmb.com</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-700">Moderator:</span>
                        <span class="font-mono text-blue-600">moderator@bmmb.com</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-700">User:</span>
                        <span class="font-mono text-blue-600">jane.smith@example.com</span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-blue-200 text-center">
                        <span class="font-bold text-blue-900">Password: <span class="font-mono text-blue-600">password</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
