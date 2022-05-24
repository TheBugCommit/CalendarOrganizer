<?php

namespace App\Http\Controllers;

use App\Classes\Mailer;
use App\Http\Requests\CalendarStoreRequest;
use App\Models\Calendar;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDOException;
use JWTFactory;
use JWTAuth;
use Tymon\JWTAuth\JWT;
use Tymon\JWTAuth\Manager;

class CalendarController extends Controller
{
    /**
     * Returns all calendars of user
     *
     * @return ResponseJson
     */
    public function index()
    {
        return response()->json(Auth::user()->calendars);
    }

    /**
     * Returns all events of calendar
     *
     * @param Request  $request
     * @return JsonResponse
     */
    public function getCalendarEvents(Request $request)
    {
        if (!isset($request->id))
            return response()->json(['message' => 'Missing parameters']);

        try {
            $calendar = Calendar::findOrFail($request->id);
        } catch (ModelNotFoundException $mnf) {
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

        try {
            $calendar = Calendar::create($request->all());
        } catch (PDOException $pe) {
            return response()->json(['message' => 'Can\'t create calendar'], 500);
        }

        if (!$calendar)
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


    /**
     * Send mail to the assistants, requesting that they join the calendar
     *
     * @param Request  $request
     * @return JsonResponse
     */
    public function addHelpers(Request $request, JWT $jwt)
    {
        //  $jwt->invalidate();
        //dd($jwt->checkOrFail());
        foreach ($request->users_email as $user) {
            $customClaims = ['calendar_id' => $request->calendar_id, 'user_email' => $user];

            $factory = JWTFactory::addClaims($customClaims);
            $payload = $factory->make();
            $token = JWTAuth::encode($payload);

            try {
                Mailer::send(
                    ((object)[
                        'view' => 'calendars.helpers_edit',
                        'subject' => 'Invitation to be part of a calendar',
                        'recipients' => [$user],
                        'view_data' => ['token' => $token]
                    ])
                );
            } catch (Exception $ex) {
                //afegir correus que no s'han pogut enviar
            }
        }
    }
}
