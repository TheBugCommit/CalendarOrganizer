<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalendarEventRequest;
use App\Models\Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDOException;

class EventController extends Controller
{
    /**
     * Store a newly calendar event in database.
     *
     * @param  \Illuminate\Http\CalendarEventRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CalendarEventRequest $request) // TODO request validation
    {
        $event = null;
        try {
            $event = Event::create($request->all());
        } catch (PDOException $pe) {
            return response()->json(['message' => 'Can\'t create event' . $pe->getMessage()], 500);
        }

        if (!$event)
            return response()->json(['message' => 'Can\'t create event'], 500);
        else
            return response()->json($event);
    }

    /**
     * Update the specified calendar event in storage.
     *
     * @param  \Illuminate\Http\CalendarEventRequest  $request
     * @return \Illuminate\Http\ResponseJson
     */
    public function update(CalendarEventRequest $request)
    {
        $event = null;

        try {
            $event = Event::findOrFail($request->id);

            $event->calendar_id = $request->calendar_id ?? $event->calendar_id;
            $event->category_id = $request->category_id ?? $event->category_id;
            $event->user_id     = $request->user_id ?? $event->user_id;
            $event->title       = $request->title ?? $event->title;
            $event->description = $request->description == null ? '' : $request->description;
            $event->location    = $request->location ?? $event->location;
            $event->published   = $request->published ?? $event->published;
            $event->color       = $request->color ?? $event->color;
            $event->start       = $request->start ?? $event->start;
            $event->end         = $request->end ?? $event->end;

            $event->save();
        } catch (ModelNotFoundException  $mnf) {
            return response()->json(['message' => 'Event not found'], 404);
        } catch (PDOException $pdoe) {
            return response()->json(['message' => 'Can\'t update event' . $pdoe->getMessage()], 500);
        }

        return response()->json($event);
    }

    /**
     * Remove the specified calendar event from storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\ResponseJson
     */
    public function destroy(Request $request)
    {
        try {
            Event::destroy($request->id);
        } catch (PDOException $pdoe) {
            return response()->json(['message' => 'Can\'t delete event'], 500);
        }

        return response()->json();
    }
}
