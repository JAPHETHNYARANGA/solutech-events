<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Show all events from all organizations (central domain)
    public function index(): JsonResponse
    {
        $events = Event::with('organization')
            ->where('date', '>', now())
            ->orderBy('date')
            ->get(['id', 'organization_id', 'title', 'venue', 'date', 'price']);

        return response()->json($events);
    }

    // Show all events for a specific organization
    public function organizationEvents(string $organization): JsonResponse
    {
        $events = Event::with('organization')
            ->whereHas('organization', function($query) use ($organization) {
                $query->where('slug', $organization);
            })
            ->where('date', '>', now())
            ->orderBy('date')
            ->get(['id', 'organization_id', 'title', 'venue', 'date', 'price']);

        return response()->json($events);
    }

    // Show single event for a specific organization
    public function show(string $organization, string $eventId): JsonResponse
    {
        $event = Event::with('organization')
            ->whereHas('organization', function($query) use ($organization) {
                $query->where('slug', $organization);
            })
            ->findOrFail($eventId, ['id', 'organization_id', 'title', 'description', 'venue', 'date', 'price', 'max_attendees']);

        return response()->json($event);
    }

    // Register for event
    public function register(Request $request, string $organization, string $eventId): JsonResponse
    {
        $event = Event::whereHas('organization', function($query) use ($organization) {
                $query->where('slug', $organization);
            })
            ->findOrFail($eventId);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ]);

        if ($event->attendees()->count() >= $event->max_attendees) {
            return response()->json([
                'message' => 'This event has reached maximum capacity'
            ], 400);
        }

        if ($event->attendees()->where('email', $data['email'])->exists()) {
            return response()->json([
                'message' => 'This email is already registered for the event'
            ], 409);
        }

        $attendee = $event->attendees()->create($data);

        return response()->json([
            'message' => 'Registration successful',
            'data' => $attendee
        ], 201);
    }
}