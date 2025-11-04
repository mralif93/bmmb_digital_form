@extends('layouts.app')

@section('title', 'Dashboard - BMMB Digital Forms')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Welcome Section -->
    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    Welcome back, {{ Auth::user()->first_name }}!
                </h1>
                <p class="text-sm text-gray-600">
                    Manage your form submissions and track your requests
                </p>
            </div>
            <div class="hidden md:block">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
                    <i class='bx bx-user text-2xl text-primary-600'></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('public.forms.raf') }}" class="bg-white rounded-xl shadow-md p-5 border border-gray-200 hover:border-primary-300 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class='bx bx-money text-xl text-primary-600'></i>
                </div>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1 group-hover:text-primary-600 transition-colors">Remittance Application</h3>
            <p class="text-xs text-gray-600">Start new application</p>
        </a>

        <a href="{{ route('public.forms.dar') }}" class="bg-white rounded-xl shadow-md p-5 border border-gray-200 hover:border-primary-300 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class='bx bx-data text-xl text-primary-600'></i>
                </div>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1 group-hover:text-primary-600 transition-colors">Data Access Request</h3>
            <p class="text-xs text-gray-600">Request data access</p>
        </a>

        <a href="{{ route('public.forms.dcr') }}" class="bg-white rounded-xl shadow-md p-5 border border-gray-200 hover:border-primary-300 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class='bx bx-edit text-xl text-primary-600'></i>
                </div>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1 group-hover:text-primary-600 transition-colors">Data Correction</h3>
            <p class="text-xs text-gray-600">Correct your data</p>
        </a>

        <a href="{{ route('public.forms.srf') }}" class="bg-white rounded-xl shadow-md p-5 border border-gray-200 hover:border-primary-300 hover:shadow-lg transition-all duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i class='bx bx-cog text-xl text-primary-600'></i>
                </div>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1 group-hover:text-primary-600 transition-colors">Service Request</h3>
            <p class="text-xs text-gray-600">Request services</p>
        </a>
    </div>

    <!-- Account Information -->
    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Account Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Full Name</p>
                <p class="text-sm text-gray-900">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Email</p>
                <p class="text-sm text-gray-900">{{ Auth::user()->email }}</p>
            </div>
            @if(Auth::user()->phone)
            <div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Phone</p>
                <p class="text-sm text-gray-900">{{ Auth::user()->phone }}</p>
            </div>
            @endif
            <div>
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1">Account Status</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                    {{ ucfirst(Auth::user()->status) }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection

