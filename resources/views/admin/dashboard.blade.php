@extends('layouts.admin-minimal')

@section('title', 'Admin Dashboard - BMMB Digital Forms')
@section('page-title', 'Dashboard')
@section('page-description', 'Overview of your form management system')

@section('content')
<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Forms</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">24</p>
                <p class="text-xs text-green-600 dark:text-green-400 flex items-center mt-1.5 font-medium">
                    <i class='bx bx-trending-up mr-1 text-xs'></i>
                    +12% from last month
                </p>
            </div>
            <div class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30 rounded-lg flex items-center justify-center shadow-sm">
                <i class='bx bx-edit-alt text-base text-orange-600 dark:text-orange-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total Responses</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">1,847</p>
                <p class="text-xs text-green-600 dark:text-green-400 flex items-center mt-1.5 font-medium">
                    <i class='bx bx-trending-up mr-1 text-xs'></i>
                    +8% from last month
                </p>
            </div>
            <div class="w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-lg flex items-center justify-center shadow-sm">
                <i class='bx bx-clipboard text-base text-green-600 dark:text-green-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Active Users</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">156</p>
                <p class="text-xs text-green-600 dark:text-green-400 flex items-center mt-1.5 font-medium">
                    <i class='bx bx-trending-up mr-1 text-xs'></i>
                    +5% from last month
                </p>
            </div>
            <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/30 dark:to-purple-800/30 rounded-lg flex items-center justify-center shadow-sm">
                <i class='bx bx-user text-base text-purple-600 dark:text-purple-400'></i>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 border border-gray-100 dark:border-gray-700 hover:shadow-md transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Conversion Rate</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">68%</p>
                <p class="text-xs text-red-600 dark:text-red-400 flex items-center mt-1.5 font-medium">
                    <i class='bx bx-trending-down mr-1 text-xs'></i>
                    -2% from last month
                </p>
            </div>
            <div class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30 rounded-lg flex items-center justify-center shadow-sm">
                <i class='bx bx-trending-up text-base text-orange-600 dark:text-orange-400'></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Analytics -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Response Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Response Trends</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Track form submission patterns</p>
            </div>
            <div class="flex space-x-1.5">
                <button class="px-2.5 py-1.5 text-xs font-semibold bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-lg shadow-sm">7D</button>
                <button class="px-2.5 py-1.5 text-xs font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">30D</button>
                <button class="px-2.5 py-1.5 text-xs font-semibold text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">90D</button>
            </div>
        </div>
        <div class="h-64 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <i class='bx bx-bar-chart-alt-2 text-2xl text-orange-600 dark:text-orange-400'></i>
                </div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Chart Coming Soon</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Interactive analytics will be implemented here</p>
            </div>
        </div>
    </div>

    <!-- Form Performance -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-5 border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Top Performing Forms</h3>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Your most successful forms</p>
            </div>
        </div>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-900/20 dark:to-amber-900/20 rounded-lg border border-orange-100 dark:border-orange-800/30">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30 rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx bx-user-plus text-lg text-orange-600 dark:text-orange-400'></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white text-sm">Registration Form</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">456 responses</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-green-600 dark:text-green-400">+12%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">vs last week</p>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg border border-green-100 dark:border-green-800/30">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx bx-clipboard text-lg text-green-600 dark:text-green-400'></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white text-sm">Customer Survey</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">234 responses</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-green-600 dark:text-green-400">+8%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">vs last week</p>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-violet-50 dark:from-purple-900/20 dark:to-violet-900/20 rounded-lg border border-purple-100 dark:border-purple-800/30">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/30 dark:to-purple-800/30 rounded-lg flex items-center justify-center shadow-sm">
                        <i class='bx bx-wallet text-lg text-purple-600 dark:text-purple-400'></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white text-sm">Payment Form</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">189 responses</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-red-600 dark:text-red-400">-3%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">vs last week</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
