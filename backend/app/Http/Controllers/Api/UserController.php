<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = $request->user() ?? auth('sanctum')->user();

        // Check if this is an admin request (for user management)
        if ($request->has('admin_view')) {
            if (!$currentUser || (!$currentUser->isAdmin() && !$currentUser->isCommunityAdmin())) {
                return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);
            }
            return $this->adminIndex($request);
        }

        // Return users list for dropdowns and selectors
        $limit = $request->get('limit', 100);

        $users = User::latest()
                     ->limit($limit)
                     ->get(['id', 'name', 'photo']);

        return response()->json([
            'users' => $users,
            'data' => $users
        ]);
    }

    private function adminIndex(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search');
        $role = $request->get('role');
        $sort = $request->get('sort', 'newest');

        $query = User::with('role')
            ->withCount(['destinations', 'comments', 'likes']);

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role) {
            $query->whereHas('role', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        // Sorting
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $users = $query->paginate($perPage);

        // Transform users to include role name
        $users->getCollection()->transform(function ($user) {
            $userData = $user->toArray();
            $userData['role'] = $user->role->name ?? 'member';
            return $userData;
        });

        $result = $users->toArray();
        $result['stats'] = [
            'total' => User::count(),
            'admins' => User::whereHas('role', fn($q) => $q->where('name', 'admin'))->count(),
            'community_admins' => User::whereHas('role', fn($q) => $q->where('name', 'community_admin'))->count(),
            'members' => User::whereHas('role', fn($q) => $q->where('name', 'member'))->count(),
        ];

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['member', 'community_admin', 'admin'])],
        ]);

        // Get role_id from role name
        $role = \App\Models\Role::where('name', $validated['role'])->first();

        if (!$role) {
            return response()->json([
                'message' => 'Role tidak ditemukan'
            ], 400);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id,
        ]);

        $user->load('role');
        $userData = $user->toArray();
        $userData['role'] = $user->role->name;

        return response()->json([
            'message' => 'User berhasil ditambahkan',
            'user' => $userData
        ], 201);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['member', 'community_admin', 'admin'])],
        ]);

        // Get role_id from role name
        $role = \App\Models\Role::where('name', $validated['role'])->first();

        if (!$role) {
            return response()->json([
                'message' => 'Role tidak ditemukan'
            ], 400);
        }

        $user->update([
            'role_id' => $role->id
        ]);

        $user->load('role');
        $userData = $user->toArray();
        $userData['role'] = $user->role->name;

        return response()->json([
            'message' => 'Role user berhasil diupdate',
            'user' => $userData
        ]);
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Anda tidak bisa menghapus akun Anda sendiri'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }

    public function ban(Request $request, User $user)
    {
        // Prevent banning yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Anda tidak bisa mem-ban akun Anda sendiri'
            ], 403);
        }

        // Prevent banning other admins
        if ($user->isAdmin()) {
            return response()->json([
                'message' => 'Anda tidak bisa mem-ban user admin lain'
            ], 403);
        }

        $validated = $request->validate([
            'ban_status' => ['required', Rule::in(['warning', 'restrict', 'suspend', 'permanent'])],
            'ban_reason' => 'required|string',
            'ban_until' => 'nullable|date|after:now', // Required for suspend
        ]);

        // For suspend, ban_until is required
        if ($validated['ban_status'] === 'suspend' && !isset($validated['ban_until'])) {
            return response()->json([
                'message' => 'Tanggal akhir suspend harus diisi'
            ], 400);
        }

        $user->update([
            'ban_status' => $validated['ban_status'],
            'ban_reason' => $validated['ban_reason'],
            'banned_at' => now(),
            'ban_until' => $validated['ban_until'] ?? null,
            'banned_by' => auth()->id(),
        ]);

        $user->load(['role', 'bannedBy']);
        $userData = $user->toArray();
        $userData['role'] = $user->role->name;

        return response()->json([
            'message' => 'User berhasil di-ban',
            'user' => $userData
        ]);
    }

    public function unban(User $user)
    {
        $user->update([
            'ban_status' => null,
            'ban_reason' => null,
            'banned_at' => null,
            'ban_until' => null,
            'banned_by' => null,
        ]);

        $user->load('role');
        $userData = $user->toArray();
        $userData['role'] = $user->role->name;

        return response()->json([
            'message' => 'Ban user berhasil dicabut',
            'user' => $userData
        ]);
    }
}
