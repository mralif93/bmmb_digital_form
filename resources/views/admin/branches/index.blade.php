@extends('layouts.admin-minimal')

@section('title', 'Branches Management - BMMB Digital Forms')
@section('page-title', 'Branches Management')
@section('page-description', 'Manage all branch records')

@section('content')
    @if(session('success'))
        <div
            class="mb-4 p-3 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg text-sm text-green-800 dark:text-green-400">
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
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.branches.trashed') }}"
                class="inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 text-xs font-semibold rounded-lg transition-colors">
                <i class='bx bx-trash mr-1.5'></i>
                Trashed Branches
            </a>
            <button onclick="confirmResync('{{ route('admin.branches.resync') }}')"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors">
                <i class='bx bx-refresh mr-1.5'></i>
                Resync
            </button>
            <a href="{{ route('admin.branches.create') }}"
                class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                <i class='bx bx-plus mr-1.5'></i>
                Create New
            </a>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="mb-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
        <form method="GET" action="{{ route('admin.branches.index') }}" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <!-- Search Input -->
                <div class="md:col-span-2">
                    <label for="search"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                    <div class="relative">
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Search by name, code, email, address, state, or region..."
                            class="w-full pl-10 pr-4 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <i
                            class='bx bx-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500'></i>
                    </div>
                </div>

                <!-- State Filter -->
                <div>
                    <label for="state" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">State</label>
                    <select name="state" id="state"
                        class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All States</option>
                        @foreach(\App\Models\State::orderBy('name')->get() as $state)
                            <option value="{{ $state->id }}" {{ request('state') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Region Filter -->
                <div>
                    <label for="region"
                        class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Region</label>
                    <select name="region" id="region"
                        class="w-full px-3 py-2 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Regions</option>
                        @foreach(\App\Models\Region::orderBy('name')->get() as $region)
                            <option value="{{ $region->id }}" {{ request('region') == $region->id ? 'selected' : '' }}>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-2">
                @if(request()->hasAny(['search', 'state', 'region']))
                    <a href="{{ route('admin.branches.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 text-xs font-semibold rounded-lg transition-colors">
                        <i class='bx bx-x mr-1.5'></i>
                        Clear Filters
                    </a>
                @endif
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
                    <i class='bx bx-search mr-1.5'></i>
                    Search
                </button>
            </div>
        </form>
    </div>

    <!-- Branches Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Branch Name
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            TI Agent Code
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Email
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            State
                        </th>
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                            Region
                        </th>
                        <th
                            class="px-4 py-3 text-right text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
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
                                    <a href="{{ route('admin.branches.show', $branch->id) }}"
                                        class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 rounded-lg text-xs transition-colors">
                                        View
                                    </a>
                                    <a href="{{ route('admin.branches.edit', $branch->id) }}"
                                        class="inline-flex items-center justify-center px-3 py-1.5 bg-orange-100 hover:bg-orange-200 text-orange-700 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 dark:text-orange-400 rounded-lg text-xs transition-colors">
                                        Edit
                                    </a>
                                    <button onclick="confirmRegenerateQr('{{ route('admin.branches.regenerate-qr', $branch->id) }}')"
                                        class="inline-flex items-center justify-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 dark:bg-green-900/30 dark:hover:bg-green-900/50 dark:text-green-400 rounded-lg text-xs transition-colors"
                                        title="Regenerate QR Code">
                                        <i class='bx bx-qr'></i>
                                    </button>
                                    <button onclick="deleteBranch({{ $branch->id }})"
                                        class="inline-flex items-center justify-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 dark:bg-red-900/30 dark:hover:bg-red-900/50 dark:text-red-400 rounded-lg text-xs transition-colors">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center">
                                <div
                                    class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class='bx bx-building text-2xl text-gray-400 dark:text-gray-500'></i>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">No branches found</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">Get started by creating your first
                                    branch</p>
                                <a href="{{ route('admin.branches.create') }}"
                                    class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-semibold rounded-lg transition-colors">
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
                Swal.fire({
                    title: 'Delete Branch?',
                    html: `
                            <div class="text-center">
                                <p class="mb-2">Are you sure you want to delete this branch?</p>
                                <p class="text-sm text-gray-600">This action will move the branch to the trash.</p>
                            </div>
                        `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Delete',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    customClass: {
                        popup: 'rounded-lg',
                        htmlContainer: 'text-center',
                        confirmButton: 'rounded-lg',
                        cancelButton: 'rounded-lg'
                    },
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
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
                });
            }

            function confirmResync(route) {
                Swal.fire({
                    title: 'Resync with MAP?',
                    html: `
                                <div class="text-center">
                                    <p class="mb-2">Are you sure you want to resync data from the MAP database?</p>
                                    <p class="text-sm text-gray-600">This process might take a while depending on the amount of data.</p>
                                </div>
                            `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Resync',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280',
                    customClass: {
                        popup: 'rounded-lg',
                        htmlContainer: 'text-center',
                        confirmButton: 'rounded-lg',
                        cancelButton: 'rounded-lg'
                    },
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = route;

                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = '{{ csrf_token() }}';
                        form.appendChild(csrf);

                        document.body.appendChild(form);
                        form.submit();

                        Swal.fire({
                            title: 'Syncing...',
                            text: 'Please wait while we sync the data.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    }
                });
            }

            function confirmRegenerateQr(route) {
                Swal.fire({
                    title: 'Regenerate QR Code?',
                    html: `
                                <div class="text-center">
                                    <p class="mb-2">Are you sure you want to regenerate the QR Code for this branch?</p>
                                    <p class="text-sm text-red-600 font-semibold">This will invalidate the existing QR Code immediately.</p>
                                </div>
                            `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Regenerate',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#f97316',
                    cancelButtonColor: '#6b7280',
                    customClass: {
                        popup: 'rounded-lg',
                        htmlContainer: 'text-center',
                        confirmButton: 'rounded-lg',
                        cancelButton: 'rounded-lg'
                    },
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = route;

                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = '{{ csrf_token() }}';
                        form.appendChild(csrf);

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
        </script>
    @endpush
@endsection