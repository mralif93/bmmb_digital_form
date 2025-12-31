@extends('layouts.admin-minimal')

@section('title', 'View Branch - BMMB Digital Forms')
@section('page-title', 'Branch Details: ' . $branch->branch_name)
@section('page-description', 'View branch information')

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <div></div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.branches.index') }}"
                class="inline-flex items-center justify-center px-3 py-2 text-xs font-semibold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                <i class='bx bx-arrow-back mr-1.5'></i>
                Back to List
            </a>
            <a href="{{ route('admin.branches.edit', $branch->id) }}"
                class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors">
                <i class='bx bx-edit mr-1.5'></i>
                Edit Branch
            </a>
            <button onclick="deleteBranch({{ $branch->id }})"
                class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                <i class='bx bx-trash mr-1.5'></i>
                Delete Branch
            </button>
        </div>
    </div>

    @if(session('success'))
        <div
            class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Branch Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class='bx bx-info-circle mr-2 text-primary-600 dark:text-primary-400'></i>
                    Branch Information
                </h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Branch Name
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white font-semibold">
                            {{ $branch->branch_name }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            TI Agent Code
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            {{ $branch->ti_agent_code }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Weekend Start Day
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            {{ $branch->weekend_start_day }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Email
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            <a href="mailto:{{ $branch->email }}"
                                class="text-primary-600 dark:text-primary-400 hover:underline">
                                {{ $branch->email }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            State
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            {{ $branch->state }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Region
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            {{ $branch->region }}
                        </dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Address
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            {{ $branch->address }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Created At
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            {{ $timezoneHelper->convert($branch->created_at)?->format($dateFormat . ' ' . $timeFormat) }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            Updated At
                        </dt>
                        <dd class="text-sm text-gray-900 dark:text-white">
                            {{ $timezoneHelper->convert($branch->updated_at)?->format($dateFormat . ' ' . $timeFormat) }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.branches.edit', $branch->id) }}"
                        class="block w-full text-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                        <i class='bx bx-edit mr-1.5'></i>
                        Edit Branch
                    </a>
                    <button onclick="deleteBranch({{ $branch->id }})"
                        class="block w-full text-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition-colors">
                        <i class='bx bx-trash mr-1.5'></i>
                        Delete Branch
                    </button>
                </div>
            </div>
        </div>
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