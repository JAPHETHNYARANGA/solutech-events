<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Organization;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Database\Models\Domain;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8',
            'organization_name' => 'required|string|max:255',
            'organization_slug' => 'required|string|max:255|unique:organizations,slug'
        ]);

        // Create organization
        $organization = Organization::create([
            'name' => $data['organization_name'],
            'slug' => $data['organization_slug']
        ]);

        // Create admin user
        $admin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'organization_id' => $organization->id
        ]);

        // Create tenant domain
        $domain = $data['organization_slug'] . '.' . config('tenancy.central_domains')[0];
        $organization->domains()->create(['domain' => $domain]);

        return response()->json([
            'message' => 'Registration successful',
            'domain' => $domain,
            'admin' => $admin
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $response = $this->authService->login($credentials);
        
        // Redirect to tenant domain after successful login
        if ($response->getStatusCode() === 200) {
            $admin = Admin::where('email', $credentials['email'])->first();
            $domain = $admin->organization->domains()->first()->domain;
            $response->setData(array_merge($response->getData(true), [
                'redirect_to' => 'https://' . $domain . '/admin'
            ]));
        }

        return $response;
    }

    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }
}