@extends('layouts.public')

@section('title', 'Data Access Request Form - BMMB Digital Forms')

@section('content')
<!-- Hero Section -->
<section class="form-section py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Data Access Request Form
            </h1>
            <p class="text-lg text-white/90 mb-6 max-w-2xl mx-auto">
                Request access to your personal data and information in compliance with data protection regulations.
            </p>
            <div class="flex items-center justify-center space-x-4 text-white/80">
                <div class="flex items-center">
                    <i class='bx bx-time mr-2'></i>
                    <span>Processing: 7-10 business days</span>
                </div>
                <div class="flex items-center">
                    <i class='bx bx-shield-check mr-2'></i>
                    <span>GDPR Compliant</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Form Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="form-card rounded-xl p-8 shadow-xl">
            <!-- Progress Indicator -->
            <div class="mb-8">
                <div class="flex items-center justify-between relative">
                    <!-- Step 1 -->
                    <div class="flex items-center relative z-10 group cursor-pointer" @click="currentStep = 1">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full flex items-center justify-center text-sm font-bold shadow-lg transition-all duration-300" 
                             :class="{ 'ring-4 ring-blue-200 scale-110': currentStep === 1 }">
                            <i class='bx bx-user text-lg' x-show="currentStep > 1"></i>
                            <span x-show="currentStep <= 1">1</span>
                        </div>
                        <div class="ml-3">
                            <span class="text-sm font-semibold text-gray-700 block" 
                                  :class="{ 'text-blue-600': currentStep >= 1 }">Personal Information</span>
                            <span class="text-xs text-gray-500" x-show="currentStep > 1">Completed</span>
                        </div>
                    </div>
                    
                    <!-- Connector Line 1 -->
                    <div class="flex-1 h-0.5 bg-gray-300 mx-6" 
                         :class="{ 'bg-gradient-to-r from-blue-600 to-purple-600': currentStep > 1 }"></div>
                    
                    <!-- Step 2 -->
                    <div class="flex items-center relative z-10 group cursor-pointer" @click="currentStep = 2">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shadow-lg transition-all duration-300"
                             :class="currentStep >= 2 ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white ring-4 ring-blue-200 scale-110' : 'bg-gray-300 text-gray-600'">
                            <i class='bx bx-data text-lg' x-show="currentStep > 2"></i>
                            <span x-show="currentStep <= 2">2</span>
                        </div>
                        <div class="ml-3">
                            <span class="text-sm font-semibold block"
                                  :class="currentStep >= 2 ? 'text-blue-600' : 'text-gray-500'">Request Details</span>
                            <span class="text-xs text-gray-500" x-show="currentStep > 2">Completed</span>
                        </div>
                    </div>
                    
                    <!-- Connector Line 2 -->
                    <div class="flex-1 h-0.5 bg-gray-300 mx-6" 
                         :class="{ 'bg-gradient-to-r from-blue-600 to-purple-600': currentStep > 2 }"></div>
                    
                    <!-- Step 3 -->
                    <div class="flex items-center relative z-10 group cursor-pointer" @click="currentStep = 3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shadow-lg transition-all duration-300"
                             :class="currentStep >= 3 ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white ring-4 ring-blue-200 scale-110' : 'bg-gray-300 text-gray-600'">
                            <i class='bx bx-shield-check text-lg' x-show="currentStep > 3"></i>
                            <span x-show="currentStep <= 3">3</span>
                        </div>
                        <div class="ml-3">
                            <span class="text-sm font-semibold block"
                                  :class="currentStep >= 3 ? 'text-blue-600' : 'text-gray-500'">Legal Basis</span>
                            <span class="text-xs text-gray-500" x-show="currentStep > 3">Completed</span>
                        </div>
                    </div>
                    
                    <!-- Connector Line 3 -->
                    <div class="flex-1 h-0.5 bg-gray-300 mx-6" 
                         :class="{ 'bg-gradient-to-r from-blue-600 to-purple-600': currentStep > 3 }"></div>
                    
                    <!-- Step 4 -->
                    <div class="flex items-center relative z-10 group cursor-pointer" @click="currentStep = 4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shadow-lg transition-all duration-300"
                             :class="currentStep >= 4 ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white ring-4 ring-blue-200 scale-110' : 'bg-gray-300 text-gray-600'">
                            <i class='bx bx-check text-lg' x-show="currentStep > 4"></i>
                            <span x-show="currentStep <= 4">4</span>
                        </div>
                        <div class="ml-3">
                            <span class="text-sm font-semibold block"
                                  :class="currentStep >= 4 ? 'text-blue-600' : 'text-gray-500'">Review & Submit</span>
                            <span class="text-xs text-gray-500" x-show="currentStep > 4">Completed</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form id="dar-form" class="space-y-8" x-data="{ currentStep: 1 }">
                @csrf
                
                <!-- Step 1: Personal Information -->
                <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Personal Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="data_subject_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="data_subject_name" name="data_subject_name" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="data_subject_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="data_subject_email" name="data_subject_email" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="data_subject_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input type="tel" id="data_subject_phone" name="data_subject_phone"
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="data_subject_id_type" class="block text-sm font-medium text-gray-700 mb-2">
                                ID Type <span class="text-red-500">*</span>
                            </label>
                            <select id="data_subject_id_type" name="data_subject_id_type" required
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select ID Type</option>
                                <option value="passport">Passport</option>
                                <option value="national_id">National ID</option>
                                <option value="drivers_license">Driver's License</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="data_subject_id_number" class="block text-sm font-medium text-gray-700 mb-2">
                                ID Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="data_subject_id_number" name="data_subject_id_number" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="data_subject_id_expiry_date" class="block text-sm font-medium text-gray-700 mb-2">
                                ID Expiry Date
                            </label>
                            <input type="date" id="data_subject_id_expiry_date" name="data_subject_id_expiry_date"
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="data_subject_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea id="data_subject_address" name="data_subject_address" rows="3" required
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="flex justify-end mt-8">
                        <button type="button" @click="currentStep = 2" 
                                class="btn-primary text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                            Next Step <i class='bx bx-right-arrow-alt ml-2'></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 2: Request Details -->
                <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Request Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="request_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Request Type <span class="text-red-500">*</span>
                            </label>
                            <select id="request_type" name="request_type" required
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Request Type</option>
                                <option value="data_access">Data Access</option>
                                <option value="data_portability">Data Portability</option>
                                <option value="data_rectification">Data Rectification</option>
                                <option value="data_erasure">Data Erasure</option>
                                <option value="data_restriction">Data Restriction</option>
                                <option value="objection">Objection to Processing</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                                Priority <span class="text-red-500">*</span>
                            </label>
                            <select id="priority" name="priority" required
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="organization_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Organization Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="organization_name" name="organization_name" required
                               class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label for="organization_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Organization Type <span class="text-red-500">*</span>
                            </label>
                            <select id="organization_type" name="organization_type" required
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Organization Type</option>
                                <option value="individual">Individual</option>
                                <option value="company">Company</option>
                                <option value="ngo">NGO</option>
                                <option value="government">Government</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="organization_contact_person" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Person <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="organization_contact_person" name="organization_contact_person" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label for="organization_contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="organization_contact_email" name="organization_contact_email" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="organization_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Phone <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="organization_contact_phone" name="organization_contact_phone" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="organization_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Organization Address <span class="text-red-500">*</span>
                        </label>
                        <textarea id="organization_address" name="organization_address" rows="3" required
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="mt-6">
                        <label for="requested_data_categories" class="block text-sm font-medium text-gray-700 mb-2">
                            Requested Data Categories <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="requested_data_categories[]" value="personal_info" class="mr-2">
                                <span class="text-sm">Personal Information</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="requested_data_categories[]" value="contact_info" class="mr-2">
                                <span class="text-sm">Contact Information</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="requested_data_categories[]" value="financial_data" class="mr-2">
                                <span class="text-sm">Financial Data</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="requested_data_categories[]" value="transaction_history" class="mr-2">
                                <span class="text-sm">Transaction History</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="requested_data_categories[]" value="communication_records" class="mr-2">
                                <span class="text-sm">Communication Records</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="requested_data_categories[]" value="other" class="mr-2">
                                <span class="text-sm">Other</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="requested_data_timeframe" class="block text-sm font-medium text-gray-700 mb-2">
                            Requested Data Timeframe <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="requested_data_timeframe" name="requested_data_timeframe" required
                               placeholder="e.g., Last 2 years, All data, Specific date range"
                               class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                
                <!-- Step 3: Legal Basis -->
                <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Legal Basis & Purpose</h3>
                    
                    <div class="mt-6">
                        <label for="request_purpose" class="block text-sm font-medium text-gray-700 mb-2">
                            Purpose of Request <span class="text-red-500">*</span>
                        </label>
                        <textarea id="request_purpose" name="request_purpose" rows="4" required
                                  placeholder="Please describe the purpose of your data access request..."
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="mt-6">
                        <label for="request_justification" class="block text-sm font-medium text-gray-700 mb-2">
                            Justification <span class="text-red-500">*</span>
                        </label>
                        <textarea id="request_justification" name="request_justification" rows="4" required
                                  placeholder="Please provide justification for your request..."
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="mt-6">
                        <label for="data_usage_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Data Usage Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="data_usage_description" name="data_usage_description" rows="4" required
                                  placeholder="Please describe how you intend to use the data..."
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label for="data_retention_period" class="block text-sm font-medium text-gray-700 mb-2">
                                Data Retention Period <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="data_retention_period" name="data_retention_period" required
                                   placeholder="e.g., 1 year, 2 years, Indefinite"
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="legal_basis" class="block text-sm font-medium text-gray-700 mb-2">
                                Legal Basis <span class="text-red-500">*</span>
                            </label>
                            <select id="legal_basis" name="legal_basis" required
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Legal Basis</option>
                                <option value="consent">Consent</option>
                                <option value="contract">Contract</option>
                                <option value="legal_obligation">Legal Obligation</option>
                                <option value="vital_interests">Vital Interests</option>
                                <option value="public_task">Public Task</option>
                                <option value="legitimate_interests">Legitimate Interests</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="legal_basis_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Legal Basis Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="legal_basis_description" name="legal_basis_description" rows="3" required
                                  placeholder="Please provide details about the legal basis for your request..."
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="mt-6">
                        <label for="consent_obtained" class="block text-sm font-medium text-gray-700 mb-2">
                            Consent Obtained <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="consent_obtained" value="1" class="mr-2">
                                <span>Yes, consent has been obtained</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="consent_obtained" value="0" class="mr-2">
                                <span>No, consent has not been obtained</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="consent_method" class="block text-sm font-medium text-gray-700 mb-2">
                            Consent Method
                        </label>
                        <input type="text" id="consent_method" name="consent_method"
                               placeholder="e.g., Written consent, Digital signature, Verbal consent"
                               class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div class="mt-6">
                        <label for="consent_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Consent Date
                        </label>
                        <input type="date" id="consent_date" name="consent_date"
                               class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                            <div><strong>Name:</strong> <span id="review_data_subject_name">-</span></div>
                            <div><strong>Email:</strong> <span id="review_data_subject_email">-</span></div>
                            <div><strong>Phone:</strong> <span id="review_data_subject_phone">-</span></div>
                            <div><strong>ID Type:</strong> <span id="review_data_subject_id_type">-</span></div>
                            <div><strong>ID Number:</strong> <span id="review_data_subject_id_number">-</span></div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Request Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div><strong>Request Type:</strong> <span id="review_request_type">-</span></div>
                            <div><strong>Priority:</strong> <span id="review_priority">-</span></div>
                            <div><strong>Organization:</strong> <span id="review_organization_name">-</span></div>
                            <div><strong>Contact Person:</strong> <span id="review_organization_contact_person">-</span></div>
                            <div><strong>Data Categories:</strong> <span id="review_requested_data_categories">-</span></div>
                            <div><strong>Timeframe:</strong> <span id="review_requested_data_timeframe">-</span></div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Legal Basis</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div><strong>Legal Basis:</strong> <span id="review_legal_basis">-</span></div>
                            <div><strong>Consent Obtained:</strong> <span id="review_consent_obtained">-</span></div>
                            <div><strong>Consent Method:</strong> <span id="review_consent_method">-</span></div>
                            <div><strong>Consent Date:</strong> <span id="review_consent_date">-</span></div>
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
                        <button type="submit" onclick="submitForm('dar-form', 'Data access request submitted successfully!')" 
                                class="btn-primary text-white px-8 py-3 rounded-lg font-semibold">
                            <i class='bx bx-check mr-2'></i>
                            Submit Request
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
                Data Protection Information
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Your data protection rights and our commitment to privacy.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-shield-check text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">GDPR Compliant</h3>
                <p class="text-gray-600">
                    We comply with the General Data Protection Regulation (GDPR) and other data protection laws.
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-time text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Response Time</h3>
                <p class="text-gray-600">
                    We will respond to your request within 30 days as required by data protection regulations.
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-support text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Support</h3>
                <p class="text-gray-600">
                    Need help? Contact our data protection officer at dpo@bmmb.com or call +1 (555) 123-4567.
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
        document.getElementById('review_data_subject_name').textContent = document.getElementById('data_subject_name').value || '-';
        document.getElementById('review_data_subject_email').textContent = document.getElementById('data_subject_email').value || '-';
        document.getElementById('review_data_subject_phone').textContent = document.getElementById('data_subject_phone').value || '-';
        document.getElementById('review_data_subject_id_type').textContent = document.getElementById('data_subject_id_type').value || '-';
        document.getElementById('review_data_subject_id_number').textContent = document.getElementById('data_subject_id_number').value || '-';
        
        document.getElementById('review_request_type').textContent = document.getElementById('request_type').value || '-';
        document.getElementById('review_priority').textContent = document.getElementById('priority').value || '-';
        document.getElementById('review_organization_name').textContent = document.getElementById('organization_name').value || '-';
        document.getElementById('review_organization_contact_person').textContent = document.getElementById('organization_contact_person').value || '-';
        document.getElementById('review_requested_data_timeframe').textContent = document.getElementById('requested_data_timeframe').value || '-';
        
        // Handle checkboxes
        const categories = Array.from(document.querySelectorAll('input[name="requested_data_categories[]"]:checked'))
            .map(cb => cb.value).join(', ');
        document.getElementById('review_requested_data_categories').textContent = categories || '-';
        
        document.getElementById('review_legal_basis').textContent = document.getElementById('legal_basis').value || '-';
        document.getElementById('review_consent_method').textContent = document.getElementById('consent_method').value || '-';
        document.getElementById('review_consent_date').textContent = document.getElementById('consent_date').value || '-';
        
        // Handle radio buttons
        const consentObtained = document.querySelector('input[name="consent_obtained"]:checked');
        document.getElementById('review_consent_obtained').textContent = consentObtained ? (consentObtained.value === '1' ? 'Yes' : 'No') : '-';
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
