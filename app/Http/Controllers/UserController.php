<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDOException;
use Tymon\JWTAuth\JWT;

class UserController extends Controller
{
    /**
     * Returns current autenticated user instance
     *
     * @return JsonResponse
     */
    public function me(){
        return response()->json(Auth::user());
    }

    /**
     * Returns all users (only email, full_name)
     *
     * @return JsonResponse
     */
    public function getAllUsers(Request $request)
    {
        return response()->json(User::getAll(['email', 'id']));
    }

    /**
     * From a token, assign a user to a calendar, as a helper
     *
     * @param string $token
     * @return RedirectResponse
     */
    public function becomeHelper($token, JWT $jwt)
    {
        $payload = null;
        try{
            $jwt->setToken($token);
            $payload = $jwt->checkOrFail();
        }catch(Exception $ex){
            abort(401);
        }

        $info = $payload->get('addClaims');
        if($info == null)
            abort(401);

        if($info->user_email != Auth::user()->email)
            abort(401);

        $calendar = null;
        try{
            $calendar = Calendar::findOrFail($info->calendar_id);
        }catch(PDOException $pdoe){
            abort(404);
        }

        if(Auth::user()->hasHelperCalendar($calendar->id)){
            return redirect()->route('dashboard')->with('alreadyHelper', $calendar->title);
        }

        $calendar->helpers()->attach(Auth::user()->id);
        return redirect()->route('dashboard')->with('becomeHelper', $calendar->title);
    }
}
