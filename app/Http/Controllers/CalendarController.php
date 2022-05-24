<?php

namespace App\Http\Controllers;

use App\Http\Requests\CalendarStoreRequest;
use App\Models\Calendar;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDOException;

class CalendarController extends Controller
{
    /**
     * Returns all calendars of user
     *
     * @return ResponseJson
     */
    public function index ()
    {
        return response()->json(Auth::user()->calendars);
    }

    /**
     * Returns all events of calendar
     *
     * @param Request  $request
     * @return JsonResponse
     */
    public function getCalendarEvents(Request $request){
        if(!isset($request->id))
            return response()->json(['message' => 'Missing parameters']);

        try{
            $calendar = Calendar::findOrFail($request->id);
        }catch(ModelNotFoundException $mnf){
            return response()->json(['message' => 'Calendar not found'], 404);
        }

        return response()->json($calendar->events);
    }

    /**
     * Store a newly calendar in database.
     *
     * @param  \Illuminate\Http\CalendarStoreRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CalendarStoreRequest $request)
    {
        $calendar = null;

        try{
            $calendar = Calendar::create($request->all());
        }catch(PDOException $pe){
            return response()->json(['message' => 'Can\'t create calendar'], 500);
        }

        if(!$calendar)
            return response()->json(['message' => 'Can\'t create calendar'], 500);
        else
            return response()->json($calendar);
    }

    /**
     * Display the specified calendar.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $calendar = Calendar::find($id);
        $categories = Auth::user()->categories;
        return view('calendars.calendars_edit', compact('calendar', 'categories'));
    }

    /**
     * Update the specified calendar in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified calendar from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
