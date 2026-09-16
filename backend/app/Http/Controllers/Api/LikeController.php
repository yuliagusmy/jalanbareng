<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'likeable_type' => 'required|in:App\Models\Destination,App\Models\Event,App\Models\Comment',
            'likeable_id' => 'required|integer',
        ]);

        $user = $request->user();

        // Check if already liked
        $existingLike = Like::where('user_id', $user->id)
            ->where('likeable_type', $request->likeable_type)
            ->where('likeable_id', $request->likeable_id)
            ->first();

        if ($existingLike) {
            // Unlike
            $existingLike->delete();

            // Decrement likes count
            $likeable = $existingLike->likeable;
            $likeable->decrement('likes_count');

            return response()->json([
                'message' => 'Unliked successfully',
                'liked' => false,
                'likes_count' => $likeable->likes_count,
            ]);
        } else {
            // Like
            $like = Like::create([
                'user_id' => $user->id,
                'likeable_type' => $request->likeable_type,
                'likeable_id' => $request->likeable_id,
            ]);

            // Increment likes count
            $likeable = $like->likeable;
            $likeable->increment('likes_count');

            // Notify the owner of the likeable item (if not liking own item)
            try {
                $owner = $likeable->user ?? null;
                if ($owner && $owner->id !== $user->id) {
                    $owner->notify(new \App\Notifications\NewLikeNotification($user, $likeable, $request->likeable_type));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Failed to send like notification: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Liked successfully',
                'liked' => true,
                'likes_count' => $likeable->likes_count,
            ]);
        }
    }

    public function check(Request $request)
    {
        $request->validate([
            'likeable_type' => 'required|in:App\Models\Destination,App\Models\Event,App\Models\Comment',
            'likeable_id' => 'required|integer',
        ]);

        $user = $request->user();

        $liked = Like::where('user_id', $user->id)
            ->where('likeable_type', $request->likeable_type)
            ->where('likeable_id', $request->likeable_id)
            ->exists();

        return response()->json([
            'liked' => $liked,
        ]);
    }
}
