<?php

/**
 * Invoice Helper
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Invoice
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */
namespace App\Http\Helper;

use App\Http\Helper\RequestHelper;
use App\Models\User;
use App\Models\Trips;
use App\Models\Wallet;
use App\Models\DriverOweAmount;
use App\Repositories\DriverOweAmountRepository;
use App\Models\Company;
use App\Models\ScheduleRide;
use App\Models\UsersPromoCode;
use App\Models\ManageFare;
use App\Models\Fees;
use App\Models\ReferralUser;
use App\Models\Currency;
use App\Models\Request as RideRequest;
use App\Http\Start\Helpers;
use DateTime;
use DB;
use Str;

class InvoiceHelper
{
	public function __construct(DriverOweAmountRepository $driver_owe_amt_repository)
	{
		$this->request_helper = new RequestHelper();
		$this->helper = new Helpers();
		$this->driver_owe_amt_repository = $driver_owe_amt_repository;
	}

	protected function checkIsWallet($payment_mode)
	{
		return Str::contains($payment_mode,'Wallet');
	}

	protected function checkIsCashTrip($payment_mode)
	{
		return Str::contains($payment_mode,'Cash');
	}

	public function calculation($data)
	{
		$save = $data['save_to_trip_table'];

		$trips = Trips::where('id', $data['trip_id'])->first();
		$user = User::where('id', $data['user_id'])->first();

		//Time calculation
		$arrive_time = new DateTime($trips->arrive_time);
		$begin_time = new DateTime($trips->begin_trip);
		$end_time = new DateTime($trips->end_trip);
		$timeDiff = date_diff($begin_time, $end_time);

		$trip_hours = $timeDiff->format("%H");
		$trip_minute = $timeDiff->format("%I");

		$request_details = RideRequest::where('id',$trips->request_id)->first();

		$fare_details = ManageFare::where('location_id',$request_details->location_id)->where('vehicle_id',$trips->car_id)->first();

		// Waiting charge calculation
		$waiting_charge = 0;
		$waitingDiff = date_diff($arrive_time, $begin_time);
		$waitingMin = $waitingDiff->format("%I");
		if($waitingMin > $fare_details->waiting_time) {
			$waitingMin = $waitingDiff->format("%I") - $fare_details->waiting_time;
			$waiting_charge = $waitingMin * $fare_details->waiting_charge;
		}

		//total fare calculation
		$total_minute = ($trip_hours * 60) + $trip_minute;
		$trip_time_fare = number_format(($fare_details->per_min * $total_minute), 2, '.', '');
		$trip_km_fare = number_format(($fare_details->per_km * $trips->total_km), 2, '.', '');
		$schedule_fare_amount = 0;

		if($request_details->schedule_id != '') {
			if($request_details->schedule_ride->booking_type != "Manual Booking") {
				$schedule_fare_amount = number_format($fare_details->schedule_fare, 2, '.', '');
			}
		}

		/* Standard fare */
		$trip_base_fare = $fare_details->base_fare;
		$driver_total_fare = $trip_total_fare = $subtotal_fare = number_format(($trip_base_fare + $trip_km_fare + $trip_time_fare), 2, '.', '');

		/* minimum fare */
		if($driver_total_fare < $fare_details->min_fare) {
			$trip_base_fare =  $fare_details->min_fare - ($trip_km_fare + $trip_time_fare);
			$driver_total_fare = $trip_total_fare =	$subtotal_fare = number_format(($trip_base_fare + $trip_km_fare + $trip_time_fare), 2, '.', '');
		}

		/* Peak fare */
		$peak_amount = 0;
		$driver_peak_amount = 0;

		if($trips->peak_fare != 0) {
			$trip_total_fare = $subtotal_fare * $trips->peak_fare;
			$peak_amount = $trip_total_fare - $subtotal_fare;

			$driver_per = Fees::find(2)->value;
		    $driver_peak_amount = number_format(($driver_per / 100) *  $peak_amount , 2, '.', '');
		    $driver_total_fare = $subtotal_fare + $driver_peak_amount;
		}

		//for driver payout variable - total_trip_fare_for
		// access fee calculation

		$percentage = Fees::find(1)->value;

		$access_fee = number_format(($percentage / 100) * $trip_total_fare, 2, '.', '');

		$owe_amount = 0;
		$remaining_wallet = 0;
		$applied_wallet = 0;
		$promo_amount = 0;

		$trips = Trips::find($data['trip_id']);

		if($trips->is_calculation == 0) {

			$total_fare = $trip_total_fare + $access_fee + $schedule_fare_amount;

			$driver_payout = $driver_total_fare;

			$company_id = User::find($trips->driver_id);
			$company_id = @$company_id->company_id;

			if ($company_id == null || $company_id == 1) {
				$driver_service_fee_percentage = Fees::find(3)->value;
				$driver_or_company_commission = number_format(($driver_service_fee_percentage / 100) * $driver_total_fare, 2, '.', '');
			}
			else {
				$company_commission_percentage = Company::find($company_id)->company_commission;
				$driver_or_company_commission = number_format(($company_commission_percentage / 100) * $driver_total_fare, 2, '.', '');
			}
			
			$driver_total_fare = $driver_total_fare + $trips->tips + $trips->toll_fee + $waiting_charge;
			$driver_payout = $driver_total_fare-$driver_or_company_commission;
			$total_fare = $total_fare + $trips->tips + $trips->toll_fee + $waiting_charge;

			//Apply promo code if promocode is available
			$promo_codes = UsersPromoCode::whereUserId($trips->user_id)->whereTripId(0)->with('promo_code_many')->whereHas('promo_code_many')->orderBy('created_at', 'asc')->first();
			if ($promo_codes) {
				if ($save == 1) {
					UsersPromoCode::whereId($promo_codes->id)->update(['trip_id' => $data['trip_id']]);
				}
				$promo_amount = $promo_codes->promo_code_many[0]->amount;
				if($promo_amount >= $total_fare) {
					$total_fare = '0';
				}
				else {
					$total_fare = $total_fare - $promo_amount;
				}
			}

			// Wallet Amount
			$wallet_amount = 0;
			$wallet = Wallet::whereUserId($trips->user_id)->first();

			if($wallet) {
				$wallet_amount = $wallet->original_amount;
			}

			if($this->checkIsWallet($trips->payment_mode)) {
				if ($total_fare >= $wallet_amount) {
					$amount = $total_fare - $wallet_amount;
					$remaining_wallet = 0;
					$applied_wallet = $wallet_amount;

					if ($trips->payment_mode == 'Cash & Wallet') {
						$owe_amount = $amount;
						if ($owe_amount >=($driver_total_fare-$driver_or_company_commission)) {  // if owe amount is more than driver payout then driver payout is zero
							$owe_amount = $owe_amount-($driver_total_fare-$driver_or_company_commission);
							$driver_payout = 0;
						}
						else { // if owe amount is less than driver payout condition
							$owe_amount = $owe_amount;
							$driver_payout = ($driver_total_fare-$driver_or_company_commission) - $owe_amount;
							$owe_amount = 0;
						}
					}
				}
				else if ($total_fare < $wallet_amount) {
					$remaining_wallet = $wallet_amount - $total_fare;
					$amount = 0;
					$applied_wallet = $total_fare;
				}

				if ($save == 1) {
					$this->referralUpdate($trips->user_id,$applied_wallet,$user->currency->code);
					Wallet::whereUserId($trips->user_id)->update(['amount' => $remaining_wallet, 'currency_code' => $user->currency->code]);
				}
				//owe amount deduction for driver 
			}
			elseif ($trips->payment_mode == 'Cash') {
				if($promo_amount > 0) {
					$trips->payment_mode = 'Cash & Wallet';
				}
				// Check total Fare less than commission for promo applied

				if($total_fare < $driver_payout) {
					$owe_amount = 0;
					$driver_payout = abs($total_fare - $driver_payout);
				}
				else {
					$owe_amount = abs($total_fare - $driver_payout);
					$driver_payout = 0;
				}
				$amount = $total_fare;
			}
			else {
				$amount = $total_fare;
			}

            if($trips->payment_mode != 'Cash' && $trips->payment_mode != 'Cash & Wallet') {
		       $driver_payout_result = $this->oweAmount($driver_payout,$trips->driver_id,$trips->id,$save,$user->currency->code);
		       $driver_payout = $driver_payout_result['driver_payout'];
		    }
		    else {
		    	$converted_owe_amount = $this->helper->currency_convert($user->currency->code,$trips->getOriginal('currency_code'),$owe_amount);
		    	if ($save == 1) {
		    		Trips::where('id', $data['trip_id'])->update(['owe_amount' => $converted_owe_amount]);
		    	}
		    	$driver_payout_result = $this->oweAmount($driver_payout,$trips->driver_id,$trips->id,$save,$user->currency->code);
		    	if($trips->payment_mode == 'Cash & Wallet') {
		       		$driver_payout = ($driver_payout_result['driver_payout'] > 0 ) ? $driver_payout_result['driver_payout'] : 0;
		    	}
		    }

		    $trips->total_time = $total_minute;
		    $trips->time_fare = $trip_time_fare;
		    $trips->distance_fare = $trip_km_fare;
		    $trips->base_fare = $trip_base_fare;
		    $trips->subtotal_fare = $subtotal_fare;
		    $trips->total_fare = $amount;
		    $trips->driver_payout = $driver_payout;
		    $trips->access_fee = $access_fee;
		    $trips->owe_amount = $owe_amount;
		    $trips->wallet_amount = $applied_wallet;
		    $trips->promo_amount = $promo_amount;
		    $trips->tips = $trips->tips;
		    $trips->toll_fee = $trips->toll_fee;
		    $trips->currency_code = $user->currency->code;
		    $trips->schedule_fare = $schedule_fare_amount;
		    $trips->peak_amount = $peak_amount;
		    $trips->waiting_charge = $waiting_charge;
		    $trips->driver_peak_amount = $driver_peak_amount;
		    $trips->driver_or_company_commission = $driver_or_company_commission;
		    $trips->applied_owe_amount = $driver_payout_result['applied'];

		    if ($save == 1) {
		    	$trips->is_calculation = 1;
		    	if ($amount <= 0) {  //If toatal amount taken from wallet then trip status changed to completed
		    		$trips->status = 'Completed';
		    	}
		    	$trips->save();
			}

			//Send payment detail as SMS to manual booking user
			$schedule_ride = ScheduleRide::find($trips->ride_request->schedule_id);
			if(isset($schedule_ride) && $schedule_ride->booking_type == 'Manual Booking') {
				$push_title = __('messages.sms_payment_detail');
		        $text 		= __('messages.api.trip_total_fare',['total_fare' => $trips->total_fare, 'currency' => $trips->currency_code]);

		        $push_data['push_title'] = $push_title;
		        $push_data['data'] = array(
		            'custom_message' => array(
		                'title' => $push_title,
		                'message_data' => $text,
		            )
		        );

		        $text = $push_title.$text;

		        $this->request_helper->checkAndSendMessage($trips->users,$text,$push_data);
        	}
		}

		return $trips;
	}

	public function getWebInvoice($trip)
	{
		$payment_mode = array(
            'key'   => __('messages.dashboard.payment_mode'),
            'value' => $trip->payment_mode,
        );
        $invoice[] = formatInvoiceItem($payment_mode);

        $user_data = [
            'user_id' => @Auth()->user()->id,
            'user_type' => strtolower(@Auth()->user()->user_type),
        ];
        $symbol = Currency::original_symbol(session('currency'));
        $symbol = html_entity_decode($symbol);

        $invoice = array_merge($invoice,$this->formatInvoice($trip,$user_data,true));
        /*if($user_data['user_type'] == 'driver') {
        	$total_fare = array(
				'key' 	=> __('messages.total_trip_fare'),
				'value' => $symbol.$trip->total_trip_fare,
				'bar' 	=> 1,
				'colour'=> 'black',
			);
			$invoice[] = formatInvoiceItem($total_fare);
        }*/
        
        return $invoice;
	}

	public function formatInvoice($trips,$data,$is_web = false)
	{
		$user_type = strtolower($data['user_type']);
		$user = User::where('id', $data['user_id'])->first();
		$symbol = html_entity_decode($user->currency->symbol);
		if($is_web) {
			$symbol = Currency::original_symbol(session('currency'));
		}

		$total_trip_amount = number_format($trips->subtotal_fare + $trips->peak_amount + $trips->access_fee + $trips->schedule_fare + $trips->tips + $trips->toll_fee + $trips->waiting_charge,2,'.','');
		$peak_subtotal_fare = number_format($trips->peak_amount + $trips->subtotal_fare,2,'.','');

		$invoice = array();

		if($trips->driver->company_id != 1 && $user_type != 'rider') {
			$total_amount = $trips->company_driver_earnings;
			if($this->checkIsCashTrip($trips->payment_mode) && $trips->total_fare > 0) {
				$total_amount = $trips->total_fare;
			}
			$item = array(
				'key' => __('messages.api.total_fare'),
				'value' => $symbol.$total_amount,
			);
			$invoice[] = formatInvoiceItem($item);
			return $invoice;
		}

		if($trips->base_fare != 0) {
			$item = array(
				'key' => __('messages.base_fare'),
				'value' => $symbol . $trips->base_fare,
			);
			$invoice[] = formatInvoiceItem($item);
		}

		if($trips->time_fare !=0) {
	   		$item = array(
				'key' => __('messages.time_fare'),
				'value' => $symbol . $trips->time_fare,
			);
			$invoice[] = formatInvoiceItem($item);
	   	}

		if($trips->distance_fare != 0) {
			$item = array(
				'key' => __('messages.distance_fare'),
				'value' => $symbol . $trips->distance_fare,
			);
			$invoice[] = formatInvoiceItem($item);
		}	   	

	   	if($user_type == 'rider' && $trips->schedule_fare != 0) {
	   		$item = array(
				'key' => __('messages.schedule_fare'),
				'value' => $symbol . $trips->schedule_fare,
			);
			$invoice[] = formatInvoiceItem($item);
	   	}

		if($trips->peak_fare != 0) {
			$item = array(
				'key' => __('messages.normal_fare'),
				'value' => $symbol . $trips->subtotal_fare,
				'bar'	=> 1,
				'colour'=> 'black',
			);
			$invoice[] = formatInvoiceItem($item);

			if($user_type == 'rider') {
				$item = array(
					'key' => trans('messages.peak_time_fare').'  x'.($trips->peak_fare + 0),
					'value' => $symbol.$trips->peak_amount,
				);

				$invoice[] = formatInvoiceItem($item);

				$item = array(
					'key' 	=> __('messages.peak_subtotal_fare'),
					'value' => $symbol.$peak_subtotal_fare,
					'bar'	=> 1,
					'colour'=> 'black'
				);
				$invoice[] = formatInvoiceItem($item);
			}
			else {
				$item = array(
					'key' => __('messages.peak_time_fare'),
					'value' => $symbol.$trips->driver_peak_amount,
				);
				$invoice[] = formatInvoiceItem($item);

				$item = array(
					'key' 	=> __('messages.peak_subtotal_fare'),
					'value' => $symbol.($trips->driver_peak_amount + $trips->subtotal_fare),
					'bar'	=> 1,
					'colour'=> 'black'
				);
				$invoice[] = formatInvoiceItem($item);
			}
		}
		else {
			$item = array(
				'key' 	=> __('messages.subtotal_fare'),
				'value' => $symbol.$trips->subtotal_fare,
				'bar'	=> 1,
				'colour'=> 'black'
			);
			$invoice[] = formatInvoiceItem($item);
		}

		if($user_type == 'rider') {
			if($trips->access_fee != 0) {
				$item = array(
					'key' => __('messages.access_fee'),
					'value' => $symbol.$trips->access_fee,
				);
				$invoice[] = formatInvoiceItem($item);
			}
		}
		else {
			if($trips->driver_or_company_commission > 0) {
				$item = array(
					'key' => __('messages.service_fee'),
					'value' => '-'.$symbol.$trips->driver_or_company_commission,
				);
				$invoice[] = formatInvoiceItem($item);
			}
		}

		$is_first = 1;
		if($trips->waiting_charge != 0) {
	 		$item = array(
				'key' => __('messages.waiting_charge'),
				'value' => $symbol.$trips->waiting_charge,
				'bar'	=> $is_first,
			);
			$invoice[] = formatInvoiceItem($item);
			$is_first = 0;
	 	}

		if($trips->toll_reason_id) {
			$item = array(
				'key' => $trips->toll_fee_reason,
				'value' => $symbol . $trips->toll_fee,
				'comment' => $trips->trip_toll_fee_reason,
				'bar'	=> $is_first,
			);
			$invoice[] = formatInvoiceItem($item);
			$is_first = 0;
		}

	 	if($trips->tips != 0) {
			$item = array(
				'key' 	=> __('messages.tips'),
				'value' => $symbol.$trips->tips,
				'bar'	=> $is_first,
			);
			$invoice[] = formatInvoiceItem($item);
			$is_first = 0;
		}

		if($user_type == 'rider') {
			$item = array(
				'key' 	=> __('messages.total_trip_fare'),
				'value' => $symbol.$total_trip_amount,
				'bar' 	=> 1,
				'colour'=> 'black',
			);
			$invoice[] = formatInvoiceItem($item);

			if($trips->promo_amount != 0) {
				$item = array(
					'key' => __('messages.promo_amount'),
					'value' => '-'.$symbol.$trips->promo_amount,
				);
				$invoice[] = formatInvoiceItem($item);
			}

			if($trips->wallet_amount != 0) {
				$item = array(
					'key' => __('messages.wallet_amount'),
					'value' => '-'.$symbol.$trips->wallet_amount,
				);
				$invoice[] = formatInvoiceItem($item);
			}

		    if($trips->promo_amount != 0 || $trips->wallet_amount != 0) {
		    	$item = array(
					'key' 	=> __('messages.payable_amount'),
					'value' => $symbol.$trips->total_fare,
					'color'	=> 'green',
				);
				$invoice[] = formatInvoiceItem($item);    	
		    }
		}
		else {
			$is_first = 1;
			if($trips->owe_amount != 0 || in_array($trips->payment_mode,['Cash','Cash & Wallet'])) {
				if($trips->total_fare != 0) {
					$item = array(
						'key' 	=> __('messages.cash_collected'),
						'value' => $symbol.$trips->total_fare,
						'bar'	=> $is_first,
						'colour'=> 'green',
					);
					$invoice[] = formatInvoiceItem($item);
					$is_first = 0;
				}

		       	if($trips->owe_amount > 0) {
		       		$item = array(
						'key' 	=> __('messages.owe_amount'),
						'value' => '-'.$symbol.$trips->owe_amount,
						'bar' 	=> $is_first,
					);
					$invoice[] = formatInvoiceItem($item);
		       	}
			}

			$item = array(
				'key' => __('messages.api.driver_earnings'),
				'value' => $symbol.$trips->company_driver_earnings,
				'bar'	=> '1',
			);
			$invoice[] = formatInvoiceItem($item);

			$is_first = 1;
			if($trips->applied_owe_amount != 0) {
				$item = array(
					'key' 	=> __('messages.applied_owe_amount'),
					'value' => '-'.$symbol.$trips->applied_owe_amount,
					'bar'	=> $is_first,
				);
				$invoice[] = formatInvoiceItem($item);
				$is_first = 0;
			}

			$item = array(
				'key' => __('messages.driver_payout'),
				'value' => $symbol.$trips->driver_payout,
				'bar'	=> $is_first,
			);
			// $invoice[] = formatInvoiceItem($item);
		}

		return $invoice;
	}

	public function getInvoice($trips,$data)
	{
		$invoice = $this->formatInvoice($trips,$data);
		$user_promo_details = $this->getUserPromoDetails($trips->user_id);

		$payment_details = [
			'currency_code' 	=> $trips->currency_code ?? '',
			'total_time' 		=> $trips->total_time ?? '0.00',
			'pickup_location' 	=> $trips->pickup_location ?? '',
			'drop_location' 	=> $trips->drop_location ?? '',
			'driver_payout' 	=> $trips->driver_payout ?? '0.00',
			'payment_status' 	=> $trips->payment_status ?? '',
			'payment_mode' 		=> $trips->payment_mode ?? '',
			'owe_amount' 		=> $trips->owe_amount ?? '0.00',
			'applied_owe_amount'=> $trips->applied_owe_amount ?? '0.00',
			'remaining_owe_amount' => $trips->remaining_owe_amount ?? '0.00',
			'trips_status' 		=> $trips->status,
			'driver_paypal_id'  => $trips->driver->payout_id,
	        'total_fare'		=> $trips->total_fare,
		];

		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> "Success",
			'total_time' 		=> $trips->total_time,
			'pickup_location' 	=> $trips->pickup_location,
			'drop_location' 	=> $trips->drop_location,
			'payment_mode' 		=> $trips->payment_mode,
			'payment_status' 	=> $trips->payment_status,
			'applied_owe_amount'=> $trips->applied_owe_amount,
			'remaining_owe_amount' => $trips->remaining_owe_amount,
			'is_calculation' 	=> $trips->is_calculation,
			'invoice' 			=> $invoice,
			'payment_details' 	=> $payment_details,
			'currency_code' 	=> $trips->currency_code,
			'total_fare' 		=> $trips->total_fare,
			'driver_payout' 	=> $trips->driver_payout,
			'promo_amount' 		=> $trips->promo_amount,
			'promo_details' 	=> $user_promo_details,
			'trip_status' 		=> $trips->status,
			'trip_id' 			=> $trips->id,
			'driver_image' 		=> $trips->driver_thumb_image,
			'driver_name' 		=> $trips->driver->first_name,
			'rider_image' 		=> @$trips->rider_profile_picture,
			'rider_name' 		=> @$trips->users->first_name,
			'paypal_app_id'		=> PAYPAL_ID,
			'paypal_mode'		=> PAYPAL_MODE,
		]);
	}

	public function oweAmount($driver_payout, $driver_id, $trip_id,$save=0,$currency_code)
	{ 
		$current_trip = Trips::where('id', $trip_id)->first();
	  	// deduction
	  	$driver_owe_amount = DriverOweAmount::where('user_id',$driver_id)->first();
	  	$owe_amount = $remaining_owe_amount = $driver_owe_amount->amount+$current_trip->owe_amount;
	  	$applied_owe_amount = 0;

	   	if($owe_amount != 0) {
			/*
			// Hided Applied Owe amount concept removed
			$remaining_owe_amount = 0;
			if($owe_amount >= $driver_payout) {
				$applied_owe_amount = $driver_payout;
				$driver_payout = 0;
				$remaining_owe_amount  = $owe_amount - $driver_payout;
			}
			else if($owe_amount < $driver_payout) {
				$applied_owe_amount = $driver_payout - ($driver_payout-$owe_amount);
				$driver_payout = $driver_payout - $owe_amount;
				$remaining_owe_amount  = 0;
			}

			if ($save == 1) {
		   		Trips::where('id', $trip_id)->update(['remaining_owe_amount' => $remaining_owe_amount, 'applied_owe_amount' => $applied_owe_amount]);

		   		$this->driver_owe_amt_repository->update($driver_id,$remaining_owe_amount,$currency_code);
		   	}*/

		   	if ($save == 1) {
		   		$this->driver_owe_amt_repository->update($driver_id,$remaining_owe_amount,$currency_code);
		   	}

		   	return array('remaining' => $remaining_owe_amount, 'applied' => $applied_owe_amount, 'driver_payout' => $driver_payout);
	   	}

	    return array('remaining' => 0, 'applied' => 0, 'driver_payout' => $driver_payout);
	}

	public function referralUpdate($user_id,$applied_amount,$from_currency_code)
	{
		$referrel_users = ReferralUser::where('user_id',$user_id)->where('payment_status','Completed')->where('pending_amount','>',0)->get();

		foreach ($referrel_users as $referrel_user) {
			if ($referrel_user->pending_amount <= $applied_amount) {
				$applied_amount = $applied_amount-$referrel_user->pending_amount;
				$referrel_user->pending_amount = 0;
				$referrel_user->save();
			}
			else {
				$referrel_user->pending_amount = $this->helper->currency_convert($from_currency_code,$referrel_user->getOriginal('currency_code'),($referrel_user->pending_amount-$applied_amount));
				$referrel_user->save();

				$applied_amount=0;
			}
		}
	}

	public function getUserPromoDetails($user_id)
	{
		$users_promo_codes = UsersPromoCode::whereUserId($user_id)->whereTripId(0)->with('promo_code')->whereHas('promo_code')->get();

		$promo_details = $users_promo_codes->map(function($users_promo) {
			$promo_code = $users_promo->promo_code;
			return [
				'id' 			=> $promo_code->id,
				'code' 			=> $promo_code->code,
				'amount' 		=> $promo_code->amount,
				'expire_date' 	=> $promo_code->expire_date_dmy,
			];
		});

		return $promo_details;
	}
}