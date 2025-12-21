@extends('layouts.admin-minimal')

@section('title', 'Deleted Fields - ' . $form->name . ' - BMMB Digital Forms')
@section('page-title', 'Deleted Fields: ' . $form->name)
@section('page-description', 'View and manage soft-deleted form fields')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-800 dark:text-red-400">
    <div class="flex items-center">
        <i class='bx bx-error-circle mr-2 text-lg'></i>
        <span>{{ session('error') }}</span>
    </div>
</div>
@endif

<div class="mb-4 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
            <i class='bx bx-trash text-red-600 dark:text-red-400 text-xl'></i>
        </div>
        <div>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Deleted Fields: {{ $form->name }}</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400">
                Total: {{ $trashedFields->count() }} deleted field(s)
            </p>
        </div>
    </div>
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.form-builder.index', $form) }}" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-900/30 dark:hover:bg-gray-900/50 dark:text-gray-400 rounded-lg text-xs transition-colors">
            <i class='bx bx-arrow-back mr-1.5'></i>
            Back to Form Builder
        </a>
    </div>
</div>

<!-- Deleted Fields Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    @if($trashedFields->isEmpty())
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class='bx bx-trash text-2xl text-gray-400 dark:text-gray-500'></i>
            </div>
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No deleted fields</h4>
            <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">All fields are active</p>
            <a href="{{ route('admin.form-builder.index', $form) }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                <i class='bx bx-arrow-back mr-1.5'></i>
                Back to Form Builder
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
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
                            Deleted At
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($trashedFields as $field)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-xs font-medium text-gray-900 dark:text-white">
                                {{ $field->field_label }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                                {{ $field->field_name }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                {{ $field->section->section_label ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ ucfirst($field->field_type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                {{ $field->deleted_at->format('Y-m-d H:i:s') }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-500">
                                {{ $field->deleted_at->diffForHumans() }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <form action="{{ route('admin.form-builder.fields.restore', [$form, $field->id]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Are you sure you want to restore this field?')" class="inline-flex items-center justify-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 dark:bg-green-900/30 dark:hover:bg-green-900/50 dark:text-green-400 rounded-lg transition-colors">
                                        <i class='bx bx-refresh mr-1'></i>
                                        Restore
                                    </button>
                                </form>
                                <form action="{{ route('admin.form-builder.fields.force-delete', [$form, $field->id]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to permanently delete this field? This action cannot be undone!')" class="inline-flex items-center justify-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg transition-colors">
                                        <i class='bx bx-trash mr-1'></i>
                                        Delete Permanently
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection


