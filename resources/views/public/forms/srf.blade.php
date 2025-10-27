@extends('layouts.public')

@section('title', 'Service Request Form - BMMB Digital Forms')

@section('content')
<!-- Hero Section -->
<section class="form-section py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Service Request Form
            </h1>
            <p class="text-lg text-white/90 mb-6 max-w-2xl mx-auto">
                Request various services and support from our organization.
            </p>
            <div class="flex items-center justify-center space-x-4 text-white/80">
                <div class="flex items-center">
                    <i class='bx bx-time mr-2'></i>
                    <span>Processing: 2-3 business days</span>
                </div>
                <div class="flex items-center">
                    <i class='bx bx-shield-check mr-2'></i>
                    <span>24/7 Support</span>
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
                            <i class='bx bx-cog text-lg' x-show="currentStep > 2" x-cloak></i>
                            <span x-show="currentStep <= 2" x-cloak>2</span>
                        </div>
                        <div class="ml-3">
                            <span class="text-sm font-semibold block"
                                  :class="currentStep >= 2 ? 'text-blue-600' : 'text-gray-500'">Service Details</span>
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
                            <i class='bx bx-check-square text-lg' x-show="currentStep > 3" x-cloak></i>
                            <span x-show="currentStep <= 3" x-cloak>3</span>
                        </div>
                        <div class="ml-3">
                            <span class="text-sm font-semibold block"
                                  :class="currentStep >= 3 ? 'text-blue-600' : 'text-gray-500'">Requirements</span>
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
            <form id="srf-form" class="space-y-8">
                @csrf
                
                <!-- Step 1: Personal Information -->
                <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Personal Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="customer_name" name="customer_name" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="customer_email" name="customer_email" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input type="tel" id="customer_phone" name="customer_phone"
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="customer_city" class="block text-sm font-medium text-gray-700 mb-2">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="customer_city" name="customer_city" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="customer_state" class="block text-sm font-medium text-gray-700 mb-2">
                                State/Province <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="customer_state" name="customer_state" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="customer_postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Postal Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="customer_postal_code" name="customer_postal_code" required
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="customer_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <textarea id="customer_address" name="customer_address" rows="3" required
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="mt-6">
                        <label for="customer_country" class="block text-sm font-medium text-gray-700 mb-2">
                            Country <span class="text-red-500">*</span>
                        </label>
                        <select id="customer_country" name="customer_country" required
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
                
                <!-- Step 2: Service Details -->
                <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Service Details</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Service Type <span class="text-red-500">*</span>
                            </label>
                            <select id="service_type" name="service_type" required
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Service Type</option>
                                <option value="technical_support">Technical Support</option>
                                <option value="account_management">Account Management</option>
                                <option value="billing_support">Billing Support</option>
                                <option value="data_services">Data Services</option>
                                <option value="consultation">Consultation</option>
                                <option value="training">Training</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="other">Other</option>
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
                            Organization Name
                        </label>
                        <input type="text" id="organization_name" name="organization_name"
                               class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label for="organization_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Organization Type
                            </label>
                            <select id="organization_type" name="organization_type"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Organization Type</option>
                                <option value="individual">Individual</option>
                                <option value="small_business">Small Business</option>
                                <option value="medium_business">Medium Business</option>
                                <option value="large_corporation">Large Corporation</option>
                                <option value="ngo">NGO</option>
                                <option value="government">Government</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="organization_contact_person" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Person
                            </label>
                            <input type="text" id="organization_contact_person" name="organization_contact_person"
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label for="organization_contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Email
                            </label>
                            <input type="email" id="organization_contact_email" name="organization_contact_email"
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="organization_contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Phone
                            </label>
                            <input type="tel" id="organization_contact_phone" name="organization_contact_phone"
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="organization_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Organization Address
                        </label>
                        <textarea id="organization_address" name="organization_address" rows="3"
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="flex justify-between mt-8">
                        <button type="button" @click="currentStep = 1" 
                                class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-400 transition-colors">
                            <i class='bx bx-left-arrow-alt mr-2'></i>
                            Previous
                        </button>
                        <button type="button" @click="currentStep = 3" 
                                class="btn-primary text-white px-8 py-3 rounded-lg font-semibold">
                            Next Step <i class='bx bx-right-arrow-alt ml-2'></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 3: Requirements -->
                <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Service Requirements</h3>
                    
                    <div class="mt-6">
                        <label for="service_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Service Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="service_description" name="service_description" rows="4" required
                                  placeholder="Please describe the service you need..."
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="mt-6">
                        <label for="service_requirements" class="block text-sm font-medium text-gray-700 mb-2">
                            Service Requirements <span class="text-red-500">*</span>
                        </label>
                        <textarea id="service_requirements" name="service_requirements" rows="4" required
                                  placeholder="Please specify your requirements..."
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="mt-6">
                        <label for="service_scope" class="block text-sm font-medium text-gray-700 mb-2">
                            Service Scope <span class="text-red-500">*</span>
                        </label>
                        <textarea id="service_scope" name="service_scope" rows="4" required
                                  placeholder="Please describe the scope of the service..."
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label for="expected_delivery_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Expected Delivery Date
                            </label>
                            <input type="date" id="expected_delivery_date" name="expected_delivery_date"
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="budget_range" class="block text-sm font-medium text-gray-700 mb-2">
                                Budget Range
                            </label>
                            <select id="budget_range" name="budget_range"
                                    class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Select Budget Range</option>
                                <option value="under_1000">Under $1,000</option>
                                <option value="1000_5000">$1,000 - $5,000</option>
                                <option value="5000_10000">$5,000 - $10,000</option>
                                <option value="10000_25000">$10,000 - $25,000</option>
                                <option value="25000_50000">$25,000 - $50,000</option>
                                <option value="over_50000">Over $50,000</option>
                                <option value="to_be_discussed">To be discussed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label for="special_instructions" class="block text-sm font-medium text-gray-700 mb-2">
                            Special Instructions
                        </label>
                        <textarea id="special_instructions" name="special_instructions" rows="3"
                                  placeholder="Any special instructions or additional information..."
                                  class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="mt-6">
                        <label for="supporting_documents" class="block text-sm font-medium text-gray-700 mb-2">
                            Supporting Documents
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="supporting_documents[]" value="project_specification" class="mr-2">
                                <span class="text-sm">Project Specification</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="supporting_documents[]" value="technical_documentation" class="mr-2">
                                <span class="text-sm">Technical Documentation</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="supporting_documents[]" value="business_requirements" class="mr-2">
                                <span class="text-sm">Business Requirements</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="supporting_documents[]" value="other" class="mr-2">
                                <span class="text-sm">Other</span>
                            </label>
                        </div>
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
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
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
                        
                        <div>
                            <label for="legal_basis_description" class="block text-sm font-medium text-gray-700 mb-2">
                                Legal Basis Description <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="legal_basis_description" name="legal_basis_description" required
                                   placeholder="Brief description of legal basis"
                                   class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div class="flex justify-between mt-8">
                        <button type="button" @click="currentStep = 2" 
                                class="bg-gray-300 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-400 transition-colors">
                            <i class='bx bx-left-arrow-alt mr-2'></i>
                            Previous
                        </button>
                        <button type="button" @click="currentStep = 4" 
                                class="btn-primary text-white px-8 py-3 rounded-lg font-semibold">
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
                            <div><strong>Name:</strong> <span id="review_customer_name">-</span></div>
                            <div><strong>Email:</strong> <span id="review_customer_email">-</span></div>
                            <div><strong>Phone:</strong> <span id="review_customer_phone">-</span></div>
                            <div><strong>City:</strong> <span id="review_customer_city">-</span></div>
                            <div><strong>State:</strong> <span id="review_customer_state">-</span></div>
                            <div><strong>Country:</strong> <span id="review_customer_country">-</span></div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Service Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div><strong>Service Type:</strong> <span id="review_service_type">-</span></div>
                            <div><strong>Priority:</strong> <span id="review_priority">-</span></div>
                            <div><strong>Organization:</strong> <span id="review_organization_name">-</span></div>
                            <div><strong>Contact Person:</strong> <span id="review_organization_contact_person">-</span></div>
                            <div><strong>Expected Delivery:</strong> <span id="review_expected_delivery_date">-</span></div>
                            <div><strong>Budget Range:</strong> <span id="review_budget_range">-</span></div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-lg p-6 mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Service Requirements</h4>
                        <div class="space-y-2 text-sm">
                            <div><strong>Description:</strong> <span id="review_service_description">-</span></div>
                            <div><strong>Requirements:</strong> <span id="review_service_requirements">-</span></div>
                            <div><strong>Scope:</strong> <span id="review_service_scope">-</span></div>
                            <div><strong>Special Instructions:</strong> <span id="review_special_instructions">-</span></div>
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
                        <button type="submit" onclick="submitForm('srf-form', 'Service request submitted successfully!')" 
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
                Service Information
            </h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Our commitment to providing excellent service and support.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-support text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">24/7 Support</h3>
                <p class="text-gray-600">
                    Our support team is available around the clock to assist you with your service needs.
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-time text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Fast Response</h3>
                <p class="text-gray-600">
                    We typically respond to service requests within 2-3 business days.
                </p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-shield-check text-white text-2xl'></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Quality Assurance</h3>
                <p class="text-gray-600">
                    We maintain high standards and quality assurance in all our services.
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
        document.getElementById('review_customer_name').textContent = document.getElementById('customer_name').value || '-';
        document.getElementById('review_customer_email').textContent = document.getElementById('customer_email').value || '-';
        document.getElementById('review_customer_phone').textContent = document.getElementById('customer_phone').value || '-';
        document.getElementById('review_customer_city').textContent = document.getElementById('customer_city').value || '-';
        document.getElementById('review_customer_state').textContent = document.getElementById('customer_state').value || '-';
        document.getElementById('review_customer_country').textContent = document.getElementById('customer_country').value || '-';
        
        document.getElementById('review_service_type').textContent = document.getElementById('service_type').value || '-';
        document.getElementById('review_priority').textContent = document.getElementById('priority').value || '-';
        document.getElementById('review_organization_name').textContent = document.getElementById('organization_name').value || '-';
        document.getElementById('review_organization_contact_person').textContent = document.getElementById('organization_contact_person').value || '-';
        document.getElementById('review_expected_delivery_date').textContent = document.getElementById('expected_delivery_date').value || '-';
        document.getElementById('review_budget_range').textContent = document.getElementById('budget_range').value || '-';
        
        document.getElementById('review_service_description').textContent = document.getElementById('service_description').value || '-';
        document.getElementById('review_service_requirements').textContent = document.getElementById('service_requirements').value || '-';
        document.getElementById('review_service_scope').textContent = document.getElementById('service_scope').value || '-';
        document.getElementById('review_special_instructions').textContent = document.getElementById('special_instructions').value || '-';
        
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
