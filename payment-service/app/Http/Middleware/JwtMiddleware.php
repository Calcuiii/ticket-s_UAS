<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Exception;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan.',
            ], 401);
        }

        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), env('JWT_ALGO', 'HS256')));

            // Simpan data user dari token ke request, supaya bisa diakses di controller
            $request->merge(['auth_user_id' => $decoded->sub ?? $decoded->user_id]);

        } catch (ExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token sudah expired.',
            ], 401);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid.',
            ], 401);
        }

        return $next($request);
    }
}