<?php

/**
 * Admin Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Admin
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataTables\AdminusersDataTable;
use Auth;
use DB;
use App\Models\Admin;
use App\Models\User;
use App\Models\Request as RideRequest;
use App\Models\Trips;
use App\Models\Country;
use App\Models\Role;
use App\Models\Currency;
use App\Models\Company;
use App\Http\Start\Helpers;
use Validator;
use Session;

class AdminController extends Controller
{
    protected $helper; // Global variable for instance of Helpers
    
    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load Index View for Dashboard
     *
     * @return view index
     */
    public function index()
    {
        $data['users_count'] = User::count();
        $data['total_driver'] = User::where('user_type','Driver')
                                ->where(function($query)  {
                                    if(LOGIN_USER_TYPE=='company') { //if login user is company then only get company user
                                        $query->where('company_id',Auth::guard('company')->user()->id);
                                    }
                                })
                                ->count();
        $data['total_rider'] = User::where('user_type','Rider')->count();
        $data['today_driver_count'] = User::whereDate('created_at', '=', date('Y-m-d'))
                                        ->where(function($query)  {
                                            if(LOGIN_USER_TYPE=='company') { //if login user is company then only get company drivers
                                                $query->where('company_id',Auth::guard('company')->user()->id);
                                            }
                                        })
                                        ->where('user_type','Driver')
                                        ->count();
        $data['today_rider_count'] = User::whereDate('created_at', '=', date('Y-m-d'))->where('user_type','Rider')->count();

        if(LOGIN_USER_TYPE=='company') {  //if login user is company then revenue calculated from company trips
            /*$data['today_revenue'] = floatval( 
                Trips::whereDate('created_at', '=', date('Y-m-d'))
                ->where('status','Completed')
                ->whereHas('driver',function($q1){
                    $q1->where('company_id',Auth::guard('company')->user()->id);
                })
                ->value(DB::raw("SUM(subtotal_fare + driver_peak_amount - driver_or_company_commission)"))
            );*/

            $data['today_revenue'] = Trips::whereDate('created_at', '=', date('Y-m-d'))
                ->where('status','Completed')
                ->whereHas('driver',function($q1){
                    $q1->where('company_id',Auth::guard('company')->user()->id);
                })
                ->get();
            $data['today_revenue'] = $data['today_revenue']->sum('driver_or_company_earning');
        }else{
            /*$data['today_revenue'] = floatval( 
            Trips::whereDate('created_at', '=', date('Y-m-d'))
            ->where('status','Completed')
            ->value(DB::raw("SUM(access_fee + (peak_amount - driver_peak_amount) + schedule_fare + driver_or_company_commission)")));*/

            $data['today_revenue'] =  Trips::whereDate('created_at', '=', date('Y-m-d'))
            ->where('status','Completed')->get();
            $data['today_revenue'] = $data['today_revenue']->sum('commission');
        }

        $data['today_trips'] = Trips::whereDate('created_at', '=', date('Y-m-d'))
                ->where(function($query)  {
                    if(LOGIN_USER_TYPE=='company') {    //if login user is company then get only company driver's trip
                        $query->whereHas('driver',function($q1){
                            $q1->where('company_id',Auth::guard('company')->user()->id);
                        });
                    }
                })
                ->count();
        $data['total_trips'] = Trips::
                                where(function($query)  {
                                    if(LOGIN_USER_TYPE=='company') {  //if login user is company then get only company driver's trip
                                        $query->whereHas('driver',function($q1){
                                            $q1->where('company_id',Auth::guard('company')->user()->id);
                                        });
                                    }
                                })
                                ->count();
        $data['total_success_trips'] = Trips::where('status','Completed')
        ->where(function($query)  {
            if(LOGIN_USER_TYPE=='company') {  //if login user is company then get only company driver's trip
                $query->whereHas('driver',function($q1){
                    $q1->where('company_id',Auth::guard('company')->user()->id);
                });
            }
        })
        ->count();
        if(LOGIN_USER_TYPE=='company') {   //if login user is company then revenue is sum of trip amount
            /*$data['total_revenue'] = floatval( 
                Trips::where('status','Completed')
                ->whereHas('driver',function($q1){
                    $q1->where('company_id',Auth::guard('company')->user()->id);
                })
                ->value(DB::raw("SUM(subtotal_fare + driver_peak_amount - driver_or_company_commission)"))
            );*/
            $data['total_revenue'] = Trips::where('status','Completed')
                ->whereHas('driver',function($q1){
                    $q1->where('company_id',Auth::guard('company')->user()->id);
                })->get();
            $data['total_revenue'] = $data['total_revenue']->sum('driver_or_company_earning');
        }else{  //if login user is admin then revenue is sum of admin commission
            /*$data['total_revenue'] = floatval( 
                Trips::where('status','Completed')->value(DB::raw("SUM(access_fee + (peak_amount - driver_peak_amount) + schedule_fare + driver_or_company_commission)"))
            );*/
            $data['total_revenue'] = Trips::where('status','Completed')->get();
            $data['total_revenue'] = $data['total_revenue']->sum('commission');
        }

        if(LOGIN_USER_TYPE=='company') {
            $data['admin_paid_amount'] = Trips::where('status','Completed')
                ->where('driver_payout','>',0)
                ->where('payment_mode','<>','Cash')
                ->whereHas('driver',function($q){
                    $q->where('company_id',Auth::guard('company')->user()->id);
                })
                ->whereHas('driver_payment',function($q1){
                    $q1->where('admin_payout_status','Paid');
                })->get();

            $data['admin_paid_amount'] = $data['admin_paid_amount']->sum('driver_payout');

            $data['admin_pending_amount'] = Trips::where('status','Completed')
                ->where('driver_payout','>',0)
                ->where('payment_mode','<>','Cash')
                ->whereHas('driver',function($q){
                    $q->where('company_id',Auth::guard('company')->user()->id);
                })
                ->whereHas('driver_payment',function($q1){
                    $q1->where('admin_payout_status','Pending');
                })->get();

            $data['admin_pending_amount'] = $data['admin_pending_amount']->sum('driver_payout');
        }

        $default_currency = Currency::active()->defaultCurrency()->first();
        if (LOGIN_USER_TYPE=='company' && session()->get('currency') != null) {  //if login user is company then get session currency
            $default_currency = Currency::whereCode(session()->get('currency'))->first();
        }
        $data['currency_code'] = $default_currency->symbol;

        $data['recent_trips'] = RideRequest::
            with(['trips','users','car_type','request'])
            ->where(function($query)  {
                if(LOGIN_USER_TYPE=='company') { //if login user is company then get only company driver's trip
                    $query->whereHas('driver',function($q1){
                        $q1->where('company_id',Auth::guard('company')->user()->id);
                    });
                }
            })
            ->groupBy('group_id')
            ->orderBy('group_id','desc')
            ->limit(10)->get();


        $quarter1 = ['01', '02', '03'];
        $quarter2 = ['04', '05', '06'];
        $quarter3 = ['07', '08', '09'];
        $quarter4 = ['10', '11', '12'];
        $chart = Trips::
            whereRaw('YEAR(created_at) = ?',[date('Y')])
            ->where('status', 'Completed')
            ->where(function($query)  {
                if(LOGIN_USER_TYPE=='company') {  
                    $query->whereHas('driver',function($q1){
                        $q1->where('company_id',Auth::guard('company')->user()->id);
                    });
                }
            });
        $quarter1_chart=clone($chart);
        $quarter2_chart=clone($chart);
        $quarter3_chart=clone($chart);
        $quarter4_chart=clone($chart);

        //if login user is company then total earning is sum of trip amount .If login user is admin then total revenue is sum of admin commission

        $quarter_amount[1]=floatval($quarter1_chart->wherein(DB::raw('MONTH(created_at)'),$quarter1)->get()->sum(LOGIN_USER_TYPE=='company'?'driver_or_company_earning':'commission'));
        $quarter_amount[2]=floatval($quarter2_chart->wherein(DB::raw('MONTH(created_at)'),$quarter2)->get()->sum(LOGIN_USER_TYPE=='company'?'driver_or_company_earning':'commission'));
        $quarter_amount[3]=floatval($quarter3_chart->wherein(DB::raw('MONTH(created_at)'),$quarter3)->get()->sum(LOGIN_USER_TYPE=='company'?'driver_or_company_earning':'commission'));
        $quarter_amount[4]=floatval($quarter4_chart->wherein(DB::raw('MONTH(created_at)'),$quarter4)->get()->sum(LOGIN_USER_TYPE=='company'?'driver_or_company_earning':'commission'));

        $chart_array = [];
        $year = date('Y');
        for($quarter=1;$quarter<=4;$quarter++)
        {
            $array['y'] = $year.' Q'.$quarter;
            $array['amount'] = number_format($quarter_amount[$quarter],2,'.','');
            $chart_array[] = $array;
        }
        $data['line_chart_data'] = json_encode($chart_array);

        return view('admin.index', $data);
    }

    /**
     * Load Datatable for Admin Users
     *
     * @param array $dataTable  Instance of AdminuserDataTable
     * @return datatable
     */
    public function view(AdminusersDataTable $dataTable)
    {
        return $dataTable->render('admin.admin_users.view');
    }

    /**
     * Load Login View
     *
     * @return view login
     */
    public function login()
    {
        return view('admin.login');
    }

    /**
     * Add Admin User Details
     *
     * @param array $request    Input values
     * @return redirect     to Admin Users View
     */
    public function add(Request $request)
    {
        if(!$_POST)
        {
            $data['roles'] = Role::all()->pluck('name','id');
            $data['countries'] = Country::codeSelect();

            return view('admin.admin_users.add', $data);  
        }
        else if($request->submit)
        {
            // Add Admin User Validation Rules
            $rules = array(
                'username'      => 'required|unique:admins',
                'email'         => 'required|email|unique:admins',
                'password'      => 'required',
                'role'          => 'required',
                'status'        => 'required',
                'country_code'  => 'required',
                'mobile_number' => 'required|numeric',
            );

            // Add Admin User Validation Custom Names
            $attributes = array(
                'username'      => 'Username',
                'email'         => 'Email',
                'password'      => 'Password',
                'role'          => 'Role',
                'status'        => 'Status',
                'country_code'  => 'Country Code',
                'mobile_number' => 'Mobile Number',
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attributes); 

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else {
                $admin = new Admin;
                $admin->username = $request->username;
                $admin->email    = $request->email;
                $admin->password = $request->password;
                $admin->status   = $request->status;
                $admin->country_code = $request->country_code;
                $admin->mobile_number   = $request->mobile_number;
                $admin->save();

                // Insert Role Id to role_user table
                $admin->attachRole($request->role); 
                // Call flash message function
                $this->helper->flash_message('success', 'Added Successfully'); 

                return redirect('admin/admin_user');
            }
        }
        else
        {
            return redirect('admin/admin_user');
        }
    }

    /**
     * Update Admin User Details
     *
     * @param array $request    Input values
     * @return redirect     to Admin Users View
     */
    public function update(Request $request)
    {
        if(!$_POST) {
            $data['result']  = Admin::find($request->id);
            $data['roles'] = Role::all()->pluck('name','id');
            $data['countries'] = Country::codeSelect();
            if($data['result']) {
                return view('admin.admin_users.edit', $data);    
            }
            else {
                $this->helper->flash_message('danger', 'Invalid ID'); // Call flash message function
                return redirect('admin/admin_user');
            }
        }
        else if($request->submit) {
            // Edit Admin User Validation Rules
            $rules = array(
                'username'   => 'required|unique:admins,username,'.$request->id,
                'email'      => 'required|email|unique:admins,email,'.$request->id,
                'country_code'     => 'required',
                'mobile_number'     => 'required|numeric',
                'role'       => 'required',
                'status'     => 'required'
            );

            // Edit Admin User Validation Custom Fields Name
            $attributes = array(
                'username'   => 'Username',
                'email'      => 'Email',
                'country_code' => 'Country Code',
                'mobile_number' => 'Mobile Number',
                'role'       => 'Role',
                'status'     => 'Status'
            );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($attributes); 

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $admin = Admin::find($request->id);

            $admin->username = $request->username;
            $admin->email    = $request->email;
            $admin->country_code = $request->country_code;
            $admin->mobile_number = $request->mobile_number;
            $admin->status   = $request->status;
            
            if($request->password != '')
                $admin->password = $request->password;

            Admin::update_role($request->id, $request->role);
            $admin->save();

            $this->helper->flash_message('success', 'Updated Successfully');

            // Redirect to dashboard when current user not have a permission to view admin users
            if(!Auth::guard('admin')->user()->can('manage_admin')) {
                return redirect('admin/dashboard');
            }

            return redirect('admin/admin_user');
        }
        return redirect('admin/admin_user');
    }

    /**
     * Login Authentication
     *
     * @param array $request Input values
     * @return redirect     to dashboard
     */
    public function authenticate(Request $request)
    {
        if($request->getmethod() == 'GET') {
            return redirect()->route('admin_login');
        }

        if ($request->user_type == 'Company') {
            $login_column = is_numeric($request->username)?'mobile_number':'email';

            $company = Company::where($login_column, $request->username)->first();
            if ($company && $company->status != "Inactive") {
                
                $guard = Auth::guard('company')->attempt([$login_column => $request->username, 'password' => $request->password]);
                if ($guard) {
                    return redirect('company/dashboard');
                }
                $this->helper->flash_message('danger', 'Log In Failed. Please Check Your Email(or)Mobile/Password');
                request()->flashExcept('password');
                return redirect('admin/login')->withInput(request()->except('password'));
            }

        }else{
            $admin = Admin::where('username', $request->username)->first();

            if(isset($admin) && $admin->status != 'Inactive') {
                if(Auth::guard('admin')->attempt(['username' => $request->username, 'password' => $request->password])) {
                    return redirect()->intended('admin/dashboard'); // Redirect to dashboard page
                }

                $this->helper->flash_message('danger', 'Log In Failed. Please Check Your Username/Password'); // Call flash message function
                request()->flashExcept('password');
                return redirect('admin/login')->withInput(request()->except('password')); // Redirect to login page
            }

        }

        $this->helper->flash_message('danger', 'Log In Failed. You are Blocked by Admin.'); // Call flash message function
        request()->flashExcept('password');
        return redirect('admin/login')->withInput(request()->except('password')); // Redirect to login page
    }

    /**
     * Admin Logout
     */
    public function logout()
    {
        Auth::guard('admin')->logout();

        return redirect('admin/login');
    }
}
