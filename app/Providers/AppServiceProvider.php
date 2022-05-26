<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Google_Client;
use Google\Service\Calendar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
       /* $client = new Google_Client();
        $client->setApplicationName('CalendarOrganizer');
        $client->setAuthConfig(storage_path() . '/app/private/credentials.json');
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setScopes([Calendar::CALENDAR, Calendar::CALENDAR_EVENTS]);

        $this->app->instance(Google_Client::class, $client);*/
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
