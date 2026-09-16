<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // Get user from Google using the code
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->user();

            \Log::info('Google user retrieved', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName()
            ]);

            // Find or create user
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Create new user with member role
                $memberRole = Role::where('name', 'member')->first();

                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'email_verified_at' => now(),
                    'role_id' => $memberRole ? $memberRole->id : 3, // Default to member
                    'photo' => $googleUser->getAvatar(),
                    'password' => bcrypt(str()->random(32)), // Random password since we use OAuth
                ]);

                \Log::info('New user created', ['user_id' => $user->id]);
            } else {
                // Update user info from Google
                $user->update([
                    'name' => $googleUser->getName(),
                    'photo' => $googleUser->getAvatar(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);

                \Log::info('Existing user updated', ['user_id' => $user->id]);
            }

            // Create token
            $token = $user->createToken('google-auth')->plainTextToken;

            \Log::info('Token created', [
                'user_id' => $user->id,
                'token_length' => strlen($token)
            ]);

            // Redirect to frontend with token
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            $redirectUrl = $frontendUrl . '/auth/google/callback?token=' . urlencode($token);

            return redirect($redirectUrl);

        } catch (\Exception $e) {
            \Log::error('Google OAuth callback error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Redirect to frontend with error
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
            $redirectUrl = $frontendUrl . '/auth/google/callback?error=' . urlencode($e->getMessage());

            return redirect($redirectUrl);
        }
    }

    /**
     * Get Google Auth URL for frontend
     */
    public function getAuthUrl()
    {
        $url = Socialite::driver('google')
            ->stateless()
            ->redirect()
            ->getTargetUrl();

        return response()->json([
            'url' => $url,
        ]);
    }

    /**
     * Handle Google One-Tap Login
     */
    public function handleGoogleOneTap(Request $request)
    {
        $request->validate([
            'credential' => 'required|string',
        ]);

        try {
            // Decode JWT token from Google One Tap
            $parts = explode('.', $request->credential);
            if (count($parts) !== 3) {
                throw new \Exception('Invalid token format');
            }

            $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);

            if (!isset($payload['email'])) {
                throw new \Exception('Email not found in token');
            }

            // Find or create user
            $user = User::where('email', $payload['email'])->first();

            if (!$user) {
                $memberRole = Role::where('name', 'member')->first();

                $user = User::create([
                    'name' => $payload['name'] ?? $payload['email'],
                    'email' => $payload['email'],
                    'email_verified_at' => now(),
                    'role_id' => $memberRole ? $memberRole->id : 3,
                    'photo' => $payload['picture'] ?? null,
                    'password' => bcrypt(str()->random(32)),
                ]);
            } else {
                $user->update([
                    'name' => $payload['name'] ?? $user->name,
                    'photo' => $payload['picture'] ?? $user->photo,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }

            // Create token
            $token = $user->createToken('google-one-tap')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user->load('role'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to authenticate with Google',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
