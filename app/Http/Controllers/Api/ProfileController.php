<?php

/**
 * Profile Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Profile
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use App\Http\Start\Helpers;
use App\Models\Currency;
use App\Models\DriverAddress;
use App\Models\DriverDocuments;
use App\Models\ProfilePicture;
use App\Models\RiderLocation;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Vehicle;
use App\Models\DriverOweAmount;
use App\Models\DriverLocation;
use App\Models\PaymentMethod;
use App\Models\ReferralUser;
use Auth;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use Image;

class ProfileController extends Controller
{
	/**
	 * User Profile photo upload
	 * @param  Post method request inputs
	 *
	 * @return Response Json
	 */
	public function upload_profile_image(Request $request)
	{
		$rules = array(
            'image' => 'required|mimes:jpg,jpeg,png,gif',
        );

        $validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
                'status_code'     => '0',
                'status_message' => $validator->messages()->first(),
            ]);
		}

        $user_details = JWTAuth::parseToken()->authenticate();

        if(!$request->hasFile('image')) {
			return response()->json([
				'status_code' 		=> "0",
				'status_message' 	=> "Invalid File",
			]);
		}
        
        $user_profile_image = ProfilePicture::find($user_details->id);
        if(!$user_profile_image)
        {
            $user_profile_image = new ProfilePicture;
            $user_profile_image->user_id = $user_details->id;
        }

        $image_uploader = resolve('App\Contracts\ImageHandlerInterface');
        $target_dir = '/images/users/'.$user_details->id;
        $target_path = asset($target_dir).'/';

        $user_profile_image->photo_source = 'Local';
        $profile_image = $request->file('image');

        $extension = $profile_image->getClientOriginalExtension();
        $file_name = "profile_image".time().".".$extension;
        $compress_size = array(
			["height" => 225, "width" => 225],
		);
        $options = compact('target_dir','file_name','compress_size');
        
        $upload_result = $image_uploader->upload($profile_image,$options);

        if(!$upload_result['status']) {
            return response()->json([
				'status_code' 		=> "0",
				'status_message' 	=> $upload_result['status_message'],
			]);
        }

        $user_picture->src = $target_path.$upload_result['file_name'];
        $user_picture->user_id =$user_details->id;
        $user_picture->save();

		return response()->json([
			'status_code' 		=> "1",
			'status_message' 	=> "Profile Image Upload Successfully",
			'image_url' 		=> asset($target_dir.'/'.$upload_result['file_name']),
		]);
	}

	/**
	 * Driver Docuemnt upload
	 * @param  Post method request inputs
	 *
	 * @return Response Json
	 */
	public function document_upload(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$user_id = $user_details->id;

		$rules = array(
			'document_type' => 'required|in:license_front,license_back,insurance,rc,permit',
			'image' 		=> 'required|mimes:jpg,jpeg,png,gif',
		);

		$messages = [
			'document_type.required' 	=> ':attribute ' . trans('messages.field_is_required') . '',
			'image.required' 			=> ':attribute ' . trans('messages.field_is_required') . '',
		];

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			return response()->json([
                'status_code'     => '0',
                'status_message' => $validator->messages()->first(),
            ]);
		}
		$image_uploader = resolve('App\Contracts\ImageHandlerInterface');
		$target_dir = '/images/users/'.$user_details->id;

		$document_type = $request->document_type;

		if ($request->hasFile('image')) {
			$image = $request->file('image');

			$extension = $image->getClientOriginalExtension();
			$file_name = $document_type."_".time().".".$extension;
			
	        $options = compact('target_dir','file_name');

	        $upload_result = $image_uploader->upload($image,$options);
	        if(!$upload_result['status']) {
	            return response()->json([
					'status_code' 		=> "0",
					'status_message' 	=> $upload_result['status_message'],
				]);
	        }

	        $filename = asset($target_dir.'/'.$upload_result['file_name']);

        	if ($document_type == 'insurance' || $document_type == 'rc' || $document_type == 'permit') {
				$count = @Vehicle::where('user_id', $user_id)->get();
				if ($count->count()) {
					$document_count = @$count[0]['document_count'] != '' ? $count[0]['document_count'] : '0';
					$document = @$count[0][$document_type] != '' ? $count[0][$document_type] : '';
				}
				else {
					$document_count = '0';
					$document = '';
				}

				if ($document_count < 3 && $document == '') {
					$vehicle_document_count = $document_count + 1;
				}
				else {
					$vehicle_document_count = $document_count;
				}

				if ($vehicle_document_count >= 3) {
					$vehicle_document_count = 3;
				}

				$driver_document_count = @DriverDocuments::where('user_id',$user_id)->first()->document_count;

				$data = [
					'user_id' 		=> $user_id,
					'company_id' 	=> $user_details->company_id,
					$document_type 	=> $filename,
					'document_count'=> @$vehicle_document_count,
				];

				if ($driver_document_count == '') {
                    DriverDocuments::updateOrCreate(['user_id' => $user_id],['document_count'=>0]);
                }
				Vehicle::updateOrCreate(['user_id' => $user_id], $data);
			}
			else {
				$count = @DriverDocuments::where('user_id', $user_id)->get();

				if($count->count()) {
					$document_count = @$count[0]['document_count'] != '' ? $count[0]['document_count'] : '0';
					$document = @$count[0][$document_type] != '' ? $count[0][$document_type] : '';
				}
				else {
					$document_count = '0';
					$document = '';
				}

				if($document_count < 2 && $document == '') {
					$driver_document_count = $document_count + 1;
				}
				else {
					$driver_document_count = $document_count;
				}

				if($driver_document_count >= 2) {
					$driver_document_count = 2;
				}

				$vehicle_document_count = @Vehicle::where('user_id',$user_id)->first()->document_count;

				//return file based on image size.
				$data = [
					'user_id' 		=> $user_id,
					$document_type 	=> $filename,
					'document_count'=> @$driver_document_count,
				];

				DriverDocuments::updateOrCreate(['user_id' => $user_id], $data);
			}

			if ($driver_document_count == 2 && $vehicle_document_count==3) {
				$status = isLiveEnv() ? "Active" : "Pending";
				User::where('id', $user_id)->update(['status' => $status]);
			}

			return response()->json([
				'status_code' 			=> "1",
				'status_message' 		=> "Upload Successfully",
				'document_url' 			=> $filename,
				'driver_document_count' => $driver_document_count + $vehicle_document_count,
			]);
		}
	}

	/**
	 * Display the vehicle details
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function vehicle_details(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();
		$user_id = $user_details->id;

		$rules = array(
			'vehicle_id' => 'required',
			'vehicle_name' => 'required',
			'vehicle_type' => 'required',
			'vehicle_number' => 'required',
		);

		$messages = [
			'vehicle_id.required' => ':attribute ' . trans('messages.field_is_required') . '',
			'vehicle_name.required' => ':attribute ' . trans('messages.field_is_required') . '',
			'vehicle_type.required' => ':attribute ' . trans('messages.field_is_required') . '',
			'vehicle_number.required' => ':attribute ' . trans('messages.field_is_required') . '',
		];

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			return response()->json([
                'status_code'     => '0',
                'status_message' => $validator->messages()->first(),
            ]);
		}
		$data = [
			'user_id' => $user_id,
			'vehicle_id' => $request->vehicle_id,
			'vehicle_name' => urldecode($request->vehicle_name),
			'vehicle_type' => $request->vehicle_type,
			'vehicle_number' => urldecode($request->vehicle_number),
		];

		$driver_doc = DriverDocuments::where('user_id',$user_id)->first();
		if($driver_doc == '') {
            DriverDocuments::updateOrCreate(['user_id' => $user_id],['document_count'=>0]);
        }
		Vehicle::updateOrCreate(['user_id' => $user_id], $data);

		User::where('id', $user_details->id)->update(['status' => 'Document_details']);

		return response()->json([
			'status_code' => "1",
			'status_message' => trans('messages.update_success'),
		]);
	}

	/**
	 * Display the Rider profile details & get the trip information while app closed
	 * 
	 * @param  Get method request inputs
	 * @return Response Json
	 */
	public function get_rider_profile(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$invoice_helper = resolve('App\Http\Helper\InvoiceHelper');

		$user_data = collect($user_details)->only(['first_name','last_name','mobile_number','country_code']);
		$user_details->load('rider_location','profile_picture');
		
		$location_data = collect($user_details->rider_location)->only('home','work','home_latitude','home_longitude','work_latitude','work_longitude');

		$user_data['email_id'] 		= $user_details->email;
		$user_data['profile_image'] = $user_details->profile_picture->src ?? url('images/user.jpeg');
		$user_data['currency_code'] = $user_details->currency->code;
		$user_data['currency_symbol'] = html_entity_decode($user_details->currency->original_symbol);
		$user_data = $user_data->merge($location_data);

		$wallet_amount = getUserWalletAmount($user_details->id);
		$promo_details = $invoice_helper->getUserPromoDetails($user_details->id);

		$user_data['wallet_amount'] = $wallet_amount;
		$user_data['promo_details'] = $promo_details;

		return response()->json(array_merge([
			'status_code' 		=> '1',
			'status_message' 	=> trans('messages.success'),
		],$user_data->toArray()));
	}

	/**
	 * Update the location of Rider
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function update_rider_location(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		if ($request->home) {
			$rules = array(
				'home' => 'required',
				'latitude' => 'required',
				'longitude' => 'required',
			);
			$location_type = 'home';
		}
		else {
			$rules = array(
				'work' => 'required',
				'latitude' => 'required',
				'longitude' => 'required',
			);
			$location_type = 'work';
		}

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
                'status_code'     => '0',
                'status_message' => $validator->messages()->first(),
            ]);
		}

		$user_check = User::where('id', $user_details->id)->first();

		if($user_check == '') {
			return response()->json([
				'status_code' 	 => '0',
				'status_message' => __('messages.invalid_credentials'),
			]);
		}

		if ($location_type == 'work') {
			$data = [
				'user_id' => $user_details->id,
				'work' => $request->work,
				'work_latitude' => $request->latitude,
				'work_longitude' => $request->longitude,
			];
		}
		else {
			$data = [
				'user_id' => $user_details->id,
				'home' => $request->home,
				'home_latitude' => $request->latitude,
				'home_longitude' => $request->longitude,
			];
		}

		RiderLocation::updateOrCreate(['user_id' => $user_details->id], $data);

		return response()->json([
			'status_code' => '1',
			'status_message' => trans('messages.update_success'),
		]);
	}

	/**
	 * Update Rider Profile
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function update_rider_profile(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'profile_image' => 'required',
			'first_name' => 'required',
			'last_name' => 'required',
			'country_code' => 'required',
			'mobile_number' => 'required',
			'email_id' => 'required',
		);

		$messages = [
			'first_name.required' => ':attribute ' . trans('messages.field_is_required') . '',
			'last_name.required' => ':attribute ' . trans('messages.field_is_required') . '',
			'mobile_number.required' => ':attribute ' . trans('messages.field_is_required') . '',
			'country_code.required' => ':attribute ' . trans('messages.field_is_required') . '',
			'email_id.required' => ':attribute ' . trans('messages.field_is_required') . '',
			'profile_image.required' => ':attribute ' . trans('messages.field_is_required') . '',
		];

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			return response()->json([
                'status_code'     => '0',
                'status_message' => $validator->messages()->first(),
            ]);
		}

		User::where('id', $user_details->id)->update(['first_name' => $request->first_name, 'last_name' => $request->last_name, 'mobile_number' => $request->mobile_number, 'email' => $request->email_id, 'country_code' => $request->country_code]);
		ProfilePicture::where('user_id', $user_details->id)->update(['src' => html_entity_decode($request->profile_image)]);

		$user = User::where('id', $user_details->id)->first();

		return response()->json([
			'status_code' => '1',
			'status_message' => trans('messages.update_success'),
			'first_name' => $user->first_name,
			'last_name' => $user->last_name,
			'mobile_number' => $user->mobile_number,
			'country_code' => $user->country_code,
			'email_id' => $user->email,
			'profile_image' => $user->profile_picture->src,
			'home' => @$user->rider_location->home ?? '',
			'work' => @$user->rider_location->work ??'',
		]);
	}

	/**
	 * Display Driver  Profile
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function get_driver_profile(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$user = User::where('id', $user_details->id)->first();

		if ($user == '') {
			return response()->json([
				'status_code' => '0',
				'status_message' => trans('messages.invalid_credentials'),
			]);
		}
		$symbol = @Currency::where('code', $user->currency_code)->first()->symbol;

		$driver_owe = DriverOweAmount::where('user_id',$user_details->id)->first();
		$owe_amount = number_format($driver_owe->amount,2,'.','');

		$driver_referral_earning = ReferralUser::where('user_id',$user_details->id)->where('payment_status','Completed')->where('pending_amount','>',0)->get();
		$driver_referral_earning = number_format(@$driver_referral_earning->sum('pending_amount'),2,'.','');

		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> 'Success',
			'first_name' 		=> $user->first_name,
			'last_name' 		=> $user->last_name,
			'mobile_number' 	=> $user->mobile_number,
			'country_code' 		=> $user->country_code,
			'email_id' 			=> $user->email,
			'car_type' 			=> $user->car_type,
			'profile_image' 	=> @$user->profile_picture->src ?? '',
			'address_line1' 	=> @$user->driver_address->address_line1 ?? '',
			'address_line2' 	=> @$user->driver_address->address_line2 ?? '',
			'city' 				=> @$user->driver_address->city ?? '',
			'state'				=> @$user->driver_address->state ?? '',
			'postal_code' 		=> @$user->driver_address->postal_code ?? '',
			'car_id' 			=> @$user->driver_documents->vehicle_id ?: '1',
			'vehicle_name' 		=> @$user->driver_documents->vehicle_name ?? '',
			'vehicle_number' 	=> @$user->driver_documents->vehicle_number ?? '',
			'license_front' 	=> @$user->driver_documents->license_front ?? '',
			'license_back' 		=> @$user->driver_documents->license_back ?? '',
			'insurance' 		=> @$user->driver_documents->insurance ?? '',
			'rc' 				=> @$user->driver_documents->rc ?? '',
			'permit' 			=> @$user->driver_documents->permit ?? '',
			'currency_code' 	=> @$user->currency->code,
			'currency_symbol' 	=> html_entity_decode(@$user->currency->original_symbol),
			'car_image' 		=> @$user->driver_documents->car_type->vehicle_image,
			'car_active_image' 	=> @$user->driver_documents->car_type->active_image,
			'company_id' 		=> $user->company_id,
			'company_name' 		=> @$user->company->name,
			'owe_amount' 		=> $owe_amount,
			'driver_referral_earning' => $driver_referral_earning,
		]);
	}

	/**
	 * Update Driver  Profile
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function update_driver_profile(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'first_name' => 'required',
			'last_name' => 'required',
			'mobile_number' => 'required',
			'country_code' => 'required',
			'email_id' => 'required',
			'profile_image' => 'required',
			'address_line1' => 'required',
			'address_line2' => 'required',
			'city' => 'required',
			'state' => 'required',
			'postal_code' => 'required',
		);

		$messages = [
			'first_name.required' => trans('messages.first_name_required'),
			'last_name.required' => trans('messages.last_name_required'),
			'mobile_number.required' => trans('messages.mobile_num_required'),
			'country_code.required' => trans('messages.country_code_required'),
			'email_id.required' => trans('messages.email_id_required'),
			'profile_image.required' => trans('messages.profile_image_required'),
			'address_line1.required' => trans('messages.address_line1_required'),
			'address_line2.required' => trans('messages.address_line2_required'),
			'city.required' => trans('messages.city_required'),
			'state.required' => trans('messages.state_required'),
			'postal_code.required' => trans('messages.postal_code_required'),
		];

		$validator = Validator::make($request->all(), $rules, $messages);

		if ($validator->fails()) {
			return response()->json([
                'status_code'     => '0',
                'status_message' => $validator->messages()->first(),
            ]);
		}

		User::where('id', $user_details->id)->update([
			'first_name' 	=> $request->first_name,
			'last_name' 	=> $request->last_name,
			'mobile_number' => $request->mobile_number,
			'country_code' 	=> $request->country_code,
			'email' 		=> $request->email_id,
		]);

		DriverAddress::where('user_id', $user_details->id)->update([
			'address_line1' => $request->address_line1,
			'address_line2' => $request->address_line2,
			'city' 			=> $request->city,
			'state' 		=> $request->state,
			'postal_code' 	=> $request->postal_code,
		]);

		ProfilePicture::where('user_id', $user_details->id)->update([
			'src' => $request->profile_image,
		]);

		$user = User::where('id', $user_details->id)->first();

		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> trans('messages.update_success'),
			'first_name' 		=> $user->first_name,
			'last_name'			=> $user->last_name,
			'mobile_number' 	=> $user->mobile_number,
			'country_code' 		=> $user->country_code,
			'email_id' 			=> $user->email,
			'profile_image' 	=> $user->profile_picture->src,
			'address_line1' 	=> $user->driver_address->address_line1,
			'car_type' 			=> $user->car_type,
			'address_line2' 	=> $user->driver_address->address_line2,
			'city' 				=> $user->driver_address->city,
			'state' 			=> $user->driver_address->state,
			'postal_code' 		=> $user->driver_address->postal_code,
			'vehicle_name' 		=> $user->driver_documents->vehicle_name,
			'vehicle_number' 	=> $user->driver_documents->vehicle_number,
			'license_front' 	=> $user->driver_documents->license_front,
			'license_back' 		=> $user->driver_documents->license_back,
			'insurance' 		=> $user->driver_documents->insurance,
			'rc' 				=> $user->driver_documents->rc,
			'permit' 			=> $user->driver_documents->permit,
		]);
	}

	/**
	 * To update the currency code for the user
	 * @param  Request $request Get values
	 * @return Response Json
	 */
	public function update_user_currency(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'currency_code' => 'required|exists:currency,code',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
                'status_code'     => '0',
                'status_message' => $validator->messages()->first(),
            ]);
		}

		User::where('id', $user_details->id)->update(['currency_code' => $request->currency_code]);

		$wallet_amount = getUserWalletAmount($user_details->id);

		return response()->json([
			'status_message' => trans('messages.update_success'),
			'status_code' => '1',
			'wallet_amount' => $wallet_amount,
		]);
	}

	public function get_caller_detail(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'user_id' => 'required|exists:users,id',
		);

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()) {
            return [
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ];
        }

        $user = User::find($request->user_id);

		if($request->send_push_notification) {
			$request_helper = resolve('App\Http\Helper\RequestHelper');

			$push_title = $user->first_name." Calling";
			$push_data['push_title'] = $push_title;
	        $push_data['data'] = array(
	            'user_calling' => array(
	                'user_id' => $user->id,
	                'title' => $push_title,
	            )
	        );

			if ($user->device_type != null && $user->device_type != '') {
				$request_helper->checkAndSendMessage($user,'',$push_data);
			}
		}

		return response()->json([
			'status_code' 	=> '1',
			'status_message'=> __('messages.api.listed_successfully'),
			'first_name' 	=> $user->first_name,
			'last_name' 	=> $user->last_name,
			'profile_image' => optional($user->profile_picture)->src ?? url('images/user.jpeg'),
		]);
	}

	/**
	 * API for create a customer id  based on card details using stripe payment gateway
	 *
	 * @return Response Json response with status
	 */
	public function add_card_details(Request $request)
	{
		$rules = array(
            'intent_id'			=> 'required',
        );

        $attributes = array(
            'intent_id'     	=> 'Setup Intent Id',
        );

        $validator = Validator::make($request->all(), $rules,$attributes);

        if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first(),
            ]);
        }

		$user_details = JWTAuth::parseToken()->authenticate();
		$stripe_payment = resolve('App\Repositories\StripePayment');

		$payment_details = PaymentMethod::firstOrNew(['user_id' => $user_details->id]);

		$setup_intent = $stripe_payment->getSetupIntent($request->intent_id);

		if($setup_intent->status != 'succeeded') {
			return response()->json([
				'status_code' => '0',
				'intent_status' => $setup_intent->status,
				'status_message' => $setup_intent->status_message ?? '',
			]);
		}

		if($payment_details->payment_method_id != '') {
			$stripe_payment->detachPaymentToCustomer($payment_details->payment_method_id);
		}

		$stripe_payment->attachPaymentToCustomer($payment_details->customer_id,$setup_intent->payment_method);

		$payment_method = $stripe_payment->getPaymentMethod($setup_intent->payment_method);
		$payment_details->intent_id = $setup_intent->id;
		$payment_details->payment_method_id = $setup_intent->payment_method;
		$payment_details->brand = $payment_method['card']['brand'];
		$payment_details->last4 = $payment_method['card']['last4'];
		$payment_details->save();

		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> 'Added Successfully',
			'brand' 			=> $payment_details->brand,
			'last4' 			=> strval($payment_details->last4),
		]);
	}

	/**
	 * API for payment card details
	 *
	 * @return Response Json response with status
	 */
	public function get_card_details(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();
		$stripe_payment = resolve('App\Repositories\StripePayment');

		$payment_details = PaymentMethod::firstOrNew(['user_id' => $user_details->id]);

		if(!isset($payment_details->customer_id)) {
			$stripe_customer = $stripe_payment->createCustomer($user_details->email);
			if($stripe_customer->status == 'failed') {
				return response()->json([
					'status_code' 		=> "0",
					'status_message' 	=> $stripe_customer->status_message,
				]);
			}
			$payment_details->customer_id = $stripe_customer->customer_id;
			$payment_details->save();
		}
		$customer_id = $payment_details->customer_id;

		// Check New Customer if customer not exists
		$customer_details = $stripe_payment->getCustomer($customer_id);
		if($customer_details->status == "failed" && $customer_details->status_message == "resource_missing") {
			$stripe_customer = $stripe_payment->createCustomer($user_details->email);
			if($stripe_customer->status == 'failed') {
				return response()->json([
					'status_code' 		=> "0",
					'status_message' 	=> $stripe_customer->status_message,
				]);
			}
			$payment_details->customer_id = $stripe_customer->customer_id;
			$payment_details->save();
			$customer_id = $payment_details->customer_id;
		}

		$status_code = "1";
		if($payment_details->intent_id == '') {
			$status_code = "2";
		}

		$setup_intent = $stripe_payment->createSetupIntent($customer_id);
		if($setup_intent->status == 'failed') {
			return response()->json([
				'status_code' 		=> "0",
				'status_message' 	=> $setup_intent->status_message,
			]);
		}

		return response()->json([
			'status_code' 		=> $status_code,
			'status_message' 	=> 'Listed Successfully',
			'intent_client_secret'=> $setup_intent->intent_client_secret,
			'brand' 			=> $payment_details->brand ?? '',
			'last4' 			=> (string)$payment_details->last4 ?? '',
		]);
	}
}