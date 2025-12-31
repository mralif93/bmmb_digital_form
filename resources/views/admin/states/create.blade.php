@extends('layouts.admin-minimal')

@section('title', 'Create State - BMMB Digital Forms')
@section('page-title', 'Create New State')

@section('content')
    <div class="mb-4 flex justify-end">
        <a href="{{ route('admin.states.index') }}"
            class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg">
            <i class='bx bx-arrow-back mr-1.5'></i> Back
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">State Information</h3>
        </div>
        <form action="{{ route('admin.states.store') }}" method="POST" class="p-6">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">Name <span
                        class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end space-x-3">
                <a href="{{ route('admin.states.index') }}"
                    class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">Cancel</a>
                <button type="submit"
                    class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-lg">Create
                    State</button>
            </div>
        </form>
    </div>
@endsection