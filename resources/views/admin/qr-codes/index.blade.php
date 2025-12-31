@extends('layouts.admin-minimal')

@section('title', 'QR Codes Management - BMMB Digital Forms')
@section('page-title', 'QR Codes Management')
@section('page-description', 'Manage all QR code records')

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
            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-qr-scan text-orange-600 dark:text-orange-400 text-xl'></i>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">QR Codes Management</h2>
                <p class="text-xs text-gray-600 dark:text-gray-400">Total: {{ $qrCodes->total() }} records</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            @if(isset($showTrashed) && $showTrashed)
                <a href="{{ route('admin.qr-codes.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-xs font-semibold rounded-lg transition-colors">
                    <i class='bx bx-arrow-back mr-1.5'></i>
                    Back to List
                </a>
            @else
                <a href="{{ route('admin.qr-codes.index', ['trashed' => 'true']) }}"
                    class="inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 text-xs font-semibold rounded-lg transition-colors">
                    <i class='bx bx-trash mr-1.5'></i>
                    Trashed QR Codes
                </a>
            @endif
            <form id="regenerate-all-form" action="{{ route('admin.qr-codes.regenerate-all') }}" method="POST"
                class="inline">
                @csrf
                <button type="button" onclick="confirmRegenerateAll()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    <i class='bx bx-refresh mr-1.5'></i>
                    Regenerate All
                </button>
            </form>
            <a href="{{ route('admin.qr-codes.create') }}"
                class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                <i class='bx bx-plus mr-1.5'></i>
                Create New
            </a>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="mb-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.qr-codes.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
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

                <!-- Status Filter -->
                <div>
                    <label for="status"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" id="status"
                        class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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

            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <!-- Expired Filter -->
                <div>
                    <label for="expired"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Expiration</label>
                    <select name="expired" id="expired"
                        class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All</option>
                        <option value="yes" {{ request('expired') === 'yes' ? 'selected' : '' }}>Expired</option>
                        <option value="no" {{ request('expired') === 'no' ? 'selected' : '' }}>Not Expired</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="md:col-span-4 flex items-end justify-end space-x-2">
                    @if(request()->hasAny(['search', 'type', 'status', 'branch_id', 'expired']))
                        <a href="{{ route('admin.qr-codes.index') }}"
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
                            Created
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Last Regenerated
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
                                    {{ $timezoneHelper->convert($qrCode->created_at)?->format($dateFormat) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $timezoneHelper->convert($qrCode->created_at)?->format($timeFormat) }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($qrCode->last_regenerated_at)
                                    <div class="text-xs text-gray-900 dark:text-white">
                                        {{ $timezoneHelper->convert($qrCode->last_regenerated_at)?->format($dateFormat) }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $timezoneHelper->convert($qrCode->last_regenerated_at)?->format($timeFormat) }}
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400 dark:text-gray-500 italic">
                                        Never
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    @if(isset($showTrashed) && $showTrashed)
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
                                    @else
                                        @if($qrCode->status === 'active')
                                            <button
                                                onclick="showQrCodePopup({{ json_encode($qrCode->content) }}, {{ json_encode($qrCode->name) }})"
                                                class="inline-flex items-center justify-center px-3 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-700 dark:bg-purple-900/30 dark:hover:bg-purple-900/50 dark:text-purple-400 rounded-lg text-xs transition-colors">
                                                QR Code
                                            </button>
                                        @elseif($qrCode->status !== 'active')
                                            <button onclick="showInactiveQrCodeError()"
                                                class="inline-flex items-center justify-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-500 dark:bg-gray-900/30 dark:hover:bg-gray-900/50 dark:text-gray-400 rounded-lg text-xs transition-colors cursor-not-allowed"
                                                title="QR Code is inactive">
                                                QR Code
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.qr-codes.show', $qrCode->id) }}"
                                            class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 rounded-lg text-xs transition-colors">
                                            View
                                        </a>
                                        <a href="{{ route('admin.qr-codes.edit', $qrCode->id) }}"
                                            class="inline-flex items-center justify-center px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 dark:text-orange-400 rounded-lg text-xs transition-colors">
                                            Edit
                                        </a>
                                        <button onclick="deleteQrCode({{ $qrCode->id }})"
                                            class="inline-flex items-center justify-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg text-xs transition-colors">
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center">
                                <div
                                    class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class='bx bx-qr-scan text-2xl text-gray-400 dark:text-gray-500'></i>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No QR codes found</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">Get started by creating your first QR
                                    code</p>
                                <a href="{{ route('admin.qr-codes.create') }}"
                                    class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                    <i class='bx bx-plus mr-1.5'></i>
                                    Create First QR Code
                                </a>
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

    @push('scripts')
        <!-- qrjs2 CDN -->
        <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
        <script>
            function showQrCodePopup(qrContent, qrCodeName) {
                Swal.fire({
                    title: qrCodeName,
                    html: `
                                                    <div class="flex flex-col items-center">
                                                        <div id="qrcode-popup" class="w-64 h-64 border border-gray-300 rounded-lg p-4 bg-white mb-4 flex items-center justify-center"></div>
                                                        <button onclick="downloadQrCode('${qrCodeName}')" 
                                                           class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-semibold rounded-lg transition-colors">
                                                            <i class='bx bx-download mr-2'></i>
                                                            Download QR Code
                                                        </button>
                                                    </div>
                                                `,
                    width: '500px',
                    showCloseButton: true,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'rounded-lg',
                        htmlContainer: 'text-center'
                    },
                    didOpen: () => {
                        // Generate QR code after modal opens
                        const qrDiv = document.getElementById('qrcode-popup');
                        qrDiv.innerHTML = ''; // Clear any previous content
                        try {
                            new QRCode(qrDiv, {
                                text: qrContent,
                                width: 256,
                                height: 256,
                                colorDark: '#000000',
                                colorLight: '#ffffff',
                                correctLevel: QRCode.CorrectLevel.H
                            });
                        } catch (error) {
                            qrDiv.innerHTML = '<p class="text-xs text-red-500">Error generating QR code</p>';
                            console.error('QR generation error:', error);
                        }
                    }
                });
            }

            function showInactiveQrCodeError() {
                Swal.fire({
                    icon: 'error',
                    title: 'QR Code Inactive',
                    text: 'This QR code is not active. Please activate it before accessing or displaying it.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ea580c',
                    customClass: {
                        popup: 'rounded-lg'
                    }
                });
            }

            function confirmRegenerateAll() {
                Swal.fire({
                    title: 'Regenerate All QR Codes?',
                    html: `
                                                    <div class="text-center">
                                                        <p class="mb-2">Are you sure you want to regenerate all active QR codes?</p>
                                                        <p class="text-sm text-gray-600 mb-2">This will:</p>
                                                        <ul class="text-sm text-gray-600 list-disc list-inside mt-2 inline-block text-left">
                                                            <li>Update all QR code images</li>
                                                            <li>Generate new validation tokens</li>
                                                            <li>Invalidate old QR codes</li>
                                                        </ul>
                                                    </div>
                                                `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Regenerate All',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280',
                    customClass: {
                        popup: 'rounded-lg',
                        htmlContainer: 'text-center',
                        confirmButton: 'rounded-lg',
                        cancelButton: 'rounded-lg'
                    },
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('regenerate-all-form').submit();
                    }
                });
            }

            function downloadQrCode(filename) {
                const qrDiv = document.getElementById('qrcode-popup');
                if (!qrDiv) return;

                // Try to simple find the image first (qrcodejs usually generates an img)
                let imgParams = qrDiv.querySelector('img');

                // If no img, check for canvas
                if (!imgParams) {
                    const canvas = qrDiv.querySelector('canvas');
                    if (canvas) {
                        try {
                            const dataUrl = canvas.toDataURL('image/png');
                            const link = document.createElement('a');
                            link.href = dataUrl;
                            link.download = (filename || 'qrcode') + '.png';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            return;
                        } catch (e) {
                            console.error('Error converting canvas to image:', e);
                        }
                    }
                }

                if (imgParams && imgParams.src) {
                    const link = document.createElement('a');
                    link.href = imgParams.src;
                    link.download = (filename || 'qrcode') + '.png';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    // If still nothing, it might be because the image hasn't loaded yet? 
                    // qrcodejs is usually synchronous for canvas but async for img src generation sometimes.
                    // But here it was generated in didOpen, so it should be ready by the time user clicks download.
                    console.error('No QR code image found to download');
                    Swal.fire({
                        icon: 'error',
                        title: 'Download Failed',
                        text: 'Could not find the QR code image to download.',
                        confirmButtonColor: '#ea580c'
                    });
                }
            }

            function deleteQrCode(qrCodeId) {
                Swal.fire({
                    title: 'Delete QR Code?',
                    text: 'Are you sure you want to delete this QR code? This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    customClass: {
                        popup: 'rounded-lg',
                        confirmButton: 'rounded-lg',
                        cancelButton: 'rounded-lg'
                    },
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `{{ route('admin.qr-codes.destroy', ':id') }}`.replace(':id', qrCodeId);

                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = '{{ csrf_token() }}';
                        form.appendChild(csrf);

                        const method = document.createElement('input');
                        method.type = 'hidden';
                        method.name = '_method';
                        method.value = 'DELETE';
                        form.appendChild(method);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
    @endpush
@endsection