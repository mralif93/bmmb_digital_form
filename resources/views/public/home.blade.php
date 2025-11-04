@extends('layouts.public')

@section('title', 'BMMB Digital Forms - Home')

@section('content')
<!-- Modern Hero Section -->
<section class="relative min-h-[85vh] flex items-center overflow-hidden" style="background: linear-gradient(135deg, #FE8000 0%, #E87700 100%);">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white/5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-24">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left Column: Content -->
            <div class="text-white">
                <div class="inline-block mb-6 px-4 py-2 bg-white/10 backdrop-blur-md rounded-full text-sm font-semibold border border-white/20 animate-fade-in-up">
                    <i class='bx bx-check-circle mr-2'></i>Trusted by 1000+ Users
                </div>
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold mb-6 leading-tight animate-fade-in-up" style="animation-delay: 0.1s;">
                    Streamline Your <span class="block">Form Management</span>
                </h1>
                <p class="text-xl md:text-2xl text-white/90 mb-8 leading-relaxed animate-fade-in-up" style="animation-delay: 0.2s;">
                    Professional digital forms for secure, efficient, and compliant document processing. Transform your workflow today.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 animate-fade-in-up" style="animation-delay: 0.3s;">
                    <a href="{{ route('public.forms.raf') }}" class="bg-white text-orange-600 px-8 py-4 rounded-xl font-bold text-lg hover:shadow-2xl hover:scale-105 transform transition-all duration-300 inline-flex items-center justify-center group">
                        <i class='bx bx-file-blank mr-2 text-xl'></i>
                        Get Started Now
                        <i class='bx bx-arrow-forward ml-2 text-xl group-hover:translate-x-1 transition-transform'></i>
                    </a>
                    <a href="#features" class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-white/20 transition-all duration-300 inline-flex items-center justify-center">
                        <i class='bx bx-info-circle mr-2 text-xl'></i>
                        Learn More
                    </a>
                </div>
            </div>
            
            <!-- Right Column: Stats -->
            <div class="grid grid-cols-2 gap-6 animate-fade-in-up" style="animation-delay: 0.4s;">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                    <div class="text-4xl font-extrabold text-white mb-2">1000+</div>
                    <div class="text-white/80 text-sm font-medium">Forms Processed</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                    <div class="text-4xl font-extrabold text-white mb-2">99.9%</div>
                    <div class="text-white/80 text-sm font-medium">Uptime</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                    <div class="text-4xl font-extrabold text-white mb-2">&lt;48h</div>
                    <div class="text-white/80 text-sm font-medium">Processing Time</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 hover:bg-white/15 transition-all duration-300 transform hover:scale-105">
                    <div class="text-4xl font-extrabold text-white mb-2">24/7</div>
                    <div class="text-white/80 text-sm font-medium">Support</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <i class='bx bx-down-arrow-alt text-white text-3xl'></i>
    </div>
</section>

<!-- Available Forms Section -->
<section id="features" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
            <div class="inline-block mb-4 px-4 py-2 bg-orange-100 text-orange-600 rounded-full text-sm font-bold">
                <i class='bx bx-collection mr-2'></i>Choose Your Form
            </div>
            <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">
                Streamlined Digital Forms
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Secure, efficient, and compliant digital forms designed to meet your specific needs
            </p>
        </div>
        
        <!-- Form Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Remittance Application Form -->
            <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-orange-300 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-green-100 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-green-500/20">
                        <i class='bx bx-money text-white text-3xl'></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-green-600 transition-colors">Remittance Application</h3>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        International money transfers and financial transactions made easy.
                    </p>
                    <a href="{{ route('public.forms.raf') }}" class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center px-6 py-3.5 rounded-xl font-bold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-xl group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
            
            <!-- Data Access Request Form -->
            <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-orange-300 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl flex items-center justify-center mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-blue-500/20">
                        <i class='bx bx-data text-white text-3xl'></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition-colors">Data Access Request</h3>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Request access to personal data with full GDPR compliance.
                    </p>
                    <a href="{{ route('public.forms.dar') }}" class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center px-6 py-3.5 rounded-xl font-bold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-xl group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
            
            <!-- Data Correction Request Form -->
            <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-orange-300 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-orange-100 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-orange-500/20">
                        <i class='bx bx-edit text-white text-3xl'></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-orange-600 transition-colors">Data Correction</h3>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Correct and update your personal data with ease.
                    </p>
                    <a href="{{ route('public.forms.dcr') }}" class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center px-6 py-3.5 rounded-xl font-bold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-xl group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
            
            <!-- Service Request Form -->
            <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-orange-300 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-purple-100 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-purple-500/20">
                        <i class='bx bx-cog text-white text-3xl'></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-purple-600 transition-colors">Service Request</h3>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Request various services and support for your needs.
                    </p>
                    <a href="{{ route('public.forms.srf') }}" class="block w-full bg-orange-600 hover:bg-orange-700 text-white text-center px-6 py-3.5 rounded-xl font-bold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-xl group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
