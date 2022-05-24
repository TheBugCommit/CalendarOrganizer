<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $id = null;

        /*if(isset($request->calendar_id))
            $id = $request->calendar_id;
        else if(isset($request->id))
            $id = $request->id;

        if(!Auth::user()->hasCalendar($id))
            abort(401);*/

        return $next($request);
    }
}
