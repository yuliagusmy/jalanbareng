<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of pages (Admin only)
     */
    public function index(Request $request)
    {
        $query = Page::query();

        // Search
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by published status
        if ($request->has('is_published')) {
            $query->where('is_published', $request->is_published);
        }

        // Sort
        $sortBy = $request->input('sort', 'order');
        if ($sortBy === 'newest') {
            $query->latest();
        } elseif ($sortBy === 'oldest') {
            $query->oldest();
        } elseif ($sortBy === 'title') {
            $query->orderBy('title', 'asc');
        } else {
            $query->orderBy('order', 'asc');
        }

        $perPage = $request->input('per_page', 20);
        $pages = $query->paginate($perPage);

        return response()->json($pages);
    }

    /**
     * Display the specified page by slug (Public)
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->published()
            ->firstOrFail();

        return response()->json([
            'page' => $page
        ]);
    }

    /**
     * Store a newly created page
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:pages,slug',
            'content' => 'required|string',
            'meta_description' => 'nullable|string|max:255',
            'is_published' => 'sometimes|boolean',
            'order' => 'sometimes|integer|min:0',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $page = Page::create($validated);

        return response()->json([
            'message' => 'Page created successfully',
            'page' => $page
        ], 201);
    }

    /**
     * Update the specified page
     */
    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:pages,slug,' . $id,
            'content' => 'sometimes|string',
            'meta_description' => 'nullable|string|max:255',
            'is_published' => 'sometimes|boolean',
            'order' => 'sometimes|integer|min:0',
        ]);

        // Auto-generate slug if title changed but slug not provided
        if (isset($validated['title']) && !isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $page->update($validated);

        return response()->json([
            'message' => 'Page updated successfully',
            'page' => $page
        ]);
    }

    /**
     * Remove the specified page
     */
    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        $page->delete();

        return response()->json([
            'message' => 'Page deleted successfully'
        ]);
    }

    /**
     * Get published pages for footer (Public)
     */
    public function footer()
    {
        $pages = Page::published()
            ->ordered()
            ->select('id', 'title', 'slug')
            ->get();

        return response()->json([
            'pages' => $pages
        ]);
    }
}
