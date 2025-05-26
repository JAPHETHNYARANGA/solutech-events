<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class EventControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function createAdminWithOrganization()
    {
        $organization = Organization::create([
            'name' => 'Test Org',
            'slug' => 'test-org'
        ]);

        $admin = Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'organization_id' => $organization->id
        ]);

        return [
            'organization' => $organization,
            'admin' => $admin,
        ];
    }

    public function test_get_events_for_organization()
    {
        $setup = $this->createAdminWithOrganization();

        Event::create([
            'title' => 'Test Event1',
            'organization_id' => $setup['organization']->id,
            'date' => now()->addDays(1),
            'venue' => 'Test Venue',
            'price' => 0,
            'max_attendees' => 10
        ]);
        Event::create([
            'title' => 'Test Event',
            'organization_id' => $setup['organization']->id,
            'date' => now()->addDays(1),
            'venue' => 'Test Venue',
            'price' => 0,
            'max_attendees' => 10
        ]);
        Event::create([
            'title' => 'Test Event2',
            'organization_id' => $setup['organization']->id,
            'date' => now()->addDays(1),
            'venue' => 'Test Venue',
            'price' => 0,
            'max_attendees' => 10
        ]);

        $response = $this->actingAs($setup['admin'], 'sanctum')
            ->getJson("/api/{$setup['organization']->slug}/admin/events");

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_create_event()
    {
        $setup = $this->createAdminWithOrganization();

        $response = $this->actingAs($setup['admin'], 'sanctum')
            ->postJson("/api/{$setup['organization']->slug}/admin/events", [
                'title' => 'Test Event',
                'description' => 'Test Description',
                'venue' => 'Test Venue',
                'date' => '2023-12-31 18:00:00',
                'price' => 100,
                'max_attendees' => 50
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Test Event',
                'organization_id' => $setup['organization']->id
            ]);

        $this->assertDatabaseHas('events', ['title' => 'Test Event']);
    }

    public function test_update_event()
    {
        $setup = $this->createAdminWithOrganization();

        $event = Event::create([
            'title' => 'Test Event',
            'organization_id' => $setup['organization']->id,
            'date' => now()->addDays(1),
            'venue' => 'Test Venue',
            'price' => 0,
            'max_attendees' => 10
        ]);

        $response = $this->actingAs($setup['admin'], 'sanctum')
            ->putJson("/api/{$setup['organization']->slug}/admin/events/{$event->id}", [
                'title' => 'Updated Event',
                'venue' => 'Updated Venue',
                'date' => '2023-12-31 19:00:00',
                'price' => 150,
                'max_attendees' => 75
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Updated Event',
                'venue' => 'Updated Venue'
            ]);
    }

    public function test_delete_event()
    {
        $setup = $this->createAdminWithOrganization();

        $event = Event::create([
            'title' => 'Test Event',
            'organization_id' => $setup['organization']->id,
            'date' => now()->addDays(1),
            'venue' => 'Test Venue',
            'price' => 0,
            'max_attendees' => 10
        ]);

        $response = $this->actingAs($setup['admin'], 'sanctum')
            ->deleteJson("/api/{$setup['organization']->slug}/admin/events/{$event->id}");

        $response->assertStatus(204);

        // Assert the event is soft deleted
        $this->assertSoftDeleted('events', ['id' => $event->id]);
    }

    public function test_event_validation()
    {
        $setup = $this->createAdminWithOrganization();

        $response = $this->actingAs($setup['admin'], 'sanctum')
            ->postJson("/api/{$setup['organization']->slug}/admin/events", [
                'title' => '',
                'price' => -10,
                'max_attendees' => 0
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'venue',
                'date',
                'price',
                'max_attendees'
            ]);
    }
}
