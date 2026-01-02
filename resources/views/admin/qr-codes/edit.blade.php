@extends('layouts.admin-minimal')

@section('title', 'Edit QR Code - BMMB Digital Forms')
@section('page-title', 'Edit QR Code: ' . $qrCode->name)
@section('page-description', 'Update QR code information')

@section('content')
    <div class="mb-4 flex items-center justify-end">
        <a href="{{ route('admin.qr-codes.show', $qrCode->id) }}"
            class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
            <i class='bx bx-arrow-back mr-1.5'></i>
            Back to Details
        </a>
    </div>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">QR Code Information</h3>
            </div>

            <form action="{{ route('admin.qr-codes.update', $qrCode->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Name: <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $qrCode->name) }}" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Type: <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('type') border-red-500 @enderror">
                            <option value="branch" {{ old('type', $qrCode->type) == 'branch' ? 'selected' : '' }}>Branch
                            </option>
                            <option value="url" {{ old('type', $qrCode->type) == 'url' ? 'selected' : '' }}>URL</option>
                            <option value="text" {{ old('type', $qrCode->type) == 'text' ? 'selected' : '' }}>Text</option>
                            <option value="phone" {{ old('type', $qrCode->type) == 'phone' ? 'selected' : '' }}>Phone</option>
                            <option value="email" {{ old('type', $qrCode->type) == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="sms" {{ old('type', $qrCode->type) == 'sms' ? 'selected' : '' }}>SMS</option>
                            <option value="wifi" {{ old('type', $qrCode->type) == 'wifi' ? 'selected' : '' }}>WiFi</option>
                            <option value="vcard" {{ old('type', $qrCode->type) == 'vcard' ? 'selected' : '' }}>vCard</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Content -->
                    <div>
                        <label for="content" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Content: <span class="text-red-500">*</span>
                        </label>
                        <textarea name="content" id="content" rows="4" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-y @error('content') border-red-500 @enderror">{{ old('content', $qrCode->content) }}</textarea>
                        @error('content')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p id="branch-url-help" class="mt-1 text-xs text-blue-600 dark:text-blue-400 hidden">
                            The secure URL for this branch is generated automatically on save.
                        </p>
                    </div>

                    <!-- Branch -->
                    <div>
                        <label for="branch_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Branch: <span class="text-gray-500">(Optional)</span>
                        </label>
                        <select name="branch_id" id="branch_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('branch_id') border-red-500 @enderror">
                            <option value="">Select branch (optional)</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ old('branch_id', $qrCode->branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->branch_name }} ({{ $branch->ti_agent_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Status: <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('status') border-red-500 @enderror">
                            <option value="active" {{ old('status', $qrCode->status) == 'active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="inactive" {{ old('status', $qrCode->status) == 'inactive' ? 'selected' : '' }}>
                                Inactive</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Size and Format -->
                    <div>
                        <label for="size" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Size: <span class="text-gray-500">(px)</span>
                        </label>
                        <input type="number" name="size" id="size" value="{{ old('size', $qrCode->size) }}" min="100"
                            max="1000"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('size') border-red-500 @enderror">
                        @error('size')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <!-- Format hidden input (forced to SVG) -->
                        <input type="hidden" name="format" value="svg">
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="{{ route('admin.qr-codes.show', $qrCode->id) }}"
                            class="px-4 py-2 text-xs font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-lg transition-colors">
                            Update QR Code
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- QR Code Preview -->
        <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Live Preview</h3>
            <div class="flex flex-col items-center">
                <div id="qrcode-preview"
                    class="w-64 h-64 border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white flex items-center justify-center">
                    <p class="text-xs text-gray-400 dark:text-gray-500">Enter content to see preview</p>
                </div>
                <p class="mt-4 text-xs text-gray-600 dark:text-gray-400 text-center">Preview updates automatically as you
                    type</p>
            </div>
        </div>
    </div>

    @push('scripts')
        <!-- qrjs2 CDN - Simpler QR code generation -->
        <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const typeSelect = document.getElementById('type');
                const contentInput = document.getElementById('content');
                const branchSelect = document.getElementById('branch_id');
                const previewDiv = document.getElementById('qrcode-preview');
                let qrcode = null;

                const helpMessage = document.getElementById('branch-url-help');

                function handleTypeChange() {
                    const type = typeSelect ? typeSelect.value : '';
                    const isBranch = type === 'branch';

                    // Check if content is already a secure URL (contains token=)
                    const isSecureUrl = contentInput.value.includes('token=');

                    if (isBranch) {
                        contentInput.readOnly = true;
                        contentInput.classList.add('bg-gray-100', 'text-gray-500', 'cursor-not-allowed');
                        contentInput.classList.remove('bg-white', 'text-gray-900');
                        helpMessage.classList.remove('hidden');

                        // If empty, set placeholder
                        if (!contentInput.value) {
                            contentInput.value = 'System will generate secure URL automatically';
                        }
                    } else {
                        if (contentInput.value === 'System will generate secure URL automatically') {
                            contentInput.value = '';
                        }
                        contentInput.readOnly = false;
                        contentInput.classList.remove('bg-gray-100', 'text-gray-500', 'cursor-not-allowed');
                        contentInput.classList.add('bg-white', 'text-gray-900');
                        helpMessage.classList.add('hidden');
                    }
                    updatePreview();
                }

                function generateQrContent(type, content, branchId) {
                    if (!content && type !== 'branch') return '';

                    type = type.toLowerCase();
                    switch (type) {
                        case 'branch':
                            if (branchId) {
                                const branchOption = branchSelect.options[branchSelect.selectedIndex];
                                if (branchOption && branchOption.value) {
                                    const branchCode = branchOption.textContent.match(/\(([^)]+)\)/)?.[1];
                                    if (branchCode) {
                                        // If content already has a URL, use it (for edit view)
                                        if (content && content.includes('http')) {
                                            return content;
                                        }
                                        return window.location.origin + '/branch/' + branchCode + '?token=GENERATED_ON_SAVE';
                                    }
                                }
                            }
                            if (content) {
                                return content;
                            }
                            return '';
                        case 'phone':
                            return 'tel:' + content;
                        case 'email':
                            return 'mailto:' + content;
                        case 'sms':
                            return 'sms:' + content;
                        case 'wifi':
                            return 'WIFI:T:' + content + ';;';
                        default:
                            return content || '';
                    }
                }

                function updatePreview() {
                    const type = typeSelect ? typeSelect.value : '';
                    const content = contentInput ? contentInput.value : '';
                    const branchId = branchSelect ? branchSelect.value : '';

                    if (!type) {
                        previewDiv.innerHTML = '<p class="text-xs text-gray-400 dark:text-gray-500">Select a type to see preview</p>';
                        return;
                    }

                    if (type === 'branch' && !branchId && !content) {
                        previewDiv.innerHTML = '<p class="text-xs text-gray-400 dark:text-gray-500">Select a branch to see preview</p>';
                        return;
                    }

                    if (type !== 'branch' && !content) {
                        previewDiv.innerHTML = '<p class="text-xs text-gray-400 dark:text-gray-500">Enter content to see preview</p>';
                        return;
                    }

                    const qrContent = generateQrContent(type, content, branchId);

                    if (!qrContent) {
                        previewDiv.innerHTML = '<p class="text-xs text-gray-400 dark:text-gray-500">Enter content to see preview</p>';
                        return;
                    }

                    // Clear previous QR code
                    previewDiv.innerHTML = '';

                    // Generate new QR code directly in the div
                    try {
                        qrcode = new QRCode(previewDiv, {
                            text: qrContent,
                            width: 256,
                            height: 256,
                            colorDark: '#000000',
                            colorLight: '#ffffff',
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    } catch (error) {
                        previewDiv.innerHTML = '<p class="text-xs text-red-500">Error generating preview</p>';
                        console.error('QR generation error:', error);
                    }
                }

                // Add event listeners
                if (typeSelect) {
                    typeSelect.addEventListener('change', handleTypeChange);
                    typeSelect.addEventListener('change', updatePreview);
                }
                if (contentInput) {
                    contentInput.addEventListener('input', updatePreview);
                }
                if (branchSelect) {
                    branchSelect.addEventListener('change', updatePreview);
                }

                // Initial preview
                handleTypeChange();
                setTimeout(updatePreview, 300);
            });
        </script>
    @endpush
@endsection
```