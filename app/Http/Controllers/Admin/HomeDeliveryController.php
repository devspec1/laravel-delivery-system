<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\HomeDeliveryOrderDataTable;
use App\Models\HomeDeliveryOrder;
use App\Models\User;

use App\Http\Start\Helpers;

use Validator;
use JWTAuth;
use DB;
use DateTime;

class HomeDeliveryController extends Controller
{

    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
        $this->otp_helper = resolve('App\Http\Helper\OtpHelper');        
    }

    /**
     * Load Datatable for Driver
     *
     * @param array $dataTable  Instance of Driver DataTable
     * @return datatable
     */
    public function index(HomeDeliveryOrderDataTable $dataTable)
    {
        return $dataTable->render('admin.delivery_order.view');
    }

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
        

        $job_array = array();
        $distances = array("5", "10", "15");
        if (in_array($request->distance, $distances)) {
            $dst = (int)$request->distance;
            $orders = HomeDeliveryOrder::select(DB::raw('*, CAST(longitude AS UNSIGNED) as distance'))
                ->having('distance', '<=', $dst)
                ->where('status','new')
                ->orWhere('driver_id', $user->id)->get();

            foreach ($orders as $order){
                $temp_details['order_id'] = $order->id;
                $date = new DateTime($order->created_at);
                $temp_details['date'] = $date->format('d M Y | H:i');
                $temp_details['pick_up'] = $order->pick_up;
                $temp_details['distance'] = $order->distance . 'KM';
                $temp_details['estimate_time'] = $order->estimate_time;
                $temp_details['fee'] = '$'. $order->fee . ' ' . $order->currency_code;
                $temp_details['status'] = $order->status;
                array_push($job_array,$temp_details);
            }
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
    public function acceptOrder(Request $request)
    {
        $user_details = JWTAuth::parseToken()->authenticate();

		$rules = array(
            'order_id' => 'required',
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

        if($order->status == 'assigned'){
            return response()->json([
                'status_code' 		=> '0',
                'status_message' 	=> 'Order already assigned.',
            ]);
        }

        $order->status = 'assigned';

        $order->driver_id = $user->id;

        $order->save();

		return response()->json([
			'status_code' 		=> '1',
			'status_message' 	=> "Order with id " . $order->id . ' successfully assigned',
		]);
    }
}
