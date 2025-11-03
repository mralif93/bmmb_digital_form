@extends('layouts.admin-minimal')

@section('title', 'Create Form - BMMB Digital Forms')
@section('page-title', 'Create New Form')
@section('page-description', 'Build your custom digital form like RAF')

@section('content')
<div class="max-w-6xl mx-auto">
    <form id="formBuilder" action="{{ route('admin.forms.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Form Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Form Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Form Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                           placeholder="e.g., Remittance Application Form">
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                              placeholder="Brief description of the form purpose"></textarea>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <div>
                    <label for="form_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Form Type
                    </label>
                    <select id="form_type" name="form_type" onchange="updateFormType()"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white">
                        <option value="single">Single Step Form</option>
                        <option value="multi">Multi-Step Form</option>
                        <option value="wizard">Wizard Form (like RAF)</option>
                    </select>
                </div>
                
                <div id="stepCountDiv" class="hidden">
                    <label for="step_count" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Number of Steps
                    </label>
                    <select id="step_count" name="step_count" onchange="updateSteps()"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white">
                        <option value="2">2 Steps</option>
                        <option value="3">3 Steps</option>
                        <option value="4">4 Steps</option>
                        <option value="5">5 Steps</option>
                    </select>
                </div>
                
                <div>
                    <label class="flex items-center mt-6">
                        <input type="checkbox" name="is_active" value="1" checked
                               class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Form is active</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Form Steps Configuration -->
        <div id="stepsConfig" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hidden">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Form Steps Configuration</h3>
            <div id="stepsContainer" class="space-y-4">
                <!-- Steps will be generated here -->
            </div>
        </div>

        <!-- Form Fields Builder -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Form Fields</h3>
                <button type="button" onclick="addField()" class="inline-flex items-center px-3 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class='bx bx-plus mr-2'></i>
                    Add Field
                </button>
            </div>

            <div id="fieldsContainer" class="space-y-4">
                <!-- Fields will be added here dynamically -->
            </div>
        </div>

        <!-- Form Preview -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Form Preview</h3>
            <div id="formPreview" class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                <p class="text-gray-500 dark:text-gray-400 text-center">Form preview will appear here as you add fields</p>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.forms.index') }}" class="px-6 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors">
                Create Form
            </button>
        </div>
    </form>
</div>

<script>
let fieldCount = 0;
let currentStepCount = 2;

const fieldTypes = {
    'text': 'Text Input',
    'email': 'Email Input',
    'number': 'Number Input',
    'textarea': 'Textarea',
    'select': 'Select Dropdown',
    'checkbox': 'Checkbox',
    'radio': 'Radio Button',
    'date': 'Date Picker',
    'file': 'File Upload',
    'tel': 'Phone Number',
    'url': 'URL Input'
};

function updateFormType() {
    const formType = document.getElementById('form_type').value;
    const stepCountDiv = document.getElementById('stepCountDiv');
    const stepsConfig = document.getElementById('stepsConfig');
    
    if (formType === 'multi' || formType === 'wizard') {
        stepCountDiv.classList.remove('hidden');
        stepsConfig.classList.remove('hidden');
        updateSteps();
    } else {
        stepCountDiv.classList.add('hidden');
        stepsConfig.classList.add('hidden');
    }
}

function updateSteps() {
    const stepCount = parseInt(document.getElementById('step_count').value);
    currentStepCount = stepCount;
    const container = document.getElementById('stepsContainer');
    
    container.innerHTML = '';
    
    for (let i = 1; i <= stepCount; i++) {
        const stepHtml = `
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                <h4 class="text-md font-medium text-gray-900 dark:text-white mb-3">Step ${i}</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Step Title</label>
                        <input type="text" name="steps[${i}][title]" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                               placeholder="e.g., Personal Information">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Step Description</label>
                        <input type="text" name="steps[${i}][description]"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                               placeholder="Brief description of this step">
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', stepHtml);
    }
}

function addField() {
    fieldCount++;
    const container = document.getElementById('fieldsContainer');
    
    const fieldHtml = `
        <div class="field-item border border-gray-200 dark:border-gray-600 rounded-lg p-4" data-field-index="${fieldCount}">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-md font-medium text-gray-900 dark:text-white">Field ${fieldCount}</h4>
                <button type="button" onclick="removeField(${fieldCount})" class="text-red-600 hover:text-red-700">
                    <i class='bx bx-trash'></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Field Type</label>
                    <select name="fields[${fieldCount}][field_type]" required onchange="updateFieldOptions(${fieldCount})"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white">
                        ${Object.entries(fieldTypes).map(([value, label]) => 
                            `<option value="${value}">${label}</option>`
                        ).join('')}
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Field Name</label>
                    <input type="text" name="fields[${fieldCount}][field_name]" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                           placeholder="e.g., applicant_name">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Field Label</label>
                    <input type="text" name="fields[${fieldCount}][field_label]" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                           placeholder="e.g., Full Name">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <input type="text" name="fields[${fieldCount}][field_description]"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                           placeholder="Optional description">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Step Assignment</label>
                    <select name="fields[${fieldCount}][step]" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white">
                        ${generateStepOptions()}
                    </select>
                </div>
                
                <div>
                    <label class="flex items-center mt-6">
                        <input type="checkbox" name="fields[${fieldCount}][is_required]" value="1"
                               class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Required field</span>
                    </label>
                </div>
            </div>
            
            <div id="field-options-${fieldCount}" class="mt-4 hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Options (one per line)</label>
                <textarea name="fields[${fieldCount}][field_options]" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                          placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', fieldHtml);
    updateFormPreview();
}

function generateStepOptions() {
    let options = '<option value="1">Step 1</option>';
    for (let i = 2; i <= currentStepCount; i++) {
        options += `<option value="${i}">Step ${i}</option>`;
    }
    return options;
}

function removeField(fieldIndex) {
    const fieldElement = document.querySelector(`[data-field-index="${fieldIndex}"]`);
    if (fieldElement) {
        fieldElement.remove();
        updateFormPreview();
    }
}

function updateFieldOptions(fieldIndex) {
    const fieldType = document.querySelector(`[data-field-index="${fieldIndex}"] select[name*="[field_type]"]`).value;
    const optionsContainer = document.getElementById(`field-options-${fieldIndex}`);
    
    if (['select', 'radio', 'checkbox'].includes(fieldType)) {
        optionsContainer.classList.remove('hidden');
    } else {
        optionsContainer.classList.add('hidden');
    }
    updateFormPreview();
}

function updateFormPreview() {
    const preview = document.getElementById('formPreview');
    const fields = document.querySelectorAll('.field-item');
    
    if (fields.length === 0) {
        preview.innerHTML = '<p class="text-gray-500 dark:text-gray-400 text-center">Form preview will appear here as you add fields</p>';
        return;
    }
    
    let previewHtml = '<div class="space-y-4">';
    
    fields.forEach((field, index) => {
        const fieldType = field.querySelector('select[name*="[field_type]"]').value;
        const fieldLabel = field.querySelector('input[name*="[field_label]"]').value || 'Field Label';
        const fieldName = field.querySelector('input[name*="[field_name]"]').value || 'field_name';
        const isRequired = field.querySelector('input[name*="[is_required]"]').checked;
        
        previewHtml += `
            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    ${fieldLabel} ${isRequired ? '<span class="text-red-500">*</span>' : ''}
                </label>
                ${generateFieldPreview(fieldType, fieldName)}
            </div>
        `;
    });
    
    previewHtml += '</div>';
    preview.innerHTML = previewHtml;
}

function generateFieldPreview(fieldType, fieldName) {
    switch (fieldType) {
        case 'text':
        case 'email':
        case 'tel':
        case 'url':
            return `<input type="${fieldType}" name="${fieldName}" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Enter ${fieldName}">`;
        case 'number':
            return `<input type="number" name="${fieldName}" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Enter number">`;
        case 'date':
            return `<input type="date" name="${fieldName}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">`;
        case 'textarea':
            return `<textarea name="${fieldName}" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Enter text"></textarea>`;
        case 'select':
            return `<select name="${fieldName}" class="w-full px-3 py-2 border border-gray-300 rounded-lg"><option value="">Select option</option></select>`;
        case 'checkbox':
            return `<div class="flex items-center"><input type="checkbox" name="${fieldName}" class="mr-2"><span>Checkbox option</span></div>`;
        case 'radio':
            return `<div class="flex items-center"><input type="radio" name="${fieldName}" class="mr-2"><span>Radio option</span></div>`;
        case 'file':
            return `<input type="file" name="${fieldName}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">`;
        default:
            return `<input type="text" name="${fieldName}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">`;
    }
}

// Add first field by default
document.addEventListener('DOMContentLoaded', function() {
    addField();
});

// Form submission
document.getElementById('formBuilder').addEventListener('submit', function(e) {
    const fields = document.querySelectorAll('.field-item');
    if (fields.length === 0) {
        e.preventDefault();
        alert('Please add at least one field to the form');
        return;
    }
});
</script>
@endsection
