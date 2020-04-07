<?php

/**
 * User Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    User
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\EmailController;
use App\Models\CarType;
use App\Models\DriverAddress;
use App\Models\DriverDocuments;
use App\Models\PasswordResets;
use App\Models\ProfilePicture;
use App\Models\RiderLocation;
use App\Models\PaymentGateway;
use App\Models\PayoutPreference;
use App\Models\PayoutCredentials;
use App\Models\Country;
use App\Models\Currency;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Company;
use Auth;
use App;
use DateTime;
use Session;
use Validator;
use Input;
use Google_Client;

class UserController extends Controller
{
	public function __construct()
	{
		$this->request_helper = resolve('App\Http\Helper\RequestHelper');
		$this->otp_helper = resolve('App\Http\Helper\OtpHelper');
		$this->helper = resolve('App\Http\Start\Helpers');
		$this->fb = resolve('App\Http\Helper\FacebookHelper');
	}

	/**
	 * Redirect the user to the Facebook authentication page.
	 *
	 * @return Response
	 */
	public function facebook_login()
	{
		if(env('APP_ENV') == 'production') {
			return redirect(FB_URL);
		}
		flashMessage('danger', trans('messages.facebook_https_error'));
		return redirect('signin_rider');
	}

	//login functionality Rider
	public function login(Request $request)
	{
		$data = $request;

		$data = json_decode($data['data']);
		foreach ($data as $key => $credential) {
			if ($key == 'email_phone') {
				if (is_numeric($credential)) {

					if (strlen($credential) < 6) {
						return ['status' => 'false', 'error' => trans('messages.home.invalid_mobile_no'), 'success' => 'false'];
					}
					if ($data->user_type=='Company') {
						$company = Company::where('mobile_number', $credential)->first();
						if ($company) {
							if ($company->status == "Inactive") {
								Auth::guard('company')->logout();
								return ['status' => 'false', 'error' => trans('messages.user.disabled_company_account'), 'success' => 'true'];
							}
							Session::put('login_type', 'mobile_number');
							return ['status' => 'true', 'error' => '', 'success' => 'false', 'user_detail' => '+' . $company->country_code . ' ' . $company->mobile_number];
						} else {
							return ['status' => 'false', 'error' => trans('messages.user.no_recognize') . SITE_NAME, 'success' => 'false'];
						}
					}
					else{
						$user = User::where('mobile_number', $credential)->where('user_type', $data->user_type)->first();
						if ($user) {
							if ($user->status == "Inactive") {
								Auth::guard('web')->logout();
								return ['status' => 'false', 'error' => trans('messages.user.disabled_account'), 'success' => 'true'];
							}
							Session::put('login_type', 'mobile_number');
							return ['status' => 'true', 'error' => '', 'success' => 'false', 'user_detail' => '+' . $user->country_code . ' ' . $user->mobile_number];
						} else {
							return ['status' => 'false', 'error' => trans('messages.user.no_recognize') . SITE_NAME, 'success' => 'false'];
						}
					}

				} elseif (filter_var($credential, FILTER_VALIDATE_EMAIL)) {
					if ($data->user_type=='Company') {
						$company = Company::where('email', $credential)->first();
						if ($company) {
							if ($company->status == "Inactive") {
								Auth::guard('company')->logout();
								return ['status' => 'false', 'error' => trans('messages.user.disabled_company_account'), 'success' => 'true'];
							}
							Session::put('login_type', 'email');
							Session::put('email', $company->email);
							return ['status' => 'true', 'error' => '', 'success' => 'false', 'user_detail' => $company->email];
						} else {
							return ['status' => 'false', 'error' => trans('messages.user.no_recognize_email',['site'=>SITE_NAME]), 'success' => 'false'];
						}
					}else{
						$user = User::where('email', $credential)->where('user_type', $data->user_type)->first();
						if ($user) {
							if ($user->status == "Inactive") {
								Auth::guard('web')->logout();
								return ['status' => 'false', 'error' => trans('messages.user.disabled_account'), 'success' => 'true'];
							}
							Session::put('login_type', 'email');
							Session::put('email', $user->email);
							return ['status' => 'true', 'error' => '', 'success' => 'false', 'user_detail' => $user->email];
						} else {
							return ['status' => 'false', 'error' => trans('messages.user.no_recognize_email') . SITE_NAME, 'success' => 'false'];
						}
					}

				} else {
					return ['error' => trans('messages.account.valid_email'), 'status' => 'false', 'success' => 'false'];
				}

			} elseif ($key == 'password') {

				if (Session::get('login_type') == 'email' || Session::get('login_type') == 'mobile_number') {
					if (is_numeric($data->email)) {

						if ($data->user_type=='Company') {
							$guard = Auth::guard('company')->attempt(['mobile_number' => $data->email, 'password' => $data->password]);
						}else{
							$guard = Auth::guard('web')->attempt(['mobile_number' => $data->email, 'password' => $data->password, 'user_type' => $data->user_type]);
						}

						if ($guard) {
							if ($data->user_type=='Company') {
								if (Auth::guard('company')->user()->status=='Pending') {
									flashMessage('success', 'Your profile status is in  pending.If your are not submit your profile detail please provide it.Otherwise please wait until admin verify your account.');
								}elseif (Auth::guard('company')->user()->status=='Inactive') {
									flashMessage('danger', 'Admin deactivate your account..');
								}
							}
							return ['status' => 'true', 'error' => '', 'success' => 'true'];
						} else {
							return ['error' => trans('messages.user.no_paswrd') , 'status' => 'false', 'success' => 'false'];
						}

					} else {

						if ($data->user_type=='Company') {
							$guard = Auth::guard('company')->attempt(['email' => $data->email, 'password' => $data->password]);
						}else{
							$guard = Auth::guard('web')->attempt(['email' => $data->email, 'password' => $data->password, 'user_type' => $data->user_type]);
						}

						if ($guard) {
							if ($data->user_type=='Company') {
								if (Auth::guard('company')->user()->status=='Pending') {
									flashMessage('success', 'Your profile status is in  pending.If your are not submit your profile detail please provide it.Otherwise please wait until admin verify your account.');
								}elseif (Auth::guard('company')->user()->status=='Inactive') {
									flashMessage('danger', 'Admin deactivate your account..');
								}
							}
							return ['status' => 'true', 'error' => '', 'success' => 'true'];
						} else {
							return ['error' =>  trans('messages.user.no_paswrd'), 'status' => 'false', 'success' => 'false'];
						}
					}

				}
			}
		}
	}

	//login functionality Driver
	public function login_driver(Request $request)
	{
		$rules = array(
			'email' => 'required|email',
			'password' => 'required|min:6',
		);

		$messages = array(
			'required'                => ':attribute '.trans('messages.home.field_is_required').'',
		);


		$attributes = array(
			'email' => trans('messages.user.email'),
			'password' => trans('messages.user.paswrd'),
		);

		$validator = Validator::make($request->all(), $rules, $messages, $attributes);

		if ($validator->fails()) {
			return back()->withErrors($validator)->withInput(); // Form calling with
		}

		if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password, 'user_type' => 'Driver'])) {
			return redirect()->intended('driver_profile');
		}
		return back()->withErrors(['password' => 'Invalid credentials'])->withInput();
	}

	public function signin_driver()
	{
		return view('user.signin_driver');
	}

	public function signin_company()
	{
		if (Auth::guard('company')->user() != null) {
			return redirect('company/dashboard');
		}
		return view('user.signin_company');
	}

	public function signin_rider()
	{
		return view('user.signin_rider');
	}

	public function forgot_password()
	{
		return view('user.forgot_password');
	}

	public function forgotpassword(Request $request, EmailController $email_controller)
	{
		if ($request->user_type == 'Company') {
			$rules = array(
				'email' => 'required|email|exists:companies,email',
			);
		}
		else{
			$rules = array(
				'email' => 'required|email|exists:users,email,user_type,' . $request->user_type,
			);
		}

		// Email validation custom messages
		$messages = array(
			'required'                => ':attribute '.trans('messages.home.field_is_required').'',
			'exists' => trans('messages.user.email_exists'),
		);

		// Email validation custom Fields name
		$attributes = array(
			'email' => trans('messages.user.email'),
		);

		$validator = Validator::make($request->all(), $rules, $messages, $attributes);

		if ($validator->fails()) {
			return back()->withErrors($validator)->withInput()->with('error_code', 4);
		}

		if ($request->user_type == 'Company') {
			$company = Company::whereEmail($request->email)->first();
			$email_controller->company_forgot_password_link($company);
			flashMessage('success', trans('messages.user.link') . $company->email);
			return redirect('signin_company');
		}else{
			$user = User::whereEmail($request->email)->first();
			$email_controller->forgot_password_link($user);
			flashMessage('success', trans('messages.user.link') . $user->email);
			if ($user->user_type == 'Rider') {
				return redirect('signin_rider');
			}
			else {
				return redirect('signin_driver');
			}
		}
	}

	public function signup_rider()
	{
		$fb_user_data = Session::get('fb_user_data');
		$data = array();
		if ($fb_user_data) {
			$data['user'] = $fb_user_data;
		}

		return view('user.signup_rider', $data);
	}

	public function doCurl($url)
	{
	  $ch = curl_init();
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	  $data = json_decode(curl_exec($ch), true);
	  curl_close($ch);
	  return $data;
	}

	public function signup_driver(Request $request)
	{
		$data = array();

		if($request->code) {
			  
			$token_exchange_url = 'https://graph.accountkit.com/'.ACCOUNTKIT_VERSION.'/access_token?'.
			'grant_type=authorization_code'.
			'&code='.$request->code.
			"&access_token=AA|".ACCOUNTKIT_APP_ID."|".ACCOUNTKIT_APP_SECRET;
			$data = $this->doCurl($token_exchange_url);
			
			if(isset($data['error'])) {
				return view('user.signup_driver');
			}

			$user_id = $data['id'];
			$user_access_token = $data['access_token'];
			$refresh_interval = $data['token_refresh_interval_sec'];

			// Get Account Kit information
			$me_endpoint_url = 'https://graph.accountkit.com/'.ACCOUNTKIT_VERSION.'/me?'.
			'access_token='.$user_access_token;
			$data = $this->doCurl($me_endpoint_url);

			$country_code = $data['phone']['country_prefix'];
			$mobile_number = $data['phone']['national_number'];
			$type ='Driver';

			$user_count = User::where('mobile_number', $mobile_number)->where('user_type', $type)->count();

			if($user_count) {
                flashMessage('success', trans('messages.mobile_number_exist'));
                return redirect('signup_driver');
			}

			$data['country_code'] = $country_code;
			$data['phone_number'] = $mobile_number;
			$data['kit_id'] 	  = $user_id;
		}

		if (@$request->step == 'car_details') {
			if ($request->user_id == Session::get('id')) {
				return view('user.driver_car_details',$data);
			}
			else {
				return view('user.signup_driver',$data);
			}
		}
		return view('user.signup_driver',$data);
	}

	public function signup_company(Request $request)
	{
		if (Auth::guard('company')->user() != null) {
			return redirect('company/dashboard');
		}
		return view('user.signup_company');
	}

	public function company_register(Request $request)
	{
		if ($request->request_type == 'submit') {
			$request->merge(['mobile_number' => session('signup_mobile')]);
			$request->merge(['country_code' => session('signup_country_code')]);
		}
		
		$rules = array(
			'name' => 'required',
			'email' => 'required|email',
			'mobile_number' => 'required|numeric|regex:/[0-9]{6}/',
			'password' => 'required|min:6',
			'country_code' => 'required',
		);

		$messages = array(
			'required'                => ':attribute '.trans('messages.home.field_is_required').'',

			'mobile_number.regex' => trans('messages.user.mobile_no'),
		);

		$attributes = array(
			'name' => trans('messages.profile.name'),
			'email' => trans('messages.user.email'),
			'password' => trans('messages.user.paswrd'),
			'country_code' => trans('messages.user.country_code'),
			'mobile_number' => trans('messages.user.mobile'),
		);

		$validator = Validator::make($request->all(), $rules, $messages,$attributes);

		$validator->after(function ($validator) use($request) {
			$company = Company::where('mobile_number', $request->mobile_number)->count();

			$company_email = Company::where('email', $request->email)->count();

			if ($company) {
				$validator->errors()->add('mobile_number',trans('messages.user.mobile_no_exists'));
			}

			if ($company_email) {
				$validator->errors()->add('email',trans('messages.user.email_exists'));
			}
		});

		if ($request->request_type == 'send_otp') {  //send OTP
			if (count($validator->errors())) {
				return json_encode(['status_code' => 0,'messages' => $validator->errors()]);
			}
			$otp_responce = $this->otp_helper->sendOtp($request->mobile_number,$request->country_code);
			if ($otp_responce['status_code'] == 0) {
				$data = [
					'status_code' => 0,
					'messages' => ['mobile_number' => [$otp_responce['message']]],
				];
				return json_encode($data);
			}

			return json_encode(['status_code' => 1,'messages' => 'success']);
		}elseif($request->request_type == 'resend_otp'){ //resend OTP
			$otp_responce = $this->otp_helper->resendOtp();
			return json_encode($otp_responce);
		}elseif($request->request_type == 'check_otp'){ //OTP submit
			$check_otp_responce = $this->otp_helper->checkOtp($request->otp);
			return json_encode($check_otp_responce);
		}else if ($validator->fails()) {
			return back()->withErrors($validator)->withInput();
		}else {
			
			$company = new Company;
			$company->name = $request->name;
			$company->email = $request->email;
			$company->country_code = $request->country_code;
			$company->mobile_number = $request->mobile_number;
			$company->password = $request->password;
			$company->save();

			if (Auth::guard('company')->attempt(['email' => $request->email, 'password' => $request->password])) {

				flashMessage('success', trans('messages.user.register_successfully'));
				return redirect('company/edit_company/'.$company->id);

			} else {
				return redirect('signin_company');
			}
		}
	}

	public function rider_register(Request $request)
	{
		if ($request->request_type == 'submit') {
			$request->merge(['mobile_number' => session('signup_mobile')]);
			$request->merge(['country_code' => session('signup_country_code')]);
		}

		$rules = array(
			'first_name' 	=> 'required',
			'last_name' 	=> 'required',
			'email' 		=> 'required|email',
			'mobile_number' => 'required|numeric|regex:/[0-9]{6}/',
			'password'		=> 'required|min:6',
			'country_code' 	=> 'required',
			'user_type' 	=> 'required',
			'referral_code' => 'nullable|exists:users,referral_code',
		);

		$messages = array(
			'required'              => ':attribute '.trans('messages.home.field_is_required').'',
			'mobile_number.regex' 	=> trans('messages.user.mobile_no'),
			'referral_code.exists' 	=> trans('messages.referrals.enter_valid_referral_code'),
		);

		$attributes = array(
			'first_name' 	=> trans('messages.user.firstname'),
			'last_name' 	=> trans('messages.user.lastname'),
			'email' 		=> trans('messages.user.email'),
			'password' 		=> trans('messages.user.paswrd'),
			'country_code'	=> trans('messages.user.country_code'),
			'user_type' 	=> trans('messages.user.user_type'),
			'mobile_number' => trans('messages.user.mobile'),
			'referral_code' => trans('messages.referrals.referral_code'),
		);

		$validator = Validator::make($request->all(), $rules, $messages, $attributes);
			
		$validator->after(function ($validator) use($request) {
			$user = User::where('mobile_number', $request->mobile_number)->where('user_type', $request->user_type)->count();

			$user_email = User::where('email', $request->email)->where('user_type', $request->user_type)->count();

			if ($user) {
				$validator->errors()->add('mobile_number',trans('messages.user.mobile_no_exists'));
			}

            //$referral_check = User::whereUserType(ucfirst($request->user_type))->where('referral_code',$request->referral_code)->count();
            $referral_check = User::get()->where('referral_code',$request->referral_code)->count();
	        if($request->referral_code != '' && $referral_check == 0)  {
	        	$validator->errors()->add('referral_code',__('messages.referrals.enter_valid_referral_code'));
	        }

			if ($user_email) {
				$validator->errors()->add('email',trans('messages.user.email_exists'));
			}
		});

		if ($request->request_type == 'send_otp') {  //send OTP
			if (count($validator->errors())) {
				return json_encode(['status_code' => 0,'messages' => $validator->errors()]);
			}
			$otp_responce = $this->otp_helper->sendOtp($request->mobile_number,$request->country_code);
			if ($otp_responce['status_code'] == 0) {
				$data = [
					'status_code' => 0,
					'messages' => ['mobile_number' => [$otp_responce['message']]],
				];
				return json_encode($data);
			}

			return json_encode(['status_code' => 1,'messages' => 'success']);
		}elseif($request->request_type == 'resend_otp'){ //resend OTP
			$otp_responce = $this->otp_helper->resendOtp();
			return json_encode($otp_responce);
		}elseif($request->request_type == 'check_otp'){ //OTP submit
			$check_otp_responce = $this->otp_helper->checkOtp($request->otp);
			return json_encode($check_otp_responce);
		}else if ($validator->fails()) {
			return back()->withErrors($validator)->withInput(); // Form calling with
		} else {
			$user = new User;
			$user->first_name = $request->first_name;
			$user->last_name = $request->last_name;
			$user->email = $request->email;
			$user->country_code = $request->country_code;
			$user->mobile_number = $request->mobile_number;
			$user->password = $request->password;
			$user->user_type = $request->user_type;
            $user->used_referral_code = $request->referral_code;
            
            //if referral_code is provided in signup form
			if (trim($request->referral_code) != "") {
				// $users = User::where('id', $user_id)->where('user_type', 'Rider')->first();
				$users = User::where('referral_code', $request->referral_code)->first();
				
				$to = '+'.$users->country_code.$users->mobile_number;
				$text = "Rideon: New User Registered Using you Referral code. Please view details by login to Dashboard";
				$this->otp_helper->sendPhoneSMS($to , $text);
			}


			if ($request->fb_id != null && $request->fb_id != "") {
				$user->fb_id = $request->fb_id;
			}

			$user->save();

			$user_pic = new ProfilePicture;

			$user_pic->user_id = $user->id;
			if ($request->fb_id != null && $request->fb_id != "") {
				$user_pic->src = "https://graph.facebook.com/" . $request->fb_id . "/picture?type=large";
				$user_pic->photo_source = 'Facebook';
				Session::forget('fb_user_data');
			} else {
				$user_pic->src = "";
				$user_pic->photo_source = 'Local';
			}

			$user_pic->save();

			$location = new RiderLocation;

			$location->user_id = $user->id;
			$location->home = '';
			$location->work = '';
			$location->home_latitude = '';
			$location->home_longitude = '';
			$location->work_latitude = '';
			$location->work_longitude = '';

			$location->save();

			if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password, 'user_type' => 'Rider'])) {

				flashMessage('success', trans('messages.user.register_successfully'));
				return redirect()->intended('trip'); // Redirect to dashboard page

			} else {
				// flashMessage('danger', trans('messages.login_failed'));
				return redirect('signin_rider'); // Redirect to login page
			}
		}

	}

	public function driver_register(Request $request)
	{
		if ($request->step == 'basics') {
			if ($request->request_type == 'submit') {
				$request->merge(['mobile_number' => session('signup_mobile')]);
				$request->merge(['country_code' => session('signup_country_code')]);
			}

			$rules = array(
				'first_name' => 'required',
				'last_name' => 'required',
				'email' => 'required|email',
				'mobile_number' => 'required|numeric|regex:/[0-9]{6}/',
				'password' => 'required|min:6',
				'home_address' => 'required',
				'user_type' => 'required',
				'referral_code'   => 'nullable|exists:users,referral_code',
			);

			// Add Driver Validation Custom Names
			$niceNames = array(
				'first_name' => trans('messages.user.firstname'),
				'last_name' => trans('messages.user.lastname'),
				'email' => trans('messages.user.email'),
				'password' => trans('messages.user.paswrd'),
				'home_address' => trans('messages.account.city'),
				'user_type' => trans('messages.user.user_type'),
				'mobile_number' => trans('messages.user.mobile'),
				'referral_code'   => trans('messages.referrals.referral_code'),
			);
			// Edit Rider Validation Custom Fields message
			$messages = array(
				'required'                => ':attribute '.trans('messages.home.field_is_required').'',
				'mobile_number.regex' => trans('messages.user.mobile_no'),
				'referral_code.exists' 	=> trans('messages.referrals.enter_valid_referral_code'),
			);
			$validator = Validator::make($request->all(), $rules, $messages);
			
			$validator->after(function ($validator) use($request) {
				$user = User::where('mobile_number', $request->mobile_number)->where('user_type', $request->user_type)->count();

				$user_email = User::where('email', $request->email)->where('user_type', $request->user_type)->count();

				if ($user) {
					$validator->errors()->add('mobile_number',trans('messages.user.mobile_no_exists'));
				}

				if ($user_email) {
					$validator->errors()->add('email',trans('messages.user.email_exists'));
				}
			});
			$validator->setAttributeNames($niceNames);

			if ($request->request_type == 'send_otp') {  //send OTP
				if (count($validator->errors())) {
					return json_encode(['status_code' => 0,'messages' => $validator->errors()]);
				}
				$otp_responce = $this->otp_helper->sendOtp($request->mobile_number,$request->country_code);
				if ($otp_responce['status_code'] == 0) {
					$data = [
						'status_code' => 0,
						'messages' => ['mobile_number' => [$otp_responce['message']]],
					];
					return json_encode($data);
				}

				return json_encode(['status_code' => 1,'messages' => 'success']);
			}elseif($request->request_type == 'resend_otp'){ //resend OTP
				$otp_responce = $this->otp_helper->resendOtp();
				return json_encode($otp_responce);
			}elseif($request->request_type == 'check_otp'){ //OTP submit
				$check_otp_responce = $this->otp_helper->checkOtp($request->otp);
				return json_encode($check_otp_responce);
			}else if ($validator->fails()) {
				return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
			} else {
				$user = new User;

				$user->first_name = $request->first_name;
				$user->last_name = $request->last_name;
				$user->email = $request->email;
				$user->country_code = $request->country_code;
				$user->mobile_number = $request->mobile_number;
				$user->password = $request->password;
				$user->user_type = $request->user_type;
				$user->company_id = 1;
                $user->used_referral_code = $request->referral_code;
                
                //if referral_code is provided in signup form
				if (trim($request->referral_code) != "") {
					// $users = User::where('id', $user_id)->where('user_type', 'Rider')->first();
					$users = User::where('referral_code', $request->referral_code)->first();
					
					$to = '+'.$users->country_code.$users->mobile_number;
					$text = "Rideon: New User Registered Using you Referral code. Please view details by login to Dashboard";
                    $this->otp_helper->sendPhoneSMS($to , $text);
				}

				// exit;

				//here first need check if ref code is present in database.
				//if referral code is present in database get user and send message to phone.
				//also send push notification and email also with same content.


				$user->status = 'Car_details';

				$user->save();

				$user_pic = new ProfilePicture;

				$user_pic->user_id = $user->id;
				$user_pic->src = "";
				$user_pic->photo_source = 'Local';

				$user_pic->save();

				$user_address = new DriverAddress;

				$user_address->user_id = $user->id;
				$user_address->address_line1 = $request->address_line1 ? $request->address_line1 : '';
				$user_address->address_line2 = $request->address_line2 ? $request->address_line2 : '';
				$user_address->city = $request->city ? $request->city : '';
				$user_address->state = $request->state ? $request->state : '';
				$user_address->postal_code = $request->postal_code ? $request->postal_code : '';

				$user_address->save();
				//store info for login
				Session::put('id', $user->id);
				Session::put('password', $request->password);

				return redirect('signup_driver?step=car_details&user_id=' . $user->id);
			}

		} else if ($request->step == 'car_details') {
			$rules = array(
				'vehicle_name' => 'required',
				'vehicle_number' => 'required',
				'vehicle_type' => 'required',
			);

			// Add Driver Validation Custom Names
			$niceNames = array(
				'vehicle_name' => trans('messages.user.veh_name'),
				'vehicle_number' => trans('messages.user.veh_no'),
				'vehicle_type' => trans('messages.user.veh_type'),
			);
			// Edit Rider Validation Custom Fields message

			$messages = array(
				'required'=> ':attribute '.trans('messages.home.field_is_required').'',
			);
			$validator = Validator::make($request->all(), $rules, $messages);

			$validator = Validator::make($request->all(), $rules);

			$validator->setAttributeNames($niceNames);

			if ($validator->fails()) {
				return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
			} else {

				$user = User::find(Session::get('id'));
				$user->status = 'Document_details';
				$user->save();
				if ($user) {
					$vehicle = Vehicle::where('user_id', $user->id)->first();
					if ($vehicle == null) {
						$vehicle = new Vehicle;
						$vehicle->user_id = $user->id;
						$vehicle->company_id = $user->company_id;
					}
					$vehicle->vehicle_name = $request->vehicle_name;
					$vehicle->vehicle_number = $request->vehicle_number;
					$vehicle->vehicle_id = $request->vehicle_type;
					$vehicle->vehicle_type = CarType::find($request->vehicle_type)->car_name;
					$vehicle->status = 'Inactive';
					$vehicle->save();

					$driver_doc = DriverDocuments::where('user_id', $user->id)->first();
					if ($driver_doc == null) {
						$driver_doc = new DriverDocuments;
						$driver_doc->user_id = $user->id;
						$driver_doc->document_count = 0;
						$driver_doc->save();
					}

					if (Auth::guard('web')->attempt(['email' => $user->email, 'password' => Session::get('password'), 'user_type' => 'Driver'])) {

						flashMessage('success', trans('messages.user.register_successfully'));
						return redirect()->intended('driver_profile'); // Redirect to dashboard page

					} else {
						return redirect('signup_driver');
					}
					// return redirect('signup_driver?step=document_upload&token='.$request->_token);
				} else {
					return redirect('signup_driver');

				}
			}
		} elseif ($request->step == 'document_upload') {
			// dd('dsf');
		} else {
			return redirect('signup_driver');
		}

	}

	// Rider Facebook login
	public function facebookAuthenticate(Request $request, EmailController $email_controller) {
		if ($request->error_code == 200) {
			// flashMessage('danger', $request->error_description);
			return redirect('signin_rider'); // Redirect to login page
		}

		$this->fb->generateSessionFromRedirect(); // Generate Access Token Session After Redirect from Facebook

		$response = $this->fb->getData(); // Get Facebook Response

		$userNode = $response->getGraphUser(); // Get Authenticated User Data

		// $email = ($userNode->getProperty('email') == '') ? $userNode->getId().'@fb.com' : $userNode->getProperty('email');
		$email = $userNode->getProperty('email');
		$fb_id = $userNode->getId();

		$user = User::user_facebook_authenticate($email, $fb_id); // Check Facebook User Email Id is exists

		if ($user->count() > 0) // If there update Facebook Id
		{
			$user = User::user_facebook_authenticate($email, $fb_id)->first();

			$user->fb_id = $userNode->getId();

			$user->save(); // Update a Facebook id

			$user_id = $user->id; // Get Last Updated Id
		} else // If not create a new user without Password
		{
			$user = User::user_facebook_authenticate($email, $fb_id);

			if ($user->count() > 0) {
				/*$data['title'] = 'Disabled ';
                return view('users.disabled', $data);*/
				return redirect('user_disabled');
			}

			$user = new User;

			// New user data
			$user->first_name = $userNode->getFirstName();
			$user->last_name = $userNode->getLastName();
			$user->email = $email;
			$user->fb_id = $userNode->getId();

			if ($email == '') {
				$user = array(
					'first_name' => $userNode->getFirstName(),
					'last_name' => $userNode->getLastName(),
					'email' => $email,
					'fb_id' => $userNode->getId(),
				);
				Session::put('fb_user_data', $user);
				return redirect('signup_rider');
			}
			$user->status = 'Active'; //user activated
			$user->user_type = 'Rider';
			$user->save(); // Create a new user

			$user_id = $user->id; // Get Last Insert Id

			$user_pic = new ProfilePicture;

			$user_pic->user_id = $user_id;
			$user_pic->src = "https://graph.facebook.com/" . $userNode->getId() . "/picture?type=large";
			$user_pic->photo_source = 'Facebook';

			$user_pic->save(); // Save Facebook profile picture

			// $email_controller->welcome_email_confirmation($user);

		}

		$users = User::where('id', $user_id)->where('user_type', 'Rider')->first();

		if (@$users->status != 'Inactive') {
			if (Auth::loginUsingId($user_id)) // Login without using User Id instead of Email and Password
			{

				return redirect()->intended('trip'); // Redirect to dashboard page
			} else {
				flashMessage('danger', trans('messages.user.login_failed'));
				return redirect('signin_rider'); // Redirect to login page
			}
		} else // Call Disabled view file for Inactive user
		{
			/*$data['title'] = 'Disabled ';
            return view('users.disabled', $data);*/
			return redirect('user_disabled');
		}
	}

	/**
     * Google User Registration and Login
     *
     * @return redirect to dashboard page
     */
    public function googleAuthenticate(Request $request)
    {
    	try {
            $client = new Google_Client(['client_id' => GOOGLE_CLIENT_ID]);  
            // Specify the CLIENT_ID of the app that accesses the backend
            $payload = $client->verifyIdToken($request->idtoken);

	        if ($payload) {
	            $google_id = $payload['sub'];
	        } 
	        else {
	            flashMessage('danger', trans('messages.user.login_failed'));
	            return redirect('signin_rider');
	        }
        }
        catch(\Exception $e) {
            flashMessage('danger', $e->getMessage());
            return redirect('signin_rider');
        }

        // Get Details From Google
        $firstName 	= $payload['given_name'];
        $lastName 	= isset($payload['family_name']) ? $payload['family_name'] : '';
        $email = ($payload['email'] == '') ? $google_id.'@gmail.com' : $payload['email'];
        $prev_count = User::user_google_authenticate($email, $google_id)->count();

        if($prev_count > 0 ) {
        	$user = User::user_google_authenticate($email, $google_id)->first();
			$user->google_id = $google_id;
			$user->save();
			$user_id = $user->id;
        }
        else {
        	flashMessage('danger', trans('messages.user.google_login_failed'));
			return redirect('signin_rider');
		}

		$user = User::where('id', $user_id)->where('user_type', 'Rider')->first();

		if ($user->status != 'Inactive') {
			if(Auth::loginUsingId($user_id)) {
				return redirect()->intended('trip');
			}
			else {
				flashMessage('danger', trans('messages.user.login_failed'));
				return redirect('signin_rider');
			}
		}
		else {
			return redirect('user_disabled');
		}
    }

	// User Disabled Page
	public function user_disabled()
	{
		$data['title'] = 'Disabled ';
		return view('user.disabled', $data);
	}

	/**
     * Add a Payout Method and Load Payout Preferences File
     *
     * @param array $request Input values
     * @return redirect to Payout Preferences page and load payout preferences view file
     */
    public function payoutPreferences(Request $request)
    {
        $data['country']   = Country::all()->pluck('long_name','short_name');
        $data['currency']   = Currency::all()->pluck('name','name');
        $data['country_list'] = Country::getPayoutCoutries();
        $data['iban_supported_countries'] = Country::getIbanRequiredCountries();
        $data['country_currency'] = $this->helper->getStripeCurrency();
        $data['mandatory']         = PayoutPreference::getAllMandatory();
        $data['branch_code_required'] = Country::getBranchCodeRequiredCountries();

        $payout_methods = getPayoutMethods(Auth::user()->company_id);

        $payout_credentials = PayoutCredentials::with('payout_preference')->where('user_id', Auth::id())->get();

        $payout_data = array();
        foreach ($payout_methods as $method) {
            $payout_credential = $payout_credentials->where('type',snakeToCamel($method,true))->first();
            $preference = optional($payout_credential)->payout_preference;
            $payout = array(
                'address1'      => $preference->address1 ?? '',
                'address2'      => $preference->address2 ?? '',
                'city'          => $preference->city ?? '',
                'state'         => $preference->state ?? '',
                'country'       => $preference->country ?? '',
                'postal_code'   => $preference->postal_code ?? '',
                'paypal_email'  => $preference->paypal_email ?? '',
                'currency_code' => $preference->currency_code ?? '',
                'routing_number'=> $preference->routing_number ?? '',
                'account_number'=> $preference->account_number ?? '',
                'holder_name'   => $preference->holder_name ?? '',
                'bank_name'     => $preference->bank_name ?? '',
                'branch_name'   => $preference->branch_name ?? '',
                'branch_code'   => $preference->branch_code ?? '',
                'bank_location' => $preference->address1 ?? '',
            );
            $payout_method = array(
                'id' => optional($payout_credential)->id ?? 0,
                'key' => $method,
                'is_default' => optional($payout_credential)->default == 'yes',
                'value' => snakeToCamel($method),
                'preference_id' => optional($payout_credential)->payout_id,
                "icon"          => asset("images/icon/".$method.".png"),
                'payout_data' => $payout,
            );
            $payout_data[] = $payout_method;
        }

        $data['payouts'] = collect($payout_data);

        return view('driver_dashboard.payout_preferences', $data);
    }

    /**
     * Add payout Preferences
     *
     * @param  Post method inputs
     * @return Response in Json
     */
    public function updatePayoutPreference(Request $request)
    {
        $user_details = Auth::user();
        $payout_methods = getPayoutMethods(Auth::user()->company_id);
        $payout_methods = implode(',',$payout_methods);

        $rules = array(
            'payout_method' => 'required|in:'.$payout_methods,
        );

        if ($request->payout_method == 'stripe') {
            $rules['country'] = 'required|exists:country,short_name';
        }

        $messages   = array('required'=> ':attribute '.trans('messages.home.field_is_required').'',);
        $validator = Validator::make($request->all(), $rules,$messages);
        
        if($validator->fails()) {
        	flashMessage('danger', $validator->messages()->first());
            return back();
        }

        $user_id = $user_details->id;
        $country = $request->country;
        $payout_default_count = PayoutCredentials::where('user_id', $user_id)->where('default', '=', 'yes');
        $account_holder_type = 'company';
        $payout_method = snakeToCamel($request->payout_method,true);
        $payout_service = resolve('App\Services\Payouts\\'.$payout_method.'Payout');

        if ($payout_method == 'Stripe') {
            $account_holder_type = 'individual';

            $request['payout_country'] = $country;
            $iban_supported_country = Country::getIbanRequiredCountries();

            $bank_data = array(
                "country"               => $country,
                "currency"              => $request->currency,
                "account_holder_name"   => $request->account_holder_name,
                "account_holder_type"   => $account_holder_type,
            );

            if (in_array($country, $iban_supported_country)) {
                $request['account_number'] = $request->account_number;
                $bank_data['account_number'] = $request->account_number;
            }
            else {
                if ($country == 'AU') {
                    $request['routing_number'] = $request->bsb;
                }
                elseif ($country == 'HK') {
                    $request['routing_number'] = $request->clearing_code . '-' . $request->branch_code;
                }
                elseif ($country == 'JP' || $country == 'SG') {
                    $request['routing_number'] = $request->bank_code . $request->branch_code;
                }
                elseif ($country == 'GB') {
                    $request['routing_number'] = $request->sort_code;
                }
                $bank_data['routing_number'] = $request['routing_number'];
                $bank_data['account_number'] = $request->account_number;
            }
        }
        else if($payout_method == 'BankTransfer') {
        	$request['account_number'] = $request->bank_account_number;
        }

        $validate_data = $payout_service->validateRequest($request);

        if($validate_data) {
            return $validate_data;
        }

        if($request->hasFile('document')) {
            $image = $request->file('document');

            $image_uploader = resolve('App\Contracts\ImageHandlerInterface');

            $target_dir = '/images/driver/'.$user_id.'/uploads';
            $extension = $image->getClientOriginalExtension();
            $file_name = "payout_document_".time().".".$extension;
            $options = compact('target_dir','file_name');

            $upload_result = $image_uploader->upload($image,$options);
            if(!$upload_result['status']) {
                flashMessage('danger', $upload_result['status_message']);
                return back();
            }
            $filename = $upload_result['file_name'];
            $document_path = public_path($target_dir.'/'.$filename);
        }

        if($request->hasFile('additional_document')) {
            $image = $request->file('additional_document');

            $image_uploader = resolve('App\Contracts\ImageHandlerInterface');

            $target_dir = '/images/driver/'.$user_id.'/uploads';
            $extension = $image->getClientOriginalExtension();
            $file_name = "payout_additional_document_".time().".".$extension;
            $options = compact('target_dir','file_name');

            $upload_result = $image_uploader->upload($image,$options);
            if(!$upload_result['status']) {
                flashMessage('danger', $upload_result['status_message']);
                return back();
            }
            $add_filename = $upload_result['file_name'];
            $add_document_path = public_path($target_dir.'/'.$add_filename);
        }

        if ($payout_method == 'Stripe') {

            $stripe_preference = $payout_service->createPayoutPreference($request);

            if(!$stripe_preference['status']) {
            	flashMessage('danger', $stripe_preference['status_message']);
            	return back();
            }

            $recipient = $stripe_preference['recipient'];
            if(isset($document_path)) {
                $document_result = $payout_service->uploadDocument($document_path,$recipient->id);
                if(!$document_result['status']) {
                    flashMessage('danger', $document_result['status_message']);
            		return back();
                }
                $stripe_document = $document_result['stripe_document'];

                $payout_service->attachDocumentToRecipient($recipient->id,$recipient->individual->id,$stripe_document,'document');
            }

            if(isset($add_document_path)) {
                $add_document_result = $payout_service->uploadDocument($add_document_path,$recipient->id);
                if(!$add_document_result['status']) {
                    flashMessage('danger', $add_document_result['status_message']);
            		return back();
                }
                $add_stripe_document = $add_document_result['stripe_document'];
                $payout_service->attachDocumentToRecipient($recipient->id,$recipient->individual->id,$add_stripe_document,'additional_document');
            }

            $payout_email = isset($recipient->id) ? $recipient->id : $user->email;
            $payout_currency = $request->currency ?? '';
        }

        if ($payout_method == 'Paypal') {
            $payout_email = $request->email;
            $payout_currency = PAYPAL_CURRENCY_CODE;
        }

        if ($payout_method == 'BankTransfer') {
            $payout_email       = $request->account_number;
            $payout_currency    = "";
            $request['branch_code']= $request->bank_code;
        }

        $payout_preference = PayoutPreference::firstOrNew(['user_id' => $user_id,'payout_method' => $payout_method]);
        $payout_preference->user_id         = $user_id;
        $payout_preference->country         = $country;
        $payout_preference->currency_code   = $payout_currency;
        $payout_preference->routing_number  = $request->routing_number ?? '';
        $payout_preference->account_number  = $request->account_number ?? '';
        $payout_preference->holder_name     = $request->account_holder_name ?? '';
        $payout_preference->holder_type     = $account_holder_type;
        $payout_preference->paypal_email    = $payout_email;
        $payout_preference->address1    = $request->address1 ?? '';
        $payout_preference->address2    = $request->address2 ?? '';
        $payout_preference->city        = $request->city;
        $payout_preference->state       = $request->state;
        $payout_preference->postal_code = $request->postal_code;
        if (isset($document_path)) {
            $payout_preference->document_id     = $stripe_document ?? '';
            $payout_preference->document_image  = $filename;
        }
        if (isset($add_document_path)) {
            $payout_preference->additional_document_id     = $add_stripe_document ?? '';
            $payout_preference->additional_document_image  = $add_filename;
        }
        $payout_preference->phone_number    = $request->phone_number ?? '';
        $payout_preference->branch_code     = $request->branch_code ?? '';
        $payout_preference->bank_name       = $request->bank_name ?? '';
        $payout_preference->bank_location       = $request->bank_location ?? '';
        $payout_preference->branch_name     = $request->branch_name ?? '';
        $payout_preference->ssn_last_4      = $country == 'US' ? $request->ssn_last_4 : '';
        $payout_preference->payout_method   = $payout_method;
        $payout_preference->address_kanji   = isset($address_kanji) ? json_encode($address_kanji) : json_encode([]);
        $payout_preference->save();

        $payout_credentials = PayoutCredentials::firstOrNew(['user_id' => $user_id,'type' => $payout_method]);
        $payout_credentials->user_id = $user_id;
        $payout_credentials->preference_id = $payout_preference->id;
        $payout_credentials->payout_id = $payout_email;
        $payout_credentials->type = $payout_method;
        if($payout_credentials->default != 'yes') {
            $payout_credentials->default = $payout_default_count->count() == 0 ? 'yes' : 'no';
        }
        $payout_credentials->save();

        flashMessage('success', __('messages.account.payout_updated'));
       	return redirect()->route('driver_payout_preference');
    }

    /**
     * Delete Payouts Default Payout Method
     *
     * @param array $request Input values
     * @return redirect to Payout Preferences page
     */
    public function payoutDelete(Request $request, EmailController $email_controller)
    {
        $payout = PayoutCredentials::find($request->id);
        if ($payout=='') {
            return redirect()->route('driver_payout_preference');
        }
        
        if($payout->default == 'yes') {
            flashMessage('danger', __('messages.account.payout_default'));
            return redirect()->route('driver_payout_preference');
        }
        
    	PayoutPreference::find($payout->preference_id)->delete();
        $payout->delete();

        $email_controller->payout_preferences($payout->id, 'delete');

        flashMessage('success', __('messages.account.payout_deleted'));
        return redirect()->route('driver_payout_preference');
    }

    /**
     * Update Payouts Default Payout Method
     *
     * @param array $request Input values
     * @return redirect to Payout Preferences page
     */
    public function payoutDefault(Request $request, EmailController $email_controller)
    {
        $payout = PayoutCredentials::find($request->id);

        if($payout->default == 'yes') {
            flashMessage('danger', __('messages.account.payout_already_defaulted'));
            return redirect()->route('driver_payout_preference');
        }
        
        PayoutCredentials::where('user_id',Auth::user()->id)->update(['default'=>'no']);
        $payout->default = 'yes';
        $payout->save();

        $email_controller->payout_preferences($payout->id, 'default_update');

        flashMessage('success', __('messages.account.payout_defaulted'));
        return redirect()->route('driver_payout_preference');
    }

	/**
	 * Set Password View and Update Password
	 *
	 * @param array $request Input values
	 * @return view set_password / redirect to Login
	 */
	public function reset_password(Request $request)
	{
		if (!$_POST) {

			$password_resets = PasswordResets::whereToken($request->secret)->first();
			$user = User::where('email', @$password_resets->email)->first();
			if ($password_resets) {
				$password_result = $password_resets;

				$datetime1 = new DateTime();
				$datetime2 = new DateTime($password_result->created_at);
				$interval = $datetime1->diff($datetime2);
				$hours = $interval->format('%h');

				if ($hours >= 1) {
					// Delete used token from password_resets table
					PasswordResets::whereToken($request->secret)->delete();

					flashMessage('error', trans('messages.user.token'));
					if ($user->user_type == 'Rider') {
						return redirect('signin_rider');
					} else {
						return redirect('signin_driver');
					}

				}

				$data['result'] = User::whereEmail($password_result->email)->first();
				$data['token'] = $request->secret;
				return view('user.reset_password', $data);
			} else {
				flashMessage('error', trans('messages.user.invalid_token'));
				if (@$user->user_type == 'Rider') {
					return redirect('signin_rider');
				} else {
					return redirect('signin_driver');
				}

			}
		} else {
			// Password validation rules
			$rules = array(
				'new_password' => 'required|min:6|max:30',
				'confirm_password' => 'required|same:new_password',
			);

			// Password validation custom Fields name
			$niceNames = array(
				'new_password' => trans('messages.user.new_paswrd'),
				'confirm_password' => trans('messages.user.cnfrm_paswrd'),
			);

			$validator = Validator::make($request->all(), $rules);
			$validator->setAttributeNames($niceNames);

			if ($validator->fails()) {
				return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
			} else {

				// Delete used token from password_resets table
				$password_resets = PasswordResets::whereToken($request->token)->delete();

				$user = User::find($request->id);

				$user->password = $request->new_password;

				$user->save(); // Update Password in users table

				flashMessage('success', trans('messages.user.pswrd_chnge'));
				if ($user->user_type == 'Rider') {
					return redirect('signin_rider');
				} else {
					return redirect('signin_driver');
				}

			}
		}
	}

	/**
	 * Set Password View and Update Password for company
	 *
	 * @param array $request Input values
	 * @return view set_password / redirect to Login
	 */
	public function company_reset_password(Request $request)
	{
		if (!$_POST) {

			$password_resets = PasswordResets::whereToken($request->secret)->first();
			$company = Company::where('email', @$password_resets->email)->first();
			if ($password_resets) {
				$password_result = $password_resets;

				$datetime1 = new DateTime();
				$datetime2 = new DateTime($password_result->created_at);
				$interval = $datetime1->diff($datetime2);
				$hours = $interval->format('%h');

				if ($hours >= 1) {
					// Delete used token from password_resets table
					PasswordResets::whereToken($request->secret)->delete();

					flashMessage('error', trans('messages.user.token'));
					return redirect('signin_company');

				}

				$data['result'] = Company::whereEmail($password_result->email)->first();
				$data['token'] = $request->secret;
				return view('user.reset_password', $data);
			} else {
				flashMessage('error', trans('messages.user.invalid_token'));
				return redirect('signin_company');

			}
		} else {
			// Password validation rules
			$rules = array(
				'new_password' => 'required|min:6|max:30',
				'confirm_password' => 'required|same:new_password',
			);

			// Password validation custom Fields name
			$niceNames = array(
				'new_password' => trans('messages.user.new_paswrd'),
				'confirm_password' => trans('messages.user.cnfrm_paswrd'),
			);

			$validator = Validator::make($request->all(), $rules);
			$validator->setAttributeNames($niceNames);

			if ($validator->fails()) {
				return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
			} else {

				// Delete used token from password_resets table
				$password_resets = PasswordResets::whereToken($request->token)->delete();

				$company = company::find($request->id);

				$company->password = $request->new_password;

				$company->save(); // Update Password in users table

				flashMessage('success', trans('messages.user.pswrd_chnge'));
				return redirect('signin_company');

			}
		}
	}

	/**
     * User Apple Logint
     *  
     * @param array $request Input values
     *
     * @return redirect
     */
    public function apple_callback(Request $request) 
    {
        $client_id = api_credentials('service_id','Apple');

        $client_secret = getAppleClientSecret();

        $params = array(
            'grant_type'    => 'authorization_code',
            'code'          => $request->code,
            'redirect_uri'  => url('apple_callback'),
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
        );
        $curl_result = curlPost("https://appleid.apple.com/auth/token",$params);
        

        if(!isset($curl_result['id_token'])) {
            flashMessage('danger', trans('messages.user.google_login_failed'));
            return redirect()->route('rider.signin');
        }

        $claims = explode('.', $curl_result['id_token'])[1];
        $user_data = json_decode(base64_decode($claims));
        $user = User::where('apple_id', $user_data->sub)->first();

        if($user == '') {
            flashMessage('danger', __('messages.user.google_login_failed'));
            return redirect()->route('rider.signin');
        }

        if ($user->status != 'Inactive') {
            if(Auth::loginUsingId($user->id,true)) {
                return redirect()->intended('trip');
            }

            flashMessage('danger', __('messages.user.google_login_failed'));
            return redirect()->route('rider.signin');
        }

        return redirect('user_disabled');
    }

    
}