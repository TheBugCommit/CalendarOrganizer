<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Exception;
use Illuminate\Http\Request;

class CalendarHelpersController extends Controller
{

    /**
     * Shows the view to handle the helpers of a calendar
     *
     * @param int $calendar_id
     * @return \Illuminate\Contracts\View\View
     */
    public function index($calendar_id)
    {
        $helpers = null;
        try {
            $helpers = Calendar::findOrFail($calendar_id)->helpers;
        } catch (Exception $ex) {
            abort(404);
        }

        return view('calendars.helpers_edit', compact('helpers'));
    }

    /**
     * Returns all helpers for a calendar
     *
     * @param int $calendar_id
     * @return JsonResponse
     */
    public function getHelpers($calendar_id)
    {
        $helpers = null;
        try {
            $helpers = Calendar::findOrFail($calendar_id)->helpers;
        } catch (Exception $ex) {
            return response()->json(['message' => 'Calendar doesn\'t found'], 404);
        }

        return response()->json($helpers);
    }

    /**
     * Add a helper to a calendar
     *
     * @param Request  $request
     * @return JsonResponse
     */
    public function addHelper(Request $request)
    {
        $calendar = null;

        try {
            $calendar = Calendar::findOrFail($request->calendar_id);
            $calendar->helpers()->attach($request->user_id);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Calendar or User doesn\'t found'], 404);
        }

        return response()->json(['message' => 'Helper attached']);
    }

    /**
     * Remove a helper from calendar
     *
     * @param Request  $request
     * @return JsonResponse
     */
    public function removeHelper(Request $request)
    {
        $calendar = null;

        try {
            $calendar = Calendar::findOrFail($request->calendar_id);
            $calendar->helpers()->detach($request->user_id);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Calendar or User doesn\'t found'], 404);
        }

        return response()->json(['message' => 'Helper detached']);
    }
}
