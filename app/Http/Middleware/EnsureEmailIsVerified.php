<?php

namespace App\Http\Middleware;

use Closure;

class EnsureEmailIsVerified
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
        // Check for 3 things:
        // 1. Whether user is logged in
        // 2. Whether user email is not yet verified
        // 3. Whether user didn't request email verification related URLs nor logout URL
        if($request->user()
            && !$request->user()->hasVerifiedEmail()
            && !$request->is('email/*', 'logout')) {
            // return response based on response content type
            return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : redirect()->route('verification.notice');
        }
        return $next($request);
    }
}
