<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HomeDeliveryOrder;
use App\Models\DriverLocation;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Request as RideRequest;
use App\Models\DriversSubscriptions;
use App\Models\Trips;

use Validator;
use JWTAuth;
use DB;
use DateTime;

class HomeDeliveryController extends Controller
{
    /**
     * Get orders data
     * 
     * @param  Get method request inputs
     *
     * @return Response Json 
     */
    public function getOrders(Request $request)
    {
        $user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
            'distance' => 'required|in:5,10,15',
            'latitude' => 'required',
            'longitude'=> 'required',
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
        
        $job_array = array();
        $distances = array("5", "10", "15");
        if (in_array($request->distance, $distances)) {
            $job_array = $this->get_jobs_list($request, $user_details);
        }
        else{
            return response()->json([
				'status_code' 	 => '0',
				'status_message' => "Wrong distance",
			]);
        }

		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> "Success",
			'jobs'               => $job_array,
		]);
    }

        /**
     * Get orders data
     * 
     * @param  Get method request inputs
     *
     * @return Response Json 
     */
    public function getDriverOrders(Request $request)
    {
        $user_details = JWTAuth::parseToken()->authenticate();

		$user = User::where('id', $user_details->id)->first();

		if($user == '') {
			return response()->json([
				'status_code' 	 => '0',
				'status_message' => "Invalid credentials",
			]);
        }
        
        $job_array = $this->get_my_jobs_list($request, $user_details);

		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> "Success",
			'jobs'               => $job_array,
		]);
    }

    /**
     * Accept orders 
     * 
     * @param  Get method request inputs
     *
     * @return Response Json 
     */
    public function acceptOrder(Request $request)
    {
        $user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
            'order_id'  => 'required',
            'latitude'  => 'required',
            'longitude' => 'required',
            'distance'  => 'required|in:5,10,15',
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

        $order = HomeDeliveryOrder::where('id',$request->order_id)->first();

        $assign_status_message = '';

        $order_status = $order->status;

        if($order_status == 'new'){
            $subscription = DriversSubscriptions::where('user_id',$user->id)
            ->whereNotIn('status', ['canceled'])
            ->first();

            if(!$subscription){
                return response()->json([
                    'status_code' 		=> '0',
                    'status_message' 	=> 'Sorry, you have no subscription for this action.',
                ]);
            }
            else{
                $order->status = 'assigned';

                $order->driver_id = $user->id;

                $order->save();

                $assign_status_message = 'successfully assigned';
            }

            $job_array = $this->get_jobs_list($request, $user_details);

            return response()->json([
                'status_code' 		=> '1',
                'status_message' 	=> "Order with id " . $order->id . " " . $assign_status_message,
                'jobs'               => $job_array,            
            ]);
        }
        else{
            return response()->json([
                'status_code' 		=> '0',
                'status_message' 	=> 'Order already assigned.',
            ]);
        }
    }

    /**
     * Proceed orders 
     * 
     * @param  Get method request inputs
     *
     * @return Response Json 
     */
    public function proceedOrder(Request $request)
    {
        $user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
            'order_id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
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

        $order = HomeDeliveryOrder::where('id',$request->order_id)->first();

        $assign_status_message = '';

        $order_status = $order->status;


        if(($request->cancel == "True" || $request->cancel == true) && $order_status != 'new' && $order_status != 'delivered'){
            $order->status = $order_status = 'new';

            $order->save();

            $assign_status_message = 'successfully cancelled';
        }
        elseif($order_status == 'assigned'){
            #assigned -> picked_up
            if($order->driver_id != $user->id){
                return response()->json([
                    'status_code' 		=> '0',
                    'status_message' 	=> 'Order already assigned.',
                ]);
            }

            $order->status = $order_status = 'picked_up';

            $order->save();

            $assign_status_message = 'successfully picked up';
        }
        elseif ($order_status == 'picked_up') {
            #picked_up -> delivered
            $rider = User::where('id', $order->customer_id)->first();
            $ride_request = RideRequest::where('id',$order->ride_request)->first();

            $subscription = DriversSubscriptions::where('user_id',$user->id)
                ->whereNotIn('status', ['canceled'])
                ->first();

            //Insert record in Trips table
            $trip = new Trips;
            $trip->user_id = $rider->id;
            $trip->otp = mt_rand(1000, 9999);
            $trip->pickup_latitude = $ride_request->pickup_latitude;
            $trip->pickup_longitude = $ride_request->pickup_longitude;
            $trip->drop_latitude = $ride_request->drop_latitude;
            $trip->drop_longitude = $ride_request->drop_longitude;
            $trip->driver_id = $user->id;
            $trip->car_id = $ride_request->car_id;
            $trip->pickup_location = $ride_request->pickup_location;
            $trip->drop_location = $ride_request->drop_location;
            $trip->request_id = $ride_request->id;
            $trip->trip_path = $ride_request->trip_path;
            $trip->payment_mode = 'Stripe';
            $trip->status = 'Completed';
            $trip->payment_status = 'Completed';
            $trip->currency_code = $rider->currency_code;
            $trip->peak_fare = $ride_request->peak_fare;
            $trip->subtotal_fare = $order->fee;
            $trip->subtotal_fare = $order->fee;
            $trip->arrive_time = $order->created_at;
            $trip->begin_trip = $order->updated_at;

            if(!$subscription){
                return response()->json([
                    'status_code' 		=> '0',
                    'status_message' 	=> 'Sorry, you have no subscription for this action.',
                ]);
            }
            else{
                if($subscription->plan == 2){
                    $trip->driver_or_company_commission = 0.00;
                    $trip->driver_payout = $order->fee;
                }
                else{
                    $commission = $order->fee * 0.1; //10% from non-members
                    $trip->driver_or_company_commission = $commission;
                    $trip->driver_payout = $order->fee - $commission;
                }
            }

            $order->status = $order_status = 'delivered';

            $order->save();

            $assign_status_message = 'successfully delivered';

            //$order = HomeDeliveryOrder::where('id',$request->order_id)->first();
            
            $trip->end_trip = $order->updated_at;

            $trip->save();
           
        }
        elseif ($order_status == 'delivered') {
            return response()->json([
                'status_code' 		=> '0',
                'status_message' 	=> 'Order already delivered.',
            ]);
        }
        else{
            return response()->json([
                'status_code' 		=> '0',
                'status_message' 	=> 'Wrong order transition.',
            ]);
        }

        return response()->json([
            'status_code' 		=> '1',
            'status_message' 	=> "Order with id " . $order->id . ' ' . $assign_status_message,
            'job_status'        => $order_status, 
        ]);
    }


    /**
     * Get new jobs list
     *
     */
    public function get_jobs_list($request, $user_details)
    {  
        $job_array = array();
        $dst = (int)$request->distance;

        $driver_location = DriverLocation::where('user_id',$user_details->id)->first();

        $data = [
            'user_id' => $user_details->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];

        $vehicle = Vehicle::where('user_id', $user_details->id)->first();

        if($vehicle){
            $data['car_id'] = $vehicle->vehicle_id;
        }
        else{
            $data['car_id'] = '1';
        }

        if (!$driver_location) {
            $data['status'] = "Online";
        }

        DriverLocation::updateOrCreate(['user_id' => $user_details->id], $data);

        $driver_location = DriverLocation::where('user_id',$user_details->id)->first();

        $orders = HomeDeliveryOrder::whereIn('delivery_orders.status',['new','expired'])
            ->join('users as rider', function($join) {
                $join->on('rider.id', '=', 'delivery_orders.customer_id');
            })
            ->join('request as ride_request', function($join) {
                $join->on('ride_request.id', '=', 'delivery_orders.ride_request');
            })
            ->select(
                DB::raw('*, ( 6371 * acos( cos( radians(ride_request.pickup_latitude) ) * cos( radians( ' . $driver_location->latitude . ' ) ) * cos(radians( ' . $driver_location->longitude . ' ) - radians(ride_request.pickup_longitude) ) + sin( radians(ride_request.pickup_latitude) ) * sin( radians( ' . $driver_location->latitude . ' ) ) ) ) as distance'),
                'delivery_orders.id as id',
                'delivery_orders.driver_id as driver_id', 
                'delivery_orders.created_at as created_at',
                'delivery_orders.estimate_time as estimate_time',
                'delivery_orders.fee as fee',
                'delivery_orders.status as status',
                'delivery_orders.currency_code as currency_code',
                'ride_request.pickup_location as pick_up_location',
                'ride_request.drop_location as drop_off_location',
                DB::raw('CONCAT(rider.first_name," ",rider.last_name) as customer_name'),
                DB::raw('CONCAT("+",rider.country_code,rider.mobile_number) as customer_phone_number'),
                DB::raw('TIMEDIFF(NOW(),(date_add(delivery_orders.created_at,interval delivery_orders.estimate_time minute))) as time_to_dead'),
            )
            ->having('distance', '<=', $dst)
            ->whereIn('delivery_orders.status',['new','expired'])
            ->orWhere('delivery_orders.driver_id', $user_details->id)
            ->whereNotIn('delivery_orders.status',['delivered','assigned','picked_up'])
            ->orderBy('time_to_dead','desc')->get();

        foreach ($orders as $order){
            $temp_details = array();

            $date_now = \Illuminate\Support\Carbon::now();
            $date_estimate = $order->created_at->addMinutes($order->estimate_time);
            $date_diff = $date_now->diffInMinutes($date_estimate, false);
            // $time1 = (int)($date_diff/60);
            // $time2 = $date_diff%60;
            // $temp_details['estimate_time'] =  $time1 . '.' . $time2 . ' Hours';
            $temp_details['estimate_time'] = $date_diff . ' Min';
            $temp_details['status'] = $order->status;

            if ($date_diff < 0 &&  $order->status != 'assigned'){
                $temp_details['estimate_time'] = 'Expired';
                
            }

            if ($order->status == 'expired'){
                $temp_details['status'] = 'new';
            }

            $temp_details['order_id'] = $order->id;

            $date = new DateTime($order->created_at);
            $temp_details['date'] = $date->format('d M Y | H:i');
            $temp_details['pick_up'] = $order->pick_up_location;
            $temp_details['drop_off'] = $order->drop_off_location;
            if($order->status == 'assigned'){
                $temp_details['customer_name'] = $order->customer_name;
                $temp_details['customer_phone_number'] = $order->customer_phone_number;
            }

            $temp_details['distance'] = (string)round((float)$order->distance, 2) . 'KM';
            $temp_details['fee'] = '$'. $order->fee;
            
            array_push($job_array,$temp_details);
        }
  
        return $job_array;
    }

    /**
     * Get driver jobs list
     *
     */
    public function get_my_jobs_list($request, $user_details)
    {  
        $job_array = array();

        $orders = HomeDeliveryOrder::whereIn('delivery_orders.status',['assigned','picked_up'])
            ->join('users as rider', function($join) {
                $join->on('rider.id', '=', 'delivery_orders.customer_id');
            })
            ->join('request as ride_request', function($join) {
                $join->on('ride_request.id', '=', 'delivery_orders.ride_request');
            })
            ->select(
                'delivery_orders.id as id',
                'delivery_orders.driver_id as driver_id', 
                'delivery_orders.created_at as created_at',
                'delivery_orders.estimate_time as estimate_time',
                'delivery_orders.fee as fee',
                'delivery_orders.status as status',
                'delivery_orders.currency_code as currency_code',
                'ride_request.pickup_location as pick_up_location',
                'ride_request.drop_location as drop_off_location',
                DB::raw('CONCAT(rider.first_name," ",rider.last_name) as customer_name'),
                DB::raw('CONCAT("+",rider.country_code,rider.mobile_number) as customer_phone_number'),
                DB::raw('TIMEDIFF(NOW(),(date_add(delivery_orders.created_at,interval delivery_orders.estimate_time minute))) as time_to_dead'),
            )
            ->where('delivery_orders.driver_id', $user_details->id)
            ->whereNotIn('delivery_orders.status',['delivered','new','expired'])
            ->orderBy('time_to_dead','desc')->get();

        foreach ($orders as $order){
            $temp_details = array();

            $date_now = \Illuminate\Support\Carbon::now();
            $date_estimate = $order->created_at->addMinutes($order->estimate_time);
            $date_diff = $date_now->diffInMinutes($date_estimate, false);
            // $time1 = (int)($date_diff/60);
            // $time2 = $date_diff%60;
            // $temp_details['estimate_time'] =  $time1 . '.' . $time2 . ' Hours';
            $temp_details['estimate_time'] = $date_diff . ' Min';
            $temp_details['status'] = $order->status;

            if ($date_diff < 0){
                $temp_details['estimate_time'] = 'Expired';
                
            }

            $temp_details['order_id'] = $order->id;

            $date = new DateTime($order->created_at);
            $temp_details['date'] = $date->format('d M Y | H:i');
            $temp_details['pick_up_time'] = $date_estimate->format('H:i A');
            $temp_details['pick_up'] = $order->pick_up_location;
            $temp_details['drop_off'] = $order->drop_off_location;
            $temp_details['customer_name'] = $order->customer_name;
            $temp_details['customer_phone_number'] = $order->customer_phone_number;
            $temp_details['order_description'] = 'Test description'; //$order->order_description;
            $temp_details['fee'] = '$'. $order->fee;
            
            array_push($job_array,$temp_details);
        }
  
        return $job_array;
    }
}