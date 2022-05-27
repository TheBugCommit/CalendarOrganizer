<?php

namespace App\Http\Controllers;

use Exception;
use PDOException;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller as Controller;
use App\Models\Calendar as ModelsCalendar;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Google\Service\Calendar;
use Google_Client;
use Google_Exception;
use Google_Service_Calendar_Calendar;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Exception;
use Google_Service_Calendar_AclRule;
use Google_Service_Calendar_AclRuleScope;
use Google_Service_Calendar_EventDateTime;

//https://dev.to/gbhorwood/accessing-googles-api-from-your-laravel-api-4ck7

/**
 * [Description GoogleController]
 */
class GoogleController extends Controller
{
    /**
     * Return the url of the google auth.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAuthUrl(Request $request): JsonResponse
    {
        $client = $this->getClient();
        $authUrl = $client->createAuthUrl();

        return response()->json($authUrl, 200);
    }

    /**
     * Save google access token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function postLogin(Request $request): JsonResponse
    {
        $authCode = urldecode($request->input('code'));

        $accessToken = null;
        try {
            $client = $this->getClient();
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode); //'access type' to 'force' and our access is 'offline', we get a refresh token
            $client->setAccessToken($accessToken);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Error feching token ' . $ex->getMessage()], 500);
        }

        try {
            $user = User::find(Auth::user()->id);
            $user->google_access_token_json = json_encode($accessToken);
            $user->save();
        } catch (Exception $ex) {
            return response()->json(['message' => 'Can\'t update user with fetched token'], 500);
        }

        return response()->json($accessToken, 201);
    }

    /**
     * Publish calendar to google calendar
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function publishGoogleCalendar(Request $request): JsonResponse
    {
        $client = $this->getUserClient();
        $createdCalendar = null;
        $service = null;

        DB::beginTransaction();
        try {
            $modelCalendar = ModelsCalendar::findOrFail($request->calendar_id);
            if($modelCalendar->google_calendar_id != null)
                return response()->json(['message' => 'Calendar already published'], 201);

            $calendar = new Google_Service_Calendar_Calendar();
            $calendar->setSummary($modelCalendar->title);
            $calendar->setDescription($modelCalendar->description);
            $calendar->setTimeZone('Europe/Madrid');

            $service = new Google_Service_Calendar($client);
            $createdCalendar = $service->calendars->insert($calendar);

            $modelCalendar->google_calendar_id = $createdCalendar->getId();
            $modelCalendar->save();

            $this->shareGoogleCalendarTargets($modelCalendar);

            DB::commit();
        } catch (Google_Service_Exception $ex) {
            return response()->json(['message' => 'Can\'t publish calendar'], 500);
        } catch (PDOException $ex) {
            try {
                $service->calendars->delete($createdCalendar->getId());
            } catch (Google_Service_Exception $ex) {
            }

            DB::rollBack();
            return response()->json(['message' => 'Can\'t publish calendar'], 500);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Can\'t publish calendar, error iviting targets' ], 500);
        }

        return response()->json($createdCalendar->getId());
    }

    /**
     * Update google calendar
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateGoogleCalendar(Request $request): JsonResponse
    {
        try {

            $modelCalendar = ModelsCalendar::find($request->calendar_id);
            if(!$modelCalendar)
                return response()->json(['message' => 'Calendar not found'], 404);

            $client = $this->getUserClient();
            $service = new Google_Service_Calendar($client);

            $calendar = $service->calendars->get($modelCalendar->google_calendar_id);

            $calendar->setSummary($modelCalendar->title);
            $calendar->setDescription($modelCalendar->description);

            $service->calendars->update($modelCalendar->google_calendar_id, $calendar);
        } catch (Google_Service_Exception $ex) {
            return response()->json(['message' => 'Can\'t update calendar' . $ex->getMessage()], 500);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Can\'t update calendar' . $ex->getMessage()], 500);
        }

        return response()->json();
    }

    /**
     * Destroy google calendar
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroyGoogleCalendar(Request $request): JsonResponse
    {
        try {

            $modelCalendar = ModelsCalendar::find($request->calendar_id);
            if(!$modelCalendar)
                return response()->json(['message' => 'Calendar not found'], 404);

            $client = $this->getUserClient();
            $service = new Google_Service_Calendar($client);

            $service->calendars->delete($modelCalendar->google_calendar_id);
        } catch (Google_Service_Exception $ex) {
            return response()->json(['message' => 'Can\'t delete calendar' . $ex->getMessage()], 500);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Can\'t delete calendar' . $ex->getMessage()], 500);
        }

        return response()->json();
    }

    /**
     * Publish calendar event to google calendar
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function publishGoogleCalendarEvent(Request $request): JsonResponse //falta color
    {
        $service = null;
        $googlEvent = null;

        DB::beginTransaction();
        try {
            $modelEvent = Event::findOrFail($request->event_id);

            $client = $this->getUserClient();
            $service = new Google_Service_Calendar($client);
            $event = new Google_Service_Calendar_Event([
                'summary' => $modelEvent->title,
                'location' => $modelEvent->location,
                'description' => $modelEvent->description,
                'start' => [
                  'dateTime' => Carbon::parse($modelEvent->start)->setTimezone('Europe/Madrid')->toISOString(),
                  'timeZone' => 'Europe/Madrid',
                ],
                'end' => [
                  'dateTime' => Carbon::parse($modelEvent->end)->setTimezone('Europe/Madrid')->toISOString(),
                  'timeZone' => 'Europe/Madrid',
                ],
                'attendees' => $modelEvent->calendar->targets->map(function($target){return ['email' => $target->email];})->all(),
                'reminders' => [
                  'useDefault' => TRUE,
                ],
                'guestsCanInviteOthers' => false,
                'guestsCanModify' => false,
              ]);

            $googlEvent = $service->events->insert($modelEvent->calendar->google_calendar_id, $event, ['sendUpdates' => "all"]);

            $modelEvent->published = true;
            $modelEvent->google_event_id = $googlEvent->getId();
            $modelEvent->save();

            DB::commit();
        } catch (Google_Exception $ex) {
            return response()->json(['message' => 'Can\'t publish calendar event'. $ex->getMessage()], 500);
        } catch (PDOException $ex) {
            try{
                $googlEvent = $service->events->delete($modelEvent->calendar->google_calendar_id, $googlEvent->getId());
            }catch(Google_Service_Exception $gse){
                return response()->json(['message' => $gse->getMessage() ], 500);

            }
            DB::rollBack();
            return response()->json(['message' => 'Can\'t publish calendar event PDO'], 500);
        }

        return response()->json();
    }

    /**
     * Updates google calendar event
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateGoogleCalendarEvent(Request $request): JsonResponse // falta color
    {
        try {
            $modelEvent = Event::findOrFail($request->event_id);

            if($modelEvent->google_event_id == null)
                return response()->json(['message' => 'First publish event'], 401);

            $google_calendar_id = $modelEvent->calendar->google_calendar_id;
            $google_event_id = $modelEvent->google_event_id;

            $client = $this->getUserClient();
            $service = new Google_Service_Calendar($client);
            $event = $service->events->get($google_calendar_id, $google_event_id);

            $event->setSummary($modelEvent->title);
            $event->setDescription($modelEvent->description);

            $end = new Google_Service_Calendar_EventDateTime();
            $end->setDateTime(Carbon::parse($modelEvent->end)->setTimezone('Europe/Madrid')->toISOString());
            $end->setTimeZone('Europe/Madrid');
            $event->setEnd($end);

            $start = new Google_Service_Calendar_EventDateTime();
            $start->setDateTime(Carbon::parse($modelEvent->start)->setTimezone('Europe/Madrid')->toISOString());
            $start->setTimeZone('Europe/Madrid');
            $event->setstart($start);

            $event->attendees = $modelEvent->calendar->targets->map(function($target){return ['email' => $target->email];})->all();

            $service->events->update($modelEvent->calendar->google_calendar_id, $google_event_id, $event, ['sendUpdates' => "all"]);
        } catch (Google_Exception $ex) {
            return response()->json(['message' => 'Can\'t update calendar event'. $ex->getMessage()], 500);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json();
    }

    /**
     * Destroy google calendar event
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroyGoogleCalendarEvent(Request $request): JsonResponse // falta color
    {
        try {
            $modelEvent = Event::findOrFail($request->event_id);

            if($modelEvent->google_event_id == null)
                return response()->json(['message' => 'First publish event'], 401);

            $client = $this->getUserClient();
            $service = new Google_Service_Calendar($client);
            $service->events->delete($modelEvent->calendar->google_calendar_id, $modelEvent->google_event_id, ['sendUpdates' => "all"]);
        } catch (Google_Exception $ex) {
            return response()->json(['message' => 'Can\'t delete calendar event'. $ex->getMessage()], 500);
        } catch (Exception $ex) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        return response()->json();
    }

    /**
     * Publish calendar event to google calendar
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getGoogleCalendarColors(Request $request): JsonResponse
    {
        $colors = [];
        try {
            $client = $this->getUserClient();
            $service = new Google_Service_Calendar($client);

            $allColors = $service->colors->get();

            $colors['calendar'] = $allColors->getCalendar();
            $colors['event'] = $allColors->getEvent();
        } catch (Exception $ex) {
            return response()->json(['message' => 'Can\'t get calendar colors'], 500);
        }

        return response()->json($colors);
    }


    /**
     * Gets a google client
     *
     * @return Google_Client
     */
    private function getClient(): Google_Client
    {
        $configJson = storage_path() . '/app/private/credentials.json';

        $applicationName = env('APP_NAME', 'CalendarOrganizer');

        $client = new Google_Client();
        $client->setApplicationName($applicationName);
        $client->setAuthConfig($configJson);
        $client->setAccessType('offline'); // for refresh token
        $client->setApprovalPrompt('force'); // for refresh token

        $client->setScopes(
            [Calendar::CALENDAR, Calendar::CALENDAR_EVENTS]
        );
        $client->setIncludeGrantedScopes(true);
        return $client;
    }


    /**
     * Returns the authenticated user's google client
     *
     * @return Google_Client
     */
    private function getUserClient(): Google_Client
    {
        $user = User::find(Auth::user()->id);

        $accessTokenJson = stripslashes($user->google_access_token_json);

        $client = $this->getClient();
        $client->setAccessToken($accessTokenJson);

        if ($client->isAccessTokenExpired()) {

            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $client->setAccessToken($client->getAccessToken());

            $user->google_access_token_json = json_encode($client->getAccessToken());
            $user->save();
        }

        return $client;
    }

    private function shareGoogleCalendarTargets(ModelsCalendar $calendar) //pendent de posar camp a la base de dades controlant si ja s li ha enviat la invitacio
    {
        if($calendar->targets == null)
            return;

        $client = $this->getUserClient();
        $service = new Google_Service_Calendar($client);

        foreach($calendar->targets as $target){
            $rule = new Google_Service_Calendar_AclRule();
            $scope = new Google_Service_Calendar_AclRuleScope();

            $scope->setType("user");
            $scope->setValue($target['email']);

            $rule->setScope($scope);
            $rule->setRole("reader");

            $service->acl->insert($calendar->google_calendar_id, $rule);
        }
    }
}
