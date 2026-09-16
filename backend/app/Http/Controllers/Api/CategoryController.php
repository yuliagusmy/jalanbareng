<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('destinations')->get();

        return response()->json([
            'categories' => $categories,
        ]);
    }

    public function show(Category $category)
    {
        $category->load(['destinations' => function ($query) {
            $query->with(['user', 'photos'])
                ->withCount(['likes', 'comments'])
                ->latest()
                ->take(10);
        }]);

        return response()->json([
            'category' => $category,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('categories', 'public');
            $validated['photo'] = $path;
        }

        $category = Category::create($validated);

        // Reload category with photo_url and destinations_count
        $category = Category::withCount('destinations')->find($category->id);

        return response()->json([
            'message' => 'Kategori berhasil ditambahkan',
            'category' => $category
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        // Update slug if name changed
        if ($validated['name'] !== $category->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($category->photo && Storage::disk('public')->exists($category->photo)) {
                Storage::disk('public')->delete($category->photo);
            }

            $path = $request->file('photo')->store('categories', 'public');
            $validated['photo'] = $path;
        }

        $category->update($validated);

        // Reload category with photo_url
        $category = Category::withCount('destinations')->find($category->id);

        return response()->json([
            'message' => 'Kategori berhasil diupdate',
            'category' => $category
        ]);
    }

    public function destroy(Category $category)
    {
        // Check if category has destinations
        if ($category->destinations()->count() > 0) {
            return response()->json([
                'message' => 'Kategori tidak bisa dihapus karena masih memiliki destinasi'
            ], 400);
        }

        // Delete photo
        if ($category->photo && Storage::disk('public')->exists($category->photo)) {
            Storage::disk('public')->delete($category->photo);
        }

        $category->delete();

        return response()->json([
            'message' => 'Kategori berhasil dihapus'
        ]);
    }
}
