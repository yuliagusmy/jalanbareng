<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     * Enforce role-based access control for administrative endpoints.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Check if user is banned
        if (method_exists($user, 'isBanned') && $user->isBanned()) {
            return response()->json(['message' => 'Akun Anda dinonaktifkan atau dibatasi.'], 403);
        }

        $userRole = $user->role?->name;

        // If specific roles required (e.g. 'role:admin')
        if (!empty($roles)) {
            if (!in_array($userRole, $roles)) {
                return response()->json([
                    'message' => 'Unauthorized. Akses ditolak. Hanya untuk ' . implode(' atau ', $roles) . '.'
                ], 403);
            }
            return $next($request);
        }

        // Default management access: admin or community_admin
        if (!in_array($userRole, ['admin', 'community_admin'])) {
            return response()->json([
                'message' => 'Unauthorized. Akses khusus pengelola Jalan Bareng.'
            ], 403);
        }

        return $next($request);
    }
}
