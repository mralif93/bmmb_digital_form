@extends('layouts.admin-minimal')

@section('title', 'View ' . $config['title'] . ' - BMMB Digital Forms')
@section('page-title', 'View ' . $config['title'] . ': ' . $form->{$config['number_field']})
@section('page-description', 'Details of ' . $config['title'])

@section('content')
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('admin.forms.index', $type) }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
        <i class='bx bx-arrow-back mr-1.5'></i>
        Back to List
    </a>
    <div class="flex items-center space-x-2">
        <a href="{{ route('admin.forms.edit', [$type, $form->id]) }}" class="inline-flex items-center px-3 py-2 text-xs font-semibold text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors">
            <i class='bx bx-edit mr-1.5'></i>
            Edit
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class='bx bx-info-circle mr-2 text-primary-600 dark:text-primary-400'></i>
                Basic Information
            </h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        {{ $config['number_prefix'] }} Number
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white font-semibold">
                        {{ $form->{$config['number_field']} }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Status
                    </dt>
                    <dd>
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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium {{ $statusColor }}">
                            {{ ucfirst(str_replace('_', ' ', $form->status)) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        User
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $form->user ? $form->user->first_name . ' ' . $form->user->last_name : 'N/A' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Version
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $form->version ?? 'N/A' }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Created At
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $form->created_at->format('M d, Y h:i A') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        Updated At
                    </dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                        {{ $form->updated_at->format('M d, Y h:i A') }}
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Form Data (if available) -->
        @if($form->form_data)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class='bx bx-data mr-2 text-primary-600 dark:text-primary-400'></i>
                Form Data
            </h3>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <pre class="text-xs text-gray-900 dark:text-white overflow-x-auto">{{ json_encode($form->form_data, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.forms.edit', [$type, $form->id]) }}" class="block w-full text-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    <i class='bx bx-edit mr-1.5'></i>
                    Edit Form
                </a>
                <button onclick="deleteForm({{ $form->id }})" class="block w-full text-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    <i class='bx bx-trash mr-1.5'></i>
                    Delete Form
                </button>
            </div>
        </div>
    </div>
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

