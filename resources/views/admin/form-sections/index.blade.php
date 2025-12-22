@extends('layouts.admin-minimal')

@section('title', 'Form Sections - ' . $form->name . ' - BMMB Digital Forms')
@section('page-title', 'Form Sections: ' . $form->name)
@section('page-description', 'Manage form sections')

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
                <i class='bx bx-layer text-orange-600 dark:text-orange-400 text-xl'></i>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Form Sections: {{ $form->name }}</h2>
                <p class="text-xs text-gray-600 dark:text-gray-400">Total: {{ $sections->count() }} section(s)</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="openCreateSectionModal()"
                class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
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
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Order
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Section Label
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Section Key
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Description
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Grid Layout
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="sectionsTableBody"
                    class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($sections as $section)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors sortable-row"
                            data-section-id="{{ $section->id }}" data-sort-order="{{ $section->sort_order }}"
                            data-section-key="{{ $section->section_key }}" data-section-label="{{ $section->section_label }}"
                            data-section-description="{{ $section->section_description ?? '' }}"
                            data-is-active="{{ $section->is_active ? '1' : '0' }}">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div
                                        class="cursor-move text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 drag-handle">
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
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $section->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $section->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $section->section_description ? Str::limit($section->section_description, 50) : '-' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <select onchange="updateGridLayout({{ $section->id }}, this.value)"
                                    class="px-2 py-1 text-xs border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                                    <option value="2-column" {{ ($section->grid_layout ?? '2-column') === '2-column' ? 'selected' : '' }}>2-Column</option>
                                    <option value="6-column" {{ ($section->grid_layout ?? '2-column') === '6-column' ? 'selected' : '' }}>6-Column</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <button onclick="moveSectionUp({{ $section->id }})"
                                        class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 rounded transition-colors"
                                        title="Move Up">
                                        <i class='bx bx-chevron-up'></i>
                                    </button>
                                    <button onclick="moveSectionDown({{ $section->id }})"
                                        class="inline-flex items-center px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 rounded transition-colors"
                                        title="Move Down">
                                        <i class='bx bx-chevron-down'></i>
                                    </button>
                                    <button onclick="openViewSectionModal({{ $section->id }})"
                                        class="inline-flex items-center justify-center px-2 lg:px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 rounded-lg transition-colors"
                                        title="View Section">
                                        <i class='bx bx-show lg:mr-1'></i>
                                        <span class="hidden lg:inline">View</span>
                                    </button>
                                    <button onclick="openEditSectionModal({{ $section->id }})"
                                        class="inline-flex items-center justify-center px-2 lg:px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 dark:text-orange-400 rounded-lg transition-colors"
                                        title="Edit Section">
                                        <i class='bx bx-edit lg:mr-1'></i>
                                        <span class="hidden lg:inline">Edit</span>
                                    </button>
                                    <button onclick="deleteSection({{ $section->id }}, '{{ $section->section_label }}')"
                                        class="inline-flex items-center justify-center px-2 lg:px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg transition-colors"
                                        title="Delete Section">
                                        <i class='bx bx-trash lg:mr-1'></i>
                                        <span class="hidden lg:inline">Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center">
                                <div
                                    class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class='bx bx-layer text-2xl text-gray-400 dark:text-gray-500'></i>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No sections found</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">Get started by creating your first
                                    section</p>
                                <button onclick="openCreateSectionModal()"
                                    class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
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
    <div id="createSectionModal"
        class="fixed inset-0 bg-black bg-opacity-0 hidden z-50 flex items-center justify-center transition-opacity duration-300">
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Create New Section</h3>
                    <button onclick="closeCreateSectionModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
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
    <div id="editSectionModal"
        class="fixed inset-0 bg-black bg-opacity-0 hidden z-50 flex items-center justify-center transition-opacity duration-300">
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Edit Section</h3>
                    <button onclick="closeEditSectionModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
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

    <!-- View Section Modal -->
    <div id="viewSectionModal"
        class="fixed inset-0 bg-black bg-opacity-0 hidden z-50 flex items-center justify-center transition-opacity duration-300 p-2 sm:p-4">
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Section Details</h3>
                    <button onclick="closeViewSectionModal()"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class='bx bx-x text-xl'></i>
                    </button>
                </div>
                <div id="viewSectionModalContent">
                    <!-- Section details will be loaded here -->
                    <div class="flex items-center justify-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Section Confirmation Modal -->
    <div id="deleteSectionModal"
        class="fixed inset-0 bg-black bg-opacity-0 hidden z-50 flex items-center justify-center transition-opacity duration-300 p-2 sm:p-4">
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 transform scale-95 transition-all duration-300 opacity-0">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4 relative">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white text-center">Delete Section</h3>
                    <button onclick="closeDeleteSectionModal()"
                        class="absolute right-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class='bx bx-x text-xl'></i>
                    </button>
                </div>
                <div class="mb-6">
                    <div
                        class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
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
            // Update grid layout via AJAX
            function updateGridLayout(sectionId, gridLayout) {
                fetch(`{{ route('admin.form-sections.grid-layout.update', [$form, ':section']) }}`.replace(':section', sectionId), {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ grid_layout: gridLayout })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Grid layout updated successfully');
                        } else {
                            alert('Failed to update grid layout: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error updating grid layout:', error);
                        alert('Failed to update grid layout');
                    });
            }

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

            function openViewSectionModal(sectionId) {
                const modal = document.getElementById('viewSectionModal');
                const modalContent = modal.querySelector('div');
                const contentContainer = document.getElementById('viewSectionModalContent');

                // Show modal with loading state
                modal.classList.remove('hidden');
                contentContainer.innerHTML = `
                                                    <div class="flex items-center justify-center py-8">
                                                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                                                    </div>
                                                `;

                // Trigger animation
                setTimeout(() => {
                    modal.classList.remove('bg-opacity-0');
                    modal.classList.add('bg-opacity-50');
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);

                // Fetch section data
                fetch(`{{ route('admin.form-sections.show', [$form, ':section']) }}`.replace(':section', sectionId), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.section) {
                            const section = data.section;
                            const statusColors = {
                                'active': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                'inactive': 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                            };

                            contentContainer.innerHTML = `
                                                            <!-- Section Details Card -->
                                                            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                                                                <!-- Title Section -->
                                                                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                                                    <div class="flex items-center justify-between">
                                                                        <div>
                                                                            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">${section.section_label || ''}</h2>
                                                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">${section.section_description || 'No description provided'}</p>
                                                                        </div>
                                                                        <div class="flex items-center space-x-2">
                                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${section.is_active ? statusColors['active'] : statusColors['inactive']}">
                                                                                ${section.is_active ? 'Active' : 'Inactive'}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Section Details List -->
                                                                <div class="px-4 py-2">
                                                                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Section Key</span>
                                                                        <span class="text-xs font-mono text-gray-900 dark:text-white">${section.section_key || ''}</span>
                                                                    </div>
                                                                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Sort Order</span>
                                                                        <span class="text-xs text-gray-900 dark:text-white">${section.sort_order || ''}</span>
                                                                    </div>
                                                                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Fields Count</span>
                                                                        <span class="text-xs text-gray-900 dark:text-white">${section.fields_count || 0}</span>
                                                                    </div>
                                                                    <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Created At</span>
                                                                        <span class="text-xs text-gray-600 dark:text-gray-400">${section.created_at || ''}</span>
                                                                    </div>
                                                                    <div class="flex items-center justify-between py-2 last:border-b-0">
                                                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Updated At</span>
                                                                        <span class="text-xs text-gray-600 dark:text-gray-400">${section.updated_at || ''}</span>
                                                                    </div>
                                                                </div>

                                                                <!-- Action Buttons -->
                                                                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                                                                    <div class="flex items-center justify-end space-x-2">
                                                                        <button onclick="closeViewSectionModal(); setTimeout(() => openEditSectionModal(${sectionId}), 300);" class="inline-flex items-center px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 dark:text-orange-400 rounded-lg text-xs transition-colors">
                                                                            <i class='bx bx-edit mr-1'></i>
                                                                            Edit
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Fields Overview -->
                                                            ${section.fields && section.fields.length > 0 ? `
                                                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                                                    <!-- Title Section -->
                                                                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Fields in this Section (${section.fields.length})</h3>
                                                                    </div>

                                                                    <!-- Fields List -->
                                                                    <div class="px-4 py-2">
                                                                        ${section.fields.map(field => `
                                                                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                                                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">${field.field_label || field.field_name}</span>
                                                                                <span class="text-xs text-gray-600 dark:text-gray-400">${field.field_type || ''}</span>
                                                                            </div>
                                                                        `).join('')}
                                                                    </div>
                                                                </div>
                                                            ` : `
                                                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
                                                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                                                        <i class='bx bx-file-blank text-xl text-gray-400 dark:text-gray-500'></i>
                                                                    </div>
                                                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No fields in this section</h4>
                                                                    <p class="text-xs text-gray-600 dark:text-gray-400">Fields will appear here once added to this section</p>
                                                                </div>
                                                            `}
                                                        `;
                        } else {
                            contentContainer.innerHTML = `
                                                            <div class="text-center py-8">
                                                                <div class="text-red-600 dark:text-red-400 text-sm">Failed to load section details</div>
                                                            </div>
                                                        `;
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching section:', error);
                        contentContainer.innerHTML = `
                                                        <div class="text-center py-8">
                                                            <div class="text-red-600 dark:text-red-400 text-sm">Error loading section details</div>
                                                        </div>
                                                    `;
                    });
            }

            function closeViewSectionModal() {
                const modal = document.getElementById('viewSectionModal');
                const modalContent = modal.querySelector('div');
                // Start exit animation
                modal.classList.remove('bg-opacity-50');
                modal.classList.add('bg-opacity-0');
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                // Hide after animation
                setTimeout(() => {
                    modal.classList.add('hidden');
                    const container = document.getElementById('viewSectionModalContent');
                    container.innerHTML = '';
                }, 300);
            }

            // Initialize drag-and-drop for sections
            document.addEventListener('DOMContentLoaded', function () {
                const tbody = document.getElementById('sectionsTableBody');
                if (tbody && typeof Sortable !== 'undefined') {
                    new Sortable(tbody, {
                        handle: '.drag-handle',
                        animation: 150,
                        ghostClass: 'opacity-50',
                        chosenClass: 'sortable-chosen',
                        onEnd: function (evt) {
                            const rows = Array.from(tbody.querySelectorAll('tr[data-section-id]'));
                            const sections = rows.map((row, index) => ({
                                id: parseInt(row.dataset.sectionId),
                                sort_order: index + 1
                            }));

                            // Update sort orders
                            fetch(`{{ route('admin.form-sections.reorder', $form) }}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ sections: sections })
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.success) {
                                        // Update sort order numbers in the UI
                                        rows.forEach((row, index) => {
                                            const orderSpan = row.querySelector('td span');
                                            if (orderSpan) {
                                                orderSpan.textContent = index + 1;
                                            }
                                            row.dataset.sortOrder = index + 1;
                                        });
                                    } else {
                                        throw new Error(data.message || 'Failed to reorder sections');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error updating sort order:', error);
                                    alert('Failed to update section order. Please refresh the page.');
                                    // Reload page on error to restore original order
                                    location.reload();
                                });
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection