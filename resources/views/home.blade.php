@extends('layouts.app')

@section('title', 'BMMB Digital Forms - Create Professional Forms in Minutes')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-20 lg:py-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-sm font-medium mb-8">
                <i class='bx bx-cog mr-2'></i>
                Digital Form Solutions
            </div>
            <h1 class="text-5xl lg:text-7xl font-bold text-gray-900 dark:text-white mb-6 leading-tight">
                Create Professional Forms
                <span class="block text-blue-600 dark:text-blue-400">in Minutes</span>
            </h1>
            <p class="text-xl lg:text-2xl text-gray-600 dark:text-gray-300 mb-12 leading-relaxed max-w-4xl mx-auto">
                Streamline your data collection with our comprehensive digital form solutions. 
                Build, customize, and deploy professional forms without any coding required.
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
                <a href="{{ route('register') }}" class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                    <i class='bx bx-edit mr-2 text-xl'></i>
                    Start Building Forms
                    <i class='bx bx-right-arrow-circle ml-2 group-hover:translate-x-1 transition-transform'></i>
                </a>
                <a href="#demo" class="group inline-flex items-center justify-center px-8 py-4 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:border-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-300">
                    <i class='bx bx-video mr-2 text-xl'></i>
                    Watch Demo
                </a>
            </div>
            
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg">
                    <div class="text-4xl font-bold text-blue-600 dark:text-blue-400 mb-2">10K+</div>
                    <div class="text-gray-600 dark:text-gray-300">Forms Created</div>
                </div>
                <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg">
                    <div class="text-4xl font-bold text-green-600 dark:text-green-400 mb-2">50K+</div>
                    <div class="text-gray-600 dark:text-gray-300">Responses</div>
                </div>
                <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg">
                    <div class="text-4xl font-bold text-purple-600 dark:text-purple-400 mb-2">99.9%</div>
                    <div class="text-gray-600 dark:text-gray-300">Uptime</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Why Choose Our Platform?</h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                Powerful features designed to make form creation simple, fast, and professional
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center p-8 bg-gray-50 dark:bg-gray-800 rounded-2xl hover:shadow-lg transition-shadow">
                        <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class='bx bx-devices text-3xl text-blue-600 dark:text-blue-400'></i>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Mobile Responsive</h3>
                        <p class="text-gray-600 dark:text-gray-300">Forms look perfect on all devices and screen sizes</p>
                    </div>
            <div class="text-center p-8 bg-gray-50 dark:bg-gray-800 rounded-2xl hover:shadow-lg transition-shadow">
                <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class='bx bx-lock-alt text-3xl text-green-600 dark:text-green-400'></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Secure & Reliable</h3>
                <p class="text-gray-600 dark:text-gray-300">Enterprise-grade security for your data and users</p>
            </div>
            <div class="text-center p-8 bg-gray-50 dark:bg-gray-800 rounded-2xl hover:shadow-lg transition-shadow">
                <div class="w-20 h-20 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class='bx bx-bar-chart-alt-2 text-3xl text-purple-600 dark:text-purple-400'></i>
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Analytics & Reports</h3>
                <p class="text-gray-600 dark:text-gray-300">Track performance and gain valuable insights</p>
            </div>
        </div>
    </div>
</section>

<!-- Form Types Section -->
<section id="templates" class="py-20 bg-gray-50 dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Choose Your Form Type</h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                Select from our pre-built templates or start from scratch with our drag-and-drop builder
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="group p-8 bg-white dark:bg-gray-800 rounded-2xl hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-500 group-hover:scale-110 transition-all duration-300">
                            <i class='bx bx-user-plus text-3xl text-blue-600 dark:text-blue-400 group-hover:text-white'></i>
                        </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Registration Forms</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">User registration, event signup, and membership applications</p>
                    <div class="flex items-center justify-center text-blue-600 dark:text-blue-400 font-medium">
                        <span>Get Started</span>
                        <i class='bx bx-arrow-right ml-2 group-hover:translate-x-1 transition-transform'></i>
                    </div>
                </div>
            </div>
            
            <div class="group p-8 bg-white dark:bg-gray-800 rounded-2xl hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-green-500 group-hover:scale-110 transition-all duration-300">
                            <i class='bx bx-clipboard text-3xl text-green-600 dark:text-green-400 group-hover:text-white'></i>
                        </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Survey Forms</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">Customer feedback, satisfaction surveys, and research forms</p>
                    <div class="flex items-center justify-center text-green-600 dark:text-green-400 font-medium">
                        <span>Get Started</span>
                        <i class='bx bx-arrow-right ml-2 group-hover:translate-x-1 transition-transform'></i>
                    </div>
                </div>
            </div>
            
            <div class="group p-8 bg-white dark:bg-gray-800 rounded-2xl hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                        <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-purple-500 group-hover:scale-110 transition-all duration-300">
                            <i class='bx bx-wallet text-3xl text-purple-600 dark:text-purple-400 group-hover:text-white'></i>
                        </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Payment Forms</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">Order processing, billing, and payment collection</p>
                    <div class="flex items-center justify-center text-purple-600 dark:text-purple-400 font-medium">
                        <span>Get Started</span>
                        <i class='bx bx-arrow-right ml-2 group-hover:translate-x-1 transition-transform'></i>
                    </div>
                </div>
            </div>
            
            <div class="group p-8 bg-white dark:bg-gray-800 rounded-2xl hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                        <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900/30 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:bg-orange-500 group-hover:scale-110 transition-all duration-300">
                            <i class='bx bx-help-circle text-3xl text-orange-600 dark:text-orange-400 group-hover:text-white'></i>
                        </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">Support Forms</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-4">Contact forms, support tickets, and help requests</p>
                    <div class="flex items-center justify-center text-orange-600 dark:text-orange-400 font-medium">
                        <span>Get Started</span>
                        <i class='bx bx-arrow-right ml-2 group-hover:translate-x-1 transition-transform'></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Demo Section -->
<section id="demo" class="py-20 bg-white dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">See It In Action</h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                Watch how easy it is to create professional forms with our drag-and-drop builder
            </p>
        </div>
        
        <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl p-8 text-center">
            <div class="w-24 h-24 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class='bx bx-play-circle text-4xl text-blue-600 dark:text-blue-400'></i>
            </div>
            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Demo Video Coming Soon</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-6">
                We're working on an amazing demo video to show you how powerful our form builder is.
            </p>
            <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                <i class='bx bx-zap mr-2'></i>
                Try It Now
            </a>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="py-20 bg-gray-50 dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Simple Pricing</h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                Choose the plan that fits your needs. No hidden fees, cancel anytime.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <!-- Free Plan -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Free</h3>
                    <div class="text-4xl font-bold text-gray-900 dark:text-white mb-4">$0<span class="text-lg text-gray-500">/month</span></div>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">Perfect for getting started</p>
                    <ul class="space-y-3 mb-8 text-left">
                        <li class="flex items-center">
                            <i class='bx bx-check text-green-500 mr-3'></i>
                            <span class="text-gray-600 dark:text-gray-300">Up to 5 forms</span>
                        </li>
                        <li class="flex items-center">
                            <i class='bx bx-check text-green-500 mr-3'></i>
                            <span class="text-gray-600 dark:text-gray-300">100 responses/month</span>
                        </li>
                        <li class="flex items-center">
                            <i class='bx bx-check text-green-500 mr-3'></i>
                            <span class="text-gray-600 dark:text-gray-300">Basic templates</span>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="w-full bg-gray-900 hover:bg-gray-800 text-white py-3 rounded-lg font-medium transition-colors block text-center">
                        Get Started
                    </a>
                </div>
            </div>

            <!-- Pro Plan -->
            <div class="bg-blue-600 text-white rounded-2xl p-8 relative">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <span class="bg-yellow-400 text-yellow-900 px-4 py-1 rounded-full text-sm font-medium">Most Popular</span>
                </div>
                <div class="text-center">
                    <h3 class="text-2xl font-bold mb-2">Pro</h3>
                    <div class="text-4xl font-bold mb-4">$29<span class="text-lg text-blue-200">/month</span></div>
                    <p class="text-blue-100 mb-6">For growing businesses</p>
                    <ul class="space-y-3 mb-8 text-left">
                        <li class="flex items-center">
                            <i class='bx bx-check mr-3'></i>
                            <span>Unlimited forms</span>
                        </li>
                        <li class="flex items-center">
                            <i class='bx bx-check mr-3'></i>
                            <span>10,000 responses/month</span>
                        </li>
                        <li class="flex items-center">
                            <i class='bx bx-check mr-3'></i>
                            <span>Advanced templates</span>
                        </li>
                        <li class="flex items-center">
                            <i class='bx bx-check mr-3'></i>
                            <span>Analytics & reports</span>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="w-full bg-white text-blue-600 hover:bg-gray-100 py-3 rounded-lg font-medium transition-colors block text-center">
                        Start Free Trial
                    </a>
                </div>
            </div>

            <!-- Enterprise Plan -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 border border-gray-200 dark:border-gray-700">
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Enterprise</h3>
                    <div class="text-4xl font-bold text-gray-900 dark:text-white mb-4">$99<span class="text-lg text-gray-500">/month</span></div>
                    <p class="text-gray-600 dark:text-gray-300 mb-6">For large organizations</p>
                    <ul class="space-y-3 mb-8 text-left">
                        <li class="flex items-center">
                            <i class='bx bx-check text-green-500 mr-3'></i>
                            <span class="text-gray-600 dark:text-gray-300">Everything in Pro</span>
                        </li>
                        <li class="flex items-center">
                            <i class='bx bx-check text-green-500 mr-3'></i>
                            <span class="text-gray-600 dark:text-gray-300">Unlimited responses</span>
                        </li>
                        <li class="flex items-center">
                            <i class='bx bx-check text-green-500 mr-3'></i>
                            <span class="text-gray-600 dark:text-gray-300">Priority support</span>
                        </li>
                        <li class="flex items-center">
                            <i class='bx bx-check text-green-500 mr-3'></i>
                            <span class="text-gray-600 dark:text-gray-300">Custom integrations</span>
                        </li>
                    </ul>
                    <a href="#" class="w-full bg-gray-900 hover:bg-gray-800 text-white py-3 rounded-lg font-medium transition-colors block text-center">
                        Contact Sales
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-blue-600 to-purple-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold text-white mb-4">Ready to Get Started?</h2>
        <p class="text-xl text-blue-100 mb-12 max-w-3xl mx-auto">
            Join thousands of users who trust our platform for their form needs. 
            Start building professional forms in minutes.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="group inline-flex items-center justify-center px-8 py-4 bg-white text-blue-600 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                <i class='bx bx-plus-circle mr-2 text-xl'></i>
                Create Your First Form
                <i class='bx bx-right-arrow-circle ml-2 group-hover:translate-x-1 transition-transform'></i>
            </a>
            <a href="#templates" class="group inline-flex items-center justify-center px-8 py-4 border-2 border-white text-white rounded-xl font-semibold hover:bg-white hover:text-blue-600 transition-all duration-300">
                <i class='bx bx-collection mr-2 text-xl'></i>
                Browse Templates
            </a>
        </div>
    </div>
</section>
@endsection
