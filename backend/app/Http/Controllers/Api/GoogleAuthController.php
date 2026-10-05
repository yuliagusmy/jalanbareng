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
            $caPath = base_path('../php82/extras/ssl/cacert.pem');
            $verify = file_exists($caPath) ? $caPath : false;
            $client = new \GuzzleHttp\Client(['verify' => $verify]);

            $driver = Socialite::driver('google')->stateless();
            $driver->setHttpClient($client);

            // Get user from Google using the code
            $googleUser = $driver->user();

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
            // Cryptographically verify ID token against Google's public tokeninfo endpoint
            $verifyResponse = \Illuminate\Support\Facades\Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $request->credential,
            ]);

            if (!$verifyResponse->successful()) {
                \Log::warning('Google One-Tap token verification failed: ' . $verifyResponse->body());
                return response()->json([
                    'success' => false,
                    'message' => 'Token Google tidak valid atau sudah kedaluwarsa.',
                ], 401);
            }

            $payload = $verifyResponse->json();

            // Validate audience if GOOGLE_CLIENT_ID is configured
            $clientId = config('services.google.client_id') ?: env('GOOGLE_CLIENT_ID');
            if ($clientId && isset($payload['aud']) && $payload['aud'] !== $clientId) {
                \Log::warning('Google One-Tap audience mismatch: expected ' . $clientId . ', got ' . $payload['aud']);
                return response()->json([
                    'success' => false,
                    'message' => 'Token audiens Google tidak sesuai.',
                ], 401);
            }

            if (empty($payload['email'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak ditemukan dari data akun Google.',
                ], 422);
            }

            // Find or create user
            $user = User::where('email', $payload['email'])->first();

            // Reject banned users
            if ($user && method_exists($user, 'isBanned') && $user->isBanned()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda sedang dinonaktifkan atau dibatasi: ' . ($user->ban_reason ?? 'Silakan hubungi admin.'),
                ], 403);
            }

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
            \Log::error('Google One-Tap authentication error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses autentikasi Google',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
