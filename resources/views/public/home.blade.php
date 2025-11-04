@extends('layouts.public')

@section('title', 'BMMB Digital Forms - Home')

@section('content')
<!-- Available Forms Section -->
<section id="features" class="py-24" style="background: #FE8000;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
            <div class="inline-block mb-4 px-4 py-2 bg-white/10 backdrop-blur-md rounded-full text-sm font-semibold border border-white/20 text-white">
                <i class='bx bx-collection mr-2'></i>Choose Your Form
            </div>
            <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-4">
                Digital Forms
            </h2>
            <p class="text-xl text-white/90 max-w-3xl mx-auto">
                Secure, efficient, and compliant digital forms designed to meet your specific needs
            </p>
        </div>
        
        <!-- Form Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Remittance Application Form -->
            <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-primary-300 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary-100 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative">
                    <div class="w-16 h-16 bg-primary-500 rounded-xl flex items-center justify-center mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-primary-500/20">
                        <i class='bx bx-money text-white text-3xl'></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-primary-600 transition-colors">Remittance Application</h3>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        International money transfers and financial transactions made easy.
                    </p>
                    <a href="{{ route('public.forms.raf') }}" class="block w-full bg-primary-500 hover:bg-primary-600 text-white text-center px-6 py-3.5 rounded-xl font-bold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-xl group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
            
            <!-- Data Access Request Form -->
            <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-primary-300 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary-100 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative">
                    <div class="w-16 h-16 bg-primary-500 rounded-xl flex items-center justify-center mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-primary-500/20">
                        <i class='bx bx-data text-white text-3xl'></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-primary-600 transition-colors">Data Access Request</h3>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Request access to personal data with full GDPR compliance.
                    </p>
                    <a href="{{ route('public.forms.dar') }}" class="block w-full bg-primary-500 hover:bg-primary-600 text-white text-center px-6 py-3.5 rounded-xl font-bold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-xl group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
            
            <!-- Data Correction Request Form -->
            <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-primary-300 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary-100 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative">
                    <div class="w-16 h-16 bg-primary-500 rounded-xl flex items-center justify-center mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-primary-500/20">
                        <i class='bx bx-edit text-white text-3xl'></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-primary-600 transition-colors">Data Correction</h3>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Correct and update your personal data with ease.
                    </p>
                    <a href="{{ route('public.forms.dcr') }}" class="block w-full bg-primary-500 hover:bg-primary-600 text-white text-center px-6 py-3.5 rounded-xl font-bold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-xl group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
            
            <!-- Service Request Form -->
            <div class="group relative bg-white rounded-2xl p-8 border border-gray-200 hover:border-primary-300 hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary-100 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative">
                    <div class="w-16 h-16 bg-primary-500 rounded-xl flex items-center justify-center mb-6 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg shadow-primary-500/20">
                        <i class='bx bx-cog text-white text-3xl'></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-primary-600 transition-colors">Service Request</h3>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        Request various services and support for your needs.
                    </p>
                    <a href="{{ route('public.forms.srf') }}" class="block w-full bg-primary-500 hover:bg-primary-600 text-white text-center px-6 py-3.5 rounded-xl font-bold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-xl group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
