@extends('layouts.admin-minimal')

@section('title', 'Edit ' . $form->name . ' Submission #' . $submission->id . ' - BMMB Digital Forms')
@section('page-title', 'Edit ' . $form->name . ' Submission #' . $submission->id)
@section('page-description', 'Edit submission details')

@section('content')
<div class="mb-4 flex items-center justify-end">
    <a href="{{ route('admin.submissions.show', [$form->slug, $submission->id]) }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
        <i class='bx bx-arrow-back mr-1.5'></i>
        Back to Submission
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

@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

<form action="{{ route('admin.submissions.update', [$form->slug, $submission->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    @foreach($sections as $section)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i class='bx bx-file-blank mr-2 text-primary-600 dark:text-primary-400'></i>
            {{ $section->section_label }}
        </h3>
        @if($section->section_description)
        <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">{{ $section->section_description }}</p>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($section->fields as $field)
            <div class="{{ $field->grid_column === 'full' ? 'md:col-span-2' : ($field->grid_column === 'right' ? 'md:col-span-1' : 'md:col-span-1') }}">
                <label for="{{ $field->field_name }}" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ $field->field_label }}
                    @if($field->is_required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
                @if($field->field_description)
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $field->field_description }}</p>
                @endif

                @php
                    $currentValue = $submissionData[$field->field_name] ?? null;
                @endphp

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
                        <div class="space-y-2">
                            @if($field->field_options && is_array($field->field_options))
                                @foreach($field->field_options as $option)
                                <label class="flex items-center">
                                    <input type="radio" 
                                           name="{{ $field->field_name }}" 
                                           value="{{ $option }}"
                                           {{ old($field->field_name, $currentValue) == $option ? 'checked' : '' }}
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
                               value="{{ old($field->field_name, $currentValue ? (is_string($currentValue) ? $currentValue : $currentValue) : '') }}"
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
                        <div class="space-y-2">
                            @if($currentValue)
                            <div class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                                Current file: 
                                @if(is_array($currentValue) && isset($currentValue['name']))
                                    {{ $currentValue['name'] }}
                                @else
                                    {{ basename($currentValue) }}
                                @endif
                            </div>
                            @endif
                            <input type="file" 
                                   name="{{ $field->field_name }}" 
                                   id="{{ $field->field_name }}"
                                   {{ $field->is_required && !$currentValue ? 'required' : '' }}
                                   class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @if($field->field_help_text)
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $field->field_help_text }}</p>
                            @endif
                        </div>
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

