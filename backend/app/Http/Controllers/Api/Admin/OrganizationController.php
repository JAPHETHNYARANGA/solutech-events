<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Services\OrganizationService;
use Illuminate\Http\JsonResponse;

class OrganizationController extends Controller
{
    public function __construct(private OrganizationService $organizationService) {}

    public function store(StoreOrganizationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $organization = $this->organizationService->createOrganization($data);
        
        return response()->json($organization, 201);
    }

    public function update(UpdateOrganizationRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();
        $organization = $this->organizationService->updateOrganization($id, $data);
        
        return response()->json($organization);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->organizationService->deleteOrganization($id);
        return response()->json(null, 204);
    }
}