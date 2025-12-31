@extends('layouts.admin-minimal')

@section('title', 'View Form Section - BMMB Digital Forms')
@section('page-title', 'Section Details')
@section('page-description', 'View section information')

@section('content')
    <div class="mb-4 flex items-center justify-end">
        <a href="{{ route('admin.form-sections.index', $form) }}"
            class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
            <i class='bx bx-arrow-back mr-1.5'></i>
            Back to Sections
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Section Key</label>
                <p class="text-xs font-mono text-gray-900 dark:text-white">{{ $section->section_key }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Section Label</label>
                <p class="text-xs text-gray-900 dark:text-white">{{ $section->section_label }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $section->section_description ?? 'No description' }}
                </p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
                <p class="text-xs text-gray-900 dark:text-white">{{ $section->sort_order }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <span
                    class="px-2 py-1 text-xs font-medium rounded-lg {{ $section->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    {{ $section->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Created At</label>
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    {{ $timezoneHelper->convert($section->created_at)?->format($dateFormat . ' ' . $timeFormat) }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Updated At</label>
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    {{ $timezoneHelper->convert($section->updated_at)?->format($dateFormat . ' ' . $timeFormat) }}</p>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end space-x-3">
            <a href="{{ route('admin.form-sections.edit', [$form, $section]) }}"
                class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                Edit Section
            </a>
        </div>
    </div>
@endsection