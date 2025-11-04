@extends('layouts.public')

@section('title', 'BMMB Digital Forms - Welcome')

@section('content')
<!-- Available Forms Section -->
<section id="features" class="min-h-screen flex items-center bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-16">
        <!-- Section Header -->
        <div class="text-center mb-10">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
                Digital Forms
            </h2>
            <p class="text-sm text-gray-600 max-w-3xl mx-auto">
                Secure, efficient, and compliant digital forms designed to meet your specific needs
            </p>
        </div>
        
        <!-- Form Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Remittance Application Form -->
            <div class="group relative bg-white rounded-xl p-5 border border-gray-200 hover:border-primary-300 hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1 flex flex-col h-full">
                <div class="absolute top-0 right-0 w-20 h-20 bg-primary-100 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative flex flex-col h-full">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mb-3 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-md shadow-primary-100/20">
                        <i class='bx bx-money text-primary-600 text-xl'></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors">Remittance Application</h3>
                    <p class="text-gray-600 mb-3 leading-relaxed flex-grow text-xs">
                        International money transfers and financial transactions made easy.
                    </p>
                    <a href="{{ route('public.forms.raf') }}" class="block w-full bg-primary-100 hover:bg-primary-200 text-primary-600 text-center px-4 py-2 rounded-lg font-semibold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center mt-auto text-xs">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-base group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
            
            <!-- Data Access Request Form -->
            <div class="group relative bg-white rounded-xl p-5 border border-gray-200 hover:border-primary-300 hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1 flex flex-col h-full">
                <div class="absolute top-0 right-0 w-20 h-20 bg-primary-100 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative flex flex-col h-full">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mb-3 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-md shadow-primary-100/20">
                        <i class='bx bx-data text-primary-600 text-xl'></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors">Data Access Request</h3>
                    <p class="text-gray-600 mb-3 leading-relaxed flex-grow text-xs">
                        Request access to personal data with full GDPR compliance.
                    </p>
                    <a href="{{ route('public.forms.dar') }}" class="block w-full bg-primary-100 hover:bg-primary-200 text-primary-600 text-center px-4 py-2 rounded-lg font-semibold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center mt-auto text-xs">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-base group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
            
            <!-- Data Correction Request Form -->
            <div class="group relative bg-white rounded-xl p-5 border border-gray-200 hover:border-primary-300 hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1 flex flex-col h-full">
                <div class="absolute top-0 right-0 w-20 h-20 bg-primary-100 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative flex flex-col h-full">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mb-3 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-md shadow-primary-100/20">
                        <i class='bx bx-edit text-primary-600 text-xl'></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors">Data Correction</h3>
                    <p class="text-gray-600 mb-3 leading-relaxed flex-grow text-xs">
                        Correct and update your personal data with ease.
                    </p>
                    <a href="{{ route('public.forms.dcr') }}" class="block w-full bg-primary-100 hover:bg-primary-200 text-primary-600 text-center px-4 py-2 rounded-lg font-semibold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center mt-auto text-xs">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-base group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
            
            <!-- Service Request Form -->
            <div class="group relative bg-white rounded-xl p-5 border border-gray-200 hover:border-primary-300 hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1 flex flex-col h-full">
                <div class="absolute top-0 right-0 w-20 h-20 bg-primary-100 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative flex flex-col h-full">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mb-3 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-md shadow-primary-100/20">
                        <i class='bx bx-cog text-primary-600 text-xl'></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-2 group-hover:text-primary-600 transition-colors">Service Request</h3>
                    <p class="text-gray-600 mb-3 leading-relaxed flex-grow text-xs">
                        Request various services and support for your needs.
                    </p>
                    <a href="{{ route('public.forms.srf') }}" class="block w-full bg-primary-100 hover:bg-primary-200 text-primary-600 text-center px-4 py-2 rounded-lg font-semibold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center mt-auto text-xs">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 text-base group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

