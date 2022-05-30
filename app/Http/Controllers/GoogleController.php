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
use RuntimeException;

/**
 * Sync calendar and calendar events with google calendar
 *
 * @method RedirectResponse getAuthUrl()
 * @method RedirectResponse postLogin(Request $request)
 * @method RedirectResponse publishGoogleCalendar($token, JWT $jwt)
 * @method static void updateGoogleCalendar(ModelsCalendar $modelCalendar)
 * @method static void destroyGoogleCalendar(ModelsCalendar $modelCalendar)
 * @method RedirectResponse publishGoogleCalendarEvent(Request $request)
 * @method static void updateGoogleCalendarEvent(Event $modelEvent)
 * @method static void destroyGoogleCalendarEvent(Event $modelEvent)
 * @method static Google_Client getClient()
 * @method static Google_Client getUserClient()
 * @method static void shareGoogleCalendarTargets(ModelsCalendar $calendar)
 * @method string parseDate($date)
 *
 * @package App\Http\Controllers
 * @author Gerard Casas
 */
class GoogleController extends Controller
{
    /**
     * Return the url of the google auth.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getAuthUrl(): \Illuminate\Http\RedirectResponse
    {
        $client = self::getClient();
        $authUrl = $client->createAuthUrl();

        return redirect($authUrl);
    }

    /**
     * Save google access token
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postLogin(Request $request): \Illuminate\Http\RedirectResponse
    {
        $authCode = urldecode($request->input('code'));

        $accessToken = null;
        DB::beginTransaction();
        try {
            $client = self::getClient();
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode); //'access type' to 'force' and our access is 'offline', we get a refresh token

            $client->setAccessToken(stripslashes(json_encode($accessToken)));

            $user = User::find(Auth::user()->id);
            $user->google_access_token_json = json_encode($accessToken);
            $user->save();
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            dd($ex->getMessage());
            abort(500);
        }

        return redirect(session()->get('action_google_url', '/'));
    }

    /**
     * Publish calendar to google calendar
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publishGoogleCalendar(Request $request): \Illuminate\Http\RedirectResponse
    {
        $client = null;
        $createdCalendar = null;
        $service = null;

        DB::beginTransaction();
        try {
            $client = self::getUserClient();

            $modelCalendar = ModelsCalendar::findOrFail($request->calendar_id);
            if ($modelCalendar->google_calendar_id != null)
                return  redirect('/calendar_edit/' . $modelCalendar->id)->with('warning', 'Calendar already published');

            $calendar = new Google_Service_Calendar_Calendar();
            $calendar->setSummary($modelCalendar->title);
            $calendar->setDescription($modelCalendar->description);
            $calendar->setTimeZone('Europe/Madrid');

            $service = new Google_Service_Calendar($client);
            $createdCalendar = $service->calendars->insert($calendar);

            $modelCalendar->google_calendar_id = $createdCalendar->getId();
            $modelCalendar->save();

            self::shareGoogleCalendarTargets($modelCalendar);

            DB::commit();
        } catch (Google_Service_Exception $ex) {
            return redirect('/calendar_edit/' . $modelCalendar->id)->withErrors('Can\'t publish calendar' . $ex->getMessage());
        } catch (PDOException $ex) {
            try {
                $service->calendars->delete($createdCalendar->getId());
            } catch (Google_Service_Exception $ex) {
            }

            DB::rollBack();
            return  redirect('/calendar_edit/' . $modelCalendar->id)->withErrors('Can\'t publish calendar');
        } catch (Exception $ex) {
            return redirect('/calendar_edit/' . $modelCalendar->id)->withErrors('Can\'t publish calendar, error iviting targets');
        }
        return redirect('/calendar_edit/' . $modelCalendar->id)->with('success', 'Calendar successfully published');
    }

    /**
     * Update google calendar
     *
     * @param ModelsCalendar $modelCalendar
     * @return void
     */
    public static function updateGoogleCalendar(ModelsCalendar $modelCalendar): void
    {
        try {
            if ($modelCalendar->google_calendar_id == null)
                throw new RuntimeException('First publish calendar');

            $client = self::getUserClient();
            $service = new Google_Service_Calendar($client);

            $calendar = $service->calendars->get($modelCalendar->google_calendar_id);

            $calendar->setSummary($modelCalendar->title);
            $calendar->setDescription($modelCalendar->description);

            $service->calendars->update($modelCalendar->google_calendar_id, $calendar);
        } catch (Google_Service_Exception $ex) {
            throw new RuntimeException('Can\'t update calendar');
        }
    }

    /**
     * Destroy google calendar
     *
     * @param ModelsCalendar $modelCalendar
     * @return void
     */
    public static function destroyGoogleCalendar(ModelsCalendar $modelCalendar): void
    {
        try {
            if ($modelCalendar->google_calendar_id == null)
                throw new RuntimeException('First publish calendar');

            $client = self::getUserClient();
            $service = new Google_Service_Calendar($client);

            $service->calendars->delete($modelCalendar->google_calendar_id);
        } catch (Google_Service_Exception $ex) {
            throw new RuntimeException('Can\'t delete calendar');
        }
    }

    /**
     * Publish calendar event to google calendar
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publishGoogleCalendarEvent(Request $request): \Illuminate\Http\RedirectResponse
    {
        $service = null;
        $googlEvent = null;

        DB::beginTransaction();
        try {
            $modelEvent = Event::findOrFail($request->id);
            if ($modelEvent->published)
                return back()->with('warning', 'This event it\'s already published');

            $client = self::getUserClient();
            $service = new Google_Service_Calendar($client);
            $event = new Google_Service_Calendar_Event([
                'summary' => $modelEvent->title,
                'location' => $modelEvent->location,
                'description' => $modelEvent->description,
                'start' => [
                    'dateTime' => $this->parseDate($modelEvent->start),
                    'timeZone' => 'Europe/Madrid',
                ],
                'end' => [
                    'dateTime' => $this->parseDate($modelEvent->end),
                    'timeZone' => 'Europe/Madrid',
                ],
                'attendees' => $modelEvent->calendar->targets->map(function ($target) {
                    return ['email' => $target->email];
                })->all(),
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
            return back()->withErrors('Can\'t publish calendar event, you publish the calendar?');
        } catch (PDOException $ex) {
            try {
                $googlEvent = $service->events->delete($modelEvent->calendar->google_calendar_id, $googlEvent->getId());
            } catch (Google_Service_Exception $gse) {
            }
            DB::rollBack();
            return back()->withErrors('Can\'t publish calendar event');
        }

        return back()->with('success', 'Event published successfully');
    }

    /**
     * Updates google calendar event
     *
     * @param Event $event
     * @throws RuntimeException if can't update event for some reason
     * @return void
     */
    public static function updateGoogleCalendarEvent(Event $modelEvent): void
    {
        try {
            if ($modelEvent->google_event_id == null)
                throw new Google_Exception('Can\'t update calendar event');

            $google_calendar_id = $modelEvent->calendar->google_calendar_id;
            $google_event_id = $modelEvent->google_event_id;

            $client = self::getUserClient();
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

            $event->attendees = $modelEvent->calendar->targets->map(function ($target) {
                return ['email' => $target->email];
            })->all();

            $service->events->update($modelEvent->calendar->google_calendar_id, $google_event_id, $event, ['sendUpdates' => "all"]);
        } catch (Google_Exception $ex) {
            throw new RuntimeException('Can\'t update calendar event');
        }
    }

    /**
     * Destroy google calendar event
     *
     * @param Event $modelEvent
     * @return void
     */
    public static function destroyGoogleCalendarEvent(Event $modelEvent): void
    {
        try {
            if ($modelEvent->google_event_id == null)
                throw new Google_Exception('First publish event');

            $client = self::getUserClient();
            $service = new Google_Service_Calendar($client);
            $service->events->delete($modelEvent->calendar->google_calendar_id, $modelEvent->google_event_id, ['sendUpdates' => "all"]);
        } catch (Google_Exception $ex) {
            throw new RuntimeException('Can\'t delete calendar event');
        }
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
            $client = self::getUserClient();
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
    private static function getClient(): Google_Client
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
    private static function getUserClient(): Google_Client
    {
        $user = User::find(Auth::user()->id);

        $accessTokenJson = stripslashes($user->google_access_token_json);

        $client = self::getClient();
        $client->setAccessToken($accessTokenJson);

        if ($client->isAccessTokenExpired()) {

            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            $client->setAccessToken($client->getAccessToken());

            $user->google_access_token_json = json_encode($client->getAccessToken());
            $user->save();
        }

        return $client;
    }

    public static function shareGoogleCalendarTargets(ModelsCalendar $calendar) : void
    {
        if ($calendar->targets == null)
            return;

        $client = self::getUserClient();
        $service = new Google_Service_Calendar($client);

        foreach ($calendar->targets as $target) {
            if($target->notifyed) continue;

            $rule = new Google_Service_Calendar_AclRule();
            $scope = new Google_Service_Calendar_AclRuleScope();

            $scope->setType("user");
            $scope->setValue($target['email']);

            $rule->setScope($scope);
            $rule->setRole("reader");

            $service->acl->insert($calendar->google_calendar_id, $rule);

            $target->notifyed = true;
            $target->save();
        }
    }

    private function parseDate($date) : string
    {
        return Carbon::parse($date)->setTimezone('Europe/Madrid')->toDateString() . 'T' . Carbon::parse($date)->setTimezone('Europe/Madrid')->toTimeString();
    }
}
