<?php

/**
 * Manage Fare Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Manage Fare
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\DataTables\ManageFareDataTable;
use App\Http\Start\Helpers;
use App\Models\Location;
use App\Models\ManageFare;
use App\Models\PeakFareDetail;
use App\Models\Currency;
use App\Models\Request as RideRequest;
use App\Models\CarType;
use Validator;

class ManageFareController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Class $dataTable instance of ManageFareDataTable
     * @return \Illuminate\Http\Response
     */
    public function index(ManageFareDataTable $dataTable)
    {
        return $dataTable->render('admin.manage_fare.view');
    }

    /**
     * Add new Peak Based Fare Details
     *
     * @param array $request  Input values
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        if($request->isMethod('GET')) {
            $data['locations']      = Location::active()->pluck('name', 'id');
            $data['vehicle_types']  = CarType::active()->pluck('car_name', 'id')->toArray();
            $data['time_options']   = $this->helper->create_time_range('0:00', '23:00', '1 hour');
            $data['currency']       = Currency::codeSelect();
            $data['day_options']    = $this->helper->get_day_options();

            return view('admin.manage_fare.add',$data);
        }

        $rules = array(
            'location'      => 'required',
            'vehicle_type'  => 'required',
            'apply_peak'    => 'required',
            'apply_night'   => 'required',
            'base_fare'     => 'required|numeric|min:0',
            'capacity'      => 'required|integer|min:1',
            'min_fare'      => 'required|numeric|min:0',
            'per_min'       => 'required|numeric|min:0',
            'per_km'        => 'required|numeric|min:0',
            'schedule_fare' => 'required|numeric|min:0',
            'currency_code' => 'required',
            'waiting_time'  => 'nullable|numeric|min:0',
        );

        if($request->waiting_time > 0) {
            $rules['waiting_charge'] = 'required|numeric|min:0.1';
        }

        $attributes = array(
            'location'      => 'Location ',
            'vehicle_type' => 'Vehicle Type',
            'apply_peak'    => 'Apply Peak',
            'apply_night'   => 'Apply Night',
            'base_fare'     => 'Base Fare',
            'min_fare'      => 'Min Fare',
            'per_min'       => 'Per Minutes',
            'per_km'        => 'Per Kilometer',
            'schedule_fare' => 'Schedule Ride Fare',
            'currency_code' => 'Currency code',
            'capacity'      => 'Capacity',
            'waiting_time'  => 'Waiting Time',
            'waiting_charge'=> 'Waiting Charge',
        );
        
        $messages = array(
            'night_fare_details.price.min'   => 'Price should above 1',
            'peak_fare_details.*.price.min'   => 'Price should above 1',
        );

        if($request->apply_peak == 'Yes') {
            foreach($request->peak_fare_details ?: array() as $key => $fare_rules)
            {
                $rules['peak_fare_details.'.$key.'.day'] = 'required';
                $rules['peak_fare_details.'.$key.'.start_time'] = 'required';
                $rules['peak_fare_details.'.$key.'.end_time'] = 'required';
                $rules['peak_fare_details.'.$key.'.price'] = 'required|numeric|min:1.1';

                $attributes['peak_fare_details.'.$key.'.day'] = 'Day';
                $attributes['peak_fare_details.'.$key.'.start_time'] = 'Start Time';
                $attributes['peak_fare_details.'.$key.'.end_time'] = 'End Time';
                $attributes['peak_fare_details.'.$key.'.price'] = 'Price';

                $messages['peak_fare_details.'.$key.'.price.min'] = 'Price should above 1';
            }
        }

        if($request->apply_night == 'Yes') {
            $rules['night_fare_details.start_time'] = 'required';
            $rules['night_fare_details.end_time'] = 'required';
            $rules['night_fare_details.price'] = 'required|numeric|min:1.1';

            $attributes['night_fare_details.start_time'] = 'Start Time';
            $attributes['night_fare_details.end_time'] = 'End Time';
            $attributes['night_fare_details.price'] = 'Price';
        }

        $validator = Validator::make($request->all(), $rules,$messages, $attributes);

        if ($validator->fails()) {
            // Form calling with Errors and Input values
            return back()->withErrors($validator)->withInput();
        }

        $fare_count = ManageFare::whereLocationId($request->location)->Where('vehicle_id',$request->vehicle_type)->count();

        if($fare_count > 0) {
            return back()->withInput()->withErrors(array('same_loc_error' => 'Same Location with same Vehicle Found, Choose another'));
        }

        $manage_fare                  = new ManageFare;
        $manage_fare->location_id     = $request->location;
        $manage_fare->vehicle_id      = $request->vehicle_type;
        $manage_fare->base_fare    = $request->base_fare;
        $manage_fare->min_fare     = $request->min_fare;
        $manage_fare->per_min      = $request->per_min;
        $manage_fare->per_km       = $request->per_km;
        $manage_fare->schedule_fare= $request->schedule_fare;
        $manage_fare->capacity     = $request->capacity;
        $manage_fare->currency_code= $request->currency_code;
        $manage_fare->apply_peak      = $request->apply_peak;
        $manage_fare->apply_night     = $request->apply_night;
        $manage_fare->waiting_time    = $request->waiting_time;
        $manage_fare->waiting_charge  = $request->waiting_time > 0 ? $request->waiting_charge : 0;

        $manage_fare->save();


        // Save Peak fare Rules
        if($request->apply_peak == 'Yes') {
            foreach($request->peak_fare_details ?: array() as $fare_rule_data) {
                if(is_null($fare_rule_data)) {
                    break;
                }

                $fare_rules             = new PeakFareDetail;
                $fare_rules->fare_id    = $manage_fare->id;
                $fare_rules->day        = $fare_rule_data['day'];
                $fare_rules->start_time = $fare_rule_data['start_time'];
                $fare_rules->end_time   = $fare_rule_data['end_time'];
                $fare_rules->price      = $fare_rule_data['price'];
                $fare_rules->type       = 'peak';

                $fare_rules->save();
            }
        }

        // Save Night fare Rules
        if($request->apply_night == 'Yes') {
            $fare_rules             = new PeakFareDetail;
            $fare_rules->fare_id    = $manage_fare->id;
            $fare_rules->start_time = $request->night_fare_details['start_time'];
            $fare_rules->end_time   = $request->night_fare_details['end_time'];
            $fare_rules->price      = $request->night_fare_details['price'];
            $fare_rules->type       = 'night';

            $fare_rules->save();
        }
        // Call flash message function
        flashMessage('success', 'Manage Fare Details Added Successfully');

        return redirect('admin/manage_fare');
    }

    /**
     * Update Peak Based Details
     *
     * @param array $request  Input values
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        if($request->isMethod('GET')) {
            $data['result'] = ManageFare::find($request->id);
            $fare_rules = PeakFareDetail::where('fare_id',$request->id)->get();
            $data['peak_fare_details'] = array_values($fare_rules->where('type','Peak')->toArray());
            if(count($data['peak_fare_details']) == 0) {
                $data['peak_fare_details'] = array(['day'=> '','start_time' => '','end_time' => '']);
            }
            $data['night_fare_details'] = $fare_rules->where('type','Night')->first();
            $data['locations'] = Location::active()->pluck('name', 'id');
            $data['vehicle_types'] = CarType::active()->pluck('car_name', 'id')->toArray();
            $data['time_options'] = $this->helper->create_time_range('0:00', '23:00', '1 hour');
            $data['day_options'] = $this->helper->get_day_options();
            $data['currency']   = Currency::codeSelect();

            if(!$data['result']) {
                flashMessage('danger', 'Invalid ID');
                return redirect('admin/manage_fare');
            }

            return view('admin.manage_fare.edit', $data);
        }

        $rules = array(
            'location'      => 'required',
            'vehicle_type'  => 'required',
            'apply_peak'    => 'required',
            'apply_night'   => 'required',
            'base_fare'     => 'required|numeric|min:0',
            'capacity'      => 'required|integer|min:1',
            'min_fare'      => 'required|numeric|min:0',
            'per_min'       => 'required|numeric|min:0',
            'per_km'        => 'required|numeric|min:0',
            'schedule_fare' => 'required|numeric|min:0',
            'currency_code' => 'required',
            'waiting_time'  => 'nullable|numeric|min:0',
        );

        if($request->waiting_time > 0) {
            $rules['waiting_charge'] = 'required|numeric|min:0.1';
        }

        $attributes = array(
            'location'      => 'Location ',
            'vehicle_type'  => 'Vehicle Type',
            'apply_peak'    => 'Apply Peak',
            'apply_night'   => 'Apply Night',
            'base_fare'     => 'Base Fare',
            'min_fare'      => 'Min Fare',
            'per_min'       => 'Per Minutes',
            'per_km'        => 'Per Kilometer',
            'schedule_fare' => 'Schedule Ride Fare',
            'currency_code' => 'Currency code',
            'capacity'      => 'Capacity',
            'waiting_time'  => 'Waiting Time',
            'waiting_charge'=> 'Waiting Charge',
        );

        if($request->apply_peak=='Yes') {
            foreach($request->peak_fare_details ?: array() as $key => $fare_rules) {
                $rules['peak_fare_details.'.$key.'.day']            = 'required';
                $rules['peak_fare_details.'.$key.'.start_time']     = 'required';
                $rules['peak_fare_details.'.$key.'.end_time']       = 'required';
                $rules['peak_fare_details.'.$key.'.price']          = 'required|numeric|min:1.1';

                $attributes['peak_fare_details.'.$key.'.day']        = 'Day';
                $attributes['peak_fare_details.'.$key.'.start_time'] = 'Start Time';
                $attributes['peak_fare_details.'.$key.'.end_time']   = 'End Time';
                $attributes['peak_fare_details.'.$key.'.price']      = 'Price';
            }
        }

        if($request->apply_night == 'Yes') {
            $rules['night_fare_details.start_time'] = 'required';
            $rules['night_fare_details.end_time'] = 'required';
            $rules['night_fare_details.price'] = 'required|numeric|min:1.1';

            $attributes['night_fare_details.start_time'] = 'Start Time';
            $attributes['night_fare_details.end_time'] = 'End Time';
            $attributes['night_fare_details.price'] = 'Price';
        }

        $messages = array(
            'night_fare_details.price.min'   => 'Price should above 1',
            'peak_fare_details.*.price.min'   => 'Price should above 1',
        );

        $validator = Validator::make($request->all(), $rules,$messages, $attributes);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $manage_fare_id             = $request->id;
        $fare_count = ManageFare::whereLocationId($request->location)->Where('vehicle_id',$request->vehicle_type)->where('id','!=',$manage_fare_id)->count();

        if($fare_count > 0) {
            return back()->withInput()->withErrors(array('same_loc_error' => 'Same Location with same Vehicle Found, Choose another'));
        }

        $manage_fare = ManageFare::find($manage_fare_id);

        if($request->location != $manage_fare->location_id || $request->vehicle_type != $manage_fare->vehicle_id) {
            $pending_request = $this->checkPendingTrips($manage_fare->location_id, $manage_fare->vehicle_id);
            if($pending_request > 0) {
                return back()->withInput()->withErrors(array('location' => 'Trip with this location is pending.So Could not edit location or Vehicle'));
            }
        }

        $manage_fare->location_id   = $request->location;
        $manage_fare->vehicle_id    = $request->vehicle_type;
        $manage_fare->base_fare     = $request->base_fare;
        $manage_fare->min_fare      = $request->min_fare;
        $manage_fare->per_min       = $request->per_min;
        $manage_fare->per_km        = $request->per_km;
        $manage_fare->schedule_fare = $request->schedule_fare;
        $manage_fare->capacity      = $request->capacity;
        $manage_fare->currency_code = $request->currency_code;
        $manage_fare->apply_peak    = $request->apply_peak;
        $manage_fare->apply_night   = $request->apply_night;
        $manage_fare->waiting_time  = $request->waiting_time;
        $manage_fare->waiting_charge= $request->waiting_time > 0 ? $request->waiting_charge : 0;

        $manage_fare->save();

        // Delete removed fare details from database
        $removed_fares = explode(',', $request->removed_fares);
        if(count($removed_fares) > 0) {
            $fare_details = PeakFareDetail::whereIn('id',$removed_fares)->delete();
        }

        // Delete All Peak price rules if apply peak equal to no
        if($request->apply_peak == 'No' && isset($request->peak_fare_details)) {
            $fare_details = PeakFareDetail::where('fare_id',$manage_fare_id)->where('type','Peak')->delete();
        }

        // Save Peak fare Rules
        if($request->apply_peak == 'Yes') {
            // dd($request->peak_fare_details);
            foreach($request->peak_fare_details ?: array() as $fare_rule_data) {
                if(is_null($fare_rule_data)) {
                    break;
                }

                $fare_rules             = PeakFareDetail::firstOrNew(['id' => $fare_rule_data['id'],'fare_id' => $manage_fare->id]);
                $fare_rules->fare_id    = $manage_fare->id;
                $fare_rules->day        = $fare_rule_data['day'];
                $fare_rules->start_time = $fare_rule_data['start_time'];
                $fare_rules->end_time   = $fare_rule_data['end_time'];
                $fare_rules->price      = $fare_rule_data['price'];
                $fare_rules->type       = 'peak';

                $fare_rules->save();
            }
        }

        if($request->apply_night == 'No' && isset($request->night_fare_details['id'])) {
            PeakFareDetail::find($request->night_fare_details['id'])->delete();
        }

        // Save Night fare Rules
        if($request->apply_night == 'Yes') {
            $fare_rules             = PeakFareDetail::firstOrNew(['id' => $request->night_fare_details['id'],'fare_id' => $manage_fare->id]);
            $fare_rules->fare_id    = $manage_fare->id;
            $fare_rules->start_time = $request->night_fare_details['start_time'];
            $fare_rules->end_time   = $request->night_fare_details['end_time'];
            $fare_rules->price      = $request->night_fare_details['price'];
            $fare_rules->type       = 'night';

            $fare_rules->save();
        }

        flashMessage('success', 'Manage Fare Details Updated Successfully');

        return redirect('admin/manage_fare');
    }

    public function checkPendingTrips($location_id, $vehicle_id)
    {
        $pending_request = RideRequest::
                            whereHas('trips',function($q) use ($location_id, $vehicle_id){
                                $q->whereHas('driver_location',function($q1){
                                    $q1->where('status','Trip');
                                })
                                ->where('car_id',$vehicle_id);
                            })
                            ->where('location_id',$location_id)
                            ->count();
        return $pending_request;
    }

    /**
     * Remove the Peak Based Fare details
     *
     * @param array $request  Input values 
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $manage_fare = ManageFare::find($request->id);
        if(!is_null($manage_fare)) {
            $pending_request = $this->checkPendingTrips($manage_fare->location_id, $manage_fare->vehicle_id);

            if($pending_request > 0) {
                flashMessage('error', 'Trip with this Fare is pending. So Could not Delete Fare');
                return redirect('admin/manage_fare');
            }

            PeakFareDetail::where('fare_id',$manage_fare->id)->delete();
            $manage_fare->delete();
            // Call flash message function
            flashMessage('success', 'Manage Fare Details successfully deleted');
        }

        flashMessage('warning', 'Manage Fare Details already deleted');

        return redirect('admin/manage_fare');
    }
}
