<?php

namespace App\Policies;

use App\Models\Destination;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DestinationPolicy
{
    /**
     * Determine whether the user can view the list of their own posts.
     * This is the policy for the 'my-posts' page.
     */
    public function viewMy(User $user): bool
    {
        // Any authenticated user can view their own posts.
        return true;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Any authenticated user can view the list of all destinations.
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Destination $destination): bool
    {
        // Anyone (even guests) can view a single destination.
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create a destination.
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Destination $destination): bool
    {
        // User can update their own destination, or if they have moderation rights.
        return $user->id === $destination->user_id || $user->canModerateContent();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Destination $destination): bool
    {
        // User can delete their own destination, or if they have moderation rights.
        return $user->id === $destination->user_id || $user->canModerateContent();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Destination $destination): bool
    {
        // Only allow if user is an admin/moderator
        return $user->canModerateContent();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Destination $destination): bool
    {
        // Only allow if user is an admin/moderator
        return $user->canModerateContent();
    }
}
