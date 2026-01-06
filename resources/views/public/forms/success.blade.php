@extends('layouts.public')

@section('title', 'Submission Successful')

@section('content')
    <section
        class="min-h-screen flex items-center justify-center py-8 md:py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-green-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="max-w-xl md:max-w-3xl w-full">
            <!-- Success Card -->
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-green-100 dark:border-green-800">
                <!-- Success Header with Animation -->
                <div
                    class="bg-gradient-to-r from-green-500 to-emerald-600 pt-8 pb-10 md:pt-12 md:pb-14 px-6 md:px-12 text-center relative overflow-hidden">
                    <!-- Animated Background Circles -->
                    <div class="absolute inset-0 overflow-hidden opacity-30">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/20 rounded-full animate-pulse"></div>
                        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-white/20 rounded-full animate-pulse"
                            style="animation-delay: 500ms;"></div>
                    </div>

                    <!-- Success Icon with Animation -->
                    <div class="relative z-10 mb-5 md:mb-6">
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 md:w-24 md:h-24 bg-white rounded-full shadow-lg animate-bounce">
                            <svg class="w-10 h-10 md:w-12 md:h-12 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                        </div>
                    </div>

                    <!-- Success Title -->
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2 md:mb-3 tracking-tight">
                        Submission Received!
                    </h1>
                    <p class="text-green-50 text-base sm:text-lg md:text-xl font-medium opacity-90 max-w-2xl mx-auto">
                        Your {{ $formName }} has been successfully submitted.
                    </p>
                </div>

                <!-- Submission Details -->
                <div class="px-6 py-6 sm:px-8 sm:py-8 md:px-10 md:py-10 space-y-6 md:space-y-8">
                    <!-- Reference Number (Prominent Display) -->
                    <div
                        class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-5 md:p-6 border border-blue-100 dark:border-blue-800 shadow-inner">
                        <div
                            class="flex flex-col sm:flex-row items-center justify-between gap-4 md:gap-6 text-center sm:text-left">
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-xs md:text-sm font-bold text-blue-600 dark:text-blue-400 mb-1.5 uppercase tracking-wider">
                                    Reference Number
                                </p>
                                <p class="text-lg sm:text-xl md:text-2xl font-black text-gray-800 dark:text-gray-100 font-mono tracking-tight break-all select-all leading-tight"
                                    id="referenceNumber">
                                    {{ $referenceNumber ?? 'N/A' }}
                                </p>
                                <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    Please save this for your records
                                </p>
                            </div>
                            <button onclick="copyToClipboard('{{ $referenceNumber }}')"
                                class="w-full sm:w-auto px-6 py-3 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-gray-600 rounded-lg transition-all duration-200 shadow-sm flex items-center justify-center gap-2.5 group whitespace-nowrap">
                                <i class='bx bx-copy text-lg md:text-xl'></i>
                                <span class="text-sm md:text-base font-bold">Copy</span>
                            </button>
                        </div>
                    </div>

                    <!-- Submission Info Grid -->
                    <div class="grid grid-cols-2 gap-4 md:gap-6">
                        <!-- Submission Date & Time -->
                        <div
                            class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 md:p-5 border border-gray-100 dark:border-gray-700">
                            <div class="flex flex-col items-center sm:items-start text-center sm:text-left">
                                <div class="mb-2 sm:mb-1 sm:flex sm:items-center sm:gap-2">
                                    <i class='bx bx-calendar text-gray-400 dark:text-gray-500 text-lg md:text-xl'></i>
                                    <p
                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Date
                                    </p>
                                </div>
                                <p class="text-sm md:text-lg font-bold text-gray-900 dark:text-gray-100 mt-0.5">
                                    {{ now()->format('d M Y') }}
                                </p>
                                <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400">
                                    {{ now()->format('h:i A') }}
                                </p>
                            </div>
                        </div>

                        <!-- Status -->
                        <div
                            class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 md:p-5 border border-green-100 dark:border-green-800">
                            <div class="flex flex-col items-center sm:items-start text-center sm:text-left">
                                <div class="mb-2 sm:mb-1 sm:flex sm:items-center sm:gap-2">
                                    <i class='bx bx-check-circle text-green-500 dark:text-green-400 text-lg md:text-xl'></i>
                                    <p
                                        class="text-xs font-semibold text-green-600 dark:text-green-400 uppercase tracking-wide">
                                        Status
                                    </p>
                                </div>
                                <p class="text-sm md:text-lg font-bold text-green-700 dark:text-green-300 mt-0.5">
                                    Received
                                </p>
                                <p class="text-xs md:text-sm text-green-600 dark:text-green-400 opacity-80">
                                    Processing soon
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- What's Next Section -->
                    <div
                        class="bg-amber-50 dark:bg-amber-900/10 rounded-xl p-5 md:p-6 border border-amber-100 dark:border-amber-800/50">
                        <h3
                            class="text-sm md:text-base font-bold text-amber-900 dark:text-amber-300 mb-3 flex items-center gap-2">
                            <i class='bx bx-time-five text-amber-600 text-lg'></i> What's Next?
                        </h3>
                        <ul class="space-y-2.5 text-sm md:text-base text-amber-800 dark:text-amber-200/80">
                            <li class="flex items-start gap-2.5">
                                <span class="text-amber-500 mt-1">•</span>
                                <span>Please wait for our officer to call you.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-amber-500 mt-1">•</span>
                                <span>Show this reference number to the officer when called.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3 pt-2">
                        <!-- Preview PDF Button -->
                        <a href="{{ route('public.forms.pdf.preview', $submissionToken) }}" target="_blank"
                            class="w-full flex items-center justify-center gap-2.5 px-6 py-4 bg-gray-900 hover:bg-black text-white rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl font-bold text-base md:text-lg group border border-transparent">
                            <i class='bx bx-file text-xl md:text-2xl mb-0.5'></i>
                            <span>Download / Preview PDF</span>
                        </a>

                        <!-- Return Home Button -->
                        <a href="{{ route('home') }}"
                            class="w-full flex items-center justify-center gap-2.5 px-6 py-3.5 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 hover:border-gray-300 rounded-xl transition-all duration-200 font-semibold text-sm md:text-base shadow-sm">
                            <i class='bx bx-arrow-back text-lg md:text-xl'></i>
                            <span>Return to Home</span>
                        </a>
                    </div>
                </div>

                <!-- Footer Support Link -->
                <div
                    class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 md:px-8 md:py-6 border-t border-gray-100 dark:border-gray-800 text-center">
                    <p class="text-xs md:text-sm text-gray-400 dark:text-gray-500">
                        Need help? <a href="tel:+60321611000"
                            class="underline hover:text-gray-600 dark:hover:text-gray-400">Contact Support</a>
                    </p>
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