<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(private EventService $eventService) {}

    public function index(): JsonResponse
    {
        return $this->eventService->getEventsForCurrentOrganization();
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'venue' => 'required|string|max:255',
            'date' => 'required|date|after:now',
            'price' => 'required|numeric|min:0',
            'max_attendees' => 'required|integer|min:1',
        ]);

        return $this->eventService->createEvent($data);
    }

    public function show(string $id): JsonResponse
    {
        return $this->eventService->getEvent($id);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'venue' => 'sometimes|string|max:255',
            'date' => 'sometimes|date|after:now',
            'price' => 'sometimes|numeric|min:0',
            'max_attendees' => 'sometimes|integer|min:1',
        ]);

        return $this->eventService->updateEvent($id, $data);
    }

    public function destroy(string $id): JsonResponse
    {
        return $this->eventService->deleteEvent($id);
    }
}