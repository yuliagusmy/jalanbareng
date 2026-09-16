<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $user->load(['role', 'destinations' => function ($query) {
            $query->with(['category', 'photos'])
                ->withCount(['likes', 'comments'])
                ->latest();
        }, 'participatedEvents' => function ($query) {
            $query->with(['photos'])
                ->where('date', '>=', now())
                ->latest('date');
        }]);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'photo' => $user->photo,
                'role' => $user->role->display_name,
                'instagram' => $user->instagram,
                'facebook' => $user->facebook,
                'twitter' => $user->twitter,
                'destinations' => $user->destinations,
                'participated_events' => $user->participatedEvents,
                'destinations_count' => $user->destinations->count(),
                'events_count' => $user->participatedEvents->count(),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
            'instagram' => 'nullable|string|max:255',
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo if exists (only if it's a local file)
            if ($user->photo && !str_starts_with($user->photo, 'http')) {
                Storage::disk('public')->delete($user->photo);
            }

            $path = $request->file('photo')->store('profiles', 'public');
            $user->photo = $path;
        }

        $user->fill($request->only(['name', 'phone', 'instagram', 'facebook', 'twitter']));
        $user->save();

        // Load role relationship
        $user->load('role');

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'photo' => $user->photo,
                'role' => $user->role,
                'role_id' => $user->role_id,
                'instagram' => $user->instagram,
                'facebook' => $user->facebook,
                'twitter' => $user->twitter,
            ],
        ]);
    }
}
