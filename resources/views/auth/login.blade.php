@extends('layouts.public')

@section('title', 'Login - BMMB Digital Forms')

@push('styles')
<style>
    body {
        overflow-y: auto !important;
    }
    main {
        min-height: auto !important;
    }
</style>
@endpush

@section('content')
<!-- Modern Hero Section with Login Form -->
<section class="relative min-h-[85vh] flex items-center overflow-hidden" style="background: linear-gradient(135deg, #fb923c 0%, #f97316 50%, #ea580c 100%);">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-0 left-0 w-96 h-96 bg-white/5 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-6">
        <div class="grid lg:grid-cols-2 gap-8 items-center">
            <!-- Left Column: Content -->
            <div class="text-white">
                <div class="inline-block mb-4 px-3 py-1.5 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold border border-white/20 animate-fade-in-up">
                    <i class='bx bx-shield-check mr-1'></i>Secure Login
                </div>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold mb-4 leading-tight animate-fade-in-up" style="animation-delay: 0.1s;">
                    Welcome Back <span class="block">to BMMB Digital</span>
                </h1>
                <p class="text-base md:text-lg text-white/90 mb-6 leading-relaxed animate-fade-in-up" style="animation-delay: 0.2s;">
                    Access your secure digital forms management system with industry-leading security.
                </p>
                <div class="flex items-center space-x-4 animate-fade-in-up" style="animation-delay: 0.3s;">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center border border-white/20">
                            <i class='bx bx-lock-alt text-white'></i>
                        </div>
                        <div class="text-xs text-white/80">256-bit Encryption</div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center border border-white/20">
                            <i class='bx bx-shield-check text-white'></i>
                        </div>
                        <div class="text-xs text-white/80">GDPR Compliant</div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Login Form -->
            <div class="animate-fade-in-up" style="animation-delay: 0.4s;">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-2xl">
                    <!-- Login Form -->
                    <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <h2 class="text-xl font-bold text-white mb-4">Sign in to your account</h2>
                            
                            <div class="space-y-3">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-white mb-1">
                                        Email address
                                    </label>
                                    <input id="email" name="email" type="email" autocomplete="email" required 
                                           class="w-full px-3 py-2 bg-white/20 backdrop-blur-md border border-white/30 rounded-lg text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent text-sm @error('email') border-red-400 @enderror" 
                                           placeholder="Enter your email">
                                    @error('email')
                                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="password" class="block text-sm font-medium text-white mb-1">
                                        Password
                                    </label>
                                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                                           class="w-full px-3 py-2 bg-white/20 backdrop-blur-md border border-white/30 rounded-lg text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white focus:border-transparent text-sm @error('password') border-red-400 @enderror" 
                                           placeholder="Enter your password">
                                    @error('password')
                                        <p class="mt-1 text-xs text-red-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-4">
                                <div class="flex items-center">
                                    <input id="remember" name="remember" type="checkbox" 
                                           class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-white/30 rounded bg-white/20">
                                    <label for="remember" class="ml-2 block text-xs text-white">
                                        Remember me
                                    </label>
                                </div>

                                <div class="text-xs">
                                    <a href="#" class="font-medium text-white hover:text-white/80">
                                        Forgot password?
                                    </a>
                                </div>
                            </div>

                            <button type="submit" 
                                    class="mt-4 w-full flex justify-center items-center py-2.5 px-4 bg-white text-orange-600 rounded-lg font-bold hover:shadow-xl hover:scale-105 transform transition-all duration-300">
                                <i class='bx bx-lock-alt mr-2'></i>
                                Sign in
                            </button>

                            <div class="mt-3 text-center">
                                <p class="text-xs text-white/80">
                                    Don't have an account?
                                    <a href="{{ route('register') }}" class="font-medium text-white hover:text-white/80">
                                        Create one
                                    </a>
                                </p>
                            </div>
                        </div>
                    </form>

                    <!-- Demo Credentials -->
                    <div class="mt-4 p-3 bg-white/10 backdrop-blur-md rounded-lg border border-white/20">
                        <h3 class="text-xs font-medium text-white mb-1">
                            <i class='bx bx-info-circle mr-1'></i>Demo Credentials
                        </h3>
                        <div class="text-xs text-white/90 space-y-0.5">
                            <p><strong>Admin:</strong> admin@bmmb.com / password</p>
                            <p><strong>User:</strong> john.doe@example.com / password</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- E-Forms Section -->
<section class="py-12 bg-white/5 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-8">
            <div class="inline-block mb-3 px-4 py-2 bg-white/10 backdrop-blur-md rounded-full text-sm font-bold text-white border border-white/20">
                <i class='bx bx-collection mr-2'></i>Go to E-Forms
            </div>
            <h2 class="text-2xl md:text-3xl font-extrabold text-white mb-2">
                Access Digital Forms
            </h2>
            <p class="text-sm text-white/80 max-w-2xl mx-auto">
                Choose from our available digital forms. No login required to get started.
            </p>
        </div>
        
        <!-- E-Form Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Remittance Application Form -->
            <div class="group relative bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 hover:border-white/40 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-24 h-24 bg-green-500/20 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mb-4 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                        <i class='bx bx-money text-white text-2xl'></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-green-300 transition-colors">Remittance Application</h3>
                    <p class="text-white/70 text-sm mb-4 leading-relaxed">
                        International money transfers and financial transactions.
                    </p>
                    <a href="{{ route('public.forms.raf') }}" class="block w-full bg-white/20 hover:bg-white/30 text-white text-center px-4 py-2.5 rounded-lg font-bold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center text-sm border border-white/30">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
            
            <!-- Data Access Request Form -->
            <div class="group relative bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 hover:border-white/40 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/20 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center mb-4 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                        <i class='bx bx-data text-white text-2xl'></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-blue-300 transition-colors">Data Access Request</h3>
                    <p class="text-white/70 text-sm mb-4 leading-relaxed">
                        Request access to personal data with full GDPR compliance.
                    </p>
                    <a href="{{ route('public.forms.dar') }}" class="block w-full bg-white/20 hover:bg-white/30 text-white text-center px-4 py-2.5 rounded-lg font-bold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center text-sm border border-white/30">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
            
            <!-- Data Correction Request Form -->
            <div class="group relative bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 hover:border-white/40 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/20 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center mb-4 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                        <i class='bx bx-edit text-white text-2xl'></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-orange-300 transition-colors">Data Correction</h3>
                    <p class="text-white/70 text-sm mb-4 leading-relaxed">
                        Correct and update your personal data with ease.
                    </p>
                    <a href="{{ route('public.forms.dcr') }}" class="block w-full bg-white/20 hover:bg-white/30 text-white text-center px-4 py-2.5 rounded-lg font-bold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center text-sm border border-white/30">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
            
            <!-- Service Request Form -->
            <div class="group relative bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20 hover:border-white/40 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-24 h-24 bg-purple-500/20 rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mb-4 transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-lg">
                        <i class='bx bx-cog text-white text-2xl'></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 group-hover:text-purple-300 transition-colors">Service Request</h3>
                    <p class="text-white/70 text-sm mb-4 leading-relaxed">
                        Request various services and support for your needs.
                    </p>
                    <a href="{{ route('public.forms.srf') }}" class="block w-full bg-white/20 hover:bg-white/30 text-white text-center px-4 py-2.5 rounded-lg font-bold transition-all duration-300 transform group-hover:scale-105 inline-flex items-center justify-center text-sm border border-white/30">
                        <span>Start Form</span>
                        <i class='bx bx-right-arrow-alt ml-2 group-hover:translate-x-1 transition-transform'></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
