<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class RedirectIfRiderAuthenticated
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
        if (Auth::guard('web')->guest()) {
            return redirect('signin_rider');
        }

        if (Auth::user()->status == 'Inactive') {
            Auth::logout();
            return redirect('signin_driver');
        }
        
        if(Auth::user()->user_type != 'Rider') {
            abort(403);
        }
        
        return $next($request);
        
    }
}
