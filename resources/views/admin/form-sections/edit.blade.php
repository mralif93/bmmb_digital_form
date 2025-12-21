@extends('layouts.admin-minimal')

@section('title', 'Edit Form Section - BMMB Digital Forms')
@section('page-title', 'Edit Section')
@section('page-description', 'Update section details')

@section('content')
<div class="mb-4 flex items-center justify-end">
    <a href="{{ route('admin.form-sections.index', $form) }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
        <i class='bx bx-arrow-back mr-1.5'></i>
        Back to Sections
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <form action="{{ route('admin.form-sections.update', [$form, $section]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <!-- Section Key -->
            <div>
                <label for="section_key" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Section Key <span class="text-red-500">*</span>
                </label>
                <input type="text" name="section_key" id="section_key" value="{{ old('section_key', $section->section_key) }}" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                       placeholder="e.g., custom_section">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Unique identifier (lowercase, underscores only)</p>
                @error('section_key')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Section Label -->
            <div>
                <label for="section_label" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Section Label <span class="text-red-500">*</span>
                </label>
                <input type="text" name="section_label" id="section_label" value="{{ old('section_label', $section->section_label) }}" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                       placeholder="e.g., Custom Section Name">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Display name shown in the form</p>
                @error('section_label')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Section Description -->
            <div>
                <label for="section_description" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Section Description
                </label>
                <textarea name="section_description" id="section_description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                          placeholder="Optional description for this section">{{ old('section_description', $section->section_description) }}</textarea>
                @error('section_description')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sort Order -->
            <div>
                <label for="sort_order" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Sort Order
                </label>
                <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $section->sort_order) }}" min="0"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
                @error('sort_order')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Active -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $section->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 text-orange-600 focus:ring-orange-500">
                    <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Active (Section will be visible in forms)</span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.form-sections.index', $form) }}" class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                Update Section
            </button>
        </div>
    </form>
</div>
@endsection


