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
use App\Models\Request as RideRequest;
use App\Models\DriverLocation;

use App\Http\Start\Helpers;
use App\Http\Helper\RequestHelper;

use Validator;
use JWTAuth;
use DB;
use DateTime;
use App;

class HomeDeliveryController extends Controller
{

    protected $helper;  // Global variable for instance of Helpers

    public function __construct(RequestHelper $request)
    {
        $this->helper = new Helpers;
        $this->otp_helper = resolve('App\Http\Helper\OtpHelper');
        $this->request_helper = $request;        
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

            $user = $this->get_or_create_rider($request);

            $ride_request = $this->create_ride_request($request, $user);

            //create order
            $order = new HomeDeliveryOrder;

            $order->estimate_time           = $request->estimate_time;
            $order->fee                     = $request->fee;
            $order->customer_id             = $user->id;
            $order->ride_request            = $ride_request->id;
            
            $order->save();
          
            flashMessage('success', 'Order successfully added, Sending push messages to nearest drivers...');

            $this->notify_drivers($request, 'New job(s) in your location');

            flashMessage('success', 'Drivers notified');

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

            $data['result'] = HomeDeliveryOrder::find($request->id);

            if($data['result']) {
                
                $user = User::where('id',$data['result']->customer_id)->first();

                $ride_request = RideRequest::where('id', $data['result']->ride_request)->first();

                $data['first_name'] = $user->first_name;

                $data['last_name'] = $user->last_name;

                $data['mobile_number'] = $user->mobile_number;

                $data['country_code_option'] = Country::select('long_name','phone_code')->get();

                $data['country_code'] = $user->country_code;

                $data['result']['pick_up_latitude'] = $ride_request->pickup_latitude;
                $data['result']['pick_up_longitude'] = $ride_request->pickup_longitude;
                $data['result']['pick_up_location'] = $ride_request->pickup_location;
                $data['result']['drop_off_latitude'] = $ride_request->drop_latitude;
                $data['result']['drop_off_longitude'] = $ride_request->drop_longitude;
                $data['result']['drop_off_location'] = $ride_request->drop_location;
        
                $timezone = date_default_timezone_get();
        
                $date_obj = \Carbon\Carbon::now()->setTimezone($timezone);
        
                $data['timezon'] = $timezone;
        
                if (LOGIN_USER_TYPE=='company' && session()->get('currency') != null) {
                    $default_currency = Currency::whereCode(session()->get('currency'))->first();
                }
                else {
                    $default_currency = view()->shared('default_currency');
                }
                $data['currency_symbol'] = html_string($default_currency->symbol);

                $data['estimate_time'] = \Carbon\Carbon::now()->addMinutes((int)$data['result']->estimate_time);

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

            $ride_request = RideRequest::where('id', $order->ride_request)->first();

            $user = $this->get_or_create_rider($request);

            $order->estimate_time               = $request->estimate_time;
            $order->fee                         = $request->fee;
            $ride_request->pickup_location      = $request->pick_up_location;
            $ride_request->drop_location        = $request->drop_off_location;
            $order->customer_id                 = $user->id;
            $ride_request->pickup_latitude      = $request->pick_up_latitude;
            $ride_request->pickup_longitude     = $request->pick_up_longitude;
            $ride_request->drop_latitude        = $request->drop_off_latitude;
            $ride_request->drop_longitude       = $request->drop_off_longitude;
            
            $order->save();
           
            flashMessage('success', 'Order successfully updated');

            return redirect(LOGIN_USER_TYPE.'/home_delivery');
        }

        return redirect(LOGIN_USER_TYPE.'/home_delivery');
    }

    /**
     * Delete Order
     *
     * @param array $request    Input values
     * @return redirect     to Order View
     */
    public function delete(Request $request)
    {
        $result= $this->canDestroy($request->id);

        if($result['status'] == 0) {
            flashMessage('error',$result['message']);
            return back();
        }

        try {
            
            $order = HomeDeliveryOrder::find($request->id);
            $rr = RideRequest::where('id',$order->ride_request)->first();
            $rr->delete();
            $order->delete();
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

    /**
     * custom push notification
     *
     * @return success or fail
     */
    public function send_custom_pushnotification($device_id,$device_type,$user_type,$message)
    {   
        if (LOGIN_USER_TYPE=='company') {
            $push_title = "Message from ".Auth::guard('company')->user()->name;    
        }
        else {
            $push_title = "Message from ".SITE_NAME;   
        }

        try {
            if($device_type == 1) {
                $data       = array('custom_message' => array('title' => $message,'push_title'=>$push_title));
                $this->request_helper->push_notification_ios($message, $data, $user_type, $device_id,$admin_msg=1);
            }
            else {
                $data       = array('custom_message' => array('message_data' => $message,'title' => $push_title ));
                $this->request_helper->push_notification_android($push_title, $data, $user_type, $device_id,$admin_msg=1);
            }
        }
        catch (\Exception $e) {
            logger('Could not send push notification');
        }
    }
    /**
     * Create new rider function
     *
     * @return success or fail
     */
    public function get_or_create_rider($request)
    {  
        //Create user for correct payment calculation
        $language = $request->language ?? 'en';
        App::setLocale($language);

        $user = User::where('mobile_number', $request->mobile_number)
            ->where('user_type','Rider')->first();

        if(!$user){
            $user = new User;
            $user->mobile_number    =   $request->mobile_number;
            $user->first_name       =   $request->first_name;
            $user->last_name        =   $request->last_name;
            $user->user_type        =   'Rider';
            $user->password         =   $request->password;
            $user->country_code     =   $request->country_code;
            $user->language         =   $language;
            $user->email            =   $request->mobile_number . '@rideon.group';
            $user->currency_code    =   get_currency_from_ip();

            $user->save();
        }
        return $user;
    }

    /**
     * Notify nearest drivers
     *
     * @return success or fail
     */
    public function notify_drivers($request, $message)
    {  
        $nearest_cars = DriverLocation::select(DB::raw('*, ( 6371 * acos( cos( radians(' . $request->pick_up_latitude . ') ) * cos( radians( latitude ) ) * cos(radians( longitude ) - radians(' . $request->pick_up_longitude . ') ) + sin( radians(' . $request->pick_up_latitude . ') ) * sin( radians( latitude ) ) ) ) as distance'))
            ->having('distance', '<=', 15)->get();

            foreach ($nearest_cars as $nearest_car) {
                $driver_details = User::where('id', $nearest_car->user_id)->first();

                if($driver_details->device_id != "" && $driver_details->status == "Active")
                {    
                    $this->send_custom_pushnotification($driver_details->device_id,$driver_details->device_type,$driver_details->user_type,$message);    
                }
            }
    }

    /**
     * Create ride request. 
     * Ride request table stores pick up and drop locations
     *
     * @return success or fail
     */
    public function create_ride_request($request, $user)
    {  
        //create ride request
        $ride_request = new RideRequest;
        $ride_request->user_id = $user->id;
        $ride_request->group_id = null;
        $ride_request->pickup_latitude = $request->pick_up_latitude;
        $ride_request->pickup_longitude = $request->pick_up_longitude;
        $ride_request->drop_latitude = $request->drop_off_latitude;
        $ride_request->drop_longitude = $request->drop_off_longitude;
        $ride_request->driver_id = User::where('user_type', 'Driver')->first()->id;
        $ride_request->car_id = '1';
        $ride_request->pickup_location = $request->pick_up_location;
        $ride_request->drop_location = $request->drop_off_location;
        $ride_request->payment_mode = 'Stripe';
        $ride_request->status = 'Accepted';
        $ride_request->timezone = 'Australia/Brisbane';
        $ride_request->location_id = '1';
        $ride_request->additional_fare = '';
        $ride_request->peak_fare = '0';
        $ride_request->save();

        return $ride_request;
    }
}
