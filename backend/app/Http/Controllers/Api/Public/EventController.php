<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'date' => 'required|date',
            'price' => 'required|numeric|min:0',
        ]);

        $event = Event::create([
            ...$validated,
            'organization_id' => Auth::user()->organization_id
        ]);

        return response()->json([
            'message' => 'Event created successfully',
            'event' => $event
        ], 201);
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