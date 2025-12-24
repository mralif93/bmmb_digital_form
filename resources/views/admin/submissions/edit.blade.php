@extends('layouts.admin-minimal')

@section('title', 'Edit ' . $form->name . ' Submission #' . $submission->id . ' - BMMB Digital Forms')
@section('page-title', 'Edit ' . $form->name . ' Submission #' . $submission->id)
@section('page-description', 'Edit submission details')

@section('content')
<div class="mb-4 flex items-center justify-end">
    <a href="{{ route('admin.submissions.show', [$form->slug, $submission->id]) }}" 
       class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
        <i class='bx bx-arrow-back mr-1.5'></i>
        Back to View
    </a>
</div>

@if($errors->any())
<div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg">
    <ul class="text-sm text-red-800 dark:text-red-400 list-disc list-inside">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.submissions.update', [$form->slug, $submission->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    <!-- Submission Metadata -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class='bx bx-info-circle mr-2 text-primary-600 dark:text-primary-400'></i>
            Submission Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="user_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    User
                </label>
                <select name="user_id" 
                        id="user_id"
                        class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Select User (Optional)</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $submission->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leave empty to use current admin user</p>
            </div>
            <div>
                <label for="branch_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Branch
                </label>
                <select name="branch_id" 
                        id="branch_id"
                        class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Select Branch (Optional)</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id', $submission->branch_id) == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }} ({{ $branch->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status" 
                        id="status"
                        required
                        class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="draft" {{ old('status', $submission->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ old('status', $submission->status) == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="under_review" {{ old('status', $submission->status) == 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="in_progress" {{ old('status', $submission->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="approved" {{ old('status', $submission->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ old('status', $submission->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ old('status', $submission->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="expired" {{ old('status', $submission->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="cancelled" {{ old('status', $submission->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </div>
    </div>

    @foreach($sections as $section)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class='bx bx-file-blank mr-2 text-primary-600 dark:text-primary-400'></i>
            {!! $section->section_label !!}
        </h3>
        @if($section->section_description && trim(strip_tags($section->section_description)))
        <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">{!! $section->section_description !!}</p>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($section->fields as $field)
            @php
                $currentValue = $submissionData[$field->field_name] ?? null;
                // Single checkboxes (without options) should show label inline with the checkbox
                $isSingleCheckbox = $field->field_type === 'checkbox' && (!$field->field_options || !is_array($field->field_options) || empty($field->field_options));
                // Get description position from field settings (default: 'bottom')
                $descriptionPosition = $field->field_settings['description_position'] ?? 'bottom';
                
                // Check if field has conditional logic
                // Don't rely solely on is_conditional flag as it may not be set correctly during import
                $isConditional = !empty($field->conditional_logic) && is_array($field->conditional_logic);
                $conditionalAttrs = '';
                $conditionalStyle = '';
                
                if ($isConditional) {
                    $conditionalLogic = $field->conditional_logic;
                    
                    // Build conditional attributes (supports both new and old formats)
                    if (isset($conditionalLogic['action']) && isset($conditionalLogic['conditions']) && is_array($conditionalLogic['conditions'])) {
                        // New format: multiple conditions with AND/OR logic
                        $action = $conditionalLogic['action']; // 'show_if' or 'hide_if'
                        $logic = $conditionalLogic['logic'] ?? 'and'; // 'and' or 'or'
                        $conditions = $conditionalLogic['conditions'];
                        
                        $dataAttr = $action === 'show_if' ? 'data-show-if-conditions' : 'data-hide-if-conditions';
                        $conditionalAttrs = $dataAttr . '="' . htmlspecialchars(json_encode(['logic' => $logic, 'conditions' => $conditions])) . '"';
                    }
                    // Old format: backward compatibility
                   elseif (isset($conditionalLogic['show_if'])) {
                        $showIf = $conditionalLogic['show_if'];
                        $conditionalAttrs = 'data-show-if-field="' . htmlspecialchars($showIf['field'] ?? '') . '" ';
                        $conditionalAttrs .= 'data-show-if-operator="' . htmlspecialchars($showIf['operator'] ?? 'equals') . '" ';
                        $conditionalAttrs .= 'data-show-if-value="' . htmlspecialchars($showIf['value'] ?? '') . '"';
                    } elseif (isset($conditionalLogic['hide_if'])) {
                        $hideIf = $conditionalLogic['hide_if'];
                        $conditionalAttrs = 'data-hide-if-field="' . htmlspecialchars($hideIf['field'] ?? '') . '" ';
                        $conditionalAttrs .= 'data-hide-if-operator="' . htmlspecialchars($hideIf['operator'] ?? 'equals') . '" ';
                        $conditionalAttrs .= 'data-hide-if-value="' . htmlspecialchars($hideIf['value'] ?? '') . '"';
                    }
                    
                    // Hide conditional fields by default (JavaScript will show them when conditions are met)
                    $conditionalStyle = ' style="display: none;"';
                }
            @endphp
            <div class="{{ $field->grid_column === 'full' ? 'md:col-span-2' : ($field->grid_column === 'right' ? 'md:col-span-1' : 'md:col-span-1') }}" {!! $conditionalAttrs !!}{!! $conditionalStyle !!}>
                @if(!in_array($field->field_type, ['notes']) && !$isSingleCheckbox)
                <label for="{{ $field->field_name }}" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {!! $field->field_label !!}
                    @if($field->is_required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
                {{-- Render description above the field if position is 'top' --}}
                @if($descriptionPosition === 'top' && $field->field_description && trim(strip_tags($field->field_description)))
                <div class="text-xs text-gray-500 dark:text-gray-400 mb-2 field-description">
                    {!! $field->field_description !!}
                </div>
                @endif
                @endif

                @switch($field->field_type)
                    @case('text')
                    @case('email')
                    @case('phone')
                    @case('number')
                    @case('currency')
                        <input type="{{ $field->field_type === 'email' ? 'email' : ($field->field_type === 'number' || $field->field_type === 'currency' ? 'number' : 'text') }}" 
                               name="{{ $field->field_name }}" 
                               id="{{ $field->field_name }}"
                               value="{{ old($field->field_name, $currentValue) }}"
                               placeholder="{{ $field->field_placeholder ?? '' }}"
                               {{ $field->is_required ? 'required' : '' }}
                               class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @break

                    @case('textarea')
                        <textarea name="{{ $field->field_name }}" 
                                  id="{{ $field->field_name }}"
                                  rows="{{ $field->field_settings['rows'] ?? 4 }}"
                                  placeholder="{{ $field->field_placeholder ?? '' }}"
                                  {{ $field->is_required ? 'required' : '' }}
                                  class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old($field->field_name, $currentValue) }}</textarea>
                        @break

                    @case('select')
                        <select name="{{ $field->field_name }}" 
                                id="{{ $field->field_name }}"
                                {{ $field->is_required ? 'required' : '' }}
                                class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Select {{ $field->field_label }}</option>
                            @if($field->field_options && is_array($field->field_options))
                                @foreach($field->field_options as $option)
                                    <option value="{{ $option }}" {{ old($field->field_name, $currentValue) == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @break

                    @case('radio')
                        {{-- DEBUG: Remove after testing --}}
                        <div class="text-xs bg-yellow-100 border border-yellow-400 p-2 mb-2">
                            <strong>DEBUG Radio Field:</strong><br>
                            Field Name: {{ $field->field_name }}<br>
                            Current Value: "{{ $currentValue }}" (type: {{ gettype($currentValue) }})<br>
                            Options: @json($field->field_options)
                        </div>
                        <div class="flex flex-wrap gap-3">
                            @if($field->field_options && is_array($field->field_options))
                                @foreach($field->field_options as $option)
                                @php
                                    // Normalize both values for comparison
                                    $savedValue = trim((string)($currentValue ?? ''));
                                    $optionValue = trim((string)$option);
                                    $isChecked = $savedValue === $optionValue || 
                                                 old($field->field_name) === $optionValue;
                                @endphp
                                <label class="flex items-center">
                                    <input type="radio" 
                                           name="{{ $field->field_name }}" 
                                           value="{{ $option }}"
                                           {{ $isChecked ? 'checked' : '' }}
                                           {{ $field->is_required ? 'required' : '' }}
                                           class="mr-2 text-primary-600 focus:ring-primary-500">
                                    <span class="text-xs text-gray-700 dark:text-gray-300">{{ $option }}</span>
                                </label>
                                @endforeach
                            @endif
                        </div>
                        @break

                    @case('checkbox')
                        @if($field->field_options && is_array($field->field_options))
                            <div class="space-y-2">
                                @foreach($field->field_options as $option)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="{{ $field->field_name }}[]" 
                                           value="{{ $option }}"
                                           {{ is_array($currentValue) && in_array($option, $currentValue) ? 'checked' : '' }}
                                           class="mr-2 text-primary-600 focus:ring-primary-500">
                                    <span class="text-xs text-gray-700 dark:text-gray-300">{{ $option }}</span>
                                </label>
                                @endforeach
                        </div>
                        @else
                            {{-- Render description above single checkbox if position is 'top' --}}
                            @if($descriptionPosition === 'top' && $field->field_description && trim(strip_tags($field->field_description)))
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-2 field-description">
                                {!! $field->field_description !!}
                            </div>
                            @endif
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="{{ $field->field_name }}" 
                                       value="1"
                                       {{ old($field->field_name, $currentValue) ? 'checked' : '' }}
                                       class="mr-2 text-primary-600 focus:ring-primary-500">
                                <span class="text-xs text-gray-700 dark:text-gray-300">{{ $field->field_label }}</span>
                            </label>
                        @endif
                        @break

                    @case('date')
                        <input type="date" 
                               name="{{ $field->field_name }}" 
                               id="{{ $field->field_name }}"
                               value="{{ old($field->field_name, $currentValue) }}"
                               {{ $field->is_required ? 'required' : '' }}
                               class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @break

                    @case('time')
                        <input type="time" 
                               name="{{ $field->field_name }}" 
                               id="{{ $field->field_name }}"
                               value="{{ old($field->field_name, $currentValue) }}"
                               {{ $field->is_required ? 'required' : '' }}
                               class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @break

                    @case('datetime')
                        <input type="datetime-local" 
                               name="{{ $field->field_name }}" 
                               id="{{ $field->field_name }}"
                               value="{{ old($field->field_name, $currentValue) }}"
                               {{ $field->is_required ? 'required' : '' }}
                               class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @break

                    @case('file')
                        <input type="file" 
                               name="{{ $field->field_name }}" 
                               id="{{ $field->field_name }}"
                               {{ $field->is_required && !$currentValue ? 'required' : '' }}
                               class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @if($currentValue)
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Current file: {{ basename($currentValue) }}
                            </p>
                        @endif
                        @if($field->field_help_text)
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $field->field_help_text }}</p>
                        @endif
                        @break

                    @case('signature')
                        <div x-data="{ showSignaturePad: {{ $currentValue ? 'false' : 'true' }} }">
                            <!-- Display saved signature image (if exists) -->
                            @if($currentValue)
                            <div x-show="!showSignaturePad" class="relative">
                                <img src="{{ asset('storage/' . $currentValue) }}" 
                                     alt="Saved signature" 
                                     class="border border-gray-300 dark:border-gray-600 rounded-lg p-2 bg-white dark:bg-gray-700 max-w-full h-auto"
                                     style="max-height: 200px;">
                                <button type="button" 
                                        @click="showSignaturePad = true"
                                        class="mt-2 px-3 py-1.5 text-xs bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                                    <i class='bx bx-edit mr-1'></i>
                                    Change Signature
                                </button>
                            </div>
                            @endif
                            
                            <!-- Signature Pad -->
                            <div x-show="showSignaturePad" class="signature-pad-container">
                                <canvas id="signature-canvas-{{ $field->field_name }}" 
                                        class="signature-canvas border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white w-full"
                                        style="height: 200px; max-width: 600px;"></canvas>
                                <input type="hidden" 
                                       name="{{ $field->field_name }}" 
                                       id="{{ $field->field_name }}-data"
                                       value="{{ old($field->field_name, $currentValue) }}">
                                <div class="mt-2 flex gap-2">
                                    <button type="button" 
                                            onclick="clearSignature('{{ $field->field_name }}')"
                                            class="px-3 py-1.5 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                                        <i class='bx bx-eraser mr-1'></i>
                                        Clear
                                    </button>
                                    @if($currentValue)
                                    <button type="button" 
                                            @click="showSignaturePad = false"
                                            class="px-3 py-1.5 text-xs bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                                        <i class='bx bx-x mr-1'></i>
                                        Cancel
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @break

                    @case('notes')
                        {{-- Notes fields only display description, no input needed --}}
                        @break

                    @default
                        <input type="text" 
                               name="{{ $field->field_name }}" 
                               id="{{ $field->field_name }}"
                               value="{{ old($field->field_name, $currentValue) }}"
                               placeholder="{{ $field->field_placeholder ?? '' }}"
                               {{ $field->is_required ? 'required' : '' }}
                               class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                @endswitch


                @if($field->field_help_text && $field->field_type !== 'file')
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $field->field_help_text }}</p>
                @endif
                
                {{-- Render description below the field if position is 'bottom' (default) --}}
                @if($descriptionPosition === 'bottom' && $field->field_description && trim(strip_tags($field->field_description)) && !in_array($field->field_type, ['notes']))
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 field-description">
                    {!! $field->field_description !!}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <!-- Action Buttons -->
    <div class="flex items-center justify-end space-x-3">
        <a href="{{ route('admin.submissions.show', [$form->slug, $submission->id]) }}" 
           class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
            Cancel
        </a>
        <button type="submit" 
                class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
            <i class='bx bx-save mr-1.5'></i>
            Update Submission
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
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

        // Multiple checkboxes (array) - return array of checked values
        if (fields.length > 1 && firstField.type === 'checkbox') {
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
        const targetFields = findFieldsByName(fieldName);
        const isSingleCheckbox = targetFields.length === 1 && targetFields[0].type === 'checkbox';

        // For single checkbox with 'checked' or 'not_checked' operator
        if (isSingleCheckbox && (operator === 'checked' || operator === 'not_checked')) {
            const isChecked = targetFields[0].checked;
            return operator === 'checked' ? isChecked : !isChecked;
        }

        const actualValue = getFieldValue(fieldName);

        // Handle checkbox arrays
        if (Array.isArray(actualValue)) {
            if (operator === 'equals') {
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
            return actualValue === expectedValue;
        } else if (operator === 'contains') {
            return actualValue && actualValue.toString().includes(expectedValue);
        } else if (operator === 'not_equals') {
            return actualValue !== expectedValue;
        } else if (operator === 'checked') {
            if (targetFields.length > 1) {
                return Array.from(targetFields).some(field => field.checked);
            }
            return targetFields.length > 0 && targetFields[0].checked;
        } else if (operator === 'not_checked') {
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
        return [...new Set(fieldNames)];
    }

    // Add event listeners to all fields involved in conditions
    function addConditionalEventListeners(fieldNames, callback) {
        fieldNames.forEach(function (fieldName) {
            const targetFields = findFieldsByName(fieldName);
            targetFields.forEach(function (targetField) {
                targetField.addEventListener('change', callback);

                if (targetField.type === 'checkbox' || targetField.type === 'radio') {
                    targetField.addEventListener('click', callback);
                }
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

        // Handle NEW format: hide conditions (data-hide-if-conditions)
        const hideFieldsNew = document.querySelectorAll('[data-hide-if-conditions]');
        hideFieldsNew.forEach(function (field) {
            const conditionsData = field.getAttribute('data-hide-if-conditions');
            if (!conditionsData) return;

            try {
                const parsed = JSON.parse(conditionsData);
                const logic = parsed.logic || 'and';
                const conditions = parsed.conditions || [];

                if (conditions.length === 0) return;

                function checkConditions() {
                    const shouldHide = evaluateMultipleConditions(conditions, logic);
                    field.style.display = shouldHide ? 'none' : 'block';
                }

                const fieldNames = getFieldNamesFromConditions(conditions);
                addConditionalEventListeners(fieldNames, checkConditions);
                checkConditions();
            } catch (e) {
                console.error('Error parsing conditional logic:', e);
            }
        });

        // Handle OLD format: single condition (data-show-if-field) - backward compatibility
        const conditionalFields = document.querySelectorAll('[data-show-if-field]');
        conditionalFields.forEach(function (field) {
            if (field.hasAttribute('data-show-if-conditions')) {
                return;
            }

            const showIfField = field.getAttribute('data-show-if-field');
            const showIfOperator = field.getAttribute('data-show-if-operator') || 'equals';
            const showIfValue = field.getAttribute('data-show-if-value');

            const targetFields = findFieldsByName(showIfField);

            if (targetFields.length > 0) {
                field.style.display = 'none';

                function checkCondition() {
                    const shouldShow = checkConditionMet(showIfField, showIfOperator, showIfValue);
                    field.style.display = shouldShow ? 'block' : 'none';
                }

                targetFields.forEach(function (targetField) {
                    targetField.addEventListener('change', checkCondition);

                    if (targetField.type === 'checkbox' || targetField.type === 'radio') {
                        targetField.addEventListener('click', checkCondition);
                    }
                    else if (targetField.tagName === 'INPUT' && targetField.type !== 'checkbox' && targetField.type !== 'radio') {
                        targetField.addEventListener('input', checkCondition);
                    }
                });

                checkCondition();
            }
        });

        // Handle OLD format: hide conditions (data-hide-if-field)
        const hideFields = document.querySelectorAll('[data-hide-if-field]');
        hideFields.forEach(function (field) {
            if (field.hasAttribute('data-hide-if-conditions')) {
                return;
            }

            const hideIfField = field.getAttribute('data-hide-if-field');
            const hideIfOperator = field.getAttribute('data-hide-if-operator') || 'equals';
            const hideIfValue = field.getAttribute('data-hide-if-value');

            const targetFields = findFieldsByName(hideIfField);

            if (targetFields.length > 0) {
                function checkHideCondition() {
                    const shouldHide = checkConditionMet(hideIfField, hideIfOperator, hideIfValue);
                    field.style.display = shouldHide ? 'none' : 'block';
                }

                targetFields.forEach(function (targetField) {
                    targetField.addEventListener('change', checkHideCondition);

                    if (targetField.type === 'checkbox' || targetField.type === 'radio') {
                        targetField.addEventListener('click', checkHideCondition);
                    }
                    else if (targetField.tagName === 'INPUT' && targetField.type !== 'checkbox' && targetField.type !== 'radio') {
                        targetField.addEventListener('input', checkHideCondition);
                    }
                });

                checkHideCondition();
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
    // Initialize signature pads
    document.addEventListener('DOMContentLoaded', function() {
        const signaturePads = {};
        
        document.querySelectorAll('[id^="signature-canvas-"]').forEach(canvas => {
            const fieldName = canvas.id.replace('signature-canvas-', '');
            
            // Set canvas size
            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                
                // Redraw if there was previous data
                if (signaturePads[fieldName] && !signaturePads[fieldName].isEmpty()) {
                    const data = signaturePads[fieldName].toData();
                    signaturePads[fieldName].fromData(data);
                }
            }
            
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);
            
            // Initialize SignaturePad
            signaturePads[fieldName] = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)'
            });
            
            // Store signature data on change
            signaturePads[fieldName].addEventListener('endStroke', () => {
                document.getElementById(fieldName + '-data').value = signaturePads[fieldName].toDataURL();
            });
            
            // Clear signature function
            window['clearSignature_' + fieldName] = function() {
                signaturePads[fieldName].clear();
                document.getElementById(fieldName + '-data').value = '';
            };
        });
    });
    
    function clearSignature(fieldName) {
        if (window['clearSignature_' + fieldName]) {
            window['clearSignature_' + fieldName]();
        }
    }
</script>
<style>
    /* Field description styling for HTML content */
    .field-description ol {
        list-style-type: decimal;
        padding-left: 1.5rem;
        margin: 0.5rem 0;
    }
    
    .field-description ul {
        list-style-type: disc;
        padding-left: 1.5rem;
        margin: 0.5rem 0;
    }
    
    .field-description li {
        margin: 0.25rem 0;
    }
    
    .field-description p {
        margin: 0.5rem 0;
    }
    
    .field-description strong {
        font-weight: 600;
    }
    
    .field-description em {
        font-style: italic;
    }
    
    .field-description u {
        text-decoration: underline;
    }
</style>
@endpush
