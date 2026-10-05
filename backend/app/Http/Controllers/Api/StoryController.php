<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Story;
use App\Models\PointTransaction;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoryController extends Controller
{
    /**
     * Public: List all approved stories (with optional pagination or limit)
     */
    public function index(Request $request)
    {
        $query = Story::published()->latest('published_at');

        if ($request->has('featured') && $request->boolean('featured')) {
            $query->featured();
        }

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        if ($request->has('limit')) {
            $limit = min((int) $request->input('limit', 6), 50);
            $stories = $query->take($limit)->get();
            return response()->json([
                'stories' => $stories,
                'total' => $stories->count(),
            ]);
        }

        $perPage = (int) $request->input('per_page', 12);
        $stories = $query->paginate($perPage);

        return response()->json($stories);
    }

    /**
     * Public: Get single approved story detail by slug
     */
    public function show(Request $request, $slug)
    {
        $story = Story::where('slug', $slug)->first();

        if (!$story) {
            return response()->json(['message' => 'Tulisan tidak ditemukan'], 404);
        }

        // If not approved, only authenticated admin or author can view
        if ($story->status !== 'approved') {
            $user = $request->user('sanctum');
            $isAdmin = $user && in_array($user->role?->name, ['admin', 'community_admin']);
            $isAuthor = $user && $story->user_id === $user->id;

            if (!$isAdmin && !$isAuthor) {
                return response()->json(['message' => 'Tulisan ini sedang dalam proses kurasi'], 403);
            }
        } else {
            // Increment view count
            $story->increment('views_count');
        }

        // Related / other approved stories
        $relatedStories = Story::published()
            ->where('id', '!=', $story->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return response()->json([
            'story' => $story,
            'related' => $relatedStories,
        ]);
    }

    /**
     * Public / Authenticated: Submit a new story for admin curation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'author_name' => 'required|string|max:255',
            'author_email' => 'nullable|email|max:255',
            'author_bio' => 'nullable|string|max:500',
            'author_instagram' => 'nullable|string|max:100',
            'card_style' => 'nullable|string|in:coral,magenta,amber,photo,dark',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $user = $request->user('sanctum');
        if ($user) {
            $validated['user_id'] = $user->id;
            if (empty($validated['author_name'])) {
                $validated['author_name'] = $user->name;
            }
            if (empty($validated['author_email'])) {
                $validated['author_email'] = $user->email;
            }
        }

        // Default card style
        if (empty($validated['card_style'])) {
            $styles = ['coral', 'magenta', 'amber', 'photo'];
            $validated['card_style'] = $styles[array_rand($styles)];
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('stories', 'public');
            $validated['cover_image'] = $path;
        }

        $validated['status'] = 'pending';
        $validated['views_count'] = 0;

        // Sanitize rich-text content to prevent Stored XSS attacks
        $validated['content'] = self::sanitizeHtml($validated['content']);

        $story = Story::create($validated);

        return response()->json([
            'message' => 'Tulisan Anda berhasil dikirim! Tim kurator Jalan Bareng akan meninjau tulisan Anda sebelum dipublikasikan.',
            'story' => $story,
        ], 201);
    }

    /**
     * Admin: List all stories with curation filters and count summaries
     */
    public function adminIndex(Request $request)
    {
        $query = Story::with('user')->latest();

        $status = $request->input('status', 'all');
        if (in_array($status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $status);
        }

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        // Counts for tabs
        $counts = [
            'all' => Story::count(),
            'pending' => Story::where('status', 'pending')->count(),
            'approved' => Story::where('status', 'approved')->count(),
            'rejected' => Story::where('status', 'rejected')->count(),
        ];

        $perPage = (int) $request->input('per_page', 15);
        $stories = $query->paginate($perPage);

        return response()->json([
            'stories' => $stories,
            'counts' => $counts,
        ]);
    }

    /**
     * Admin: Update curation status (approve or reject)
     */
    public function adminUpdateStatus(Request $request, $id)
    {
        $story = Story::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|max:1000',
            'is_featured' => 'nullable|boolean',
            'card_style' => 'nullable|string|in:coral,magenta,amber,photo,dark',
        ]);

        if ($validated['status'] === 'approved' && !$story->published_at) {
            $validated['published_at'] = now();
        }

        $oldStatus = $story->status;
        $story->update($validated);

        // Berikan poin kontributor jika disetujui untuk pertama kali
        if ($validated['status'] === 'approved' && $oldStatus !== 'approved' && $story->user_id) {
            $alreadyAwarded = PointTransaction::where('user_id', $story->user_id)
                ->where('source', 'story_approved')
                ->where('reference_id', $story->id)
                ->exists();

            if (!$alreadyAwarded && $story->user) {
                PointService::addPoints(
                    $story->user,
                    100,
                    'story_approved',
                    $story->id,
                    "Poin kontributor: Tulisan '{$story->title}' disetujui kurator"
                );
            }
        }

        $statusLabels = [
            'approved' => 'disetujui dan dipublikasikan (Poin kontributor telah diberikan)',
            'rejected' => 'ditolak',
            'pending' => 'dikembalikan ke status pending',
        ];

        return response()->json([
            'message' => "Tulisan berhasil {$statusLabels[$story->status]}",
            'story' => $story,
        ]);
    }

    /**
     * Admin: Update story content/metadata
     */
    public function adminUpdate(Request $request, $id)
    {
        $story = Story::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'author_name' => 'required|string|max:255',
            'author_email' => 'nullable|email|max:255',
            'author_bio' => 'nullable|string|max:500',
            'author_instagram' => 'nullable|string|max:100',
            'card_style' => 'nullable|string|in:coral,magenta,amber,photo,dark',
            'is_featured' => 'nullable|boolean',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($story->cover_image && Storage::disk('public')->exists($story->cover_image)) {
                Storage::disk('public')->delete($story->cover_image);
            }
            $path = $request->file('cover_image')->store('stories', 'public');
            $validated['cover_image'] = $path;
        }

        if (isset($validated['content'])) {
            $validated['content'] = self::sanitizeHtml($validated['content']);
        }

        $story->update($validated);

        return response()->json([
            'message' => 'Tulisan berhasil diperbarui',
            'story' => $story,
        ]);
    }

    /**
     * Admin: Delete story
     */
    public function adminDestroy($id)
    {
        $story = Story::findOrFail($id);

        if ($story->cover_image && Storage::disk('public')->exists($story->cover_image)) {
            Storage::disk('public')->delete($story->cover_image);
        }

        $story->delete();

        return response()->json([
            'message' => 'Tulisan berhasil dihapus',
        ]);
    }

    /**
     * Sanitize rich-text HTML input to prevent Stored XSS
     */
    public static function sanitizeHtml(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        // Whitelist standard formatting and editorial tags
        $allowedTags = '<p><br><b><strong><i><em><u><s><strike><h1><h2><h3><h4><h5><h6><ul><ol><li><blockquote><a><img><span><div><figure><figcaption>';
        $clean = strip_tags($content, $allowedTags);

        // Strip inline JavaScript handlers and javascript: URI schemes
        $clean = preg_replace('/\s+on[a-zA-Z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean);
        $clean = preg_replace('/href\s*=\s*("\s*javascript:[^"]*"|\'\s*javascript:[^\']*\'|javascript:[^\s>]+)/i', 'href="#"', $clean);
        $clean = preg_replace('/src\s*=\s*("\s*javascript:[^"]*"|\'\s*javascript:[^\']*\'|javascript:[^\s>]+)/i', 'src=""', $clean);

        return $clean;
    }
}
