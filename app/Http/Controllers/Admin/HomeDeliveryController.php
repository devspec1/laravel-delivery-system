<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataTables\HomeDeliveryOrderDataTable;
use App\Models\HomeDeliveryOrder;
use App\Models\User;
use App\Models\Country;
use App\Models\Company;
use App\Models\CarType;
use App\Models\ScheduleRide;

use App\Http\Start\Helpers;

use Validator;
use JWTAuth;
use DB;
use DateTime;

class HomeDeliveryController extends Controller
{

    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
        $this->otp_helper = resolve('App\Http\Helper\OtpHelper');        
    }

    /**
     * Load Datatable for Driver
     *
     * @param array $dataTable  Instance of HomeDelivery DataTable
     * @return datatable
     */
    public function index(HomeDeliveryOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.delivery_order.view');
    }

    /**
     * Add a New Home Delivery Order
     *
     * @param array $request  Input values
     * @return redirect     to Home Delivery Order view
     */
    public function add(Request $request, $id=null)
    {
        if($request->isMethod("GET")) {
            //Inactive Company could not add driver
            if (LOGIN_USER_TYPE=='company' && Auth::guard('company')->user()->status != 'Active') {
                abort(404);
            }
    
            $timezone = date_default_timezone_get();
    
            $date_obj = \Carbon\Carbon::now()->setTimezone($timezone);
    
            $data['timezon'] = $timezone;
            $data['country_code_option'] = Country::select('long_name','phone_code')->get();
    
            if (LOGIN_USER_TYPE=='company' && session()->get('currency') != null) {
                $default_currency = Currency::whereCode(session()->get('currency'))->first();
            }
            else {
                $default_currency = view()->shared('default_currency');
            }
            $data['currency_symbol'] = html_string($default_currency->symbol);
    
            return view('admin.delivery_order.add',$data);
        }

        if($request->isMethod("POST")) {
            // Add Driver Validation Rules
            $rules = array(
                'estimate_time'     => 'required',
                'fee'               => 'required',
                'pick_up_location'  => 'required',
                'drop_off_location' => 'required',
                'customer_name'         => 'required',
                'customer_phone_number' => 'required',
                'pick_up_latitude'      => 'required',
                'pick_up_longitude'     => 'required',
                'drop_off_latitude'      => 'required',
                'drop_off_longitude'     => 'required',
            );
            
            // Add Driver Validation Custom Names
            $attributes = array(
                'estimate_time'     => 'Estimate Time',
                'fee'               => 'Fee',
                'pick_up_location'  => 'Pick Up Location',
                'drop_off_location' => 'Drop Off Location',
                'customer_name'         => 'Customer Name',
                'customer_phone_number' => 'Customer Phone Number',
                'pick_up_latitude'      => 'Pick Up Latitude',
                'pick_up_longitude'     => 'Pick Up Longitude',
                'drop_off_latitude'      => 'Drop Off Latitude',
                'drop_off_longitude'     => 'Drop Off Longitude',
            );
                // Edit Rider Validation Custom Fields message
            $messages = array(
                'required'            => ':attribute is required.',
            );
            $validator = Validator::make($request->all(), $rules,$messages, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $order = new HomeDeliveryOrder;

            $time_calc = round((float)$request->estimate_time / 60, 2);

            $order->estimate_time           = (string)$time_calc . ' hours' ;
            $order->fee                     = $request->fee;
            $order->pick_up_location        = $request->pick_up_location ;
            $order->drop_off_location       = $request->drop_off_location ;
            $order->customer_name           = $request->customer_name;
            $order->customer_phone_number   = $request->customer_phone_number;
            $order->pick_up_latitude        = $request->pick_up_latitude;
            $order->pick_up_longitude       = $request->pick_up_longitude;
            $order->drop_off_latitude       = $request->drop_off_latitude;
            $order->drop_off_longitude      = $request->drop_off_longitude;
            
            $order->save();
           
            flashMessage('success', 'Order successfully added');

            return redirect(LOGIN_USER_TYPE.'/home_delivery');
        }

        return redirect(LOGIN_USER_TYPE.'/home_delivery');
    }

    /**
     * Update Home Delivery Order Details
     *
     * @param array $request    Input values
     * @return redirect     to Home Delivery Order View
     */
    public function update(Request $request)
    {   
        if($request->isMethod("GET")) {
            //Inactive Company could not add driver
            if (LOGIN_USER_TYPE=='company' && Auth::guard('company')->user()->status != 'Active') {
                abort(404);
            }
    
            $timezone = date_default_timezone_get();
    
            $date_obj = \Carbon\Carbon::now()->setTimezone($timezone);
    
            $data['timezon'] = $timezone;
            $data['country_code_option'] = Country::select('long_name','phone_code')->get();
    
            if (LOGIN_USER_TYPE=='company' && session()->get('currency') != null) {
                $default_currency = Currency::whereCode(session()->get('currency'))->first();
            }
            else {
                $default_currency = view()->shared('default_currency');
            }
            $data['currency_symbol'] = html_string($default_currency->symbol);

            $data['result'] = HomeDeliveryOrder::find($request->id);

            
            if($data['result']) {
                $c_name = explode(' ', $data['result']->customer_name, 2);
                $data['first_name'] = $c_name[0];
                $data['last_name'] = $c_name[1];
    
                $data['mobile_number'] = str_replace("+61","",$data['result']->customer_phone_number);
                $est_time = (float)$data['result']->estimate_time * 60;
                $data['estimate_time'] = \Carbon\Carbon::now()->addMinutes((int)$est_time);

                return view('admin.delivery_order.edit', $data);
            }
            

            flashMessage('danger', 'Invalid ID');
            return redirect(LOGIN_USER_TYPE.'/home_delivery'); 
        }

        if($request->isMethod("POST")) {
            // Add Driver Validation Rules
            $rules = array(
                'estimate_time'     => 'required',
                'fee'               => 'required',
                'pick_up_location'  => 'required',
                'drop_off_location' => 'required',
                'customer_name'         => 'required',
                'customer_phone_number' => 'required',
                'pick_up_latitude'      => 'required',
                'pick_up_longitude'     => 'required',
                'drop_off_latitude'      => 'required',
                'drop_off_longitude'     => 'required',
            );
            
            // Add Driver Validation Custom Names
            $attributes = array(
                'estimate_time'     => 'Estimate Time',
                'fee'               => 'Fee',
                'pick_up_location'  => 'Pick Up Location',
                'drop_off_location' => 'Drop Off Location',
                'customer_name'         => 'Customer Name',
                'customer_phone_number' => 'Customer Phone Number',
                'pick_up_latitude'      => 'Pick Up Latitude',
                'pick_up_longitude'     => 'Pick Up Longitude',
                'drop_off_latitude'      => 'Drop Off Latitude',
                'drop_off_longitude'     => 'Drop Off Longitude',
            );
            
            // Edit Rider Validation Custom Fields message
            $messages = array(
                'required'            => ':attribute is required.',
            );
            $validator = Validator::make($request->all(), $rules,$messages, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $order = HomeDeliveryOrder::find($request->id);

            $time_calc = round((float)$request->estimate_time / 60, 2);

            $order->estimate_time           = (string)$time_calc . ' hours' ;
            $order->fee                     = $request->fee ;
            $order->pick_up_location        = $request->pick_up_location ;
            $order->drop_off_location       = $request->drop_off_location ;
            $order->customer_name           = $request->customer_name;
            $order->customer_phone_number   = $request->customer_phone_number;
            $order->pick_up_latitude        = $request->pick_up_latitude;
            $order->pick_up_longitude       = $request->pick_up_longitude;
            $order->drop_off_latitude       = $request->drop_off_latitude;
            $order->drop_off_longitude      = $request->drop_off_longitude;
            
            $order->save();
           
            flashMessage('success', 'Order successfully updated');

            return redirect(LOGIN_USER_TYPE.'/home_delivery');
        }

        return redirect(LOGIN_USER_TYPE.'/home_delivery');
    }

    /**
     * Delete Driver
     *
     * @param array $request    Input values
     * @return redirect     to Driver View
     */
    public function delete(Request $request)
    {
        $result= $this->canDestroy($request->id);

        if($result['status'] == 0) {
            flashMessage('error',$result['message']);
            return back();
        }

        try {
            HomeDeliveryOrder::find($request->id)->delete();
        }
        catch(\Exception $e) {
            flashMessage('error','Got a problem on deleting this order. Contact admin, please');
            return back();
        }

        flashMessage('success', 'Deleted Successfully');
        return redirect(LOGIN_USER_TYPE.'/home_delivery');
    }

    // Check Given Order deletable or not
    public function canDestroy($order_id)
    {
        $return  = array('status' => '1', 'message' => '');

        // $driver_trips   = Trips::where('driver_id',$user_id)->count();
        // $user_referral  = ReferralUser::where('user_id',$user_id)->orWhere('referral_id',$user_id)->count();

        // if($driver_trips) {
        //     $return = ['status' => 0, 'message' => 'Driver have some trips, So can\'t delete this driver'];
        // }
        // else if($user_referral) {
        //     $return = ['status' => 0, 'message' => 'Rider have referrals, So can\'t delete this driver'];
        // }
        return $return;
    }
}
