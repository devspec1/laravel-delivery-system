<?php

/**
 * Rider Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Rider
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\RiderDataTable;
use App\Models\User;
use App\Models\Trips;
use App\Models\Wallet;
use App\Models\UsersPromoCode;
use App\Models\Country;
use App\Models\ProfilePicture;
use App\Models\PaymentMethod;
use App\Models\RiderLocation;
use App\Models\ApiCredentials;
use App\Models\ReferralUser;
use App\Http\Start\Helpers;
use Validator;
use DB;

class RiderController extends Controller
{
    /**
     * Load Datatable for Rider
     *
     * @param array $dataTable  Instance of RiderDataTable
     * @return datatable
     */
    public function index(RiderDataTable $dataTable)
    {
        return $dataTable->render('admin.rider.view');
    }

    /**
     * Add a New Rider
     *
     * @param array $request  Input values
     * @return redirect     to Rider view
     */
    public function add(Request $request)
    {
        if($request->isMethod('GET')) {
            $data['country_code_option']=Country::select('long_name','phone_code')->get();
            $data['google_api']=ApiCredentials::where('id','');
            return view('admin.rider.add',$data);
        }
        if($request->submit) {
            $rules = array(
                'first_name'    => 'required',
                'last_name'     => 'required',
                'email'         => 'required|email',
                'mobile_number' => 'required|numeric|regex:/[0-9]{6}/',
                'password'      => 'required',
                'country_code'  => 'required',
                'user_type'     => 'required',
            );

            // Add Rider Validation Custom Names
            $attributes = array(
                'first_name' => trans('messages.user.firstname'),
                'last_name' => trans('messages.user.lastname'),
                'email' => trans('messages.user.email'),
                'password' => trans('messages.user.paswrd'),
                'country_code' => trans('messages.user.country_code'),
                'user_type' => trans('messages.user.user_type'),
                'mobile_number' => trans('messages.user.mobile'),
            );

            // Edit Rider Validation Custom Fields message
            $messages =array(
                'required'                => ':attribute '.trans('messages.home.field_is_required').'',
                'mobile_number.regex' => trans('messages.user.mobile_no'),
            );

            $validator = Validator::make($request->all(), $rules,$messages, $attributes);
            $validator->after(function ($validator) use($request) {
                $user = User::where('mobile_number', $request->mobile_number)->where('user_type', $request->user_type)->count();
                $user_email = User::where('email', $request->email)->where('user_type', $request->user_type)->count();

                if($user) {
                   $validator->errors()->add('mobile_number',trans('messages.user.mobile_no_exists'));
                }

                if($user_email) {
                   $validator->errors()->add('email',trans('messages.user.email_exists'));
                }
            });

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $user = new User;
            $user->first_name   = $request->first_name;
            $user->last_name    = $request->last_name;
            $user->email        = $request->email;
            $user->country_code = $request->country_code;
            $user->mobile_number= $request->mobile_number;
            $user->password     = $request->password;
            $user->user_type    = $request->user_type;
            $user->status       = 'Active';
            $user->save();

            $user_pic = new ProfilePicture;
            $user_pic->user_id      =   $user->id;
            $user_pic->src          =   "";
            $user_pic->photo_source =   'Local';
            $user_pic->save();

            $location = new RiderLocation;
            $location->user_id          =   $user->id;
            $location->home             =   $request->home_location?$request->home_location:'';
            $location->work             =   $request->work_location ? $request->work_location :'';
            $location->home_latitude    =   $request->home_latitude ? $request->home_latitude :'';
            $location->home_longitude   =   $request->home_longitude ? $request->home_longitude : '';
            $location->work_latitude    =   $request->work_latitude ? $request->work_latitude :'';
            $location->work_longitude   =   $request->work_longitude ? $request->work_longitude : '';
            $location->save();
           
            flashMessage('success', 'Added Successfully');
        }
        return redirect('admin/rider');
    }

    /**
     * Update Rider Details
     *
     * @param array $request    Input values
     * @return redirect     to Rider View
     */
    public function update(Request $request)
    {
        if($request->isMethod("GET")) {
            $data['result'] = User::find($request->id);
            if($data['result']) {
                $data['country_code_option']=Country::select('long_name','phone_code')->get();
                $data['location']=RiderLocation::where('user_id', $request->id)->first();
                return view('admin.rider.edit', $data);
            }
            flashMessage('danger', 'Invalid ID');
            return redirect('admin/rider');
        }
        if($request->submit) {
            $rules = array(
                'first_name'    => 'required',
                'last_name'     => 'required',
                'email'         => 'required|email',
                'mobile_number' => 'required|numeric|regex:/[0-9]{6}/',
                'country_code'  => 'required',
            );
            // Edit Rider Validation Custom Fields message
            $messages =array(
                'required'                => ':attribute '.trans('messages.home.field_is_required').'',
                'mobile_number.regex' => trans('messages.user.mobile_no'),
            );
            // Edit Rider Validation Custom Fields Name
            $attributes = array(
                'first_name' => trans('messages.user.firstname'),
                'last_name' => trans('messages.user.lastname'),
                'email' => trans('messages.user.email'),
                'password' => trans('messages.user.paswrd'),
                'country_code' => trans('messages.user.country_code'),
                'mobile_number' => trans('messages.user.mobile'),
            );

            $validator = Validator::make($request->all(), $rules,$messages, $attributes);
            
            if($request->mobile_number != "") {
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
                return back()->withErrors($validator)->withInput();
            }

            $user = User::where('mobile_number', $request->mobile_number)->where('user_type', $request->user_type)->where('id','!=', $request->id)->count();
            if($user) {
                return back()->withErrors(['mobile_number' => trans('messages.user.mobile_no_exists')])->withInput();
            }

            $user_email = User::where('email', $request->email)->where('user_type', $request->user_type)->where('id','!=', $request->id)->count();
            if($user_email) {
                return back()->withErrors(['email' => trans('messages.user.email_exists')])->withInput();
            }

            $user = User::find($request->id);

            $user->first_name   = $request->first_name;
            $user->last_name    = $request->last_name;
            $user->email        = $request->email;
            $user->country_code = $request->country_code;
            if($request->mobile_number != "") {
                $user->mobile_number= $request->mobile_number;
            }
            $user->user_type    = $request->user_type;

            if($request->password != '') {
                $user->password = $request->password;
            }

            $user->setUsedReferralCodeAttribute($request->used_referral_code);


            $user->save();

            $location = RiderLocation::where('user_id',$request->id)->first();
            if($location == '') {
                $location   = new RiderLocation;
            }
            $location->user_id          =   $request->id;
            $location->home             =   $request->home_location?$request->home_location:'';
            $location->work             =   $request->work_location ? $request->work_location :'';
            $location->home_latitude    =   $request->home_latitude ? $request->home_latitude :'';
            $location->home_longitude   =   $request->home_longitude ? $request->home_longitude : '';
            $location->work_latitude    =   $request->work_latitude ? $request->work_latitude :'';
            $location->work_longitude   =   $request->work_longitude ? $request->work_longitude : '';
            $location->save();

            flashMessage('success', trans('messages.user.update_success'));
        }

        return redirect('admin/rider');
    }

    /**
     * Delete Rider
     *
     * @param array $request    Input values
     * @return redirect     to Rider View
     */
    public function delete(Request $request)
    {
        $result= $this->canDestroy($request->id);

        if($result['status'] == 0) {
            flashMessage('error',$result['message']);
            return back();
        }
        try {
            PaymentMethod::where('user_id',$request->id)->delete();
            User::find($request->id)->delete();
        }
        catch(\Exception $e) {
            flashMessage('error','Rider have wallet or promo or trips, So can\'t delete this rider.');
            return back();
        }

        flashMessage('success', 'Deleted Successfully');
        return redirect('admin/rider');
    }

    // Check Given User deletable or not
    public function canDestroy($user_id)
    {
        $return  = array('status' => '1', 'message' => '');

        $user_promo = UsersPromoCode::where('user_id',$user_id)->count();
        $user_wallet = Wallet::where('user_id',$user_id)->count();
        $user_trips = Trips::where('user_id',$user_id)->count();
        $user_referral = ReferralUser::where('user_id',$user_id)->orWhere('referral_id',$user_id)->count();

        if($user_promo || $user_wallet || $user_trips) {
            $return = ['status' => 0, 'message' => 'Rider have wallet or promo or trips, So can\'t delete this rider'];
        }
        else if($user_referral) {
            $return = ['status' => 0, 'message' => 'Rider have referrals, So can\'t delete this rider'];
        }
        return $return;
    }
}