<?php

namespace App\Services;

use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class AttendeeService
{
    public function getAttendeesForEvent(string $eventId)
    {
        $event = Event::where('organization_id', Auth::user()->organization_id)
            ->findOrFail($eventId);
            
        return $event->attendees;
    }

    public function getAttendee(string $id): Attendee
    {
        return Attendee::whereHas('event', function($query) {
                $query->where('organization_id', Auth::user()->organization_id);
            })
            ->findOrFail($id);
    }

    public function deleteAttendee(string $id): void
    {
        $attendee = $this->getAttendee($id);
        $attendee->delete();
    }
}