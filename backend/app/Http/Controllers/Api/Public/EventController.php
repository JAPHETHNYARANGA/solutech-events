<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterAttendeeRequest;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function index(): JsonResponse
    {
        $events = Event::with('organization')
            ->orderBy('date')
            ->get(['id', 'organization_id', 'title', 'venue', 'date', 'price']);
        return response()->json($events);
    }

    public function organizationEvents(string $organization): JsonResponse
    {
        $events = Event::with('organization')
            ->whereHas('organization', fn($q) => $q->where('slug', $organization))
            ->where('date', '>', now())
            ->orderBy('date')
            ->get(['id', 'organization_id', 'title', 'venue', 'date', 'price']);
        return response()->json($events);
    }

    public function show(string $organization, string $eventId): JsonResponse
    {
        $event = Event::with('organization')
            ->whereHas('organization', fn($q) => $q->where('slug', $organization))
            ->findOrFail($eventId, ['id', 'organization_id', 'title', 'description', 'venue', 'date', 'price', 'max_attendees']);
        return response()->json($event);
    }

    public function register(RegisterAttendeeRequest $request, string $organization, string $eventId): JsonResponse
    {
        $event = Event::whereHas('organization', fn($q) => $q->where('slug', $organization))
            ->findOrFail($eventId);

        if ($event->attendees()->count() >= $event->max_attendees) {
            return response()->json(['message' => 'Event at capacity'], 400);
        }

        if ($event->attendees()->where('email', $request->email)->exists()) {
            return response()->json(['message' => 'Email already registered'], 409);
        }

        $attendee = $event->attendees()->create($request->validated());
        return response()->json(['message' => 'Registration successful', 'data' => $attendee], 201);
    }
}