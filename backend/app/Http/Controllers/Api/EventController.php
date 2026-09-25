<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventPhoto;
use App\Support\Geo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['user', 'photos'])
            ->withCount(['likes', 'comments', 'participants']);

        // Filter by upcoming/past events
        if ($request->has('status')) {
            if ($request->status === 'upcoming') {
                $query->where('date', '>=', now());
            } elseif ($request->status === 'past') {
                $query->where('date', '<', now());
            }
        }

        // Filter by activation
        if ($request->has('activation_id')) {
            $query->where('activation_id', $request->activation_id);
        }

        // Filter by featured
        if ($request->has('featured')) {
            $query->where('is_featured', $request->featured);
        }

        // Filter by walking events only
        if ($request->has('walking_only')) {
            $query->where('type', 'walking');
        }

        $perPage = $request->input('per_page', 20);
        $events = $query->latest('date')->paginate($perPage);

        return response()->json($events);
    }

    public function store(Request $request)
    {
        // Check if user can create events
        if (!$request->user()->canCreateEvents()) {
            return response()->json([
                'message' => 'Only admins and community admins can create events'
            ], 403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'poster' => 'required|image|max:2048',
            'youtube_link' => 'nullable|url',
            'registration_link' => 'required|url',
            'type' => 'required|in:walking,regular',
            'is_featured' => 'sometimes|boolean',
            'activation_id' => 'nullable|exists:activations,id',
        ];

        // Add walking event specific validation
        if ($request->type === 'walking') {
            $rules['start_lat'] = 'required|numeric|between:-90,90';
            $rules['start_lng'] = 'required|numeric|between:-180,180';
            $rules['finish_lat'] = 'required|numeric|between:-90,90';
            $rules['finish_lng'] = 'required|numeric|between:-180,180';
            $rules['distance'] = 'required|numeric|min:0';
            $rules['duration'] = 'nullable|integer|min:1';
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $posterPath = null;
            if ($request->hasFile('poster')) {
                $posterPath = $request->file('poster')->store('events', 'public');
            }

            $eventDateTime = $request->date . ' ' . $request->time;

            $event = Event::create([
                'user_id' => $request->user()->id,
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . time(),
                'type' => $request->type,
                'description' => $request->description,
                'date' => $eventDateTime,
                'time' => $request->time,
                'youtube_link' => $request->youtube_link,
                'registration_link' => $request->registration_link,
                'is_featured' => $request->user()->isAdmin() ? ($request->is_featured ?? false) : false,
                'distance' => $request->distance,
                'estimated_duration' => $request->duration,
                'poster' => $posterPath,
                'activation_id' => $request->activation_id,
            ]);

            if ($request->type === 'walking') {
                Geo::setPoint('events', 'start_point', $event->id, (float) $request->start_lng, (float) $request->start_lat);
                Geo::setPoint('events', 'finish_point', $event->id, (float) $request->finish_lng, (float) $request->finish_lat);

                if ($request->has('route')) {
                    $routePoints = json_decode($request->route, true);
                    if (is_array($routePoints) && count($routePoints) > 1) {
                        $lineString = 'LINESTRING(' . implode(',', array_map(function ($point) {
                            return $point['lng'] . ' ' . $point['lat'];
                        }, $routePoints)) . ')';
                        Geo::setLineString('events', 'route', $event->id, $lineString);
                    }
                }
            }

            if ($posterPath) {
                EventPhoto::create([
                    'event_id' => $event->id,
                    'photo_path' => $posterPath,
                    'is_poster' => true,
                    'order' => 0,
                ]);
            }

            DB::commit();

            $event->load(['user', 'photos']);

            return response()->json([
                'message' => 'Event created successfully',
                'event' => $event,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Event $event)
    {
        $event->load([
            'user',
            'activation',
            'photos',
            'comments' => function ($query) {
                $query->whereNull('parent_id')
                    ->with(['user', 'replies.user'])
                    ->withCount('likes')
                    ->latest();
            },
            'participants' => function ($query) {
                $query->latest();
            }
        ])->loadCount(['likes', 'comments', 'participants']);

        if ($event->type === 'walking') {
            if (Geo::usesPostgis()) {
                $geoData = DB::selectOne("
                    SELECT
                        ST_Y(start_point::geometry) as start_lat,
                        ST_X(start_point::geometry) as start_lng,
                        ST_Y(finish_point::geometry) as finish_lat,
                        ST_X(finish_point::geometry) as finish_lng,
                        ST_AsText(route::geometry) as route_text
                    FROM events
                    WHERE id = ?
                ", [$event->id]);

                $event->start_point = [
                    'lat' => $geoData->start_lat,
                    'lng' => $geoData->start_lng,
                ];
                $event->finish_point = [
                    'lat' => $geoData->finish_lat,
                    'lng' => $geoData->finish_lng,
                ];
                $event->route = Geo::parseLineString($geoData->route_text ?? null);
            }
        }

        return response()->json([
            'event' => $event,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        // Check authorization
        if ($event->user_id !== $request->user()->id && !$request->user()->canModerateContent()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $rules = [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'date' => 'sometimes|date_format:Y-m-d',
            'time' => 'sometimes|date_format:H:i',
            'poster' => 'nullable|image|max:2048',
            'youtube_link' => 'nullable|url',
            'registration_link' => 'sometimes|url',
            'type' => 'sometimes|in:walking,regular',
            'is_featured' => 'sometimes|boolean',
            'activation_id' => 'nullable|exists:activations,id',
        ];

        if ($request->input('type', $event->type) === 'walking') {
            $rules['start_lat'] = 'sometimes|required|numeric|between:-90,90';
            $rules['start_lng'] = 'sometimes|required|numeric|between:-180,180';
            $rules['finish_lat'] = 'sometimes|required|numeric|between:-90,90';
            $rules['finish_lng'] = 'sometimes|required|numeric|between:-180,180';
            $rules['distance'] = 'sometimes|required|numeric|min:0';
            $rules['duration'] = 'nullable|integer|min:1';
        }

        $validatedData = $request->validate($rules);

        DB::beginTransaction();
        try {
            // Handle poster upload
            if ($request->hasFile('poster')) {
                // Delete old poster
                if ($event->poster) {
                    Storage::disk('public')->delete($event->poster);
                }
                // Store new poster
                $validatedData['poster'] = $request->file('poster')->store('events', 'public');
                
                // Update event photo record
                $eventPhoto = $event->photos()->where('is_poster', true)->first();
                if ($eventPhoto) {
                    $eventPhoto->update(['photo_path' => $validatedData['poster']]);
                } else {
                    EventPhoto::create([
                        'event_id' => $event->id,
                        'photo_path' => $validatedData['poster'],
                        'is_poster' => true,
                        'order' => 0,
                    ]);
                }
            }

            // Combine date and time
            if (isset($validatedData['date']) && isset($validatedData['time'])) {
                $validatedData['date'] = $validatedData['date'] . ' ' . $validatedData['time'];
            }
            
            // Map duration to estimated_duration
            if (isset($validatedData['duration'])) {
                $validatedData['estimated_duration'] = $validatedData['duration'];
                unset($validatedData['duration']);
            }

            $event->fill($validatedData);

            if ($request->has('name')) {
                $event->slug = Str::slug($request->name) . '-' . $event->id;
            }

            // Only admins can set featured status
            if ($request->has('is_featured') && $request->user()->isAdmin()) {
                $event->is_featured = $request->is_featured;
            }

            $event->save();

            if ($event->type === 'walking') {
                if ($request->has(['start_lat', 'start_lng'])) {
                    Geo::setPoint('events', 'start_point', $event->id, (float) $request->start_lng, (float) $request->start_lat);
                }
                if ($request->has(['finish_lat', 'finish_lng'])) {
                    Geo::setPoint('events', 'finish_point', $event->id, (float) $request->finish_lng, (float) $request->finish_lat);
                }

                if ($request->has('route')) {
                    $routePoints = json_decode($request->route, true);
                    if (is_array($routePoints) && count($routePoints) > 1) {
                        $lineString = 'LINESTRING(' . implode(',', array_map(function ($point) {
                            return $point['lng'] . ' ' . $point['lat'];
                        }, $routePoints)) . ')';
                        Geo::setLineString('events', 'route', $event->id, $lineString);
                    } else {
                        Geo::setLineString('events', 'route', $event->id, null);
                    }
                }
            }
            DB::commit();

            $event->load(['user', 'photos']);

            return response()->json([
                'message' => 'Event updated successfully',
                'event' => $event,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, Event $event)
    {
        // Check authorization
        if ($event->user_id !== $request->user()->id && !$request->user()->canModerateContent()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete photos from storage
        foreach ($event->photos as $photo) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully',
        ]);
    }

    public function join(Request $request, Event $event)
    {
        $user = $request->user();

        if ($event->participants()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'You have already joined this event',
            ], 400);
        }

        $event->participants()->attach($user->id);
        $event->increment('participants_count');

        return response()->json([
            'message' => 'Successfully joined the event',
        ]);
    }

    public function leave(Request $request, Event $event)
    {
        $user = $request->user();

        if (!$event->participants()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'You are not a participant of this event',
            ], 400);
        }

        $event->participants()->detach($user->id);
        $event->decrement('participants_count');

        return response()->json([
            'message' => 'Successfully left the event',
        ]);
    }

    public function featured()
    {
        $events = Event::with(['user', 'photos'])
            ->where('is_featured', true)
            ->where('date', '>=', now())
            ->withCount(['likes', 'comments', 'participants'])
            ->latest('date')
            ->take(3)
            ->get();

        return response()->json([
            'events' => $events,
        ]);
    }
}
