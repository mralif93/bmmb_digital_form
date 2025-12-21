@extends('layouts.admin-minimal')

@section('title', 'Edit Branch - BMMB Digital Forms')
@section('page-title', 'Edit Branch: ' . $branch->branch_name)
@section('page-description', 'Update branch information')

@section('content')
    <div class="mb-4 flex items-center justify-end">
        <a href="{{ route('admin.branches.show', $branch->id) }}"
            class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
            <i class='bx bx-arrow-back mr-1.5'></i>
            Back to View
        </a>
    </div>

    <div class="w-full">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Branch Information</h3>
            </div>

            <form action="{{ route('admin.branches.update', $branch->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <!-- Branch Name -->
                    <div>
                        <label for="branch_name" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Branch: <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="branch_name" id="branch_name"
                            value="{{ old('branch_name', $branch->branch_name) }}" required placeholder="e.g., ALAM DAMAI"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('branch_name') border-red-500 @enderror">
                        @error('branch_name')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Weekend Start Day -->
                    <div>
                        <label for="weekend_start_day"
                            class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Weekend Start Day: <span class="text-red-500">*</span>
                        </label>
                        <select name="weekend_start_day" id="weekend_start_day" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('weekend_start_day') border-red-500 @enderror">
                            <option value="">Select weekend start day</option>
                            <option value="MONDAY" {{ old('weekend_start_day', $branch->weekend_start_day) == 'MONDAY' ? 'selected' : '' }}>MONDAY</option>
                            <option value="TUESDAY" {{ old('weekend_start_day', $branch->weekend_start_day) == 'TUESDAY' ? 'selected' : '' }}>TUESDAY</option>
                            <option value="WEDNESDAY" {{ old('weekend_start_day', $branch->weekend_start_day) == 'WEDNESDAY' ? 'selected' : '' }}>WEDNESDAY</option>
                            <option value="THURSDAY" {{ old('weekend_start_day', $branch->weekend_start_day) == 'THURSDAY' ? 'selected' : '' }}>THURSDAY</option>
                            <option value="FRIDAY" {{ old('weekend_start_day', $branch->weekend_start_day) == 'FRIDAY' ? 'selected' : '' }}>FRIDAY</option>
                            <option value="SATURDAY" {{ old('weekend_start_day', $branch->weekend_start_day) == 'SATURDAY' ? 'selected' : '' }}>SATURDAY</option>
                            <option value="SUNDAY" {{ old('weekend_start_day', $branch->weekend_start_day) == 'SUNDAY' ? 'selected' : '' }}>SUNDAY</option>
                        </select>
                        @error('weekend_start_day')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- TI Agent Code -->
                    <div>
                        <label for="ti_agent_code" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            TI Agent Code: <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="ti_agent_code" id="ti_agent_code"
                            value="{{ old('ti_agent_code', $branch->ti_agent_code) }}" required placeholder="e.g., FN12984"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('ti_agent_code') border-red-500 @enderror">
                        @error('ti_agent_code')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Address: <span class="text-red-500">*</span>
                        </label>
                        <textarea name="address" id="address" rows="4" required placeholder="Enter full address"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-y @error('address') border-red-500 @enderror">{{ old('address', $branch->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Email: <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $branch->email) }}" required
                            placeholder="e.g., sgar@muamalat.com.my"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- State -->
                    <div>
                        <label for="state_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            State: <span class="text-red-500">*</span>
                        </label>
                        <select name="state_id" id="state_id" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('state_id') border-red-500 @enderror">
                            <option value="">Select State</option>
                            @foreach(\App\Models\State::orderBy('name')->get() as $state)
                                <option value="{{ $state->id }}" {{ old('state_id', $branch->state_id) == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                            @endforeach
                        </select>
                        @error('state_id')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Region -->
                    <div>
                        <label for="region_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Region: <span class="text-red-500">*</span>
                        </label>
                        <select name="region_id" id="region_id" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('region_id') border-red-500 @enderror">
                            <option value="">Select Region</option>
                            @foreach(\App\Models\Region::orderBy('name')->get() as $region)
                                <option value="{{ $region->id }}" {{ old('region_id', $branch->region_id) == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                        @error('region_id')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('admin.branches.show', $branch->id) }}"
                        class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-lg transition-colors">
                        Update Branch
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection