<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

use App\Models\HomeDeliveryOrder;
use App\Models\Merchant;
use App\Models\MerchantIntegrationType;
use App\DataTables\MerchantsDataTable;

use App\Models\User;
use App\Models\Country;
use App\Models\Company;
use App\Models\CarType;
use App\Models\ScheduleRide;
use App\Models\Request as RideRequest;
use App\Models\DriverLocation;
use App\Models\ReferralUser;
use App\Models\DriverAddress;

use App\Http\Start\Helpers;
use App\Http\Helper\RequestHelper;

use Validator;
use JWTAuth;
use DB;
use DateTime;
use App;


class MerchantsController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct(RequestHelper $request)
    {
        $this->helper = new Helpers;
        $this->otp_helper = resolve('App\Http\Helper\OtpHelper');
        $this->request_helper = $request;        
    }

    /**
     * Load Datatable for Merchants
     *
     * @param array $dataTable  Instance of Merchants DataTable
     * @return datatable
     */
    public function index(MerchantsDataTable $dataTable)
    {
        return $dataTable->render('admin.merchant.view');
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

            if (LOGIN_USER_TYPE=='company' && Auth::guard('company')->user()->status != 'Active') {
                abort(404);
            }
            $data['integrations'] = MerchantIntegrationType::pluck('name', 'id');
            $data['country_code_option'] = Country::select('long_name','phone_code')->get();
    
            return view('admin.merchant.add', $data);
        }

        if($request->isMethod("POST")) {
            // Add Merchant Validation Rules
            $rules = array(
                'name'              => 'required',
                'description'       => 'required',
                'cuisine_type'      => 'required',
                'integration_type'  => 'required',
                'base_fee'          => 'required',
                'base_distance'     => 'required',
                'surchange_fee'     => 'required',
                'first_name'    => 'required',
                'last_name'     => 'required',
                'email'         => 'required|email',
                // 'mobile_number' => 'required|regex:/[0-9]{6}/',
                //'used_referral_code' => 'nullable',
                'country_code'  => 'required',
            );
            
            switch ($request->integration_type)
            {
                case 2:     // Square Up
                case 3:     // Shopify
                    $rules['shared_secret'] = 'required';
                    break;
            }
                
            // Add Merchant Validation Custom Names
            $attributes = array(
                'name'              => 'Merchant Name',
                'description'       => 'Description',
                'cuisine_type'      => 'Type of Cuisine',
                'integration_type'  => 'Integration Type',
                'base_fee'          => 'Base fee',
                'base_distance'     => 'Base distance',
                'surchange_fee'     => 'Surchange fee',
                'first_name'    => trans('messages.user.firstname'),
                'last_name'     => trans('messages.user.lastname'),
                'email'         => trans('messages.user.email'),
                'mobile_number' => trans('messages.profile.phone'),
                'country_code'   => trans('messages.user.country_code'), 
            );
            // Edit Merchant Validation Custom Fields message
            $messages = array(
                'required'            => ':attribute is required.',
                'mobile_number.regex' => trans('messages.user.mobile_no'),
            );
            $validator = Validator::make($request->all(), $rules,$messages, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }

            $country_code = $request->country_code;

            $user = new User;
            $usedRef = User::find($request->referrer_id);

            $user->first_name   = $request->first_name;
            $user->last_name    = $request->last_name;
            
            if ($usedRef)
                $user->used_referral_code = $usedRef->referral_code;
            else
                $user->used_referral_code = 0;

            $user->email        = $request->email;
            $user->country_code = $country_code;

            if($request->mobile_number!="") {
                $user->mobile_number = $request->mobile_number;
            }
            $user->user_type    = $request->user_type;
         
            $user->save();

            //find user by refferer_id
            if($usedRef) {
                //if there is no reference between users, create it
                $referrel_user = new ReferralUser;
                $referrel_user->referral_id = $user->id;
                $referrel_user->user_id     = $usedRef->id;
                $referrel_user->user_type   = $usedRef->user_type;
                $referrel_user->save();
            }

            $user_address = new DriverAddress;

            $user_address->user_id       = $user->id;
            $user_address->address_line1 = $request->address_line1;
            $user_address->address_line2 = $request->address_line2;
            $user_address->city          = $request->city;
            $user_address->state         = $request->state;
            $user_address->postal_code   = $request->postal_code;
            $user_address->save();

            $merchant = new Merchant;

            $merchant->user_id = $user->id;
            $merchant->name = $request->name;
            $merchant->description = $request->description;
            $merchant->cuisine_type = $request->cuisine_type;
            $merchant->integration_type = $request->integration_type;
            $merchant->delivery_fee  = $request->base_fee;
            $merchant->delivery_fee_per_km = $request->surchange_fee;
            $merchant->delivery_fee_base_distance = $request->base_distance;
            switch ($request->integration_type)
            {
                case 1: // Gloria Food
                    $merchant->shared_secret = Str::uuid();
                    break;
                case 2: // Square Up
                    // curl initiate
                    $ch = curl_init();

                    // API URL to send data
                    $url = 'https://connect.squareupsandbox.com/v2/merchants';
                    curl_setopt($ch, CURLOPT_URL, $url);

                    // SET Header
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Square-Version: 2020-04-22',
                        'Authorization: Bearer ' . $request->shared_secret,
                        'Content-Type: application/json'));

                    // SET Method as a POST
                    curl_setopt($ch, CURLOPT_POST, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    // Execute curl and assign returned data
                    $response  = curl_exec($ch);
                    $tmp = json_decode($response);
                    $square_merchant = $tmp->merchant[0];
                    $merchant->squareup_id = $square_merchant->id;
                    $merchant->shared_secret = $request->shared_secret;

                    // Close curl
                    curl_close($ch);
                    break;
                case 3: // Shopify
                    $merchant->shared_secret = $request->shared_secret;
                    break;
            }
            $merchant->save();

            flashMessage('success', 'Merchant created');

            return redirect(LOGIN_USER_TYPE.'/merchants');
        }

        return redirect(LOGIN_USER_TYPE.'/merchants');
    }

    /**
     * Update Merchants
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

            $data['result'] = Merchant::find($request->id);
            $data['result_info'] = User::find($data['result']->user_id);

            if($data['result']) {

                $data['integrations'] = MerchantIntegrationType::pluck('name', 'id');
                $data['base_fee'] = $data['result']->delivery_fee;
                $data['surchange_fee'] = $data['result']->delivery_fee_per_km;
                $data['base_distance'] = $data['result']->delivery_fee_base_distance;

                $data['address']            = DriverAddress::where('user_id',$data['result']->user_id)->first();
                $data['country_code_option']=Country::select('long_name','phone_code')->get();

                $usedRef = User::where('referral_code', $data['result_info']->used_referral_code)->first();
                if($usedRef){
                    $data['referrer_id'] = $usedRef->id;
                }
                else{
                    $data['referrer_id'] = null;
                }
                
                return view('admin.merchant.edit', $data);
            }

            flashMessage('danger', 'Invalid ID');
            return redirect(LOGIN_USER_TYPE.'/merchants'); 
        }

        if($request->isMethod("POST")) {
            // Edit Driver Validation Rules
            $rules = array(
                'name'              => 'required',
                'description'       => 'required',
                'cuisine_type'      => 'required',
                'integration_type'  => 'required',
                'base_fee'          => 'required',
                'base_distance'     => 'required',
                'surchange_fee'     => 'required',
                'first_name'    => 'required',
                'last_name'     => 'required',
                'email'         => 'required|email',
                'referral_code' => 'required',
                'country_code'  => 'required',
            );
            
            switch ($request->integration_type)
            {
                case 2:     // Square Up
                case 3:     // Shopify
                    $rules['shared_secret'] = 'required';
                    break;
            }

            // Edit Driver Validation Custom Names
            $attributes = array(
                'name'              => 'Name',
                'description'       => 'Description',
                'integration_type'  => 'Integration Type',
                'base_fee'          => 'Base fee',
                'base_distance'     => 'Base distance',
                'surchange_fee'     => 'Surchange fee',
                'cuisine_type'      => 'Type of Cuisine',
                'first_name'    => trans('messages.user.firstname'),
                'last_name'     => trans('messages.user.lastname'),
                'email'         => trans('messages.user.email'),
                'status'        => trans('messages.driver_dashboard.status'),
                'mobile_number' => trans('messages.profile.phone'),
                'country_code'   => trans('messages.user.country_code'),
            );
            
            // Edit Rider Validation Custom Fields message
            $messages = array(
                'required'            => ':attribute is required.',
                'mobile_number.regex' => trans('messages.user.mobile_no'),
            );
            $validator = Validator::make($request->all(), $rules,$messages, $attributes);
            
            $merchant = Merchant::find($request->id);
            $user_id = $merchant->user_id;
           
            $validator->after(function ($validator) use($request, $user_id) {                
                //--- Konstantin N edits: refferal checking for coincidence
                $referral_c = User::where('referral_code', $request->referral_code)->where('user_type', $request->user_type)->where('id','!=', $user_id)->count();

                if($referral_c){
                    $validator->errors()->add('referral_code',trans('messages.referrals.referral_exists'));
                }
            });

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }

            // $merchant = Merchant::find($request->id);

            $merchant->name = $request->name;
            $merchant->description = $request->description;
            $merchant->cuisine_type = $request->cuisine_type;
            $merchant->integration_type = $request->integration_type;
            $merchant->delivery_fee  = $request->base_fee;
            $merchant->delivery_fee_per_km = $request->surchange_fee;
            $merchant->delivery_fee_base_distance = $request->base_distance;
            switch ($request->integration_type)
            {
                case 2: // Square Up
                    // curl initiate
                    $ch = curl_init();

                    // API URL to send data
                    $url = 'https://connect.squareupsandbox.com/v2/merchants';
                    curl_setopt($ch, CURLOPT_URL, $url);

                    // SET Header
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Square-Version: 2020-04-22',
                        'Authorization: Bearer ' . $request->shared_secret,
                        'Content-Type: application/json'));

                    // SET Method as a POST
                    curl_setopt($ch, CURLOPT_POST, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    // Execute curl and assign returned data
                    $response  = curl_exec($ch);
                    $tmp = json_decode($response);
                    $square_merchant = $tmp->merchant[0];
                    $merchant->squareup_id = $square_merchant->id;
                    $merchant->shared_secret = $request->shared_secret;

                    // Close curl
                    curl_close($ch);
                    break;
                case 3: // Shopify
                    $merchant->shared_secret = $request->shared_secret;
                    break;
            }
            $merchant->save();          
            

            $country_code = $request->country_code;

            $user = User::find($merchant->user_id);

            $user->first_name   = $request->first_name;
            $user->last_name    = $request->last_name;
            $user->email        = $request->email;
            $user->country_code = $country_code;
            $user->referral_code = $request->referral_code;
                      
            //find user by refferer_id
            $usedRef = User::find($request->referrer_id);
            if($usedRef){
                //remove old reference if used referral code updated
                if($usedRef->used_referral_code != $user->used_referral_code){
                    $old_reffered = User::where('referral_code', $user->used_referral_code)->first();
                    if($old_reffered){
                        $reference = ReferralUser::where('user_id', $old_reffered->id)->where('referral_id', $user->id)->first();
                        if($reference){
                            $reference->delete();
                        }
                    }
                }

                //get reffernce between referred user and current user
                $reference = ReferralUser::where('user_id', $usedRef->id)->where('referral_id', $user->id)->first();

                if(!$reference) {
                    //if there is no reference between users, create it
                    $referrel_user = new ReferralUser;
                    $referrel_user->referral_id = $user->id;
                    $referrel_user->user_id     = $usedRef->id;
                    $referrel_user->user_type   = $usedRef->user_type;
                    $referrel_user->save();                   
                }

                $user->used_referral_code = $usedRef->referral_code;

            }

            if($request->mobile_number!="") {
                $user->mobile_number = $request->mobile_number;
            }
            $user->user_type    = $request->user_type;
         
            $user->save();

            $user_address = DriverAddress::where('user_id',  $user->id)->first();
            if($user_address == '') {
                $user_address = new DriverAddress;
            }

            $user_address->user_id       = $user->id;
            $user_address->address_line1 = $request->address_line1;
            $user_address->address_line2 = $request->address_line2;
            $user_address->city          = $request->city;
            $user_address->state         = $request->state;
            $user_address->postal_code   = $request->postal_code;
            $user_address->save();

            flashMessage('success', 'Merchant data successfully updated');

            return redirect(LOGIN_USER_TYPE.'/merchants');
        }

        return redirect(LOGIN_USER_TYPE.'/merchants');
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
            $merchant = Merchant::find($request->id);
            $contact_info = User::find($merchant->user_id);
            DriverAddress::where('user_id', $contact_info->id)->delete();
            $contact_info->delete();
            $merchant->delete();
        }
        catch(\Exception $e) {
            flashMessage('error','Got a problem on deleting this merchant. Contact admin, please');
            return back();
        }

        flashMessage('success', 'Deleted Successfully');
        return redirect(LOGIN_USER_TYPE.'/merchants');
    }

    // Check Given Order deletable or not
    public function canDestroy($order_id)
    {
        if ($order_id == 1){
            $return  = array('status' => '0', 'message' => 'Default merchant can\'t be deleted');
        }
        else{
            $return  = array('status' => '1', 'message' => '');
        }
    
        return $return;
    }

    /**
     * Display a referral detail
     *
     * @return \Illuminate\Http\Response
     */
    public function merchant_order_details(Request $request)
    {
        $data['merchant_orders'] = HomeDeliveryOrder::where('merchant_id', $request->id)
            ->join('users as rider', function($join) {
                $join->on('rider.id', '=', 'delivery_orders.customer_id');
            })
            ->join('request as ride_request', function($join) {
                $join->on('ride_request.id', '=', 'delivery_orders.ride_request');
            })
            ->join('merchants', function($join) {
                $join->on('merchants.id', '=', 'delivery_orders.merchant_id');
            })
            ->select([
                'delivery_orders.id as id',
                DB::raw('CONCAT(delivery_orders.estimate_time," mins") as estimate_time'),
                'delivery_orders.driver_id as driver_id', 
                'delivery_orders.created_at as created_at',
                DB::raw('CONCAT(delivery_orders.distance/1000," KM") as distance'),
                'merchants.name as merchant_name',
                'delivery_orders.order_description as order_description',
                DB::raw('CONCAT(delivery_orders.estimate_time," mins") as estimate_time'),
                'delivery_orders.fee as fee',
                'delivery_orders.status as status',
                'ride_request.pickup_location as pick_up_location',
                'ride_request.drop_location as drop_off_location',
                DB::raw('CONCAT(rider.first_name," ",rider.last_name) as customer_name'),
                DB::raw('CONCAT("+",rider.country_code,rider.mobile_number) as mobile_number'),
            ])
            ->get();

        if($data['merchant_orders']->count() == 0) {
            flashMessage('error','Invalid ID');
            return back();
        }

        $data['merchant_name'] = Merchant::where('id', $request->id)
            ->get('name')
            ->first()
            ->name;

        return view('admin.delivery_order.details', $data);
    }
}