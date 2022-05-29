<?php

namespace App\Http\Controllers;

use App\Mail\Mailer;
use App\Http\Requests\CalendarStoreRequest;
use App\Http\Requests\TargetsUploadRequest;
use App\Mail\GenericMail;
use App\Models\Calendar;
use App\Models\Target;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * Returns all helper calendars
     *
     * @return ResponseJson
     */
    public function getHelperCalendars()
    {
        return response()->json(Auth::user()->helperCalendars);
    }

    /**
     * Returns all calendars owner, helper
     *
     * @return ResponseJson
     */
    public function getAllMyCalendars()
    {
        return response()->json(array_merge(Auth::user()->helperCalendars->toArray(), Auth::user()->calendars->toArray()));
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
        try {
            $helpers = Auth::user()->calendars()->where('id', $request->id)->first()->helpers;
        } catch (Exception $ex) {
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
        if (!isset($request->users) || !isset($request->calendar_id))
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

            try {
                $mailer = new Mailer(['name' => '', 'email' => $user], GenericMail::class, $data);
                $mailer->send();
            } catch (Exception $ex) {
                return response()->json(['message' => 'Can\'t send all emails' . $ex->getMessage()]);
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
        if ($request->user_id == null)
            return response()->json(['message' => 'Invalid User'], 400);

        try {
            $calendar = Calendar::findOrFail($request->calendar_id);
            $calendar->helpers()->detach($request->user_id);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Can\'t remove helper from calendar ' . $request->calendar_id], 500);
        }

        return response()->json(['message' => 'Helper removed from calendar ' . $request->calendar_id]);
    }

    /**
     * Fetch a .json file and insert or update the passed targets
     *
     * @param Request $request
     * @return Response
     */
    public function uploadTargets(TargetsUploadRequest $request)
    {
        $targets = $request->file()["file"]->get();
        $targets = json_decode($targets, true);

        if($targets == null || !$this->validateJSON($targets))
            return back()->withErrors(['Invalid Json']);

        DB::beginTransaction();
        try{
            foreach($targets['targets'] as $target){
                Target::firstOrCreate([
                    'email'       => $target,
                    'calendar_id' => $request->id
                ]);
            }
            DB::commit();
        }catch(Exception $ex){
            DB::rollBack();
            return back()->withErrors('Something went wrong creating targets');
        }

        return back()->with('success', 'File has been uploaded.');
    }

    protected function validateJSON(&$array)
    {
        if(!array_key_exists('targets',$array) || !is_array($array['targets']))
            return false;

        foreach($array['targets'] as $key => $value){
            if(is_array($value))
                return false;
        }

        return true;
    }
}
