<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentController extends Controller
{
    public function index()
    {
        $pages = ContentPage::with(['creator', 'updater'])->latest()->paginate(10);
        return view('admin.content.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.content.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'page_type' => 'required|string|in:page,blog,announcement',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $page = ContentPage::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'page_type' => $request->page_type,
            'is_published' => $request->boolean('is_published'),
            'is_featured' => $request->boolean('is_featured'),
            'seo_settings' => $request->seo_settings ?? [],
            'created_by' => auth()->id(),
            'published_at' => $request->boolean('is_published') ? now() : null,
        ]);

        return redirect()->route('admin.content.index')->with('success', 'Content page created successfully!');
    }

    public function show(ContentPage $page)
    {
        $page->load(['creator', 'updater']);
        return view('admin.content.show', compact('page'));
    }

    public function edit(ContentPage $page)
    {
        return view('admin.content.edit', compact('page'));
    }

    public function update(Request $request, ContentPage $page)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'page_type' => 'required|string|in:page,blog,announcement',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $page->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->content,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'page_type' => $request->page_type,
            'is_published' => $request->boolean('is_published'),
            'is_featured' => $request->boolean('is_featured'),
            'seo_settings' => $request->seo_settings ?? [],
            'updated_by' => auth()->id(),
            'published_at' => $request->boolean('is_published') && !$page->published_at ? now() : $page->published_at,
        ]);

        return redirect()->route('admin.content.index')->with('success', 'Content page updated successfully!');
    }

    public function destroy(ContentPage $page)
    {
        $page->delete();
        return redirect()->route('admin.content.index')->with('success', 'Content page deleted successfully!');
    }

    public function toggleStatus(ContentPage $page)
    {
        $page->update([
            'is_published' => !$page->is_published,
            'published_at' => !$page->is_published ? now() : null,
        ]);
        
        return response()->json([
            'success' => true,
            'is_published' => $page->is_published,
        ]);
    }

    public function toggleFeatured(ContentPage $page)
    {
        $page->update(['is_featured' => !$page->is_featured]);
        
        return response()->json([
            'success' => true,
            'is_featured' => $page->is_featured,
        ]);
    }
}