<?php

/**
 * Fees Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Fees
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Fees;
use App\Models\Currency;
use App\Http\Start\Helpers;
use Validator;

class FeesController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load View and Update Fees Data
     *
     * @return redirect     to fees
     */
    public function index(Request $request)
    {
        if(!$_POST)
        {
            $data['result'] = Fees::get();
            return view('admin.fees', $data);
        }
        else if($request->submit)
        {
            // Fees Validation Rules
            $rules = array(
                    'access_fee' => 'numeric',
                    'driver_peak_fare' => 'numeric',
                    'driver_service_fee' => 'numeric',
                    'additional_fee' => 'required|in:Yes,No'
                    );

            // Fees Validation Custom Names
            $niceNames = array(
                        'access_fee' => 'Rider Service Fee',
                        'driver_peak_fare' => 'driver Peak Fare',
                        'driver_service_fee' => 'driver Service Fee',
                        'additional_fee' => 'Apply Trip Additional Fee',
                        );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else
            {
                Fees::where(['name' => 'access_fee'])->update(['value' => $request->access_fee]);
                Fees::where(['name' => 'driver_peak_fare'])->update(['value' => $request->driver_peak_fare]);
                Fees::where(['name' => 'driver_access_fee'])->update(['value' => $request->driver_service_fee]);
                Fees::where(['name' => 'additional_fee'])->update(['value' => $request->additional_fee]);

                $this->helper->flash_message('success', 'Updated Successfully'); // Call flash message function
            
                return redirect('admin/fees');
            }
        }
        else
        {
            return redirect('admin/fees');
        }
    }
    
}
