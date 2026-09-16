<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\DestinationPhoto;
use App\Support\Geo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DestinationController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Destination::with(['user', 'category', 'photos'])
            ->withCount(['likes', 'comments']);

        // Filter for user's own posts
        if ($request->boolean('my_posts')) {
            $query->where('user_id', $request->user()->id);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by activation
        if ($request->has('activation_id')) {
            $query->where('activation_id', $request->activation_id);
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Get nearby destinations (requires lat/lng)
        if ($request->has('lat') && $request->has('lng') && Geo::usesPostgis()) {
            $lat = $request->lat;
            $lng = $request->lng;
            $radius = $request->get('radius', 10); // km

            $query->whereRaw(
                "ST_DWithin(location::geography, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography, ?)",
                [$lng, $lat, $radius * 1000]
            );
        }

        $perPage = $request->input('per_page', 20);
        $destinations = $query->latest()->paginate($perPage);

        return response()->json($destinations);
    }

    public function myPosts(Request $request)
    {
        // Start query from the authenticated user's destinations relationship
        $query = $request->user()->destinations()->with(['category', 'photos'])
            ->withCount(['likes', 'comments']);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        if ($sort === 'most_liked') {
            $query->orderByDesc('likes_count');
        } elseif ($sort === 'most_commented') {
            $query->orderByDesc('comments_count');
        } elseif ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc'); // Default to newest
        }

        $perPage = $request->input('per_page', 12);
        $destinations = $query->paginate($perPage);

        return response()->json($destinations);
    }


    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'activation_id' => 'nullable|exists:activations,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photos' => 'required|array|min:1|max:5',
            'photos.*' => 'image|max:2048',
            'primary_photo_index' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $destination = Destination::create([
                'user_id' => $request->user()->id,
                'category_id' => $request->category_id,
                'activation_id' => $request->activation_id,
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . time(),
                'description' => $request->description,
                'primary_photo' => '', // Will be updated after photo upload
            ]);

            Geo::setPoint('destinations', 'location', $destination->id, (float) $request->longitude, (float) $request->latitude);

            // Upload photos
            $photos = [];
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('destinations', 'public');

                $destinationPhoto = DestinationPhoto::create([
                    'destination_id' => $destination->id,
                    'photo_path' => $path,
                    'is_primary' => $index == $request->primary_photo_index,
                    'order' => $index,
                ]);

                $photos[] = $destinationPhoto;

                if ($index == $request->primary_photo_index) {
                    $destination->primary_photo = $path;
                    $destination->save();
                }
            }

            DB::commit();

            $destination->load(['user', 'category', 'photos']);

            return response()->json([
                'message' => 'Destination created successfully',
                'destination' => $destination,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create destination',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, Destination $destination)
    {
        $user = $request->user();

        $destination->load([
            'user',
            'category',
            'photos',
            'comments' => function ($query) use ($user) {
                $query->whereNull('parent_id')
                    ->with([
                        'user',
                        'replies.user',
                        'likes' => function ($likeQuery) use ($user) {
                            if ($user) {
                                $likeQuery->where('user_id', $user->id);
                            }
                        },
                        'replies.likes' => function ($likeQuery) use ($user) {
                            if ($user) {
                                $likeQuery->where('user_id', $user->id);
                            }
                        }
                    ])
                    ->withCount('likes')
                    ->latest();
            },
        ])->loadCount(['likes', 'comments']);

        // Add is_liked attribute to comments and replies
        if ($user) {
            $destination->is_liked = $destination->likes()->where('user_id', $user->id)->exists();
            $destination->comments->each(function ($comment) use ($user) {
                $comment->is_liked = $comment->likes()->where('user_id', $user->id)->exists();
                $comment->replies->each(function ($reply) use ($user) {
                    $reply->is_liked = $reply->likes()->where('user_id', $user->id)->exists();
                });
            });
        }


        if (Geo::usesPostgis()) {
            $location = DB::selectOne(
                "SELECT ST_Y(location::geometry) as lat, ST_X(location::geometry) as lng FROM destinations WHERE id = ?",
                [$destination->id]
            );
            $destination->latitude = $location->lat ?? null;
            $destination->longitude = $location->lng ?? null;
        } else {
            $location = $destination->location;
            $destination->latitude = $location['lat'] ?? null;
            $destination->longitude = $location['lng'] ?? null;
        }

        return response()->json([
            'destination' => $destination,
        ]);
    }

    public function update(Request $request, Destination $destination)
    {
        // Check authorization
        if ($destination->user_id !== $request->user()->id && !$request->user()->canModerateContent()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'activation_id' => 'nullable|exists:activations,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
        ]);

        DB::beginTransaction();
        try {
            $destination->fill($request->only(['category_id', 'activation_id', 'name', 'description']));

            if ($request->has('name')) {
                $destination->slug = Str::slug($request->name) . '-' . $destination->id;
            }

            // Update location if provided
            if ($request->has('latitude') && $request->has('longitude')) {
                Geo::setPoint('destinations', 'location', $destination->id, (float) $request->longitude, (float) $request->latitude);
            }

            $destination->save();

            DB::commit();

            $destination->load(['user', 'category', 'photos']);

            return response()->json([
                'message' => 'Destination updated successfully',
                'destination' => $destination,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update destination',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, Destination $destination)
    {
        // Check authorization
        if ($destination->user_id !== $request->user()->id && !$request->user()->canModerateContent()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete photos from storage
        foreach ($destination->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        $destination->delete();

        return response()->json([
            'message' => 'Destination deleted successfully',
        ]);
    }

    public function nearby(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:50', // km
        ]);

        $lat = (float) $request->latitude;
        $lng = (float) $request->longitude;
        $radiusKm = (float) $request->get('radius', 5);

        $query = Destination::with(['user', 'category', 'photos'])
            ->withCount(['likes', 'comments']);

        if (Geo::usesPostgis()) {
            $query->whereRaw(
                "ST_DWithin(location::geography, ST_SetSRID(ST_MakePoint(?, ?), 4326)::geography, ?)",
                [$lng, $lat, $radiusKm * 1000]
            );
            $destinations = $query->get();
        } else {
            $destinations = $query->get()->filter(function (Destination $destination) use ($lat, $lng, $radiusKm) {
                $location = $destination->location;
                if (! is_array($location)) {
                    return false;
                }

                return Geo::haversineKm($lat, $lng, $location['lat'], $location['lng']) <= $radiusKm;
            })->values();
        }

        return response()->json([
            'destinations' => $destinations,
            'radius_km' => $radiusKm,
        ]);
    }

    public function map()
    {
        if (Geo::usesPostgis()) {
            $destinations = DB::select("
                SELECT
                    d.id,
                    d.name,
                    d.slug,
                    d.primary_photo,
                    d.activation_id,
                    c.name as category_name,
                    ST_Y(d.location::geometry) as latitude,
                    ST_X(d.location::geometry) as longitude,
                    d.likes_count,
                    d.comments_count
                FROM destinations d
                LEFT JOIN categories c ON d.category_id = c.id
                ORDER BY d.created_at DESC
            ");
        } else {
            $destinations = Destination::with('category')
                ->latest()
                ->get()
                ->map(function (Destination $destination) {
                    $location = $destination->location;

                    return [
                        'id' => $destination->id,
                        'name' => $destination->name,
                        'slug' => $destination->slug,
                        'primary_photo' => $destination->primary_photo,
                        'activation_id' => $destination->activation_id,
                        'category_name' => $destination->category?->name,
                        'latitude' => $location['lat'] ?? null,
                        'longitude' => $location['lng'] ?? null,
                        'likes_count' => $destination->likes_count,
                        'comments_count' => $destination->comments_count,
                    ];
                });
        }

        return response()->json([
            'destinations' => $destinations,
        ]);
    }
}
