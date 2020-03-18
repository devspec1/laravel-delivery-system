<?php

/**
 * Request Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Request
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\RequestDataTable;
use App\Models\Request as RideRequest;
use App\Models\Currency;
use DB;

class RequestController extends Controller
{
    /**
     * Load Datatable for Request
     *
     * @param array $dataTable  Instance of RequestDataTable
     * @return datatable
     */
    public function index(RequestDataTable $dataTable)
    {
        return $dataTable->render('admin.request.view');
    }

    public function detail_request(Request $request)
    {
        $request_id = $request->id;

        $data['request_details'] = RideRequest::with('users','driver','trips')
        ->where(function($query)  {
            if(LOGIN_USER_TYPE=='company') {
                $query->whereHas('driver',function($q1){
                    $q1->where('company_id',auth('company')->user()->id);
                });
            }
        })
        ->where('id',$request_id)
        ->first();

        if($data['request_details']) {
            $request_status = RideRequest::where('group_id',$data['request_details']->group_id)->where('status','Accepted');
            if($dt = $request_status->first()) {
              $data['driver_name']  = $dt->driver->first_name.' '.$dt->driver->last_name;
              $data['company_name'] = $dt->driver->company->name;
            }

            $data['trip_path'] = $data['request_details']->trip_path;

            $pending_request_status = DB::table('request')->where('group_id',$data['request_details']->group_id)->where('status','Pending');
            $data['invoice_data'] = array();
            if($request_status->count()) {
                $ride_request = $request_status->first();
                $req_id  = $ride_request->id;
                $trips_controller = resolve('App\Http\Controllers\Admin\TripsController');
                $data['invoice_data'] = $trips_controller->getAdminInvoice($ride_request->accepted_trips);
                $trip_status = @DB::table('trips')->where('request_id',$req_id)->get()->first()->status;

                $data['trip_data'] = $ride_request->accepted_trips;
                $data['trip_path'] = $ride_request->accepted_trips->trip_path;
                $data['is_tripped'] = true;
                $data['request_status'] = $trip_status;
            }
            elseif($pending_request_status->count()) {
                $data['request_status'] = "Searching";
            }
            else {
                $data['request_status'] = "No one accepted";
            }
            //For company user login, get session currency
            if (LOGIN_USER_TYPE=='company' && session()->get('currency') != null) {
                $data['default_currency'] = Currency::whereCode(session()->get('currency'))->first();
            }

            return view('admin.request.details', $data);
        }

        flashMessage('danger', 'Invalid ID');
        return redirect(LOGIN_USER_TYPE.'/request');
    }
}