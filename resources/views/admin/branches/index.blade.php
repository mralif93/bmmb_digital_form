@extends('layouts.admin-minimal')

@section('title', 'Branches Management - BMMB Digital Forms')
@section('page-title', 'Branches Management')
@section('page-description', 'Manage all branch records')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

<div class="mb-4 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
            <i class='bx bx-building text-orange-600 dark:text-orange-400 text-xl'></i>
        </div>
        <div>
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Branches Management</h2>
            <p class="text-xs text-gray-600 dark:text-gray-400">Total: {{ $branches->total() }} records</p>
        </div>
    </div>
    <a href="{{ route('admin.branches.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
        <i class='bx bx-plus mr-1.5'></i>
        Create New
    </a>
</div>

<!-- Branches Table -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Branch Name
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        TI Agent Code
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Email
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        State
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Region
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($branches as $branch)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-xs font-semibold text-gray-900 dark:text-white">
                            {{ $branch->branch_name }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-xs text-gray-900 dark:text-white">
                            {{ $branch->ti_agent_code }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-xs text-gray-900 dark:text-white">
                            {{ $branch->email }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-xs text-gray-600 dark:text-gray-400">
                            {{ $branch->state }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <div class="text-xs text-gray-600 dark:text-gray-400">
                            {{ $branch->region }}
                        </div>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-right text-xs font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('admin.branches.show', $branch->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                <i class='bx bx-show'></i>
                            </a>
                            <a href="{{ route('admin.branches.edit', $branch->id) }}" class="text-orange-600 dark:text-orange-400 hover:text-orange-900 dark:hover:text-orange-300">
                                <i class='bx bx-edit'></i>
                            </a>
                            <button onclick="deleteBranch({{ $branch->id }})" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class='bx bx-building text-2xl text-gray-400 dark:text-gray-500'></i>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No branches found</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">Get started by creating your first branch</p>
                        <a href="{{ route('admin.branches.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                            <i class='bx bx-plus mr-1.5'></i>
                            Create First Branch
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($branches->hasPages())
    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
        {{ $branches->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function deleteBranch(branchId) {
    if (confirm('Are you sure you want to delete this branch? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('admin.branches.destroy', ':id') }}`.replace(':id', branchId);
        
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
