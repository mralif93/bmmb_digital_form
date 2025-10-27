<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard - BMMB Digital Forms')</title>
    
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
</head>
<body class="font-sans bg-gray-100 dark:bg-gray-900" x-data="{ profileDropdown: false }">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-lg border-b border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo & Title -->
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-blue-700 rounded-lg shadow-lg flex items-center justify-center">
                        <i class='bx bx-edit-alt text-white text-sm'></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900 dark:text-white">BMMB Digital Forms</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Admin Panel</p>
                    </div>
                </div>
                
                <!-- User Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="w-9 h-9 bg-gradient-to-br from-blue-600 to-purple-600 rounded-full flex items-center justify-center shadow-md">
                            <i class='bx bx-user text-white text-sm'></i>
                        </div>
                        <div class="hidden lg:block text-left">
                            <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                        <i class='bx bx-chevron-down text-gray-500 dark:text-gray-400 text-sm transition-transform duration-200' :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div x-show="open" x-cloak 
                         class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 py-2 z-50">
                        <!-- User Info -->
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Auth::user()->email }}</p>
                            <span class="inline-block mt-2 px-2 py-0.5 text-xs font-medium text-blue-700 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded">
                                {{ ucfirst(Auth::user()->role) }}
                            </span>
                        </div>
                        
                        <!-- Dropdown Items -->
                        <div class="py-2">
                            <a href="{{ route('admin.profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                <i class='bx bx-user mr-3 text-lg'></i>
                                <span>Profile</span>
                            </a>
                            
                            <a href="{{ route('admin.settings') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-400 transition-colors">
                                <i class='bx bx-cog mr-3 text-lg'></i>
                                <span>Settings</span>
                            </a>
                            
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                                <i class='bx bx-help-circle mr-3 text-lg'></i>
                                <span>Help & Support</span>
                            </a>
                        </div>
                        
                        <!-- Logout -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <i class='bx bx-log-out mr-3 text-lg'></i>
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
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                            <i class='bx bx-home-alt-2 mr-3 text-base'></i>
                            <span class="font-medium">Dashboard</span>
                        </a>
                        
                        <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:text-orange-600 dark:hover:text-orange-400 transition-colors {{ request()->routeIs('admin.users*') ? 'bg-orange-50 dark:bg-orange-900/20 text-orange-600 dark:text-orange-400' : '' }}">
                            <i class='bx bx-user mr-3 text-base'></i>
                            <span class="font-medium">Users</span>
                        </a>
                        
                        <div class="pt-4">
                            <p class="px-3 py-2 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Form Submissions</p>
                            <a href="{{ route('admin.submissions.dar') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 transition-colors {{ request()->routeIs('admin.submissions.dar*') ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400' : '' }}">
                                <i class='bx bx-file mr-3 text-base'></i>
                                <span class="font-medium">DAR</span>
                            </a>
                            <a href="{{ route('admin.submissions.dcr') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-600 dark:hover:text-purple-400 transition-colors {{ request()->routeIs('admin.submissions.dcr*') ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}">
                                <i class='bx bx-edit mr-3 text-base'></i>
                                <span class="font-medium">DCR</span>
                            </a>
                            <a href="{{ route('admin.submissions.raf') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400 transition-colors {{ request()->routeIs('admin.submissions.raf*') ? 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' : '' }}">
                                <i class='bx bx-money mr-3 text-base'></i>
                                <span class="font-medium">RAF</span>
                            </a>
                            <a href="{{ route('admin.submissions.srf') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors {{ request()->routeIs('admin.submissions.srf*') ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400' : '' }}">
                                <i class='bx bx-cog mr-3 text-base'></i>
                                <span class="font-medium">SRF</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div class="space-y-0.5">
                        <a href="{{ route('admin.profile') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-md hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-600 dark:hover:text-purple-400 transition-colors {{ request()->routeIs('admin.profile*') ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : '' }}">
                            <i class='bx bx-user-circle mr-3 text-base'></i>
                            <span class="font-medium">Profile</span>
                        </a>
                        
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
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
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
            <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-3">
                <div class="px-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <p class="text-xs text-gray-600 dark:text-gray-400">&copy; {{ date('Y') }} BMMB Digital Forms. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>
</html>
