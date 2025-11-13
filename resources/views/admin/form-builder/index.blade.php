@extends('layouts.admin-minimal')

@section('title', 'Form Builder - ' . $form->name . ' - BMMB Digital Forms')
@section('page-title', 'Form Builder: ' . $form->name)
@section('page-description', 'Configure form fields dynamically')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

<div class="mb-4 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
            <i class='bx bx-code-alt text-purple-600 dark:text-purple-400 text-xl'></i>
        </div>
        <div>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Form Builder: {{ $form->name }}</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400">
                @php
                    $totalFields = 0;
                    foreach($sectionsWithFields ?? [] as $sectionData) {
                        $totalFields += $sectionData['fields']->count();
                    }
                @endphp
                Total: {{ $totalFields }} field(s)
            </p>
        </div>
    </div>
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.form-sections.index', $form) }}" class="inline-flex items-center px-3 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 dark:text-indigo-400 rounded-lg text-xs transition-colors">
            <i class='bx bx-list-ul mr-1.5'></i>
            Manage Sections
        </a>
        <button onclick="openCreateModal()" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
            <i class='bx bx-plus mr-1.5'></i>
            Create Field
        </button>
    </div>
</div>

<!-- Form Fields Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    @if(empty($sectionsWithFields))
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class='bx bx-file-blank text-2xl text-gray-400 dark:text-gray-500'></i>
            </div>
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No fields found</h4>
            <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">Get started by adding your first field</p>
            <button onclick="openCreateModal()" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                <i class='bx bx-plus mr-1.5'></i>
                Create First Field
            </button>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Order
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Field Label
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Field Name
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Section
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Column
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="fieldsTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($sectionsWithFields as $sectionId => $sectionData)
                        @php
                            $section = $sectionData['section'];
                            $sectionFields = $sectionData['fields'];
                        @endphp
                        @foreach($sectionFields as $field)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors sortable-row" 
                            data-field-id="{{ $field->id }}" 
                            data-section="{{ $section->id }}" 
                            data-sort-order="{{ $field->sort_order }}"
                            data-section-id="{{ $section->id }}">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="cursor-move text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 drag-handle">
                                        <i class='bx bx-menu text-lg'></i>
                                    </div>
                                    <span class="text-xs font-medium text-gray-900 dark:text-white">
                                        {{ $field->sort_order }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs font-semibold text-gray-900 dark:text-white">
                                    {{ $field->field_label }}
                                    @if($field->is_required)
                                        <span class="text-red-500 ml-1">*</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                                    {{ $field->field_name }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $section->section_label }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ ucfirst($field->field_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <select onchange="updateFieldColumn({{ $field->id }}, this.value)" 
                                        class="px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                                    <option value="left" {{ ($field->grid_column ?? 'left') === 'left' ? 'selected' : '' }}>Left</option>
                                    <option value="right" {{ ($field->grid_column ?? 'left') === 'right' ? 'selected' : '' }}>Right</option>
                                    <option value="full" {{ ($field->grid_column ?? 'left') === 'full' ? 'selected' : '' }}>Full</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $field->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $field->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="moveFieldUp({{ $field->id }}, {{ $section->id }})" 
                                            class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 rounded transition-colors" title="Move Up">
                                        <i class='bx bx-chevron-up'></i>
                                    </button>
                                    <button onclick="moveFieldDown({{ $field->id }}, {{ $section->id }})" 
                                            class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 rounded transition-colors" title="Move Down">
                                        <i class='bx bx-chevron-down'></i>
                                    </button>
                                    <button onclick="openViewFieldModal({{ $field->id }})" 
                                            class="inline-flex items-center justify-center px-2 lg:px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 rounded-lg transition-colors" title="View Field">
                                        <i class='bx bx-show lg:mr-1'></i>
                                        <span class="hidden lg:inline">View</span>
                                    </button>
                                    <button onclick="editField({{ $field->id }})" 
                                            class="inline-flex items-center justify-center px-2 lg:px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 dark:text-orange-400 rounded-lg transition-colors" title="Edit Field">
                                        <i class='bx bx-edit lg:mr-1'></i>
                                        <span class="hidden lg:inline">Edit</span>
                                    </button>
                                    <button onclick="deleteField({{ $field->id }})" 
                                            class="inline-flex items-center justify-center px-2 lg:px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg transition-colors" title="Delete Field">
                                        <i class='bx bx-trash lg:mr-1'></i>
                                        <span class="hidden lg:inline">Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Create Field Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-0 hidden z-50 flex items-center justify-center transition-opacity duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Create New Field</h3>
                <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
            <form id="createFieldForm" action="{{ route('admin.form-builder.fields.store', $form) }}" method="POST">
                @csrf
                <div class="space-y-4" id="createModalContent">
                    <!-- Create form will be loaded here -->
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Field Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-0 hidden z-50 flex items-center justify-center transition-opacity duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0">
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

<!-- View Field Modal -->
<div id="viewFieldModal" class="fixed inset-0 bg-black bg-opacity-0 hidden z-50 flex items-center justify-center transition-opacity duration-300 p-2 sm:p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0">
        <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Field Details</h3>
                <button onclick="closeViewFieldModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
            <div id="viewFieldModalContent">
                <!-- Field details will be loaded here -->
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Field Confirmation Modal -->
<div id="deleteFieldModal" class="fixed inset-0 bg-black bg-opacity-0 hidden z-50 flex items-center justify-center transition-opacity duration-300 p-2 sm:p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 transform scale-95 transition-all duration-300 opacity-0">
        <div class="p-6">
            <div class="flex items-center justify-center mb-4 relative">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white text-center">Delete Field</h3>
                <button onclick="closeDeleteFieldModal()" class="absolute right-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
            <div class="mb-6">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-trash text-2xl text-red-600 dark:text-red-400'></i>
                </div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white text-center mb-2">Are you sure?</h4>
                <p class="text-xs text-gray-600 dark:text-gray-400 text-center" id="deleteFieldMessage">
                    This action cannot be undone. This will permanently delete the field.
                </p>
            </div>
            <form id="deleteFieldForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex items-center justify-center space-x-3">
                    <button type="button" onclick="closeDeleteFieldModal()" 
                            class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors">
                        Yes, delete it
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Convert textarea options to JSON and format conditional logic on create form submit
    document.addEventListener('DOMContentLoaded', function() {
        const createForm = document.getElementById('createFieldForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                // Handle field options
                const optionsText = document.getElementById('create_field_options_text');
                const optionsContainer = document.getElementById('createFieldOptionsContainer');
                
                if (optionsContainer && optionsContainer.style.display !== 'none' && optionsText && optionsText.value) {
                    const options = {};
                    optionsText.value.split('\n').forEach(line => {
                        const parts = line.trim().split('|');
                        if (parts.length === 2) {
                            options[parts[0].trim()] = parts[1].trim();
                        }
                    });
                    const hiddenInput = document.getElementById('create_field_options');
                    if (hiddenInput) {
                        hiddenInput.value = JSON.stringify(options);
                    }
                }
                
                // Handle conditional logic
                const isConditional = document.getElementById('create_is_conditional')?.checked;
                if (isConditional) {
                    const action = document.getElementById('create_conditional_action')?.value;
                    const field = document.getElementById('create_conditional_field')?.value;
                    const operator = document.getElementById('create_conditional_operator')?.value;
                    const value = document.getElementById('create_conditional_value')?.value;
                    
                    if (action && field && operator) {
                        const conditionalLogic = {};
                        conditionalLogic[action] = {
                            field: field,
                            operator: operator,
                            value: value || ''
                        };
                        
                        // Add hidden input for conditional_logic
                        let hiddenInput = document.getElementById('create_conditional_logic');
                        if (!hiddenInput) {
                            hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'conditional_logic';
                            hiddenInput.id = 'create_conditional_logic';
                            createForm.appendChild(hiddenInput);
                        }
                        hiddenInput.value = JSON.stringify(conditionalLogic);
                    }
                }
            });
        }
        
        // Convert textarea options to JSON and format conditional logic on edit form submit
        const editForm = document.getElementById('editFieldForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                // Handle field options
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
                
                // Handle conditional logic
                const isConditional = document.getElementById('edit_is_conditional')?.checked;
                if (isConditional) {
                    const action = document.getElementById('edit_conditional_action')?.value;
                    const field = document.getElementById('edit_conditional_field')?.value;
                    const operator = document.getElementById('edit_conditional_operator')?.value;
                    const value = document.getElementById('edit_conditional_value')?.value;
                    
                    if (action && field && operator) {
                        const conditionalLogic = {};
                        conditionalLogic[action] = {
                            field: field,
                            operator: operator,
                            value: value || ''
                        };
                        
                        // Add hidden input for conditional_logic
                        let hiddenInput = document.getElementById('edit_conditional_logic');
                        if (!hiddenInput) {
                            hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'conditional_logic';
                            hiddenInput.id = 'edit_conditional_logic';
                            editForm.appendChild(hiddenInput);
                        }
                        hiddenInput.value = JSON.stringify(conditionalLogic);
                    }
                } else {
                    // Remove conditional_logic if disabled
                    const hiddenInput = document.getElementById('edit_conditional_logic');
                    if (hiddenInput) {
                        hiddenInput.value = JSON.stringify({});
                    }
                }
            });
        }
    });
    
    // Toggle conditional logic section for create modal
    function toggleCreateConditionalLogic() {
        const checkbox = document.getElementById('create_is_conditional');
        const container = document.getElementById('createConditionalLogicContainer');
        const operator = document.getElementById('create_conditional_operator');
        
        if (checkbox && container) {
            if (checkbox.checked) {
                container.style.display = 'block';
                toggleCreateConditionalValue();
            } else {
                container.style.display = 'none';
            }
        }
    }
    
    // Toggle conditional logic section for edit modal
    function toggleEditConditionalLogic() {
        const checkbox = document.getElementById('edit_is_conditional');
        const container = document.getElementById('editConditionalLogicContainer');
        
        if (checkbox && container) {
            if (checkbox.checked) {
                container.style.display = 'block';
                toggleEditConditionalValue();
            } else {
                container.style.display = 'none';
            }
        }
    }
    
    // Toggle value field based on operator for create modal
    function toggleCreateConditionalValue() {
        const operator = document.getElementById('create_conditional_operator');
        const valueContainer = document.getElementById('create_conditional_value_container');
        const valueInput = document.getElementById('create_conditional_value');
        const valueRequired = document.getElementById('create_conditional_value_required');
        
        if (operator && valueContainer) {
            const operatorValue = operator.value;
            if (operatorValue === 'checked' || operatorValue === 'not_checked') {
                valueContainer.style.display = 'none';
                if (valueInput) valueInput.removeAttribute('required');
                if (valueRequired) valueRequired.style.display = 'none';
            } else {
                valueContainer.style.display = 'block';
                if (valueInput) valueInput.setAttribute('required', 'required');
                if (valueRequired) valueRequired.style.display = 'inline';
            }
        }
    }
    
    // Toggle value field based on operator for edit modal
    function toggleEditConditionalValue() {
        const operator = document.getElementById('edit_conditional_operator');
        const valueContainer = document.getElementById('edit_conditional_value_container');
        const valueInput = document.getElementById('edit_conditional_value');
        const valueRequired = document.getElementById('edit_conditional_value_required');
        
        if (operator && valueContainer) {
            const operatorValue = operator.value;
            if (operatorValue === 'checked' || operatorValue === 'not_checked') {
                valueContainer.style.display = 'none';
                if (valueInput) valueInput.removeAttribute('required');
                if (valueRequired) valueRequired.style.display = 'none';
            } else {
                valueContainer.style.display = 'block';
                if (valueInput) valueInput.setAttribute('required', 'required');
                if (valueRequired) valueRequired.style.display = 'inline';
            }
        }
    }

    function openCreateModal() {
        const container = document.getElementById('createModalContent');
        
        // Build create form HTML (same as edit form structure)
        container.innerHTML = `
            <!-- Section -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="create_section_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                        Section <span class="text-red-500">*</span>
                    </label>
                    <a href="{{ route('admin.form-sections.index', $form) }}" target="_blank" class="text-xs text-orange-600 dark:text-orange-400 hover:underline">
                        <i class='bx bx-plus text-xs mr-1'></i>Manage Sections
                    </a>
                </div>
                <select name="section_id" id="create_section_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Select Section</option>
                    @foreach($form->sections as $section)
                        <option value="{{ $section->id }}">{{ $section->section_label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Field Name -->
            <div>
                <label for="create_field_name" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Field Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="field_name" id="create_field_name" required
                       placeholder="e.g., applicant_name"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Unique identifier (no spaces, use underscore)</p>
            </div>

            <!-- Field Label -->
            <div>
                <label for="create_field_label" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Field Label <span class="text-red-500">*</span>
                </label>
                <input type="text" name="field_label" id="create_field_label" required
                       placeholder="e.g., Full Name"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <!-- Field Type -->
            <div>
                <label for="create_field_type" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Field Type <span class="text-red-500">*</span>
                </label>
                <select name="field_type" id="create_field_type" required
                        onchange="toggleCreateFieldOptions()"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Select Type</option>
                    @foreach($fieldTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Placeholder -->
            <div>
                <label for="create_field_placeholder" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Placeholder
                </label>
                <input type="text" name="field_placeholder" id="create_field_placeholder"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <!-- Help Text -->
            <div>
                <label for="create_field_help_text" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Help Text
                </label>
                <textarea name="field_help_text" id="create_field_help_text" rows="2"
                          placeholder="e.g., Please enter your name exactly as it appears on your identification document."
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">This text will appear below the input field</p>
            </div>

            <!-- Field Options (for select/radio/checkbox) -->
            <div id="createFieldOptionsContainer" style="display: none;">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Options (one per line, format: value|label)
                </label>
                <textarea id="create_field_options_text" rows="4"
                          placeholder="value1|Label 1&#10;value2|Label 2&#10;value3|Label 3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                <input type="hidden" name="field_options" id="create_field_options">
            </div>

            <!-- Grid Column -->
            <div>
                <label for="create_grid_column" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Column Position
                </label>
                <select name="grid_column" id="create_grid_column"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="left">Left Column</option>
                    <option value="right">Right Column</option>
                    <option value="full">Full Width</option>
                </select>
            </div>

            <!-- Checkboxes -->
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_required" id="create_is_required" value="1"
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                    <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Required Field</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" id="create_is_active" value="1" checked
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                    <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Active</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_conditional" id="create_is_conditional" value="1"
                           onchange="toggleCreateConditionalLogic()"
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                    <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Enable Conditional Logic</span>
                </label>
            </div>

            <!-- Conditional Logic Section -->
            <div id="createConditionalLogicContainer" style="display: none;" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                <h4 class="text-xs font-semibold text-gray-900 dark:text-white mb-3">Conditional Logic Settings</h4>
                
                <!-- Action Type (Show/Hide) -->
                <div class="mb-3">
                    <label for="create_conditional_action" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Action
                    </label>
                    <select name="conditional_action" id="create_conditional_action"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="show_if">Show this field if...</option>
                        <option value="hide_if">Hide this field if...</option>
                    </select>
                </div>

                <!-- Field to Check -->
                <div class="mb-3">
                    <label for="create_conditional_field" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Check Field <span class="text-red-500">*</span>
                    </label>
                    <select name="conditional_field" id="create_conditional_field" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Select a field...</option>
                        @foreach($form->fields as $existingField)
                            <option value="{{ $existingField->field_name }}">{{ $existingField->field_label }} ({{ $existingField->field_name }})</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select the field that will trigger this condition</p>
                </div>

                <!-- Operator -->
                <div class="mb-3">
                    <label for="create_conditional_operator" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Operator <span class="text-red-500">*</span>
                    </label>
                    <select name="conditional_operator" id="create_conditional_operator" required
                            onchange="toggleCreateConditionalValue()"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="equals">Equals</option>
                        <option value="contains">Contains</option>
                        <option value="not_equals">Not Equals</option>
                        <option value="checked">Checked (for checkboxes)</option>
                        <option value="not_checked">Not Checked (for checkboxes)</option>
                    </select>
                </div>

                <!-- Value -->
                <div class="mb-3" id="create_conditional_value_container">
                    <label for="create_conditional_value" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Value <span class="text-red-500" id="create_conditional_value_required">*</span>
                    </label>
                    <input type="text" name="conditional_value" id="create_conditional_value"
                           placeholder="e.g., email, 1, true"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter the value to match. For checkboxes, use "1", "true", or "checked" for checked state.</p>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition-colors">
                Create Field
            </button>
        `;
        
        // Show modal with animation
        const modal = document.getElementById('createModal');
        const modalContent = modal.querySelector('div');
        modal.classList.remove('hidden');
        // Trigger animation
        setTimeout(() => {
            modal.classList.remove('bg-opacity-0');
            modal.classList.add('bg-opacity-50');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeCreateModal() {
        const modal = document.getElementById('createModal');
        const modalContent = modal.querySelector('div');
        // Start exit animation
        modal.classList.remove('bg-opacity-50');
        modal.classList.add('bg-opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        // Hide after animation
        setTimeout(() => {
            modal.classList.add('hidden');
            // Reset form
            const container = document.getElementById('createModalContent');
            container.innerHTML = '';
        }, 300);
    }

    function toggleCreateFieldOptions() {
        const fieldType = document.getElementById('create_field_type').value;
        const container = document.getElementById('createFieldOptionsContainer');
        const options = ['select', 'radio', 'checkbox', 'multiselect'];
        
        if (options.includes(fieldType)) {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
    }

    function editField(fieldId) {
        // Load field data and populate edit form
        fetch(`{{ route('admin.form-builder.fields.show', [$form, ':field']) }}`.replace(':field', fieldId))
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
                    document.getElementById('editFieldForm').action = `{{ route('admin.form-builder.fields.update', [$form, ':field']) }}`.replace(':field', fieldId);
                    
                    // Build edit form HTML
                    container.innerHTML = `
                        <!-- Section -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="edit_section_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300">
                                    Section <span class="text-red-500">*</span>
                                </label>
                                <a href="{{ route('admin.form-sections.index', $form) }}" target="_blank" class="text-xs text-orange-600 dark:text-orange-400 hover:underline">
                                    <i class='bx bx-plus text-xs mr-1'></i>Manage Sections
                                </a>
                            </div>
                            <select name="section_id" id="edit_section_id" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="">Select Section</option>
                                @foreach($form->sections as $section)
                                    <option value="{{ $section->id }}">{{ $section->section_label }}</option>
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
                            <label class="flex items-center">
                                <input type="checkbox" name="is_conditional" id="edit_is_conditional" value="1"
                                       onchange="toggleEditConditionalLogic()"
                                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                                <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Enable Conditional Logic</span>
                            </label>
                        </div>

                        <!-- Conditional Logic Section -->
                        <div id="editConditionalLogicContainer" style="display: none;" class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                            <h4 class="text-xs font-semibold text-gray-900 dark:text-white mb-3">Conditional Logic Settings</h4>
                            
                            <!-- Action Type (Show/Hide) -->
                            <div class="mb-3">
                                <label for="edit_conditional_action" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Action
                                </label>
                                <select name="conditional_action" id="edit_conditional_action"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="show_if">Show this field if...</option>
                                    <option value="hide_if">Hide this field if...</option>
                                </select>
                            </div>

                            <!-- Field to Check -->
                            <div class="mb-3">
                                <label for="edit_conditional_field" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Check Field <span class="text-red-500">*</span>
                                </label>
                                <select name="conditional_field" id="edit_conditional_field" required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="">Select a field...</option>
                                    @foreach($form->fields as $existingField)
                                        <option value="{{ $existingField->field_name }}">{{ $existingField->field_label }} ({{ $existingField->field_name }})</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Select the field that will trigger this condition</p>
                            </div>

                            <!-- Operator -->
                            <div class="mb-3">
                                <label for="edit_conditional_operator" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Operator <span class="text-red-500">*</span>
                                </label>
                                <select name="conditional_operator" id="edit_conditional_operator" required
                                        onchange="toggleEditConditionalValue()"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    <option value="equals">Equals</option>
                                    <option value="contains">Contains</option>
                                    <option value="not_equals">Not Equals</option>
                                    <option value="checked">Checked (for checkboxes)</option>
                                    <option value="not_checked">Not Checked (for checkboxes)</option>
                                </select>
                            </div>

                            <!-- Value -->
                            <div class="mb-3" id="edit_conditional_value_container">
                                <label for="edit_conditional_value" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Value <span class="text-red-500" id="edit_conditional_value_required">*</span>
                                </label>
                                <input type="text" name="conditional_value" id="edit_conditional_value"
                                       placeholder="e.g., email, 1, true"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Enter the value to match. For checkboxes, use "1", "true", or "checked" for checked state.</p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition-colors">
                            Update Field
                        </button>
                    `;
                    
                    // Populate form fields
                    document.getElementById('edit_section_id').value = field.section_id || '';
                    document.getElementById('edit_field_name').value = field.field_name || '';
                    document.getElementById('edit_field_label').value = field.field_label || '';
                    document.getElementById('edit_field_type').value = field.field_type || '';
                    document.getElementById('edit_field_placeholder').value = field.field_placeholder || '';
                    document.getElementById('edit_field_help_text').value = field.field_help_text || field.field_description || '';
                    document.getElementById('edit_grid_column').value = field.grid_column || 'left';
                    document.getElementById('edit_is_required').checked = field.is_required === true || field.is_required === 1;
                    document.getElementById('edit_is_active').checked = field.is_active === true || field.is_active === 1 || field.is_active === undefined || field.is_active === null;
                    
                    // Handle conditional logic
                    if (field.is_conditional && field.conditional_logic) {
                        const conditionalLogic = typeof field.conditional_logic === 'string' 
                            ? JSON.parse(field.conditional_logic) 
                            : field.conditional_logic;
                        
                        document.getElementById('edit_is_conditional').checked = true;
                        toggleEditConditionalLogic();
                        
                        // Extract show_if or hide_if
                        if (conditionalLogic.show_if) {
                            document.getElementById('edit_conditional_action').value = 'show_if';
                            document.getElementById('edit_conditional_field').value = conditionalLogic.show_if.field || '';
                            document.getElementById('edit_conditional_operator').value = conditionalLogic.show_if.operator || 'equals';
                            document.getElementById('edit_conditional_value').value = conditionalLogic.show_if.value || '';
                        } else if (conditionalLogic.hide_if) {
                            document.getElementById('edit_conditional_action').value = 'hide_if';
                            document.getElementById('edit_conditional_field').value = conditionalLogic.hide_if.field || '';
                            document.getElementById('edit_conditional_operator').value = conditionalLogic.hide_if.operator || 'equals';
                            document.getElementById('edit_conditional_value').value = conditionalLogic.hide_if.value || '';
                        }
                        
                        toggleEditConditionalValue();
                    }
                    
                    // Handle field options
                    if (data.field_options_text) {
                        document.getElementById('edit_field_options_text').value = data.field_options_text;
                        toggleEditFieldOptions();
                    }
                    
                    // Show modal with animation
                    const editModal = document.getElementById('editModal');
                    const editModalContent = editModal.querySelector('div');
                    editModal.classList.remove('hidden');
                    // Trigger animation
                    setTimeout(() => {
                        editModal.classList.remove('bg-opacity-0');
                        editModal.classList.add('bg-opacity-50');
                        editModalContent.classList.remove('scale-95', 'opacity-0');
                        editModalContent.classList.add('scale-100', 'opacity-100');
                    }, 10);
                } else {
                    throw new Error('Invalid response data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load field data. Please try again.');
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
        const editModal = document.getElementById('editModal');
        const editModalContent = editModal.querySelector('div');
        // Start exit animation
        editModal.classList.remove('bg-opacity-50');
        editModal.classList.add('bg-opacity-0');
        editModalContent.classList.remove('scale-100', 'opacity-100');
        editModalContent.classList.add('scale-95', 'opacity-0');
        // Hide after animation
        setTimeout(() => {
            editModal.classList.add('hidden');
            // Reset form
            const container = document.getElementById('editModalContent');
            container.innerHTML = '';
        }, 300);
    }

    function moveFieldUp(fieldId, sectionId) {
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

    function moveFieldDown(fieldId, sectionId) {
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
        fetch(`{{ route('admin.form-builder.fields.reorder', $form) }}`, {
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
        fetch(`{{ route('admin.form-builder.fields.column', [$form, ':field']) }}`.replace(':field', fieldId), {
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
                // Reload page to see changes
                setTimeout(() => location.reload(), 300);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function deleteField(fieldId) {
        // Set form action
        document.getElementById('deleteFieldForm').action = `{{ route('admin.form-builder.fields.destroy', [$form, ':field']) }}`.replace(':field', fieldId);
        
        // Show modal with animation
        const deleteModal = document.getElementById('deleteFieldModal');
        const deleteModalContent = deleteModal.querySelector('div');
        deleteModal.classList.remove('hidden');
        // Trigger animation
        setTimeout(() => {
            deleteModal.classList.remove('bg-opacity-0');
            deleteModal.classList.add('bg-opacity-50');
            deleteModalContent.classList.remove('scale-95', 'opacity-0');
            deleteModalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeDeleteFieldModal() {
        const deleteModal = document.getElementById('deleteFieldModal');
        const deleteModalContent = deleteModal.querySelector('div');
        // Start exit animation
        deleteModal.classList.remove('bg-opacity-50');
        deleteModal.classList.add('bg-opacity-0');
        deleteModalContent.classList.remove('scale-100', 'opacity-100');
        deleteModalContent.classList.add('scale-95', 'opacity-0');
        // Hide after animation
        setTimeout(() => {
            deleteModal.classList.add('hidden');
        }, 300);
    }

    function openViewFieldModal(fieldId) {
        const modal = document.getElementById('viewFieldModal');
        const modalContent = modal.querySelector('div');
        const contentContainer = document.getElementById('viewFieldModalContent');
        
        // Show modal with loading state
        modal.classList.remove('hidden');
        contentContainer.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
            </div>
        `;
        
        // Trigger animation
        setTimeout(() => {
            modal.classList.remove('bg-opacity-0');
            modal.classList.add('bg-opacity-50');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
        
        // Fetch field data
        fetch(`{{ route('admin.form-builder.fields.view', [$form, ':field']) }}`.replace(':field', fieldId), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.field) {
                const field = data.field;
                const statusColors = {
                    'active': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                    'inactive': 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                };
                
                // Format field options
                let fieldOptionsHtml = '-';
                if (field.field_options && typeof field.field_options === 'object') {
                    const options = Object.entries(field.field_options).map(([value, label]) => `${value}: ${label}`).join(', ');
                    fieldOptionsHtml = options || '-';
                }
                
                contentContainer.innerHTML = `
                    <!-- Field Details Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                        <!-- Title Section -->
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">${field.field_label || field.field_name || ''}</h2>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">${field.field_help_text || 'No help text provided'}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${field.is_active ? statusColors['active'] : statusColors['inactive']}">
                                        ${field.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                    ${field.is_required ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Required</span>' : ''}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Field Details List -->
                        <div class="px-4 py-2">
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Field Name</span>
                                <span class="text-xs font-mono text-gray-900 dark:text-white">${field.field_name || ''}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Field Type</span>
                                <span class="text-xs text-gray-900 dark:text-white">${field.field_type ? field.field_type.charAt(0).toUpperCase() + field.field_type.slice(1) : ''}</span>
                            </div>
                            ${field.section ? `
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Section</span>
                                <span class="text-xs text-gray-900 dark:text-white">${field.section.section_label || field.section.section_key || ''}</span>
                            </div>
                            ` : ''}
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Grid Column</span>
                                <span class="text-xs text-gray-900 dark:text-white">${field.grid_column ? field.grid_column.charAt(0).toUpperCase() + field.grid_column.slice(1) : 'Left'}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Sort Order</span>
                                <span class="text-xs text-gray-900 dark:text-white">${field.sort_order || ''}</span>
                            </div>
                            ${field.field_placeholder ? `
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Placeholder</span>
                                <span class="text-xs text-gray-600 dark:text-gray-400">${field.field_placeholder}</span>
                            </div>
                            ` : ''}
                            ${field.field_default_value ? `
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Default Value</span>
                                <span class="text-xs text-gray-600 dark:text-gray-400">${field.field_default_value}</span>
                            </div>
                            ` : ''}
                            ${fieldOptionsHtml !== '-' ? `
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Options</span>
                                <span class="text-xs text-gray-600 dark:text-gray-400 max-w-xs truncate" title="${fieldOptionsHtml}">${fieldOptionsHtml}</span>
                            </div>
                            ` : ''}
                            ${field.is_conditional ? `
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Conditional Field</span>
                                <span class="text-xs text-gray-600 dark:text-gray-400">${field.conditional_field || '-'}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Conditional Value</span>
                                <span class="text-xs text-gray-600 dark:text-gray-400">${field.conditional_value || '-'}</span>
                            </div>
                            ` : ''}
                            ${field.field_validation_rules ? `
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Validation Rules</span>
                                <span class="text-xs text-gray-600 dark:text-gray-400 max-w-xs truncate" title="${field.field_validation_rules}">${field.field_validation_rules}</span>
                            </div>
                            ` : ''}
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Created At</span>
                                <span class="text-xs text-gray-600 dark:text-gray-400">${field.created_at || ''}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 last:border-b-0">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Updated At</span>
                                <span class="text-xs text-gray-600 dark:text-gray-400">${field.updated_at || ''}</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-end space-x-2">
                                <button onclick="closeViewFieldModal(); setTimeout(() => editField(${fieldId}), 300);" class="inline-flex items-center px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 dark:text-orange-400 rounded-lg text-xs transition-colors">
                                    <i class='bx bx-edit mr-1'></i>
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                contentContainer.innerHTML = `
                    <div class="text-center py-8">
                        <div class="text-red-600 dark:text-red-400 text-sm">Failed to load field details</div>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching field:', error);
            contentContainer.innerHTML = `
                <div class="text-center py-8">
                    <div class="text-red-600 dark:text-red-400 text-sm">Error loading field details</div>
                </div>
            `;
        });
    }

    function closeViewFieldModal() {
        const modal = document.getElementById('viewFieldModal');
        const modalContent = modal.querySelector('div');
        // Start exit animation
        modal.classList.remove('bg-opacity-50');
        modal.classList.add('bg-opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        // Hide after animation
        setTimeout(() => {
            modal.classList.add('hidden');
            const container = document.getElementById('viewFieldModalContent');
            container.innerHTML = '';
        }, 300);
    }

    // Initialize drag-and-drop for fields
    document.addEventListener('DOMContentLoaded', function() {
        const tbody = document.getElementById('fieldsTableBody');
        if (tbody && typeof Sortable !== 'undefined') {
            new Sortable(tbody, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'opacity-50',
                chosenClass: 'sortable-chosen',
                onEnd: function(evt) {
                    const rows = Array.from(tbody.querySelectorAll('tr[data-field-id]'));
                    
                    // Group fields by section based on their new positions in the table
                    const sectionGroups = {};
                    rows.forEach((row) => {
                        const sectionId = parseInt(row.dataset.sectionId);
                        if (!sectionGroups[sectionId]) {
                            sectionGroups[sectionId] = [];
                        }
                        sectionGroups[sectionId].push({
                            row: row,
                            fieldId: parseInt(row.dataset.fieldId)
                        });
                    });
                    
                    // Update sort orders for each section based on their position within that section
                    const updatePromises = [];
                    Object.keys(sectionGroups).forEach(sectionId => {
                        const sectionFields = sectionGroups[sectionId];
                        const fields = sectionFields.map((item, index) => ({
                            id: item.fieldId,
                            sort_order: index + 1
                        }));
                        
                        const promise = fetch(`{{ route('admin.form-builder.fields.reorder', $form) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ fields: fields })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Update sort order numbers in the UI for this section
                                sectionFields.forEach((item, index) => {
                                    const orderSpan = item.row.querySelector('td span');
                                    if (orderSpan) {
                                        orderSpan.textContent = index + 1;
                                    }
                                    item.row.dataset.sortOrder = index + 1;
                                });
                                return true;
                            } else {
                                throw new Error(data.message || 'Failed to reorder fields');
                            }
                        });
                        
                        updatePromises.push(promise);
                    });
                    
                    // Wait for all updates to complete
                    Promise.all(updatePromises)
                        .catch(error => {
                            console.error('Error updating sort order:', error);
                            alert('Failed to update field order. Please refresh the page.');
                            // Reload page on error to restore original order
                            location.reload();
                        });
                }
            });
        }
    });
</script>
@endpush
@endsection

