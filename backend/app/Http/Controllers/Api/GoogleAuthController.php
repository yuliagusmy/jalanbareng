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
     * Get sanitized Google OAuth redirect URL
     */
    private function getCleanRedirectUrl(): ?string
    {
        $redirect = config('services.google.redirect') ?: env('GOOGLE_REDIRECT_URI');
        if ($redirect) {
            return trim(preg_replace('/[\r\n\t ]+/', '', $redirect));
        }
        return null;
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        $driver = Socialite::driver('google')->stateless();
        if ($redirect = $this->getCleanRedirectUrl()) {
            $driver->redirectUrl($redirect);
        }
        return $driver->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request)
    {
        $frontendUrl = $this->resolveFrontendRedirectUrl($request);

        try {
            $caPath = base_path('../php82/extras/ssl/cacert.pem');
            $verify = file_exists($caPath) ? $caPath : false;
            $client = new \GuzzleHttp\Client(['verify' => $verify]);

            $driver = Socialite::driver('google')->stateless();
            if ($redirect = $this->getCleanRedirectUrl()) {
                $driver->redirectUrl($redirect);
            }
            $driver->setHttpClient($client);

            $googleUser = $driver->user();
            $user = $this->findOrCreateGoogleUser($googleUser);

            if (method_exists($user, 'isBanned') && $user->isBanned()) {
                $msg = 'Akun Anda sedang dinonaktifkan: ' . ($user->ban_reason ?? 'Hubungi admin.');
                return redirect($frontendUrl . '/auth/google/callback?error=' . urlencode($msg));
            }

            $token = $user->createToken('google-auth')->plainTextToken;
            return redirect($frontendUrl . '/auth/google/callback?token=' . urlencode($token));

        } catch (\Throwable $e) {
            \Log::error('Google OAuth callback error', [
                'message' => $e->getMessage(),
            ]);

            return redirect($frontendUrl . '/auth/google/callback?error=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Find existing user or create a new user with guaranteed member/admin role
     */
    private function findOrCreateGoogleUser($googleUser): User
    {
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['display_name' => 'Administrator', 'description' => 'Full system access and management']
        );
        $memberRole = Role::firstOrCreate(
            ['name' => 'member'],
            ['display_name' => 'Member', 'description' => 'Regular user with basic permissions']
        );

        $email = $googleUser->getEmail();
        $adminEmails = array_filter(array_map('trim', explode(',', env('ADMIN_EMAILS', 'admin@jalanbareng.com,yuliagusmy@gmail.com'))));
        $isAdmin = in_array(strtolower($email), array_map('strtolower', $adminEmails));
        $targetRoleId = $isAdmin ? $adminRole->id : $memberRole->id;

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Member',
                'email' => $email,
                'email_verified_at' => now(),
                'role_id' => $targetRoleId,
                'photo' => $googleUser->getAvatar(),
                'password' => bcrypt(str()->random(32)),
            ]);

            \Log::info('New Google OAuth user created', ['user_id' => $user->id, 'role' => $isAdmin ? 'admin' : 'member']);
        } else {
            $updateData = [
                'name' => $googleUser->getName() ?? $user->name,
                'photo' => $googleUser->getAvatar() ?? $user->photo,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ];

            if ($isAdmin && $user->role_id !== $adminRole->id) {
                $updateData['role_id'] = $adminRole->id;
            }

            $user->update($updateData);

            \Log::info('Existing Google OAuth user updated', ['user_id' => $user->id, 'role' => $isAdmin ? 'admin' : ($user->role?->name ?? 'member')]);
        }

        return $user;
    }

    /**
     * Resolve frontend redirect URL based on state parameter or env
     */
    private function resolveFrontendRedirectUrl(Request $request): string
    {
        $frontendUrl = env('FRONTEND_URL', 'https://jalanbareng.web.id');

        if ($request->filled('state')) {
            try {
                $stateData = json_decode(base64_decode($request->state), true);
                if (!empty($stateData['frontend_url'])) {
                    $allowedHosts = [
                        'jalanbareng.web.id',
                        'jalanbareng.id',
                        'jalanbareng-gilt.vercel.app',
                        'localhost',
                        '127.0.0.1',
                    ];
                    $host = parse_url($stateData['frontend_url'], PHP_URL_HOST);
                    foreach ($allowedHosts as $allowed) {
                        if ($host === $allowed || ($host && str_ends_with($host, '.' . $allowed))) {
                            $frontendUrl = rtrim($stateData['frontend_url'], '/');
                            break;
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Fallback to configured FRONTEND_URL
            }
        }

        return $frontendUrl;
    }

    /**
     * Get Google Auth URL for frontend
     */
    public function getAuthUrl(Request $request)
    {
        $driver = Socialite::driver('google')->stateless();
        if ($redirect = $this->getCleanRedirectUrl()) {
            $driver->redirectUrl($redirect);
        }

        $origin = $request->input('origin') ?: $request->headers->get('referer');
        if ($origin) {
            $parsed = parse_url($origin);
            if (!empty($parsed['host'])) {
                $scheme = $parsed['scheme'] ?? 'https';
                $originUrl = $scheme . '://' . $parsed['host'] . (!empty($parsed['port']) ? ':' . $parsed['port'] : '');
                $driver->with(['state' => base64_encode(json_encode(['frontend_url' => $originUrl]))]);
            }
        }

        $url = $driver->redirect()->getTargetUrl();

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

            $adminRole = Role::firstOrCreate(
                ['name' => 'admin'],
                ['display_name' => 'Administrator', 'description' => 'Full system access and management']
            );
            $memberRole = Role::firstOrCreate(
                ['name' => 'member'],
                ['display_name' => 'Member', 'description' => 'Regular user with basic permissions']
            );

            $adminEmails = array_filter(array_map('trim', explode(',', env('ADMIN_EMAILS', 'admin@jalanbareng.com,yuliagusmy@gmail.com'))));
            $isAdmin = in_array(strtolower($payload['email']), array_map('strtolower', $adminEmails));
            $targetRoleId = $isAdmin ? $adminRole->id : $memberRole->id;

            if (!$user) {
                $user = User::create([
                    'name' => $payload['name'] ?? $payload['email'],
                    'email' => $payload['email'],
                    'email_verified_at' => now(),
                    'role_id' => $targetRoleId,
                    'photo' => $payload['picture'] ?? null,
                    'password' => bcrypt(str()->random(32)),
                ]);
            } else {
                $updateData = [
                    'name' => $payload['name'] ?? $user->name,
                    'photo' => $payload['picture'] ?? $user->photo,
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ];

                if ($isAdmin && $user->role_id !== $adminRole->id) {
                    $updateData['role_id'] = $adminRole->id;
                }

                $user->update($updateData);
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
