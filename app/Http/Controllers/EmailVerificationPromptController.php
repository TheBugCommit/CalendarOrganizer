<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

/**
 * Returns verification email view
 *
 * @method mixed _invoke(Request $request)
 *
 * @package App\Http\Controllers
 * @author Gerard Casas
 */
class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(RouteServiceProvider::DASHBOARD)
                    : view('auth.verify-email');
    }
}
