<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Attendee;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class EventService
{
    public function getEventsForCurrentOrganization()
    {
        return Event::where('organization_id', Auth::user()->organization_id)->get();
    }

    public function createEvent(array $data): Event
    {
        $data['organization_id'] = Auth::user()->organization_id;
        $event = Event::create($data);
        
        $this->logActivity($event, 'created', $data);
        
        return $event;
    }

    public function getEvent(string $id): Event
    {
        return Event::where('organization_id', Auth::user()->organization_id)
            ->findOrFail($id);
    }

    public function updateEvent(string $id, array $data): Event
    {
        $event = $this->getEvent($id);
        $changes = $event->getDirty();
        
        $event->update($data);
        $this->logActivity($event, 'updated', $changes);
        
        return $event;
    }

    public function deleteEvent(string $id): void
    {
        $event = $this->getEvent($id);
        $this->logActivity($event, 'deleted');
        $event->delete();
    }

    public function getUpcomingPublicEvents()
    {
        return Event::where('date', '>', now())
            ->orderBy('date')
            ->get();
    }

    public function getPublicEvent(string $id): Event
    {
        return Event::findOrFail($id);
    }

    public function registerAttendee(string $eventId, array $data): array
    {
        $event = $this->getPublicEvent($eventId);
        
        if ($event->attendees()->count() >= $event->max_attendees) {
            return [
                'status' => 400,
                'data' => ['message' => 'Event has reached maximum attendees']
            ];
        }
        
        $attendee = $event->attendees()->create($data);
        $this->logActivity($event, 'attendee_registered', $data);
        
        return [
            'status' => 201,
            'data' => $attendee
        ];
    }

    private function logActivity(Event $event, string $action, array $changes = []): void
    {
        ActivityLog::create([
            'event_id' => $event->id,
            'action' => $action,
            'changes' => $changes
        ]);
    }
}