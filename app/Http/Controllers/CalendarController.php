<?php

namespace App\Http\Controllers;

use App\Mail\Mailer;
use App\Http\Requests\CalendarStoreRequest;
use App\Mail\GenericMail;
use App\Models\Calendar;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDOException;
use JWTFactory;
use JWTAuth;
use Tymon\JWTAuth\JWT;

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
     * Returns single calendar
     *
     * @param Request $request
     * @return ResponseJson
     */
    public function getCalendar(Request $request)
    {
        return response()->json(Calendar::find($request->id));
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
     * Display the calendar.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $calendar_id = $request->id;
        return view('calendars.calendars_edit', compact('calendar_id'));
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

    public function getHelpers(Request $request)
    {
        $helpers = null;
        try{
            $helpers = Auth::user()->calendars()->where('id', $request->id)->first()->helpers;
        }catch(Exception $ex){
            return response()->json(['message' => 'Can\'t get helpers']);
        }

        return response()->json($helpers);
    }

    /**
     * Display view to manage calendar helpers
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function editHelpers()
    {
        return view('calendars.helpers_edit');
    }

    /**
     * Send mail to the assistants, requesting that they join the calendar
     *
     * - calendar_id is required
     * - users       is required and format must be ['name' => '..', 'email' => '...']
     * - subject     is optional
     *
     *  @param Request  $request
     * @param \Tymon\JWTAuth\JWT $jwt
     * @return JsonResponse
     */
    public function addHelpers(Request $request, JWT $jwt)
    {
        if(!isset($request->users) || !isset($request->calendar_id))
            return response()->json(['message' => 'Missing data'], 400);

        foreach ($request->users as $user) {
            $customClaims = ['calendar_id' => $request->calendar_id, 'user_email' => $user];

            $factory = JWTFactory::addClaims($customClaims);
            $payload = $factory->make();
            $token = JWTAuth::encode($payload);

            $data = [
                'subject' => 'Invitation to be part of a calendar',
                'view'    => 'emails.helper_invitation',
                'varname' => 'data',
                'data'    => ['token' => route('user.become.helper', ['token' => $token])],
            ];

            try{
                $mailer = new Mailer(['name' => '', 'email' => $user], GenericMail::class, $data);
                $mailer->send();
            }catch(Exception $ex){
                return response()->json(['message' => 'Can\'t send all emails'. $ex->getMessage()]);
            }
        }

        return response()->json(['message' => 'Calendar helper invitations sended']);
    }

    /**
     * Removes a helper from calendar
     *
     * @param Request $request
     * @return ResponseJson
     */
    public function removeHelper(Request $request)
    {
        if($request->user_id == null)
            return response()->json(['message' => 'Invalid User'], 400);

        try{
            $calendar = Calendar::findOrFail($request->calendar_id);
            $calendar->helpers()->detach($request->user_id);
        }catch(Exception $ex){
            return response()->json(['message' => 'Can\'t remove helper from calendar ' . $request->calendar_id], 500);
        }

        return response()->json(['message' => 'Helper removed from calendar '. $request->calendar_id]);
    }
}
