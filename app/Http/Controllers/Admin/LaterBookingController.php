<?php

/**
 * Later Booking Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Later Booking
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\LaterBookingDataTable;
use App\Models\ScheduleRide;
use App\Models\ScheduleCancel;
use App\Models\PeakFareDetail;
use App\Models\User;
use App\Models\CancelReason;
use App\Models\Request as RideRequest;
use App\Http\Helper\RequestHelper;

class LaterBookingController extends Controller
{
    protected $request_helper;

    public function __construct(RequestHelper $request)
    {
        $this->request_helper = $request;
    }

    /**
     * Load Datatable for LaterBooking
     *
     * @return view file
     */
    public function index(LaterBookingDataTable $dataTable)
    {
        $login_user = (LOGIN_USER_TYPE == 'admin') ? 'Admin' : 'Company';

        $data['cancel_reasons'] = CancelReason::where('cancelled_by',$login_user)->where('status','Active')->get();
        return $dataTable->render('admin.later_booking',$data);
    }

    public function cancel(Request $request)
    {
        $schedule = ScheduleRide::find($request->id);
        $schedule->status='Cancelled';
        $schedule->save();

        $cancel = new ScheduleCancel;
        $cancel->schedule_ride_id = $request->id;
        $cancel->cancel_reason = strip_tags($request->reason);
        $cancel->cancel_reason_id = $request->reason_id;
        $cancel->cancel_by = (LOGIN_USER_TYPE == 'admin')?'Admin':'Company';
        $cancel->save();

        if ($schedule->driver_id != 0) {
            $driver_details = @User::where('id', $schedule->driver_id)->first();
            $rider = User::find($schedule->user_id);

            $push_data['push_title'] = __('messages.api.schedule_cancelled');
            $push_data['data'] = array(
                'manual_booking_trip_canceled_info' => array(
                    'date'      => $schedule->schedule_date,
                    'time'      => $schedule->schedule_time,
                    'pickup_location'    => $schedule->pickup_location,
                    'pickup_latitude'    => $schedule->pickup_latitude,
                    'pickup_longitude'   => $schedule->pickup_longitude,
                    'rider_first_name'   => $rider->first_name,
                    'rider_last_name'    => $rider->last_name,
                    'rider_mobile_number'=> $rider->mobile_number,
                    'rider_country_code' => $rider->country_code
                )
            );

            $this->request_helper->SendPushNotification($rider,$push_data);
        }

        return [
            'status_code'   => 1,
            'cancel_reason' => CancelReason::where('id',$request->reason_id)->first()->reason,
            'reason' => $cancel->cancel_reason,
        ];
    }

    public function immediate_request(Request $request)
    {
        $schedule = ScheduleRide::where('status','Car Not Found')->where('id',$request->id)->first();
        if ($schedule == null) {
            return response()->json([
                'status_code' => 0
            ]);
        }

        date_default_timezone_set($schedule->timezone);
        $current_date = date('Y-m-d');              
        $current_time = date('H:i');
        $schedule->save();

        $additional_fare = "";
        $peak_price = 0;
        if(isset($schedule->peak_id)!='') {
            $fare = PeakFareDetail::find($schedule->peak_id);
            if($fare) {
                $peak_price = $fare->price; 
                $additional_fare = "Peak";
            }
        }
        $data = [ 
            'rider_id'          => $schedule->user_id,
            'pickup_latitude'   => $schedule->pickup_latitude,
            'pickup_longitude'  => $schedule->pickup_longitude,
            'drop_latitude'     => $schedule->drop_latitude,
            'drop_longitude'    => $schedule->drop_longitude,
            'user_type'         => 'rider',
            'car_id'            => $schedule->car_id,
            'driver_group_id'   => null,
            'pickup_location'   => $schedule->pickup_location,
            'drop_location'     => $schedule->drop_location,
            'payment_method'    => $schedule->payment_method,
            'is_wallet'         => $schedule->is_wallet,
            'timezone'          => $schedule->timezone,
            'schedule_id'       => $schedule->id,
            'additional_fare'   => $additional_fare,
            'location_id'       => $schedule->location_id,
            'peak_price'        => $peak_price,
            'booking_type'      => $schedule->booking_type, 
            'driver_id'         => 0, 
        ];
        $car_details = $this->request_helper->find_driver($data);

        $schedule = ScheduleRide::find($request->id);

        $accepted_request = RideRequest::where('schedule_id',$schedule->id)->first();

        $status = optional($accepted_request->accepted_trips)->status ?? $schedule->status;

        return response()->json([
            'status_code' => 1,
            'status_message' => $status
        ]);
    }
}
