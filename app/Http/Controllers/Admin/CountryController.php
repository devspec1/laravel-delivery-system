<?php

/**
 * Country Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Country
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataTables\CountryDataTable;
use App\Models\Country;
use App\Http\Start\Helpers;
use Validator;

class CountryController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load Datatable for Country
     *
     * @param array $dataTable  Instance of CountryDataTable
     * @return datatable
     */
    public function index(CountryDataTable $dataTable)
    {
        return $dataTable->render('admin.country.view');
    }

    /**
     * Add a New Country
     *
     * @param array $request  Input values
     * @return redirect     to Country view
     */
    public function add(Request $request)
    {
        if(!$_POST)
        {
            return view('admin.country.add');
        }
        else if($request->submit)
        {
            // Add Country Validation Rules
            $rules = array(
                    'short_name' => 'required|unique:country',
                    'long_name'  => 'required|unique:country',
                    'phone_code' => 'required'
                    );

            // Add Country Validation Custom Names
            $niceNames = array(
                        'short_name' => 'Short Name',
                        'long_name'  => 'Long Name',
                        'phone_code' => 'Phone Code'
                        );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else
            {
            $country = new Country;

            $country->short_name = $request->short_name;
            $country->long_name  = $request->long_name;
            $country->iso3       = $request->iso3;
            $country->num_code   = $request->num_code;
            $country->phone_code = $request->phone_code;

            $country->save();

            $this->helper->flash_message('success', 'Added Successfully'); // Call flash message function

            return redirect('admin/country');
            }
        }
        else
        {
            return redirect('admin/country');
        }
    }

    /**
     * Update Country Details
     *
     * @param array $request    Input values
     * @return redirect     to Country View
     */
    public function update(Request $request)
    {
        if(!$_POST)
        {
			$data['result'] = Country::find($request->id);

            return view('admin.country.edit', $data);
        }
        else if($request->submit)
        {
            // Edit Country Validation Rules
            $rules = array(
                    'short_name' => 'required|unique:country,short_name,'.$request->id,
                    'long_name'  => 'required|unique:country,long_name,'.$request->id,
                    'phone_code' => 'required'
                    );

            // Edit Country Validation Custom Fields Name
            $niceNames = array(
                        'short_name' => 'Short Name',
                        'long_name'  => 'Long Name',
                        'phone_code' => 'Phone Code'
                        );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else
            {
                $country = Country::find($request->id);

			    $country->short_name = $request->short_name;
                $country->long_name  = $request->long_name;
                $country->iso3       = $request->iso3;
                $country->num_code   = $request->num_code;
                $country->phone_code = $request->phone_code;

                $country->save();

                $this->helper->flash_message('success', 'Updated Successfully'); // Call flash message function

                return redirect('admin/country');
            }
        }
        else
        {
            return redirect('admin/country');
        }
    }

    /**
     * Delete Country
     *
     * @param array $request    Input values
     * @return redirect     to Country View
     */
    public function delete(Request $request)
    {
        $country_code = Country::find($request->id)->short_name;
        Country::find($request->id)->delete();
        $this->helper->flash_message('success', 'Deleted Successfully'); // Call flash message function
        return redirect('admin/country');
    }
}
