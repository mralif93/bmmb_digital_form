@extends('layouts.admin-minimal')

@section('title', 'Form Sections - ' . $form->name . ' - BMMB Digital Forms')
@section('page-title', 'Form Sections: ' . $form->name)
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

<div class="mb-4 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
            <i class='bx bx-layer text-orange-600 dark:text-orange-400 text-xl'></i>
        </div>
        <div>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Form Sections: {{ $form->name }}</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400">Total: {{ $sections->count() }} section(s)</p>
        </div>
    </div>
    <div class="flex items-center space-x-2">
        <button onclick="openCreateSectionModal()" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
            <i class='bx bx-plus mr-1.5'></i>
            Create New Section
        </button>
    </div>
</div>

<!-- Sections Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Order
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Section Label
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Section Key
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Description
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($sections as $section)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" 
                    data-section-id="{{ $section->id }}" 
                    data-sort-order="{{ $section->sort_order }}"
                    data-section-key="{{ $section->section_key }}"
                    data-section-label="{{ $section->section_label }}"
                    data-section-description="{{ $section->section_description ?? '' }}"
                    data-is-active="{{ $section->is_active ? '1' : '0' }}">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <div class="cursor-move text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class='bx bx-menu text-lg'></i>
                            </div>
                            <span class="text-xs font-medium text-gray-900 dark:text-white">
                                {{ $section->sort_order }}
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-xs font-semibold text-gray-900 dark:text-white">
                            {{ $section->section_label }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                            {{ $section->section_key }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $section->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                            {{ $section->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-xs text-gray-600 dark:text-gray-400">
                            {{ $section->section_description ? Str::limit($section->section_description, 50) : '-' }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <button onclick="moveSectionUp({{ $section->id }})" 
                                    class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 rounded transition-colors" title="Move Up">
                                <i class='bx bx-chevron-up'></i>
                            </button>
                            <button onclick="moveSectionDown({{ $section->id }})" 
                                    class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 rounded transition-colors" title="Move Down">
                                <i class='bx bx-chevron-down'></i>
                            </button>
                            <button onclick="openEditSectionModal({{ $section->id }})" 
                                    class="inline-flex items-center px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 dark:text-orange-400 rounded-lg transition-colors">
                                Edit
                            </button>
                            <button onclick="deleteSection({{ $section->id }}, '{{ $section->section_label }}')" 
                                    class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class='bx bx-layer text-2xl text-gray-400 dark:text-gray-500'></i>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No sections found</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">Get started by creating your first section</p>
                        <button onclick="openCreateSectionModal()" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                            <i class='bx bx-plus mr-1.5'></i>
                            Create First Section
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Create Section Modal -->
<div id="createSectionModal" class="fixed inset-0 bg-black bg-opacity-0 hidden z-50 flex items-center justify-center transition-opacity duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Create New Section</h3>
                <button onclick="closeCreateSectionModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
            <form id="createSectionForm" action="{{ route('admin.form-sections.store', $form) }}" method="POST">
                @csrf
                <div class="space-y-4" id="createSectionModalContent">
                    <!-- Create form will be loaded here -->
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Section Modal -->
<div id="editSectionModal" class="fixed inset-0 bg-black bg-opacity-0 hidden z-50 flex items-center justify-center transition-opacity duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Edit Section</h3>
                <button onclick="closeEditSectionModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
            <form id="editSectionForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4" id="editSectionModalContent">
                    <!-- Edit form will be loaded here -->
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Section Confirmation Modal -->
<div id="deleteSectionModal" class="fixed inset-0 bg-black bg-opacity-0 hidden z-50 flex items-center justify-center transition-opacity duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 transform scale-95 transition-all duration-300 opacity-0">
        <div class="p-6">
            <div class="flex items-center justify-center mb-4 relative">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white text-center">Delete Section</h3>
                <button onclick="closeDeleteSectionModal()" class="absolute right-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
            <div class="mb-6">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-trash text-2xl text-red-600 dark:text-red-400'></i>
                </div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white text-center mb-2">Are you sure?</h4>
                <p class="text-xs text-gray-600 dark:text-gray-400 text-center" id="deleteSectionMessage">
                    This action cannot be undone. This will permanently delete the section.
                </p>
            </div>
            <form id="deleteSectionForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex items-center justify-center space-x-3">
                    <button type="button" onclick="closeDeleteSectionModal()" 
                            class="px-4 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors">
                        Yes, delete it
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openCreateSectionModal() {
        const container = document.getElementById('createSectionModalContent');
        const maxOrder = {{ $sections->max('sort_order') ?? 0 }};
        
        // Build create form HTML
        container.innerHTML = `
            <!-- Section Key -->
            <div>
                <label for="create_section_key" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Section Key <span class="text-red-500">*</span>
                </label>
                <input type="text" name="section_key" id="create_section_key" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       placeholder="e.g., custom_section">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Unique identifier (lowercase, underscores only)</p>
            </div>

            <!-- Section Label -->
            <div>
                <label for="create_section_label" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Section Label <span class="text-red-500">*</span>
                </label>
                <input type="text" name="section_label" id="create_section_label" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       placeholder="e.g., Custom Section Name">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Display name shown in the form</p>
            </div>

            <!-- Section Description -->
            <div>
                <label for="create_section_description" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Section Description
                </label>
                <textarea name="section_description" id="create_section_description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                          placeholder="Optional description for this section"></textarea>
            </div>

            <!-- Sort Order -->
            <div>
                <label for="create_sort_order" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Sort Order
                </label>
                <input type="number" name="sort_order" id="create_sort_order" value="${maxOrder + 1}" min="0"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
            </div>

            <!-- Is Active -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" id="create_is_active" value="1" checked
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                    <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Active (Section will be visible in forms)</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition-colors">
                Create Section
            </button>
        `;
        
        // Show modal with animation
        const modal = document.getElementById('createSectionModal');
        const modalContent = modal.querySelector('div');
        modal.classList.remove('hidden');
        // Trigger animation
        setTimeout(() => {
            modal.classList.remove('bg-opacity-0');
            modal.classList.add('bg-opacity-50');
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeCreateSectionModal() {
        const modal = document.getElementById('createSectionModal');
        const modalContent = modal.querySelector('div');
        // Start exit animation
        modal.classList.remove('bg-opacity-50');
        modal.classList.add('bg-opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        // Hide after animation
        setTimeout(() => {
            modal.classList.add('hidden');
            // Reset form
            const container = document.getElementById('createSectionModalContent');
            container.innerHTML = '';
        }, 300);
    }

    function openEditSectionModal(sectionId) {
        // Get section data from the table row
        const sectionRow = document.querySelector(`[data-section-id="${sectionId}"]`);
        if (!sectionRow) {
            alert('Section not found');
            return;
        }

        // Get section data from data attributes
        const sectionKey = sectionRow.dataset.sectionKey || '';
        const sectionLabel = sectionRow.dataset.sectionLabel || '';
        const sectionDescription = sectionRow.dataset.sectionDescription || '';
        const sortOrder = sectionRow.dataset.sortOrder || '0';
        const isActive = sectionRow.dataset.isActive === '1';

        const container = document.getElementById('editSectionModalContent');
        
        // Build edit form HTML
        container.innerHTML = `
            <!-- Section Key -->
            <div>
                <label for="edit_section_key" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Section Key <span class="text-red-500">*</span>
                </label>
                <input type="text" name="section_key" id="edit_section_key" value="${sectionKey.replace(/"/g, '&quot;')}" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       placeholder="e.g., custom_section">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Unique identifier (lowercase, underscores only)</p>
            </div>

            <!-- Section Label -->
            <div>
                <label for="edit_section_label" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Section Label <span class="text-red-500">*</span>
                </label>
                <input type="text" name="section_label" id="edit_section_label" value="${sectionLabel.replace(/"/g, '&quot;')}" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       placeholder="e.g., Custom Section Name">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Display name shown in the form</p>
            </div>

            <!-- Section Description -->
            <div>
                <label for="edit_section_description" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Section Description
                </label>
                <textarea name="section_description" id="edit_section_description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                          placeholder="Optional description for this section">${sectionDescription.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</textarea>
            </div>

            <!-- Sort Order -->
            <div>
                <label for="edit_sort_order" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Sort Order
                </label>
                <input type="number" name="sort_order" id="edit_sort_order" value="${sortOrder}" min="0"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Lower numbers appear first</p>
            </div>

            <!-- Is Active -->
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" ${isActive ? 'checked' : ''}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                    <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Active (Section will be visible in forms)</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition-colors">
                Update Section
            </button>
        `;
        
        // Set form action
        document.getElementById('editSectionForm').action = `{{ route('admin.form-sections.update', [$form, ':section']) }}`.replace(':section', sectionId);
        
        // Show modal with animation
        const editModal = document.getElementById('editSectionModal');
        const editModalContent = editModal.querySelector('div');
        editModal.classList.remove('hidden');
        // Trigger animation
        setTimeout(() => {
            editModal.classList.remove('bg-opacity-0');
            editModal.classList.add('bg-opacity-50');
            editModalContent.classList.remove('scale-95', 'opacity-0');
            editModalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeEditSectionModal() {
        const editModal = document.getElementById('editSectionModal');
        const editModalContent = editModal.querySelector('div');
        // Start exit animation
        editModal.classList.remove('bg-opacity-50');
        editModal.classList.add('bg-opacity-0');
        editModalContent.classList.remove('scale-100', 'opacity-100');
        editModalContent.classList.add('scale-95', 'opacity-0');
        // Hide after animation
        setTimeout(() => {
            editModal.classList.add('hidden');
            // Reset form
            const container = document.getElementById('editSectionModalContent');
            container.innerHTML = '';
        }, 300);
    }

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
        fetch(`{{ route('admin.form-sections.reorder', $form) }}`, {
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
        // Update message with section label
        document.getElementById('deleteSectionMessage').textContent = `Are you sure you want to delete "${sectionLabel}"? This action cannot be undone.`;
        
        // Set form action
        document.getElementById('deleteSectionForm').action = `{{ route('admin.form-sections.destroy', [$form, ':section']) }}`.replace(':section', sectionId);
        
        // Show modal with animation
        const deleteModal = document.getElementById('deleteSectionModal');
        const deleteModalContent = deleteModal.querySelector('div');
        deleteModal.classList.remove('hidden');
        // Trigger animation
        setTimeout(() => {
            deleteModal.classList.remove('bg-opacity-0');
            deleteModal.classList.add('bg-opacity-50');
            deleteModalContent.classList.remove('scale-95', 'opacity-0');
            deleteModalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeDeleteSectionModal() {
        const deleteModal = document.getElementById('deleteSectionModal');
        const deleteModalContent = deleteModal.querySelector('div');
        // Start exit animation
        deleteModal.classList.remove('bg-opacity-50');
        deleteModal.classList.add('bg-opacity-0');
        deleteModalContent.classList.remove('scale-100', 'opacity-100');
        deleteModalContent.classList.add('scale-95', 'opacity-0');
        // Hide after animation
        setTimeout(() => {
            deleteModal.classList.add('hidden');
        }, 300);
    }
</script>
@endpush
@endsection

