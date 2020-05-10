<?php

/**
 * Token Auth Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Token Auth
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ProfilePicture;
use App\Models\DriverLocation;
use App\Models\DriverAddress;
use App\Models\CarType;
use App\Models\Currency;
use App\Models\Merchant;
use App\Models\Trips;
use App\Models\Language;
use App\Models\PaymentMethod;
use App\Models\Request as RideRequest;
use Validator;
use Session;
use App;
use JWTAuth;
use Auth;

class TokenAuthController extends Controller
{
    /**
     * Constructor
     * 
     */
    public function __construct()
    {
        $this->request_helper = resolve('App\Http\Helper\RequestHelper');
    }

    /**
     * Get User Details
     * 
     * @param Collection User
     *
     * @return Response Array
     */
    protected function getUserDetails($user)
    {
        $invoice_helper = resolve('App\Http\Helper\InvoiceHelper');
        $promo_details = $invoice_helper->getUserPromoDetails($user->id);

        $user_data = array(
            'user_id'           => $user->id,
            'first_name'        => $user->first_name,
            'last_name'         => $user->last_name,
            'mobile_number'     => $user->mobile_number,
            'country_code'      => $user->country_code,
            'email_id'          => $user->email ?? '',
            'user_status'       => $user->status,
            'user_thumb_image'  => @$user->profile_picture->src ?? url('images/user.jpeg'),
            'currency_symbol'   => $user->currency->symbol,
            'currency_code'     => $user->currency->code,
            'payout_id'         => $user->payout_id ?? '',
            'wallet_amount'     => getUserWalletAmount($user->id),
            'promo_details'     => $promo_details,
        );

        // Also sent for rider because mobile team also handle these parameters in rider

        $rider_details = array();
        if($user->user_type == 'Rider' || true) {
            $user->load('rider_location');
            $rider_location = $user->rider_location;
            $rider_details = array(
                'home'          => optional($rider_location)->home ?? '',
                'work'          => optional($rider_location)->work ?? '',
                'home_latitude' => optional($rider_location)->home_latitude ?? '',
                'home_longitude'=> optional($rider_location)->home_longitude ?? '',
                'work_latitude' => optional($rider_location)->work_latitude ?? '',
                'work_longitude'=> optional($rider_location)->work_longitude ?? '',
                'rider_rating'  => getRiderRating($user->id),
            );
        }

        $driver_details = array();
        if($user->user_type == 'Driver' || true) {
            $user->load(['driver_documents','driver_address']);
            $driver_documents = $user->driver_documents;
            $driver_address = $user->driver_address;
            $driver_details = array(
                'car_details'       => CarType::active()->get(),
                'license_front'     => optional($driver_documents)->license_front ?? '',
                'license_back'      => optional($driver_documents)->license_back ?? '',
                'insurance'         => optional($driver_documents)->insurance ?? '',
                'rc'                => optional($driver_documents)->rc ?? '',
                'permit'            => optional($driver_documents)->permit ?? '',
                'vehicle_id'        => optional($driver_documents)->vehicle_id ?? '',
                'vehicle_type'      => optional($driver_documents)->vehicle_type ?? '',
                'vehicle_number'    => optional($driver_documents)->vehicle_number ?? '',
                'address_line1'     => optional($driver_address)->address_line1 ?? '',
                'address_line2'     => optional($driver_address)->address_line2 ?? '',
                'state'             => optional($driver_address)->state ?? '',
                'postal_code'       => optional($driver_address)->postal_code ?? '',
                'company_name'      => $user->company_name,
                'company_id'        => $user->company_id ?? '',
                'driver_rating'     => getDriverRating($user->id),
            );
        }

        return array_merge($user_data,$rider_details,$driver_details);
    }
 
    /**
     * User Resister
     *@param  Get method request inputs
     *
     * @return Response Json 
     */
    public function register(Request $request) 
    {
        $language = $request->language ?? 'en';
        App::setLocale($language);

        $rules = array(
            'mobile_number'   => 'required|regex:/^[0-9]+$/|min:6',
            'user_type'       => 'required|in:Rider,Driver,rider,driver',
            'auth_type'       => 'required|in:facebook,google,apple,email',
            'email_id'        => 'required|max:255|email',
            'password'        => 'required|min:6',
            'first_name'      => 'required',
            'last_name'       => 'required',
            'country_code'    => 'required',
            'device_type'     => 'required',
            'device_id'       => 'required',
            'referral_code'   => 'nullable|exists:users,referral_code',
        );

        if(strtolower($request->user_type) == 'driver') {
            $rules['city'] = 'required';
        }

        if(in_array($request->auth_type,['facebook','google','apple'])) {
            $social_signup = true;
            $rules['auth_id'] = 'required';
        }
       
        $attributes = array(
            'mobile_number' => trans('messages.user.mobile'),
            'referral_code' => trans('messages.referrals.referral_code'), 
        );

        $messages = array(
            'referral_code.exists'  => trans('messages.referrals.enter_valid_referral_code'),
        );

        $validator = Validator::make($request->all(), $rules, $messages, $attributes);

        if($validator->fails())  {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

        //$referral_check = User::whereUserType(ucfirst($request->user_type))->where('referral_code',$request->referral_code)->count();
        $referral_check = User::get()->where('referral_code',$request->referral_code)->count();
        if($request->referral_code != '' && $referral_check == 0) {
            return response()->json([
                'status_code' => '0',
                'status_message' => __('messages.referrals.enter_valid_referral_code')
            ]);
        }

        $mobile_number = $request->mobile_number;
        $user_count = User::where('mobile_number', $mobile_number)->where('user_type', $request->user_type)->count();
        if($user_count > 0) {
            return response()->json([
                'status_code'     => '0',
                'status_message' =>  trans('messages.already_have_account'),
            ]);
        }

        $user_email_count = User::where('email', $request->email_id)->where('user_type', $request->user_type)->count();
        if($user_email_count > 0) {
            return response()->json([
                'status_code'     => '0',
                'status_message' =>  trans('messages.api.email_already_exists'),
            ]);
        }
        
        $user = new User;
        $user->mobile_number    =   $request->mobile_number;
        $user->first_name       =   $request->first_name;
        $user->last_name        =   $request->last_name;
        $user->user_type        =   $request->user_type;
        $user->password         =   $request->password;
        $user->country_code     =   $request->country_code;
        $user->device_type      =   $request->device_type;
        $user->device_id        =   $request->device_id;
        $user->language         =   $language;
        $user->email            =   $request->email_id;
        $user->currency_code    =   get_currency_from_ip();
        $user->used_referral_code = $request->referral_code;

        if(strtolower($request->user_type) =='rider') {
            $user->status           =   "Active";
            if(isset($social_signup)) {
                if($request->auth_type == 'facebook') {
                    $auth_column = 'fb_id';
                }
                else if($request->auth_type == 'google') {
                    $auth_column = 'google_id';
                }
                else {
                    $auth_column = 'apple_id';
                }

                $user->$auth_column = $request->auth_id;

                $photo_source = ucfirst($request->auth_type);
                $image = $request->user_image ?? '';
            }           

            $user->save();                  
        }
        else {
            $user->company_id       =   1;
            $user->status           =   "Car_details";
            $user->save();
            $driver_address                    = new DriverAddress;
            $driver_address->user_id           = $user->id;
            $driver_address->address_line1     = '';
            $driver_address->address_line2     = '';
            $driver_address->city              = $request->city;
            $driver_address->state             = '';
            $driver_address->postal_code       = '';
            $driver_address->save();
        }

        $profile               = new ProfilePicture;
        $profile->user_id      = $user->id;
        $profile->src          = $image ?? '';
        $profile->photo_source = $photo_source ?? 'Local';
        $profile->save();

        $credentials = $request->only('mobile_number', 'password','user_type');
     
        try {
            $token = JWTAuth::attempt($credentials);
            if (!$token) {
                return response()->json(['error' => 'invalid_credentials']);
            }
        }
        catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token']);
        }

        $return_data = array(
            'status_code'       => '1',
            'status_message'    => __('messages.user.register_successfully'),
            'access_token'      => $token,
        );

        $user_data = $this->getUserDetails($user);

        return response()->json(array_merge($return_data,$user_data));
    }

    /**
     * User Socail media Resister & Login 
     * @param Get method request inputs
     *
     * @return Response Json 
     */
    public function apple_callback(Request $request) 
    {
        $client_id = api_credentials('service_id','Apple');

        $client_secret = getAppleClientSecret();

        $params = array(
            'grant_type' 	=> 'authorization_code',
            'code' 		 	=> $request->code,
            'redirect_uri'  => url('api/apple_callback'),
            'client_id' 	=> $client_id,
            'client_secret' => $client_secret,
        );
        
        $curl_result = curlPost("https://appleid.apple.com/auth/token",$params);

        if(!isset($curl_result['id_token'])) {
            $return_data = array(
                'status_code'       => '0',
                'status_message'    => $curl_result['error'],
            );

            return response()->json($return_data);
        }

        $claims = explode('.', $curl_result['id_token'])[1];
        $user_data = json_decode(base64_decode($claims));

        $user = User::where('apple_id', $user_data->sub)->first();

        if($user == '') {
            $return_data = array(
                'status_code'       => '1',
                'status_message'    => 'New User',
                'email_id'          => optional($user_data)->email ?? '',
                'apple_id'          => $user_data->sub,
            );

            return response()->json($return_data);
        }

        $token = JWTAuth::fromUser($user);

        $user_details = $this->getUserDetails($user);

        $return_data = array(
            'status_code'       => '2',
            'status_message'    => 'Login Successfully',
            'apple_email'       => optional($user_data)->email ?? '',
            'apple_id'          => $user_data->sub,
            'access_token'      => $token,
        );

        return response()->json(array_merge($return_data,$user_details));
    }

    /**
     * User Socail media Resister & Login 
     * @param Get method request inputs
     *
     * @return Response Json 
     */
    public function socialsignup(Request $request) 
    {
        $rules = array(
            'auth_type'   => 'required|in:facebook,google,apple',
            'auth_id'     => 'required',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

        if($request->auth_type == 'facebook') {
            $auth_column = 'fb_id';
        }
        else if($request->auth_type == 'google') {
            $auth_column = 'google_id';
        }
        else {
            $auth_column = 'apple_id';
        }

        $user_count = User::where($auth_column,$request->auth_id)->count();

        // Social Login Flow
        if($user_count == 0) {
            return response()->json([
                'status_code'   => '2',
                'status_message'=> 'New User',
            ]);
        }

        $rules =  array(
            'device_type'  =>'required',
            'device_id'    =>'required'
        );

        $messages = array('required'=>':attribute is required.');
        $validator = Validator::make($request->all(), $rules, $messages);
        if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

        $user = User::where($auth_column,$request->auth_id)->first();

        $user->device_id    = $request->device_id;
        $user->device_type  = $request->device_type;
        $user->language     = $request->language;

        $user->currency_code= get_currency_from_ip();
        $user->save();

        $token = JWTAuth::fromUser($user);

        $return_data = array(
            'status_code'       => '1',
            'status_message'    => 'Login Success',
            'access_token'      => $token,
        );

        $user_data = $this->getUserDetails($user);

        return response()->json(array_merge($return_data,$user_data));
    }

    /**
     * User Login
     * @param  Get method request inputs
     *
     * @return Response Json 
     */
    public function login(Request $request)
    {
        $user_id = $request->mobile_number;
        $auth_column   = 'mobile_number';

        $rules = array(
            'mobile_number'   =>'required|regex:/^[0-9]+$/|min:6',
            'user_type'       =>'required|in:Rider,Driver,rider,driver',
            'password'        =>'required',
            'country_code'    =>'required',
            'device_type'     =>'required',
            'device_id'       =>'required',
           // 'language'        =>'required',
        );

        $validator = Validator::make($request->all(), $rules); 

        if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

        $language = $request->language ?? 'en';
        App::setLocale($language);

        $attempt = Auth::attempt([$auth_column => $user_id, 'password' => $request->password,'user_type' =>$request->user_type]);

        if(!$attempt) {
            return response()->json([
                'status_code'    => '0',
                'status_message' => __('messages.credentials'),
            ]);
        }

        $credentials = $request->only($auth_column, 'password','user_type');
    
        try {
            $token = JWTAuth::attempt($credentials);
            if (!$token) {
                return response()->json([
                    'status_code'    => '0',
                    'status_message' => __('messages.credentials'),
                ]);
            }

        }
        catch (JWTException $e) {
            return response()->json([
                'status_code'    => '0',
                'status_message' => 'could_not_create_token',
            ]);
        }

        $user = User::with('company')->where($auth_column, $user_id)->where('user_type',$request->user_type)->first();

        if($user->status == 'Inactive') {
            return response()->json([
                'status_code'     => '0',
                'status_message' => __('messages.inactive_admin'),
           ]);
        }

        if(isset($user->company) && $user->company->status == 'Inactive') {
            return response()->json([
                'status_code'     => '0',
                'status_message' => __('messages.inactive_company'),
           ]);
        }

        $currency_code          = get_currency_from_ip();
        User::whereId($user->id)->update([
            'device_id'     => $request->device_id,
            'device_type'   => $request->device_type,
            'currency_code' => $currency_code,
            'language'=>$request->language
        ]);

        $user = User::where('id', $user->id)->first();
        auth()->setUser($user);

        if(strtolower($request->user_type) != 'rider') {
            $first_car = CarType::active()->first();
            $data = [   
                'user_id'  => $user->id,
                'status'   => 'Offline',
                'car_id'   => optional($user->driver_documents)->vehicle_id ?? $first_car->id,
            ];

            DriverLocation::updateOrCreate(['user_id' => $user->id], $data);
            RideRequest::where('driver_id',$user->id)->where('status','Pending')->update(['status'=>'Cancelled']);
        }

        $language = $user->language ?? 'en';
        App::setLocale($language);

        $return_data = array(
            'status_code'       => '1',
            'status_message'    => __('messages.login_success'),
            'access_token'      => $token,
        );

        $user =$this->getUserDetails($user);
    
        return response()->json(array_merge($return_data,$user));   
    }

    public function language(Request $request)
    {
        $user_details = JWTAuth::parseToken()->authenticate();

        $user= User::find($user_details->id);

        if($user == '') {
            return response()->json([
                'status_code'    => '0',
                'status_message' => __('messages.invalid_credentials'),
            ]);
        }
        $user->language = $request->language;
        $user->save();

        $language = $user->language ?? 'en';

        App::setLocale($language);

        return response()->json([
            'status_code'       => '1',
            'status_message'    => trans('messages.update_success'),
        ]);
    }
    
     /**
     * User Email Validation
     *
     * @return Response in Json
     */
    public function emailvalidation(Request $request)
    {
        $rules = array('email'=> 'required|max:255|email_id|unique:users');

        // Email signup validation custom messages
        $messages = array('required'=>':attribute is required.');

        $validator = Validator::make($request->all(), $rules, $messages);

        if($validator->fails()) {
            return response()->json([
                'status_code'   => '0',
                'status_message'=> 'Email Already exist',
            ]);
        }

        return response()->json([
            'status_code'   => '1',
            'status_message'=> 'Emailvalidation Success',
        ]);
    }

    /**
     * Forgot Password
     * 
     * @return Response in Json
     */ 
    public function forgotpassword(Request $request)
    {
        $rules = array(
            'mobile_number'   => 'required|regex:/^[0-9]+$/|min:6',
            'user_type'       =>'required|in:Rider,Driver,rider,driver',
            'password'        =>'required|min:6',
            'country_code'    =>'required',
            'device_type'     =>'required',
            'device_id'       =>'required'
        );
        $attributes = array(
            'mobile_number'   => 'Mobile Number',
        );

        $validator = Validator::make($request->all(), $rules, $attributes);

        if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }
        $user_check = User::where('mobile_number', $request->mobile_number)->where('user_type', $request->user_type)->first();
        
        if($user_check == '') {
            return response()->json([
                'status_code'    => '0',
                'status_message' => __('messages.invalid_credentials'),
            ]);
        }

        $user = User::whereId($user_check->id)->first();
        $user->password = $request->password;
        $user->device_id = $request->device_id;
        $user->device_type = $request->device_type;
        $user->currency_code = $request->currency_code;
        $user->save();

        $user = User::where('mobile_number', $request->mobile_number)->where('user_type', $request->user_type)->first();

        $token = JWTAuth::fromUser($user);

        auth()->setUser($user);

        if(strtolower($request->user_type) != 'rider') {
            $data = [
                'user_id'  => $user->id,
                'status'   => 'Offline',
                'car_id'   => @$user->driver_documents->vehicle_id!=''? $user->driver_documents->vehicle_id:@$car_detais[0]->id
            ];
            DriverLocation::updateOrCreate(['user_id' => $user->id], $data);
            RideRequest::where('driver_id',$user->id)->where('status','Pending')->update(['status'=>'Cancelled']);
        }

        $return_data = array(
            'status_code'       => '1',
            'status_message'    => __('messages.login_success'),
            'access_token'      => $token,
        );

        $user_data =$this->getUserDetails($user);
        
        return response()->json(array_merge($return_data,$user_data));
    }

    /**
     * Mobile number verification
     * 
     * @return Response in Json
     */ 
    public function numbervalidation(Request $request)
    {
        if(isset($request->language)) {
            $language = $request->language;
        }
        else {
            $language = 'en';
        }
        App::setLocale($language);

        $rules = array(
            'mobile_number'   => 'required|regex:/^[0-9]+$/|min:6',
            'user_type'       =>'required|in:Rider,Driver,rider,driver',
            'country_code'    =>'required',
        );

        if($request->forgotpassword==1) {
            $rules['mobile_number'] = 'required|regex:/^[0-9]+$/|min:6|exists:users,mobile_number';
        }

        $messages = array(
            'mobile_number.required' => trans('messages.mobile_num_required'),
            'mobile_number.exists'   => trans('messages.enter_registered_number'),
        );

        $validator = Validator::make($request->all(), $rules,$messages);
      
        if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

        $mobile_number = $request->mobile_number;

        $user = User::where('mobile_number', $mobile_number)->where('user_type', $request->user_type)->get();
        if($user->count() && $request->forgotpassword != 1) {
            return response()->json([
                'status_message'  => trans('messages.mobile_number_exist'),
                'status_code'     => '0',
            ]);
        }

        if($user->count() <= 0 && $request->forgotpassword == 1) {
            return response()->json([
                'status_message'  => trans('messages.number_does_not_exists'),
                'status_code'     => '0',
            ]);
        }

        $otp = rand(1000,9999);
        $text = __('messages.api.your_otp_is').$otp;
        $to = '+'.$request->country_code.$request->mobile_number;
        $twillio_responce = $this->request_helper->send_message($to,$text);

        /*if($twillio_responce['status_code'] == 0) {
            return response()->json([
                'status_message' => $twillio_responce['message'],
                'status_code' => '0',
                'otp' => '',
            ]);
        }*/

        return response()->json([
            'status_code'    => '1',
            'status_message' => 'Success',
            'otp'           => strval($otp),
        ]);
    }

    /**
     * Updat Device ID and Device Type
     * @param  Get method request inputs
     *
     * @return Response Json 
     */
    public function updateDevice(Request $request)
    {
        $user_details = JWTAuth::parseToken()->authenticate();

        $rules = array(
            'user_type'    =>'required|in:Rider,Driver,rider,driver',
            'device_type'  =>'required',
            'device_id'    =>'required'
        );
        $attributes = array(
            'mobile_number'   => 'Mobile Number',
        );
        $validator = Validator::make($request->all(), $rules, $attributes);

        if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

        $user = User::where('id', $user_details->id)->first();

        if($user == '') {
            return response()->json([
                'status_code'       => '0',
                'status_message'    => trans('messages.api.invalid_credentials'),
            ]);
        }

        User::whereId($user_details->id)->update(['device_id'=>$request->device_id,'device_type'=>$request->device_type]);                
        return response()->json([
            'status_code'     => '1',
            'status_message'  => __('messages.api.updated_successfully'),
        ]);
    }

    public function logout(Request $request)
    {
        $user_details = JWTAuth::parseToken()->authenticate();

        $user = User::where('id', $user_details->id)->first();

        if($user == '') {
            return response()->json([
                'status_code'       => '0',
                'status_message'    => __('messages.api.invalid_credentials'),
            ]);
        }

        if($user->user_type == 'Driver') {

            $trips_count = Trips::where('driver_id',$user_details->id)->whereNotIn('status',['Completed','Cancelled'])->count();

            $driver_location = DriverLocation::where('user_id',$user_details->id)->first();

            if(optional($driver_location)->status == 'Trip' || $trips_count > 0) {
                return response()->json([
                    'status_code'    => '0',
                    'status_message' => __('messages.complete_your_trips'),
                ]); 
            }

            DriverLocation::where('user_id',$user_details->id)->update(['status'=>'Offline']);
            JWTAuth::invalidate($request->token);
            Session::flush();

            $user->device_type = Null;
            $user->device_id = '';
            $user->save();
            
            return response()->json([
                'status_code'     => '1',
                'status_message'  => "Logout Successfully",
            ]); 
        }

        $trips_count = Trips::where('user_id',$user_details->id)->whereNotIn('status',['Completed','Cancelled'])->count();
        if($trips_count) {
            return response()->json([
              'status_code'    => '0',
              'status_message' => __('messages.complete_your_trips'),
            ]);
        }
        //Deactive the Access Token
        JWTAuth::invalidate($request->token);

        Session::flush();

        $user->device_type = Null;
        $user->device_id = '';
        $user->save();

        return response()->json([
            'status_code'     => '1',
            'status_message'  => "Logout Successfully",
        ]);
    }

    public function currency_conversion(Request $request)
    {
        $rules  = [
            'amount' => 'required|numeric|min:0'
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

        $user_details   = JWTAuth::toUser($request->token);
        $currency_code  = $user_details->currency->code;
        $payment_currency = site_settings('payment_currency');

        $price = floatval($request->amount);

        $converted_amount = currencyConvert($currency_code,$payment_currency,$price);

        $gateway = resolve('braintree');
        $customer_id = $user_details->id.$user_details->mobile_number;
        try {
            $customer = $gateway->customer()->find($customer_id);
        }
        catch(\Exception $e) {
            try {
                $newCustomer = $gateway->customer()->create([
                    'id'        => $customer_id,
                    'firstName' => $user_details->first_name,
                    'lastName'  => $user_details->last_name,
                    'email'     => $user_details->email,
                    'phone'     => $user_details->phone_number,
                ]);
            }
            catch(\Exception $e) {
                if($e instanceOf \Braintree\Exception\Authentication) {
                    return response()->json([
                        'status_code' => '0',
                        'status_message' => __('messages.api.authentication_failed'),
                    ]);
                }
                return response()->json([
                    'status_code' => '0',
                    'status_message' => $e->getMessage(),
                ]);
            }
            $customer = $newCustomer->customer;
        }

        $bt_clientToken = $gateway->clientToken()->generate([
            "customerId" => $customer->id
        ]);

        return response()->json([
            'status_code'    => '1',
            'status_message' => 'Amount converted successfully',
            'currency_code'  => $payment_currency,
            'amount'         => $converted_amount,
            'braintree_clientToken' => $bt_clientToken,
        ]);
    }

    public function getSessionOrDefaultCode()
    {
        $currency_code = Currency::defaultCurrency()->first()->code;
    }

    public function currency_list() 
    {
        $currency_list = Currency::active()->orderBy('code')->get();
        $curreny_list_keys = ['code', 'symbol'];

        $currency_list = $currency_list->map(function ($item, $key) use($curreny_list_keys) {
            return array_combine($curreny_list_keys, [$item->code, $item->symbol]);
        })->all();

        if(!empty($currency_list)) { 
            return response()->json([
                'status_message' => 'Currency Details Listed Successfully',
                'status_code'     => '1',
                'currency_list'   => $currency_list
            ]);
        }
        return response()->json([
            'status_code'     => '0',
            'status_message' => 'Currency Details Not Found',
        ]);
    }

    public function language_list() 
    {
        $languages = Language::active()->get();

        $languages = $languages->map(function ($item, $key)  {
            return $item->value;
        })->all();

        if(!empty($languages)) { 
            return response()->json([
                'status_code'   => '1',
                'status_message'=> 'Successfully',
                'language_list' => $languages,
            ]);
        }
        return response()->json([
            'status_code'     => '0',
            'status_message' => 'language Details Not Found',
        ]);
    }

    /**
     * Webhook for integration with GloriaFood 
     * @param Get method request inputs
     *
     * @return Response Json 
     */
    public function gloria_food(Request $request) 
    {

        $result = json_encode($request->all());
        
        $merch = new Merchant;
        $merch->name = 'HelloW';
        $merch->description = $result;

        return response()->json([
            'status_code'     => '1',
            'status_message' => 'Successfully created',
        ]);
    }
}