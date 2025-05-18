<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\AttendeeService;
use Illuminate\Http\JsonResponse;

class AttendeeController extends Controller
{
    public function __construct(private AttendeeService $attendeeService) {}

    public function index(string $eventId): JsonResponse
    {
        $attendees = $this->attendeeService->getAttendeesForEvent($eventId);
        return response()->json($attendees);
    }

    public function show(string $eventId, string $id): JsonResponse
    {
        $attendee = $this->attendeeService->getAttendee($id);
        return response()->json($attendee);
    }

    public function destroy(string $eventId, string $id): JsonResponse
    {
        $this->attendeeService->deleteAttendee($id);
        return response()->json(null, 204);
    }
}