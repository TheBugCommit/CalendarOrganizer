<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Displays dashboard view, with user, own calendars, and helper calendars
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $helperCalendars = Auth::user()->helperCalendars;
        $calendars = Auth::user()->calendars;

        return view('dashboard', compact('calendars', 'helperCalendars'));
    }
}
