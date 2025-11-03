@extends('layouts.admin-minimal')

@section('title', 'Create Content - BMMB Digital Forms')
@section('page-title', 'Create New Content')
@section('page-description', 'Create a new content page, blog post, or announcement')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.content.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Content Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Content Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                           placeholder="Enter content title">
                </div>
                
                <div>
                    <label for="page_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Content Type <span class="text-red-500">*</span>
                    </label>
                    <select id="page_type" name="page_type" required
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select content type</option>
                        <option value="page">Page</option>
                        <option value="blog">Blog Post</option>
                        <option value="announcement">Announcement</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Content Editor -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Content</h3>
            
            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Content <span class="text-red-500">*</span>
                </label>
                <textarea id="content" name="content" rows="15" required
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                          placeholder="Enter your content here..."></textarea>
            </div>
            
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <p>You can use HTML tags for formatting. Common tags:</p>
                <ul class="list-disc list-inside mt-2 space-y-1">
                    <li><code>&lt;h1&gt;</code> to <code>&lt;h6&gt;</code> for headings</li>
                    <li><code>&lt;p&gt;</code> for paragraphs</li>
                    <li><code>&lt;strong&gt;</code> or <code>&lt;b&gt;</code> for bold text</li>
                    <li><code>&lt;em&gt;</code> or <code>&lt;i&gt;</code> for italic text</li>
                    <li><code>&lt;ul&gt;</code> and <code>&lt;ol&gt;</code> for lists</li>
                    <li><code>&lt;a href="url"&gt;</code> for links</li>
                </ul>
            </div>
        </div>

        <!-- SEO Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">SEO Settings</h3>
            
            <div class="space-y-4">
                <div>
                    <label for="meta_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Meta Title
                    </label>
                    <input type="text" id="meta_title" name="meta_title"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                           placeholder="SEO title (max 60 characters)">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave empty to use the main title</p>
                </div>
                
                <div>
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Meta Description
                    </label>
                    <textarea id="meta_description" name="meta_description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white"
                              placeholder="SEO description (max 160 characters)"></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Brief description for search engines</p>
                </div>
            </div>
        </div>

        <!-- Publishing Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Publishing Settings</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_published" value="1"
                               class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Publish immediately</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="is_featured" value="1"
                               class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Mark as featured</span>
                    </label>
                </div>
                
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <p><strong>Publish immediately:</strong> Makes the content visible to visitors</p>
                    <p><strong>Mark as featured:</strong> Highlights this content on the website</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.content.index') }}" class="px-6 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition-colors">
                Create Content
            </button>
        </div>
    </form>
</div>

<script>
// Auto-generate slug from title
document.getElementById('title').addEventListener('input', function() {
    const title = this.value;
    const slug = title.toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim('-');
    
    // You could add a slug field if needed
    console.log('Generated slug:', slug);
});

// Character counters for SEO fields
document.getElementById('meta_title').addEventListener('input', function() {
    const length = this.value.length;
    const maxLength = 60;
    
    if (length > maxLength) {
        this.style.borderColor = '#ef4444';
    } else {
        this.style.borderColor = '';
    }
});

document.getElementById('meta_description').addEventListener('input', function() {
    const length = this.value.length;
    const maxLength = 160;
    
    if (length > maxLength) {
        this.style.borderColor = '#ef4444';
    } else {
        this.style.borderColor = '';
    }
});
</script>
@endsection


