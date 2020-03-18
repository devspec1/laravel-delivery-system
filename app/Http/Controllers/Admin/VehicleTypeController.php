<?php

/**
 * Vehicle Type Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Vehicle Type
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\VehicleTypeDataTable;
use App\Models\CarType;
use App\Models\DriverLocation;
use App\Models\Vehicle;
use Validator;

class VehicleTypeController extends Controller
{
    /**
     * Load Datatable for vehicle Type
     *
     * @param array $dataTable Instance of VehicleTypeDataTable
     * @return datatable
     */
    public function index(VehicleTypeDataTable $dataTable)
    {
        return $dataTable->render('admin.vehicle_type.view');
    }

    /**
     * Add a New vehicle Type
     *
     * @param array $request  Input values
     * @return redirect     to vehicle Type view
     */
    public function add(Request $request)
    {
        if($request->isMethod('GET')) {
            return view('admin.vehicle_type.add');
        }
        else if($request->submit) {
            $rules = array(
                'vehicle_name'  => 'required|unique:car_type,car_name,'.$request->id,
                'vehicle_image' => 'required|mimes:jpg,jpeg,png,gif',                
                'active_image'  => 'required|mimes:jpg,jpeg,png,gif',
                'status'        => 'required',
            );

            $attributes = array(
                'vehicle_name'  => 'Name',                      
                'active_image'  =>'Active image',
                'vehicle_image' =>'Vehicle image',
                'status'        => 'Status',
                'is_pool'       => 'is For Pool',
            );

            $validator = Validator::make($request->all(), $rules, [], $attributes);
            $validator->after(function ($validator) use($request) {
                $active_car = CarType::where('status','active')->count();
                if($active_car<=0 && $request->status=='Inactive') {
                   $validator->errors()->add('status',"Atleast one vehicle type should be in active status");
                }
            });

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $vehicle_type = new  CarType;
            $vehicle_type->car_name     = $request->vehicle_name;
            $vehicle_type->description  = $request->description;
            $vehicle_type->status       = $request->status;

            $image_uploader = resolve('App\Contracts\ImageHandlerInterface');
            $target_dir = '/images/car_image';

            if ($request->hasFile('active_image')) {
                $image = $request->file('active_image');

                $extension = $image->getClientOriginalExtension();
                $file_name = "active_image_".time().".".$extension;
                $options = compact('target_dir','file_name');

                $upload_result = $image_uploader->upload($image,$options);
                if(!$upload_result['status']) {
                    flashMessage('danger', $upload_result['status_message']);
                    return back();
                }

                $vehicle_type->active_image = $upload_result['file_name'];
            }

            if ($request->hasFile('vehicle_image')) {
                $image = $request->file('vehicle_image');

                $extension = $image->getClientOriginalExtension();
                $file_name = "vehicle_image_".time().".".$extension;
                $options = compact('target_dir','file_name');

                $upload_result = $image_uploader->upload($image,$options);
                if(!$upload_result['status']) {
                    flashMessage('danger', $upload_result['status_message']);
                    return back();
                }

                $vehicle_type->vehicle_image = $upload_result['file_name'];
            }

            $vehicle_type->save();

            flashMessage('success', 'Added Successfully');
            return redirect('admin/vehicle_type');
        }

        return redirect('admin/vehicle_type');
    }

    /**
     * Update vehicle Type Details
     *
     * @param array $request    Input values
     * @return redirect     to vehicle Type View
     */
    public function update(Request $request)
    {
        if($request->isMethod('GET')) {
            $data['result'] = CarType::find($request->id);
            if($data['result']) {
                return view('admin.vehicle_type.edit', $data);  
            }
            flashMessage('danger', 'Invalid ID');
        }
        else if($request->submit) {
            $rules = array(
                'vehicle_name'  => 'required|unique:car_type,car_name,'.$request->id,
                'status'        => 'required',
                'active_image'  => 'mimes:jpg,jpeg,png,gif',
                'vehicle_image' => 'mimes:jpg,jpeg,png,gif',
            );

            // add vehicle Type Validation Custom Fields Name
            $attributes = array(
                'vehicle_name'  => 'Name',                      
                'active_image'  =>'Active image',
                'vehicle_image' =>'Vehicle image',
                'status'        => 'Status',
                'is_pool'       => 'is For Pool',
            );

            $validator = Validator::make($request->all(), $rules,[], $attributes);
            $validator->after(function ($validator) use($request) {
                $active_car = CarType::where('status','active')->where('id','!=',$request->id)->count();
                if($active_car<=0 && $request->status=='Inactive') {
                   $validator->errors()->add('status',"Atleast one vehicle type should be in active status");
                }
            });
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $vehicle_type = CarType::find($request->id);              
            $vehicle_type->car_name     = $request->vehicle_name;
            $vehicle_type->description  = $request->description;                
            $vehicle_type->status       = $request->status; 

            $image_uploader = resolve('App\Contracts\ImageHandlerInterface');
            $target_dir = '/images/car_image';

            if ($request->hasFile('active_image')) {
                $image = $request->file('active_image');

                $extension = $image->getClientOriginalExtension();
                $file_name = "active_image_".time().".".$extension;
                $options = compact('target_dir','file_name');

                $upload_result = $image_uploader->upload($image,$options);
                if(!$upload_result['status']) {
                    flashMessage('danger', $upload_result['status_message']);
                    return back();
                }

                $vehicle_type->active_image = $upload_result['file_name'];
            }

            if ($request->hasFile('vehicle_image')) {
                $image = $request->file('vehicle_image');

                $extension = $image->getClientOriginalExtension();
                $file_name = "vehicle_image_".time().".".$extension;
                $options = compact('target_dir','file_name');

                $upload_result = $image_uploader->upload($image,$options);
                if(!$upload_result['status']) {
                    flashMessage('danger', $upload_result['status_message']);
                    return back();
                }

                $vehicle_type->vehicle_image = $upload_result['file_name'];
            }

            $vehicle_type->save(); 

            flashMessage('success', 'Updated Successfully');
        }

        return redirect('admin/vehicle_type');
    }

    /**
     * Delete vehicle Type
     *
     * @param array $request    Input values
     * @return redirect     to vehicle Type View
     */
    public function delete(Request $request)
    {
        $driver_location_id = DriverLocation::where('car_id',$request->id)->count();
        $find_vehicle_id = Vehicle::where('vehicle_id',$request->id)->count();
        $active_car = CarType::where('status','active')->where('id','!=',$request->id)->count();
        if($driver_location_id) {
            flashMessage('danger', "Driver using this Vehicle  type, So can't delete this"); // Call flash message function
        }
        elseif($find_vehicle_id) {
            flashMessage('danger', "vehicle using this Vehicle type, So can't delete this"); // Call flash message function
        }
        elseif($active_car<=0) {
            flashMessage('danger', "Atleast one vehicle type should be in active status, So can't delete this");
        }
        else { 
            CarType::find($request->id)->delete();
            flashMessage('success', 'Deleted Successfully');
        }
        return redirect('admin/vehicle_type');
    }
}