<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function login(array $credentials): array
    {
        $admin = Admin::where('email', $credentials['email'])->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $admin->createToken('auth_token')->plainTextToken;

        return [
            'status' => 200,
            'data' => [
                'token' => $token,
                'admin' => $admin
            ]
        ];
    }

    public function logout(): array
    {
        Auth::user()->tokens()->delete();
        
        return [
            'status' => 200,
            'data' => ['message' => 'Logged out successfully']
        ];
    }
}