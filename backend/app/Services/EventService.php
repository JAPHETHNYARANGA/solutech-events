<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Attendee;
use App\Models\ActivityLog;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EventService
{
    public function getEventsForOrganization(string $organizationSlug): JsonResponse
    {
        $events = Event::whereHas('organization', fn($q) => $q->where('slug', $organizationSlug))
            ->get();

        return response()->json($events);
    }

    public function getOrganizationEvent(string $organizationSlug, string $eventId): JsonResponse
    {
        $event = Event::whereHas('organization', fn($q) => $q->where('slug', $organizationSlug))
            ->findOrFail($eventId);

        return response()->json($event);
    }

    public function createEvent(string $organizationSlug, array $data): JsonResponse
    {
        $organization = Organization::where('slug', $organizationSlug)->firstOrFail();

        $event = $organization->events()->create($data);
        $this->logActivity($event, 'created', $data);

        return response()->json($event, Response::HTTP_CREATED);
    }

    public function updateEvent(string $organizationSlug, string $eventId, array $data): JsonResponse
    {
        $event = Event::whereHas('organization', fn($q) => $q->where('slug', $organizationSlug))
            ->findOrFail($eventId);

        $changes = $event->getDirty();
        $event->update($data);
        $this->logActivity($event, 'updated', $changes);

        return response()->json($event);
    }

    public function deleteEvent(string $organizationSlug, string $eventId): JsonResponse
    {
        $event = Event::whereHas('organization', fn($q) => $q->where('slug', $organizationSlug))
            ->findOrFail($eventId);

        $this->logActivity($event, 'deleted');
        $event->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function getPublicEvent(string $organizationSlug, string $eventId): JsonResponse
    {
        $event = Event::with('organization')
            ->whereHas('organization', fn($q) => $q->where('slug', $organizationSlug))
            ->findOrFail($eventId, ['id', 'organization_id', 'title', 'description', 'venue', 'date', 'price', 'max_attendees']);

        return response()->json($event);
    }

    public function registerAttendee(string $eventId, array $data): JsonResponse
    {
        $event = Event::findOrFail($eventId);

        if ($event->attendees()->count() >= $event->max_attendees) {
            return response()->json(['message' => 'Event is full'], Response::HTTP_BAD_REQUEST);
        }

        if ($event->attendees()->where('email', $data['email'])->exists()) {
            return response()->json(['message' => 'Email already registered'], Response::HTTP_CONFLICT);
        }

        $attendee = $event->attendees()->create($data);
        $this->logActivity($event, 'attendee_registered', $data);

        return response()->json([
            'message' => 'Registration successful',
            'data' => $attendee
        ], Response::HTTP_CREATED);
    }

    private function logActivity(Event $event, string $action, array $changes = []): void
    {
        ActivityLog::create([
            'event_id' => $event->id,
            'action' => $action,
            'changes' => $changes,
            'user_id' => Auth::user()->id,
        ]);
    }
}
