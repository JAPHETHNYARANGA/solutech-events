<?php

namespace App\Services;

use App\Models\Organization;
use Illuminate\Support\Facades\Auth;

class OrganizationService
{
    public function createOrganization(array $data): Organization
    {
        return Organization::create($data);
    }

    public function updateOrganization(string $id, array $data): Organization
    {
        $organization = Organization::findOrFail($id);
        $organization->update($data);
        
        return $organization;
    }

    public function deleteOrganization(string $id): void
    {
        $organization = Organization::findOrFail($id);
        $organization->delete();
    }
}