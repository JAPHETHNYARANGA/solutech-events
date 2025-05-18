<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function __construct(private EventService $eventService) {}

    public function index(): JsonResponse
    {
        $events = $this->eventService->getEventsForCurrentOrganization();
        return response()->json($events);
    }

    public function store(StoreEventRequest $request): JsonResponse
    {
        $data = $request->validated();
        $event = $this->eventService->createEvent($data);
        
        return response()->json($event, 201);
    }

    public function show(string $id): JsonResponse
    {
        $event = $this->eventService->getEvent($id);
        return response()->json($event);
    }

    public function update(UpdateEventRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();
        $event = $this->eventService->updateEvent($id, $data);
        
        return response()->json($event);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->eventService->deleteEvent($id);
        return response()->json(null, 204);
    }
}