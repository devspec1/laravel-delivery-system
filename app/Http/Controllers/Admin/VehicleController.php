<?php

/**
 * Vehicle Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Vehicle
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\VehicleDataTable;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Country;
use App\Models\CarType;
use App\Models\Company;
use App\Models\DriverDocuments;
use Validator;
use DB;
use Auth;

class VehicleController extends Controller
{
    /**
     * Load Datatable for Driver
     *
     * @param array $dataTable  Instance of Driver DataTable
     * @return datatable
     */
    public function index(VehicleDataTable $dataTable)
    {
        return $dataTable->render('admin.vehicle.view');
    }

    /**
     * Add a New Driver
     *
     * @param array $request  Input values
     * @return redirect     to Driver view
     */
    public function add(Request $request)
    {
        if($request->isMethod('GET')) {
            // Inactive company can not add any vehicle
            if (LOGIN_USER_TYPE == 'company' && Auth::guard('company')->user()->status != 'Active') {
                abort(404);
            }

            $data['country_code_option'] = Country::select('long_name','phone_code')->get();
            $data['country_name_option'] = Country::pluck('long_name', 'short_name');
            $data['company'] = Company::where('status','Active')->pluck('name','id');
            $data['car_type'] = CarType::where('status','Active')->pluck('car_name', 'id');
            
            return view('admin.vehicle.add',$data);
        }

        if($request->submit) {
            $rules = array(
                'driver_name'   => 'required',
                'status'        => 'required',
                'insurance'     => 'required|mimes:jpg,jpeg,png,gif',
                'rc'            => 'required|mimes:jpg,jpeg,png,gif',
                'permit'        => 'required|mimes:jpg,jpeg,png,gif',
                'vehicle_id'    => 'required',
                'vehicle_name'  => 'required',
                'vehicle_number'=> 'required',
            );

            if (LOGIN_USER_TYPE!='company') {
                $rules['company_name'] = 'required';
            }

            $attributes = array(
                'status'        => trans('messages.driver_dashboard.status'),
                'vehicle_id'    => trans('messages.user.veh_type'),
                'insurance'     => trans('messages.driver_dashboard.motor_insurance'),
                'rc'            => trans('messages.driver_dashboard.reg_certificate'),
                'permit'        => trans('messages.driver_dashboard.carriage_permit'),
            );
            
            $messages =array(
                'required'            => ':attribute is required.',
                'mobile_number.regex' => trans('messages.user.mobile_no'),
            );

            $validator = Validator::make($request->all(), $rules,$messages,$attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $vehicle = new Vehicle;
            $vehicle->user_id = $request->driver_name;
            $vehicle->company_id = (LOGIN_USER_TYPE != 'company') ? $request->company_name : Auth::guard('company')->user()->id;
            $vehicle->status       =   $request->status;

            $vehicle->save();

            $driver_doc = DriverDocuments::where('user_id', $vehicle->user_id)->first();
            if ($driver_doc == '') {
                $driver_doc = new DriverDocuments;
                $driver_doc->user_id = $vehicle->user_id;
                $driver_doc->document_count = 0;
                $driver_doc->save();
            }

            $image_uploader = resolve('App\Contracts\ImageHandlerInterface');
            $target_dir = '/images/vehicle/'.$vehicle->id;
            $target_path = asset($target_dir).'/';

            if ($request->hasFile('insurance')) {
                $image = $request->file('insurance');

                $extension = $image->getClientOriginalExtension();
                $file_name = "insurance_".time().".".$extension;
                $options = compact('target_dir','file_name');

                $upload_result = $image_uploader->upload($image,$options);
                if(!$upload_result['status']) {
                    return back()->withError($upload_result['status_message']);
                }

                $vehicle->insurance = $target_path.$upload_result['file_name'];
            }

            if ($request->hasFile('rc')) {
                $image = $request->file('rc');

                $extension = $image->getClientOriginalExtension();
                $file_name = "rc_".time().".".$extension;
                $options = compact('target_dir','file_name');

                $upload_result = $image_uploader->upload($image,$options);
                if(!$upload_result['status']) {
                    return back()->withError($upload_result['status_message']);
                }

                $vehicle->rc = $target_path.$upload_result['file_name'];
            }

            if ($request->hasFile('permit')) {
                $image = $request->file('permit');

                $extension = $image->getClientOriginalExtension();
                $file_name = "permit_".time().".".$extension;
                $options = compact('target_dir','file_name');

                $upload_result = $image_uploader->upload($image,$options);
                if(!$upload_result['status']) {
                    return back()->withError($upload_result['status_message']);
                }

                $vehicle->permit = $target_path.$upload_result['file_name'];
            }
            
            $vehicle->vehicle_id      = $request->vehicle_id;
            $vehicle->vehicle_name    = $request->vehicle_name;
            $vehicle->vehicle_number  = $request->vehicle_number;
            $vehicle->vehicle_type    = CarType::find($request->vehicle_id)->car_name;
            $vehicle->save();
           
            flashMessage('success', trans('messages.user.add_success'));
        }

        return redirect(LOGIN_USER_TYPE.'/vehicle');
    }

    /**
     * Update Driver Details
     *
     * @param array $request    Input values
     * @return redirect     to Driver View
     */
    public function update(Request $request)
    {
        if($request->isMethod("GET")) {
            $data['result']             = Vehicle::find($request->id);

            if($data['result'] && (LOGIN_USER_TYPE!='company' || Auth::guard('company')->user()->id == $data['result']->company_id)) {
                $data['country_code_option']=Country::select('long_name','phone_code')->get();
                $data['car_type']           = CarType::where('status','Active')->pluck('car_name', 'id');
                $data['company']=Company::where('status','Active')->pluck('name','id');
                $data['path']               = url('images/users/'.$request->id);
                return view('admin.vehicle.edit', $data);
            }
            flashMessage('danger', 'Invalid ID');
        }

        if($request->submit) {
            $rules = array(
                'status'        => 'required',
                'insurance'     => 'mimes:jpg,jpeg,png,gif',
                'rc'            => 'mimes:jpg,jpeg,png,gif',
                'permit'        => 'mimes:jpg,jpeg,png,gif',
                'vehicle_id'    => 'required',
                'vehicle_name'  => 'required',
                'vehicle_number'=> 'required',
            );

            if (LOGIN_USER_TYPE!='company') {
                $rules['company_name'] = 'required';
            }

            $attributes = array(
                'status'        => trans('messages.driver_dashboard.status'),
                'insurance'     => trans('messages.user.insurance'),
                'rc'            => trans('messages.user.rc_book'),
                'permit'        => trans('messages.user.permit'),
                'vehicle_id'    => trans('messages.user.veh_type'),
                'vehicle_name'  => trans('messages.user.veh_name'),
                'vehicle_number'=> trans('messages.user.veh_no'),
                'insurance'     => trans('messages.user.motor_insurance'),
                'rc'            => trans('messages.user.reg_certificate'),
                'permit'        => trans('messages.user.carriage_permit'),
            );

            $messages =array(
                'required'            => ':attribute is required.',
                'mobile_number.regex' => trans('messages.user.mobile_no'),
            );

            $validator = Validator::make($request->all(), $rules,$messages,$attributes);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $vehicle = Vehicle::find($request->id);
            $vehicle->user_id   = $request->driver_name;
            $vehicle->company_id= (LOGIN_USER_TYPE != 'company') ? $request->company_name : Auth::guard('company')->user()->id;
            $vehicle->status    = $request->status;

            $image_uploader = resolve('App\Contracts\ImageHandlerInterface');
            $target_dir = '/images/vehicle/'.$vehicle->id;
            $target_path = asset($target_dir).'/';

            if ($request->hasFile('insurance')) {
                $image = $request->file('insurance');

                $extension = $image->getClientOriginalExtension();
                $file_name = "insurance_".time().".".$extension;
                $options = compact('target_dir','file_name');

                $upload_result = $image_uploader->upload($image,$options);
                if(!$upload_result['status']) {
                    return back()->withError($upload_result['status_message']);
                }

                $vehicle->insurance = $target_path.$upload_result['file_name'];
            }

            if ($request->hasFile('rc')) {
                $image = $request->file('rc');

                $extension = $image->getClientOriginalExtension();
                $file_name = "rc_".time().".".$extension;
                $options = compact('target_dir','file_name');

                $upload_result = $image_uploader->upload($image,$options);
                if(!$upload_result['status']) {
                    return back()->withError($upload_result['status_message']);
                }

                $vehicle->rc = $target_path.$upload_result['file_name'];
            }

            if ($request->hasFile('permit')) {
                $image = $request->file('permit');

                $extension = $image->getClientOriginalExtension();
                $file_name = "permit_".time().".".$extension;
                $options = compact('target_dir','file_name');

                $upload_result = $image_uploader->upload($image,$options);
                if(!$upload_result['status']) {
                    return back()->withError($upload_result['status_message']);
                }

                $vehicle->permit = $target_path.$upload_result['file_name'];
            }

            $vehicle->vehicle_id      = $request->vehicle_id;
            $vehicle->vehicle_name    = $request->vehicle_name;
            $vehicle->vehicle_number  = $request->vehicle_number;
            $vehicle->vehicle_type    = CarType::find($request->vehicle_id)->car_name;                
            $vehicle->save();
            
            $driver_doc = DriverDocuments::where('user_id', $vehicle->user_id)->first();
            if ($driver_doc == null) {
                $driver_doc = new DriverDocuments;
                $driver_doc->user_id = $vehicle->user_id;
                $driver_doc->document_count = 0;
                $driver_doc->save();
            }

            flashMessage('success', 'Updated Successfully');
            return redirect(LOGIN_USER_TYPE.'/vehicle');
        }

        return redirect(LOGIN_USER_TYPE.'/vehicle');
    }

    /**
     * Delete Driver
     *
     * @param array $request    Input values
     * @return redirect     to Driver View
     */
    public function delete(Request $request)
    {    
        $vehicle = Vehicle::find($request->id);
        //If login user is company then it can edit it's vehicle only
        if($vehicle==null || (LOGIN_USER_TYPE=='company' && $vehicle->company_id != Auth::guard('company')->user()->id)) {
            flashMessage('danger', 'Invalid ID');
            return redirect(LOGIN_USER_TYPE.'/vehicle');
        }

        try {
            $vehicle->delete();
            flashMessage('success', 'Deleted Successfully');            
        }
        catch (\Exception $e) {
            flashMessage('danger', $e->getMessage());
        }

        return redirect(LOGIN_USER_TYPE.'/vehicle');
    }

    /**
     * get Driver
     *
     * @param array $request Input values
     * @return response json
     */
    public function get_driver(Request $request,$company_id)
    {  
        $drivers = User::select('id','first_name','last_name')
            ->whereNotIn('status',['Inactive'])
            ->where('user_type','Driver')
            ->where('company_id',$company_id)
            ->where(function($query) use ($request)  {
                $query->whereDoesntHave('vehicle')
                ->orWhereHas('vehicle',function($query) use ($request) {
                    $query->where('id',$request->vehicle_id);
                });
            })
            ->get();

        return response()->json([
            'status_code' => '1',
            'drivers' => $drivers,
        ]);
    }
}