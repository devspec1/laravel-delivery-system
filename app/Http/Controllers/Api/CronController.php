<?php

/**
 * Cron Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Cron
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ScheduleRide;
use App\Models\PeakFareDetail;
use App\Models\User;
use App\Models\ReferralUser;
use App\Models\DriverLocation;
use App\Models\Currency;
use App\Models\Trips;
use App\Models\Payment;
use Carbon\Carbon;

class CronController extends Controller
{
	public function __construct()
	{
		$this->request_helper = resolve('App\Http\Helper\RequestHelper');
	}

    /**
	 * Cron request to cars for scheduled ride
	 * @param
	 * @return Response Json
	 */
	public function requestCars()
	{
		// before 5 min from schedule time
		$ride = ScheduleRide::where('status','Pending')->get();

		if($ride->count() == 0) {
			return '';			
		}

		foreach ($ride as $request_val) {   
			if($request_val->timezone) {
				date_default_timezone_set($request_val->timezone);
			}
		
			$current_date = date('Y-m-d');				
			$current_time = date('H:i');
            if(strtotime($request_val->schedule_date) == strtotime($current_date) && strtotime($request_val->schedule_time) == (strtotime($current_time) + 300)){
				$additional_fare = "";
				$peak_price = 0;

				if(isset($request_val->peak_id)!='') {
				   $fare = PeakFareDetail::find($request_val->peak_id);
					if($fare) {
						$peak_price = $fare->price; 
						$additional_fare = "Peak";
					}
				}

	            $schedule_id = $request_val->id;
				$payment_mode = $request_val->payment_method;
				$is_wallet = $request_val->is_wallet;

				$data = [ 
					'rider_id' =>$request_val->user_id,
					'pickup_latitude' => $request_val->pickup_latitude,
					'pickup_longitude' => $request_val->pickup_longitude,
					'drop_latitude' => $request_val->drop_latitude,
					'drop_longitude' => $request_val->drop_longitude,
					'user_type' => 'rider',
					'car_id' => $request_val->car_id,
					'driver_group_id' => null,
					'pickup_location' => $request_val->pickup_location,
					'drop_location' => $request_val->drop_location,
					'payment_method' => $payment_mode,
					'is_wallet' => $is_wallet,
					'timezone' => $request_val->timezone,
					'schedule_id' => $schedule_id,
					'additional_fare'  =>$additional_fare,
					'location_id' => $request_val->location_id,
					'peak_price'  => $peak_price,
					'booking_type'  => $request_val->booking_type, 
					'driver_id'  => $request_val->driver_id, 
				];
					
				if ($request_val->driver_id==0) {
					$car_details = $this->request_helper->find_driver($data);
				}
				else {
					$car_details = $this->request_helper->trip_assign($data);
				}
            }
            elseif(strtotime($request_val->schedule_date.' '.$request_val->schedule_time) == strtotime(date('Y-m-d H:i')) + 1800) {
		        $rider = User::find($request_val->user_id);
            	if ($request_val->booking_type=='Manual Booking' && $request_val->driver_id!=0) {
	            	$driver_details = User::find($request_val->driver_id);
		            $push_data['push_title'] = __('messages.api.schedule_remainder');
		            $push_data['data'] = array(
		                'manual_booking_trip_reminder' => array(
		                	'date' 	=> $request_val->schedule_date,
		                	'time'	=> $request_val->schedule_time,
		                	'pickup_location' 		=> $request_val->pickup_location,
		                	'pickup_latitude' 		=> $request_val->pickup_latitude,
		                	'pickup_longitude' 		=> $request_val->pickup_longitude,
		                	'rider_first_name'		=> $rider->first_name,
		                	'rider_last_name'		=> $rider->last_name,
		                	'rider_mobile_number'	=> $rider->mobile_number,
		                	'rider_country_code'	=> $rider->country_code
		                )
		            );

		            $this->request_helper->SendPushNotification($rider,$push_data);

			        $text = trans('messages.trip_booked_driver_remainder',['date'=>$request_val->schedule_date.' ' .$request_val->schedule_time,'pickup_location'=>$request_val->pickup_location,'drop_location'=>$request_val->drop_location]);
			        
			        $to = $driver_details->phone_number;
			        $this->request_helper->send_message($to,$text);
			    }

			    //booking message to user
	            $text = trans('messages.trip_booked_user_remainder',['date'=>$request_val->schedule_date.' ' .$request_val->schedule_time]);
	            if ($request_val->booking_type=='Manual Booking' && $request_val->driver_id!=0) {
	            	$driver = User::find($request_val->driver_id);
	                $text = $text.trans('messages.trip_booked_driver_detail',['first_name'=>$driver->first_name,'phone_number'=>$driver->mobile_number]);
	                $text = $text.trans('messages.trip_booked_vehicle_detail',['name'=>$driver->driver_documents->vehicle_name,'number'=>$driver->driver_documents->vehicle_number]);
	            }
	            $to = $rider->phone_number;
	            $this->request_helper->send_message($to,$text);
            }
            else {
				if(strtotime($request_val->schedule_date) < strtotime($current_date)) {
                    $update_ride = ScheduleRide::find($request_val->id);
                    $update_ride->status ='Cancelled';
                    $update_ride->save();
				}
            }
        }
	}

	public function updateReferralStatus()
	{
		ReferralUser::where('end_date','<',date('Y-m-d'))->where('payment_status','Pending')->update(['payment_status' => 'Expired']);
		return response()->json(['status' => true, 'status_message' => 'updated successfully']);
	}

	public function updateOfflineUsers()
	{
		$offline_hours = site_settings('offline_hours');
		$minimumTimestamp = Carbon::now()->subHours($offline_hours);

		\DB::table('driver_location')->where('status','Online')->where('updated_at','<',$minimumTimestamp)->update(['status' => 'Offline']);
		return response()->json(['status' => true, 'status_message' => 'updated successfully']);
	}

	public function updateCurrency()
	{
		$return_data = array();
		$result = Currency::all();
		$result->each(function($row) use(&$return_data) {
			$rate = 1;
			try {
		        if($row->code != 'USD') {
		            $rate = \Swap::latest('USD/'.$row->code);
		            $rate = $rate->getValue();
		        }
		        Currency::where('code',$row->code)->update(['rate' => $rate]);
		        $return_data[] = ['status' => true,'status_message' => 'updated successfully','target' => $row->code,'value' => $rate];
			}
			catch(\Exception $e) {
				$return_data[] = ['status' => false,'status_message' => $e->getMessage(),'target' => $row->code];
			}
		});

		return response()->json($return_data);
	}

	public function updatePaypalPayouts()
	{
		$pending_payments = Payment::where('driver_payout_status','Processing')->orWhere('admin_payout_status','Processing')->get();
		if($pending_payments->count() == 0) {
			return response()->json(['status' => false, 'status_message' => 'No Pending Payouts found']);
		}

		$paypal_payout = resolve("App\Services\Payouts\PaypalPayout");
		$pending_payments->each(function($pending_payment) use($paypal_payout) {
			$batch_id = $pending_payment->correlation_id;
			$payment_data = $paypal_payout->fetchPayoutViaBatchId($batch_id);
			if($payment_data['status']) {
				$payout_data = $paypal_payout->getPayoutStatus($payment_data['data']);
				$trip = Trips::find($pending_payment->trip_id);

				if($payout_data['status']) {
					if($payout_data['payout_status'] == 'SUCCESS') {
						if($trip->driver->company_id == '1') {
							$pending_payment->driver_payout_status = "Paid";
							$pending_payment->driver_transaction_id = $payout_data['transaction_id'];
						}
						else {
							$pending_payment->admin_payout_status = "Paid";
							$pending_payment->admin_transaction_id = $payout_data['transaction_id'];
						}
					}

					if(in_array($payout_data['payout_status'], ['FAILED','RETURNED','BLOCKED'])) {
						if($trip->driver->company_id == '1') {
							$pending_payment->driver_payout_status = "Pending";
						}
						else {
							$pending_payment->admin_payout_status = "Pending";
						}
					}
					
					$pending_payment->save();
				}
			}
		});
		return response()->json(['status' => true, 'status_message' => 'updated successfully']);
	}
}