@extends('layouts.public')

@section('title', 'BMMB Digital Forms - Home')

@section('content')
<!-- Hero Section -->
<section class="form-section py-20 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="2"/></g></svg>');"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center">
            <div class="animate-fade-in-up">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 leading-tight">
                    Digital Form Management
                </h1>
                <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-3xl mx-auto leading-relaxed">
                    Streamline your form processes with our comprehensive digital form management system. 
                    Submit, track, and manage your applications efficiently.
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up" style="animation-delay: 0.2s;">
                <a href="{{ route('public.forms.raf') }}" class="btn-primary text-white px-8 py-4 rounded-lg font-semibold inline-flex items-center text-lg hover:scale-105 transform transition-all duration-300">
                    <i class='bx bx-money mr-2 text-xl'></i>
                    Start Remittance Form
                </a>
                <a href="{{ route('public.forms.dar') }}" class="bg-white text-gray-900 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 hover:scale-105 transform transition-all duration-300 inline-flex items-center text-lg">
                    <i class='bx bx-data mr-2 text-xl'></i>
                    Data Access Request
                </a>
            </div>
            
            <!-- Stats -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto animate-fade-in-up" style="animation-delay: 0.4s;">
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-2">1000+</div>
                    <div class="text-white/80">Forms Processed</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-2">99.9%</div>
                    <div class="text-white/80">Uptime</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-2">24/7</div>
                    <div class="text-white/80">Support</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Forms Overview -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <div class="animate-fade-in-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Available Forms
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Choose from our comprehensive range of digital forms designed to meet your specific needs.
                </p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Remittance Application Form -->
            <div class="form-card rounded-xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class='bx bx-money text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-green-600 transition-colors">Remittance Application</h3>
                <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                    Submit remittance applications for international money transfers and financial transactions.
                </p>
                <div class="space-y-2 mb-6">
                    <div class="flex items-center text-sm text-gray-500">
                        <i class='bx bx-time mr-2 text-green-500'></i>
                        <span>Processing: 3-5 business days</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                        <i class='bx bx-shield-check mr-2 text-green-500'></i>
                        <span>Secure & Encrypted</span>
                    </div>
                </div>
                <a href="{{ route('public.forms.raf') }}" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold inline-flex items-center w-full justify-center group-hover:shadow-lg transition-all duration-300">
                    <i class='bx bx-edit mr-2'></i>
                    Start Application
                </a>
            </div>
            
            <!-- Data Access Request Form -->
            <div class="form-card rounded-xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class='bx bx-data text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">Data Access Request</h3>
                <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                    Request access to personal data and information in compliance with data protection regulations.
                </p>
                <div class="space-y-2 mb-6">
                    <div class="flex items-center text-sm text-gray-500">
                        <i class='bx bx-time mr-2 text-blue-500'></i>
                        <span>Processing: 7-10 business days</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                        <i class='bx bx-shield-check mr-2 text-blue-500'></i>
                        <span>GDPR Compliant</span>
                    </div>
                </div>
                <a href="{{ route('public.forms.dar') }}" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold inline-flex items-center w-full justify-center group-hover:shadow-lg transition-all duration-300">
                    <i class='bx bx-edit mr-2'></i>
                    Start Request
                </a>
            </div>
            
            <!-- Data Correction Request Form -->
            <div class="form-card rounded-xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group animate-fade-in-up" style="animation-delay: 0.3s;">
                <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class='bx bx-edit text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors">Data Correction Request</h3>
                <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                    Request corrections to personal data and ensure accuracy of your information.
                </p>
                <div class="space-y-2 mb-6">
                    <div class="flex items-center text-sm text-gray-500">
                        <i class='bx bx-time mr-2 text-orange-500'></i>
                        <span>Processing: 5-7 business days</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                        <i class='bx bx-shield-check mr-2 text-orange-500'></i>
                        <span>Data Protection</span>
                    </div>
                </div>
                <a href="{{ route('public.forms.dcr') }}" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold inline-flex items-center w-full justify-center group-hover:shadow-lg transition-all duration-300">
                    <i class='bx bx-edit mr-2'></i>
                    Start Request
                </a>
            </div>
            
            <!-- Service Request Form -->
            <div class="form-card rounded-xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 group animate-fade-in-up" style="animation-delay: 0.4s;">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class='bx bx-cog text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-purple-600 transition-colors">Service Request</h3>
                <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                    Request various services and support from our organization.
                </p>
                <div class="space-y-2 mb-6">
                    <div class="flex items-center text-sm text-gray-500">
                        <i class='bx bx-time mr-2 text-purple-500'></i>
                        <span>Processing: 2-3 business days</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                        <i class='bx bx-shield-check mr-2 text-purple-500'></i>
                        <span>24/7 Support</span>
                    </div>
                </div>
                <a href="{{ route('public.forms.srf') }}" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold inline-flex items-center w-full justify-center group-hover:shadow-lg transition-all duration-300">
                    <i class='bx bx-edit mr-2'></i>
                    Start Request
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <div class="animate-fade-in-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Why Choose Our Digital Forms?
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Experience the benefits of modern digital form management.
                </p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center group animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class='bx bx-shield-check text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">Secure & Encrypted</h3>
                <p class="text-gray-600 leading-relaxed">
                    Your data is protected with enterprise-grade encryption and security measures.
                </p>
            </div>
            
            <div class="text-center group animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class='bx bx-time text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-green-600 transition-colors">Fast Processing</h3>
                <p class="text-gray-600 leading-relaxed">
                    Submit forms instantly and receive faster processing times compared to paper forms.
                </p>
            </div>
            
            <div class="text-center group animate-fade-in-up" style="animation-delay: 0.3s;">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class='bx bx-trending-up text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-purple-600 transition-colors">Track Progress</h3>
                <p class="text-gray-600 leading-relaxed">
                    Monitor your application status in real-time with our tracking system.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600 relative overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><path d="M20 20c0-11.046-8.954-20-20-20v20h20z"/></g></svg>');"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">
        <div class="animate-fade-in-up">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Ready to Get Started?
            </h2>
            <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                Choose your form and begin the application process today. It's quick, easy, and secure.
            </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up" style="animation-delay: 0.2s;">
            <a href="{{ route('public.forms.raf') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 hover:scale-105 transform transition-all duration-300 inline-flex items-center text-lg">
                <i class='bx bx-money mr-2 text-xl'></i>
                Remittance Form
            </a>
            <a href="{{ route('public.forms.dar') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 hover:scale-105 transform transition-all duration-300 inline-flex items-center text-lg">
                <i class='bx bx-data mr-2 text-xl'></i>
                Data Access Request
            </a>
            <a href="{{ route('public.forms.dcr') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 hover:scale-105 transform transition-all duration-300 inline-flex items-center text-lg">
                <i class='bx bx-edit mr-2 text-xl'></i>
                Data Correction
            </a>
            <a href="{{ route('public.forms.srf') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 hover:scale-105 transform transition-all duration-300 inline-flex items-center text-lg">
                <i class='bx bx-cog mr-2 text-xl'></i>
                Service Request
            </a>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <div class="animate-fade-in-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Get in Touch
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Have questions? We're here to help you with your digital form needs.
                </p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center group animate-fade-in-up" style="animation-delay: 0.1s;">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class='bx bx-phone text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">Phone</h3>
                <p class="text-gray-600 mb-2">+1 (123) 456-7890</p>
                <p class="text-sm text-gray-500">Mon-Fri 9AM-6PM</p>
            </div>
            
            <div class="text-center group animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class='bx bx-envelope text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-green-600 transition-colors">Email</h3>
                <p class="text-gray-600 mb-2">info@bmmbforms.com</p>
                <p class="text-sm text-gray-500">24/7 Support</p>
            </div>
            
            <div class="text-center group animate-fade-in-up" style="animation-delay: 0.3s;">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i class='bx bx-map text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-purple-600 transition-colors">Address</h3>
                <p class="text-gray-600 mb-2">123 Business St</p>
                <p class="text-sm text-gray-500">City, State 12345</p>
            </div>
        </div>
    </div>
</section>
@endsection
