<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(private EventService $eventService) {}

    // Show all events from all organizations (central domain)
    public function indexAll(): JsonResponse
    {
        $events = Event::with('organization')
            ->where('date', '>', now())
            ->orderBy('date')
            ->get(['id', 'organization_id', 'title', 'venue', 'date', 'price']);

        return response()->json($events);
    }

    // Show single event (tenant domain)
    public function show(string $id): JsonResponse
    {
        return $this->eventService->getPublicEvent($id);
    }

    // Register for event (tenant domain)
    public function register(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ]);

        return $this->eventService->registerAttendee($id, $data);
    }
}