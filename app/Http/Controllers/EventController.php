<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalendarEventRequest;
use App\Models\Event;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDOException;
use RuntimeException;

/**
 * Manage Events
 *
 * @method ResponseJson store(CalendarEventRequest $request)
 * @method ResponseJson update(CalendarEventRequest $request)
 * @method ResponseJson destroy(Request $request)
 *
 * @package App\Http\Controllers
 * @author Gerard Casas
 */
class EventController extends Controller
{
    /**
     * Store a newly calendar event in database.
     *
     * @param  \Illuminate\Http\CalendarEventRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CalendarEventRequest $request)
    {
        $event = null;
        try {
            $event = Event::create($request->all());
        } catch (PDOException $pe) {
            return response()->json(['message' => 'Can\'t create event'], 500);
        }

        if (!$event)
            return response()->json(['message' => 'Can\'t create event'], 500);

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
        $orginal_event = null;
        try {
            $event = Event::findOrFail($request->id);
            $orginal_event = $event;

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

            if($event->published)
                GoogleController::updateGoogleCalendarEvent($event);

            $event->save();
        } catch (ModelNotFoundException  $mnf) {
            return response()->json(['message' => 'Event not found'], 404);
        } catch (PDOException $pdoe) {
            try{
                if($orginal_event->published)
                    GoogleController::updateGoogleCalendarEvent($orginal_event);
            }catch(Exception $ex){}

            return response()->json(['message' => 'Can\'t update event'], 500);
        } catch (RuntimeException $ex) {
            return response()->json(['message' => 'Can\'t update event on google'], 500);
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
        DB::beginTransaction();
        try {
            $event = Event::findOrFail($request->id);

            Event::destroy($request->id);

            if($event->published)
                GoogleController::destroyGoogleCalendarEvent($event);

            DB::commit();
        } catch (PDOException $pdoe) {
            return response()->json(['message' => 'Can\'t delete event'], 500);
        } catch (RuntimeException $ex) {
            DB::rollBack();
            return response()->json(['message' => 'Can\'t delete event on google calendar'], 500);
        }

        return response()->json();
    }
}
