<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticateUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\Nation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Takes care of authenticating and registering users
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * Shows login page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Shows signin page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function signin()
    {
        $nations = Nation::all();

        return view('auth.signin', compact('nations'));
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function authenticate(AuthenticateUserRequest $request)
    {
        if (!Auth::attempt($request->except('_token'))) {
            return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->withInput();
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if($user->locked){
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->withErrors(['locked' => 'Your account is locked please contact with administrator'])->withInput();
        }

        Log::info("User $user->id logged at " . Carbon::now());

        return redirect()->route('dashboard');
    }

    /**
     * Try to register a new user
     *
     * @param RegisterUser $request
     * @return RedirectResponse
     */
    public function register(RegisterUserRequest $request)
    {
        $userData = $request->except('_token', 'password_confirmation');

        $user = null;
        try{
            $user = User::create($userData);
            Auth::login($user, true);
        } catch (QueryException $e) {
            Log::error($e->getMessage());
        }

        return $user ? redirect()->route('dashboard') : back()->withErrors(['errors' => 'Can\'t register user something went wrong'])->withInput();
    }

    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('auth.login');
    }
}
