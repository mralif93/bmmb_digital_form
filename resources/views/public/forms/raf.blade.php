@extends('layouts.public')

@section('title', 'Remittance Application Form - BMMB Digital Forms')

@section('content')
<!-- Hero Section -->
<section class="form-section py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Remittance Application Form
            </h1>
            <p class="text-lg text-white/90 mb-6 max-w-2xl mx-auto">
                Submit your remittance application for international money transfers and financial transactions.
            </p>
            <div class="flex items-center justify-center space-x-4 text-white/80">
                <div class="flex items-center">
                    <i class='bx bx-time mr-2'></i>
                    <span>Processing: 3-5 business days</span>
                </div>
                <div class="flex items-center">
                    <i class='bx bx-shield-check mr-2'></i>
                    <span>Secure & Encrypted</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Form Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="form-card rounded-xl p-8 shadow-xl" x-data="{ currentStep: 1 }">
            <!-- Progress Indicator -->
            <div class="mb-8">
                <div class="flex items-center justify-between relative">
                    <!-- Step 1 -->
                    <div class="flex items-center relative z-10 group cursor-pointer" @click="currentStep = 1">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full flex items-center justify-center text-sm font-bold shadow-lg transition-all duration-300" 
                             :class="{ 'ring-4 ring-blue-200 scale-110': currentStep === 1 }">
                            <i class='bx bx-user text-lg' x-show="currentStep > 1" x-cloak></i>
                            <span x-show="currentStep <= 1" x-cloak>1</span>
                        </div>
                        <div class="ml-3">
                            <span class="text-sm font-semibold text-gray-700 block" 
                                  :class="{ 'text-blue-600': currentStep >= 1 }">Personal Information</span>
                            <span class="text-xs text-gray-500" x-show="currentStep > 1" x-cloak>Completed</span>
                        </div>
                    </div>
                    
                    <!-- Connector Line 1 -->
                    <div class="flex-1 h-0.5 bg-gray-300 mx-6" 
                         :class="{ 'bg-gradient-to-r from-blue-600 to-purple-600': currentStep > 1 }"></div>
                    
                    <!-- Step 2 -->
                    <div class="flex items-center relative z-10 group cursor-pointer" @click="currentStep = 2">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shadow-lg transition-all duration-300"
                             :class="currentStep >= 2 ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white ring-4 ring-blue-200 scale-110' : 'bg-gray-300 text-gray-600'">
                            <i class='bx bx-money text-lg' x-show="currentStep > 2" x-cloak></i>
                            <span x-show="currentStep <= 2" x-cloak>2</span>
                        </div>
                        <div class="ml-3">
                            <span class="text-sm font-semibold block"
                                  :class="currentStep >= 2 ? 'text-blue-600' : 'text-gray-500'">Remittance Details</span>
                            <span class="text-xs text-gray-500" x-show="currentStep > 2" x-cloak>Completed</span>
                        </div>
                    </div>
                    
                    <!-- Connector Line 2 -->
                    <div class="flex-1 h-0.5 bg-gray-300 mx-6" 
                         :class="{ 'bg-gradient-to-r from-blue-600 to-purple-600': currentStep > 2 }"></div>
                    
                    <!-- Step 3 -->
                    <div class="flex items-center relative z-10 group cursor-pointer" @click="currentStep = 3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shadow-lg transition-all duration-300"
                             :class="currentStep >= 3 ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white ring-4 ring-blue-200 scale-110' : 'bg-gray-300 text-gray-600'">
                            <i class='bx bx-user-circle text-lg' x-show="currentStep > 3" x-cloak></i>
                            <span x-show="currentStep <= 3" x-cloak>3</span>
                        </div>
                        <div class="ml-3">
                            <span class="text-sm font-semibold block"
                                  :class="currentStep >= 3 ? 'text-blue-600' : 'text-gray-500'">Beneficiary Information</span>
                            <span class="text-xs text-gray-500" x-show="currentStep > 3" x-cloak>Completed</span>
                        </div>
                    </div>
                    
                    <!-- Connector Line 3 -->
                    <div class="flex-1 h-0.5 bg-gray-300 mx-6" 
                         :class="{ 'bg-gradient-to-r from-blue-600 to-purple-600': currentStep > 3 }"></div>
                    
                    <!-- Step 4 -->
                    <div class="flex items-center relative z-10 group cursor-pointer" @click="currentStep = 4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shadow-lg transition-all duration-300"
                             :class="currentStep >= 4 ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white ring-4 ring-blue-200 scale-110' : 'bg-gray-300 text-gray-600'">
                            <i class='bx bx-check text-lg' x-show="currentStep > 4" x-cloak></i>
                            <span x-show="currentStep <= 4" x-cloak>4</span>
                        </div>
                        <div class="ml-3">
                            <span class="text-sm font-semibold block"
                                  :class="currentStep >= 4 ? 'text-blue-600' : 'text-gray-500'">Review & Submit</span>
                            <span class="text-xs text-gray-500" x-show="currentStep > 4" x-cloak>Completed</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form id="raf-form" class="space-y-8">
                @csrf
                
                <!-- Step 1: Personal Information -->
                <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Personal Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="applicant_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="applicant_name" name="applicant_name" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="applicant_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="applicant_email" name="applicant_email" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="applicant_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="applicant_phone" name="applicant_phone" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="applicant_id_type" class="block text-sm font-medium text-gray-700 mb-2">
                                ID Type <span class="text-red-500">*</span>
                            </label>
                            <select id="applicant_id_type" name="applicant_id_type" required
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select ID Type</option>
                                <option value="passport">Passport</option>
                                <option value="national_id">National ID</option>
                                <option value="drivers_license">Driver's License</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="applicant_id_number" class="block text-sm font-medium text-gray-700 mb-2">
                                ID Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="applicant_id_number" name="applicant_id_number" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="applicant_id_expiry_date" class="block text-sm font-medium text-gray-700 mb-2">
                                ID Expiry Date
                            </label>
                            <input type="date" id="applicant_id_expiry_date" name="applicant_id_expiry_date"
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="applicant_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea id="applicant_address" name="applicant_address" rows="3" required
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <label for="applicant_city" class="block text-sm font-medium text-gray-700 mb-2">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="applicant_city" name="applicant_city" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="applicant_state" class="block text-sm font-medium text-gray-700 mb-2">
                                State/Province <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="applicant_state" name="applicant_state" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="applicant_postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Postal Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="applicant_postal_code" name="applicant_postal_code" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="applicant_country" class="block text-sm font-medium text-gray-700 mb-2">
                            Country <span class="text-red-500">*</span>
                        </label>
                        <select id="applicant_country" name="applicant_country" required
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Country</option>
                            <option value="US">United States</option>
                            <option value="CA">Canada</option>
                            <option value="GB">United Kingdom</option>
                            <option value="AU">Australia</option>
                            <option value="DE">Germany</option>
                            <option value="FR">France</option>
                            <option value="JP">Japan</option>
                            <option value="SG">Singapore</option>
                            <option value="MY">Malaysia</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end mt-8">
                        <button type="button" @click="currentStep = 2" 
                                class="btn-primary text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                            Next Step <i class='bx bx-right-arrow-alt ml-2'></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 2: Remittance Details -->
                <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Remittance Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="remittance_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Remittance Amount <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="remittance_amount" name="remittance_amount" step="0.01" min="0.01" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="remittance_currency" class="block text-sm font-medium text-gray-700 mb-2">
                                Currency <span class="text-red-500">*</span>
                            </label>
                            <select id="remittance_currency" name="remittance_currency" required
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Currency</option>
                                <option value="USD">USD - US Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="GBP">GBP - British Pound</option>
                                <option value="JPY">JPY - Japanese Yen</option>
                                <option value="CAD">CAD - Canadian Dollar</option>
                                <option value="AUD">AUD - Australian Dollar</option>
                                <option value="CHF">CHF - Swiss Franc</option>
                                <option value="CNY">CNY - Chinese Yuan</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="remittance_purpose" class="block text-sm font-medium text-gray-700 mb-2">
                                Purpose <span class="text-red-500">*</span>
                            </label>
                            <select id="remittance_purpose" name="remittance_purpose" required
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Purpose</option>
                                <option value="family_support">Family Support</option>
                                <option value="education">Education</option>
                                <option value="medical">Medical</option>
                                <option value="business">Business</option>
                                <option value="investment">Investment</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="remittance_frequency" class="block text-sm font-medium text-gray-700 mb-2">
                                Frequency <span class="text-red-500">*</span>
                            </label>
                            <select id="remittance_frequency" name="remittance_frequency" required
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Frequency</option>
                                <option value="one_time">One Time</option>
                                <option value="monthly">Monthly</option>
                                <option value="quarterly">Quarterly</option>
                                <option value="annually">Annually</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="remittance_purpose_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Purpose Description
                        </label>
                        <textarea id="remittance_purpose_description" name="remittance_purpose_description" rows="3"
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <select id="payment_method" name="payment_method" required
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Payment Method</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="debit_card">Debit Card</option>
                                <option value="cash">Cash</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="payment_source" class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Source <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="payment_source" name="payment_source" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="flex justify-between mt-8">
                        <button type="button" @click="currentStep = 1" 
                                class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-400 transition-colors">
                            <i class='bx bx-left-arrow-alt mr-2'></i>
                            Previous
                        </button>
                        <button type="button" @click="currentStep = 3" 
                                class="btn-primary text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                            Next Step <i class='bx bx-right-arrow-alt ml-2'></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 3: Beneficiary Information -->
                <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Beneficiary Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="beneficiary_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Beneficiary Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="beneficiary_name" name="beneficiary_name" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="beneficiary_relationship" class="block text-sm font-medium text-gray-700 mb-2">
                                Relationship <span class="text-red-500">*</span>
                            </label>
                            <select id="beneficiary_relationship" name="beneficiary_relationship" required
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Relationship</option>
                                <option value="spouse">Spouse</option>
                                <option value="parent">Parent</option>
                                <option value="child">Child</option>
                                <option value="sibling">Sibling</option>
                                <option value="relative">Relative</option>
                                <option value="friend">Friend</option>
                                <option value="business_partner">Business Partner</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="beneficiary_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input type="tel" id="beneficiary_phone" name="beneficiary_phone"
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="beneficiary_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address
                            </label>
                            <input type="email" id="beneficiary_email" name="beneficiary_email"
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="beneficiary_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea id="beneficiary_address" name="beneficiary_address" rows="3" required
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <div>
                            <label for="beneficiary_city" class="block text-sm font-medium text-gray-700 mb-2">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="beneficiary_city" name="beneficiary_city" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="beneficiary_state" class="block text-sm font-medium text-gray-700 mb-2">
                                State/Province <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="beneficiary_state" name="beneficiary_state" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="beneficiary_postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Postal Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="beneficiary_postal_code" name="beneficiary_postal_code" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="beneficiary_country" class="block text-sm font-medium text-gray-700 mb-2">
                            Country <span class="text-red-500">*</span>
                        </label>
                        <select id="beneficiary_country" name="beneficiary_country" required
                                class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Country</option>
                            <option value="US">United States</option>
                            <option value="CA">Canada</option>
                            <option value="GB">United Kingdom</option>
                            <option value="AU">Australia</option>
                            <option value="DE">Germany</option>
                            <option value="FR">France</option>
                            <option value="JP">Japan</option>
                            <option value="SG">Singapore</option>
                            <option value="MY">Malaysia</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-between mt-8">
                        <button type="button" @click="currentStep = 2" 
                                class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-400 transition-colors">
                            <i class='bx bx-left-arrow-alt mr-2'></i>
                            Previous
                        </button>
                        <button type="button" @click="currentStep = 4" 
                                class="btn-primary text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                            Next Step <i class='bx bx-right-arrow-alt ml-2'></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 4: Review & Submit -->
                <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Review & Submit</h3>
                    
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div><strong>Name:</strong> <span id="review_applicant_name">-</span></div>
                            <div><strong>Email:</strong> <span id="review_applicant_email">-</span></div>
                            <div><strong>Phone:</strong> <span id="review_applicant_phone">-</span></div>
                            <div><strong>ID Type:</strong> <span id="review_applicant_id_type">-</span></div>
                            <div><strong>ID Number:</strong> <span id="review_applicant_id_number">-</span></div>
                            <div><strong>Country:</strong> <span id="review_applicant_country">-</span></div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Remittance Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div><strong>Amount:</strong> <span id="review_remittance_amount">-</span></div>
                            <div><strong>Currency:</strong> <span id="review_remittance_currency">-</span></div>
                            <div><strong>Purpose:</strong> <span id="review_remittance_purpose">-</span></div>
                            <div><strong>Frequency:</strong> <span id="review_remittance_frequency">-</span></div>
                            <div><strong>Payment Method:</strong> <span id="review_payment_method">-</span></div>
                            <div><strong>Payment Source:</strong> <span id="review_payment_source">-</span></div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Beneficiary Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div><strong>Name:</strong> <span id="review_beneficiary_name">-</span></div>
                            <div><strong>Relationship:</strong> <span id="review_beneficiary_relationship">-</span></div>
                            <div><strong>Phone:</strong> <span id="review_beneficiary_phone">-</span></div>
                            <div><strong>Email:</strong> <span id="review_beneficiary_email">-</span></div>
                            <div><strong>Country:</strong> <span id="review_beneficiary_country">-</span></div>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3 mb-6">
                        <input type="checkbox" id="terms_agreement" name="terms_agreement" required
                               class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="terms_agreement" class="text-sm text-gray-700">
                            I agree to the <a href="#" class="text-blue-600 hover:underline">Terms and Conditions</a> 
                            and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                        </label>
                    </div>
                    
                    <div class="flex justify-between mt-8">
                        <button type="button" @click="currentStep = 3" 
                                class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-400 transition-colors">
                            <i class='bx bx-left-arrow-alt mr-2'></i>
                            Previous
                        </button>
                        <button type="submit" onclick="submitForm('raf-form', 'Remittance application submitted successfully!')" 
                                class="btn-primary text-white px-8 py-3 rounded-lg font-semibold">
                            <i class='bx bx-check mr-2'></i>
                            Submit Application
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Information Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                Important Information
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Please read the following information before submitting your application.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-shield-check text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Security</h3>
                <p class="text-gray-600">
                    All information is encrypted and secure. We follow industry-standard security practices.
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-time text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Processing Time</h3>
                <p class="text-gray-600">
                    Applications are typically processed within 3-5 business days. You'll receive email updates.
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-support text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Support</h3>
                <p class="text-gray-600">
                    Need help? Contact our support team at support@bmmb.com or call +1 (555) 123-4567.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Update review section when moving to step 4
    function updateReview() {
        document.getElementById('review_applicant_name').textContent = document.getElementById('applicant_name').value || '-';
        document.getElementById('review_applicant_email').textContent = document.getElementById('applicant_email').value || '-';
        document.getElementById('review_applicant_phone').textContent = document.getElementById('applicant_phone').value || '-';
        document.getElementById('review_applicant_id_type').textContent = document.getElementById('applicant_id_type').value || '-';
        document.getElementById('review_applicant_id_number').textContent = document.getElementById('applicant_id_number').value || '-';
        document.getElementById('review_applicant_country').textContent = document.getElementById('applicant_country').value || '-';
        
        document.getElementById('review_remittance_amount').textContent = document.getElementById('remittance_amount').value || '-';
        document.getElementById('review_remittance_currency').textContent = document.getElementById('remittance_currency').value || '-';
        document.getElementById('review_remittance_purpose').textContent = document.getElementById('remittance_purpose').value || '-';
        document.getElementById('review_remittance_frequency').textContent = document.getElementById('remittance_frequency').value || '-';
        document.getElementById('review_payment_method').textContent = document.getElementById('payment_method').value || '-';
        document.getElementById('review_payment_source').textContent = document.getElementById('payment_source').value || '-';
        
        document.getElementById('review_beneficiary_name').textContent = document.getElementById('beneficiary_name').value || '-';
        document.getElementById('review_beneficiary_relationship').textContent = document.getElementById('beneficiary_relationship').value || '-';
        document.getElementById('review_beneficiary_phone').textContent = document.getElementById('beneficiary_phone').value || '-';
        document.getElementById('review_beneficiary_email').textContent = document.getElementById('beneficiary_email').value || '-';
        document.getElementById('review_beneficiary_country').textContent = document.getElementById('beneficiary_country').value || '-';
    }
    
    // Override nextStep function to update review
    const originalNextStep = nextStep;
    nextStep = function(currentStep, nextStep) {
        if (nextStep === 4) {
            updateReview();
        }
        originalNextStep(currentStep, nextStep);
    };
</script>
@endpush
