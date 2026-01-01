@extends('layouts.admin-minimal')

@section('title', 'System Settings - BMMB Digital Forms')
@section('page-title', 'System Settings')
@section('page-description', 'Configure system settings and preferences')

@section('content')
    @if(session('success'))
        <div
            class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div
            class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-800 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-6">
        <!-- Settings Overview -->
        <div
            class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-xl p-6 border border-primary-200 dark:border-primary-800/30">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-primary-600 rounded-xl flex items-center justify-center">
                    <i class='bx bx-cog text-white text-2xl'></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">System Configuration</h2>
                    <p class="text-gray-600 dark:text-gray-400">Manage your application settings, security, and integrations
                    </p>
                </div>
            </div>
        </div>

        <!-- Settings Tabs -->
        <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700"
                x-data="{ activeTab: 'general' }">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button type="button" @click="activeTab = 'general'"
                            :class="activeTab === 'general' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                            <i class='bx bx-cog mr-2'></i>
                            General
                        </button>
                        <button type="button" @click="activeTab = 'email'"
                            :class="activeTab === 'email' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                            <i class='bx bx-envelope mr-2'></i>
                            Email
                        </button>
                        <button type="button" @click="activeTab = 'security'"
                            :class="activeTab === 'security' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                            <i class='bx bx-shield-check mr-2'></i>
                            Security
                        </button>
                        <button type="button" @click="activeTab = 'appearance'"
                            :class="activeTab === 'appearance' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                            <i class='bx bx-palette mr-2'></i>
                            Appearance
                        </button>
                        <button type="button" @click="activeTab = 'scheduler'"
                            :class="activeTab === 'scheduler' ? 'border-primary-500 text-primary-600 dark:text-primary-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                            class="border-b-2 py-4 px-1 text-sm font-medium transition-colors">
                            <i class='bx bx-time-five mr-2'></i>
                            Scheduler
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <!-- General Settings Tab -->
                    <div x-show="activeTab === 'general'" x-transition class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Application Settings</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Application
                                        Name</label>
                                    <input type="text" value="BMMB Digital Forms"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Application
                                        URL</label>
                                    <input type="url" value="https://forms.bmmb.com"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                </div>
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Application
                                        Description</label>
                                    <textarea
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white"
                                        rows="3">Digital form management system for BMMB - Streamline your data collection with our comprehensive digital form solutions.</textarea>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Preferences</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default
                                        Timezone</label>
                                    <select name="timezone"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                        <option value="UTC" {{ ($settings['timezone'] ?? 'UTC') == 'UTC' ? 'selected' : '' }}>
                                            UTC (Coordinated Universal Time)</option>
                                        <optgroup label="Asia">
                                            <option value="Asia/Kuala_Lumpur" {{ ($settings['timezone'] ?? 'UTC') == 'Asia/Kuala_Lumpur' ? 'selected' : '' }}>Kuala Lumpur, Malaysia
                                                (GMT+8)</option>
                                            <option value="Asia/Singapore" {{ ($settings['timezone'] ?? 'UTC') == 'Asia/Singapore' ? 'selected' : '' }}>Singapore (GMT+8)</option>
                                            <option value="Asia/Jakarta" {{ ($settings['timezone'] ?? 'UTC') == 'Asia/Jakarta' ? 'selected' : '' }}>Jakarta, Indonesia (GMT+7)</option>
                                            <option value="Asia/Bangkok" {{ ($settings['timezone'] ?? 'UTC') == 'Asia/Bangkok' ? 'selected' : '' }}>Bangkok, Thailand (GMT+7)</option>
                                            <option value="Asia/Manila" {{ ($settings['timezone'] ?? 'UTC') == 'Asia/Manila' ? 'selected' : '' }}>Manila, Philippines (GMT+8)</option>
                                            <option value="Asia/Tokyo" {{ ($settings['timezone'] ?? 'UTC') == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo, Japan (GMT+9)</option>
                                            <option value="Asia/Seoul" {{ ($settings['timezone'] ?? 'UTC') == 'Asia/Seoul' ? 'selected' : '' }}>Seoul, South Korea (GMT+9)</option>
                                            <option value="Asia/Hong_Kong" {{ ($settings['timezone'] ?? 'UTC') == 'Asia/Hong_Kong' ? 'selected' : '' }}>Hong Kong (GMT+8)</option>
                                            <option value="Asia/Shanghai" {{ ($settings['timezone'] ?? 'UTC') == 'Asia/Shanghai' ? 'selected' : '' }}>Shanghai, China (GMT+8)
                                            </option>
                                            <option value="Asia/Dubai" {{ ($settings['timezone'] ?? 'UTC') == 'Asia/Dubai' ? 'selected' : '' }}>Dubai, UAE (GMT+4)</option>
                                        </optgroup>
                                        <optgroup label="Europe">
                                            <option value="Europe/London" {{ ($settings['timezone'] ?? 'UTC') == 'Europe/London' ? 'selected' : '' }}>London, UK (GMT+0/+1)</option>
                                            <option value="Europe/Paris" {{ ($settings['timezone'] ?? 'UTC') == 'Europe/Paris' ? 'selected' : '' }}>Paris, France (GMT+1/+2)</option>
                                            <option value="Europe/Berlin" {{ ($settings['timezone'] ?? 'UTC') == 'Europe/Berlin' ? 'selected' : '' }}>Berlin, Germany (GMT+1/+2)
                                            </option>
                                        </optgroup>
                                        <optgroup label="America">
                                            <option value="America/New_York" {{ ($settings['timezone'] ?? 'UTC') == 'America/New_York' ? 'selected' : '' }}>New York, USA (EST/EDT)
                                            </option>
                                            <option value="America/Chicago" {{ ($settings['timezone'] ?? 'UTC') == 'America/Chicago' ? 'selected' : '' }}>Chicago, USA (CST/CDT)
                                            </option>
                                            <option value="America/Los_Angeles" {{ ($settings['timezone'] ?? 'UTC') == 'America/Los_Angeles' ? 'selected' : '' }}>Los Angeles, USA
                                                (PST/PDT)</option>
                                        </optgroup>
                                        <optgroup label="Australia">
                                            <option value="Australia/Sydney" {{ ($settings['timezone'] ?? 'UTC') == 'Australia/Sydney' ? 'selected' : '' }}>Sydney, Australia
                                                (AEST/AEDT)</option>
                                            <option value="Australia/Melbourne" {{ ($settings['timezone'] ?? 'UTC') == 'Australia/Melbourne' ? 'selected' : '' }}>Melbourne, Australia
                                                (AEST/AEDT)</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default
                                        Language</label>
                                    <select
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                        <option value="en" selected>English</option>
                                        <option value="es">Spanish</option>
                                        <option value="fr">French</option>
                                        <option value="de">German</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date
                                        Format</label>
                                    <select name="date_format"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                        <option value="Y-m-d" {{ ($settings['date_format'] ?? 'Y-m-d') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2024-12-31)</option>
                                        <option value="m/d/Y" {{ ($settings['date_format'] ?? 'Y-m-d') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (12/31/2024)</option>
                                        <option value="d/m/Y" {{ ($settings['date_format'] ?? 'Y-m-d') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (31/12/2024)</option>
                                        <option value="M d, Y" {{ ($settings['date_format'] ?? 'Y-m-d') == 'M d, Y' ? 'selected' : '' }}>M d, Y (Dec 31, 2024)</option>
                                        <option value="d M Y" {{ ($settings['date_format'] ?? 'Y-m-d') == 'd M Y' ? 'selected' : '' }}>d M Y (31 Dec 2024)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time
                                        Format</label>
                                    <select name="time_format"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                        <option value="H:i" {{ ($settings['time_format'] ?? 'H:i') == 'H:i' ? 'selected' : '' }}>24 Hour (14:30)</option>
                                        <option value="g:i A" {{ ($settings['time_format'] ?? 'H:i') == 'g:i A' ? 'selected' : '' }}>12 Hour (2:30 PM)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">QR Code
                                        Expiration (minutes)</label>
                                    <input type="number" name="qr_code_expiration_minutes"
                                        value="{{ $settings['qr_code_expiration_minutes'] ?? 60 }}" min="1" max="10080"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">QR codes will expire after this
                                        many minutes (1-10080 minutes = 7 days)</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Feature Toggles</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">User Registration</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Allow new users to register
                                            accounts</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" checked class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600">
                                        </div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Email Notifications
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Send email notifications for
                                            form submissions</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" checked class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600">
                                        </div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Analytics Tracking
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Track user behavior and form
                                            analytics</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Settings Tab -->
                    <div x-show="activeTab === 'email'" x-transition class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Email Configuration</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SMTP
                                        Host</label>
                                    <input type="text" placeholder="smtp.gmail.com"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">SMTP
                                        Port</label>
                                    <input type="number" placeholder="587"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Encryption</label>
                                    <select
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                        <option value="tls" selected>TLS</option>
                                        <option value="ssl">SSL</option>
                                        <option value="none">None</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From
                                        Email</label>
                                    <input type="email" placeholder="noreply@bmmb.com"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From
                                        Name</label>
                                    <input type="text" placeholder="BMMB Digital Forms"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Test
                                        Email</label>
                                    <div class="flex space-x-2">
                                        <input type="email" placeholder="test@example.com"
                                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                        <button type="button"
                                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-md transition-colors">
                                            <i class='bx bx-send mr-1'></i>
                                            Test
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Settings Tab -->
                    <div x-show="activeTab === 'security'" x-transition class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Security Settings</h3>
                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Session
                                            Timeout (minutes)</label>
                                        <input type="number" value="120" min="5" max="1440"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max
                                            Login Attempts</label>
                                        <input type="number" value="5" min="3" max="10"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Two-Factor
                                                Authentication</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Require 2FA for all admin
                                                users</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600">
                                            </div>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">IP Whitelist</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Restrict admin access to
                                                specific IP addresses</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600">
                                            </div>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Password Policy
                                            </h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Enforce strong password
                                                requirements</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" checked class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600">
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Appearance Settings Tab -->
                    <div x-show="activeTab === 'appearance'" x-transition class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Theme Settings</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Default
                                        Theme</label>
                                    <select name="default_theme"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                        <option value="light" {{ ($settings['default_theme'] ?? 'light') == 'light' ? 'selected' : '' }}>Light</option>
                                        <option value="dark" {{ ($settings['default_theme'] ?? 'light') == 'dark' ? 'selected' : '' }}>Dark</option>
                                        <option value="auto" {{ ($settings['default_theme'] ?? 'light') == 'auto' ? 'selected' : '' }}>Auto (System)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Primary
                                        Color</label>
                                    <div class="flex space-x-2">
                                        <input type="color" id="primary_color_picker"
                                            value="{{ $settings['primary_color'] ?? '#FE8000' }}"
                                            onchange="document.getElementById('primary_color').value = this.value"
                                            class="w-12 h-10 border border-gray-300 dark:border-gray-600 rounded-md cursor-pointer">
                                        <input type="text" name="primary_color" id="primary_color"
                                            value="{{ $settings['primary_color'] ?? '#FE8000' }}"
                                            onchange="document.getElementById('primary_color_picker').value = this.value"
                                            pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This color will be applied to
                                        both admin and public layouts</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Layout Settings</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Compact Mode</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Use smaller spacing and fonts
                                        </p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600">
                                        </div>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Sidebar Collapsed</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Start with sidebar collapsed by
                                            default</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Scheduler Settings Tab -->
                    <div x-show="activeTab === 'scheduler'" x-transition class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">MAP Synchronization</h3>
                            <div class="space-y-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Enable User Sync</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Automatically sync users from
                                            MAP database</p>
                                        @if(isset($settings['map_last_sync_at']))
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Last Synced: {{ \Carbon\Carbon::parse($settings['map_last_sync_at'])->format('d M Y, h:i A') }}</p>
                                        @else
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Last Synced: Never</p>
                                        @endif
                                    </div>
                                    <!-- Hidden input to ensure false is sent when unchecked -->
                                    <input type="hidden" name="map_sync_enabled" value="0">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="map_sync_enabled" value="1" {{ ($settings['map_sync_enabled'] ?? true) ? 'checked' : '' }}
                                            class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600">
                                        </div>
                                    </label>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sync
                                            Frequency</label>
                                        <select name="map_sync_frequency"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                            <option value="daily" {{ ($settings['map_sync_frequency'] ?? 'daily') == 'daily' ? 'selected' : '' }}>Daily</option>
                                            <option value="every_4_hours" {{ ($settings['map_sync_frequency'] ?? 'daily') == 'every_4_hours' ? 'selected' : '' }}>Every 4 Hours</option>
                                            <option value="hourly" {{ ($settings['map_sync_frequency'] ?? 'daily') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                                            <option value="every_30_minutes" {{ ($settings['map_sync_frequency'] ?? 'daily') == 'every_30_minutes' ? 'selected' : '' }}>Every 30 Minutes</option>
                                            <option value="every_15_minutes" {{ ($settings['map_sync_frequency'] ?? 'daily') == 'every_15_minutes' ? 'selected' : '' }}>Every 15 Minutes</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sync
                                            Time (Daily)</label>
                                        <input type="time" name="map_sync_time"
                                            value="{{ $settings['map_sync_time'] ?? '06:00' }}"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 dark:bg-gray-700 dark:text-white">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Only applicable when
                                            frequency is set to Daily</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>

        <!-- System Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">System Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                            <i class='bx bx-server text-primary-600 dark:text-primary-400'></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">PHP Version</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">8.4.1</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class='bx bx-data text-green-600 dark:text-green-400'></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Laravel Version</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">11.x</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                            <i class='bx bx-memory-card text-purple-600 dark:text-purple-400'></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Memory Usage</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">128 MB</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center">
            <div class="flex space-x-3">
                <button
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium transition-colors">
                    <i class='bx bx-reset mr-2'></i>
                    Reset to Defaults
                </button>
                <button
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium transition-colors">
                    <i class='bx bx-download mr-2'></i>
                    Export Settings
                </button>
            </div>
            <div class="flex space-x-3">
                <button
                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium transition-colors">
                    Cancel
                </button>
                <button type="submit" form="settings-form"
                    class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    <i class='bx bx-save mr-2'></i>
                    Save Settings
                </button>
            </div>
        </div>


    </div>
@endsection