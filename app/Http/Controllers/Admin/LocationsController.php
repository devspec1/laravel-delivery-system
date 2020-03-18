<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\LocationsDataTable;
use App\Http\Start\Helpers;
use App\Models\Location;
use App\Models\ManageFare;
use Validator;
use DB;

class LocationsController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Class $dataTable instance of LocationsDatatable
     * @return \Illuminate\Http\Response
     */
    public function index(LocationsDataTable $dataTable)
    {
        return $dataTable->render('admin.locations.view');
    }

    /**
     * Add new Location
     *
     * @param array $request  Input values
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
    	if(!$_POST) {
            return view('admin.locations.add');
        }
        else if($request->submit) {
            $rules = array(
                    'name'              => 'required|unique:locations',
                    'coordinates'       => 'required',
                    'status'            => 'required'
                    );

            $niceNames = array(
                        'name'          => 'Name',
                        'coordinates'   => 'Coordinates',
                        'status'        => 'Status'
                        );
            $message = array(
                        'coordinates.required'   => 'Please mark any location.',
                        );

            $validator = Validator::make($request->all(), $rules,$message);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) {
                $formatted_cords = $this->helper->getFormattedCoordinates($request->coordinates);
                // Form calling with Errors and Input values
                return back()->withErrors($validator)->withInput()->withInput(['formatted_coords' => $formatted_cords]);
            }
            else {
                $formatted_cords = $this->helper->getFormattedCoordinates($request->coordinates);
                $is_already = $this->validate_cords($formatted_cords);
                if($is_already) {
                    return back()->withInput()->withInput(['formatted_coords' => $formatted_cords,'location_set' => 'Location already selected']);
                }
                $location = new Location;
                $location->name         = $request->name;
                $coordinates            = rtrim($request->coordinates,')');
                $location->coordinates  = 'POLYGON('.$coordinates.' ))';
                $location->status       = $request->status;

                $location->save();

                $this->helper->flash_message('success', 'Location Added Successfully'); // Call flash message function

                return redirect('admin/locations');
            }
        }
        else{
            return redirect('admin/locations');
        }
    }

    /**
     * Update Location Details
     *
     * @param array $request  Input values
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
    	if(!$_POST) {
            $data['result'] = Location::find($request->id);

            if(!$data['result']) {
                // Call flash message function
                $this->helper->flash_message('danger', 'Invalid ID');

                return redirect('admin/locations');
            }

            return view('admin.locations.edit', $data);
        }
        else if($request->submit) {
            $rules = array(
                    'name'              => 'required',
                    'coordinates'       => 'required',
                    'status'            => 'required'
                    );

            $niceNames = array(
                        'name'          => 'Name',
                        'coordinates'   => 'Coordinates',
                        'status'        => 'Status'
                        );
            $message = array(
                        'coordinates.required'   => 'Please mark any location.',
                        );

            $validator = Validator::make($request->all(), $rules,$message);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) {
                $formatted_cords = $this->helper->getFormattedCoordinates($request->coordinates);
                // Form calling with Errors and Input values
                return back()->withErrors($validator)->withInput()->withInput(['formatted_coords' => $formatted_cords]);
            }
            else {
                $formatted_cords = $this->helper->getFormattedCoordinates($request->coordinates);
                $is_already = $this->validate_cords($formatted_cords,$request->id);
                if($is_already) {
                    return back()->withInput()->withInput(['formatted_coords' => $formatted_cords,'location_set' => 'Location already selected']);
                }
                $location = Location::find($request->id);
                $location->name         = $request->name;
                $coordinates            = rtrim($request->coordinates,')');
                $location->coordinates  = 'POLYGON('.$coordinates.' ))';
                $location->status       = $request->status;

                $location->save();

                $this->helper->flash_message('success', 'Location Added Successfully'); // Call flash message function

                return redirect('admin/locations');
            }
        }
        else {
            return redirect('admin/locations');
        }
    }

    /**
     * Remove the Location details
     *
     * @param array $request  Input values 
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $fare_count = ManageFare::where('location_id',$request->id)->get()->count();
    	$location = Location::find($request->id);

        if($fare_count > 0 ) {
            $this->helper->flash_message('danger', 'Sorry this Location has fare details. So cannot delete.');
        }
        else if(!is_null($location)) {
            $location->delete();
            // Call flash message function
            $this->helper->flash_message('success', 'Location successfully deleted');
        }
        else {
            // Call flash message function
            $this->helper->flash_message('danger', 'Location already deleted');
        }

        return redirect('admin/locations');
    }

    /**
     * Validate selected location already selected or not
     *
     * @param array $request  Input values 
     * @return Boolean $success 
     */
    public function validate_cords($coordinate_data,$location_id = '')
    {
        $success = false;
        foreach ($coordinate_data[0] as $cord_data) {
            $match_location = Location::select(DB::raw("id,(ST_WITHIN( GeomFromText(
                        'POINT(".$cord_data['lat'].' '.$cord_data['lng'].")'),ST_GeomFromText(coordinates))) as available "))->where('id','<>',$location_id)->having('available','1')->first();
            if($match_location) {
                $success = true;
                return $success;
            }
        }
        return $success;
    }
}
