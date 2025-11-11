@extends('layouts.admin-minimal')

@section('title', 'Create New ' . $form->name . ' Submission - BMMB Digital Forms')
@section('page-title', 'Create New ' . $form->name . ' Submission')
@section('page-description', 'Create a new submission for ' . $form->name)

@section('content')
<div class="mb-4 flex items-center justify-end">
    <a href="{{ route('admin.submissions.index', $form->slug) }}" class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
        <i class='bx bx-arrow-back mr-1.5'></i>
        Back to List
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

<form action="{{ route('admin.submissions.store', $form->slug) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

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
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
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
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
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
                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                    <option value="under_review" {{ old('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="expired" {{ old('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
        </div>
    </div>

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

                @switch($field->field_type)
                    @case('text')
                    @case('email')
                    @case('phone')
                    @case('number')
                    @case('currency')
                        <input type="{{ $field->field_type === 'email' ? 'email' : ($field->field_type === 'number' || $field->field_type === 'currency' ? 'number' : 'text') }}" 
                               name="{{ $field->field_name }}" 
                               id="{{ $field->field_name }}"
                               value="{{ old($field->field_name) }}"
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
                                  class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old($field->field_name) }}</textarea>
                        @break

                    @case('select')
                        <select name="{{ $field->field_name }}" 
                                id="{{ $field->field_name }}"
                                {{ $field->is_required ? 'required' : '' }}
                                class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Select {{ $field->field_label }}</option>
                            @if($field->field_options && is_array($field->field_options))
                                @foreach($field->field_options as $option)
                                    <option value="{{ $option }}" {{ old($field->field_name) == $option ? 'selected' : '' }}>
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
                                           {{ old($field->field_name) == $option ? 'checked' : '' }}
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
                                           {{ is_array(old($field->field_name)) && in_array($option, old($field->field_name)) ? 'checked' : '' }}
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
                                       {{ old($field->field_name) ? 'checked' : '' }}
                                       class="mr-2 text-primary-600 focus:ring-primary-500">
                                <span class="text-xs text-gray-700 dark:text-gray-300">{{ $field->field_label }}</span>
                            </label>
                        @endif
                        @break

                    @case('date')
                        <input type="date" 
                               name="{{ $field->field_name }}" 
                               id="{{ $field->field_name }}"
                               value="{{ old($field->field_name) }}"
                               {{ $field->is_required ? 'required' : '' }}
                               class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @break

                    @case('time')
                        <input type="time" 
                               name="{{ $field->field_name }}" 
                               id="{{ $field->field_name }}"
                               value="{{ old($field->field_name) }}"
                               {{ $field->is_required ? 'required' : '' }}
                               class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @break

                    @case('datetime')
                        <input type="datetime-local" 
                               name="{{ $field->field_name }}" 
                               id="{{ $field->field_name }}"
                               value="{{ old($field->field_name) }}"
                               {{ $field->is_required ? 'required' : '' }}
                               class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @break

                    @case('file')
                        <input type="file" 
                               name="{{ $field->field_name }}" 
                               id="{{ $field->field_name }}"
                               {{ $field->is_required ? 'required' : '' }}
                               class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @if($field->field_help_text)
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $field->field_help_text }}</p>
                        @endif
                        @break

                    @default
                        <input type="text" 
                               name="{{ $field->field_name }}" 
                               id="{{ $field->field_name }}"
                               value="{{ old($field->field_name) }}"
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
        <a href="{{ route('admin.submissions.index', $form->slug) }}" 
           class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
            Cancel
        </a>
        <button type="submit" 
                class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
            <i class='bx bx-save mr-1.5'></i>
            Create Submission
        </button>
    </div>
</form>
@endsection

