<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
  

    protected function setUp(): void
    {
        parent::setUp();
        // Additional setup if needed
    }

    protected function createAdminWithOrganization()
    {
        $organization = \App\Models\Organization::factory()->create();
        $admin = \App\Models\Admin::factory()->create([
            'organization_id' => $organization->id
        ]);
        
        return [
            'admin' => $admin,
            'organization' => $organization,
            'token' => $admin->createToken('test-token')->plainTextToken
        ];
    }
}
