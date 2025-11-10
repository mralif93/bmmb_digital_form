@extends('layouts.public')

@section('title', 'Access Denied - BMMB Digital Forms')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <div class="w-20 h-20 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class='bx bx-error-circle text-red-600 dark:text-red-400 text-4xl'></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Access Denied
            </h2>
            <p class="text-gray-600 dark:text-gray-400 text-sm">
                {{ $exception->getMessage() ?: 'You do not have permission to access this resource.' }}
            </p>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-center space-x-2 text-gray-500 dark:text-gray-400 mb-4">
                <i class='bx bx-info-circle text-lg'></i>
                <span class="text-sm">This QR code is not active or has been deactivated.</span>
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">
                Please contact the administrator if you believe this is an error.
            </p>
            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 dark:bg-orange-500 hover:bg-orange-700 dark:hover:bg-orange-600 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class='bx bx-home mr-2'></i>
                Return to Home
            </a>
        </div>
    </div>
</div>
@endsection

