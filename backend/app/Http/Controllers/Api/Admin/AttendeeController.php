<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\AttendeeService;
use Illuminate\Http\JsonResponse;

class AttendeeController extends Controller
{
    public function __construct(private AttendeeService $attendeeService) {}

    public function index(string $organizationSlug, string $eventId): JsonResponse
    {
        $attendees = $this->attendeeService->getAttendeesForEvent($organizationSlug, $eventId);
        return response()->json($attendees);
    }

    public function show(string $organizationSlug, string $eventId, string $id): JsonResponse
    {
        $attendee = $this->attendeeService->getAttendee($organizationSlug, $id);
        return response()->json($attendee);
    }

    public function destroy(string $organizationSlug, string $eventId, string $id): JsonResponse
    {
        $this->attendeeService->deleteAttendee($organizationSlug, $id);
        return response()->json(null, 204);
    }
}