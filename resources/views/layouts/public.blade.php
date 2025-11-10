<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BMMB Digital Forms')</title>
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN - Play CDN (More Reliable) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    @php
        $settings = \Illuminate\Support\Facades\Cache::get('system_settings', [
            'default_theme' => 'light',
            'primary_color' => '#FE8000',
        ]);
        $primaryColor = $settings['primary_color'] ?? '#FE8000';
        $defaultTheme = $settings['default_theme'] ?? 'light';
        $colorShades = \App\Helpers\ColorHelper::generateColorShades($primaryColor);
    @endphp
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '{{ $colorShades[50] }}',
                            100: '{{ $colorShades[100] }}',
                            200: '{{ $colorShades[200] }}',
                            300: '{{ $colorShades[300] }}',
                            400: '{{ $colorShades[400] }}',
                            500: '{{ $colorShades[500] }}',
                            600: '{{ $colorShades[600] }}',
                            700: '{{ $colorShades[700] }}',
                            800: '{{ $colorShades[800] }}',
                            900: '{{ $colorShades[900] }}',
                        }
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.6s ease-out forwards',
                        'slide-down': 'slideDown 0.3s ease-out',
                    },
                    fontFamily: {
                        'sans': ['Poppins', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif'],
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
            font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
        }
        
        body {
            margin: 0;
            line-height: inherit;
            font-size: 14px;
            font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        /* Dark mode body background */
        body.dark {
            background: #111827;
        }
        
        .dark body {
            background: #111827;
        }
        
        [x-cloak] { display: none !important; }
        
        .form-section {
            background: {{ $primaryColor }};
        }
        
        .form-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .dark .form-card {
            background: rgba(31, 41, 55, 0.95);
        }
        
        .btn-primary {
            background: {{ $primaryColor }};
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px {{ $primaryColor }}66;
            background: {{ $colorShades[600] }};
        }
        
        .form-input {
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px {{ $primaryColor }}33;
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
            background: {{ $primaryColor }};
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
            background: {{ $primaryColor }};
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: {{ $colorShades[600] }};
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
        
        .dark .sticky {
            background: rgba(31, 41, 55, 0.95);
        }
        
        /* Dark mode scrollbar */
        .dark ::-webkit-scrollbar-track {
            background: #1f2937;
        }
        
        .dark ::-webkit-scrollbar-thumb {
            background: #4b5563;
        }
        
        .dark ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
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
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex flex-col" x-data="{ mobileMenuOpen: false }">
    <!-- Header -->
    <header class="backdrop-blur-md shadow-sm border-b border-gray-200 dark:border-gray-700 bg-white/98 dark:bg-gray-800/98 sticky top-0 z-50 flex-shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 md:h-18">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                        <img src="{{ asset('assets/images/Logo_BMMB_Full.png') }}" alt="BMMB Logo" class="h-10 md:h-12 object-contain group-hover:scale-105 transition-transform duration-300">
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <nav class="hidden xl:flex items-center space-x-1">
                    <a href="{{ route('home') }}" class="relative px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all duration-300 group {{ request()->routeIs('home') ? 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20' : '' }}">
                        <i class='bx bx-home mr-2 text-base'></i>
                        <span>Home</span>
                    </a>
                    
                    <!-- Forms Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all duration-300 group">
                            <i class='bx bx-file-blank mr-2 text-base'></i>
                            <span>Forms</span>
                            <i class='bx bx-chevron-down ml-1.5 text-sm transition-transform duration-200' :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 backdrop-blur-md rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 py-2 z-50">
                            <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Available Forms</h3>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('public.forms.raf') }}" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/30 hover:text-green-700 dark:hover:text-green-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200 shadow-md">
                                        <i class='bx bx-money text-white text-lg'></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">Remittance Application</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Financial transactions & transfers</div>
                                    </div>
                                </a>
                                <a href="{{ route('public.forms.dar') }}" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-700 dark:hover:text-blue-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200 shadow-md">
                                        <i class='bx bx-data text-white text-lg'></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">Data Access Request</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Personal data access & privacy</div>
                                    </div>
                                </a>
                                <a href="{{ route('public.forms.dcr') }}" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-700 dark:hover:text-primary-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200 shadow-md">
                                        <i class='bx bx-edit text-white text-lg'></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">Data Correction</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Update & correct personal data</div>
                                    </div>
                                </a>
                                <a href="{{ route('public.forms.srf') }}" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400 transition-all duration-200 group">
                                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200 shadow-md">
                                        <i class='bx bx-cog text-white text-lg'></i>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-sm">Service Request</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">General services & support</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </nav>
                
                <!-- Action Buttons -->
                <div class="hidden xl:flex items-center space-x-3">
                    <!-- Dark Mode Toggle -->
                    <button onclick="toggleDarkMode()" class="p-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all duration-300" title="Toggle Dark Mode">
                        <i class='bx bx-moon text-xl dark:hidden' id="darkModeIcon"></i>
                        <i class='bx bx-sun text-xl hidden dark:inline-block' id="lightModeIcon"></i>
                    </button>
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-white bg-primary-500 hover:bg-primary-600 rounded-lg transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                        Sign In
                    </a>
                </div>
                
                <!-- Mobile menu button -->
                <div class="xl:hidden flex items-center space-x-2">
                    <!-- Dark Mode Toggle (Mobile) -->
                    <button onclick="toggleDarkMode()" class="p-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all duration-300" title="Toggle Dark Mode">
                        <i class='bx bx-moon text-xl dark:hidden' id="darkModeIconMobile"></i>
                        <i class='bx bx-sun text-xl hidden dark:inline-block' id="lightModeIconMobile"></i>
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all duration-300">
                        <i class='bx bx-menu text-xl' x-show="!mobileMenuOpen"></i>
                        <i class='bx bx-x text-xl' x-show="mobileMenuOpen"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div x-show="mobileMenuOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" class="xl:hidden backdrop-blur-md border-t border-gray-200 dark:border-gray-700 shadow-xl bg-white/98 dark:bg-gray-800/98">
            <div class="px-4 py-4 space-y-1">
                <!-- Mobile Home Link -->
                <a href="{{ route('home') }}" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 rounded-lg transition-all duration-300 group {{ request()->routeIs('home') ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400' : '' }}">
                    <i class='bx bx-home text-lg mr-3'></i>
                    <span class="text-base font-medium">Home</span>
                </a>
                
                <!-- Mobile Forms Section -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
                    <div class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 px-4">Available Forms</div>
                    <div class="space-y-1">
                        <a href="{{ route('public.forms.raf') }}" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/30 hover:text-green-700 dark:hover:text-green-400 rounded-lg transition-all duration-300 group {{ request()->routeIs('public.forms.raf') ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400' : '' }}">
                            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200 shadow-md">
                                <i class='bx bx-money text-white text-lg'></i>
                            </div>
                            <div>
                                <div class="font-semibold text-sm">Remittance Application</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Financial transactions & transfers</div>
                            </div>
                        </a>
                        <a href="{{ route('public.forms.dar') }}" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-700 dark:hover:text-blue-400 rounded-lg transition-all duration-300 group {{ request()->routeIs('public.forms.dar') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : '' }}">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200 shadow-md">
                                <i class='bx bx-data text-white text-lg'></i>
                            </div>
                            <div>
                                <div class="font-semibold text-sm">Data Access Request</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Personal data access & privacy</div>
                            </div>
                        </a>
                        <a href="{{ route('public.forms.dcr') }}" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-700 dark:hover:text-primary-400 rounded-lg transition-all duration-300 group {{ request()->routeIs('public.forms.dcr') ? 'bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400' : '' }}">
                            <div class="w-10 h-10 bg-primary-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200 shadow-md">
                                <i class='bx bx-edit text-white text-lg'></i>
                            </div>
                            <div>
                                <div class="font-semibold text-sm">Data Correction</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Update & correct personal data</div>
                            </div>
                        </a>
                        <a href="{{ route('public.forms.srf') }}" class="flex items-center px-4 py-3 text-gray-700 dark:text-gray-300 hover:bg-purple-50 dark:hover:bg-purple-900/30 hover:text-purple-700 dark:hover:text-purple-400 rounded-lg transition-all duration-300 group {{ request()->routeIs('public.forms.srf') ? 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' : '' }}">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3 group-hover:scale-110 transition-transform duration-200 shadow-md">
                                <i class='bx bx-cog text-white text-lg'></i>
                            </div>
                            <div>
                                <div class="font-semibold text-sm">Service Request</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">General services & support</div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- Mobile Action Buttons -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-3 mt-3">
                    <a href="{{ route('login') }}" class="flex items-center justify-center px-4 py-2.5 text-sm font-semibold text-white bg-primary-500 hover:bg-primary-600 rounded-lg transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105">
                        <i class='bx bx-log-in text-lg mr-2'></i>
                        <span>Sign In</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row justify-between items-center space-y-2 sm:space-y-0">
                <div class="text-gray-600 dark:text-gray-400 text-xs">
                    © {{ date('Y') }} BMMB Digital Forms. All rights reserved.
                </div>
                <div class="flex space-x-4 text-xs">
                    <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-300">Privacy Policy</a>
                    <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-300">Terms of Service</a>
                    <a href="#" class="text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors duration-300">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Dark Mode Functions
        function getThemePreference() {
            const saved = localStorage.getItem('darkMode');
            if (saved !== null) {
                return saved === 'true';
            }
            // Use system default theme setting
            const defaultTheme = '{{ $defaultTheme }}';
            if (defaultTheme === 'dark') {
                return true;
            } else if (defaultTheme === 'auto') {
                return window.matchMedia('(prefers-color-scheme: dark)').matches;
            }
            // Default to light mode
            return false;
        }
        
        function applyTheme(isDark) {
            const html = document.documentElement;
            if (isDark) {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }
            updateDarkModeIcons(isDark);
        }
        
        function updateDarkModeIcons(isDark) {
            const darkIcons = document.querySelectorAll('#darkModeIcon, #darkModeIconMobile');
            const lightIcons = document.querySelectorAll('#lightModeIcon, #lightModeIconMobile');
            
            darkIcons.forEach(icon => {
                icon.classList.toggle('hidden', isDark);
            });
            lightIcons.forEach(icon => {
                icon.classList.toggle('hidden', !isDark);
            });
        }
        
        function toggleDarkMode() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            const newTheme = !isDark;
            
            applyTheme(newTheme);
            localStorage.setItem('darkMode', newTheme.toString());
        }
        
        // Apply theme on page load
        document.addEventListener('DOMContentLoaded', () => {
            const isDark = getThemePreference();
            applyTheme(isDark);
        });
        
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
                    confirmButtonColor: '{{ $primaryColor }}'
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
            
            // Get form element
            const form = document.getElementById(formId);
            const formData = new FormData(form);
            
            // Determine form type from URL or form ID
            let formType = 'raf';
            if (formId.includes('dar')) formType = 'dar';
            else if (formId.includes('dcr')) formType = 'dcr';
            else if (formId.includes('srf')) formType = 'srf';
            
            // Submit to backend
            fetch(`/forms/${formType}/submit`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || successMessage,
                        confirmButtonColor: '{{ $primaryColor }}'
                    }).then(() => {
                        form.reset();
                        // Optionally redirect to a thank you page
                        // window.location.href = '/forms/thank-you';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: data.message || 'An error occurred. Please try again.',
                        confirmButtonColor: '{{ $primaryColor }}'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Failed',
                    text: 'An error occurred. Please try again.',
                    confirmButtonColor: '{{ $primaryColor }}'
                });
            });
            
            return false; // Prevent default form submission
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
