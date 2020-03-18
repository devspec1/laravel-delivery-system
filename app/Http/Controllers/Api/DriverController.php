<?php

/**
 * Driver Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Driver
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use App\Http\Helper\RequestHelper;
use App\Http\Start\Helpers;
use App\Models\DriverLocation;
use App\Models\Payment;
use App\Models\DriverOweAmountPayment;
use App\Models\DriverOweAmount;
use App\Models\Rating;
use App\Models\Request as RideRequest;
use App\Models\ScheduleRide;
use App\Models\PaymentMethod;
use App\Models\Trips;
use App\Models\User;
use App\Models\UsersPromoCode;
use App\Models\Country;
use App\Models\BankDetail;
use App\Models\AppliedReferrals;
use App\Models\ReferralUser;
use App\Models\Fees;
use Auth;
use DB;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;
use File;
use App\Http\Helper\InvoiceHelper;

class DriverController extends Controller
{
	protected $request_helper; // Global variable for Helpers instance

	public function __construct(RequestHelper $request,InvoiceHelper $invoice_helper)
	{
		$this->request_helper = $request;
		$this->helper = new Helpers;
		$this->invoice_helper = $invoice_helper;
	}

	/**
	 * Update Location of Driver & calculate the trip distance while trip
	 *
	 * @param Get method request inputs
	 * @return @return Response in Json
	 */

	public function updateLocation(Request $request)
	{
	 	$user_details = JWTAuth::parseToken()->authenticate();

	 	$rules = array(
			'latitude' 	=> 'required',
			'longitude' => 'required',
			'user_type' => 'required|in:Driver,driver',
			'car_id' 	=> 'required|exists:car_type,id',
			'status' 	=> 'required|in:Online,Offline,online,offline,Trip,trip',
		);

		if ($request->trip_id) {
			$rules['trip_id'] = 'required|exists:trips,id';
			$rules['total_km'] = 'required';
		}

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()) {
            return response()->json([
            	'status_code' => '0',
            	'status_message' => $validator->messages()->first()
            ]);
        }

		$user = User::where('id', $user_details->id)->where('user_type', $request->user_type)->first();

		if ($user == '') {
			return response()->json([
				'status_code'	 => '0',
				'status_message' => __('messages.invalid_credentials'),
			]);
		}
		$driver_location = DriverLocation::where('user_id', $user_details->id)->first();

		if ($request->trip_id) {

			$old_km = Trips::where('id', $request->trip_id)->first()->total_km;
			$user_id = Trips::where('id', $request->trip_id)->first()->user_id;

			$user_rider = User::where('id', $user_id)->get()->first();

			$device_type = $user_rider->device_type;

			$device_id = $user_rider->device_id;
			$user_type = $user_rider->user_type;
			$push_title = "Live Tracking";
			$data = array('live_tracking' => array('trip_id' => $request->trip_id, 'driver_latitude' => @$request->latitude, 'driver_longitude' => @$request->longitude));

			if ($user->device_type == 3) {
				$old_latitude = $driver_location->latitude;
				$old_longitude = $driver_location->longitude;

				$earthRadius = 6371000;
				$latFrom = deg2rad($old_latitude);
				$lonFrom = deg2rad($old_longitude);
				$latTo = deg2rad($request->latitude);
				$lonTo = deg2rad($request->longitude);

				$latDelta = $latTo - $latFrom;
				$lonDelta = $lonTo - $lonFrom;

				$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

				$meter = number_format((($angle * $earthRadius)), 2);

				$km = (($meter) / 1000);

			}
			else {
				$km = $request->total_km;
			}

			$new_km = $old_km + $km;
			 
			/* json file */
			$trip_id = $request->trip_id;		

			$file = $trip_id. '_file.json';
			$destinationPath=public_path()."/trip_file/";

			if(!is_dir($destinationPath)) { 
			  mkdir($destinationPath,0777,true);  
			}

			$old_path = base_path('public/trip_file/'.$trip_id.'_file.json');

			if(file_exists($old_path)) {
				$jsonString = file_get_contents($old_path);
				$datas = json_decode($jsonString, true);
			}

			$datas[] = array(
				'latitude' => $request->latitude,
				'longitude'=>$request->longitude,
				'current_km' =>  $km,
				'old_km'=>$old_km,
				'new_km'=> (string)$new_km,
				'time' => date('H:i:s')
			);

			$data= json_encode($datas ,JSON_PRETTY_PRINT);
			File::put($destinationPath.$file,$data);
			/* json file */

			Trips::where('id', $request->trip_id)->update(['total_km' => $new_km]);

			$data = [
				'user_id' => $user_details->id,
				'latitude' => $request->latitude,
				'longitude' => $request->longitude,
			];

			DriverLocation::updateOrCreate(['user_id' => $user_details->id], $data);

			$test = Trips::where('id', $request->trip_id)->first();
			$new_meter = $request->total_km + $test->meter;
			$new = $test->test . ',' . $request->latitude . '-' . $request->longitude . '--' . $request->total_km . '--' . $test->total_km . ')';
			$new_count = $test->count + 1;

			return response()->json([
				'status_code' => '1',
				'status_message' => "updated successfully",
			]);
		}

		if ($driver_location != '' && $driver_location->status == 'Trip') {
			return response()->json([
				'status_code' => '0',
				'status_message' => trans('messages.please_complete_your_current_trip'),
			]);
		}

		$data = [
			'user_id' => $user_details->id,
			'car_id' => $request->car_id,
			'latitude' => $request->latitude,
			'longitude' => $request->longitude,
		];

		if ($request->status == "Online" || $request->status == "Offline") {
			$data['status'] = $request->status;
		}

		DriverLocation::updateOrCreate(['user_id' => $user_details->id], $data);

		return response()->json([
			'status_code' => '1',
			'status_message' => "updated successfully",
		]);
	}

	/**
	 * Check the Document status from driver side
	 *
	 * @param Get method request inputs
	 * @return @return Response in Json
	 */
	public function checkStatus(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'user_type' => 'required|in:Driver,driver,Rider,rider',
		);

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()) {
            return [
            	'status_code' => '0',
            	'status_message' => $validator->messages()->first()
            ];
        }

		$user = User::where('id', $user_details->id)->where('user_type', $request->user_type)->first();

		if ($user == '') {
			return response()->json([
				'status_code' 		=> '0',
				'status_message'	=> trans('messages.api.invalid_credentials'),
			]);
		}

		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> trans('messages.success'),
			'driver_status' 	=> @$user->status != '' ? $user->status : '',
		]);
	}

	public function cash_collected(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();
		$rules = array(
			'trip_id' => 'required|exists:trips,id',
		);

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()) {
            return response()->json([
            	'status_code' => '0',
            	'status_message' => $validator->messages()->first()
            ]);
        }
        
		$trip = Trips::where('id', $request->trip_id)->first();
		if ($trip->status != 'Payment') {
			return response()->json([
				'status_code' => '0',
				'status_message' => __('messages.api.something_went_wrong'),
			]);
		}
		elseif ($trip->is_calculation == 0) {
			$data = [
				'trip_id' => $request->trip_id,
				'user_id' => $user_details->id,
				'save_to_trip_table' => 1,
			];
			$this->invoice_helper->calculation($data);
	 		$trip = Trips::where('id', $request->trip_id)->first();
		}

		$trip_save = Trips::where('id', $request->trip_id)->first();
		$trip_save->status = 'Completed';
		$trip_save->paykey = @$request->paykey;
		$trip_save->payment_status = 'Completed';
		$trip_save->save();

		$data = [
			'trip_id' => $request->trip_id,
			'correlation_id' => @$request->paykey,
			'driver_payout_status' => ($trip->driver_payout) ? 'Pending' : 'Completed',
		];

		Payment::updateOrCreate(['trip_id' => $request->trip_id], $data);
		$rider = User::where('id', $trip->user_id)->first();
		$driver_thumb_image = @$trip->driver_thumb_image != '' ? $trip->driver_thumb_image : url('images/user.jpeg');

		$push_data['push_title'] = __('messages.dashboard.cash_collect');
		$push_data['data'] = array(
			'trip_payment' => array(
				'status' 	=> __('messages.dashboard.cash_collect'),
				'trip_id' 	=> $request->trip_id,
				'driver_thumb_image' => $driver_thumb_image
			)
		);
        $this->request_helper->SendPushNotification($rider,$push_data);

		$schedule_ride = ScheduleRide::find($trip->ride_request->schedule_id);
		if (isset($schedule_ride) && $schedule_ride->booking_type == 'Manual Booking') {

			$push_title = __('messages.trip_cash_collected');
	        $text 		= __('messages.api.trip_total_fare',['total_fare'=> $trip->total_fare,'currency' => $trip->currency_code]);

	        $push_data['push_title'] = $push_title;
	        $push_data['data'] = array(
	            'custom_message' => array(
	                'title' => $push_title,
	                'message_data' => $text,
	            )
	        );

	        $text = $push_title.$text;

	        $this->request_helper->checkAndSendMessage($rider,$text,$push_data);
		}

		$invoice_helper = resolve('App\Http\Helper\InvoiceHelper');
        $promo_details = $invoice_helper->getUserPromoDetails($trip->user_id);

		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> "Cash Collected Successfully",
			'trip_id' 			=> $trip->id,
			'promo_details' 	=> $promo_details,
			'rider_thumb_image' => $trip->rider_thumb_image,
		]);
	}

	/**
	 * Display Country List
	 *
	 * @param Get method request inputs
	 * @return @return Response in Json
	 */
	public function country_list(Request $request)
	{
		$data = Country::select(
			'id as country_id',
			'long_name as country_name',
			'short_name as country_code'
		)->get();

		return response()->json([
			'status_code' => '1',
			'status_message' => 'Country Listed Successfully',
			'country_list' => $data,
		]);
	}

    /**
	 * Driver Bank Details if company is private
	 *
	 * @param Get method request inputs
	 * @return @return Response in Json
	 */
	public function driver_bank_details(Request $request)
	{
		$user = JWTAuth::toUser($request->token);

		if(!$request) {
			$bank_detail = BankDetail::where('user_id',$user->id)->first();
			if(isset($bank_detail)) {
				$bank_detail = (object)[];
			}
		}
		else {
			$rules = array(
    			'account_holder_name' => 'required',
                'account_number' => 'required',
                'bank_name' => 'required',
                'bank_location' => 'required',
                'bank_code' => 'required',
            );

            $attributes = array(
                'account_holder_name'  => trans('messages.account.holder_name'),
                'account_number'  => trans('messages.account.account_number'),
                'bank_name'  => trans('messages.account.bank_name'),
                'bank_location'  => trans('messages.account.bank_location'),
                'bank_code'  => trans('messages.account.bank_code'),
            );

    		$messages   = array('required'=> ':attribute '.trans('messages.home.field_is_required').'',);
            $validator = Validator::make($request->all(), $rules,$messages,$attributes);
            
            if($validator->fails()) {
	            return response()->json([
	            	'status_code' => '0',
	            	'status_message' => $validator->messages()->first()
	            ]);
	        }

    		$bank_detail = BankDetail::firstOrNew(['user_id' => $user->id]);

            $bank_detail->user_id = $user->id;
            $bank_detail->holder_name = $request->account_holder_name;
            $bank_detail->account_number = $request->account_number;
            $bank_detail->bank_name = $request->bank_name;
            $bank_detail->bank_location = $request->bank_location;
            $bank_detail->code = $request->bank_code;
            $bank_detail->save();
		}
                
		return response()->json([
			'status_code' => '1',
			'status_message' => 'Listed Successfully',
			'bank_detail' =>  $bank_detail,
		]);
    }

	public function pay_to_admin(Request $request)
	{
		$user 	= JWTAuth::toUser($request->token);

		//validation started
		$rules = array(
			'applied_referral_amount' => 'In:0,1',
            'amount'	=> 'numeric|min:0',
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            return response()->json([
            	'status_code' => '0',
            	'status_message' => $validator->messages()->first()
            ]);
        }

		$owe_amount = DriverOweAmount::where('user_id', $user->id)->first();
		if ($owe_amount && $owe_amount->amount > 0) {
			//applying referral amount start
			if ($request->has('applied_referral_amount') && $request->applied_referral_amount==1) {

				$total_referral_amount = ReferralUser::where('user_id',$user->id)
					->where('payment_status','Completed')
					->where('pending_amount','>',0)
					->get()
					->sum('pending_amount');

				if ($owe_amount->amount < $total_referral_amount) {
					$total_referral_amount = $owe_amount->amount;
				}

				if ($total_referral_amount > 0) {
					$applied_referrals = new AppliedReferrals;
					$applied_referrals->amount = $total_referral_amount;
					$applied_referrals->user_id = $user->id;
					$applied_referrals->currency_code = $user->currency->code;
					$applied_referrals->save();

					$this->invoice_helper->referralUpdate($user->id,$total_referral_amount,$user->currency->code);

					//owe amount
					$owe_amount = DriverOweAmount::where('user_id', $user->id)->first();
					$currency_code = $owe_amount->currency_code;
					$owe_amount->amount = $owe_amount->amount - $total_referral_amount;
					$owe_amount->currency_code = $currency_code;
					$owe_amount->save();

					$payment = new DriverOweAmountPayment;
					$payment->user_id = $user->id;
					$payment->transaction_id = "";
					$payment->amount = $total_referral_amount;
					$payment->currency_code = $currency_code;
					$payment->status = 1;
					$payment->save();

					$owe_amount = DriverOweAmount::where('user_id', $user->id)->first();
				}
			}
			//applying referral amount

			//pay to admin from payout preference start
			$owe_amount = DriverOweAmount::where('user_id', $user->id)->first();
			if ($owe_amount->amount < $request->amount) {
				$request->amount = $owe_amount->amount;
			}
			$amount = $request->amount;

			if($request->has('amount') && $request->amount > 0) {
				if($owe_amount->amount < $request->amount) {
					return response()->json([
						'status_code' => '0',
						'status_message' => trans('messages.api.invalid'),
					]);
				}

				$rules = array(
					'payment_type' 	=> 'required|in:paypal,stripe,braintree',
				);

				if($request->payment_type != "stripe") {
		            $rules['pay_key'] = 'required';
		        }

				$validator = Validator::make($request->all(), $rules);

				if($validator->fails()) {
		            return response()->json([
		                'status_code' => '0',
		                'status_message' => $validator->messages()->first()
		            ]);
		        }

				$owe_amount = DriverOweAmount::where('user_id', $user->id)->first();
				$total_owe_amount = $owe_amount->amount;
				$currency_code = $owe_amount->currency_code;
				$remaining_amount = $total_owe_amount - $amount;

				$payment_data['currency_code'] = $user->currency_code;
				$payment_data['amount'] = $amount;
				$payment_data['user_id'] = $user->id;

				if($request->payment_type == 'stripe') {
					$payment_method = PaymentMethod::where('user_id', $user->id)->first();
					if($payment_method == '') {
						return response()->json([
			                'status_code' => '0',
			                'status_message' => __('messages.api.please_add_card_details'),
			            ]);
					}

					$payment_data = array(
						"amount" 		=> $amount * 100,
						'currency' 		=> $user->currency_code,
						'description' 	=> 'Owe Payment By '.$user->first_name,
						"customer" 		=> $payment_method->customer_id,
						'payment_method'=> $payment_method->payment_method_id,
				      	'confirm' 		=> true,
				      	'off_session' 	=> true,
					);
				}

				$payment_func = $request->payment_type."Payment";

				$payment_helper = resolve('App\Http\Helper\PaymentHelper');
				$pay_result = $payment_helper->$payment_func($payment_data,$request->pay_key);

				if(!$pay_result->status) {
					return response()->json([
		                'status_code' => '0',
		                'status_message' => $pay_result->status_message,
		            ]);
				}

				if($pay_result->is_two_step) {
					return response()->json([
		                'status_code' => '2',
		                'status_message' => $pay_result->status_message,
		                'two_step_id' => $pay_result->two_step_id,
		            ]);
				}

				//owe amount
				$owe_amount->amount = $remaining_amount;
				$owe_amount->currency_code = $currency_code;
				$owe_amount->save();

				$payment = new DriverOweAmountPayment;
				$payment->user_id = $user->id;
				$payment->transaction_id = $pay_result->transaction_id;
				$payment->amount = $amount;
				$payment->status = 1;
				$payment->currency_code = $currency_code;
				$payment->save();

				$owe_amount = DriverOweAmount::where('user_id', $user->id)->first();
			}

			$referral_amount = ReferralUser::where('user_id',$user->id)->where('payment_status','Completed')->where('pending_amount','>',0)->get();
			$referral_amount = number_format($referral_amount->sum('pending_amount'),2,'.','');

			return response()->json([
				'status_code' 	=> '1',
				'status_message'=> __('messages.api.payout_successfully'),
				'referral_amount' => $referral_amount,
				'owe_amount' 	=> $owe_amount->amount,
				'currency_code' => $owe_amount->currency_code
			]);
		}
		
		return response()->json([
			'status_code' => '0',
			'status_message' => __('messages.api.not_generate_amount'),
		]);
	}
}