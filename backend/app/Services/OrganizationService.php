<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrganizationService
{
    public function createOrganization(array $data): JsonResponse
    {
        $organization = Organization::create($data);
        return response()->json($organization, Response::HTTP_CREATED);
    }

    public function updateOrganization(string $id, array $data): JsonResponse
    {
        $organization = Organization::findOrFail($id);
        $organization->update($data);
        return response()->json($organization);
    }

    public function deleteOrganization(string $id): JsonResponse
    {
        $organization = Organization::findOrFail($id);
        $organization->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}