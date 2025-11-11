@extends('layouts.admin-minimal')

@section('title', 'View Form - ' . $form->name . ' - BMMB Digital Forms')
@section('page-title', 'Form Details')
@section('page-description', 'View form information and structure')

@section('content')
<div class="mb-4 flex items-center justify-end">
    <a href="{{ route('admin.forms.index') }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
        <i class='bx bx-arrow-back mr-1.5'></i>
        Back to Forms
    </a>
</div>

@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

<!-- Form Information Card -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $form->name }}</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $form->description ?? 'No description provided' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            @php
                $statusColors = [
                    'draft' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                    'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                    'inactive' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                ];
            @endphp
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$form->status] ?? $statusColors['draft'] }}">
                {{ ucfirst($form->status) }}
            </span>
            @if($form->is_public)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                    Public
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                    Private
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Slug</label>
            <p class="text-xs font-mono text-gray-900 dark:text-white">{{ $form->slug }}</p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Submission Limit</label>
            <p class="text-xs text-gray-900 dark:text-white">{{ $form->submission_limit ?? 'Unlimited' }}</p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Multiple Submissions</label>
            <p class="text-xs text-gray-900 dark:text-white">{{ $form->allow_multiple_submissions ? 'Allowed' : 'Not Allowed' }}</p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Created At</label>
            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $form->created_at->format('Y-m-d H:i:s') }}</p>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Updated At</label>
            <p class="text-xs text-gray-600 dark:text-gray-400">{{ $form->updated_at->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>

    <div class="flex items-center justify-end space-x-2 pt-4 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('admin.form-builder.index', $form) }}" class="inline-flex items-center px-3 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-700 dark:bg-purple-900/30 dark:hover:bg-purple-900/50 dark:text-purple-400 rounded-lg text-xs transition-colors">
            <i class='bx bx-code-alt mr-1'></i>
            Form Builder
        </a>
        <a href="{{ route('admin.form-sections.index', $form) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 dark:text-indigo-400 rounded-lg text-xs transition-colors">
            <i class='bx bx-list-ul mr-1'></i>
            Sections
        </a>
        <a href="{{ route('admin.forms.edit', $form) }}" class="inline-flex items-center px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 dark:text-orange-400 rounded-lg text-xs transition-colors">
            <i class='bx bx-edit mr-1'></i>
            Edit
        </a>
    </div>
</div>

<!-- Sections and Fields Overview -->
@if($form->sections->count() > 0)
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Form Structure</h3>
    
    <div class="space-y-4">
        @foreach($form->sections as $section)
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-xs font-semibold text-gray-900 dark:text-white">
                        {{ $section->section_label }}
                    </h4>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $section->fields->count() }} field(s)
                    </span>
                </div>
                @if($section->section_description)
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ $section->section_description }}</p>
                @endif
                
                @if($section->fields->count() > 0)
                    <div class="mt-2 space-y-1">
                        @foreach($section->fields as $field)
                            <div class="flex items-center justify-between px-2 py-1 bg-gray-50 dark:bg-gray-700/50 rounded text-xs">
                                <span class="text-gray-700 dark:text-gray-300">{{ $field->field_label }}</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ ucfirst($field->field_type) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400 italic">No fields in this section</p>
                @endif
            </div>
        @endforeach
    </div>
</div>
@else
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
        <i class='bx bx-layer text-2xl text-gray-400 dark:text-gray-500'></i>
    </div>
    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No sections configured</h4>
    <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">Start building your form by adding sections and fields</p>
    <div class="flex items-center justify-center space-x-2">
        <a href="{{ route('admin.form-sections.index', $form) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors">
            <i class='bx bx-list-ul mr-1.5'></i>
            Manage Sections
        </a>
        <a href="{{ route('admin.form-builder.index', $form) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg transition-colors">
            <i class='bx bx-code-alt mr-1.5'></i>
            Form Builder
        </a>
    </div>
</div>
@endif
@endsection

