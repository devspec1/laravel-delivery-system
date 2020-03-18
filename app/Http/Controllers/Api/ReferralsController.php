<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helper\RequestHelper;
use App\Http\Start\Helpers;
use App\Models\User;
use App\Models\ReferralSetting;
use App\Models\ReferralUser;
use JWTAuth;
use App;

class ReferralsController extends Controller
{
	// Global variable for Helpers instance
	protected $request_helper;

    public function __construct(RequestHelper $request)
    {
    	$this->request_helper = $request;
		$this->helper = new Helpers;
	}

	/**
	 * To Get the referral Users Details
	 * @param  Request $request Get values
	 * @return Response Json
	 */
	public function get_referral_details(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$user = User::where('id', $user_details->id)->first();

		if ($user == '') {
			return response()->json([
				'status_code'		=> '0',
				'status_message'	=> trans('messages.invalid_credentials'),
			]);
		}

		$user_type = $user->user_type;

		$admin_referral_settings = ReferralSetting::whereUserType($user_type)->where('name','apply_referral')->first();

		$referral_amount = 0;
    	if($admin_referral_settings->value) {
        	$referral_amount = $admin_referral_settings->get_referral_amount($user_type);
		}

		$referral_users = ReferralUser::where('user_id',$user_details->id)->get();

		$pending_referrals = array();
		$completed_referrals = array();

		foreach ($referral_users as $referral_user) {
			$temp_details['id'] 			= $referral_user->id;
			$temp_details['name'] 			= $referral_user->referred_user_name;
			$temp_details['profile_image'] 	= $referral_user->referred_user_profile_picture_src;
			$temp_details['start_date'] 	= $referral_user->start_date;
			$temp_details['end_date'] 		= $referral_user->end_date;
			$temp_details['days'] 			= $referral_user->days;
			$temp_details['remaining_days'] = $referral_user->remaining_days;
			$temp_details['trips'] 			= $referral_user->trips;
			$temp_details['remaining_trips']= $referral_user->remaining_trips;
			$temp_details['earnable_amount']= $referral_user->earnable_amount;
			$temp_details['status'] 		= $referral_user->payment_status;

			if($referral_user->payment_status == 'Pending') {
				array_push($pending_referrals,$temp_details);
			}
			else {
				array_push($completed_referrals,$temp_details);
			}
		}

		return response()->json([
			'status_code' 			=> '1',
			'status_message' 		=> trans('messages.success'),
			'apply_referral' 		=> $admin_referral_settings->value,
			'referral_link' 		=> route('redirect_to_app',['type' => strtolower($user_type)]),
			'referral_code'  		=> $user->referral_code,
			'referral_amount' 		=> $referral_amount,
			'pending_amount' 		=> $user->pending_referral_amount,
			'total_earning'  		=> $user->total_referral_earnings,
			'pending_referrals' 	=> $pending_referrals,
			'completed_referrals' 	=> $completed_referrals,
		]);
	}
}