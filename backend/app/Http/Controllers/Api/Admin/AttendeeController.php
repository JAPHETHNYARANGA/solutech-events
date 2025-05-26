<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\AttendeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AttendeeController extends Controller
{
    public function __construct(private AttendeeService $attendeeService) {}

    public function index(string $organizationSlug, string $eventId): JsonResponse
    {
        try {
            $attendees = $this->attendeeService->getAttendeesForEvent($organizationSlug, $eventId);
            return response()->json($attendees);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Event or organization not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function show(string $organizationSlug, string $eventId, string $id): JsonResponse
    {
        try {
            $attendee = $this->attendeeService->getAttendee($organizationSlug, $id);
            return response()->json($attendee);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Attendee not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function destroy(string $organizationSlug, string $eventId, string $id): JsonResponse
    {
        try {
            $this->attendeeService->deleteAttendee($organizationSlug, $id);
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Attendee not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}