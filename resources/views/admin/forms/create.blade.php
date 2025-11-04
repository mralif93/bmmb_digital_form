@extends('layouts.admin-minimal')

@section('title', 'Create ' . $config['title'] . ' - BMMB Digital Forms')
@section('page-title', 'Create ' . $config['title'])
@section('page-description', 'Create a new ' . $config['title'] . ' record')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.forms.index', $type) }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
        <i class='bx bx-arrow-back mr-1.5'></i>
        Back to List
    </a>
</div>

<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                    <i class='bx {{ $config['icon'] }} text-primary-600 dark:text-primary-400 text-xl'></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Create New {{ $config['title'] }}</h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">A unique {{ $config['number_prefix'] }} number will be generated automatically</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.forms.store', $type) }}" method="POST" class="p-6">
            @csrf
            
            <div class="mb-4">
                <label for="user_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    User * <span class="text-red-500">*</span>
                </label>
                <select name="user_id" id="user_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('user_id') border-red-500 @enderror">
                    <option value="">Select a user</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('admin.forms.index', $type) }}" class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition-colors">
                    Create {{ $config['title'] }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

