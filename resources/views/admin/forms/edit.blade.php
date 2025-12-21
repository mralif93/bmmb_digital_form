@extends('layouts.admin-minimal')

@section('title', 'Edit Form - BMMB Digital Forms')
@section('page-title', 'Edit Form')
@section('page-description', 'Update form details')

@section('content')
@php
    $formSettings = $form->settings ?? [];
@endphp
<div class="mb-4 flex items-center justify-end">
    <a href="{{ route('admin.forms.index') }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
        <i class='bx bx-arrow-back mr-1.5'></i>
        Back to Forms
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <form action="{{ route('admin.forms.update', $form) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <!-- Form Name -->
            <div>
                <label for="name" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Form Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $form->name) }}" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                       placeholder="e.g., Customer Feedback Form">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">The display name for this form</p>
                @error('name')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Slug
                </label>
                <input type="text" name="slug" id="slug" value="{{ old('slug', $form->slug) }}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent font-mono"
                       placeholder="e.g., customer-feedback-form">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">URL-friendly identifier (auto-generated if left empty)</p>
                @error('slug')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Description
                </label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                          placeholder="Brief description of what this form is used for">{{ old('description', $form->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status" id="status" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}" {{ old('status', $form->status) == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submission Limit -->
            <div>
                <label for="submission_limit" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Submission Limit
                </label>
                <input type="number" name="submission_limit" id="submission_limit" value="{{ old('submission_limit', $form->submission_limit) }}" min="1"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                       placeholder="Leave empty for unlimited">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum number of submissions allowed per user (leave empty for unlimited)</p>
                @error('submission_limit')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Checkboxes -->
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_public" value="1" {{ old('is_public', $form->is_public) ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500">
                    <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Public Form (accessible to all users)</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="allow_multiple_submissions" value="1" {{ old('allow_multiple_submissions', $form->allow_multiple_submissions) ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500">
                    <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Allow Multiple Submissions</span>
                </label>
                <label class="flex items-start">
                    <input type="checkbox" name="show_important_note" value="1" {{ old('show_important_note', $formSettings['show_important_note'] ?? false) ? 'checked' : '' }}
                           class="mt-0.5 rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500">
                    <span class="ml-2 text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                        Show Important Note banner before the first section on the public form
                    </span>
                </label>
            </div>

            <div class="mt-6 space-y-3 border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900/30">
                <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 uppercase">Important Note Content</p>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Note Title</label>
                    <input type="text" name="important_note_title" value="{{ old('important_note_title', $formSettings['important_note_title'] ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                           placeholder="Important Note">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Intro Text</label>
                    <input type="text" name="important_note_intro" value="{{ old('important_note_intro', $formSettings['important_note_intro'] ?? '') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                           placeholder="e.g., This form is made to Bank Muamalat Malaysia Berhad">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Bullet Items (one per line)</label>
                    @php
                        $noteItemsValue = $formSettings['important_note_items'] ?? [];
                        if (is_array($noteItemsValue)) {
                            $noteItemsValue = implode("\n", $noteItemsValue);
                        }
                    @endphp
                    <textarea name="important_note_items" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                              placeholder="Enter each bullet item on a new line">{{ old('important_note_items', $noteItemsValue) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Contact Details</label>
                    <textarea name="important_note_contact" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                              placeholder="Contact information displayed below the note">{{ old('important_note_contact', $formSettings['important_note_contact'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.forms.index') }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                Update Form
            </button>
        </div>
    </form>
</div>
@endsection

