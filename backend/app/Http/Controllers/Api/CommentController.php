<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'commentable_type' => 'required|in:destination,event,App\Models\Destination,App\Models\Event',
            'commentable_id' => 'required|integer',
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // Normalize commentable_type to full namespace
        $commentableType = $this->normalizeCommentableType($request->commentable_type);

        // If parent_id is provided, check that it doesn't already have a parent (max 2 levels)
        if ($request->parent_id) {
            $parentComment = Comment::find($request->parent_id);
            if ($parentComment && $parentComment->parent_id !== null) {
                return response()->json([
                    'message' => 'Cannot reply to a reply. Maximum 2 levels of comments allowed.',
                ], 400);
            }

            // Check parent belongs to same commentable
            if ($parentComment->commentable_id != $request->commentable_id ||
                $parentComment->commentable_type != $commentableType) {
                return response()->json([
                    'message' => 'Parent comment does not belong to this item.',
                ], 400);
            }
        }

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'commentable_type' => $commentableType,
            'commentable_id' => $request->commentable_id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        // Increment comments count on the commentable
        $commentable = $comment->commentable;
        $commentable->increment('comments_count');

        // Notify post owner (if not commenting on own post)
        try {
            $currentUser = $request->user();
            $owner = $commentable->user ?? null;
            if ($owner && $owner->id !== $currentUser->id) {
                $owner->notify(new \App\Notifications\NewCommentNotification($currentUser, $comment, $commentable));
            }

            // Also notify parent comment author if this is a reply
            if ($comment->parent_id) {
                $parentComment = \App\Models\Comment::find($comment->parent_id);
                if ($parentComment && $parentComment->user_id !== $currentUser->id && (!$owner || $parentComment->user_id !== $owner->id)) {
                    $parentUser = $parentComment->user;
                    if ($parentUser) {
                        $parentUser->notify(new \App\Notifications\NewCommentNotification($currentUser, $comment, $commentable));
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to send comment notification: ' . $e->getMessage());
        }

        $comment->load('user');

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment,
        ], 201);
    }

    public function update(Request $request, Comment $comment)
    {
        // Check authorization
        if ($comment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->content = $request->content;
        $comment->save();

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment,
        ]);
    }

    public function destroy(Request $request, Comment $comment)
    {
        // Check authorization - owner or moderator can delete
        if ($comment->user_id !== $request->user()->id && !$request->user()->canModerateContent()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Decrement comments count
        $commentable = $comment->commentable;
        $commentable->decrement('comments_count');

        // If this is a parent comment with replies, also decrement for each reply
        if ($comment->parent_id === null) {
            $repliesCount = $comment->replies()->count();
            if ($repliesCount > 0) {
                $commentable->decrement('comments_count', $repliesCount);
            }
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully',
        ]);
    }

    /**
     * Normalize commentable_type to full namespace
     */
    private function normalizeCommentableType(string $type): string
    {
        $typeMap = [
            'destination' => 'App\Models\Destination',
            'event' => 'App\Models\Event',
            'Destination' => 'App\Models\Destination',
            'Event' => 'App\Models\Event',
        ];

        // If already a full namespace, return as is
        if (str_starts_with($type, 'App\\Models\\')) {
            return $type;
        }

        // Convert short form to full namespace
        return $typeMap[$type] ?? $type;
    }
}
