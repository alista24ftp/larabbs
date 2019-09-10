<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class RecordLastActivedTime
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // If user authenticated
        if(Auth::check()){
            // record last login time
            Auth::user()->recordLastActivedAt();
        }
        return $next($request);
    }
}
