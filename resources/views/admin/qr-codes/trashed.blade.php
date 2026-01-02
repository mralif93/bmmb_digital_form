@extends('layouts.admin-minimal')

@section('title', 'Trashed QR Codes - BMMB Digital Forms')
@section('page-title', 'Trashed QR Codes')
@section('page-description', 'View and manage deleted QR codes')

@section('content')
    @if(session('success'))
        <div
            class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-trash text-red-600 dark:text-red-400 text-xl'></i>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Trashed QR Codes</h2>
                <p class="text-xs text-gray-600 dark:text-gray-400">Total: {{ $qrCodes->total() }} records</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.qr-codes.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-xs font-semibold rounded-lg transition-colors">
                <i class='bx bx-arrow-back mr-1.5'></i>
                Back to List
            </a>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="mb-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.qr-codes.trashed') }}" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <!-- Search Input -->
                <div class="md:col-span-2">
                    <label for="search"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <div class="relative">
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Search by name, content, or branch..."
                            class="w-full pl-10 pr-4 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <i
                            class='bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500'></i>
                    </div>
                </div>

                <!-- Type Filter -->
                <div>
                    <label for="type" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                    <select name="type" id="type"
                        class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Types</option>
                        <option value="branch" {{ request('type') === 'branch' ? 'selected' : '' }}>Branch</option>
                        <option value="url" {{ request('type') === 'url' ? 'selected' : '' }}>URL</option>
                        <option value="text" {{ request('type') === 'text' ? 'selected' : '' }}>Text</option>
                        <option value="phone" {{ request('type') === 'phone' ? 'selected' : '' }}>Phone</option>
                        <option value="email" {{ request('type') === 'email' ? 'selected' : '' }}>Email</option>
                        <option value="sms" {{ request('type') === 'sms' ? 'selected' : '' }}>SMS</option>
                        <option value="wifi" {{ request('type') === 'wifi' ? 'selected' : '' }}>WiFi</option>
                        <option value="vcard" {{ request('type') === 'vcard' ? 'selected' : '' }}>vCard</option>
                    </select>
                </div>

                <!-- Branch Filter -->
                <div>
                    <label for="branch_id"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                    <select name="branch_id" id="branch_id"
                        class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->branch_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-2">
                @if(request()->hasAny(['search', 'type', 'branch_id']))
                    <a href="{{ route('admin.qr-codes.trashed') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-xs font-semibold rounded-lg transition-colors">
                        <i class='bx bx-x mr-1.5'></i>
                        Clear Filters
                    </a>
                @endif
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    <i class='bx bx-search mr-1.5'></i>
                    Search
                </button>
            </div>
        </form>
    </div>

    <!-- QR Codes Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Name
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Type
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Branch
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Deleted At
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($qrCodes as $qrCode)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-xs font-semibold text-gray-900 dark:text-white">
                                    {{ $qrCode->name }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ $qrCode->type_display }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $qrCode->branch ? $qrCode->branch->branch_name : 'N/A' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $statusColor = $qrCode->status === 'active'
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                        : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusColor }}">
                                    {{ $qrCode->status_display }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-xs text-gray-900 dark:text-white">
                                    {{ $qrCode->deleted_at->format('Y-m-d H:i') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <form action="{{ route('admin.qr-codes.restore', $qrCode->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to restore this QR code?');">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center justify-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 dark:bg-green-900/30 dark:hover:bg-green-900/50 dark:text-green-400 rounded-lg text-xs transition-colors">
                                            Restore
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.qr-codes.force-delete', $qrCode->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Are you sure you want to permanently delete this QR code? This action cannot be undone.');">
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
                            <td colspan="6" class="px-4 py-8 text-center">
                                <div
                                    class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class='bx bx-trash text-2xl text-gray-400 dark:text-gray-500'></i>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No trashed QR codes found
                                </h4>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($qrCodes->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                {{ $qrCodes->links() }}
            </div>
        @endif
    </div>
@endsection