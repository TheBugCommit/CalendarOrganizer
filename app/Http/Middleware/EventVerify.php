<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventVerify
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
        if(!isset($request->id))
            abort(401);

        $event = Event::find($request->id);
        if(!$event)
            abort(404);

        if((!Auth::user()->hasEvent($request->id) && Auth::user()->id != $event->calendar->user_id) || $event->published)
            abort(401);

        return $next($request);
    }
}
