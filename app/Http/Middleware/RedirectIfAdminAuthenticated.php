<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Http\Start\Helpers;

class RedirectIfAdminAuthenticated
{

    /**
     * Creates a new instance of the middleware.
     *
     * @param Guard 
     */
    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if ($request->segment(1)=='company' && Auth::guard('company')->guest()) {
            return redirect('signin_company');
        }elseif($request->segment(1)=='company' && Auth::guard('company')->user()->status == 'Inactive'){
            Auth::guard('company')->logout();
            $this->helper->flash_message('danger', 'Admin deactivate your account..');
            return redirect('signin_company');
        }

        if ($request->segment(1)!='company' && Auth::guard('admin')->guest()) {
            return redirect('admin/login');
        }

        return $next($request);
    }
}
