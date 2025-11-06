@extends('layouts.admin-minimal')

@section('title', 'Form Sections - ' . ($formTypeLabels[$type] ?? ucfirst($type)) . ' - BMMB Digital Forms')
@section('page-title', 'Form Sections: ' . ($formTypeLabels[$type] ?? ucfirst($type)))
@section('page-description', 'Manage form sections')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg text-sm text-red-800 dark:text-red-400">
    {{ session('error') }}
</div>
@endif

<div class="mb-4">
    <a href="{{ route('admin.form-builder.index', [$type, 1]) }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
        <i class='bx bx-arrow-back mr-1.5'></i>
        Back to Form Builder
    </a>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 rounded-lg text-xs">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 rounded-lg text-xs">
        {{ session('error') }}
    </div>
@endif

<!-- Header Card -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-200 dark:from-orange-900/30 dark:to-orange-800/30 rounded-lg flex items-center justify-center">
                <i class='bx bx-layer text-2xl text-orange-600 dark:text-orange-400'></i>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $formTypeLabels[$type] ?? ucfirst($type) }} Sections</h2>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $sections->count() }} section(s) total</p>
            </div>
        </div>
        <a href="{{ route('admin.form-sections.create', $type) }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
            <i class='bx bx-plus mr-1.5'></i>
            Create New Section
        </a>
    </div>
</div>

<!-- Sections List -->
@if($sections->isEmpty())
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
        <i class='bx bx-file-blank text-4xl text-gray-400 dark:text-gray-500 mb-4'></i>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">No sections found.</p>
        <a href="{{ route('admin.form-sections.create', $type) }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
            <i class='bx bx-plus mr-1.5'></i>
            Create Your First Section
        </a>
    </div>
@else
    <div class="space-y-4">
        @foreach($sections as $section)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700" data-section-id="{{ $section->id }}" data-sort-order="{{ $section->sort_order }}">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex items-center space-x-3">
                            <!-- Drag Handle -->
                            <div class="cursor-move text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class='bx bx-menu text-lg'></i>
                            </div>
                            
                            <!-- Sort Order -->
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-8 text-center">
                                {{ $section->sort_order }}
                            </span>
                            
                            <!-- Section Info -->
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-white">
                                        {{ $section->section_label }}
                                    </p>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-lg {{ $section->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                                        {{ $section->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <span class="font-mono text-gray-600 dark:text-gray-400">{{ $section->section_key }}</span>
                                    @if($section->section_description)
                                        <span class="ml-2">• {{ Str::limit($section->section_description, 60) }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-2 ml-4">
                            <button onclick="moveSectionUp({{ $section->id }})" 
                                    class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 rounded transition-colors">
                                <i class='bx bx-chevron-up'></i>
                            </button>
                            <button onclick="moveSectionDown({{ $section->id }})" 
                                    class="px-2 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 rounded transition-colors">
                                <i class='bx bx-chevron-down'></i>
                            </button>
                            <a href="{{ route('admin.form-sections.edit', [$type, $section]) }}" 
                               class="px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 dark:text-orange-400 rounded-lg text-xs transition-colors">
                                Edit
                            </a>
                            <button onclick="deleteSection({{ $section->id }}, '{{ $section->section_label }}')" 
                                    class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg text-xs transition-colors">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@push('scripts')
<script>
    function moveSectionUp(sectionId) {
        const sectionRow = document.querySelector(`[data-section-id="${sectionId}"]`);
        const prevSection = sectionRow.previousElementSibling;
        
        if (!prevSection || !prevSection.hasAttribute('data-section-id')) {
            return;
        }
        
        const currentOrder = parseInt(sectionRow.dataset.sortOrder);
        const prevOrder = parseInt(prevSection.dataset.sortOrder);
        
        // Swap sort orders
        updateSectionOrder(sectionId, prevOrder);
        updateSectionOrder(prevSection.dataset.sectionId, currentOrder);
        
        // Reload page to reflect changes
        setTimeout(() => location.reload(), 300);
    }

    function moveSectionDown(sectionId) {
        const sectionRow = document.querySelector(`[data-section-id="${sectionId}"]`);
        const nextSection = sectionRow.nextElementSibling;
        
        if (!nextSection || !nextSection.hasAttribute('data-section-id')) {
            return;
        }
        
        const currentOrder = parseInt(sectionRow.dataset.sortOrder);
        const nextOrder = parseInt(nextSection.dataset.sortOrder);
        
        // Swap sort orders
        updateSectionOrder(sectionId, nextOrder);
        updateSectionOrder(nextSection.dataset.sectionId, currentOrder);
        
        // Reload page to reflect changes
        setTimeout(() => location.reload(), 300);
    }

    function updateSectionOrder(sectionId, newOrder) {
        fetch(`/admin/form-sections/{{ $type }}/reorder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                sections: [{
                    id: sectionId,
                    sort_order: newOrder
                }]
            })
        }).catch(error => console.error('Error updating sort order:', error));
    }

    function deleteSection(sectionId, sectionLabel) {
        Swal.fire({
            title: 'Delete Section?',
            text: `Are you sure you want to delete "${sectionLabel}"? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/form-sections/{{ $type }}/${sectionId}`;
                
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endpush
@endsection

