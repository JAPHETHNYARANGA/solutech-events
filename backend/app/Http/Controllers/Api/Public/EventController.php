<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(private EventService $eventService) {}

    public function index(): JsonResponse
    {
        return $this->eventService->getUpcomingPublicEvents();
    }

    public function show(string $id): JsonResponse
    {
        return $this->eventService->getPublicEvent($id);
    }

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