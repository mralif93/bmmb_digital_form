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

<div class="mb-4 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
            <i class='bx bx-file-blank text-orange-600 dark:text-orange-400 text-xl'></i>
        </div>
        <div>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Forms Management</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400">Total: {{ $forms->total() }} forms</p>
        </div>
    </div>
    <a href="{{ route('admin.forms.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
        <i class='bx bx-plus mr-1.5'></i>
        Create New Form
    </a>
</div>

<!-- Forms Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Form Name
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Slug
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Public
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Created
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($forms as $form)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="text-xs font-semibold text-gray-900 dark:text-white">
                            {{ $form->name }}
                        </div>
                        @if($form->description)
                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                            {{ Str::limit($form->description, 50) }}
                        </div>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                            {{ $form->slug }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
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
                    <td class="px-4 py-3 whitespace-nowrap">
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
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-xs text-gray-600 dark:text-gray-400">
                            {{ $form->created_at->format('M d, Y') }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.forms.show', $form->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 rounded-lg text-xs transition-colors">
                                View
                            </a>
                            <a href="{{ route('admin.forms.edit', $form->id) }}" class="inline-flex items-center px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 dark:text-orange-400 rounded-lg text-xs transition-colors">
                                Edit
                            </a>
                            <button onclick="deleteForm({{ $form->id }})" class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg text-xs transition-colors">
                                Delete
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
                        <a href="{{ route('admin.forms.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                            <i class='bx bx-plus mr-1.5'></i>
                            Create First Form
                        </a>
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

@push('scripts')
<script>
function deleteForm(formId) {
    if (confirm('Are you sure you want to delete this form? This action cannot be undone and will delete all associated fields and submissions.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.forms.destroy', ':id') }}`.replace(':id', formId);
        
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
}
</script>
@endpush
@endsection


