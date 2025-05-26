<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Admin;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_registration()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test Admin',
            'email' => 'test@example.com',
            'password' => 'password123',
            'organization_name' => 'Test Org',
            'organization_slug' => 'test-org'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'redirect_to',
                'admin' => ['id', 'name', 'email'],
                'organization' => ['id', 'name', 'slug']
            ]);

        $this->assertDatabaseHas('organizations', ['slug' => 'test-org']);
        $this->assertDatabaseHas('admins', ['email' => 'test@example.com']);
    }

    public function test_admin_login()
{
    $organization = Organization::create([
        'name' => 'Test Org',
        'slug' => 'test-org'
    ]);

    $admin = Admin::create([
        'name' => 'Test Admin',
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
        'organization_id' => $organization->id
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password123'
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'access_token',
            'token_type',
            'admin',
            'redirect_to'
        ]);
}


   
}