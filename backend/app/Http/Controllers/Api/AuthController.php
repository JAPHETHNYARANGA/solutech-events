<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $response = $this->authService->login($credentials);
        
        return response()->json($response['data'], $response['status']);
    }

    public function logout(): JsonResponse
    {
        $response = $this->authService->logout();
        return response()->json($response['data'], $response['status']);
    }
}