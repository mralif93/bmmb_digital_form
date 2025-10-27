@extends('layouts.public')

@section('title', 'Forgot Password - BMMB Digital Forms')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-purple-50 to-indigo-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-xl">
        <!-- Forgot Password Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <!-- Logo & Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class='bx bx-key text-white text-3xl'></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">
                    Reset Your Password
                </h2>
                <p class="text-gray-600">
                    Enter your email address and we'll send you a link to reset your password
                </p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class='bx bx-check-circle text-green-600 text-xl mr-3'></i>
                        <p class="text-green-800 text-sm">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <!-- Reset Form -->
            <form class="space-y-6" action="{{ route('password.email') }}" method="POST">
                @csrf
                
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
                
                <!-- Send Reset Link Button -->
                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-[1.02]">
                    <i class='bx bx-send mr-2'></i>
                    Send Reset Link
                </button>
            </form>
            
            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500">
                        Remember your password?
                    </span>
                </div>
            </div>
            
            <!-- Back to Login -->
            <a href="{{ route('login') }}" class="block w-full border-2 border-gray-300 text-gray-700 py-3 px-6 rounded-lg font-semibold hover:border-blue-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-300 text-center">
                <i class='bx bx-arrow-back mr-2'></i>
                Back to Login
            </a>
        </div>
    </div>
</div>
@endsection

