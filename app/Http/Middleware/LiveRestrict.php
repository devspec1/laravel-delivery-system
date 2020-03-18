<?php

namespace App\Http\Middleware;

use Session;
use Route;

class LiveRestrict
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if (isLiveEnv()) {
            if (in_array(request()->segment(1),['admin','company'])) {
                $url = url()->current();
                $delete_url = strlen((string)stripos($url,"delete"));
                $admin_unrestricted_url = [
                    '/authenticate',
                    '/search_phone',
                    '/search_cars',
                    '/get_driver',
                    '/driver_list',
                    '/immediate_request',
                    '/manual_booking/cancel',
                    '/manual_booking/store',
                ];

                $unrestricted_routes = [
                    'admin.add_location',
                    'admin.edit_location',
                    'admin.add_manage_fare',
                    'admin.edit_manage_fare',
                    'admin.send_message',
                ];

                $admin_url = explode(url('/'.LOGIN_USER_TYPE), $url);
                
                if (($_POST||$delete_url) && !in_array($admin_url[1],$admin_unrestricted_url) && !in_array($request->route()->getName(),$unrestricted_routes) ) {
                    Session::flash('alert-class', 'alert-error');
                    Session::flash('message', 'Data add,edit & delete Operation are restricted in live.');
                    return redirect(url()->previous());
                }
            }
            else {  // restriction for for driver & rider urls
                $user_unrestricted_url = [
                    'driver_update_profile/{id}',
                    'stripe_payout_preferences',
                    'update_payout_preferences/{id}',
                    'rider_update_profile/{id}'
                ];

                if (in_array(Route::current()->uri(),$user_unrestricted_url) || (Route::current()->uri()=='payout_preferences/{id}' && $_POST)) {
                    Session::flash('alert-class', 'alert-error');
                    Session::flash('message', 'Data add,edit & delete Operation are restricted in live.');
                    return redirect(url()->previous());
                }
            }
        }

        return $next($request);
    }
}
