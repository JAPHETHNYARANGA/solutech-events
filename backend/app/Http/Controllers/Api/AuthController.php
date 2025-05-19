<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Organization;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Database\Models\Domain;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:admins,email',
                'password' => 'required|string',
                'organization_name' => 'required|string|max:255',
                'organization_slug' => 'required|string|max:255|alpha_dash|unique:organizations,slug'
            ]);

            DB::beginTransaction();

            // Create organization
            $organization = Organization::create([
                'name' => $data['organization_name'],
                'slug' => $data['organization_slug']
            ]);

            // Create tenant (no domain needed for path-based)
            $tenant = Tenant::create([
                'id' => $data['organization_slug'],
                'data' => [
                    'organization_id' => $organization->id,
                    'organization_name' => $data['organization_name']
                ]
            ]);

            // Initialize tenancy and create admin
            tenancy()->initialize($tenant);

            $admin = Admin::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'organization_id' => $organization->id
            ]);

            // Create default database for tenant
            tenancy()->end();

            DB::commit();

            return response()->json([
                'message' => 'Registration successful',
                'redirect_to' => url("/{$organization->slug}/admin"),
                'admin' => $admin,
                'organization' => $organization
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $admin = Admin::where('email', $credentials['email'])->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Initialize tenancy for the admin's organization
        tenancy()->initialize($admin->organization->slug);

        $token = $admin->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'admin' => $admin,
            'redirect_to' => url("/{$admin->organization->slug}/admin")
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        tenancy()->end();
        
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}