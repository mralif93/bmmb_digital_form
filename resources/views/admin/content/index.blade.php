@extends('layouts.admin-minimal')

@section('title', 'Content Management - BMMB Digital Forms')
@section('page-title', 'Content Management')
@section('page-description', 'Manage website content and pages')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Content Management</h1>
            <p class="text-gray-600 dark:text-gray-400">Create and manage website content, pages, and announcements</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.content.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class='bx bx-plus mr-2'></i>
                Create New Content
            </a>
        </div>
    </div>

    <!-- Content Types Filter -->
    <div class="flex space-x-2">
        <button onclick="filterContent('all')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400">
            All Content
        </button>
        <button onclick="filterContent('page')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
            Pages
        </button>
        <button onclick="filterContent('blog')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
            Blog Posts
        </button>
        <button onclick="filterContent('announcement')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
            Announcements
        </button>
    </div>

    <!-- Content List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pages as $page)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @if($page->is_featured)
                                    <i class='bx bx-star text-orange-500'></i>
                                    @else
                                    <i class='bx bx-file-blank text-gray-400'></i>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $page->title }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $page->slug }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $page->page_type === 'page' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                {{ $page->page_type === 'blog' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                {{ $page->page_type === 'announcement' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' : '' }}">
                                {{ ucfirst($page->page_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $page->is_published ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                {{ $page->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $page->creator->name ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $page->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.content.show', $page) }}" class="text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400">
                                    <i class='bx bx-show'></i>
                                </a>
                                <a href="{{ route('admin.content.edit', $page) }}" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                                    <i class='bx bx-edit'></i>
                                </a>
                                <button onclick="toggleStatus({{ $page->id }})" class="text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400">
                                    <i class='bx bx-power-off'></i>
                                </button>
                                <button onclick="toggleFeatured({{ $page->id }})" class="text-gray-600 dark:text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400">
                                    <i class='bx bx-star'></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class='bx bx-file-blank text-2xl text-gray-400'></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No content created yet</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Get started by creating your first content page</p>
                                <a href="{{ route('admin.content.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    <i class='bx bx-plus mr-2'></i>
                                    Create Your First Content
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($pages->hasPages())
    <div class="mt-6">
        {{ $pages->links() }}
    </div>
    @endif
</div>

<script>
function filterContent(type) {
    // Update filter buttons
    document.querySelectorAll('[onclick^="filterContent"]').forEach(btn => {
        btn.className = 'px-4 py-2 text-sm font-medium rounded-lg transition-colors text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700';
    });
    
    event.target.className = 'px-4 py-2 text-sm font-medium rounded-lg transition-colors bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400';
    
    // Filter table rows
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        if (type === 'all') {
            row.style.display = '';
        } else {
            const typeCell = row.querySelector('td:nth-child(2) span');
            if (typeCell && typeCell.textContent.toLowerCase().includes(type)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}

function toggleStatus(pageId) {
    fetch(`/admin/content/${pageId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error toggling status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error toggling status');
    });
}

function toggleFeatured(pageId) {
    fetch(`/admin/content/${pageId}/toggle-featured`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error toggling featured status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error toggling featured status');
    });
}
</script>
@endsection


