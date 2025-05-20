<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function __construct(private EventService $eventService) {}

    public function index(string $organizationSlug): JsonResponse
    {
        try {
            $events = $this->eventService->getEventsForOrganization($organizationSlug);
            return response()->json($events);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to fetch events',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request, string $organizationSlug): JsonResponse
{
    try {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'venue' => 'required|string|max:255',
            'date' => 'required|date',
            'price' => 'required|numeric|min:0',
            'max_attendees' => 'required|integer|min:1',
        ]);

        return $this->eventService->createEvent($organizationSlug, $data);
    } catch (Throwable $e) {
        // Log the error message
        Log::error('Failed to create event: ' . $e->getMessage(), [
            'exception' => $e,
            'request_data' => $request->all(),
            'organization_slug' => $organizationSlug
        ]);

        return response()->json([
            'message' => 'Failed to create event',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function show(string $organizationSlug, string $id): JsonResponse
    {
        try {
            return $this->eventService->getOrganizationEvent($organizationSlug, $id);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to fetch event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, string $organizationSlug, string $id): JsonResponse
    {
        try {
            $data = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'venue' => 'sometimes|string|max:255',
                'date' => 'sometimes|date|after:now',
                'price' => 'sometimes|numeric|min:0',
                'max_attendees' => 'sometimes|integer|min:1',
            ]);

            return $this->eventService->updateEvent($organizationSlug, $id, $data);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to update event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $organizationSlug, string $id): JsonResponse
    {
        try {
            return $this->eventService->deleteEvent($organizationSlug, $id);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to delete event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}