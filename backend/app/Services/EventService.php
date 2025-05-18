<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Attendee;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EventService
{
    // Admin methods
    public function getEventsForCurrentOrganization(): JsonResponse
    {
        $events = Event::where('organization_id', Auth::user()->organization_id)->get();
        return response()->json($events);
    }

    public function getEvent(string $id): JsonResponse
    {
        $event = Event::where('organization_id', Auth::user()->organization_id)
            ->findOrFail($id);
            
        return response()->json($event);
    }

    public function createEvent(array $data): JsonResponse
    {
        $data['organization_id'] = Auth::user()->organization_id;
        $event = Event::create($data);
        
        $this->logActivity($event, 'created', $data);

        return response()->json($event, Response::HTTP_CREATED);
    }

    public function updateEvent(string $id, array $data): JsonResponse
    {
        $event = Event::where('organization_id', Auth::user()->organization_id)
            ->findOrFail($id);
            
        $changes = $event->getDirty();
        $event->update($data);
        
        $this->logActivity($event, 'updated', $changes);

        return response()->json($event);
    }

    public function deleteEvent(string $id): JsonResponse
    {
        $event = Event::where('organization_id', Auth::user()->organization_id)
            ->findOrFail($id);
            
        $this->logActivity($event, 'deleted');
        $event->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    // Public methods
    public function getUpcomingPublicEvents(): JsonResponse
    {
        $events = Event::where('date', '>', now())
            ->orderBy('date')
            ->get(['id', 'title', 'description', 'venue', 'date', 'price', 'max_attendees']);

        return response()->json($events);
    }

    public function getPublicEvent(string $id): JsonResponse
    {
        $event = Event::findOrFail($id, ['id', 'title', 'description', 'venue', 'date', 'price', 'max_attendees']);
        return response()->json($event);
    }

    public function registerAttendee(string $eventId, array $data): JsonResponse
    {
        $event = Event::findOrFail($eventId);
        
        // Check if event has reached maximum attendees
        if ($event->attendees()->count() >= $event->max_attendees) {
            return response()->json([
                'message' => 'This event has reached maximum capacity'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Check if email already registered
        if ($event->attendees()->where('email', $data['email'])->exists()) {
            return response()->json([
                'message' => 'This email is already registered for the event'
            ], Response::HTTP_CONFLICT);
        }

        $attendee = $event->attendees()->create($data);
        
        $this->logActivity($event, 'attendee_registered', [
            'attendee_id' => $attendee->id,
            'name' => $data['name'],
            'email' => $data['email']
        ]);

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
            'user_id' => Auth::id() ?? null // Null for public registrations
        ]);
    }
}