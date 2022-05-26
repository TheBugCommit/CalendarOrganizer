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
use Illuminate\Support\Facades\DB;

use Google\Service\Calendar;
use Google_Client;
use Google_Exception;
use Google_Service_Calendar_Calendar;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Exception;


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
        } catch (PDOException $ex) {
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
            $calendar = new Google_Service_Calendar_Calendar();
            $calendar->setSummary($request->title);
            $calendar->setDescription($request->description);
            $calendar->setTimeZone('Europe/Madrid');

            $service = new Google_Service_Calendar($client);
            $createdCalendar = $service->calendars->insert($calendar);

            $modelCalendar = ModelsCalendar::findOrFail($request->calendar_id);
            $modelCalendar->google_calendar_id = $createdCalendar->getId();
            $modelCalendar->save();
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
        }

        return response()->json($createdCalendar->getId());
    }


    /**
     * Publish calendar event to google calendar
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function publishGoogleCalendarEvent(Request $request): JsonResponse
    {
        $service = null;
        $e = null;

        try {
            $client = $this->getUserClient();
            $service = new Google_Service_Calendar($client);
            $event = new Google_Service_Calendar_Event(array(
                'summary' => 'Google I/O 2015',
                'location' => '800 Howard St., San Francisco, CA 94103',
                'description' => 'A chance to hear more about Google\'s developer products.',
                'start' => array(
                  'dateTime' => '2022-05-28T09:00:00-07:00',
                  'timeZone' => 'Europe/Madrid',
                ),
                'end' => array(
                  'dateTime' => '2022-05-28T17:00:00-07:00',
                  'timeZone' => 'Europe/Madrid',
                ),
                'attendees' => array(
                  array('email' => 'g3casas@gmail.com'),
                  array('email' => 'sbrin@example.com'),
                ),
                'reminders' => array(
                  'useDefault' => FALSE,
                  'overrides' => array(
                    array('method' => 'email', 'minutes' => 24 * 60),
                    array('method' => 'popup', 'minutes' => 10),
                  ),
                ),
              ));

            $ev = Event::findOrFail($request->event_id);
            $e = $service->events->insert($ev->calendar->google_calendar_id, $event);

            $ev->published = true;
            $ev->save();
        } catch (Google_Exception $ex) {
            return response()->json(['message' => 'Can\'t publish calendar event'], 500);
        } catch (PDOException $ex) {
            try{
                $event = $service->events->delete($ev->calendar->google_calendar_id, $e->getId());
            }catch(Google_Service_Exception $gse){
                return response()->json(['message' => $gse->getMessage()], 500);

            }
            return response()->json(['message' => 'Can\'t publish calendar event PDO'], 500);
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
}
