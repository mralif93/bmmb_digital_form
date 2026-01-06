@extends('layouts.public')

@section('title', ($form->name ?? ($type == 'raf' ? 'Remittance Application Form' : ($type == 'dar' ? 'Data Access Request Form' : ($type == 'dcr' ? 'Data Correction Request Form' : 'Service Request Form')))) . ' - BMMB Digital Forms')

@section('content')
    <!-- Hero Section -->
    <section class="form-section py-12">
        <div class="w-full max-w-none px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    {{ $form->name ?? ($type == 'raf' ? 'Remittance Application Form' : ($type == 'dar' ? 'Data Access Request Form' : ($type == 'dcr' ? 'Data Correction Request Form' : 'Service Request Form'))) }}
                </h1>
                <p class="text-lg text-white/90 mb-6 max-w-2xl mx-auto">
                    {{ $form->description ?? ($type == 'raf' ? 'Submit your remittance application for international money transfers and financial transactions.' : ($type == 'dar' ? 'Request access to your personal data and information in compliance with data protection regulations.' : ($type == 'dcr' ? 'Request correction of your personal data in compliance with data protection regulations.' : 'Submit your service request for banking and financial services.'))) }}
                </p>
            </div>
        </div>
    </section>

    <!-- Form Section -->
    <section class="py-12" x-data="formWizard" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(isset($error))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 px-6 py-4 mb-6 rounded-lg">
                    <div class="flex items-center">
                        <i class='bx bx-error-circle text-xl mr-2'></i>
                        <span class="font-medium">{{ $error }}</span>
                    </div>
                </div>
            @endif

            <!-- White Card Container -->
            <div
                class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-800 overflow-hidden form-card">
                <!-- Stepper Progress Indicator -->
                @if(count($sections ?? []) > 0)
                    <div
                        class="w-full pt-6 pb-6 border-b border-gray-200 dark:border-gray-800 bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-900">
                        @php
                            // Helper function to shorten step labels
                            $shortLabels = [
                                'Important Notes' => 'Start',
                                'About Yourself' => 'About You',
                                'Customer Information' => 'Customer',
                                'Account Type' => 'Account',
                                'Consent' => 'Consent',
                                'Third Party Requestor' => 'Third Party',
                                'Particulars of the Data Subject (Account Holder)' => 'Account',
                                'Particulars of the Third Party Requestor' => 'Third Party',
                                'Personal Data Correction' => 'Data Correction',
                                'Personal Data Requested' => 'Data Request',
                                'Method of Delivery' => 'Delivery',
                                'Declaration' => 'Declaration',
                            ];

                            function getShortLabel($label, $shortLabels)
                            {
                                return $shortLabels[$label] ?? (strlen($label) > 15 ? substr($label, 0, 15) . '...' : $label);
                            }
                        @endphp

                        <!-- Progress Percentage (Top) -->
                        <div class="text-center mb-4 px-4">
                            <span class="text-sm font-bold text-primary-600 dark:text-primary-400"
                                x-text="Math.min(100, Math.round(((currentStep - 1) / (totalSteps - 1)) * 100)) + '% Complete'"></span>
                        </div>

                        <!-- Stepper (Desktop) -->
                        <div class="hidden md:flex items-center justify-between relative px-4 sm:px-6 lg:px-8">
                            @foreach($sections as $index => $section)
                                @php
                                    $stepNumber = $section['step'] ?? ($index + 1);
                                    $shortLabel = getShortLabel($section['label'], $shortLabels);
                                @endphp
                                <div class="flex items-center relative z-10 flex-1"
                                    :class="{ 'cursor-pointer hover:opacity-80': currentStep > {{ $stepNumber }} }"
                                    @click="if (currentStep > {{ $stepNumber }}) currentStep = {{ $stepNumber }}">
                                    <div class="flex flex-col items-center">
                                        <!-- Step Circle -->
                                        <div class="rounded-full flex items-center justify-center font-bold transition-all duration-500 ease-out"
                                            :class="{
                                                                                                                             'w-12 h-12 text-base bg-gradient-to-br from-primary-500 to-primary-600 text-white ring-4 ring-primary-200 shadow-2xl shadow-primary-500/50 scale-110': currentStep === {{ $stepNumber }},
                                                                                                                             'w-9 h-9 text-xs bg-primary-600 text-white ring-2 ring-primary-300/50 shadow-lg': currentStep > {{ $stepNumber }},
                                                                                                                             'w-9 h-9 text-xs bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 shadow-md': currentStep < {{ $stepNumber }}
                                                                                                                         }"
                                            :style="currentStep === {{ $stepNumber }} ? 'box-shadow: 0 10px 40px -10px rgba(254, 128, 0, 0.6), 0 0 0 4px rgba(254, 128, 0, 0.1)' : ''">
                                            <i class='bx bx-check font-bold'
                                                :class="currentStep > {{ $stepNumber }} ? 'text-base' : ''"
                                                x-show="currentStep > {{ $stepNumber }}"></i>
                                            <span x-show="currentStep <= {{ $stepNumber }}">{{ $stepNumber }}</span>
                                        </div>
                                        <!-- Step Label -->
                                        <div class="mt-3 text-center">
                                            <span class="text-xs font-semibold block transition-all duration-300" :class="{
                                                                                                                                  'text-primary-700 dark:text-primary-400 scale-105': currentStep === {{ $stepNumber }},
                                                                                                                                  'text-primary-600 dark:text-primary-500': currentStep > {{ $stepNumber }},
                                                                                                                                  'text-gray-500 dark:text-gray-500': currentStep < {{ $stepNumber }}
                                                                                                                              }">
                                                {{ $shortLabel }}
                                            </span>
                                            <span
                                                class="text-xs text-green-600 dark:text-green-400 font-medium mt-1 flex items-center justify-center"
                                                x-show="currentStep > {{ $stepNumber }}">
                                                <i class='bx bx-check-circle text-sm mr-0.5'></i> Done
                                            </span>
                                        </div>
                                    </div>
                                    <!-- Connector Line -->
                                    @if($index < count($sections) - 1 || true)
                                        <div class="flex-1 h-1 mx-3 mb-6 transition-all duration-500 rounded-full relative overflow-hidden"
                                            :class="{
                                                                                                                                                     'bg-gray-200 dark:bg-gray-700': currentStep <= {{ $stepNumber }}
                                                                                                                                                 }">
                                            <div class="h-full bg-gradient-to-r from-primary-500 to-primary-600 rounded-full transition-all duration-500"
                                                :style="currentStep > {{ $stepNumber }} ? 'width: 100%' : 'width: 0%'"></div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            <!-- Preview Step -->
                            <div class="flex items-center relative z-10">
                                <div class="flex flex-col items-center">
                                    <!-- Step Circle -->
                                    <div class="rounded-full flex items-center justify-center font-bold transition-all duration-500 ease-out"
                                        :class="{
                                                                                             'w-12 h-12 text-base bg-gradient-to-br from-primary-500 to-primary-600 text-white ring-4 ring-primary-200 shadow-2xl shadow-primary-500/50 scale-110': currentStep === {{ count($sections) + 1 }},
                                                                                             'w-9 h-9 text-xs bg-primary-600 text-white ring-2 ring-primary-300/50 shadow-lg': currentStep > {{ count($sections) + 1 }},
                                                                                             'w-9 h-9 text-xs bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 shadow-md': currentStep < {{ count($sections) + 1 }}
                                                                                         }"
                                        :style="currentStep === {{ count($sections) + 1 }} ? 'box-shadow: 0 10px 40px -10px rgba(254, 128, 0, 0.6), 0 0 0 4px rgba(254, 128, 0, 0.1)' : ''">
                                        <i class='bx bx-check font-bold'
                                            :class="currentStep > {{ count($sections) + 1 }} ? 'text-base' : ''"
                                            x-show="currentStep > {{ count($sections) + 1 }}"></i>
                                        <i class='bx bx-show font-bold'
                                            :class="currentStep === {{ count($sections) + 1 }} ? 'text-base' : 'text-sm'"
                                            x-show="currentStep === {{ count($sections) + 1 }}"></i>
                                        <span
                                            x-show="currentStep < {{ count($sections) + 1 }}">{{ count($sections) + 1 }}</span>
                                    </div>
                                    <!-- Step Label -->
                                    <div class="mt-3 text-center">
                                        <span class="text-xs font-semibold block transition-all duration-300" :class="{
                                                                                                  'text-primary-700 dark:text-primary-400 scale-105': currentStep === {{ count($sections) + 1 }},
                                                                                                  'text-primary-600 dark:text-primary-500': currentStep > {{ count($sections) + 1 }},
                                                                                                  'text-gray-500 dark:text-gray-500': currentStep < {{ count($sections) + 1 }}
                                                                                              }">
                                            Review
                                        </span>
                                        <span
                                            class="text-xs text-green-600 dark:text-green-400 font-medium mt-1 flex items-center justify-center"
                                            x-show="currentStep > {{ count($sections) + 1 }}">
                                            <i class='bx bx-check-circle text-sm mr-0.5'></i> Done
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stepper (Mobile) -->
                        <div class="md:hidden px-4">
                            <!-- Progress Bar (Mobile) -->
                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-400"
                                        x-text="'Step ' + currentStep + ' of ' + totalSteps"></span>
                                    <span class="text-xs font-bold text-primary-600 dark:text-primary-400"
                                        x-text="Math.min(100, Math.round(((currentStep - 1) / (totalSteps - 1)) * 100)) + '%'"></span>
                                </div>
                                <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-primary-500 to-primary-600 rounded-full transition-all duration-500"
                                        :style="'width: ' + Math.round(((currentStep - 1) / (totalSteps - 1)) * 100) + '%'">
                                    </div>
                                </div>
                            </div>

                            <!-- Step Dots -->
                            <div class="flex items-center justify-between relative">
                                @foreach($sections as $index => $section)
                                    @php
                                        $stepNumber = $section['step'] ?? ($index + 1);
                                    @endphp
                                    <div class="flex items-center relative z-10 flex-1"
                                        :class="{ 'cursor-pointer': currentStep > {{ $stepNumber }} }"
                                        @click="if (currentStep > {{ $stepNumber }}) currentStep = {{ $stepNumber }}">
                                        <!-- Step Circle -->
                                        <div class="rounded-full flex items-center justify-center text-xs font-bold transition-all duration-500"
                                            :class="{
                                                                                                                             'w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-600 text-white ring-3 ring-primary-200 shadow-xl scale-110': currentStep === {{ $stepNumber }},
                                                                                                                             'w-7 h-7 bg-primary-600 text-white ring-1 ring-primary-300 shadow-md': currentStep > {{ $stepNumber }},
                                                                                                                             'w-7 h-7 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 shadow-sm': currentStep < {{ $stepNumber }}
                                                                                                                         }">
                                            <i class='bx bx-check text-xs font-bold' x-show="currentStep > {{ $stepNumber }}"></i>
                                            <span x-show="currentStep <= {{ $stepNumber }}">{{ $stepNumber }}</span>
                                        </div>
                                        <!-- Connector Line -->
                                        @if($index < count($sections))
                                            <div
                                                class="flex-1 h-0.5 mx-1 transition-all duration-300 rounded-full bg-gray-200 dark:bg-gray-700 overflow-hidden">
                                                <div class="h-full bg-gradient-to-r from-primary-500 to-primary-600 transition-all duration-500"
                                                    :style="currentStep > {{ $stepNumber }} ? 'width: 100%' : 'width: 0%'"></div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                <!-- Preview Step -->
                                <div class="flex items-center relative z-10">
                                    <div class="rounded-full flex items-center justify-center text-xs font-bold transition-all duration-500"
                                        :class="{
                                                                                             'w-9 h-9 bg-gradient-to-br from-primary-500 to-primary-600 text-white ring-3 ring-primary-200 shadow-xl scale-110': currentStep === {{ count($sections) + 1 }},
                                                                                             'w-7 h-7 bg-primary-600 text-white ring-1 ring-primary-300 shadow-md': currentStep > {{ count($sections) + 1 }},
                                                                                             'w-7 h-7 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 shadow-sm': currentStep < {{ count($sections) + 1 }}
                                                                                         }">
                                        <i class='bx bx-check text-xs font-bold'
                                            x-show="currentStep > {{ count($sections) + 1 }}"></i>
                                        <i class='bx bx-show text-xs font-bold'
                                            x-show="currentStep === {{ count($sections) + 1 }}"></i>
                                        <span
                                            x-show="currentStep < {{ count($sections) + 1 }}">{{ count($sections) + 1 }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Step Label (Mobile) -->
                            <div class="mt-4 text-center">
                                <span class="text-sm font-bold text-primary-700 dark:text-primary-400"
                                    x-text="getCurrentStepLabel()"></span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Dynamic Form -->
                <form id="{{ $type }}-form" class="p-4 sm:p-6 lg:p-8 bg-white dark:bg-gray-900">
                    @csrf

                    @if(session('submission_branch_id'))
                        <input type="hidden" name="branch_id" value="{{ session('submission_branch_id') }}">
                    @endif

                    {!! $formHtml !!}

                    <!-- Preview Step -->
                    <div x-show="currentStep === totalSteps" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        x-init="$watch('currentStep', (value) => { if (value === totalSteps) { collectFormData(); } })"
                        :key="currentStep">
                        <!-- Preview Header -->
                        <div class="bg-primary-500 px-4 py-3 mb-4 rounded-lg">
                            <h3 class="text-lg font-bold text-white tracking-wide flex items-center">
                                <i class="bx bx-show mr-2 text-lg text-white"></i>
                                Review Your Information
                            </h3>
                        </div>

                        <!-- Preview Content -->
                        <div class="space-y-6">
                            @foreach($sections as $section)
                                <div class="overflow-hidden">
                                    <div class="bg-gray-50 dark:bg-gray-800/70 px-4 py-3">
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ $section['label'] }}
                                        </h4>
                                    </div>
                                    <div class="p-4 space-y-4 bg-white dark:bg-gray-900">
                                        @php
                                            // Use unified FormField model with form_id (new system)
                                            $fields = \App\Models\FormField::where('form_id', $form->id)
                                                ->whereHas('section', function ($q) use ($section) {
                                                    $q->where('section_key', $section['name']);
                                                })
                                                ->where('is_active', true)
                                                ->ordered()
                                                ->get();
                                        @endphp
                                        @foreach($fields as $field)
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-3 gap-4 py-2 border-b border-gray-100 dark:border-gray-800 last:border-b-0">
                                                <div class="md:col-span-1">
                                                    <span
                                                        class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $field->field_label }}@if($field->is_required)
                                                        <span class="text-red-500">*</span>@endif:</span>
                                                </div>
                                                <div class="md:col-span-2">
                                                    @if($field->field_type === 'signature')
                                                        <div x-data="{ signatureVal: '' }"
                                                            x-effect="if(currentStep === totalSteps) signatureVal = getFieldValue('{{ $field->field_name }}')">
                                                            <template x-if="signatureVal && signatureVal.startsWith('data:image')">
                                                                <img :src="signatureVal" alt="Signature"
                                                                    class="max-h-24 max-w-full object-contain border border-gray-300 rounded p-1 bg-white">
                                                            </template>
                                                            <template x-if="!signatureVal || !signatureVal.startsWith('data:image')">
                                                                <span class="text-sm text-gray-500 italic">Not signed</span>
                                                            </template>
                                                        </div>
                                                    @else
                                                        <span class="text-sm text-gray-900 dark:text-gray-100"
                                                            x-text="currentStep === totalSteps ? getFieldValue('{{ $field->field_name }}') : 'Not provided'"
                                                            x-init="$watch('currentStep', () => { if (currentStep === totalSteps) { $el.textContent = getFieldValue('{{ $field->field_name }}'); } })"></span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Terms Agreement (At bottom of preview) -->
                        <div class="flex items-start space-x-3 mt-10 pt-8 border-t-2 border-gray-200 dark:border-gray-800">
                            <input type="checkbox" id="terms_agreement" name="terms_agreement" required
                                class="mt-1 h-5 w-5 text-primary-600 focus:ring-2 focus:ring-primary-500 border-gray-300 rounded cursor-pointer transition-all flex-shrink-0">
                            <label for="terms_agreement"
                                class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed cursor-pointer flex-1">
                                I acknowledge that I have read and agree to the <a href="#"
                                    class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-semibold underline transition-colors">Terms
                                    and Conditions</a>
                                and <a href="#"
                                    class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-semibold underline transition-colors">Privacy
                                    Policy</a>
                                <span class="text-red-500 font-bold ml-1">*</span>
                            </label>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div
                        class="flex justify-between items-center mt-10 pt-8 border-t-2 border-gray-200 dark:border-gray-800">
                        <!-- Previous Button -->
                        <button type="button" @click="if (currentStep > 1) currentStep--"
                            x-show="currentStep > 1 && totalSteps > 1"
                            class="px-5 py-2 text-xs font-semibold text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-lg transition-all duration-300 flex items-center shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-300 dark:focus:ring-gray-700">
                            <i class='bx bx-chevron-left mr-1.5 text-sm'></i>
                            Previous
                        </button>
                        <div x-show="currentStep === 1"></div>

                        <!-- Next/Submit Button -->
                        <div class="ml-auto flex gap-3">
                            <button type="button" x-show="currentStep < totalSteps - 1 && totalSteps > 1"
                                @click="if (window.validateStep(currentStep)) { currentStep++; }"
                                class="btn-primary text-white px-5 py-2 rounded-lg font-semibold text-xs shadow-md hover:shadow-lg transition-all duration-300 flex items-center focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                Continue
                                <i class='bx bx-chevron-right ml-1.5 text-sm'></i>
                            </button>
                            <button type="button" x-show="currentStep === totalSteps - 1 && totalSteps > 1"
                                @click="if (window.validateStep(currentStep)) { collectFormData(); setTimeout(() => { currentStep++; }, 100); }"
                                class="btn-primary text-white px-5 py-2 rounded-lg font-semibold text-xs shadow-md hover:shadow-lg transition-all duration-300 flex items-center focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                Review & Continue
                                <i class='bx bx-show ml-1.5 text-sm'></i>
                            </button>
                            <button type="button" x-show="currentStep === totalSteps"
                                @click="submitForm('{{ $type }}-form', 'Form submitted successfully!')"
                                class="btn-primary text-white px-6 py-2 rounded-lg font-semibold text-xs shadow-md hover:shadow-lg transition-all duration-300 flex items-center focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                                <i class='bx bx-check-circle mr-1.5 text-sm'></i>
                                Submit Application
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- End White Card Container -->
        </div>
    </section>

    @push('scripts')
        <script>
            // Form wizard Alpine.js data - must be available globally before Alpine initializes
            document.addEventListener('alpine:init', () => {
                Alpine.data('formWizard', () => {
                    return {
                        currentStep: 1,
                        totalSteps: {{ count($sections) > 0 ? count($sections) + 1 : 2 }},
                        sections: @js($sections ?? []),
                        formData: {},
                        submissionToken: '',
                        submissionMessage: '',
                        init() {
                            const formId = '{{ $type }}-form';
                            const form = document.getElementById(formId);
                            if (form) {
                                form.addEventListener('input', () => this.collectFormData());
                                form.addEventListener('change', () => this.collectFormData());
                            }
                        },
                        collectFormData() {
                            const formId = '{{ $type }}-form';
                            const form = document.getElementById(formId);
                            if (!form) return;
                            const formDataObj = {};
                            const inputs = form.querySelectorAll('input, select, textarea');
                            inputs.forEach(input => {
                                if (input.type === 'checkbox') {
                                    if (input.checked) {
                                        if (input.name.endsWith('[]')) {
                                            const name = input.name.replace('[]', '');
                                            if (!formDataObj[name]) formDataObj[name] = [];
                                            formDataObj[name].push(input.value);
                                        } else {
                                            formDataObj[input.name] = input.value;
                                        }
                                    }
                                } else if (input.type === 'radio') {
                                    const checked = form.querySelector('[name=\"' + input.name + '\"]:checked');
                                    if (checked) formDataObj[input.name] = checked.value;
                                } else {
                                    if (input.value) formDataObj[input.name] = input.value;
                                }
                            });
                            this.formData = formDataObj;
                        },
                        getFieldValue(fieldName) {
                            return window.getFormFieldValue('{{ $type }}-form', fieldName);
                        },
                        getCurrentStepLabel() {
                            if (this.currentStep === this.totalSteps) {
                                return 'Review & Submit';
                            }
                            const section = this.sections.find(s => (s.step ?? (this.sections.indexOf(s) + 1)) === this.currentStep);
                            return section ? section.label : `Step ${this.currentStep}`;
                        }
                    };
                });
            });

            // Form field value getter function
            window.getFormFieldValue = function (formId, fieldName) {
                const form = document.getElementById(formId);
                if (!form) {
                    console.warn('Form not found:', formId);
                    return 'Not provided';
                }

                // Try exact name match first
                let input = form.querySelector('[name="' + fieldName + '"]');

                // If not found, try to find within all inputs (including hidden ones)
                if (!input) {
                    // Get all inputs in the form (even hidden)
                    const allInputs = form.querySelectorAll('input, select, textarea');
                    input = Array.from(allInputs).find(el => el.name === fieldName);
                }

                // If still not found, try with array notation
                if (!input) {
                    input = form.querySelector('[name="' + fieldName + '[]"]');
                }

                // If still not found, try partial match for radio buttons
                if (!input) {
                    const inputs = form.querySelectorAll('input[type="radio"][name*="' + fieldName + '"]');
                    if (inputs.length > 0) {
                        const checked = Array.from(inputs).find(i => i.checked);
                        if (checked) {
                            return checked.value || 'Not provided';
                        }
                        return 'Not provided';
                    }
                }

                // If still not found, try checkbox groups
                if (!input) {
                    const inputs = form.querySelectorAll('input[type="checkbox"][name*="' + fieldName + '"]');
                    if (inputs.length > 0) {
                        const checked = Array.from(inputs).filter(i => i.checked);
                        if (checked.length > 0) {
                            return checked.map(i => i.value).join(', ');
                        }
                        return 'Not provided';
                    }
                }

                if (!input) {
                    console.warn('Input not found for field:', fieldName, 'Form ID:', formId);
                    // Debug: log all available field names
                    const allFields = form.querySelectorAll('input, select, textarea');
                    const fieldNames = Array.from(allFields).map(el => el.name).filter(n => n);
                    console.log('Available field names:', fieldNames);
                    return 'Not provided';
                }

                // Handle different input types
                if (input.type === 'checkbox') {
                    return input.checked ? (input.value || 'Yes') : 'No';
                }

                if (input.type === 'radio') {
                    const checked = form.querySelector('[name="' + fieldName + '"]:checked');
                    return checked ? (checked.value || 'Not provided') : 'Not provided';
                }

                if (input.tagName === 'SELECT') {
                    const selected = input.options[input.selectedIndex];
                    if (selected && selected.value && selected.value !== '' && selected.value !== '-- Select') {
                        return selected.text || selected.value;
                    }
                    return 'Not provided';
                }

                if (input.tagName === 'TEXTAREA') {
                    const value = input.value ? input.value.trim() : '';
                    return value || 'Not provided';
                }

                // For text inputs and other types
                const value = input.value ? input.value.trim() : '';
                if (!value || value === '') return 'Not provided';
                return value;
            };

            // Step validation function (accessible globally)
            window.validateStep = function (stepNumber) {
                const currentStepElement = document.querySelector(`[data-step="${stepNumber}"]`);
                if (!currentStepElement) {
                    console.warn('Step element not found:', stepNumber);
                    return true;
                }

                // Clear previous validation errors
                currentStepElement.querySelectorAll('.border-red-500').forEach(field => {
                    field.classList.remove('border-red-500');
                });

                const requiredFields = currentStepElement.querySelectorAll('[required]');
                let isValid = true;
                let firstInvalidField = null;

                requiredFields.forEach(field => {
                    // Skip hidden fields (conditional fields that are not shown)
                    if (field.closest('.form-field') && field.closest('.form-field').style.display === 'none') {
                        return;
                    }

                    // Check if field is visible
                    const fieldVisible = field.offsetParent !== null;
                    if (!fieldVisible) return;

                    let fieldValue = '';
                    if (field.type === 'checkbox' || field.type === 'radio') {
                        const checkedField = currentStepElement.querySelector(`[name="${field.name}"]:checked`);
                        fieldValue = checkedField ? checkedField.value : '';
                    } else {
                        fieldValue = field.value ? field.value.trim() : '';
                    }

                    if (!fieldValue) {
                        field.classList.add('border-red-500');
                        isValid = false;
                        if (!firstInvalidField) {
                            firstInvalidField = field;
                        }
                    } else {
                        field.classList.remove('border-red-500');
                    }
                });

                if (!isValid && firstInvalidField) {
                    // Scroll to the step container first
                    currentStepElement.scrollIntoView({ behavior: 'smooth', block: 'start' });

                    // Then scroll to the invalid field
                    setTimeout(() => {
                        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalidField.focus();
                    }, 300);

                    // Show error message
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validation Error',
                        text: 'Please fill in all required fields before proceeding.',
                        confirmButtonColor: '#FE8000',
                        timer: 4000,
                        showConfirmButton: true
                    });
                }

                return isValid;
            };

            // Helper function to find fields by name (handles both with and without brackets)
            function findFieldsByName(fieldName) {
                // Try exact match first
                let fields = document.querySelectorAll(`[name="${fieldName}"]`);

                // If no exact match and field name doesn't end with [], try with brackets
                if (fields.length === 0 && !fieldName.endsWith('[]')) {
                    fields = document.querySelectorAll(`[name="${fieldName}[]"]`);
                }

                // If still no match and field name ends with [], try without brackets
                if (fields.length === 0 && fieldName.endsWith('[]')) {
                    const nameWithoutBrackets = fieldName.replace('[]', '');
                    fields = document.querySelectorAll(`[name="${nameWithoutBrackets}"]`);
                }

                return fields;
            }

            // Helper function to get value from a field (handles checkboxes, radio, select, and regular inputs)
            function getFieldValue(fieldName) {
                // Find all fields with this name (could be checkboxes, radios, select, or input)
                const fields = findFieldsByName(fieldName);

                if (fields.length === 0) {
                    return '';
                }

                const firstField = fields[0];

                // Select dropdown - return selected value
                if (firstField.tagName === 'SELECT') {
                    return firstField.value || '';
                }

                // Single checkbox (not an array)
                if (fields.length === 1 && firstField.type === 'checkbox') {
                    return firstField.checked ? (firstField.value || '1') : '';
                }

                // Multiple checkboxes (array) - check if specific value is checked
                if (fields.length > 1 && firstField.type === 'checkbox') {
                    // For checkbox arrays, we need to check if a specific value is checked
                    // Return array of checked values
                    const checkedValues = Array.from(fields)
                        .filter(cb => cb.checked)
                        .map(cb => cb.value);
                    return checkedValues;
                }

                // Radio buttons - find the checked radio button
                if (firstField.type === 'radio') {
                    const checked = Array.from(fields).find(rb => rb.checked);
                    return checked ? checked.value : '';
                }

                // Regular input (text, email, number, textarea, etc.)
                return firstField.value || '';
            }

            // Helper function to check if condition is met
            function checkConditionMet(fieldName, operator, expectedValue) {
                // Check if target field is a single checkbox
                const targetFields = findFieldsByName(fieldName);
                const isSingleCheckbox = targetFields.length === 1 && targetFields[0].type === 'checkbox';

                // For single checkbox with 'checked' or 'not_checked' operator, check directly
                if (isSingleCheckbox && (operator === 'checked' || operator === 'not_checked')) {
                    const isChecked = targetFields[0].checked;
                    return operator === 'checked' ? isChecked : !isChecked;
                }

                const actualValue = getFieldValue(fieldName);

                // Handle checkbox arrays - check if expected value is in the array
                if (Array.isArray(actualValue)) {
                    if (operator === 'equals') {
                        // Check if the expected value exists in the checked values array
                        return actualValue.includes(expectedValue);
                    } else if (operator === 'contains') {
                        return actualValue.some(val => val.toString().includes(expectedValue));
                    } else if (operator === 'not_equals') {
                        return !actualValue.includes(expectedValue);
                    }
                    return false;
                }

                // Handle single values (string)
                if (operator === 'equals') {
                    // Direct match
                    if (actualValue === expectedValue) {
                        return true;
                    }
                    // For single checkbox: "1" or "true" means checked, empty or "false" means unchecked
                    if (isSingleCheckbox) {
                        const isChecked = targetFields[0].checked;
                        // If expected value is "1", "true", or "checked", check if checkbox is checked
                        if ((expectedValue === '1' || expectedValue === 'true' || expectedValue === 'checked') && isChecked) {
                            return true;
                        }
                        // If expected value is "", "false", or "unchecked", check if checkbox is not checked
                        if ((expectedValue === '' || expectedValue === 'false' || expectedValue === 'unchecked') && !isChecked) {
                            return true;
                        }
                    }
                    return false;
                } else if (operator === 'contains') {
                    return actualValue && actualValue.toString().includes(expectedValue);
                } else if (operator === 'not_equals') {
                    return actualValue !== expectedValue;
                } else if (operator === 'checked') {
                    // Special operator for checkboxes - just check if checked
                    // For multiple checkboxes, check if any is checked
                    if (targetFields.length > 1) {
                        return Array.from(targetFields).some(field => field.checked);
                    }
                    return targetFields.length > 0 && targetFields[0].checked;
                } else if (operator === 'not_checked') {
                    // Special operator for checkboxes - check if not checked
                    // For multiple checkboxes, check if none are checked
                    if (targetFields.length > 1) {
                        return !Array.from(targetFields).some(field => field.checked);
                    }
                    return targetFields.length === 0 || !targetFields[0].checked;
                }

                return false;
            }

            // Evaluate multiple conditions with AND/OR logic
            function evaluateMultipleConditions(conditions, logic) {
                if (!conditions || !Array.isArray(conditions) || conditions.length === 0) {
                    return false;
                }

                const results = conditions.map(function (condition) {
                    return checkConditionMet(condition.field, condition.operator, condition.value || '');
                });

                if (logic === 'or') {
                    return results.some(function (result) { return result === true; });
                } else {
                    // Default to 'and'
                    return results.every(function (result) { return result === true; });
                }
            }

            // Get all unique field names from conditions array
            function getFieldNamesFromConditions(conditions) {
                if (!conditions || !Array.isArray(conditions)) {
                    return [];
                }
                const fieldNames = conditions.map(function (condition) {
                    return condition.field;
                });
                // Return unique field names
                return [...new Set(fieldNames)];
            }

            // Add event listeners to all fields involved in conditions
            function addConditionalEventListeners(fieldNames, callback) {
                fieldNames.forEach(function (fieldName) {
                    const targetFields = findFieldsByName(fieldName);
                    targetFields.forEach(function (targetField) {
                        targetField.addEventListener('change', callback);

                        // For checkboxes and radio buttons, also listen to click events for immediate feedback
                        if (targetField.type === 'checkbox' || targetField.type === 'radio') {
                            targetField.addEventListener('click', callback);
                        }
                        // For text inputs, also listen to input event for real-time updates
                        else if (targetField.tagName === 'INPUT' && targetField.type !== 'checkbox' && targetField.type !== 'radio') {
                            targetField.addEventListener('input', callback);
                        }
                    });
                });
            }

            // Handle conditional field show/hide
            document.addEventListener('DOMContentLoaded', function () {
                // Handle NEW format: multiple conditions (data-show-if-conditions)
                const conditionalFieldsNew = document.querySelectorAll('[data-show-if-conditions]');
                conditionalFieldsNew.forEach(function (field) {
                    const conditionsData = field.getAttribute('data-show-if-conditions');
                    if (!conditionsData) return;

                    try {
                        const parsed = JSON.parse(conditionsData);
                        const logic = parsed.logic || 'and';
                        const conditions = parsed.conditions || [];

                        if (conditions.length === 0) return;

                        // Initially hide conditional field
                        field.style.display = 'none';

                        // Function to check conditions
                        function checkConditions() {
                            const shouldShow = evaluateMultipleConditions(conditions, logic);
                            field.style.display = shouldShow ? 'block' : 'none';
                        }

                        // Get all field names involved in conditions
                        const fieldNames = getFieldNamesFromConditions(conditions);

                        // Add event listeners to all involved fields
                        addConditionalEventListeners(fieldNames, checkConditions);

                        // Initial check
                        checkConditions();
                    } catch (e) {
                        console.error('Error parsing conditional logic:', e);
                    }
                });

                // Handle NEW format: multiple conditions for hide (data-hide-if-conditions)
                const hideFieldsNew = document.querySelectorAll('[data-hide-if-conditions]');
                hideFieldsNew.forEach(function (field) {
                    const conditionsData = field.getAttribute('data-hide-if-conditions');
                    if (!conditionsData) return;

                    try {
                        const parsed = JSON.parse(conditionsData);
                        const logic = parsed.logic || 'and';
                        const conditions = parsed.conditions || [];

                        if (conditions.length === 0) return;

                        // Function to check conditions
                        function checkConditions() {
                            const shouldHide = evaluateMultipleConditions(conditions, logic);
                            field.style.display = shouldHide ? 'none' : 'block';
                        }

                        // Get all field names involved in conditions
                        const fieldNames = getFieldNamesFromConditions(conditions);

                        // Add event listeners to all involved fields
                        addConditionalEventListeners(fieldNames, checkConditions);

                        // Initial check
                        checkConditions();
                    } catch (e) {
                        console.error('Error parsing conditional logic:', e);
                    }
                });

                // Handle OLD format: single condition (data-show-if-field) - backward compatibility
                const conditionalFields = document.querySelectorAll('[data-show-if-field]');

                conditionalFields.forEach(function (field) {
                    // Skip if this field already has new format conditions
                    if (field.hasAttribute('data-show-if-conditions')) {
                        return;
                    }

                    const showIfField = field.getAttribute('data-show-if-field');
                    const showIfOperator = field.getAttribute('data-show-if-operator') || 'equals';
                    const showIfValue = field.getAttribute('data-show-if-value');

                    // Find all target fields (could be multiple checkboxes with same name)
                    const targetFields = findFieldsByName(showIfField);

                    if (targetFields.length > 0) {
                        // Initially hide conditional field
                        field.style.display = 'none';

                        // Function to check condition
                        function checkCondition() {
                            const shouldShow = checkConditionMet(showIfField, showIfOperator, showIfValue);

                            if (shouldShow) {
                                field.style.display = 'block';
                            } else {
                                field.style.display = 'none';
                            }
                        }

                        // Add event listeners to all target fields
                        targetFields.forEach(function (targetField) {
                            // All fields get change event (works for select, radio, checkbox, and input)
                            targetField.addEventListener('change', checkCondition);

                            // For checkboxes and radio buttons, also listen to click events for immediate feedback
                            if (targetField.type === 'checkbox' || targetField.type === 'radio') {
                                targetField.addEventListener('click', checkCondition);
                            }
                            // For select dropdowns, change event is sufficient (already added above)
                            // For text inputs, also listen to input event for real-time updates
                            else if (targetField.tagName === 'INPUT' && targetField.type !== 'checkbox' && targetField.type !== 'radio') {
                                targetField.addEventListener('input', checkCondition);
                            }
                        });

                        // Initial check
                        checkCondition();
                    }
                });

                // Handle OLD format: hide conditions (data-hide-if-field) - backward compatibility
                const hideFields = document.querySelectorAll('[data-hide-if-field]');
                hideFields.forEach(function (field) {
                    // Skip if this field already has new format conditions
                    if (field.hasAttribute('data-hide-if-conditions')) {
                        return;
                    }

                    const hideIfField = field.getAttribute('data-hide-if-field');
                    const hideIfOperator = field.getAttribute('data-hide-if-operator') || 'equals';
                    const hideIfValue = field.getAttribute('data-hide-if-value');

                    // Find all target fields (could be multiple checkboxes with same name)
                    const targetFields = findFieldsByName(hideIfField);

                    if (targetFields.length > 0) {
                        function checkHideCondition() {
                            const shouldHide = checkConditionMet(hideIfField, hideIfOperator, hideIfValue);

                            if (shouldHide) {
                                field.style.display = 'none';
                            } else {
                                field.style.display = 'block';
                            }
                        }

                        // Add event listeners to all target fields
                        targetFields.forEach(function (targetField) {
                            // All fields get change event (works for select, radio, checkbox, and input)
                            targetField.addEventListener('change', checkHideCondition);

                            // For checkboxes and radio buttons, also listen to click events for immediate feedback
                            if (targetField.type === 'checkbox' || targetField.type === 'radio') {
                                targetField.addEventListener('click', checkHideCondition);
                            }
                            // For select dropdowns, change event is sufficient (already added above)
                            // For text inputs, also listen to input event for real-time updates
                            else if (targetField.tagName === 'INPUT' && targetField.type !== 'checkbox' && targetField.type !== 'radio') {
                                targetField.addEventListener('input', checkHideCondition);
                            }
                        });

                        // Initial check
                        checkHideCondition();
                    }
                });
            });
        </script>
    @endpush
@endsection