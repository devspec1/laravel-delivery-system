<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Session;
use App;
use App\Models\Language;
use View;
use Request;
use Auth;

class Locale
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

        $locale = Language::where('default_language', '=', '1')->first()->value;
        $session_language = Language::where('value', '=', Session::get('language'))->first();

        if ($session_language) {
            $locale = $session_language->value;
        }

            App::setLocale($locale);
            Session::put('language', $locale);

            if(Auth::check())
            {
                $rider_uri = array("signin_rider", "signin", "signup", "signup_rider","signin_driver", "signin", "signup_driver", "signup_company", "signin_company");

                if (in_array(Request::segment(1), $rider_uri) && Auth::user()->user_type=='Rider')
                {
                  return redirect('/profile');
                }

                if (in_array(Request::segment(1), $rider_uri) && Auth::user()->user_type=='Driver')
                {
                  return redirect('/driver_profile');
                }
            }

            
        
        return $next($request);
    }
}
