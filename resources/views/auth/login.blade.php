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

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@section('content')
    <!-- Login Section -->
    <section class="relative min-h-screen flex items-start bg-gray-100 dark:bg-gray-900 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="max-w-md mx-auto">
                <!-- Redirect to MAP Card -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-6 border border-gray-200 dark:border-gray-700 shadow-lg text-center">
                    <!-- Header -->
                    <div class="mb-6">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-4">
                            <i class='bx bx-log-in text-3xl text-blue-600 dark:text-blue-400'></i>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                            Session Expired
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                            Please log in through MAP to continue
                        </p>
                    </div>

                    <!-- Loading Indicator -->
                    <div class="flex flex-col items-center space-y-4 mb-6">
                        <div class="loading-spinner"></div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Redirecting to MAP login...
                        </p>
                    </div>

                    <!-- Manual Redirect Button -->
                    <div class="space-y-3">
                        @php
                            // Get MAP redirect URL from config
                            $mapRedirectUrl = config('map.redirect_url');

                            // If the config URL is HTTP but we're on HTTPS, force HTTPS
                            if (request()->secure() && str_starts_with($mapRedirectUrl, 'http://')) {
                                $mapRedirectUrl = str_replace('http://', 'https://', $mapRedirectUrl);
                            }
                        @endphp
                        <a href="{{ $mapRedirectUrl }}"
                            class="w-full flex justify-center items-center py-3 px-4 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-all duration-300 transform hover:scale-[1.02] text-sm shadow-md hover:shadow-lg">
                            <i class='bx bx-log-in mr-2'></i>
                            Go to MAP Login
                        </a>

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            You will be redirected automatically in <span id="countdown">3</span> seconds
                        </p>
                    </div>

                    <!-- Info Box -->
                    <div
                        class="mt-5 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                        <div class="flex items-start">
                            <i class='bx bx-info-circle text-blue-600 dark:text-blue-400 mr-2 mt-0.5'></i>
                            <div class="text-left">
                                <h3 class="text-xs font-semibold text-blue-900 dark:text-blue-300 mb-1">
                                    Why MAP Login?
                                </h3>
                                <p class="text-xs text-gray-700 dark:text-gray-300">
                                    eForm uses Single Sign-On (SSO) with MAP. All authentication is handled through the MAP
                                    application for security.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            // Auto redirect countdown
            let seconds = 3;
            const countdownEl = document.getElementById('countdown');
            const mapLoginUrl = "{{ $mapRedirectUrl }}";

            const countdown = setInterval(() => {
                seconds--;
                if (countdownEl) {
                    countdownEl.textContent = seconds;
                }
                if (seconds <= 0) {
                    clearInterval(countdown);
                    window.location.href = mapLoginUrl;
                }
            }, 1000);
        </script>
    @endpush
@endsection