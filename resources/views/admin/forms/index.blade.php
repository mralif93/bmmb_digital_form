@extends('layouts.admin-minimal')

@section('title', $config['title'] . ' Management - BMMB Digital Forms')
@section('page-title', $config['title'] . ' Management')
@section('page-description', 'Manage all ' . $config['title'] . ' records')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

<div class="mb-4 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
            <i class='bx {{ $config['icon'] }} text-primary-600 dark:text-primary-400 text-xl'></i>
        </div>
        <div>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $config['title'] }}</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400">Total: {{ $forms->total() }} records</p>
        </div>
    </div>
    <a href="{{ route('admin.forms.create', $type) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition-colors">
        <i class='bx bx-plus mr-1.5'></i>
        Create New
    </a>
</div>

<!-- Forms Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        {{ $config['number_prefix'] }} Number
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        User
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Status
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
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-xs font-semibold text-gray-900 dark:text-white">
                            {{ $form->{$config['number_field']} }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-xs text-gray-900 dark:text-white">
                            {{ $form->user ? $form->user->first_name . ' ' . $form->user->last_name : 'N/A' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $form->user ? $form->user->email : '' }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                'submitted' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                'under_review' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                'in_progress' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                'cancelled' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                'expired' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
                                'partially_approved' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                            ];
                            $statusColor = $statusColors[$form->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusColor }}">
                            {{ ucfirst(str_replace('_', ' ', $form->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-xs text-gray-900 dark:text-white">
                            {{ $form->created_at->format('M d, Y') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $form->created_at->format('h:i A') }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.forms.show', [$type, $form->id]) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                <i class='bx bx-show'></i>
                            </a>
                            <a href="{{ route('admin.forms.edit', [$type, $form->id]) }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-900 dark:hover:text-primary-300">
                                <i class='bx bx-edit'></i>
                            </a>
                            <button onclick="deleteForm({{ $form->id }})" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class='bx bx-file-blank text-2xl text-gray-400 dark:text-gray-500'></i>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No forms found</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">Get started by creating your first {{ strtolower($config['title']) }}</p>
                        <a href="{{ route('admin.forms.create', $type) }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition-colors">
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
    if (confirm('Are you sure you want to delete this form? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.forms.destroy', [$type, ':id']) }}`.replace(':id', formId);
        
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

