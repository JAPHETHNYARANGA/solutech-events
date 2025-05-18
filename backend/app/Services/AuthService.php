<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(array $credentials): JsonResponse
    {
        $admin = Admin::where('email', $credentials['email'])->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $admin->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'admin' => $admin
        ]);
    }

    public function logout(): JsonResponse
    {
        // Auth::user()->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }
}