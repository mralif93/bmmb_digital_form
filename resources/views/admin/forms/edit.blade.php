@extends('layouts.admin-minimal')

@section('title', 'Edit Form - BMMB Digital Forms')
@section('page-title', 'Edit Form')
@section('page-description', 'Update form details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.forms.index') }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
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

