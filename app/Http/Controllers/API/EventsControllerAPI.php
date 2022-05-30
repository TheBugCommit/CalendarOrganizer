<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExportEventsRequest;
use App\Models\Calendar;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventsControllerAPI extends Controller
{
    public function export(ExportEventsRequest $request)
    {
        try{
            $calendar = Calendar::getById($request->calendar_id, Auth::guard('api')->user()->id);
            if(!$calendar)
                return response()->json(['message' => 'You doesn\'t have this calendar'], 500);

            $events   = $calendar->getEventsByRange($request->start, $request->end);
            $events = $events->map(function($event){
                return [
                    'summary'       =>  $event->title,
                    'description'   =>  $event->description,
                    'location'      =>  $event->location,
                    'color'         =>  $event->color,
                    'start'         => [
                        'dateTime'  => $event->start,
                        'timeZone'  => 'Europe/Madrid'
                    ] ,
                    'end'           => [
                        'dateTime'  => $event->end,
                        'timeZone'  => 'Europe/Madrid'
                    ]
                ];
            });
        }catch(Exception $ex){
            return response()->json(['message' => 'Cannot get events'], 500);
        }

        return response()->json($events);
    }
}
