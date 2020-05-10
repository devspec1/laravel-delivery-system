<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

use App\Models\HomeDeliveryOrder;
use App\Models\Merchant;
use App\Models\MerchantIntegrationType;
use App\DataTables\MerchantsDataTable;

use App\Models\User;
use App\Models\Country;
use App\Models\Company;
use App\Models\CarType;
use App\Models\ScheduleRide;
use App\Models\Request as RideRequest;
use App\Models\DriverLocation;

use App\Http\Start\Helpers;
use App\Http\Helper\RequestHelper;

use Validator;
use JWTAuth;
use DB;
use DateTime;
use App;


class MerchantsController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct(RequestHelper $request)
    {
        $this->helper = new Helpers;
        $this->otp_helper = resolve('App\Http\Helper\OtpHelper');
        $this->request_helper = $request;        
    }

    /**
     * Load Datatable for Merchants
     *
     * @param array $dataTable  Instance of Merchants DataTable
     * @return datatable
     */
    public function index(MerchantsDataTable $dataTable)
    {
        return $dataTable->render('admin.merchant.view');
    }

    /**
     * Add a New Home Delivery Order
     *
     * @param array $request  Input values
     * @return redirect     to Home Delivery Order view
     */
    public function add(Request $request, $id=null)
    {
        if($request->isMethod("GET")) {

            if (LOGIN_USER_TYPE=='company' && Auth::guard('company')->user()->status != 'Active') {
                abort(404);
            }
            $data['integrations'] = MerchantIntegrationType::pluck('name', 'id');
    
            return view('admin.merchant.add', $data);
        }

        if($request->isMethod("POST")) {
            dd(json_encode($request->all()));
            $rules = array(
                'name'              => 'required',
                'description'       => 'required',
                'integration_type'  => 'required',
            );
            
            // Add Driver Validation Custom Names
            $attributes = array(
                'name'              => 'Merchant Name',
                'description'       => 'Description',
                'integration_type'  => 'Integration Type',
            );
                // Edit Rider Validation Custom Fields message
            $messages = array(
                'required'            => ':attribute is required.',
            );
            $validator = Validator::make($request->all(), $rules,$messages, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $merchant = new Merchant;

            $merchant->name = $request->name;
            $merchant->description = $request->description;
            $merchant->integration_type = $request->integration_type;
            $merchant->shared_secret = Str::uuid();
            $merchant->save();

            flashMessage('success', 'Merchant created');

            return redirect(LOGIN_USER_TYPE.'/merchants');
        }

        return redirect(LOGIN_USER_TYPE.'/merchants');
    }

    /**
     * Update Merchants
     *
     * @param array $request    Input values
     * @return redirect     to Home Delivery Order View
     */
    public function update(Request $request)
    {   
        if($request->isMethod("GET")) {
            //Inactive Company could not add driver
            if (LOGIN_USER_TYPE=='company' && Auth::guard('company')->user()->status != 'Active') {
                abort(404);
            }

            $data['result'] = Merchant::find($request->id);

            

            if($data['result']) {

                $data['integrations'] = MerchantIntegrationType::pluck('name', 'id');

                return view('admin.merchant.edit', $data);
            }

            flashMessage('danger', 'Invalid ID');
            return redirect(LOGIN_USER_TYPE.'/merchants'); 
        }

        if($request->isMethod("POST")) {
            // Add Driver Validation Rules
            $rules = array(
                'name'              => 'required',
                'description'       => 'required',
                'integration_type'  => 'required',
            );
            
            // Add Driver Validation Custom Names
            $attributes = array(
                'name'              => 'Name',
                'description'       => 'Description',
                'integration_type'  => 'Integration Type',
            );
            
            // Edit Rider Validation Custom Fields message
            $messages = array(
                'required'            => ':attribute is required.',
            );
            $validator = Validator::make($request->all(), $rules,$messages, $attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $merchant = Merchant::find($request->id);

            $merchant->name = $request->name;
            $merchant->description = $request->description;
            $merchant->integration_type = $request->integration_type;

            $merchant->save();
           
            flashMessage('success', 'Merchant data successfully updated');

            return redirect(LOGIN_USER_TYPE.'/merchants');
        }

        return redirect(LOGIN_USER_TYPE.'/merchants');
    }

    /**
     * Delete Order
     *
     * @param array $request    Input values
     * @return redirect     to Order View
     */
    public function delete(Request $request)
    {
        $result= $this->canDestroy($request->id);

        if($result['status'] == 0) {
            flashMessage('error',$result['message']);
            return back();
        }

        try {
            $merchant = Merchant::find($request->id);
            $merchant->delete();
        }
        catch(\Exception $e) {
            flashMessage('error','Got a problem on deleting this merchant. Contact admin, please');
            return back();
        }

        flashMessage('success', 'Deleted Successfully');
        return redirect(LOGIN_USER_TYPE.'/merchants');
    }

    // Check Given Order deletable or not
    public function canDestroy($order_id)
    {
        if ($order_id == 1){
            $return  = array('status' => '0', 'message' => 'Default merchant can\'t be deleted');
        }
        else{
            $return  = array('status' => '1', 'message' => '');
        }
    
        return $return;
    }

    /**
     * Display a referral detail
     *
     * @return \Illuminate\Http\Response
     */
    public function merchant_order_details(Request $request)
    {
        $data['merchant_orders'] = HomeDeliveryOrder::where('merchant_id', $request->id)
            ->join('users as rider', function($join) {
                $join->on('rider.id', '=', 'delivery_orders.customer_id');
            })
            ->join('request as ride_request', function($join) {
                $join->on('ride_request.id', '=', 'delivery_orders.ride_request');
            })
            ->join('merchants', function($join) {
                $join->on('merchants.id', '=', 'delivery_orders.merchant_id');
            })
            ->select([
                'delivery_orders.id as id',
                DB::raw('CONCAT(delivery_orders.estimate_time," mins") as estimate_time'),
                'delivery_orders.driver_id as driver_id', 
                'delivery_orders.created_at as created_at',
                'merchants.name as merchant_name',
                'delivery_orders.order_description as order_description',
                DB::raw('CONCAT(delivery_orders.estimate_time," mins") as estimate_time'),
                'delivery_orders.fee as fee',
                'delivery_orders.status as status',
                'ride_request.pickup_location as pick_up_location',
                'ride_request.drop_location as drop_off_location',
                DB::raw('CONCAT(rider.first_name," ",rider.last_name) as customer_name'),
                DB::raw('CONCAT("+",rider.country_code,rider.mobile_number) as mobile_number'),
            ])
            ->get();

        if($data['merchant_orders']->count() == 0) {
            flashMessage('error','Invalid ID');
            return back();
        }

        $data['merchant_name'] = Merchant::where('id', $request->id)
            ->get('name')
            ->first()
            ->name;

        return view('admin.delivery_order.details', $data);
    }
}