<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function register(array $data): JsonResponse
    {
        $organization = Organization::create([
            'name' => $data['organization_name'],
            'slug' => $data['organization_slug']
        ]);

        $admin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'organization_id' => $organization->id
        ]);

        return response()->json([
            'access_token' => $admin->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
            'admin' => $admin,
            'organization' => $organization
        ], 201);
    }

    public function login(array $credentials): JsonResponse
    {
        $admin = Admin::where('email', $credentials['email'])->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'access_token' => $admin->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
            'admin' => $admin
        ]);
    }

    public function logout(): JsonResponse
    {
        // Auth::user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }
}