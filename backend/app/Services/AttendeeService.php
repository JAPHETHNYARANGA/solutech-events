<?php

namespace App\Services;

use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class AttendeeService
{
    public function getAttendeesForEvent(string $organizationSlug, string $eventId)
    {
        $event = Event::whereHas('organization', fn($q) => $q->where('slug', $organizationSlug))
            ->findOrFail($eventId);
            
        return $event->attendees()->get();
    }

    public function getAttendee(string $organizationSlug, string $id): Attendee
    {
        return Attendee::whereHas('event.organization', fn($q) => $q->where('slug', $organizationSlug))
            ->findOrFail($id);
    }

    public function deleteAttendee(string $organizationSlug, string $id): void
    {
        $attendee = $this->getAttendee($organizationSlug, $id);
        $attendee->delete();
    }
}