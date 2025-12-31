@extends('layouts.admin-minimal')

@section('title', 'Trashed States - BMMB Digital Forms')
@section('page-title', 'Trashed States')
@section('page-description', 'View and manage deleted states')

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
            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-trash text-red-600 dark:text-red-400 text-xl'></i>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Trashed States</h2>
                <p class="text-xs text-gray-600 dark:text-gray-400">Total: {{ $states->total() }} records</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.states.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-xs font-semibold rounded-lg transition-colors">
                <i class='bx bx-arrow-back mr-1.5'></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="mb-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.states.trashed') }}" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="md:col-span-4">
                    <label for="search"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <div class="relative">
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Search by name..."
                            class="w-full pl-10 pr-4 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <i
                            class='bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500'></i>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-2">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    <i class='bx bx-search mr-1.5'></i>
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.states.trashed') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-xs font-semibold rounded-lg transition-colors">
                        <i class='bx bx-x mr-1.5'></i>
                        Clear
                    </a>
                @endif
            </div>
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
                        Deleted At</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
                        Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($states as $state)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">{{ $state->id }}</td>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-900 dark:text-white">{{ $state->name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">
                            {{ $state->deleted_at ? $state->deleted_at->format($dateFormat . ' ' . $timeFormat) : '-' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <form action="{{ route('admin.states.restore', $state->id) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('Are you sure you want to restore this state?');">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center justify-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 dark:bg-green-900/30 dark:hover:bg-green-900/50 dark:text-green-400 rounded-lg text-xs transition-colors">
                                        Restore
                                    </button>
                                </form>
                                <form action="{{ route('admin.states.force-delete', $state->id) }}" method="POST"
                                    class="inline-block"
                                    onsubmit="return confirm('Are you sure you want to permanently delete this state? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center justify-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg text-xs transition-colors">
                                        Delete Permanently
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-xs text-gray-500 dark:text-gray-400">No trashed states
                            found
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