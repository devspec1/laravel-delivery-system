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
                'first_name'    => 'required',
                'last_name'     => 'required',
                'email'         => 'required|email',
                // 'mobile_number' => 'required|regex:/[0-9]{6}/',
                //'used_referral_code' => 'nullable',
                'country_code'  => 'required',
            );
            
            // Add Merchant Validation Custom Names
            $attributes = array(
                'name'              => 'Merchant Name',
                'description'       => 'Description',
                'cuisine_type'      => 'Type of Cuisine',
                'integration_type'  => 'Integration Type',
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

            $validator = Validator::make($request->all(), $rules,$messages, $attributes);
            if($request->mobile_number!="") {
                $validator->after(function ($validator) use($request) {
                    $user = User::where('mobile_number', $request->mobile_number)->where('user_type', $request->user_type)->where('id','!=', $request->id)->count();

                    if($user) {
                       $validator->errors()->add('mobile_number',trans('messages.user.mobile_no_exists'));
                    }
                });
            }
            $validator->after(function ($validator) use($request) {
                $user_email = User::where('email', $request->email)->where('user_type', $request->user_type)->where('id','!=', $request->id)->count();

                if($user_email) {
                    $validator->errors()->add('email',trans('messages.user.email_exists'));
                }
            });

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }

            $country_code = $request->country_code;

            $user = new User;

            $user->first_name   = $request->first_name;
            $user->last_name    = $request->last_name;
            $user->email        = $request->email;
            $user->country_code = $country_code;

            if($request->mobile_number!="") {
                $user->mobile_number = $request->mobile_number;
            }
            $user->user_type    = $request->user_type;
         
            $user->save();

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
            $merchant->shared_secret = Str::uuid();
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
                $data['address']            = DriverAddress::where('user_id',$data['result']->user_id)->first();
                $data['country_code_option']=Country::select('long_name','phone_code')->get();

                $usedRef = User::where('referral_code', $data['result_info']->used_referral_code)->first();
                if($usedRef){
                    $data['referrer'] = $usedRef->id;
                }
                else{
                    $data['referrer'] = null;
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
                'first_name'    => 'required',
                'last_name'     => 'required',
                'email'         => 'required|email',
                'referral_code' => 'required',
                'country_code'  => 'required',
            );
            
            // Edit Driver Validation Custom Names
            $attributes = array(
                'name'              => 'Name',
                'description'       => 'Description',
                'integration_type'  => 'Integration Type',
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

            $validator = Validator::make($request->all(), $rules,$messages, $attributes);
            if($request->mobile_number!="") {
                $validator->after(function ($validator) use($request) {
                    $user = User::where('mobile_number', $request->mobile_number)->where('user_type', $request->user_type)->where('id','!=', $request->id)->count();

                    if($user) {
                       $validator->errors()->add('mobile_number',trans('messages.user.mobile_no_exists'));
                    }
                });
            }
           
            $validator->after(function ($validator) use($request) {
                $user_email = User::where('email', $request->email)->where('user_type', $request->user_type)->where('id','!=', $request->id)->count();

                if($user_email) {
                    $validator->errors()->add('email',trans('messages.user.email_exists'));
                }

                //--- Konstantin N edits: refferal checking for coincidence
                $referral_c = User::where('referral_code', $request->referral_code)->where('user_type', $request->user_type)->where('id','!=', $request->id)->count();

                if($referral_c){
                    $validator->errors()->add('referral_code',trans('messages.referrals.referral_exists'));
                }

            });

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }

            $merchant = Merchant::find($request->id);

            $merchant->name = $request->name;
            $merchant->description = $request->description;
            $merchant->cuisine_type = $request->cuisine_type;
            $merchant->integration_type = $request->integration_type;

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