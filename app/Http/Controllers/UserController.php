<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
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
     * Returns categories of autenticated user
     *
     * @return JsonResponse
     */
    public function getCategories()
    {
        return response()->json(Auth::user()->categories);
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

        $calendar = null;
        try{
            $calendar = Calendar::findOrFail($info->calendar_id);
        }catch(PDOException $pdoe){
            abort(404);
        }

        if(Auth::user()->hasHelperCalendar($calendar->id)){
            $jwt->invalidate();
            return redirect()->route('dashboard')->with('alreadyHelper', $calendar->title);
        }

        $calendar->helpers()->attach(Auth::user()->id);
        $jwt->invalidate();

        return redirect()->route('dashboard')->with('becomeHelper', $calendar->title);
    }
}
