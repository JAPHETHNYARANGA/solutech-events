<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function __construct(private EventService $eventService) {}

    public function index(string $organizationSlug): JsonResponse
    {
        try {
            $events = $this->eventService->getEventsForOrganization($organizationSlug);
            return response()->json($events);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch events: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch events'], 500);
        }
    }

    public function store(StoreEventRequest $request, string $organizationSlug): JsonResponse
    {
        Log::info('Event creation request received.', [
            'organization' => $organizationSlug,
            'request_data' => $request->validated(), // Ensure no sensitive data is logged
        ]);

        try {
            $event = $this->eventService->createEvent($organizationSlug, $request->validated());

            Log::info('Event created successfully.', [
                'event_id' => $event->id ?? null,
                'organization' => $organizationSlug
            ]);

            return response()->json($event, 201);
        } catch (\Throwable $e) {
            Log::error('Failed to create event.', [
                'organization' => $organizationSlug,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Failed to create event'], 500);
        }
    }

    public function show(string $organizationSlug, string $id): JsonResponse
    {
        try {
            $event = $this->eventService->getOrganizationEvent($organizationSlug, $id);
            return response()->json($event);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to fetch event'], 500);
        }
    }

    public function update(UpdateEventRequest $request, string $organizationSlug, string $id): JsonResponse
    {
        try {
            $event = $this->eventService->updateEvent($organizationSlug, $id, $request->validated());
            return response()->json($event);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to update event'], 500);
        }
    }

    public function destroy(string $organizationSlug, string $id): JsonResponse
    {
        try {
            $this->eventService->deleteEvent($organizationSlug, $id);
            return response()->json(null, 204);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to delete event'], 500);
        }
    }
}
