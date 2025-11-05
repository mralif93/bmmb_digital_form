<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard - BMMB Digital Forms')</title>
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Boxicons CDN -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Poppins', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#FE8000',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        },
                        'blue-50': '#eff6ff',
                        'blue-100': '#dbeafe',
                        'blue-400': '#60a5fa',
                        'blue-600': '#2563eb',
                        'blue-700': '#1d4ed8',
                        'blue-900': '#1e3a8a',
                        'green-50': '#f0fdf4',
                        'green-100': '#dcfce7',
                        'green-500': '#10b981',
                        'green-600': '#16a34a',
                        'green-900': '#14532d',
                        'purple-50': '#f5f3ff',
                        'purple-100': '#ede9fe',
                        'purple-600': '#9333ea',
                        'purple-900': '#581c87',
                        'orange-50': '#fff7ed',
                        'orange-100': '#ffedd5',
                        'orange-600': '#ea580c',
                        'orange-900': '#7c2d12',
                        'gray-50': '#f9fafb',
                        'gray-100': '#f3f4f6',
                        'gray-200': '#e5e7eb',
                        'gray-300': '#d1d5db',
                        'gray-400': '#9ca3af',
                        'gray-500': '#6b7280',
                        'gray-600': '#4b5563',
                        'gray-700': '#374151',
                        'gray-800': '#1f2937',
                        'gray-900': '#111827',
                    }
                }
            }
        }
    </script>
    
    <!-- Custom Styles for White Background and Typography Scale -->
    <style>
        body {
            background: #ffffff;
            min-height: 100vh;
            font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif;
            font-size: 13px;
            line-height: 1.5;
            transition: background-color 0.3s ease;
        }
        .dark body {
            background: #111827;
        }
        .bg-white {
            background: #ffffff;
        }
        .dark .bg-white {
            background: #1f2937;
        }
        .bg-gray-50 {
            background: #f9fafb;
        }
        .dark .bg-gray-50 {
            background: #111827;
        }

        /* Admin dashboard typography scale - standardized */
        h1 { font-size: 1.375rem; font-weight: 600; }
        h2 { font-size: 1.125rem; font-weight: 600; }
        h3 { font-size: 1rem; font-weight: 600; }
        h4 { font-size: 0.9375rem; font-weight: 600; }
        p, li, span, div, a, button, input, select, textarea, label { font-size: 0.8125rem; }
        
        /* Standardize text sizes */
        .text-xs { font-size: 0.75rem !important; }
        .text-sm { font-size: 0.8125rem !important; }
        .text-base { font-size: 0.875rem !important; }
        .text-lg { font-size: 1rem !important; }
        .text-xl { font-size: 1.125rem !important; }
        .text-2xl { font-size: 1.25rem !important; }
        
        /* Alpine.js x-cloak */
        [x-cloak] { display: none !important; }
    </style>
    
    <!-- Dark Mode Script -->
    <script>
        // Check for saved theme preference or default to light mode
        function getThemePreference() {
            const saved = localStorage.getItem('darkMode');
            if (saved !== null) {
                return saved === 'true';
            }
            // Default to light mode
            return false;
        }

        // Apply theme on page load
        function applyTheme(isDark) {
            const html = document.documentElement;
            const icon = document.getElementById('darkModeIcon');
            
            if (isDark) {
                html.classList.add('dark');
                if (icon) {
                    icon.className = 'bx bx-sun text-gray-300 text-lg';
                }
            } else {
                html.classList.remove('dark');
                if (icon) {
                    icon.className = 'bx bx-moon text-gray-600 text-lg';
                }
            }
            localStorage.setItem('darkMode', isDark);
        }

        // Toggle dark mode
        function toggleDarkMode() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            applyTheme(!isDark);
        }

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', function() {
            applyTheme(getThemePreference());
        });
    </script>
</head>
<body class="font-sans">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-lg border-b border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo & Title -->
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-orange-600 to-orange-700 rounded-lg shadow-lg flex items-center justify-center">
                        <i class='bx bx-edit-alt text-white text-sm'></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900 dark:text-white">BMMB Digital Forms</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Admin Panel</p>
                    </div>
                </div>
                
                <!-- Dark Mode Toggle & User Profile Dropdown -->
                <div class="flex items-center space-x-3">
                    <!-- Dark Mode Toggle -->
                    <button id="darkModeToggle" onclick="toggleDarkMode()" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" title="Toggle Dark Mode">
                        <i id="darkModeIcon" class='bx bx-moon text-gray-600 dark:text-gray-300 text-lg'></i>
                    </button>
                    
                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center space-x-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg px-2 py-1.5 transition-colors">
                        <div class="w-7 h-7 bg-gradient-to-br from-gray-400 to-gray-500 rounded-md flex items-center justify-center">
                            <i class='bx bx-user text-white text-xs'></i>
                        </div>
                        <div class="hidden lg:block text-left">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                        <i class='bx bx-chevron-down text-gray-600 dark:text-gray-400 text-xs ml-1 transition-transform duration-200' :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="py-1">
                            <a href="{{ route('admin.profile') }}" class="flex items-center px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class='bx bx-user-circle mr-2.5 text-sm'></i>
                                <span>Profile</span>
                            </a>
                            <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-2 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class='bx bx-cog mr-2.5 text-sm'></i>
                                <span>Settings</span>
                            </a>
                            <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-4 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <i class='bx bx-log-out mr-2.5 text-sm'></i>
                                    <span>Sign Out</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-56 bg-white dark:bg-gray-800 shadow-lg border-r border-gray-200 dark:border-gray-700">
            <nav class="p-4 space-y-1">
                <div class="mb-8">
                    <div class="space-y-0.5">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                            <i class='bx bx-home-alt-2 mr-3 text-base'></i>
                            <span class="font-medium">Dashboard</span>
                        </a>
                        
                        <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                            <i class='bx bx-user mr-3 text-base'></i>
                            <span class="font-medium">Users</span>
                        </a>
                        
                        <a href="{{ route('admin.branches.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors {{ request()->routeIs('admin.branches*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                            <i class='bx bx-building mr-3 text-base'></i>
                            <span class="font-medium">Branches</span>
                        </a>
                        
                        <a href="{{ route('admin.qr-codes.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors {{ request()->routeIs('admin.qr-codes*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                            <i class='bx bx-qr-scan mr-3 text-base'></i>
                            <span class="font-medium">QR Codes</span>
                        </a>
                        
                        <a href="{{ route('admin.audit-trails.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors {{ request()->routeIs('admin.audit-trails*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                            <i class='bx bx-history mr-3 text-base'></i>
                            <span class="font-medium">Audit Trail</span>
                        </a>
                        
                        <!-- Forms Menu (Collapsible) -->
                        <div x-data="{ open: {{ request()->routeIs('admin.forms*') ? 'true' : 'false' }} }" class="space-y-0.5">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors {{ request()->routeIs('admin.forms*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                                <div class="flex items-center">
                                    <i class='bx bx-file-blank mr-3 text-base'></i>
                                    <span class="font-medium">Forms</span>
                                </div>
                                <i class='bx bx-chevron-down text-xs transition-transform duration-200' :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-collapse class="ml-4 space-y-0.5 mt-1">
                                @php
                                    $currentType = request()->route('type');
                                @endphp
                                <a href="{{ route('admin.forms.index', 'raf') }}" class="flex items-center px-3 py-2 text-xs text-gray-600 dark:text-gray-400 rounded-md hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors {{ $currentType == 'raf' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                                    <i class='bx bx-money mr-2 text-sm'></i>
                                    <span>RAF</span>
                                </a>
                                <a href="{{ route('admin.forms.index', 'dar') }}" class="flex items-center px-3 py-2 text-xs text-gray-600 dark:text-gray-400 rounded-md hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors {{ $currentType == 'dar' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                                    <i class='bx bx-data mr-2 text-sm'></i>
                                    <span>DAR</span>
                                </a>
                                <a href="{{ route('admin.forms.index', 'dcr') }}" class="flex items-center px-3 py-2 text-xs text-gray-600 dark:text-gray-400 rounded-md hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors {{ $currentType == 'dcr' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                                    <i class='bx bx-edit mr-2 text-sm'></i>
                                    <span>DCR</span>
                                </a>
                                <a href="{{ route('admin.forms.index', 'srf') }}" class="flex items-center px-3 py-2 text-xs text-gray-600 dark:text-gray-400 rounded-md hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors {{ $currentType == 'srf' ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                                    <i class='bx bx-cog mr-2 text-sm'></i>
                                    <span>SRF</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="space-y-0.5">
                        <a href="{{ route('admin.settings') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-400 transition-colors {{ request()->routeIs('admin.settings*') ? 'bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400' : '' }}">
                            <i class='bx bx-cog mr-3 text-base'></i>
                            <span class="font-medium">Settings</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="flex-1 flex flex-col bg-gray-50 dark:bg-gray-900">
            <!-- Page Header -->
            <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">@yield('page-title', 'Dashboard')</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">@yield('page-description', 'Welcome to your admin dashboard')</p>
                </div>
            </div>

            <!-- Page Content -->
            <main class="flex-1 bg-gray-50 dark:bg-gray-900">
                <div class="p-6">
                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-md border-t border-gray-200 dark:border-gray-700 py-3">
                <div class="px-4">
                    <div class="flex items-center justify-center">
                        <p class="text-xs text-gray-600 dark:text-gray-400">&copy; {{ date('Y') }} BMMB Digital Forms. All rights reserved.</p>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>
