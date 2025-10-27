@extends('layouts.admin-minimal')

@section('title', 'Admin Dashboard - BMMB Digital Forms')
@section('page-title', 'Dashboard')
@section('page-description', 'Overview of your form management system')

@section('content')
<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Forms</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">24</p>
                <p class="text-xs text-green-600 dark:text-green-400 flex items-center mt-2 font-medium">
                    <i class='bx bx-trending-up mr-1 text-sm'></i>
                    +12% from last month
                </p>
            </div>
            <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl flex items-center justify-center shadow-md">
                <i class='bx bx-edit-alt text-xl text-blue-600 dark:text-blue-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Responses</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">1,847</p>
                <p class="text-xs text-green-600 dark:text-green-400 flex items-center mt-2 font-medium">
                    <i class='bx bx-trending-up mr-1 text-sm'></i>
                    +8% from last month
                </p>
            </div>
            <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-xl flex items-center justify-center shadow-md">
                <i class='bx bx-clipboard text-xl text-green-600 dark:text-green-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Active Users</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">156</p>
                <p class="text-xs text-green-600 dark:text-green-400 flex items-center mt-2 font-medium">
                    <i class='bx bx-trending-up mr-1 text-sm'></i>
                    +5% from last month
                </p>
            </div>
            <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/30 dark:to-purple-800/30 rounded-xl flex items-center justify-center shadow-md">
                <i class='bx bx-user text-xl text-purple-600 dark:text-purple-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Conversion Rate</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">68%</p>
                <p class="text-xs text-red-600 dark:text-red-400 flex items-center mt-2 font-medium">
                    <i class='bx bx-trending-down mr-1 text-sm'></i>
                    -2% from last month
                </p>
            </div>
            <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30 rounded-xl flex items-center justify-center shadow-md">
                <i class='bx bx-trending-up text-xl text-orange-600 dark:text-orange-400'></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Analytics -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
    <!-- Response Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Response Trends</h3>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Track form submission patterns</p>
            </div>
            <div class="flex space-x-2">
                <button class="px-4 py-2 text-sm font-semibold bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl shadow-sm">7D</button>
                <button class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">30D</button>
                <button class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition-colors">90D</button>
            </div>
        </div>
        <div class="h-80 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-600">
            <div class="text-center">
                <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-bar-chart-alt-2 text-4xl text-blue-600 dark:text-blue-400'></i>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Chart Coming Soon</h4>
                <p class="text-gray-500 dark:text-gray-400">Interactive analytics will be implemented here</p>
            </div>
        </div>
    </div>

    <!-- Form Performance -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Top Performing Forms</h3>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Your most successful forms</p>
            </div>
                   <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 text-sm font-semibold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl transition-colors">
                       View All
                       <i class='bx bx-right-arrow-alt ml-2'></i>
                   </a>
        </div>
        <div class="space-y-6">
            <div class="flex items-center justify-between p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl border border-blue-100 dark:border-blue-800/30">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl flex items-center justify-center shadow-lg">
                        <i class='bx bx-user-plus text-2xl text-blue-600 dark:text-blue-400'></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white text-lg">Registration Form</p>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">456 responses</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-green-600 dark:text-green-400">+12%</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">vs last week</p>
                </div>
            </div>

            <div class="flex items-center justify-between p-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-2xl border border-green-100 dark:border-green-800/30">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-xl flex items-center justify-center shadow-lg">
                        <i class='bx bx-clipboard text-2xl text-green-600 dark:text-green-400'></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white text-lg">Customer Survey</p>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">234 responses</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-green-600 dark:text-green-400">+8%</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">vs last week</p>
                </div>
            </div>

            <div class="flex items-center justify-between p-6 bg-gradient-to-r from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-2xl border border-purple-100 dark:border-purple-800/30">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/30 dark:to-purple-800/30 rounded-xl flex items-center justify-center shadow-lg">
                        <i class='bx bx-wallet text-2xl text-purple-600 dark:text-purple-400'></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white text-lg">Payment Form</p>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">189 responses</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-red-600 dark:text-red-400">-3%</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">vs last week</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity and Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Activity -->
    <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h3>
            <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
        </div>
        <div class="space-y-4">
            <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                    <i class='bx bx-plus text-blue-600 dark:text-blue-400 text-sm'></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-900 dark:text-white">New form "Event Registration" created</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">2 hours ago</p>
                </div>
            </div>

            <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                    <i class='bx bx-clipboard text-green-600 dark:text-green-400 text-sm'></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-900 dark:text-white">45 new responses received</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">4 hours ago</p>
                </div>
            </div>

            <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                    <i class='bx bx-user text-purple-600 dark:text-purple-400 text-sm'></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-900 dark:text-white">New user registered</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">6 hours ago</p>
                </div>
            </div>

            <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center">
                    <i class='bx bx-edit text-orange-600 dark:text-orange-400 text-sm'></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-900 dark:text-white">Form "Contact Us" updated</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">1 day ago</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Quick Actions</h3>
        <div class="space-y-3">
            <a href="{{ route('admin.users.index') }}" class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                <i class='bx bx-user-plus mr-3'></i>
                Manage Users
            </a>
            
            <a href="{{ route('admin.settings') }}" class="flex items-center p-3 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/50 transition-colors">
                <i class='bx bx-download mr-3'></i>
                Export Data
            </a>
            
            <a href="{{ route('admin.users.create') }}" class="flex items-center p-3 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/50 transition-colors">
                <i class='bx bx-user-plus mr-3'></i>
                Add User
            </a>
            
            <a href="{{ route('admin.settings') }}" class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                <i class='bx bx-cog mr-3'></i>
                System Settings
            </a>
        </div>
    </div>
</div>
@endsection
