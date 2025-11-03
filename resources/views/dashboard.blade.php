@extends('layouts.public')

@section('title', 'Dashboard - BMMB Digital Forms')

@section('content')
<!-- Stats Overview -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i class='bx bx-edit-alt text-2xl text-blue-600 dark:text-blue-400'></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Forms</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">12</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i class='bx bx-clipboard text-2xl text-green-600 dark:text-green-400'></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Responses</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">1,247</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                        <i class='bx bx-user text-2xl text-purple-600 dark:text-purple-400'></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Users</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">89</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                        <i class='bx bx-trending-up text-2xl text-orange-600 dark:text-orange-400'></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Conversion</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">68%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Forms -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Forms</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Your latest form submissions</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                <i class='bx bx-user-plus text-blue-600 dark:text-blue-400'></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">Registration Form</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">45 responses today</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-green-600 dark:text-green-400">+12%</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">vs yesterday</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                <i class='bx bx-clipboard text-green-600 dark:text-green-400'></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">Customer Survey</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">23 responses today</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-green-600 dark:text-green-400">+8%</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">vs yesterday</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                <i class='bx bx-wallet text-purple-600 dark:text-purple-400'></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">Payment Form</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">18 responses today</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-red-600 dark:text-red-400">-3%</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">vs yesterday</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="#" class="group bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center group-hover:bg-orange-200 dark:group-hover:bg-orange-800/50 transition-colors">
                        <i class='bx bx-plus text-2xl text-orange-600 dark:text-orange-400'></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">Create New Form</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Build a custom form from scratch</p>
                    </div>
                </div>
            </a>

            <a href="#" class="group bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-800/50 transition-colors">
                        <i class='bx bx-template text-2xl text-green-600 dark:text-green-400'></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">Use Template</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Start with a pre-built template</p>
                    </div>
                </div>
            </a>

            <a href="#" class="group bg-white rounded-xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-800/50 transition-colors">
                        <i class='bx bx-bar-chart-alt-2 text-2xl text-purple-600 dark:text-purple-400'></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">View Analytics</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Analyze form performance</p>
                    </div>
                </div>
            </a>
        </div>
        </div>
@endsection
