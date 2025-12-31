@extends('layouts.admin-minimal')

@section('title', 'View QR Code - BMMB Digital Forms')
@section('page-title', 'QR Code Details: ' . $qrCode->name)
@section('page-description', 'View QR code information')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div></div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.qr-codes.index') }}"
                class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                <i class='bx bx-arrow-back mr-1.5'></i>
                Back to List
            </a>
            <a href="{{ route('admin.qr-codes.edit', $qrCode->id) }}"
                class="inline-flex items-center px-4 py-2 text-xs font-semibold bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors">
                <i class='bx bx-edit mr-1.5'></i>
                Edit QR Code
            </a>
            <button onclick="deleteQrCode({{ $qrCode->id }})"
                class="inline-flex items-center px-4 py-2 text-xs font-semibold bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                <i class='bx bx-trash mr-1.5'></i>
                Delete QR Code
            </button>
        </div>
    </div>

    @if(session('success'))
        <div
            class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- QR Code Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class='bx bx-info-circle mr-2 text-orange-600 dark:text-orange-400'></i>
                    QR Code Information
                </h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Name
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white font-semibold">
                            {{ $qrCode->name }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Type
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                {{ $qrCode->type_display }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Status
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            @php
                                $statusColor = $qrCode->status === 'active'
                                    ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                    : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                            @endphp
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusColor }}">
                                {{ $qrCode->status_display }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Branch
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            {{ $qrCode->branch ? $qrCode->branch->branch_name : 'N/A' }}
                        </dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Content
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white break-all">
                            {{ $qrCode->content }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Size
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            {{ $qrCode->size }}px
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Format
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            {{ strtoupper($qrCode->format) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Created At
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            {{ $timezoneHelper->convert($qrCode->created_at)?->format($dateFormat . ' ' . $timeFormat) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Updated At
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            {{ $timezoneHelper->convert($qrCode->updated_at)?->format($dateFormat . ' ' . $timeFormat) }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- QR Code Preview -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">QR Code Preview</h3>
                <div class="flex flex-col items-center">
                    <div id="qrcode-display"
                        class="w-64 h-64 border border-gray-300 rounded-lg p-4 bg-white mb-4 flex items-center justify-center">
                    </div>

                    <button onclick="downloadQrCode('{{ $qrCode->name }}')"
                        class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                        <i class='bx bx-download mr-1.5'></i>
                        Download QR Code
                    </button>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.qr-codes.edit', $qrCode->id) }}"
                        class="block w-full text-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                        <i class='bx bx-edit mr-1.5'></i>
                        Edit QR Code
                    </a>
                    <button onclick="deleteQrCode({{ $qrCode->id }})"
                        class="block w-full text-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors">
                        <i class='bx bx-trash mr-1.5'></i>
                        Delete QR Code
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Generate QR code on load
                const qrDiv = document.getElementById('qrcode-display');
                if (qrDiv) {
                    try {
                        new QRCode(qrDiv, {
                            text: "{{ $qrCode->content }}",
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

            function downloadQrCode(filename) {
                const qrDiv = document.getElementById('qrcode-display');
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