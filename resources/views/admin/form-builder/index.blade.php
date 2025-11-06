@extends('layouts.admin-minimal')

@section('title', 'Form Builder - ' . $config['title'] . ' - BMMB Digital Forms')
@section('page-title', 'Form Builder: ' . $config['title'])
@section('page-description', 'Configure form fields dynamically')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
        <i class='bx bx-arrow-back mr-1.5'></i>
        Back to Dashboard
    </a>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 rounded-lg text-xs">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Fields List -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Form Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ $config['title'] }}
                    </h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                        @if($form->application_number ?? $form->request_number ?? null)
                            {{ $form->application_number ?? $form->request_number }}
                        @else
                            Form ID: {{ $form->id }}
                        @endif
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 text-xs font-medium rounded-lg {{ $form->status === 'draft' ? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' }}">
                        {{ ucfirst($form->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Fields by Section -->
        @if($fields->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                <i class='bx bx-file-blank text-4xl text-gray-400 dark:text-gray-500 mb-4'></i>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">No fields configured yet.</p>
                <p class="text-xs text-gray-500 dark:text-gray-500">Add your first field using the form on the right.</p>
            </div>
        @else
            @foreach($fields as $sectionName => $sectionFields)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ ucfirst(str_replace('_', ' ', $sectionName)) }}
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $sectionFields->count() }} field(s)
                        </p>
                    </div>
                    <div class="p-6 space-y-3" data-section="{{ $sectionName }}" id="fields-{{ $sectionName }}">
                        @foreach($sectionFields as $field)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 hover:shadow-sm transition-all" data-field-id="{{ $field->id }}" data-sort-order="{{ $field->sort_order }}">
                                <div class="flex-1 flex items-center space-x-3">
                                    <!-- Drag Handle -->
                                    <div class="cursor-move text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <i class='bx bx-menu text-lg'></i>
                                    </div>
                                    
                                    <!-- Sort Order -->
                                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8 text-center">
                                        {{ $field->sort_order }}
                                    </span>
                                    
                                    <!-- Field Info -->
                                    <div class="flex-1">
                                        <p class="text-xs font-semibold text-gray-900 dark:text-white">
                                            {{ $field->field_label }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400">
                                                {{ ucfirst($field->field_type) }}
                                            </span>
                                            @if($field->is_required)
                                                <span class="text-red-500">Required</span>
                                            @endif
                                            @if(!$field->is_active)
                                                <span class="text-gray-400">Inactive</span>
                                            @endif
                                        </p>
                                    </div>
                                    
                                    <!-- Column Selector -->
                                    <div class="flex items-center space-x-2">
                                        <select onchange="updateFieldColumn({{ $field->id }}, this.value)" 
                                                class="px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                                            <option value="left" {{ ($field->grid_column ?? 'left') === 'left' ? 'selected' : '' }}>Left</option>
                                            <option value="right" {{ ($field->grid_column ?? 'left') === 'right' ? 'selected' : '' }}>Right</option>
                                            <option value="full" {{ ($field->grid_column ?? 'left') === 'full' ? 'selected' : '' }}>Full</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex items-center space-x-2 ml-4">
                                    <button onclick="moveFieldUp({{ $field->id }}, '{{ $sectionName }}')" 
                                            class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 rounded transition-colors">
                                        <i class='bx bx-chevron-up'></i>
                                    </button>
                                    <button onclick="moveFieldDown({{ $field->id }}, '{{ $sectionName }}')" 
                                            class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 rounded transition-colors">
                                        <i class='bx bx-chevron-down'></i>
                                    </button>
                                    <button onclick="editField({{ $field->id }})" 
                                            class="px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 dark:text-orange-400 rounded-lg text-xs transition-colors">
                                        Edit
                                    </button>
                                    <button onclick="deleteField({{ $field->id }})" 
                                            class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg text-xs transition-colors">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Add Field Form -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 sticky top-4">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                <i class='bx bx-plus-circle mr-2'></i>
                Add New Field
            </h3>

            <form id="fieldForm" action="{{ route('admin.form-builder.fields.store', [$type, $form->id]) }}" method="POST">
                @csrf

                <div class="space-y-4">
                    <!-- Section -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label for="field_section" class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                Section <span class="text-red-500">*</span>
                            </label>
                            <a href="{{ route('admin.form-sections.index', $type) }}" target="_blank" class="text-xs text-orange-600 dark:text-orange-400 hover:underline">
                                <i class='bx bx-plus text-xs mr-1'></i>Manage Sections
                            </a>
                        </div>
                        <select name="field_section" id="field_section" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">Select Section</option>
                            @foreach($sections as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Need a new section? <a href="{{ route('admin.form-sections.create', $type) }}" target="_blank" class="text-orange-600 dark:text-orange-400 hover:underline">Create one</a></p>
                    </div>

                    <!-- Field Name -->
                    <div>
                        <label for="field_name" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Field Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="field_name" id="field_name" required
                               placeholder="e.g., applicant_name"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Unique identifier (no spaces, use underscore)</p>
                    </div>

                    <!-- Field Label -->
                    <div>
                        <label for="field_label" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Field Label <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="field_label" id="field_label" required
                               placeholder="e.g., Full Name"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>

                    <!-- Field Type -->
                    <div>
                        <label for="field_type" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Field Type <span class="text-red-500">*</span>
                        </label>
                        <select name="field_type" id="field_type" required
                                onchange="toggleFieldOptions()"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="">Select Type</option>
                            @foreach($fieldTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Placeholder -->
                    <div>
                        <label for="field_placeholder" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Placeholder
                        </label>
                        <input type="text" name="field_placeholder" id="field_placeholder"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    </div>

                    <!-- Help Text -->
                    <div>
                        <label for="field_help_text" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Help Text
                        </label>
                        <textarea name="field_help_text" id="field_help_text" rows="2"
                                  placeholder="e.g., Please enter your name exactly as it appears on your identification document."
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">This text will appear below the input field</p>
                    </div>

                    <!-- Field Options (for select/radio/checkbox) -->
                    <div id="fieldOptionsContainer" style="display: none;">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Options (one per line, format: value|label)
                        </label>
                        <textarea id="field_options_text" rows="4"
                                  placeholder="value1|Label 1&#10;value2|Label 2&#10;value3|Label 3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                        <input type="hidden" name="field_options" id="field_options">
                    </div>

                    <!-- Grid Column -->
                    <div>
                        <label for="grid_column" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Column Position
                        </label>
                        <select name="grid_column" id="grid_column"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            <option value="left">Left Column</option>
                            <option value="right">Right Column</option>
                            <option value="full">Full Width</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Choose which column this field appears in</p>
                    </div>

                    <!-- Checkboxes -->
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_required" value="1"
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                            <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Required Field</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" checked
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                            <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Active</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition-colors">
                        Add Field
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Field Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Edit Field</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
            <form id="editFieldForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4" id="editModalContent">
                    <!-- Edit form will be loaded here -->
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleFieldOptions() {
        const fieldType = document.getElementById('field_type').value;
        const container = document.getElementById('fieldOptionsContainer');
        const options = ['select', 'radio', 'checkbox', 'multiselect'];
        
        if (options.includes(fieldType)) {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }

    // Convert textarea options to JSON on form submit
    document.getElementById('fieldForm').addEventListener('submit', function(e) {
        const optionsText = document.getElementById('field_options_text').value;
        const optionsContainer = document.getElementById('fieldOptionsContainer');
        
        if (optionsContainer.style.display !== 'none' && optionsText) {
            const options = {};
            optionsText.split('\n').forEach(line => {
                const parts = line.trim().split('|');
                if (parts.length === 2) {
                    options[parts[0].trim()] = parts[1].trim();
                }
            });
            document.getElementById('field_options').value = JSON.stringify(options);
        }
    });
    
    // Convert textarea options to JSON on edit form submit
    document.addEventListener('DOMContentLoaded', function() {
        const editForm = document.getElementById('editFieldForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                const optionsText = document.getElementById('edit_field_options_text');
                const optionsContainer = document.getElementById('editFieldOptionsContainer');
                
                if (optionsText && optionsContainer && optionsContainer.style.display !== 'none' && optionsText.value) {
                    const options = {};
                    optionsText.value.split('\n').forEach(line => {
                        const parts = line.trim().split('|');
                        if (parts.length === 2) {
                            options[parts[0].trim()] = parts[1].trim();
                        }
                    });
                    const hiddenInput = document.getElementById('edit_field_options');
                    if (hiddenInput) {
                        hiddenInput.value = JSON.stringify(options);
                    }
                }
            });
        }
    });

    function editField(fieldId) {
        // Load field data and populate edit form
        fetch(`/admin/form-builder/{{ $type }}/{{ $form->id }}/fields/${fieldId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch field data');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.field) {
                    const field = data.field;
                    const container = document.getElementById('editModalContent');
                    
                    // Set form action
                    document.getElementById('editFieldForm').action = `/admin/form-builder/{{ $type }}/{{ $form->id }}/fields/${fieldId}`;
                    
                    // Build edit form HTML
                    container.innerHTML = `
                        <!-- Section -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="edit_field_section" class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                    Section <span class="text-red-500">*</span>
                                </label>
                                <a href="{{ route('admin.form-sections.index', $type) }}" target="_blank" class="text-xs text-orange-600 dark:text-orange-400 hover:underline">
                                    <i class='bx bx-plus text-xs mr-1'></i>Manage Sections
                                </a>
                            </div>
                            <select name="field_section" id="edit_field_section" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">Select Section</option>
                                @foreach($sections as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Field Name -->
                        <div>
                            <label for="edit_field_name" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Field Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="field_name" id="edit_field_name" required
                                   placeholder="e.g., applicant_name"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>

                        <!-- Field Label -->
                        <div>
                            <label for="edit_field_label" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Field Label <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="field_label" id="edit_field_label" required
                                   placeholder="e.g., Full Name"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>

                        <!-- Field Type -->
                        <div>
                            <label for="edit_field_type" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Field Type <span class="text-red-500">*</span>
                            </label>
                            <select name="field_type" id="edit_field_type" required
                                    onchange="toggleEditFieldOptions()"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">Select Type</option>
                                @foreach($fieldTypes as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Placeholder -->
                        <div>
                            <label for="edit_field_placeholder" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Placeholder
                            </label>
                            <input type="text" name="field_placeholder" id="edit_field_placeholder"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>

                        <!-- Help Text -->
                        <div>
                            <label for="edit_field_help_text" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Help Text
                            </label>
                            <textarea name="field_help_text" id="edit_field_help_text" rows="2"
                                      placeholder="e.g., Please enter your name exactly as it appears on your identification document."
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">This text will appear below the input field</p>
                        </div>

                        <!-- Field Options (for select/radio/checkbox) -->
                        <div id="editFieldOptionsContainer" style="display: none;">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Options (one per line, format: value|label)
                            </label>
                            <textarea id="edit_field_options_text" rows="4"
                                      placeholder="value1|Label 1&#10;value2|Label 2&#10;value3|Label 3"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                            <input type="hidden" name="field_options" id="edit_field_options">
                        </div>

                        <!-- Grid Column -->
                        <div>
                            <label for="edit_grid_column" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Column Position
                            </label>
                            <select name="grid_column" id="edit_grid_column"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="left">Left Column</option>
                                <option value="right">Right Column</option>
                                <option value="full">Full Width</option>
                            </select>
                        </div>

                        <!-- Checkboxes -->
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_required" id="edit_is_required" value="1"
                                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                                <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Required Field</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" id="edit_is_active" value="1"
                                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                                <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition-colors">
                            Update Field
                        </button>
                    `;
                    
                    // Populate form fields
                    document.getElementById('edit_field_section').value = field.field_section || '';
                    document.getElementById('edit_field_name').value = field.field_name || '';
                    document.getElementById('edit_field_label').value = field.field_label || '';
                    document.getElementById('edit_field_type').value = field.field_type || '';
                    document.getElementById('edit_field_placeholder').value = field.field_placeholder || '';
                    document.getElementById('edit_field_help_text').value = field.field_help_text || field.field_description || '';
                    document.getElementById('edit_grid_column').value = field.grid_column || 'left';
                    document.getElementById('edit_is_required').checked = field.is_required === true || field.is_required === 1;
                    document.getElementById('edit_is_active').checked = field.is_active === true || field.is_active === 1 || field.is_active === undefined || field.is_active === null;
                    
                    // Handle field options
                    if (data.field_options_text) {
                        document.getElementById('edit_field_options_text').value = data.field_options_text;
                        toggleEditFieldOptions();
                    }
                    
                    // Show modal
                    document.getElementById('editModal').classList.remove('hidden');
                } else {
                    throw new Error('Invalid response data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load field data. Please try again.',
                    confirmButtonColor: '#FE8000',
                });
            });
    }
    
    function toggleEditFieldOptions() {
        const fieldType = document.getElementById('edit_field_type').value;
        const container = document.getElementById('editFieldOptionsContainer');
        const options = ['select', 'radio', 'checkbox', 'multiselect'];
        
        if (options.includes(fieldType)) {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function moveFieldUp(fieldId, sectionName) {
        const fieldRow = document.querySelector(`[data-field-id="${fieldId}"]`);
        const prevField = fieldRow.previousElementSibling;
        
        if (!prevField || !prevField.hasAttribute('data-field-id')) {
            return;
        }
        
        const currentOrder = parseInt(fieldRow.dataset.sortOrder);
        const prevOrder = parseInt(prevField.dataset.sortOrder);
        
        // Swap sort orders
        updateSortOrder(fieldId, prevOrder);
        updateSortOrder(prevField.dataset.fieldId, currentOrder);
        
        // Reload page to reflect changes
        setTimeout(() => location.reload(), 300);
    }

    function moveFieldDown(fieldId, sectionName) {
        const fieldRow = document.querySelector(`[data-field-id="${fieldId}"]`);
        const nextField = fieldRow.nextElementSibling;
        
        if (!nextField || !nextField.hasAttribute('data-field-id')) {
            return;
        }
        
        const currentOrder = parseInt(fieldRow.dataset.sortOrder);
        const nextOrder = parseInt(nextField.dataset.sortOrder);
        
        // Swap sort orders
        updateSortOrder(fieldId, nextOrder);
        updateSortOrder(nextField.dataset.fieldId, currentOrder);
        
        // Reload page to reflect changes
        setTimeout(() => location.reload(), 300);
    }

    function updateSortOrder(fieldId, newOrder) {
        fetch(`/admin/form-builder/{{ $type }}/{{ $form->id }}/fields/reorder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                fields: [{
                    id: fieldId,
                    sort_order: newOrder
                }]
            })
        }).catch(error => console.error('Error updating sort order:', error));
    }

    function updateFieldColumn(fieldId, gridColumn) {
        fetch(`/admin/form-builder/{{ $type }}/{{ $form->id }}/fields/${fieldId}/column`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                grid_column: gridColumn
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                });
                // Reload page to see changes
                setTimeout(() => location.reload(), 500);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to update column position'
            });
        });
    }

    function deleteField(fieldId) {
        Swal.fire({
            title: 'Delete Field?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/form-builder/{{ $type }}/{{ $form->id }}/fields/${fieldId}`;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush
@endsection

