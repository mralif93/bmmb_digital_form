@extends('layouts.admin-minimal')

@section('title', 'Forms Management - BMMB Digital Forms')
@section('page-title', 'Forms Management')
@section('page-description', 'Manage all custom forms')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

<div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
            <i class='bx bx-file-blank text-orange-600 dark:text-orange-400 text-xl'></i>
        </div>
        <div>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Forms Management</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400">Total: {{ $forms->total() }} forms</p>
        </div>
    </div>
    <button onclick="openCreateFormModal()" class="inline-flex items-center justify-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors w-full sm:w-auto">
        <i class='bx bx-plus mr-1.5'></i>
        Create New Form
    </button>
</div>

<!-- Forms Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto" style="-webkit-overflow-scrolling: touch;">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" style="min-width: 900px; width: 100%;">
            <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Form Name
                        </th>
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell">
                            Slug
                        </th>
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell">
                            Public
                        </th>
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider hidden lg:table-cell">
                            Created
                        </th>
                        <th class="px-2 sm:px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($forms as $form)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                    data-form-id="{{ $form->id }}"
                    data-form-name="{{ $form->name }}"
                    data-form-slug="{{ $form->slug }}"
                    data-form-description="{{ $form->description ?? '' }}"
                    data-form-status="{{ $form->status }}"
                    data-form-is-public="{{ $form->is_public ? '1' : '0' }}"
                    data-form-allow-multiple="{{ $form->allow_multiple_submissions ? '1' : '0' }}"
                    data-form-submission-limit="{{ $form->submission_limit ?? '' }}">
                    <td class="px-3 sm:px-4 py-3">
                        <div class="text-xs font-semibold text-gray-900 dark:text-white">
                            {{ $form->name }}
                        </div>
                        @if($form->description)
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1 hidden sm:block">
                            {{ Str::limit($form->description, 50) }}
                        </div>
                        @endif
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 sm:hidden font-mono">
                            {{ $form->slug }}
                        </div>
                    </td>
                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap hidden sm:table-cell">
                        <div class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                            {{ $form->slug }}
                        </div>
                    </td>
                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                'inactive' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$form->status] ?? $statusColors['draft'] }}">
                            {{ ucfirst($form->status) }}
                        </span>
                    </td>
                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap hidden md:table-cell">
                        @if($form->is_public)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                Yes
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                No
                            </span>
                        @endif
                    </td>
                    <td class="px-3 sm:px-4 py-3 whitespace-nowrap hidden lg:table-cell">
                        <div class="text-xs text-gray-600 dark:text-gray-400">
                            {{ $form->created_at->format('M d, Y') }}
                        </div>
                    </td>
                    <td class="px-2 sm:px-4 py-3 text-right text-xs font-medium">
                        <div class="flex flex-wrap items-center justify-end gap-1 sm:gap-2">
                            <a href="{{ route('admin.form-builder.index', $form) }}" class="inline-flex items-center justify-center px-2 lg:px-3 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-700 dark:bg-purple-900/30 dark:hover:bg-purple-900/50 dark:text-purple-400 rounded-lg text-xs transition-colors" title="Form Builder">
                                <i class='bx bx-code-alt lg:mr-1'></i>
                                <span class="hidden lg:inline">Builder</span>
                            </a>
                            <a href="{{ route('admin.form-sections.index', $form) }}" class="inline-flex items-center justify-center px-2 lg:px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 dark:text-indigo-400 rounded-lg text-xs transition-colors" title="Sections">
                                <i class='bx bx-list-ul lg:mr-1'></i>
                                <span class="hidden lg:inline">Sections</span>
                            </a>
                            <button onclick="openViewFormModal({{ $form->id }})" class="inline-flex items-center justify-center px-2 lg:px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 rounded-lg text-xs transition-colors" title="View Form">
                                <i class='bx bx-show lg:mr-1'></i>
                                <span class="hidden lg:inline">View</span>
                            </button>
                            <button onclick="openEditFormModal({{ $form->id }})" class="inline-flex items-center justify-center px-2 lg:px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 dark:text-orange-400 rounded-lg text-xs transition-colors" title="Edit Form">
                                <i class='bx bx-edit lg:mr-1'></i>
                                <span class="hidden lg:inline">Edit</span>
                            </button>
                            <button onclick="openDeleteFormModal({{ $form->id }}, '{{ $form->name }}')" class="inline-flex items-center justify-center px-2 lg:px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg text-xs transition-colors" title="Delete Form">
                                <i class='bx bx-trash lg:mr-1'></i>
                                <span class="hidden lg:inline">Delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class='bx bx-file-blank text-2xl text-gray-400 dark:text-gray-500'></i>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No forms found</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">Get started by creating your first custom form</p>
                        <button onclick="openCreateFormModal()" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                            <i class='bx bx-plus mr-1.5'></i>
                            Create First Form
                        </button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($forms->hasPages())
    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
        {{ $forms->links() }}
    </div>
    @endif
</div>

<!-- Create Form Modal -->
<div id="createFormModal" class="fixed inset-0 bg-black bg-opacity-0 hidden z-50 flex items-center justify-center transition-opacity duration-300 p-2 sm:p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0">
        <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Create New Form</h3>
                <button onclick="closeCreateFormModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
            <form id="createFormForm" action="{{ route('admin.forms.store') }}" method="POST">
                @csrf
                <div class="space-y-4" id="createFormModalContent">
                    <!-- Create form will be loaded here -->
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Form Modal -->
<div id="editFormModal" class="fixed inset-0 bg-black bg-opacity-0 hidden z-50 flex items-center justify-center transition-opacity duration-300 p-2 sm:p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0">
        <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Edit Form</h3>
                <button onclick="closeEditFormModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
            <form id="editFormForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4" id="editFormModalContent">
                    <!-- Edit form will be loaded here -->
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Form Modal -->
<div id="viewFormModal" class="fixed inset-0 bg-black bg-opacity-0 hidden z-50 flex items-center justify-center transition-opacity duration-300 p-2 sm:p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto transform scale-95 transition-all duration-300 opacity-0">
        <div class="p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Form Details</h3>
                <button onclick="closeViewFormModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
            <div id="viewFormModalContent">
                <!-- Form details will be loaded here -->
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form Confirmation Modal -->
<div id="deleteFormModal" class="fixed inset-0 bg-black bg-opacity-0 hidden z-50 flex items-center justify-center transition-opacity duration-300 p-2 sm:p-4">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full transform scale-95 transition-all duration-300 opacity-0">
        <div class="p-4 sm:p-6">
            <div class="flex items-center justify-center mb-4 relative">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white text-center">Delete Form</h3>
                <button onclick="closeDeleteFormModal()" class="absolute right-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class='bx bx-x text-xl'></i>
                </button>
            </div>
            <div class="mb-6">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class='bx bx-trash text-2xl text-red-600 dark:text-red-400'></i>
                </div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white text-center mb-2">Are you sure?</h4>
                <p class="text-xs text-gray-600 dark:text-gray-400 text-center" id="deleteFormMessage">
                    This action cannot be undone and will delete all associated sections, fields, and submissions.
                </p>
            </div>
            <form id="deleteFormForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex items-center justify-center space-x-3">
                    <button type="button" onclick="closeDeleteFormModal()" 
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
    const statusOptions = @json(\App\Models\Form::getStatusOptions());

    function openCreateFormModal() {
        const container = document.getElementById('createFormModalContent');
        
        // Build create form HTML
        container.innerHTML = `
            <!-- Form Name -->
            <div>
                <label for="create_name" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Form Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="create_name" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       placeholder="e.g., Customer Feedback Form">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">The display name for this form</p>
            </div>

            <!-- Slug -->
            <div>
                <label for="create_slug" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Slug
                </label>
                <input type="text" name="slug" id="create_slug"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent font-mono"
                       placeholder="e.g., customer-feedback-form">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">URL-friendly identifier (auto-generated if left empty)</p>
            </div>

            <!-- Description -->
            <div>
                <label for="create_description" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description
                </label>
                <textarea name="description" id="create_description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                          placeholder="Brief description of what this form is used for"></textarea>
            </div>

            <!-- Status -->
            <div>
                <label for="create_status" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status" id="create_status" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    ${Object.entries(statusOptions).map(([value, label]) => 
                        `<option value="${value}" ${value === 'draft' ? 'selected' : ''}>${label}</option>`
                    ).join('')}
                </select>
            </div>

            <!-- Submission Limit -->
            <div>
                <label for="create_submission_limit" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Submission Limit
                </label>
                <input type="number" name="submission_limit" id="create_submission_limit" min="1"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       placeholder="Leave empty for unlimited">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum number of submissions allowed per user (leave empty for unlimited)</p>
            </div>

            <!-- Checkboxes -->
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_public" id="create_is_public" value="1" checked
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                    <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Public Form (accessible to all users)</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="allow_multiple_submissions" id="create_allow_multiple" value="1" checked
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                    <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Allow Multiple Submissions</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition-colors">
                Create Form
            </button>
        `;
        
        // Show modal with animation
        const modal = document.getElementById('createFormModal');
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

    function closeCreateFormModal() {
        const modal = document.getElementById('createFormModal');
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
            const container = document.getElementById('createFormModalContent');
            container.innerHTML = '';
        }, 300);
    }

    function openEditFormModal(formId) {
        // Get form data from the table row
        const formRow = document.querySelector(`[data-form-id="${formId}"]`);
        if (!formRow) {
            alert('Form not found');
            return;
        }

        // Get form data from data attributes
        const formName = formRow.dataset.formName || '';
        const formSlug = formRow.dataset.formSlug || '';
        const formDescription = formRow.dataset.formDescription || '';
        const formStatus = formRow.dataset.formStatus || 'draft';
        const isPublic = formRow.dataset.formIsPublic === '1';
        const allowMultiple = formRow.dataset.formAllowMultiple === '1';
        const submissionLimit = formRow.dataset.formSubmissionLimit || '';

        const container = document.getElementById('editFormModalContent');
        
        // Build edit form HTML
        container.innerHTML = `
            <!-- Form Name -->
            <div>
                <label for="edit_name" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Form Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="edit_name" value="${formName.replace(/"/g, '&quot;')}" required
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       placeholder="e.g., Customer Feedback Form">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">The display name for this form</p>
            </div>

            <!-- Slug -->
            <div>
                <label for="edit_slug" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Slug
                </label>
                <input type="text" name="slug" id="edit_slug" value="${formSlug.replace(/"/g, '&quot;')}"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent font-mono"
                       placeholder="e.g., customer-feedback-form">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">URL-friendly identifier (auto-generated if left empty)</p>
            </div>

            <!-- Description -->
            <div>
                <label for="edit_description" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description
                </label>
                <textarea name="description" id="edit_description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                          placeholder="Brief description of what this form is used for">${formDescription.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</textarea>
            </div>

            <!-- Status -->
            <div>
                <label for="edit_status" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status" id="edit_status" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    ${Object.entries(statusOptions).map(([value, label]) => 
                        `<option value="${value}" ${value === formStatus ? 'selected' : ''}>${label}</option>`
                    ).join('')}
                </select>
            </div>

            <!-- Submission Limit -->
            <div>
                <label for="edit_submission_limit" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Submission Limit
                </label>
                <input type="number" name="submission_limit" id="edit_submission_limit" value="${submissionLimit}" min="1"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                       placeholder="Leave empty for unlimited">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum number of submissions allowed per user (leave empty for unlimited)</p>
            </div>

            <!-- Checkboxes -->
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" name="is_public" id="edit_is_public" value="1" ${isPublic ? 'checked' : ''}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                    <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Public Form (accessible to all users)</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="allow_multiple_submissions" id="edit_allow_multiple" value="1" ${allowMultiple ? 'checked' : ''}
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:border-gray-600 rounded">
                    <span class="ml-2 text-xs text-gray-700 dark:text-gray-300">Allow Multiple Submissions</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded-lg transition-colors">
                Update Form
            </button>
        `;
        
        // Set form action
        const editForm = document.getElementById('editFormForm');
        editForm.action = `{{ url('admin/forms') }}/${formId}`;
        
        // Show modal with animation
        const editModal = document.getElementById('editFormModal');
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

    function closeEditFormModal() {
        const editModal = document.getElementById('editFormModal');
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
            const container = document.getElementById('editFormModalContent');
            container.innerHTML = '';
        }, 300);
    }

    function openDeleteFormModal(formId, formName) {
        // Update message with form name
        document.getElementById('deleteFormMessage').textContent = `Are you sure you want to delete "${formName}"? This action cannot be undone and will delete all associated sections, fields, and submissions.`;
        
        // Set form action
        const deleteForm = document.getElementById('deleteFormForm');
        deleteForm.action = `{{ url('admin/forms') }}/${formId}`;
        
        // Show modal with animation
        const deleteModal = document.getElementById('deleteFormModal');
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

    function closeDeleteFormModal() {
        const deleteModal = document.getElementById('deleteFormModal');
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

    function openViewFormModal(formId) {
        const modal = document.getElementById('viewFormModal');
        const modalContent = modal.querySelector('div');
        const contentContainer = document.getElementById('viewFormModalContent');
        
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
        
        // Fetch form data
        fetch(`{{ url('admin/forms') }}/${formId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.form) {
                const form = data.form;
                const sections = data.sections || [];
                
                // Build status badges
                const statusColors = {
                    'draft': 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                    'active': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                    'inactive': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                };
                
                let sectionsHtml = '';
                if (sections.length > 0) {
                    sectionsHtml = sections.map(section => {
                        let fieldsHtml = '';
                        if (section.fields && section.fields.length > 0) {
                            fieldsHtml = section.fields.map(field => `
                                <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">${field.field_label || field.field_name}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">${field.field_type ? field.field_type.charAt(0).toUpperCase() + field.field_type.slice(1) : ''}</span>
                                </div>
                            `).join('');
                        } else {
                            fieldsHtml = '<div class="py-2 text-xs text-gray-500 dark:text-gray-400 italic text-center">No fields in this section</div>';
                        }
                        
                        return `
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden mb-4">
                                <!-- Title Section -->
                                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-xs font-semibold text-gray-900 dark:text-white">
                                            ${section.section_label || section.section_key}
                                        </h4>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            ${section.fields_count || 0} field(s)
                                        </span>
                                    </div>
                                    ${section.section_description ? `<p class="text-xs text-gray-600 dark:text-gray-400 mt-1">${section.section_description}</p>` : ''}
                                </div>
                                <!-- Fields List -->
                                <div class="px-4 py-2">
                                    ${fieldsHtml}
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    sectionsHtml = `
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class='bx bx-layer text-2xl text-gray-400 dark:text-gray-500'></i>
                            </div>
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No sections configured</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">Start building your form by adding sections and fields</p>
                        </div>
                    `;
                }
                
                contentContainer.innerHTML = `
                    <!-- Form Details Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                        <!-- Title Section -->
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">${form.name || ''}</h2>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">${form.description || 'No description provided'}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusColors[form.status] || statusColors['draft']}">
                                        ${form.status ? form.status.charAt(0).toUpperCase() + form.status.slice(1) : 'Draft'}
                                    </span>
                                    ${form.is_public ? 
                                        '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Public</span>' :
                                        '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Private</span>'
                                    }
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Details List -->
                        <div class="px-4 py-2">
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Slug</span>
                                <span class="text-xs font-mono text-gray-900 dark:text-white">${form.slug || ''}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Submission Limit</span>
                                <span class="text-xs text-gray-900 dark:text-white">${form.submission_limit || 'Unlimited'}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Multiple Submissions</span>
                                <span class="text-xs text-gray-900 dark:text-white">${form.allow_multiple_submissions ? 'Allowed' : 'Not Allowed'}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Created At</span>
                                <span class="text-xs text-gray-600 dark:text-gray-400">${form.created_at || ''}</span>
                            </div>
                            <div class="flex items-center justify-between py-2 last:border-b-0">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Updated At</span>
                                <span class="text-xs text-gray-600 dark:text-gray-400">${form.updated_at || ''}</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ url('admin/forms') }}/${formId}/builder" class="inline-flex items-center px-3 py-1.5 bg-purple-100 hover:bg-purple-200 text-purple-700 dark:bg-purple-900/30 dark:hover:bg-purple-900/50 dark:text-purple-400 rounded-lg text-xs transition-colors">
                                    <i class='bx bx-code-alt mr-1'></i>
                                    Form Builder
                                </a>
                                <a href="{{ url('admin/forms') }}/${formId}/sections" class="inline-flex items-center px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 dark:text-indigo-400 rounded-lg text-xs transition-colors">
                                    <i class='bx bx-list-ul mr-1'></i>
                                    Sections
                                </a>
                                <button onclick="closeViewFormModal(); setTimeout(() => openEditFormModal(${formId}), 300);" class="inline-flex items-center px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 dark:text-orange-400 rounded-lg text-xs transition-colors">
                                    <i class='bx bx-edit mr-1'></i>
                                    Edit
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sections and Fields Overview -->
                    ${sections.length > 0 ? `
                        <div class="bg-white dark:bg-gray-800 rounded-lg">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Form Structure</h3>
                            <div class="space-y-4">
                                ${sectionsHtml}
                            </div>
                        </div>
                    ` : sectionsHtml}
                `;
            } else {
                contentContainer.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-xs text-red-600 dark:text-red-400">Failed to load form details</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contentContainer.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-xs text-red-600 dark:text-red-400">Error loading form details</p>
                </div>
            `;
        });
    }

    function closeViewFormModal() {
        const modal = document.getElementById('viewFormModal');
        const modalContent = modal.querySelector('div');
        // Start exit animation
        modal.classList.remove('bg-opacity-50');
        modal.classList.add('bg-opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        modalContent.classList.add('scale-95', 'opacity-0');
        // Hide after animation
        setTimeout(() => {
            modal.classList.add('hidden');
            // Reset content
            const container = document.getElementById('viewFormModalContent');
            container.innerHTML = '';
        }, 300);
    }
</script>
@endpush
@endsection


