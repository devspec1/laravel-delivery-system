<?php

/**
 * Trip Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Trip
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cancel;
use App\Models\DriverLocation;
use App\Models\ScheduleRide;
use App\Models\Trips;
use App\Models\User;
use App\Models\Rating;
use App\Models\ManageFare;
use App\Models\Request as RideRequest;
use App\Models\UsersPromoCode;
use App\Models\CancelReason;
use App\Models\TollReason;
use App\Models\ApiCredentials;
use App\Models\TripTollReason;
use App\Models\Fees;
use DateTime;
use DB;
use JWTAuth;
use Validator;

class TripController extends Controller
{
	public function __construct()
	{
		$this->request_helper = resolve('App\Http\Helper\RequestHelper');
		$this->invoice_helper = resolve('App\Http\Helper\InvoiceHelper');
		$this->paginate_limit = 10;
	}

	protected function checkPendingTrips($user)
	{
		if($user->user_type == 'Rider') {
			$incomplete_trips = Trips::where('user_id',$user->id)->whereNotIn('status',['Completed','Cancelled'])->orderBy('id','desc')->first();
		}
		else {
			$incomplete_trips = Trips::where('driver_id',$user->id)->whereNotIn('status',['Completed','Cancelled'])->orderBy('id','desc')->first();
		}

		return $incomplete_trips->id ?? 0;
	}

	protected function getTripDetails($trip_id,$user)
	{
		$trip = Trips::with('car_type.manage_fare','users','driver.driver_location')->where('id', $trip_id)->first();
		$driver = $trip->driver;
		$driver_location = $driver->driver_location;

		$driver_rating = getDriverRating($trip->driver_id);
		
		$final_promo_details = $this->invoice_helper->getUserPromoDetails($user->id);

		$arrival_time = -1;
		
		$data = [
			'user_id' => $user->id,
			'user_type' => $user->user_type,
		];

		$invoice = $this->invoice_helper->formatInvoice($trip,$data);

		$car_type = $trip->car_type;

		$payment_mode  = $trip->payment_mode;
		$symbol = html_entity_decode($trip->currency->symbol);
		$subtotal_fare = (checkIsCashTrip($trip->payment_mode)) ? $trip->total_fare : $trip->subtotal_fare;
		$driver_earnings = $symbol.number_format($trip->company_driver_earnings,2);
		$total_fare = $trip->admin_total_amount;
		if($trip->driver->company_id != 1 && checkIsCashTrip($trip->payment_mode) && $trip->total_fare == 0) {
			$total_fare = $trip->company_driver_earnings;
		}

		$trip_data = array(
			'trip_id'			=> $trip->id,
			'request_id'		=> $trip->request_id,
			'otp' 				=> $trip->otp,
			'pickup_latitude' 	=> $trip->pickup_latitude,
			'pickup_longitude' 	=> $trip->pickup_longitude,
			'drop_latitude' 	=> $trip->drop_latitude,
			'drop_longitude' 	=> $trip->drop_longitude,
			'trip_path'			=> $trip->trip_path,
			'map_image'			=> $trip->map_image,
			'car_type'			=> $car_type->car_name,
			'car_active_image' 	=> $car_type->active_image,
			'waiting_time' 		=> strval(@$car_type->manage_fare->waiting_time),
			'waiting_charge' 	=> @$car_type->manage_fare->waiting_charge,
			'pickup_location' 	=> $trip->pickup_location,
			'drop_location' 	=> $trip->drop_location,
			'driver_latitude' 	=> $driver->driver_location->latitude,
			'driver_longitude' 	=> $driver->driver_location->longitude,
			'vehicle_number' 	=> $driver->driver_documents->vehicle_number ?? '',
			'vehicle_name' 		=> $driver->driver_documents->vehicle_name ?? '',
			'arrival_time' 		=> $arrival_time,
			'total_time' 		=> $trip->total_time,
			'total_km' 			=> $trip->total_km,
			'begin_trip' 		=> $trip->begin_trip,
			'end_trip' 			=> $trip->end_trip,
			'payment_mode' 		=> $payment_mode,
			'payment_status' 	=> $trip->payment_status,
			'currency_symbol' 	=> $symbol,
			'sub_total_fare' 	=> $subtotal_fare,
			'total_fare' 		=> $total_fare,
			'driver_earnings' 	=> $driver_earnings,
			'driver_payout' 	=> $trip->driver_payout,
			'status' 			=> $trip->status ?? '',
			'invoice' 			=> $invoice ,
			'booking_type' 		=> @$trip->ride_request->schedule_ride->booking_type ?? '',
			'created_at' 		=> $trip->created_at->format('Y-m-d H:i:s'),
		);

		// Set Waiting time and charge based on car type when trip is not completed
		if(in_array($trip->status,['Completed','Rating'])) {
			$trip_data['waiting_charge'] = $trip->waiting_charge;
		}

		if($user->user_type == 'Rider') {
			$user_data = array(
				'driver_id' 		=> $driver->id,
				'driver_name' 		=> $driver->first_name,
				'mobile_number' 	=> $driver->phone_number,
				'driver_thumb_image'=> @$driver->profile_picture->src ?? url('images/user.jpeg'),
				'rating'	 		=> $driver_rating,
				'promo_details' 	=> $final_promo_details,
			);
		}
		else {
			$user_data = array(
				'rider_id' 					=> $trip->users->id,
				'rider_name' 				=> $trip->users->first_name,
				'mobile_number' 			=> $trip->users->phone_number,
				'rider_thumb_image' 		=> @$trip->profile_picture->src != '' ? $trip->profile_picture->src : url('images/user.jpeg'),
				'rating'					=> '',
				'payment_mode'	 			=> $trip->payment_mode,
			);
		}

		$other_data = array(
			'paypal_mode' 	=> PAYPAL_MODE,
			'paypal_app_id' => PAYPAL_CLIENT_ID,
		);

		return [
			'status' => true,
			'data' => array_merge($trip_data,$user_data,$other_data),
		];
	}

	/**
	 * Common Function to Map Trips Details
	 * 
	 * @param Collection $trips
	 * @return Collection Formatted Trips
	 */
	protected function mapTripDetails($trips,$user_type)
	{
		return $trips->map(function($trip) use ($user_type) {
			if(isset($trip->booking_type)) {
				$trip_data = array(
					'trip_id'			=> $trip->id,
					'car_id'			=> $trip->car_id,
					'car_name'			=> $trip->car_name,
					'trip_path'			=> $trip->trip_path,
					'pickup_location' 	=> $trip->pickup_location,
					'drop_location' 	=> $trip->drop_location,
					'booking_type' 		=> $trip->booking_type,
					'schedule_time'		=> $trip->schedule_time,
					'schedule_date' 	=> $trip->schedule_date,
					'schedule_display_date'	=> $trip->schedule_display_date,
					'currency_symbol'	=> html_entity_decode($trip->currency_symbol),
					'total_fare'	 	=> $trip->fare_estimation ?? '0',
					'status'		 	=> $trip->status,
				);

				if($user_type == 'Driver') {
					$other_data = array(
						'rider_name' 		=> $trip->rider_name,
						'rider_thumb_image'	=> $trip->rider_thumb_image,
					);
				}
				else {
					$other_data = array(
						'driver_name'		=> $trip->driver_name ?? '',
						'driver_thumb_image' => @$trip->driver->profile_picture->src ?? url('images/user.jpeg'),
					);
				}

				return array_merge($trip_data,$other_data);
			}

			$subtotal_fare = in_array($trip->payment_mode,['Cash','Cash & Wallet']) ? $trip->total_fare : $trip->subtotal_fare;

			$total_fare = $trip->admin_total_amount;

			$trip_data = array(
				'trip_id'			=> $trip->id,
				'car_id'			=> $trip->car_id,
				'trip_path'			=> $trip->trip_path,
				'total_fare'		=> $total_fare,
				'subtotal_fare'		=> $subtotal_fare,
				'driver_payout'		=> $trip->driver_payout,
				'driver_earnings'	=> $trip->company_driver_earnings ?? '',
				'car_name'			=> $trip->car_type->car_name,
				'map_image' 		=> $trip->map_image,
				'pickup_latitude' 	=> $trip->pickup_latitude,
				'pickup_longitude' 	=> $trip->pickup_longitude,
				'drop_latitude' 	=> $trip->drop_latitude,
				'drop_longitude' 	=> $trip->drop_longitude,
				'pickup_location' 	=> $trip->pickup_location,
				'drop_location' 	=> $trip->drop_location,
				'booking_type' 		=> @$trip->ride_request->schedule_ride->booking_type ?? '',
				'currency_symbol'	=> html_entity_decode($trip->currency_symbol),
				'status'		 	=> $trip->status,
			);

			if($user_type == 'Driver') {
				$other_data = array(
					'rider_name' 		=> $trip->rider_name,
					'rider_thumb_image'	=> $trip->rider_thumb_image,
				);
			}
			else {
				$other_data = array(
					'driver_name'		=> $trip->driver_name,
					'driver_thumb_image' => @$trip->driver->profile_picture->src ?? url('images/user.jpeg'),
				);
			}

			return array_merge($trip_data,$other_data);
		});
	}

	/**
	 * Display the Arive Now Status
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function ariveNow(Request $request)
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

		$data = Trips::where('id', $request->trip_id)->first();

		$user_timezone = $data->ride_request->timezone;
		if ($user_timezone != '') {
			date_default_timezone_set($user_timezone);
		}

		$arrive_time = new DateTime(date("Y-m-d H:i:s"));
		
		Trips::where('id', $request->trip_id)->update(['arrive_time' => $arrive_time, 'status' => 'Begin trip']);

		$push_data['push_title'] = __('messages.api.driver_arrived');
		$push_data['data'] = array(
			'arrive_now' => array(
				'status' => 'Arrive Now',
				'trip_id' => $request->trip_id,
			)
		);
        $this->request_helper->SendPushNotification($data->users,$push_data);

		$schedule_ride = ScheduleRide::find($data->ride_request->schedule_id);

		//if booking is manual booking then send "Driver Arrived" SMS to rider
		if(isset($schedule_ride) && $schedule_ride->booking_type == 'Manual Booking') {
			/*$fare_details = ManageFare::where('location_id',$schedule_ride->location_id)->where('vehicle_id',$schedule_ride->car_id)->first();
			$waiting_time = $fare_details->waiting_time;
			$waiting_charge = $fare_details->currency_code.' '.$fare_details->waiting_charge;

			$push_title = __('messages.driver_arrive');
	        $text 		= __('messages.driver_arrived').' '.__('messages.api.waiting_charge_apply_after',['amount' => $waiting_charge,'minutes' => $waiting_time]);

	        $push_data['push_title'] = $push_title;
	        $push_data['data'] = array(
	            'custom_message' => array(
	                'title' => $push_title,
	                'message_data' => $text,
	            )
	        );

	        $this->request_helper->checkAndSendMessage($data->users,$text,$push_data);*/
		}

		return response()->json([
			'status_message' => "Success",
			'status_code' => '1',
		]);
	}

	/**
	 * Begin Trip From Driver
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function beginTrip(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'trip_id' => 'required|exists:trips,id',
			'begin_latitude' => 'required',
			'begin_longitude' => 'required',
		);

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }
		$pickup_location = $this->request_helper->GetLocation($request->begin_latitude, $request->begin_longitude);

		$user_location = DriverLocation::where('user_id', $user_details->id)->first();

		$trip = Trips::where('id', $request->trip_id)->first();

		$user_timezone = $trip->ride_request->timezone;
		if ($user_timezone != '') {
			date_default_timezone_set($user_timezone);
		}

		$begin_time = new DateTime(date("Y-m-d H:i:s"));

		Trips::where('id', $request->trip_id)->update(['status' => 'End trip', 'begin_trip' => $begin_time, 'pickup_latitude' => $request->begin_latitude, 'pickup_longitude' => $request->begin_longitude, 'pickup_location' => $pickup_location, 'otp' => 0]);

		$trip = Trips::where('id', $request->trip_id)->first();

		$push_data['push_title'] = __('messages.api.trip_begin_by_driver');
        $push_data['data'] = array(
            'begin_trip' => array(
            	'trip_id' => $request->trip_id
            )
        );

        $this->request_helper->SendPushNotification($trip->users,$push_data); 

		$schedule_ride = ScheduleRide::find($trip->ride_request->schedule_id);
		//if booking is manual booking then send "Trip Began" SMS to rider
		if (isset($schedule_ride) && $schedule_ride->booking_type == 'Manual Booking') {
			$push_title = __('messages.trip_begined');
	        $text 		= __('messages.trip_begined');

	        $push_data['push_title'] = $push_title;
	        $push_data['data'] = array(
	            'custom_message' => array(
	                'title' => $push_title,
	                'message_data' => $text,
	            )
	        );

	        $this->request_helper->checkAndSendMessage($trip->users,$text,$push_data);
		}

		return response()->json([
			'status_code' => '1',
			'status_message' => "Trip Started",
		]);
	}

	/**
	 * End Trip From Driver
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function end_trip(Request $request)
	{
		$user_details = $user = JWTAuth::toUser(request()->token);

		$rules = array(
			'trip_id' => 'required|exists:trips,id',
			'end_latitude' => 'required',
			'end_longitude' => 'required',
		);

		$fees = resolve('fees');

		$apply_extra_fee = @$fees->where('name','additional_fee')->first()->value;
        if($apply_extra_fee == 'Yes') {
        	$rules = array(
				'trip_id' => 'required|exists:trips,id',
				'end_latitude' => 'required',
				'end_longitude' => 'required',
				'toll_reason_id' => 'required_with:toll_fee|exists:toll_reasons,id',
				'toll_reason' => 'required_if:toll_reason_id,1',
				'toll_fee' => 'required_with:toll_reason_id',
			);
        }

		$messages = array(
			'toll_reason.required' => __('messages.api.toll_reason').' '. __('messages.field_is_required'),
		);

		$validator = Validator::make($request->all(), $rules, $messages);

		if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

		$trip = Trips::where('id', $request->trip_id)->first();

		$toll_fee = 0;
		if ($request->toll_fee) {
			$toll_fee = $request->toll_fee;
		}

		// Final Distance calcualtion
		$driver_location = DriverLocation::where('user_id', $user_details->id)->first();

		$user_timezone = $trip->ride_request->timezone;
		// $user_timezone = $this->request_helper->getTimeZone($driver_location->latitude, $driver_location->longitude);
		
		if ($user_timezone != '') {
			date_default_timezone_set($user_timezone);
		}

		if ($request->trip_id) {
			$total_km = number_format($request->total_km,5,'.','0');
			$data = [
				'user_id' => $user_details->id,
				'latitude' => $request->end_latitude,
				'longitude' => $request->end_longitude,
			];

			DriverLocation::updateOrCreate(['user_id' => $user_details->id], $data);
		}

		$end_time = new DateTime(date("Y-m-d H:i:s"));
		$drop_location = $this->request_helper->GetLocation($request->end_latitude, $request->end_longitude);

		//check uploaded image is set or not
		if (isset($_FILES['image'])) {
			$errors = array();
			$acceptable = view()->shared('acceptable_mimes');

			if ((!in_array($_FILES['image']['type'], $acceptable)) && (!empty($_FILES["image"]["type"]))) {
				return response()->json([
					'status_code' => "0",
					'status_message' => "Invalid file type. Only  JPG, GIF and PNG types are accepted.",
				]);
			}

			$type = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
			$file_name = substr(md5(uniqid(rand(), true)), 0, 8) . ".$type";
			$trip_id = $request->trip_id;
			$file_tmp = $_FILES['image']['tmp_name'];
			$dir_name = dirname($_SERVER['SCRIPT_FILENAME']) . '/images/map/' . $trip_id;
			$f_name = dirname($_SERVER['SCRIPT_FILENAME']) . '/images/map/' . $trip_id . '/' . $file_name;

			//check file directory is created or not
			if (!file_exists($dir_name)) {
				mkdir(dirname($_SERVER['SCRIPT_FILENAME']) . '/images/map/' . $trip_id, 0777, true);
			}

			move_uploaded_file($file_tmp, $f_name);

			$image_url = url('/') . '/images/map/' . $trip_id . '/' . $file_name;
		}

		// Hided for give rating after end trip
		/*$status = 'Payment';
		$schedule_ride = ScheduleRide::find($trip->ride_request->schedule_id);
		if (!isset($schedule_ride) || $schedule_ride->booking_type == 'Schedule Booking') {
		}*/
		$status = 'Rating';

		$trip_data = [
			'drop_latitude'	=> $request->end_latitude,
			'drop_longitude'=> $request->end_longitude,
			'drop_location' => $drop_location,
			'status' 		=> $status,
			'end_trip' 		=> $end_time,
			'total_km' 		=> $total_km,
			'map_image' 	=> $file_name ?? '',
			'toll_fee'		=> $toll_fee
		];

		if ($request->toll_reason_id) {
			$trip_data['toll_reason_id'] = $request->toll_reason_id;
		}

	  	Trips::where('id', $request->trip_id)->update($trip_data);
	  	if ($request->toll_reason_id == 1 && $request->toll_reason) {
	  		$trip_toll_reason = new TripTollReason();
	  		$trip_toll_reason->trip_id = $request->trip_id;
	  		$trip_toll_reason->reason = $request->toll_reason;
	  		$trip_toll_reason->save();
	  	}
	  	
		$push_title = __('messages.api.trip_ended_by_driver');
		$driver_thumb_image = @$trip->driver_thumb_image != '' ? $trip->driver_thumb_image : url('images/user.jpeg');
		$push_data = array('end_trip' => array('trip_id' => $request->trip_id, 'driver_thumb_image' => $driver_thumb_image));
		$user_type = $trip->users->user_type;
		$device_id = $trip->users->device_id;

		// Send push notification
		if ($trip->users->device_type != null && $trip->users->device_type!= '') {
			if ($trip->users->device_type == 1) {
				$this->request_helper->push_notification_ios($push_title, $push_data, $user_type, $device_id);
			}
			else {
				$this->request_helper->push_notification_android($push_title, $push_data, $user_type, $device_id);
			}
		}

		//if booking is manual booking then send "Trip Ended" SMS to rider
		if (isset($schedule_ride) && $schedule_ride->booking_type == 'Manual Booking') {
			$data = [
				'trip_id' 	=> $request->trip_id,
				'user_type' => $request->user_type,
				'user_id' 	=> $user->id,
				'save_to_trip_table' => 0,
			];

    		$push_title = __('messages.trip_ended');
	        $text 		= __('messages.trip_ended');

	        $push_data['push_title'] = $push_title;
	        $push_data['data'] = array(
	            'custom_message' => array(
	                'title' => $push_title,
	                'message_data' => $text,
	            )
	        );

	        $this->request_helper->checkAndSendMessage($trip->users,$text,$push_data);
		}	

		DriverLocation::where('user_id', $user_details->id)->update(['status' => 'Online']);

		$driver = User::where('id', $trip->driver_id)->first();

		return response()->json([
			'status_code' => '1',
			'status_message' => "Trip Completed",
		    'image_url' => isset($image_url) ? $image_url:'',
		]);
	}

	/**
	 * Display the Past Trips of Rider
	 * @param Get method request inputs
	 *
	 * @return Response Json
	 */
	public function get_past_trips(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'page' => 'required|min:1',
		);

		$validator = Validator::make($request->all(), $rules);
		if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

		$trips = Trips::with('car_type')->where('user_id', $user_details->id)->orderBy('id','DESC')->paginate($this->paginate_limit)->toJson();

		$data_result = json_decode($trips);
		if ($data_result->total == 0 || empty($data_result->data)) {
			return response()->json([
				'status_code' 		=> '0',
				'status_message' 	=> trans('messages.api.no_data_found'),
			]);
		}
		$trip_result = collect($data_result->data);
		$result_data = $this->mapTripDetails($trip_result,$user_details->user_type);

		return response()->json([
			'status_code'		=>	'1',
			'status_message'	=>	__('messages.api.listed_successfully'),
			'current_page'		=>  $data_result->current_page,
			'total_pages'		=>  $data_result->last_page,
			'data'				=>	$result_data,
		]);
	}

	/**
	 * Display the Upcoming Trips of Rider
	 * @param Get method request inputs
	 *
	 * @return Response Json
	 */
	public function get_upcoming_trips(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'page' => 'required|min:1',
		);

		$validator = Validator::make($request->all(), $rules);
		if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }
        $schedule = ScheduleRide::where('user_id', $user_details->id)->where('status','Pending')->orderBy('id','DESC')->paginate($this->paginate_limit);
        $schedule->getCollection()->transformWithAppends(['fare_estimation']);
		$schedule = $schedule->toJson();

		$data_result = json_decode($schedule);
		if ($data_result->total == 0 || empty($data_result->data)) {
			return response()->json([
				'status_code' 		=> '0',
				'status_message' 	=> trans('messages.api.no_data_found'),
			]);
		}
		$trip_result = collect($data_result->data);

		$result_data = $this->mapTripDetails($trip_result,$user_details->user_type);

		return response()->json([
			'status_code'		=>	'1',
			'status_message'	=>	__('messages.api.listed_successfully'),
			'current_page'		=>  $data_result->current_page,
			'total_pages'		=>  $data_result->last_page,
			'data'				=>	$result_data,
		]);
	}

	/**
	 * Display the Pending Trips For the Driver
	 * 
	 * @param Get method request inputs
	 * @return Response Json
	 */
	public function get_pending_trips(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'page' => 'required|min:1',
		);

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

		$trips = Trips::with('car_type')->where('driver_id', $user_details->id)->orderBy('id','DESC')->whereNotIn('status',['Completed','Cancelled'])->get();

		$schedule = ScheduleRide::where('driver_id', $user_details->id)->where('status','Pending')->get();

		$trips_data = $schedule->merge($trips);

		$data_result = $trips_data->paginate($this->paginate_limit);

		if ($data_result->isEmpty()) {
			return response()->json([
				'status_code' 		=> '0',
				'status_message' 	=> trans('messages.api.no_data_found'),
			]);
		}

		$trip_result = collect($data_result->items())->values();
		$result_data = $this->mapTripDetails($trip_result,$user_details->user_type);

		return response()->json([
			'status_code'		=>	'1',
			'status_message'	=>	__('messages.api.listed_successfully'),
			'current_page'		=>  $data_result->currentPage(),
			'total_pages'		=>  $data_result->lastPage(),
			'data'				=>	$result_data,
		]);
	}

	/**
	 * Display the Completed Trips
	 * 
	 * @param Get method request inputs
	 * @return Response Json
	 */
	public function get_completed_trips(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'page' => 'required|min:1',
		);

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

		$trips = Trips::with('car_type')->where('driver_id', $user_details->id)->orderBy('id','DESC')->whereIn('status',['Completed'])->paginate($this->paginate_limit)->toJson();

		$data_result = json_decode($trips);
		if ($data_result->total == 0 || empty($data_result->data)) {
			return response()->json([
				'status_code' 		=> '0',
				'status_message' 	=> trans('messages.api.no_data_found'),
			]);
		}
		$trip_result = collect($data_result->data);
		$result_data = $this->mapTripDetails($trip_result,$user_details->user_type);

		return response()->json([
			'status_code'		=>	'1',
			'status_message'	=>	__('messages.api.listed_successfully'),
			'current_page'		=>  $data_result->current_page,
			'total_pages'		=>  $data_result->last_page,
			'data'				=>	$result_data,
		]);
	}

	/**
	 * Map Image upload
	 * @param  Post method request inputs
	 *
	 * @return Response Json
	 */
	public function map_upload(Request $request)
	{
		$rules = array(
			'trip_id' => 'required|exists:trips,id',
			'image' => 'required',
			'token' => 'required',
		);

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }
		$user = JWTAuth::toUser($_POST['token']);

		$user_id = $user->id;
		//check uploaded image is set or not
		if (isset($_FILES['image'])) {
			$errors = array();
			$acceptable = view()->shared('acceptable_mimes');

			if ((!in_array($_FILES['image']['type'], $acceptable)) && (!empty($_FILES["image"]["type"]))) {
				return response()->json([
					'status_code' => "0",
					'status_message' => "Invalid file type. Only  JPG, GIF and PNG types are accepted.",
				]);
			}

			$type = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
			$file_name = substr(md5(uniqid(rand(), true)), 0, 8) . ".$type";
			$trip_id = $request->trip_id;
			$file_tmp = $_FILES['image']['tmp_name'];
			$dir_name = dirname($_SERVER['SCRIPT_FILENAME']) . '/images/map/' . $trip_id;
			$f_name = dirname($_SERVER['SCRIPT_FILENAME']) . '/images/map/' . $trip_id . '/' . $file_name;

			if (!file_exists($dir_name)) {
				mkdir(dirname($_SERVER['SCRIPT_FILENAME']) . '/images/map/' . $trip_id, 0777, true);
			}

			move_uploaded_file($file_tmp, $f_name);

			Trips::where('id', $request->trip_id)->update(['map_image' => @$file_name]);

			$image_url = url('/') . '/images/map/' . $trip_id . '/' . $file_name;

			return response()->json([
				'status_code' 		=> "1",
				'status_message' 	=> "Upload Successfully",
				'image_url' 		=> $image_url,
			]);
		}
	}

	/**
	 * Trip Cancel by Driver or Rider
	 * @param  Get method request inputs
	 *
	 * @return Response Json
	 */
	public function cancel_trip(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'user_type' => 'required|in:Rider,rider,Driver,driver',
			'trip_id' => 'required',
			'cancel_reason_id' => 'required',
		);

		$messages = array(
			'user_type.required' => trans('messages.required.user_type') . ' ' . trans('messages.field_is_required') . '',
			'trip_id.required' => trans('messages.required.trip_id') . ' ' . trans('messages.field_is_required') . '',
			'cancel_reason_id.required' => trans('messages.required.cancel_reason_id') . ' ' . trans('messages.field_is_required') . '',
		);

		$validator = Validator::make($request->all(), $rules, $messages);

		$validator->after(function ($validator) use ($request) {
			$cancelled_by = 'Driver';
			if ($request->user_type == 'Rider' || $request->user_type == 'rider') {
				$cancelled_by = 'Rider';
			}

			$cancel_reason_exists= CancelReason::active()->where('cancelled_by',$cancelled_by)->where('id',$request->cancel_reason_id)->exists();
	        if (!$cancel_reason_exists) {
	            $validator->errors()->add('cancel_reason_id', __('messages.api.reason_inactive_admin'));
	        }
	    });

		if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

		$user = User::where('id', $user_details->id)->where('user_type', $request->user_type)->first();

		if ($user == '') {
			return response()->json([
				'status_code' 	 => '0',
				'status_message' => "Invalid credentials",
			]);
		}

		$cancelled_id = Trips::where('id', $request->trip_id)->first();
		$user_type = strtolower($request->user_type);
		if ($user_type == 'rider') {
			$data = [
				'trip_id' => $request->trip_id,
				'user_id' => $user_details->id,
				'cancel_comments' => @$request->cancel_comments != '' ? $request->cancel_comments : '',
				'cancelled_by' => 'Rider',
				'cancel_reason_id' => @$request->cancel_reason_id,

			];

			Cancel::updateOrCreate(['trip_id' => $request->trip_id], $data);
			$driver_id = $cancelled_id->driver_id;
			$rider = User::where('id', $driver_id)->first();
			$device_id = $rider->device_id;
			$device_type = $rider->device_type;
			$user_type = $rider->user_type;
			$push_title = "Trip Cancelled by Rider";
		}
		else {
			$data = [
				'trip_id' => $request->trip_id,
				'user_id' => $user_details->id,
				'cancel_reason_id' => $request->cancel_reason_id,
				'cancel_comments' => @$request->cancel_comments != '' ? $request->cancel_comments : '',
				'cancelled_by' => 'Driver',
			];

			Cancel::updateOrCreate(['trip_id' => $request->trip_id], $data);
			$user_id = $cancelled_id->user_id;
			$driver_id = $cancelled_id->driver_id;
			$driver = User::where('id', $user_id)->first();
			$device_id = $driver->device_id;
			$device_type = $driver->device_type;
			$user_type = $driver->user_type;
			$push_title = __('messages.api.trip_cancelled_by_driver');
		}

		Trips::where('id', $request->trip_id)->update(['status' => 'Cancelled', 'payment_status' => 'Trip Cancelled']);
		DriverLocation::where('user_id', $cancelled_id->driver_id)->update(['status' => 'Online']);

		// push notification
		$push_data = array(
			'cancel_trip' => array(
				'trip_id' => $request->trip_id,
				'status' => 'Cancelled',
			)
		);

		if ($device_type == 1) {
			$this->request_helper->push_notification_ios($push_title, $push_data, $user_type, $device_id);
		}
		else {
			$this->request_helper->push_notification_android($push_title, $push_data, $user_type, $device_id);
		}

		return response()->json([
			'status_code' 	 => '1',
			'status_message' => "Success",
		]);
	}

	public function cancel_reasons()
	{
		$user_details = JWTAuth::parseToken()->authenticate();
		$cancel_reasons = CancelReason::active()->where('cancelled_by',$user_details->user_type)->get();

		return response()->json([
			'status_code' 	 => '1',
			'status_message' => "Success",
			'cancel_reasons' => $cancel_reasons,
		]);
	}

	public function toll_reasons()
	{
		$user_details = JWTAuth::parseToken()->authenticate();
		$toll_reasons = TollReason::where('id','>',1)->active()->get();
		$toll_reasons[] = TollReason::where('id',1)->active()->first();

		foreach ($toll_reasons as $toll_reason) {
			$toll_reason->commendable = 0; 
			if ($toll_reason->id == 1) {
				$toll_reason->commendable = 1;
			}
		}

		return response()->json([
			'status_message' => "Success",
			'status_code' => '1',
			'toll_reasons' => $toll_reasons
		]);
	}

	/**
	 * Get Trip details Of Given trip id. If trip id not passed then returns incomplete trip details
	 * 
	 * @param  Get method request inputs
	 * @return Response Json
	 */
	public function get_trip_details(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'trip_id' => 'nullable|exists:trips,id',
		);

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }
        
        $user_details 	= JWTAuth::parseToken()->authenticate();
        $trip_id 		= $request->trip_id;
        
        // Check Any Trip Is Incomplete Or Not
        if($trip_id == '') {
        	$trip_id = $this->checkPendingTrips($user_details);
        	
        	if($trip_id == 0) {
				return response()->json([
					'status_code' => '0',
					'status_message' => __('messages.api.no_trips_found'),
				]);
        	}
        }

        $trip_detail = $this->getTripDetails($trip_id,$user_details);
		if(!$trip_detail['status']) {
			return response()->json([
                'status_code' => '0',
                'status_message' => $trip_detail['status_message'],
            ]);
		}

		$return_data = array_merge([
            'status_code' => '1',
            'status_message' => __('messages.api.listed_successfully'),
        ],$trip_detail['data']);

		return response()->json($return_data);

        // Get Trip Details For Given Trip ID
        $trip = Trips::where('id',$request->trip_id)->first();

        $data = [
			'user_id' => $user_details->id,
			'user_type' => $user_details->user_type,
		];

		$symbol = html_entity_decode($trip->currency->symbol);

		$subtotal_fare = ($trip->payment_mode == 'Cash' || $trip->payment_mode == 'Cash & Wallet') ? $trip->total_fare : $trip->subtotal_fare;
		$driver_earnings = $symbol.$trip->company_driver_earnings;

		$trip_data = collect($trip)->only(['user_id','car_id','request_id','otp','pickup_latitude','pickup_longitude','drop_latitude','drop_longitude','pickup_location','drop_location','rider_name','rider_profile_picture','driver_id','driver_name','vehicle_name','waiting_charge','map_image','trip_path','total_time','begin_trip','end_trip','payment_mode','payment_status','currency_code','status','total_fare','driver_payout','total_km','created_at']);
		
		$trip_data['trip_id'] = $trip->id;
		$trip_data['vehicle_number'] = $trip->driver->driver_documents->vehicle_number;
		$trip_data['driver_latitude'] = $trip->driver->driver_location->latitude;
		$trip_data['driver_longitude'] = $trip->driver->driver_location->longitude;

		$trip_data['waiting_time'] = strval($trip->car_type->manage_fare->waiting_time);
		// Set Waiting time and charge based on car type when trip is not completed
		if(!in_array($trip->status,['Completed','Rating'])) {
			$trip_data['waiting_charge'] = $trip->car_type->manage_fare->waiting_charge;
		}

		$trip_data['rating'] = '';
		if($user_details->user_type == 'Rider') {
			$trip_data['rating'] = getDriverRating($trip->driver_id);
		}

		$trip_data['driver_thumb_image'] = $trip->driver->profile_picture->src ?? '';
		$trip_data['car_active_image'] 	= $trip->car_type->active_image ?? '';
		$trip_data['sub_total_fare'] 	= $symbol.$subtotal_fare;
		$trip_data['driver_earnings'] 	= $driver_earnings;
		$trip_data['invoice'] 			= $this->invoice_helper->formatInvoice($trip,$data);

		return response()->json(array_merge([
			'status_code' => '1',
			'status_message' =>  __('messages.api.listed_successfully'),
		],$trip_data->toArray()));

	}

	/**
	 * Accept the Trip Request
	 *
	 * @param Get method request inputs
	 * @return @return Response in Json
	 */
	public function acceptTrip(Request $request)
	{
		$user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'user_type' => 'required|in:Driver,driver',
			'status' 	=> 'required|in:Online,online,Trip,trip',
			'request_id'=> 'required',
		);

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			return response()->json([
                'status_code'     => '0',
                'status_message' => $validator->messages()->first(),
            ]);
		}

		$user = User::where('id', $user_details->id)->where('user_type', $request->user_type)->first();

		if (!$user) {
			return response()->json([
				'status_code'	 => '0',
				'status_message' => __('messages.invalid_credentials'),
			]);
		}
		
		$req = RideRequest::where('id', $request->request_id)->first();
		$request_group = $req->group_id;
		$request_status = RideRequest::where('group_id', $request_group)->where('status', 'Accepted')->count();
		if ($request_status != "0") {
			return response()->json([
				'status_code' 	=> '0',
				'status_message'=> "Already Accepted",
			]);
		}

		DriverLocation::where('user_id', $user->id)->update(['status' => $request->status]);
		RideRequest::where('id', $request->request_id)->update(['status' => 'Accepted']);

		$data = RideRequest::where('id', $request->request_id)->first();

		if ($data->schedule_id != '') {
			ScheduleRide::where('id', $data->schedule_id)->update(['status' => 'Completed']);
			$schedule_ride = ScheduleRide::where('id', $data->schedule_id)->first();
		}
		
		if($req->timezone != '') {
			date_default_timezone_set($req->timezone);
		}
		else {
			$driver_location = DriverLocation::where('user_id', $user_details->id)->first();
			//  get user default location
			$user_timezone = $this->request_helper->getTimeZone($driver_location->latitude, $driver_location->longitude);

			if($user_timezone != '') {
				date_default_timezone_set($user_timezone);
			}
		}

		// Create Trip
		$trip = new Trips;
		$trip->user_id = $data->user_id;
		$trip->pickup_latitude = $data->pickup_latitude;
		$trip->pickup_longitude = $data->pickup_longitude;
		$trip->drop_latitude = $data->drop_latitude;
		$trip->drop_longitude = $data->drop_longitude;
		$trip->driver_id = $data->driver_id;
		$trip->car_id = $data->car_id;
		$trip->pickup_location = $data->pickup_location;
		$trip->drop_location = $data->drop_location;
		$trip->request_id = $data->id;
		$trip->trip_path = $data->trip_path;
		$trip->payment_mode = $data->payment_mode;
		$trip->status = 'Scheduled';
		$trip->currency_code = $user->currency->code;
		$trip->peak_fare = $data->peak_fare;
		$trip->otp = mt_rand(1000, 9999);
		$trip->save();

		$push_data['push_title'] = __('messages.api.request_accepted');
        $push_data['data'] = array(
            'accept_request' => array(
            	'trip_id' => $trip->id
            )
        );

        $this->request_helper->SendPushNotification($data->users,$push_data);

		if (isset($schedule_ride) && $schedule_ride->booking_type == 'Manual Booking') {
			$push_title = __('messages.request_accepted');
	        $text 		= __('messages.api.your_otp_to_begin_trip').$trip->otp;

	        $push_data['push_title'] = $push_title;
	        $push_data['data'] = array(
	            'custom_message' => array(
	                'title' => $push_title,
	                'message_data' => $text,
	            )
	        );

	        $text = $push_title.$text;

	        $this->request_helper->checkAndSendMessage($data->users,$text,$push_data);
		}

		$trip_detail = $this->getTripDetails($trip->id,$user_details);
		if(!$trip_detail['status']) {
			return response()->json([
                'status_code' => '0',
                'status_message' => $trip_detail['status_message'],
            ]);
		}

		$return_data = array_merge([
            'status_code' => '1',
            'status_message' => __('messages.api.listed_successfully'),
        ],$trip_detail['data']);

		return response()->json($return_data);
	}

	/**
	 * Send Message to the User
	 * 
	 * @param  Get method request inputs
	 * @return Response Json
	 */
	public function send_message(Request $request)
	{
		$user = JWTAuth::parseToken()->authenticate();

		$rules = array(
			'receiver_id' 	=> 'required|exists:users,id',
			'message' 		=> 'required',
		);

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()) {
            return response()->json([
                'status_code' => '0',
                'status_message' => $validator->messages()->first()
            ]);
        }

        $receiver = User::find($request->receiver_id);

        $push_data['change_title'] = 1;
        $push_data['push_title'] = $user->first_name;
        $push_data['data'] = array(
            'chat_notification' => array(
                'title' 		=> $request->message,
                'message_data' 	=> $request->message,
            )
        );

        $this->request_helper->SendPushNotification($receiver,$push_data);

        return response()->json([
            'status_code' => '1',
            'status_message' => __('messages.api.success'),
        ]);
	}
}