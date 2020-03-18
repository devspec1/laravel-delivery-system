<?php

/**
 * Rating Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Rating
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helper\RequestHelper;
use App\Http\Start\Helpers;
use App\Models\Fees;
use App\Models\Rating;
use App\Models\Request as RideRequest;
use App\Models\Trips;
use App\Models\ManageFare;
use App\Models\User;
use App\Models\UsersPromoCode;
use App\Models\Wallet;
use App\Models\ScheduleRide;
use App\Models\Company;
use App\Models\DriverOweAmount;
use App\Repositories\DriverOweAmountRepository;
use Auth;
use DateTime;
use DB;
use Illuminate\Http\Request;
use App\Http\Helper\InvoiceHelper;
use JWTAuth;
use Validator;

class RatingController extends Controller
{
	protected $request_helper; // Global variable for Helpers instance

	public function __construct(RequestHelper $request,DriverOweAmountRepository $driver_owe_amt_repository,InvoiceHelper $invoice_helper)
	{
		$this->request_helper = $request;
		$this->helper = new Helpers;
		$this->invoice_helper = $invoice_helper;
		$this->driver_owe_amt_repository = $driver_owe_amt_repository;
	}

	/**
	 * Display the Diver rating
	 * @param  Post method request inputs
	 *
	 * @return Response Json
	 */
	public function driver_rating(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'user_type' => 'required|in:Driver,driver',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
                'status_code'     => '0',
                'status_message' => $validator->messages()->first(),
            ]);
		}
		$user = User::where('id', $user_details->id)->first();

		if($user == '') {
			return response()->json([
				'status_code' 	 => '0',
				'status_message' => "Invalid credentials",
			]);
		}

		$total_rated_trips = DB::table('rating')->select(DB::raw('count(id) as total_rated_trips'))
			->where('driver_id', $user_details->id)->where('rider_rating', '>', 0)->first()->total_rated_trips;

		$total_rating = DB::table('rating')->select(DB::raw('sum(rider_rating) as rating'))
			->where('driver_id', $user_details->id)->where('rider_rating', '>', 0)->where('driver_id', $user_details->id)->first()->rating;

		$total_rating_count = Rating::where('driver_id', $user_details->id)->where('rider_rating','>', 0)->get()->count();

		$life_time_trips = DB::table('trips')->select(DB::raw('count(id) as total_trips'))
			->where('driver_id', $user_details->id)->first()->total_trips;

		$five_rating_count = Rating::where('driver_id', $user_details->id)->where('rider_rating', 5)->get()->count();

		$driver_rating = '0.00';
		if ($total_rating_count != 0) {
			$driver_rating = (string) round(($total_rating / $total_rating_count), 2);
		}

		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> "Success",
			'total_rating' 		=> @$total_rated_trips != '' ? $total_rated_trips : '0',
			'total_rating_count'=> @$life_time_trips != '' ? $life_time_trips : '0',
			'driver_rating' 	=> @$driver_rating != '' ? $driver_rating : '0.00',
			'five_rating_count' => @$five_rating_count != '' ? $five_rating_count : '0',
		]);
	}

	/**
	 * Get The Invoice of the given Trip id
	 *
	 * @param  Get method request inputs
	 * @return Response Json
	 */
	public function getinvoice(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$request->merge(['payment_mode' =>strtolower($request->payment_mode)]);
		$rules = array(
			'user_type' => 'required|in:Rider,rider,Driver,driver',
			'trip_id' => 'required',
			'payment_mode' => 'in:paypal,stripe,cash,braintree',
			'is_wallet' => 'in:Yes,No',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
                'status_code'     => '0',
                'status_message' => $validator->messages()->first(),
            ]);
		}

		$request->payment_mode = ($request->payment_mode=='paypal')?'PayPal':ucfirst($request->payment_mode);

		$user = User::where('id', $user_details->id)->first();

		$trips = Trips::where('id', $request->trip_id)->first();

		$save = 0;
		if ($request->payment_mode && $trips->is_calculation == 0) { //if is_calculation is zero and payment_mode send then update payment mode in table
			$payment_method_store = $request->payment_mode;
			if ($request->is_wallet == 'Yes' && $payment_method_store != 'Wallet') {
				$payment_method_store = $request->payment_mode . ' & Wallet';
			}

			//If user change payment mode then change payment mode in trips,requests & schedule_rides tables also
			Trips::where('id', $request->trip_id)->update(['payment_mode' => $payment_method_store]);
			$rideRequest = RideRequest::where('id', $trips->request_id)->first();
			$rideRequest->payment_mode = $payment_method_store;
			$rideRequest->save();
			ScheduleRide::where('id', $rideRequest->schedule_id)->update(['payment_method' => $rideRequest->payment_mode]);
		}

		if ($trips->status == 'Payment') {
			$data = [
				'trip_id' 	=> $request->trip_id,
				'user_type' => $request->user_type,
				'user_id' 	=> $user->id,
				'save_to_trip_table' => $save,
			];
			$trips = $this->invoice_helper->calculation($data);
			return $this->invoice_helper->getInvoice($trips,$data);
		}

		return response()->json([
			'status_code' 	 => '2',
			'status_message' => __('messages.api.something_went_wrong'),
		]);
	}

	/**
	 * Update the trip Rating given by Driver or Rider
	 * @param  Post method request inputs
	 *
	 * @return Response Json
	 */
	public function trip_rating(Request $request) 
	{
        $user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'user_type' => 'required|in:Rider,rider,Driver,driver',
			'rating' => 'required',
			'trip_id' => 'required',
		);

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()) {
            return [
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ];
        }
		$user = User::where('id', $user_details->id)->first();

		$trips = Trips::where('id', $request->trip_id)->first();

		if($user == '') {
			return response()->json([
				'status_code'	 => '0',
				'status_message' => __('messages.invalid_credentials'),
			]);
		}

		$rating = Rating::where('trip_id', $request->trip_id)->first();
		$user_type = strtolower($request->user_type);

		if ($user_type == 'rider') {
			$data = [
				'trip_id' => $request->trip_id,
				'user_id' => $trips->user_id,
				'driver_id' => $trips->driver_id,
				'rider_rating' => $request->rating,
				'rider_comments' => @$request->rating_comments != '' ? $request->rating_comments : '',
			];

			Rating::updateOrCreate(['trip_id' => $request->trip_id], $data);
		}
		else {
			$data = [
				'trip_id' => $request->trip_id,
				'user_id' => $trips->user_id,
				'driver_id' => $trips->driver_id,
				'driver_rating' => $request->rating,
				'driver_comments' => @$request->rating_comments != '' ? $request->rating_comments : '',
			];
			Rating::updateOrCreate(['trip_id' => $request->trip_id], $data);
		}

		$trip = Trips::where('id', $request->trip_id)->first();

		if(!in_array($trip->status,['Rating','Payment'])) {
			return response()->json([
				'status_code' => '0',
				'status_message' => __('messages.api.trip_already_completed'),
			]);
		}
		$trip->status = 'Payment';

		if($user_type == 'rider') {
			$currency_code = $user_details->currency->code;
			$tips 		= currencyConvert($currency_code, $trip->getOriginal('currency_code'),$request->tips);
			$trip->tips = $tips;
		}

		$trip->save();
		
		return response()->json([
			'status_code' => '1',
			'status_message' => "Rating successfully",
		]);
	}

	/**
	 * Display the Rider Feedback
	 * @param  Post method request inputs
	 *
	 * @return Response Json
	 */
	public function rider_feedback(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'user_type' => 'required|in:Driver,driver',
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

		$rider_comments = DB::table('rating')
			->select(DB::raw('DATE_FORMAT(created_at, "%d %M %Y") AS date,rider_rating,rider_comments,trip_id'))
			->where('driver_id', $user_details->id)
			->where('rider_rating', '>', 0)
			->orderBy('trip_id', 'DESC')
			->get();

		return response()->json([
			'status_code' 	 => '1',
			'status_message' => __('messages.api.listed_successfully'),
			'rider_feedback' => $rider_comments,
		]);
	}

}