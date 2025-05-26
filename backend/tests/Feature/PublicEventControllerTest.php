<?php

namespace Tests\Feature;

use App\Models\Attendee;
use Tests\TestCase;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PublicEventControllerTest extends TestCase
{
    use RefreshDatabase;
    public function test_get_all_public_events()
    {
        $org = Organization::create(['name' => 'Test Org', 'slug' => 'test-org']);
        Event::create([
            'title' => 'Event 1',
            'organization_id' => $org->id,
            'date' => now()->addDays(1),
            'venue' => 'Venue 1',
            'price' => 0, // Add required field
            'max_attendees' => 10 // Add required field
        ]);
        Event::create([
            'title' => 'Event 2',
            'organization_id' => $org->id,
            'date' => now()->addDays(1),
            'venue' => 'Venue 1',
            'price' => 0, // Add required field
            'max_attendees' => 10 // Add required field
        ]);
        Event::create([
            'title' => 'Event 3',
            'organization_id' => $org->id,
            'date' => now()->addDays(1),
            'venue' => 'Venue 1',
            'price' => 0, // Add required field
            'max_attendees' => 10 // Add required field
        ]);

        $response = $this->getJson('/api/public/events');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_get_organization_events()
    {
        $org = Organization::create(['name' => 'Test Org', 'slug' => 'test-org']);
        Event::create([
            'title' => 'Test Event1',
            'organization_id' => $org->id,
            'date' => now()->addDays(1),
            'venue' => 'Test Venue',
            'price' => 0, 
            'max_attendees' => 10 
        ]);
        Event::create([
            'title' => 'Test Event2',
            'organization_id' => $org->id,
            'date' => now()->addDays(1),
            'venue' => 'Test Venue',
            'price' => 0, 
            'max_attendees' => 10 
        ]);
        Event::create([
            'title' => 'Test Event3',
            'organization_id' => $org->id,
            'date' => now()->addDays(1),
            'venue' => 'Test Venue',
            'price' => 0, 
            'max_attendees' => 10 
        ]);

        $response = $this->getJson('/api/test-org/public/events');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_get_single_event()
    {
        $org = Organization::create(['name' => 'Test Org', 'slug' => 'test-org']);
        $event = Event::create([
            'title' => 'Test Event3',
            'organization_id' => $org->id,
            'date' => now()->addDays(1),
            'venue' => 'Test Venue',
            'price' => 0, 
            'max_attendees' => 10 
        ]);

        $response = $this->getJson("/api/test-org/public/events/{$event->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $event->id]);
    }

    public function test_register_for_event()
    {
        $org = Organization::create(['name' => 'Test Org', 'slug' => 'test-org']);
        $event = Event::create([
            'title' => 'Test Event3',
            'organization_id' => $org->id,
            'date' => now()->addDays(1),
            'venue' => 'Test Venue',
            'price' => 0, 
            'max_attendees' => 10 
        ]);

        $response = $this->postJson("/api/test-org/public/events/{$event->id}/register", [
            'name' => 'Test Attendee',
            'email' => 'attendee@example.com',
            'phone' => '1234567890'
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Registration successful']);

        $this->assertDatabaseHas('attendees', ['email' => 'attendee@example.com']);
    }

}