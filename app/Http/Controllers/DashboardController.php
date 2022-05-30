<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Manage Dashboard
 *
 * @method View index()
 *
 * @package App\Http\Controllers
 * @author Gerard Casas
 */
class DashboardController extends Controller
{
    /**
     * Displays dashboard view, with user, own calendars, and helper calendars
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('dashboard');
    }
}
