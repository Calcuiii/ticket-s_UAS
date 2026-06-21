<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class JwtAuthMiddleware
{
    /**
     * Validasi JWT token ke User Service.
     * Inject user_id & user data ke dalam request.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan. Silakan login terlebih dahulu.',
            ], 401);
        }

        try {
            // Call User Service untuk validasi token
            $response = Http::withToken($token)
                ->get(env('USER_SERVICE_URL') . '/api/profile');

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak valid atau sudah expired.',
                ], 401);
            }

            $userData = $response->json('data.user');

            // Inject user data ke request agar controller bisa akses
            $request->merge(['auth_user' => $userData]);
            $request->attributes->set('auth_user_id', $userData['id']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal terhubung ke User Service.',
            ], 503);
        }

        return $next($request);
    }
}
