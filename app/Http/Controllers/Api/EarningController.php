<?php

/**
 * Earning Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Earning
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\PromoCode;
use App\Models\Trips;
use App\Models\User;
use App\Models\UsersPromoCode;
use App\Models\PaymentMethod;
use App\Models\Wallet;
use App\Http\Helper\RequestHelper;
use App\Http\Helper\InvoiceHelper;
use JWTAuth;
use Validator;

class EarningController extends Controller
{
	protected $request_helper; // Global variable for Helpers instance

	protected $provider;

	public function __construct(RequestHelper $request,InvoiceHelper $invoice_helper)
	{
		$this->request_helper = $request;
		$this->invoice_helper = $invoice_helper;
	}

	/**
	 * Display the Earning chart details in Driver
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function earning_chart(Request $request)
	{
		$driver_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'user_type' => 'required|in:Driver,driver',
			'start_date' => 'required',
			'end_date' => 'required',
		);

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

		$user = User::where('id', $driver_details->id)->where('user_type', $request->user_type)->first();
		$company_id = $user->company_id;

		$last_trip = Trips::where('driver_id', $driver_details->id)->where('status', 'Completed')->orderBy('id', 'DESC')->first();

		$last_trip = $last_trip->company_driver_earnings ?? "0";

		$symbol = Currency::where('code', $user->currency_code)->first()->symbol;

		$total_weekly_fare = 0;

		for ($i = 0; $i < 7; $i++) {

			$created_at = date("Y-m-d", strtotime($request->start_date . '+' . $i . 'day'));
			$created_day = date("l", strtotime($created_at));

			$trips = Trips::
				whereRaw("DATE_FORMAT(created_at,'%Y-%m-%d') = '" . $created_at . "'")
				->where('driver_id', $driver_details->id)
				->where('status', 'Completed')
				->get();

			$fare_amount = '0.00';

			if ($trips->count()) {
				$fare_amount = $trips->sum('company_driver_earnings');
			}

			$total_weekly_fare += $fare_amount;
			$trips_array[] = ["created_at" => $created_at, "day" => $created_day, "daily_fare" => strval($fare_amount)];
		}

		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> "Success",
			'recent_payout' 	=> '',
			'trip_details' 		=> $trips_array,
			'last_trip' 		=> $last_trip,
			'total_week_amount' => (string) number_format(($total_weekly_fare), 2),
			'currency_code' 	=> @$user->currency_code,
			'currency_symbol' 	=> $symbol,
		]);

		return response()->json($user);
	}

	/**
	 * After Payment and  update the trip status
	 * @param  POST method request inputs
	 *
	 * @return Response Json
	 */
	public function afterPayment(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();
		$payment_methods = collect(PAYMENT_METHODS);
		$payment_types = $payment_methods->pluck('key')->implode(',');

		$rules = array(
			'trip_id' 		=> 'required|exists:trips,id',
			'payment_type' 	=> 'required|in:'.$payment_types,
		);

		$validator = Validator::make($request->all(), $rules);
		if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

		$user = User::where('id', $user_details->id)->first();

		if($user == '') {
			return response()->json([
				'status_code'	 => '0',
				'status_message' => __('messages.invalid_credentials'),
			]);
		}

		$trip = Trips::find($request->trip_id); 
		if($trip->status != 'Payment' && $trips->status != 'Rating') {
			return response()->json([
				'status_code' => '0',
				'status_message' => __('messages.api.something_went_wrong'),
			]);
		}

		if($trip->is_calculation == 0) {
			$data = [
				'trip_id' => $request->trip_id,
				'user_id' => $user_details->id,
				'save_to_trip_table' => 1,
			];
			$trip = $this->invoice_helper->calculation($data);
		}

		if($trip->total_fare > 0) {
			if($request->payment_type != "stripe" && $request->pay_key == '') {
	            return response()->json([
					'status_code' => '0',
					'status_message' => __('messages.api.something_went_wrong'),
				]);
	        }

	        $payment_data['currency_code'] = $trip->currency_code;
			$payment_data['amount'] = $trip->total_fare;

			if($request->payment_type == 'stripe') {
				$payment_method = PaymentMethod::where('user_id', $user_details->id)->first();
				if($payment_method == '') {
					return response()->json([
		                'status_code' => '0',
		                'status_message' => __('messages.api.please_add_card_details'),
		            ]);
				}

				$payment_data = array(
					"amount" 		=> $trip->total_fare * 100,
					'currency' 		=> $trip->currency_code,
					'description' 	=> 'Trip Payment by '.$user_details->first_name,
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

			if(!$pay_result->status) {
				$trip->is_calculation = 0;
				$trip->status = 'Payment';
				$trip->save();

				return response()->json([
	                'status_code' => '0',
	                'status_message' => $pay_result->status_message,
	            ]);
			}
		}

		$trip = Trips::where('id', $request->trip_id)->first();
		$trip->status = 'Completed';
		$trip->paykey = $pay_result->transaction_id ?? '';
		$trip->payment_status = 'Completed';
		$trip->save();

		$data = [
			'trip_id' => $trip->id,
			'correlation_id' => $pay_result->transaction_id ?? '',
			'driver_payout_status' => ($trip->driver_payout) ? 'Pending' : 'Completed',
		];

		Payment::updateOrCreate(['trip_id' => $trip->id], $data);

		$driver = User::where('id', $trip->driver_id)->first();

		$push_title = "Payment Completed";

		$push_data['push_title'] = $push_title;
        $push_data['data'] = array(
        	'trip_payment' => array(
        		'status' => 'Paid',
        		'trip_id' => $trip->id,
        		'rider_thumb_image' => $trip->rider_profile_picture
        	)
        );
        $this->request_helper->SendPushNotification($driver,$push_data);

		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> "Paid Successfully",
			'currency_code' 	=> $trip->currency_code ?? '',
			'total_time' 		=> $trip->total_time ?? '0.00',
			'total_km' 			=> $trip->total_km ?? '0.00',
			'total_time_fare' 	=> $trip->time_fare ?? '0.00',
			'total_km_fare' 	=> $trip->distance_fare ?? '0.00',
			'base_fare' 		=> $trip->base_fare ?? '0.00',
			'total_fare' 		=> $trip->total_fare ?? '0.00',
			'access_fee' 		=> $trip->access_fee ?? '0.00',
			'pickup_location' 	=> $trip->pickup_location ?? '',
			'drop_location' 	=> $trip->drop_location ?? '',
			'driver_payout' 	=> $trip->driver_payout ?? '0.00',
			'trip_status'		=> $trip->status,
			'driver_thumb_image'=> $driver->profile_picture->src ?? url('images/user.jpeg'),
		]);
	}

	public function add_wallet(Request $request)
	{
		$user_details 	= JWTAuth::toUser($request->token);

		$payment_methods = collect(PAYMENT_METHODS);
		$payment_methods = $payment_methods->reject(function($value) {
            $is_enabled = payment_gateway('is_enabled',ucfirst($value['key']));
            return ($is_enabled != '1');
        });
		$payment_types = $payment_methods->pluck('key')->implode(',');

		$rules = array(
			'amount' 		=> 'required|min:1',
			'payment_type' 	=> 'required|in:'.$payment_types,
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

		$wallet_amount = $request->amount;

		$wallet = Wallet::whereUserId($user_details->id)->first();
		$transaction_id = '';

		$payment_data['currency_code'] = $user_details->currency_code;
		$payment_data['amount'] = $wallet_amount;
		$payment_data['user_id'] = $user_details->id;

		if($request->payment_type == 'stripe') {
			$payment_method = PaymentMethod::where('user_id', $user_details->id)->first();
			if($payment_method == '') {
				return response()->json([
	                'status_code' => '0',
	                'status_message' => __('messages.api.please_add_card_details'),
	            ]);
			}

			$payment_data = array(
				"amount" 		=> $wallet_amount * 100,
				'currency' 		=> $user_details->currency_code,
				'description' 	=> 'Wallet Payment by '.$user_details->first_name,
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

		if($wallet) {
			$prev_wallet_amount = $wallet->original_amount ?? 0;
			$wallet_amount = floatval($prev_wallet_amount) + floatval($wallet_amount);
		}
		else {
			$wallet = new Wallet;
		}

		$wallet->amount = $wallet_amount;
		$wallet->paykey = $pay_result->transaction_id;
		$wallet->currency_code = $user_details->currency_code;
		$wallet->user_id = $user_details->id;
		$wallet->save();

		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> "Amount Added Successfully",
			'wallet_amount' 	=> $wallet->original_amount,
		]);
	}

	public function add_promo_code(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'code' => 'required',
		);

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

		$promo_code_check = PromoCode::whereCode($request->code)->where('status', 'Active')->first();
		$promo_code_date_check = PromoCode::whereCode($request->code)->where('expire_date', '>=', date('Y-m-d'))->first();

		if (@$promo_code_check) {
			if (@$promo_code_date_check) {
				$users_promo_code_check = UsersPromoCode::whereUserId($user_details->id)->wherePromoCodeId($promo_code_date_check->id)->first();
				if (@$users_promo_code_check) {
					return ['status_code' => '0', 'status_message' => trans('messages.promo_already_applied')];
				}

				$users_promo_code = new UsersPromoCode;

				$users_promo_code->user_id = $user_details->id;
				$users_promo_code->promo_code_id = $promo_code_date_check->id;

				$users_promo_code->save();

				$users_promo_codes = UsersPromoCode::whereUserId($user_details->id)->whereTripId(0)->with('promo_code')->get();

				$final_promo_details = [];

				foreach ($users_promo_codes as $row) {
					if (@$row->promo_code) {
						$promo_details['id'] = $row->promo_code->id;
						$promo_details['amount'] = $row->promo_code->amount;
						$promo_details['code'] = $row->promo_code->code;
						$promo_details['expire_date'] = $row->promo_code->expire_date_dmy;
						$final_promo_details[] = $promo_details;
					}
				}

				return ['status_code' => '1', 'status_message' => trans('messages.promo_applied_success'), 'promo_details' => $final_promo_details];
			}
			return ['status_code' => '0', 'status_message' => trans('messages.promo_expired')];
		}
		return ['status_code' => '0', 'status_message' => trans('messages.promo_invalid')];
	}
}