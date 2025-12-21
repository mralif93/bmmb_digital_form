@extends('layouts.public')

@section('title', 'BMMB Digital Forms - Home')

@section('content')
<!-- Hero Section -->
<section class="relative bg-primary-600 dark:bg-primary-700 py-12 md:py-16 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <!-- Badge -->
            <div class="inline-flex items-center mb-4 px-4 py-2 bg-white/20 dark:bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-white border border-white/30 shadow-lg">
                <i class='bx bx-shield-check mr-2'></i>
                <span>Secure & Compliant Digital Forms</span>
            </div>
            
            <!-- Main Heading -->
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-4 leading-tight">
                Streamline Your
                <span class="block text-white/90 mt-1">Digital Experience</span>
            </h1>
            
            <!-- Subheading -->
            <p class="text-base md:text-lg text-white/90 max-w-3xl mx-auto mb-6 leading-relaxed font-light">
                Access our comprehensive suite of digital forms designed to simplify your transactions and requests
            </p>
            
            <!-- Stats -->
            <div class="flex flex-wrap justify-center gap-6 md:gap-8 mt-8">
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold text-white mb-1">100%</div>
                    <div class="text-xs text-white/80 font-medium">Secure</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold text-white mb-1">24/7</div>
                    <div class="text-xs text-white/80 font-medium">Available</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl md:text-3xl font-bold text-white mb-1">Fast</div>
                    <div class="text-xs text-white/80 font-medium">Processing</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Available Forms Section -->
<section id="features" class="py-12 md:py-16 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-10 md:mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-3">
                Available Forms
            </h2>
            <p class="text-base text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                Choose from our range of professional forms to get started with your request
            </p>
        </div>
        
        <!-- Form Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
            <!-- Remittance Application Form -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl p-6 md:p-8 border border-gray-200 dark:border-gray-700 hover:border-primary-400 dark:hover:border-primary-500 hover:shadow-xl md:hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
                <!-- Decorative Element -->
                <div class="absolute top-0 right-0 w-24 md:w-32 h-24 md:h-32 bg-primary-100 dark:bg-primary-900/30 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative">
                    <!-- Icon -->
                    <div class="w-14 md:w-16 h-14 md:h-16 bg-primary-500 dark:bg-primary-600 rounded-xl flex items-center justify-center mb-4 md:mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-primary-500/30 dark:shadow-primary-900/30">
                        <i class='bx bx-money text-white text-2xl md:text-3xl'></i>
                    </div>
                    
                    <!-- Content -->
                    <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        Remittance Application
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 md:mb-8 leading-relaxed text-sm">
                        International money transfers and financial transactions made easy with our secure platform.
                    </p>
                    
                    <!-- Button -->
                    <a href="{{ route('public.forms.raf') }}" class="block w-full bg-primary-500 dark:bg-primary-600 hover:bg-primary-600 dark:hover:bg-primary-700 text-white text-center px-5 md:px-6 py-2.5 md:py-3.5 rounded-xl font-semibold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center shadow-md hover:shadow-lg text-sm">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-lg md:text-xl group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
            
            <!-- Data Access Request Form -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl p-6 md:p-8 border border-gray-200 dark:border-gray-700 hover:border-primary-400 dark:hover:border-primary-500 hover:shadow-xl md:hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
                <div class="absolute top-0 right-0 w-24 md:w-32 h-24 md:h-32 bg-primary-100 dark:bg-primary-900/30 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative">
                    <div class="w-14 md:w-16 h-14 md:h-16 bg-primary-500 dark:bg-primary-600 rounded-xl flex items-center justify-center mb-4 md:mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-primary-500/30 dark:shadow-primary-900/30">
                        <i class='bx bx-data text-white text-2xl md:text-3xl'></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        Data Access Request
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 md:mb-8 leading-relaxed text-sm">
                        Request access to your personal data with full GDPR compliance and transparency.
                    </p>
                    <a href="{{ route('public.forms.dar') }}" class="block w-full bg-primary-500 dark:bg-primary-600 hover:bg-primary-600 dark:hover:bg-primary-700 text-white text-center px-5 md:px-6 py-2.5 md:py-3.5 rounded-xl font-semibold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center shadow-md hover:shadow-lg text-sm">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-lg md:text-xl group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
            
            <!-- Data Correction Request Form -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl p-6 md:p-8 border border-gray-200 dark:border-gray-700 hover:border-primary-400 dark:hover:border-primary-500 hover:shadow-xl md:hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
                <div class="absolute top-0 right-0 w-24 md:w-32 h-24 md:h-32 bg-primary-100 dark:bg-primary-900/30 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative">
                    <div class="w-14 md:w-16 h-14 md:h-16 bg-primary-500 dark:bg-primary-600 rounded-xl flex items-center justify-center mb-4 md:mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-primary-500/30 dark:shadow-primary-900/30">
                        <i class='bx bx-edit text-white text-2xl md:text-3xl'></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        Data Correction
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 md:mb-8 leading-relaxed text-sm">
                        Correct and update your personal information quickly and securely.
                    </p>
                    <a href="{{ route('public.forms.dcr') }}" class="block w-full bg-primary-500 dark:bg-primary-600 hover:bg-primary-600 dark:hover:bg-primary-700 text-white text-center px-5 md:px-6 py-2.5 md:py-3.5 rounded-xl font-semibold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center shadow-md hover:shadow-lg text-sm">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-lg md:text-xl group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
            
            <!-- Service Request Form -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl p-6 md:p-8 border border-gray-200 dark:border-gray-700 hover:border-primary-400 dark:hover:border-primary-500 hover:shadow-xl md:hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
                <div class="absolute top-0 right-0 w-24 md:w-32 h-24 md:h-32 bg-primary-100 dark:bg-primary-900/30 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative">
                    <div class="w-14 md:w-16 h-14 md:h-16 bg-primary-500 dark:bg-primary-600 rounded-xl flex items-center justify-center mb-4 md:mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-primary-500/30 dark:shadow-primary-900/30">
                        <i class='bx bx-cog text-white text-2xl md:text-3xl'></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-3 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        Service Request
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 md:mb-8 leading-relaxed text-sm">
                        Request various services and get the support you need for your requirements.
                    </p>
                    <a href="{{ route('public.forms.srf') }}" class="block w-full bg-primary-500 dark:bg-primary-600 hover:bg-primary-600 dark:hover:bg-primary-700 text-white text-center px-5 md:px-6 py-2.5 md:py-3.5 rounded-xl font-semibold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center shadow-md hover:shadow-lg text-sm">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-lg md:text-xl group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-10 md:py-12 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
            <div class="text-center">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mx-auto mb-3 md:mb-4">
                    <i class='bx bx-shield-check text-primary-600 dark:text-primary-400 text-xl md:text-2xl'></i>
                </div>
                <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-1 md:mb-2">Secure & Encrypted</h3>
                <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400">Your data is protected with industry-standard encryption</p>
            </div>
            
            <div class="text-center">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mx-auto mb-3 md:mb-4">
                    <i class='bx bx-time-five text-primary-600 dark:text-primary-400 text-xl md:text-2xl'></i>
                </div>
                <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-1 md:mb-2">Fast Processing</h3>
                <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400">Quick submission and efficient handling of your requests</p>
            </div>
            
            <div class="text-center">
                <div class="w-14 h-14 md:w-16 md:h-16 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mx-auto mb-3 md:mb-4">
                    <i class='bx bx-support text-primary-600 dark:text-primary-400 text-xl md:text-2xl'></i>
                </div>
                <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-1 md:mb-2">24/7 Support</h3>
                <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400">Round-the-clock assistance whenever you need help</p>
            </div>
        </div>
    </div>
</section>
@endsection
