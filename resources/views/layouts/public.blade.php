<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'BMMB Digital Forms')</title>
    
    <!-- Tailwind CSS CDN - Play CDN (More Reliable) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                        'slide-down': 'slideDown 0.3s ease-out',
                    },
                    fontFamily: {
                        'sans': ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Additional Tailwind Utilities -->
    <script>
        // Force Tailwind to scan all classes
        if (typeof tailwind !== 'undefined') {
            tailwind.config.safelist = [
                'animate-fade-in-up',
                'animate-slide-down',
                'form-section',
                'form-card',
                'btn-primary',
                'form-input',
                'step-indicator',
                'nav-link',
                'dropdown-item',
                'mobile-nav-item',
                'mobile-form-item'
            ];
        }
    </script>
    
    <!-- Boxicons CDN -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom CSS -->
    <style>
        /* Ensure Tailwind base styles are applied */
        *, ::before, ::after {
            box-sizing: border-box;
            border-width: 0;
            border-style: solid;
            border-color: #e5e7eb;
        }
        
        html {
            line-height: 1.5;
            -webkit-text-size-adjust: 100%;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
        }
        
        body {
            margin: 0;
            line-height: inherit;
        }
        
        [x-cloak] { display: none !important; }
        
        .form-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .form-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .form-input {
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }
        
        .step-indicator {
            position: relative;
        }
        
        .step-indicator::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 100%;
            height: 2px;
            background: #e5e7eb;
            transform: translateY(-50%);
        }
        
        .step-indicator:last-child::after {
            display: none;
        }
        
        .step-indicator.active::after {
            background: #667eea;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }
        
        .animate-fade-in-up:nth-child(1) { animation-delay: 0.1s; }
        .animate-fade-in-up:nth-child(2) { animation-delay: 0.2s; }
        .animate-fade-in-up:nth-child(3) { animation-delay: 0.3s; }
        .animate-fade-in-up:nth-child(4) { animation-delay: 0.4s; }
        
        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #5a67d8;
        }
        
        /* Navigation Styles */
        .nav-link {
            @apply text-gray-700 hover:text-blue-600 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 flex items-center;
        }
        
        .nav-link:hover {
            @apply bg-blue-50 transform scale-105;
        }
        
        .nav-link.active {
            @apply text-blue-600 bg-blue-50 font-semibold;
        }
        
        .dropdown-item {
            @apply flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors duration-200;
        }
        
        .dropdown-item:hover {
            @apply transform translate-x-1;
        }
        
        .mobile-nav-item {
            @apply flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg transition-all duration-200;
        }
        
        .mobile-nav-item.active {
            @apply text-blue-600 bg-blue-50 font-semibold;
        }
        
        .mobile-form-item {
            @apply flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 hover:text-blue-600 rounded-lg transition-all duration-200;
        }
        
        .mobile-form-item.active {
            @apply text-blue-600 bg-blue-50 font-semibold;
        }
        
        .mobile-form-item:hover {
            @apply transform translate-x-2;
        }
        
        /* Sticky header animation */
        .sticky {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        /* Dropdown animation */
        [x-cloak] { display: none !important; }
        
        /* Mobile menu slide animation */
        .mobile-menu-enter {
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen" x-data="{ mobileMenuOpen: false }">
    <!-- Header -->
    <header class="bg-white/95 backdrop-blur-md shadow-xl border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('public.home') }}" class="flex items-center space-x-4 group">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-600 via-purple-600 to-indigo-700 rounded-2xl flex items-center justify-center group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-blue-500/25">
                            <i class='bx bx-file-text text-white text-3xl'></i>
                        </div>
                        <div class="group-hover:translate-x-1 transition-transform duration-300">
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-purple-800 bg-clip-text text-transparent group-hover:from-blue-600 group-hover:to-purple-600 transition-all duration-300">
                                BMMB Digital Forms
                            </h1>
                            <p class="text-sm text-gray-500 font-medium">Streamlined Form Management</p>
                        </div>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <nav class="hidden xl:flex items-center space-x-2">
                    <a href="{{ route('public.home') }}" class="relative px-4 py-2.5 text-sm font-semibold text-gray-700 hover:text-blue-600 rounded-xl hover:bg-blue-50 transition-all duration-300 group {{ request()->routeIs('public.home') ? 'text-blue-600 bg-blue-50' : '' }}">
                        <i class='bx bx-home mr-2 text-lg'></i>
                        <span>Home</span>
                        <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>
                    
                    <!-- Forms Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center px-4 py-2.5 text-sm font-semibold text-gray-700 hover:text-blue-600 rounded-xl hover:bg-blue-50 transition-all duration-300 group">
                            <i class='bx bx-file-blank mr-2 text-lg'></i>
                            <span>Forms</span>
                            <i class='bx bx-chevron-down ml-2 text-sm transition-transform duration-200' :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" x-cloak class="absolute right-0 mt-3 w-80 bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-100 py-3 z-50">
                            <div class="px-3 py-2 border-b border-gray-100">
                                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Available Forms</h3>
                            </div>
                            <div class="py-2">
                                <a href="{{ route('public.forms.raf') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-50 hover:text-green-700 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-200">
                                        <i class='bx bx-money text-white text-lg'></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">Remittance Application</div>
                                        <div class="text-xs text-gray-500">Financial transactions & transfers</div>
                                    </div>
                                </a>
                                <a href="{{ route('public.forms.dar') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-200">
                                        <i class='bx bx-data text-white text-lg'></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">Data Access Request</div>
                                        <div class="text-xs text-gray-500">Personal data access & privacy</div>
                                    </div>
                                </a>
                                <a href="{{ route('public.forms.dcr') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-orange-50 hover:text-orange-700 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-200">
                                        <i class='bx bx-edit text-white text-lg'></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">Data Correction</div>
                                        <div class="text-xs text-gray-500">Update & correct personal data</div>
                                    </div>
                                </a>
                                <a href="{{ route('public.forms.srf') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-200">
                                        <i class='bx bx-cog text-white text-lg'></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">Service Request</div>
                                        <div class="text-xs text-gray-500">General services & support</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <a href="#features" class="px-4 py-2.5 text-sm font-semibold text-gray-700 hover:text-blue-600 rounded-xl hover:bg-blue-50 transition-all duration-300 group">
                        <i class='bx bx-star mr-2 text-lg'></i>
                        <span>Features</span>
                    </a>
                    <a href="#contact" class="px-4 py-2.5 text-sm font-semibold text-gray-700 hover:text-blue-600 rounded-xl hover:bg-blue-50 transition-all duration-300 group">
                        <i class='bx bx-phone mr-2 text-lg'></i>
                        <span>Contact</span>
                    </a>
                </nav>
                
                <!-- Action Buttons -->
                <div class="hidden xl:flex items-center space-x-3">
                    <a href="{{ route('login') }}" class="px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-blue-600 transition-colors duration-300">
                        Sign In
                    </a>
                    <a href="{{ route('public.forms.raf') }}" class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 text-white px-6 py-2.5 rounded-xl font-semibold hover:shadow-xl hover:shadow-blue-500/25 transform hover:scale-105 transition-all duration-300 flex items-center">
                        <i class='bx bx-plus mr-2 text-lg'></i>
                        Get Started
                    </a>
                </div>
                
                <!-- Mobile menu button -->
                <div class="xl:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-3 text-gray-700 hover:text-blue-600 rounded-xl hover:bg-blue-50 transition-all duration-300">
                        <i class='bx bx-menu text-2xl' x-show="!mobileMenuOpen"></i>
                        <i class='bx bx-x text-2xl' x-show="mobileMenuOpen"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div x-show="mobileMenuOpen" x-cloak class="xl:hidden bg-white/95 backdrop-blur-md border-t border-gray-100 shadow-xl">
            <div class="px-6 py-6 space-y-4">
                <!-- Mobile Home Link -->
                <a href="{{ route('public.home') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-xl transition-all duration-300 group {{ request()->routeIs('public.home') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class='bx bx-home text-xl mr-4'></i>
                    <span class="text-lg font-semibold">Home</span>
                </a>
                
                <!-- Mobile Forms Section -->
                <div class="border-t border-gray-100 pt-4">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4 px-4">Available Forms</div>
                    <div class="space-y-2">
                        <a href="{{ route('public.forms.raf') }}" class="flex items-center px-4 py-4 text-gray-700 hover:bg-green-50 hover:text-green-700 rounded-xl transition-all duration-300 group {{ request()->routeIs('public.forms.raf') ? 'bg-green-50 text-green-700' : '' }}">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-200">
                                <i class='bx bx-money text-white text-xl'></i>
                            </div>
                            <div>
                                <div class="font-semibold text-base">Remittance Application</div>
                                <div class="text-sm text-gray-500">Financial transactions & transfers</div>
                            </div>
                        </a>
                        <a href="{{ route('public.forms.dar') }}" class="flex items-center px-4 py-4 text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-xl transition-all duration-300 group {{ request()->routeIs('public.forms.dar') ? 'bg-blue-50 text-blue-700' : '' }}">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-200">
                                <i class='bx bx-data text-white text-xl'></i>
                            </div>
                            <div>
                                <div class="font-semibold text-base">Data Access Request</div>
                                <div class="text-sm text-gray-500">Personal data access & privacy</div>
                            </div>
                        </a>
                        <a href="{{ route('public.forms.dcr') }}" class="flex items-center px-4 py-4 text-gray-700 hover:bg-orange-50 hover:text-orange-700 rounded-xl transition-all duration-300 group {{ request()->routeIs('public.forms.dcr') ? 'bg-orange-50 text-orange-700' : '' }}">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-200">
                                <i class='bx bx-edit text-white text-xl'></i>
                            </div>
                            <div>
                                <div class="font-semibold text-base">Data Correction</div>
                                <div class="text-sm text-gray-500">Update & correct personal data</div>
                            </div>
                        </a>
                        <a href="{{ route('public.forms.srf') }}" class="flex items-center px-4 py-4 text-gray-700 hover:bg-purple-50 hover:text-purple-700 rounded-xl transition-all duration-300 group {{ request()->routeIs('public.forms.srf') ? 'bg-purple-50 text-purple-700' : '' }}">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform duration-200">
                                <i class='bx bx-cog text-white text-xl'></i>
                            </div>
                            <div>
                                <div class="font-semibold text-base">Service Request</div>
                                <div class="text-sm text-gray-500">General services & support</div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- Mobile Action Buttons -->
                <div class="border-t border-gray-100 pt-4 space-y-3">
                    <a href="{{ route('login') }}" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-xl transition-all duration-300">
                        <i class='bx bx-log-in text-xl mr-4'></i>
                        <span class="text-lg font-semibold">Sign In</span>
                    </a>
                    <a href="{{ route('public.forms.raf') }}" class="w-full bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 text-white px-6 py-4 rounded-xl font-semibold text-center hover:shadow-xl hover:shadow-blue-500/25 transform hover:scale-105 transition-all duration-300 flex items-center justify-center">
                        <i class='bx bx-plus mr-2 text-xl'></i>
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0">
                <div class="text-gray-400 text-sm">
                    © {{ date('Y') }} BMMB Digital Forms. All rights reserved.
                </div>
                <div class="flex space-x-6 text-sm">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Privacy Policy</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Terms of Service</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        document.addEventListener('alpine:init', () => {
            Alpine.data('mobileMenu', () => ({
                mobileMenuOpen: false
            }))
        })
        
        // Form validation and submission
        function validateForm(formId) {
            const form = document.getElementById(formId);
            if (!form) return false;
            
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            return isValid;
        }
        
        // Form submission with SweetAlert
        function submitForm(formId, successMessage = 'Form submitted successfully!') {
            if (!validateForm(formId)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fill in all required fields.',
                    confirmButtonColor: '#667eea'
                });
                return false;
            }
            
            Swal.fire({
                title: 'Submitting Form...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Simulate form submission (replace with actual form submission)
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: successMessage,
                    confirmButtonColor: '#667eea'
                }).then(() => {
                    document.getElementById(formId).reset();
                });
            }, 2000);
            
            return true;
        }
        
        // Step navigation for multi-step forms
        function nextStep(currentStep, nextStep) {
            if (validateForm(`step-${currentStep}`)) {
                document.getElementById(`step-${currentStep}`).classList.add('hidden');
                document.getElementById(`step-${nextStep}`).classList.remove('hidden');
                
                // Update step indicators
                document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
                    if (index < nextStep - 1) {
                        indicator.classList.add('active');
                    } else {
                        indicator.classList.remove('active');
                    }
                });
            }
        }
        
        function prevStep(currentStep, prevStep) {
            document.getElementById(`step-${currentStep}`).classList.add('hidden');
            document.getElementById(`step-${prevStep}`).classList.remove('hidden');
            
            // Update step indicators
            document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
                if (index < prevStep - 1) {
                    indicator.classList.add('active');
                } else {
                    indicator.classList.remove('active');
                }
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>
