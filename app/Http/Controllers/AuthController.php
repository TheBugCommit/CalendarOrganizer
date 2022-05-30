<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthenticateUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\Nation;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

 /**
 * Takes care of authenticating and registering users
 *
 * @method \Illuminate\Contracts\View\View index()
 * @method \Illuminate\Contracts\View\View signup()
 * @method \Illuminate\Http\RedirectResponse authenticate(AuthenticateUserRequest $request)
 * @method \Illuminate\Http\RedirectResponse register(RegisterUserRequest $request)
 * @method \Illuminate\Http\RedirectResponse logout(Request $request)
 * @method string getRedirectUri()
 * @method null|array getBecomeHelperToken()
 *
 * @package App\Http\Controllers
 * @author Gerard Casas
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
    public function signup()
    {
        $nations = Nation::all();

        return view('auth.signup', compact('nations'));
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function authenticate(AuthenticateUserRequest $request)
    {
        $redirect = $this->getRedirectUri();

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

        return redirect($redirect);
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

        $redirect = $this->getRedirectUri();

        $user = null;
        try{
            $user = User::create($userData);
            event(new Registered($user));
            Auth::login($user);
            return redirect($redirect);
        } catch (QueryException $e) {
            Log::error($e->getMessage());
        }

        return back()->withErrors(['errors' => 'Can\'t register user something went wrong'])->withInput();
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

    /**
     * Generate the redirect uri
     *
     * @return string
     */
    protected function getRedirectUri() : string
    {
        $redirect = "";
        if(($token = $this->getBecomeHelperToken()) != null)
            $redirect = 'become_calendar_helper/' . $token;
        else
            $redirect = '/';

        return $redirect;
    }

    /**
     * Fetch the become helper token
     *
     * @return null|array
     */
    protected function getBecomeHelperToken()
    {
        $intended = redirect()->intended()->getTargetUrl();
        $intended = explode('/', $intended);
        if(count($intended) == 5 && $intended[3] == 'become_calendar_helper' && strlen($intended[4]) > 0){
            session()->put('become_helper_token', $intended[4]);
            return $intended[4];
        } else{
            return null;
        }
    }
}
