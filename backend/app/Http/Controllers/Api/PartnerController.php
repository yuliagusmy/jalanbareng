<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    /**
     * GET /api/partners
     * Public: list all active partners, grouped by category.
     */
    public function index()
    {
        $partners = Partner::active()->get();

        return response()->json([
            'data'    => $partners,
            'message' => 'Partners retrieved successfully',
        ]);
    }

    /**
     * POST /api/admin/partners
     * Admin: create a new partner.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|in:brand,government,bumn,community,media',
            'role'        => 'nullable|string|max:255',
            'collab_type' => 'nullable|string|max:255',
            'initial'     => 'nullable|string|max:10',
            'logo_url'    => 'nullable|url|max:1000',
            'bg_color'    => 'nullable|string|max:20',
            'text_color'  => 'nullable|string|max:20',
            'website_url' => 'nullable|url|max:1000',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        $partner = Partner::create($validated);

        return response()->json([
            'data'    => $partner,
            'message' => 'Partner berhasil ditambahkan',
        ], 201);
    }

    /**
     * GET /api/admin/partners/{id}
     * Admin: get single partner detail.
     */
    public function show(Partner $partner)
    {
        return response()->json([
            'data'    => $partner,
            'message' => 'Partner retrieved successfully',
        ]);
    }

    /**
     * PUT /api/admin/partners/{id}
     * Admin: update a partner.
     */
    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'category'    => 'sometimes|required|in:brand,government,bumn,community,media',
            'role'        => 'nullable|string|max:255',
            'collab_type' => 'nullable|string|max:255',
            'initial'     => 'nullable|string|max:10',
            'logo_url'    => 'nullable|url|max:1000',
            'bg_color'    => 'nullable|string|max:20',
            'text_color'  => 'nullable|string|max:20',
            'website_url' => 'nullable|url|max:1000',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        $partner->update($validated);

        return response()->json([
            'data'    => $partner->fresh(),
            'message' => 'Partner berhasil diperbarui',
        ]);
    }

    /**
     * DELETE /api/admin/partners/{id}
     * Admin: delete a partner.
     */
    public function destroy(Partner $partner)
    {
        $partner->delete();

        return response()->json([
            'message' => 'Partner berhasil dihapus',
        ]);
    }

    /**
     * GET /api/admin/partners
     * Admin: list all partners (including inactive), with search & filter.
     */
    public function adminIndex(Request $request)
    {
        $query = Partner::query()->orderBy('sort_order')->orderBy('name');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->is_active);
        }

        $partners = $query->get();

        $counts = [
            'total'      => Partner::count(),
            'active'     => Partner::where('is_active', true)->count(),
            'inactive'   => Partner::where('is_active', false)->count(),
            'brand'      => Partner::where('category', 'brand')->count(),
            'government' => Partner::where('category', 'government')->count(),
            'community'  => Partner::where('category', 'community')->count(),
        ];

        return response()->json([
            'data'    => $partners,
            'counts'  => $counts,
            'message' => 'Partners retrieved successfully',
        ]);
    }
}
