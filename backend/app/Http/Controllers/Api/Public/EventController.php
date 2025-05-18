<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterAttendeeRequest;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function __construct(private EventService $eventService) {}

    public function index(): JsonResponse
    {
        $events = $this->eventService->getUpcomingPublicEvents();
        return response()->json($events);
    }

    public function show(string $id): JsonResponse
    {
        $event = $this->eventService->getPublicEvent($id);
        return response()->json($event);
    }

    public function register(RegisterAttendeeRequest $request, string $id): JsonResponse
    {
        $data = $request->validated();
        $response = $this->eventService->registerAttendee($id, $data);
        
        return response()->json($response['data'], $response['status']);
    }
}