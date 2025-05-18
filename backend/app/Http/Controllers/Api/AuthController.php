<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Organization;
use App\Models\Tenant;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Database\Models\Domain;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}


    public function register(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:admins,email',
                'password' => 'required|string|min:8',
                'organization_name' => 'required|string|max:255',
                'organization_slug' => 'required|string|max:255|unique:organizations,slug'
            ]);

            DB::beginTransaction();

            $organization = Organization::create([
                'name' => $data['organization_name'],
                'slug' => $data['organization_slug']
            ]);

            $tenant = Tenant::create([
                'id' => $data['organization_slug'],
                'data' => [
                    'organization_id' => $organization->id,
                    'organization_name' => $data['organization_name']
                ]
            ]);

            $domain = $data['organization_slug'] . '.' . config('tenancy.central_domains')[0];
            $tenant->domains()->create([
                'domain' => $domain,
                'tenant_id' => $organization->slug,
            ]);

            DB::commit();

            tenancy()->initialize($tenant);

            $admin = Admin::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'organization_id' => $organization->id
            ]);

            tenancy()->end();

            return response()->json([
                'message' => 'Registration successful',
                'domain' => $domain,
                'admin' => $admin
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
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            $response = $this->authService->login($credentials);

            if ($response->getStatusCode() === 200) {
                $admin = Admin::where('email', $credentials['email'])->firstOrFail();

                $tenant = \App\Models\Tenant::find($admin->organization->slug);
                $domainModel = $tenant?->domains()->first();
                if (!$domainModel) {
                    return response()->json([
                        'message' => 'Login successful, but domain not found'
                    ], 200);
                }

                $domain = $domainModel->domain;

                $response->setData(array_merge($response->getData(true), [
                    'redirect_to' => 'https://' . $domain . '/admin'
                ]));
            }

            return $response;
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function logout(): JsonResponse
    {
        return $this->authService->logout();
    }
}
