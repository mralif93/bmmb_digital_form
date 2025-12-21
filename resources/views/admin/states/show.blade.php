@extends('layouts.admin-minimal')

@section('title', 'View State - BMMB Digital Forms')
@section('page-title', 'State: ' . $state->name)

@section('content')
    <div class="mb-4 flex justify-end">
        <a href="{{ route('admin.states.index') }}"
            class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg">
            <i class='bx bx-arrow-back mr-1.5'></i> Back
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">State Details</h3>
            <a href="{{ route('admin.states.edit', $state) }}"
                class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg">Edit</a>
        </div>
        <div class="p-6">
            <dl class="grid grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">ID</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">{{ $state->id }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Name</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">{{ $state->name }}</dd>
                </div>
            </dl>
        </div>
    </div>

    @if($state->branches->count() > 0)
        <div class="mt-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Branches in this State</h3>
            </div>
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($state->branches as $branch)
                    <li class="px-6 py-3 text-xs text-gray-900 dark:text-white">{{ $branch->branch_name }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection