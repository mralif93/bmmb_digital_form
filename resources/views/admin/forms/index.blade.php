@extends('layouts.admin-minimal')

@section('title', 'Form Management - BMMB Digital Forms')
@section('page-title', 'Form Management')
@section('page-description', 'Create and manage digital forms')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Form Management</h1>
            <p class="text-gray-600 dark:text-gray-400">Create, manage, and track your digital forms</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.forms.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class='bx bx-plus mr-2'></i>
                Create New Form
            </a>
            <button onclick="bulkGenerateQr()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class='bx bx-qr-scan mr-2'></i>
                Bulk Generate QR
            </button>
        </div>
    </div>

    <!-- Forms Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($forms as $form)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 hover:shadow-xl transition-all duration-300">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ $form->title }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($form->description, 80) }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $form->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                        {{ $form->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            <div class="space-y-3 mb-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Fields:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $form->fields->count() }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Submissions:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $form->submissions_count }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Created:</span>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $form->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            <!-- QR Code Section -->
            @if($form->qr_code)
            <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <i class='bx bx-qr-scan text-orange-600'></i>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">QR Code Available</span>
                    </div>
                    <a href="{{ $form->qr_code_url }}" target="_blank" class="text-orange-600 hover:text-orange-700 text-sm">
                        <i class='bx bx-download'></i>
                    </a>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex space-x-2">
                    <a href="{{ route('admin.forms.show', $form) }}" class="inline-flex items-center px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                        <i class='bx bx-show mr-1'></i>
                        View
                    </a>
                    <a href="{{ route('admin.forms.edit', $form) }}" class="inline-flex items-center px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <i class='bx bx-edit mr-1'></i>
                        Edit
                    </a>
                </div>
                <div class="flex space-x-2">
                    <button onclick="generateQr({{ $form->id }})" class="inline-flex items-center px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 transition-colors">
                        <i class='bx bx-qr-scan mr-1'></i>
                        QR
                    </button>
                    <button onclick="toggleStatus({{ $form->id }})" class="inline-flex items-center px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                        <i class='bx bx-power-off mr-1'></i>
                        Toggle
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-file-blank text-2xl text-gray-400'></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No forms created yet</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Get started by creating your first digital form</p>
                <a href="{{ route('admin.forms.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class='bx bx-plus mr-2'></i>
                    Create Your First Form
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($forms->hasPages())
    <div class="mt-6">
        {{ $forms->links() }}
    </div>
    @endif
</div>

<!-- Bulk QR Generation Modal -->
<div id="bulkQrModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Bulk QR Code Generation</h3>
                <button onclick="closeBulkQrModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
            <div class="space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Select forms to generate QR codes for:</p>
                <div class="max-h-60 overflow-y-auto space-y-2">
                    @foreach($forms as $form)
                    <label class="flex items-center space-x-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer">
                        <input type="checkbox" name="selected_forms[]" value="{{ $form->id }}" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span class="text-sm text-gray-900 dark:text-white">{{ $form->title }}</span>
                    </label>
                    @endforeach
                </div>
                <div class="flex justify-end space-x-3">
                    <button onclick="closeBulkQrModal()" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button onclick="confirmBulkGenerate()" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Generate QR Codes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateQr(formId) {
    fetch(`/admin/forms/${formId}/generate-qr`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message and reload page
            alert('QR code generated successfully!');
            location.reload();
        } else {
            alert('Error generating QR code');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error generating QR code');
    });
}

function toggleStatus(formId) {
    fetch(`/admin/forms/${formId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error toggling status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error toggling status');
    });
}

function bulkGenerateQr() {
    document.getElementById('bulkQrModal').classList.remove('hidden');
}

function closeBulkQrModal() {
    document.getElementById('bulkQrModal').classList.add('hidden');
}

function confirmBulkGenerate() {
    const selectedForms = Array.from(document.querySelectorAll('input[name="selected_forms[]"]:checked')).map(cb => cb.value);
    
    if (selectedForms.length === 0) {
        alert('Please select at least one form');
        return;
    }

    fetch('/admin/qr-codes/bulk-generate', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ forms: selectedForms }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeBulkQrModal();
            location.reload();
        } else {
            alert('Error generating QR codes');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error generating QR codes');
    });
}
</script>
@endsection


