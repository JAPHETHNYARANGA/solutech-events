<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Event;
use App\Models\Attendee;
use App\Models\Organization;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendeeControllerTest extends TestCase
{
    use RefreshDatabase;
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
        
        $token = $admin->createToken('test-token')->plainTextToken;
        
        return [
            'organization' => $organization,
            'admin' => $admin,
            'token' => $token
        ];
    }

    public function test_get_attendees_for_event()
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
    
        
        Attendee::create([
            'event_id' => $event->id,
            'name' => 'Test Attendee',
            'email' => 'attendee1@test.com',
            'phone' => '1234567890'
        ]);
        Attendee::create([
            'event_id' => $event->id,
            'name' => 'Test Attendee',
            'email' => 'attendee2@test.com',
            'phone' => '1234567890'
        ]);
        Attendee::create([
            'event_id' => $event->id,
            'name' => 'Test Attendee',
            'email' => 'attendee3@test.com',
            'phone' => '1234567890'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $setup['token']
        ])->getJson("/api/{$setup['organization']->slug}/events/{$event->id}/attendees");

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_get_single_attendee()
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
        $attendee = Attendee::create([
            'event_id' => $event->id,
            'name' => 'Test Attendee',
            'email' => 'attendee@test.com',
            'phone' => '1234567890'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $setup['token']
        ])->getJson("/api/{$setup['organization']->slug}/events/{$event->id}/attendees/{$attendee->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $attendee->id]);
    }

    public function test_delete_attendee()
    {
        $setup = $this->createAdminWithOrganization();
        $event = Event::create([
            'title' => 'Test Event',
            'organization_id' => $setup['organization']->id,
            'date' => now()->addDays(1),
            'venue' => 'Test Venue',
            'description'=> 'Test Description', 
            'venue'=> 'Test Venue',
            'price'=> 0,
            'max_attendees'=> 10
        ]);
        $attendee = Attendee::create([
            'event_id' => $event->id,
            'name' => 'Test Attendee',
            'email' => 'attendee@test.com',
            'phone' => '1234567890'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $setup['token']
        ])->deleteJson("/api/{$setup['organization']->slug}/events/{$event->id}/attendees/{$attendee->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('attendees', ['id' => $attendee->id]);
    }

    public function test_attendee_not_found()
    {
        $setup = $this->createAdminWithOrganization();
        $event =Event::create([
            'title' => 'Test Event',
            'organization_id' => $setup['organization']->id,
            'date' => now()->addDays(1),
            'venue' => 'Test Venue',
            'description'=> 'Test Description', 
            'venue'=> 'Test Venue',
            'price'=> 0,
            'max_attendees'=> 10
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $setup['token']
        ])->getJson("/api/{$setup['organization']->slug}/events/{$event->id}/attendees/999");

        $response->assertStatus(404)
            ->assertJson(['error' => 'Attendee not found']);
    }
}