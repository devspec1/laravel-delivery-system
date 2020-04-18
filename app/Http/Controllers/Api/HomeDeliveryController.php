<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HomeDeliveryOrder;
use App\Models\User;

use Validator;
use JWTAuth;

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
        
        // // Find nearest cars in location
		// $nearest_car = DriverLocation::select(DB::raw('*, ( 3959 * acos( cos( radians('.$request->latitude.') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('.$request->longitude.') ) + sin( radians('.$request->latitude.') ) * sin( radians( latitude ) ) ) ) as distance'))
        // ->having('distance', '<=', Driver_Km)
        // ->where('driver_location.status', 'Online')
        // ->with('car_type', 'users')
        // ->whereHas('users', function ($query) {
        //     $query->activeOnlyStrict();
        // })
        // ->whereHas('car_type', function ($q) {
        //     $q->where('status', 'Active');
        // })
        // ->orderBy('distance', 'ASC')
        // ->get();

        $job_array = array();

        if ($request->distance == '5') {
            $temp_details['date'] = '5 Apr 2020 | 20:00';
            $temp_details['pick_up'] = 'Funky, Thai, Keilor';
            $temp_details['distance'] = '3KM';
            $temp_details['estimate_time'] = '2.5 hours';
            $temp_details['fee'] = '$30.00 USD';
            $temp_details['status'] = 'new';

            array_push($job_array,$temp_details);


            $temp_details['date'] = '5 Apr 2020 | 21:00';
            $temp_details['pick_up'] = 'Second, Thai, Second';
            $temp_details['distance'] = '2KM';
            $temp_details['estimate_time'] = '3 hours';
            $temp_details['fee'] = '$30.00 USD';
            $temp_details['status'] = 'new';
            
            array_push($job_array,$temp_details);


            $temp_details['date'] = '5 Apr 2020 | 21:00';
            $temp_details['pick_up'] = 'Third, Second, Thai';
            $temp_details['distance'] = '4KM';
            $temp_details['estimate_time'] = '1.5 hours';
            $temp_details['fee'] = '$30.00 USD';
            $temp_details['status'] = 'new';
            
            array_push($job_array,$temp_details);
        }
        elseif ($request->distance == '10') {
            $temp_details['date'] = '5 Apr 2020 | 20:30';
            $temp_details['pick_up'] = 'Funky, Thai, Keilor';
            $temp_details['distance'] = '8KM';
            $temp_details['estimate_time'] = '3.5 hours';
            $temp_details['fee'] = '$30.00 USD';
            $temp_details['status'] = 'new';

            array_push($job_array,$temp_details);


            $temp_details['date'] = '5 Apr 2020 | 22:00';
            $temp_details['pick_up'] = 'Restaraunt, New, Second';
            $temp_details['distance'] = '12KM';
            $temp_details['estimate_time'] = '3 hours';
            $temp_details['fee'] = '$30.00 USD';
            $temp_details['status'] = 'new';
            
            array_push($job_array,$temp_details);


            $temp_details['date'] = '5 Apr 2020 | 21:00';
            $temp_details['pick_up'] = 'Third, Second, Thai';
            $temp_details['distance'] = '14KM';
            $temp_details['estimate_time'] = '4.5 hours';
            $temp_details['fee'] = '$30.00 USD';
            $temp_details['status'] = 'new';
            
            array_push($job_array,$temp_details);
        }
        elseif ($request->distance == '15') {
            $job_array = null;
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
}
