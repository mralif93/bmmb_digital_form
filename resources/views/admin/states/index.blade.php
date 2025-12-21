@extends('layouts.admin-minimal')

@section('title', 'States Management - BMMB Digital Forms')
@section('page-title', 'States Management')
@section('page-description', 'Manage all state records')

@section('content')
    @if(session('success'))
        <div
            class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div
            class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-800 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-map text-purple-600 dark:text-purple-400 text-xl'></i>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">States Management</h2>
                <p class="text-xs text-gray-600 dark:text-gray-400">Total: {{ $states->total() }} records</p>
            </div>
        </div>
        <a href="{{ route('admin.states.create') }}"
            class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg transition-colors">
            <i class='bx bx-plus mr-1.5'></i>
            Create New
        </a>
    </div>

    <!-- Search -->
    <div class="mb-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.states.index') }}" class="flex items-center space-x-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..."
                    class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500">
            </div>
            <button type="submit"
                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-lg">
                <i class='bx bx-search mr-1'></i> Search
            </button>
            @if(request('search'))
                <a href="{{ route('admin.states.index') }}"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-lg">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">Name
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                        Branches</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($states as $state)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">{{ $state->id }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900 dark:text-white">{{ $state->name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">{{ $state->branches_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.states.show', $state) }}"
                                    class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-lg text-xs">View</a>
                                <a href="{{ route('admin.states.edit', $state) }}"
                                    class="px-3 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded-lg text-xs">Edit</a>
                                @if($state->branches_count == 0)
                                    <form action="{{ route('admin.states.destroy', $state) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Delete this state?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-lg text-xs">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-xs text-gray-500 dark:text-gray-400">No states found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($states->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $states->links() }}
            </div>
        @endif
    </div>
@endsection