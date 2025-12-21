@extends('layouts.public')

@section('title', 'Submission Successful')

@section('content')
    <section
        class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-green-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="max-w-3xl w-full">
            <!-- Success Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden border border-green-200 dark:border-green-700">
                <!-- Success Header with Animation -->
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-10 text-center relative overflow-hidden">
                    <!-- Animated Background Circles -->
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full animate-pulse"></div>
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-white/10 rounded-full animate-pulse"
                            style="animation-delay: 500ms;"></div>
                    </div>

                    <!-- Success Icon with Animation -->
                    <div class="relative z-10 mb-6">
                        <div
                            class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-full shadow-xl animate-bounce">
                            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                        </div>
                    </div>

                    <!-- Success Title -->
                    <h1 class="text-3xl font-bold text-white mb-3 tracking-tight">
                        Submission Successful!
                    </h1>
                    <p class="text-green-50 text-lg font-medium">
                        Your {{ $formName }} has been received
                    </p>
                </div>

                <!-- Submission Details -->
                <div class="px-8 py-8 space-y-6">
                    <!-- Reference Number (Prominent Display) -->
                    <div
                        class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600 rounded-xl p-6 border-2 border-blue-200 dark:border-blue-700 shadow-md">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <p
                                    class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2 uppercase tracking-wider">
                                    Reference Number
                                </p>
                                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 font-mono tracking-wide select-all"
                                    id="referenceNumber">
                                    {{ $referenceNumber ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                    Please save this number for your records
                                </p>
                            </div>
                            <button onclick="copyToClipboard('{{ $referenceNumber }}')"
                                class="ml-4 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2 group">
                                <i class='bx bx-copy text-lg'></i>
                                <span class="text-sm font-semibold">Copy</span>
                            </button>
                        </div>
                    </div>

                    <!-- Submission Info Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Submission Date & Time -->
                        <div
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                        <i class='bx bx-calendar text-purple-600 dark:text-purple-400 text-xl'></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p
                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">
                                        Submitted On
                                    </p>
                                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                        {{ now()->format('d M Y, h:i A') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                        <i class='bx bx-check-circle text-green-600 dark:text-green-400 text-xl'></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p
                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">
                                        Status
                                    </p>
                                    <p class="text-sm font-bold text-green-600 dark:text-green-400">
                                        Submitted Successfully
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- What's Next Section -->
                    <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 rounded-r-lg p-5">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class='bx bx-info-circle text-amber-600 dark:text-amber-400 text-2xl'></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-bold text-amber-900 dark:text-amber-300 mb-2">
                                    What Happens Next?
                                </h3>
                                <ul class="space-y-2 text-sm text-amber-800 dark:text-amber-200">
                                    <li class="flex items-start">
                                        <i
                                            class='bx bx-check text-amber-600 dark:text-amber-400 mt-0.5 mr-2 flex-shrink-0'></i>
                                        <span>Our team will review your submission within <strong>3-5 business
                                                days</strong></span>
                                    </li>
                                    <li class="flex items-start">
                                        <i
                                            class='bx bx-check text-amber-600 dark:text-amber-400 mt-0.5 mr-2 flex-shrink-0'></i>
                                        <span>You will receive an email confirmation shortly</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i
                                            class='bx bx-check text-amber-600 dark:text-amber-400 mt-0.5 mr-2 flex-shrink-0'></i>
                                        <span>If we need additional information, we'll contact you using the details you
                                            provided</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4">
                        <!-- Preview PDF Button -->
                        <a href="{{ route('public.forms.pdf.preview', $submissionToken) }}" target="_blank"
                            class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-all duration-300 shadow-md hover:shadow-lg font-semibold group">
                            <i class='bx bx-show text-lg group-hover:scale-110 transition-transform'></i>
                            <span>Preview PDF</span>
                        </a>

                        <!-- Return Home Button -->
                        <a href="{{ route('home') }}"
                            class="flex-1 flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg transition-all duration-300 shadow-md hover:shadow-lg font-semibold group">
                            <i class='bx bx-home text-lg group-hover:scale-110 transition-transform'></i>
                            <span>Return to Home</span>
                        </a>
                    </div>

                    <!-- Contact Support Info -->
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                        <p class="text-center text-sm text-gray-600 dark:text-gray-400">
                            Need help? Contact us at
                            <a href="mailto:support@bmmb.com.my"
                                class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">support@bmmb.com.my</a>
                            or call
                            <a href="tel:+60321611000"
                                class="text-blue-600 dark:text-blue-400 hover:underline font-semibold">03-2161 1000</a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Additional Tips Card -->
            <div
                class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class='bx bx-bulb text-yellow-500 text-3xl'></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-2">
                            💡 Helpful Tips
                        </h3>
                        <ul class="space-y-1.5 text-xs text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">•</span>
                                <span>Save or screenshot your reference number for future inquiries</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">•</span>
                                <span>Check your email (including spam folder) for confirmation</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-green-500 mr-2">•</span>
                                <span>You can print this page for your records before leaving</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Print Styles -->
    <style>
        @media print {

            /* Hide unnecessary elements when printing */
            header,
            footer,
            nav,
            .no-print {
                display: none !important;
            }

            /* Optimize for printing */
            body {
                background: white !important;
            }

            .bg-gradient-to-br,
            .bg-gradient-to-r {
                background: white !important;
                color: black !important;
            }

            /* Show reference number prominently */
            #referenceNumber {
                font-size: 24px;
                color: black;
            }

            /* Remove shadows and borders for cleaner print */
            .shadow-2xl,
            .shadow-xl,
            .shadow-lg,
            .shadow-md {
                box-shadow: none !important;
            }

            /* Ensure all text is black */
            * {
                color: black !important;
            }

            /* Keep borders for structure */
            .border {
                border-color: #cccccc !important;
            }
        }

        /* Animation for success icon */
        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-bounce {
            animation: bounce 2s infinite;
        }
    </style>

    <!-- JavaScript for Interactions -->
    <script>
        // Copy reference number to clipboard
        function copyToClipboard(text) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    // Show success toast
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Reference number copied!',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }).catch(err => {
                    fallbackCopyToClipboard(text);
                });
            } else {
                fallbackCopyToClipboard(text);
            }
        }

        // Fallback copy method for older browsers
        function fallbackCopyToClipboard(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                document.execCommand('copy');
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Reference number copied!',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to copy',
                    text: 'Please manually copy the reference number',
                    showConfirmButton: true
                });
            }

            document.body.removeChild(textArea);
        }

        // Auto-select reference number on click for easy copying
        document.getElementById('referenceNumber').addEventListener('click', function () {
            const range = document.createRange();
            range.selectNodeContents(this);
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
        });

        // Confetti effect on page load (optional celebration)
        document.addEventListener('DOMContentLoaded', function () {
            // Simple celebration - you can add confetti library if desired
            console.log('🎉 Form submitted successfully!');
        });
    </script>
@endsection