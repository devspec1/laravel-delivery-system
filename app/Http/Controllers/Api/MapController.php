<?php

/**
 * Heat Map Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    HeatMap
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Request as RideRequest;
use App\Models\Trips;
use Carbon\Carbon;
use JWTAuth;

class MapController extends Controller
{
    /**
     * Send data for heat map
     *
     * @return Json
     */
    public function heat_map(Request $request)
    {
        if (!$request->timezone) {
            return response()->json([
                'status_code' => '0' , 
                'status_message' => 'timezone is required',
            ]); 
        }
        // $timezone = isValidTimezone($request->timezone) ? $request->timezone : 'UTC';
        $timezone = $request->timezone;

        $user_details = JWTAuth::parseToken()->authenticate();
        
        $heat_map_hours = site_settings('heat_map_hours');
        $date_obj = Carbon::now()->setTimezone($timezone);

        $current_date = $date_obj->format('Y-m-d');
        $current_time = $date_obj->format('Y-m-d H:i:s');
        $prev_time = $date_obj->subHours($heat_map_hours)->format('Y-m-d H:i:s');  
        $ride_requests = RideRequest::whereBetween('created_at', array($prev_time, $current_time))->groupBy('group_id')->orderByDesc('id')->get();

        $heat_map_data = $ride_requests->map(function($requests) {
            return [
                'id' =>  $requests->id, 
                'timezone' =>  $requests->timezone, 
                'created_at' =>  $requests->created_at, 
                'latitude' =>  $requests->pickup_latitude, 
                'longitude' =>  $requests->pickup_longitude,
            ];
        });

        $today_trips = Trips::select('total_fare')->where('driver_id',$user_details->id)->whereIn('status',['Payment','Rating','Completed'])->whereDate('end_trip',$current_date);
        $today_earnings = round($today_trips->sum('total_fare'));
        $today_booking  = $today_trips->count();

        return response()->json([
            'status_code'       => '1' , 
            'status_message'    => 'Success',
            'today_trip'        => $today_booking,
            'today_amount'      => $today_earnings,
            'heat_map_data'     => $heat_map_data,
        ]);        
    }
}