<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'BMMB Digital Forms')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Boxicons CDN -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Poppins', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        'blue-50': '#eff6ff',
                        'blue-100': '#dbeafe',
                        'blue-400': '#60a5fa',
                        'blue-600': '#2563eb',
                        'green-500': '#10b981',
                        'purple-600': '#9333ea',
                        'orange-600': '#ea580c',
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

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    @stack('styles')

    <!-- Custom Styles for Orange Theme -->
    <style>
        body {
            background: linear-gradient(135deg, #fb923c 0%, #f97316 50%, #ea580c 100%) !important;
            min-height: 100vh !important;
        }

        .bg-white {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px) !important;
        }

        .dark\\:bg-gray-800 {
            background: rgba(31, 41, 55, 0.95) !important;
            backdrop-filter: blur(10px) !important;
        }

        .bg-gray-50 {
            background: transparent !important;
        }

        .dark\\:bg-gray-900 {
            background: transparent !important;
        }
    </style>
</head>

<body class="font-sans text-gray-900 dark:text-gray-100">
    <!-- Navigation -->
    <nav
        class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-md shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center">
                            <i class='bx bx-edit-alt text-white text-lg'></i>
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">BMMB Forms</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}"
                        class="text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Home</a>
                    <a href="#features"
                        class="text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Features</a>
                    <a href="#templates"
                        class="text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Templates</a>
                    <a href="#pricing"
                        class="text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Pricing</a>
                </div>

                <!-- Auth Links -->
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                            Dashboard
                        </a>
                        <form method="POST" action="{{ route('map.logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                class="text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                            Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Get Started
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button"
                        class="text-gray-600 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400">
                        <i class='bx bx-menu-alt-right text-2xl'></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer
        class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-md text-gray-900 dark:text-white border-t border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center">
                            <i class='bx bx-edit-alt text-white text-lg'></i>
                        </div>
                        <span class="text-xl font-bold">BMMB Forms</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-4 max-w-md">
                        Create professional digital forms in minutes. Streamline your data collection with our
                        comprehensive form solutions.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#"
                            class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                            <i class='bx bxl-facebook text-xl'></i>
                        </a>
                        <a href="#"
                            class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                            <i class='bx bxl-twitter text-xl'></i>
                        </a>
                        <a href="#"
                            class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                            <i class='bx bxl-linkedin text-xl'></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}"
                                class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Home</a>
                        </li>
                        <li><a href="#features"
                                class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Features</a>
                        </li>
                        <li><a href="#templates"
                                class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Templates</a>
                        </li>
                        <li><a href="#pricing"
                                class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Pricing</a>
                        </li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Support</h3>
                    <ul class="space-y-2">
                        <li><a href="#"
                                class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Help
                                Center</a></li>
                        <li><a href="#"
                                class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Documentation</a>
                        </li>
                        <li><a href="#"
                                class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Contact
                                Us</a></li>
                        <li><a href="#"
                                class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">Privacy
                                Policy</a></li>
                    </ul>
                </div>
            </div>

            <div
                class="border-t border-gray-300 dark:border-gray-600 mt-8 pt-8 text-center text-gray-600 dark:text-gray-400">
                <p>&copy; {{ date('Y') }} BMMB Digital Forms. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>