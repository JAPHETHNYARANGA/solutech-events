<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class EventController extends Controller
{
    public function __construct(private EventService $eventService) {}

    public function index(): JsonResponse
    {
        try {
            return $this->eventService->getEventsForCurrentOrganization();
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to fetch events',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'venue' => 'required|string|max:255',
                'date' => 'required|date|after:now',
                'price' => 'required|numeric|min:0',
                'max_attendees' => 'required|integer|min:1',
            ]);

            return $this->eventService->createEvent($data);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to create event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            return $this->eventService->getEvent($id);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to fetch event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, string $id): JsonResponse
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

            return $this->eventService->updateEvent($id, $data);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to update event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            return $this->eventService->deleteEvent($id);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to delete event',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
