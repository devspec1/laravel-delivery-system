<?php

/**
 * Currency Controller
 *
 * @package     Makent
 * @subpackage  Controller
 * @category    Currency
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataTables\CurrencyDataTable;
use App\Models\Currency;
use App\Models\User;
use App\Models\SiteSettings;
use App\Http\Start\Helpers;
use Validator;

class CurrencyController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load Datatable for Currency
     *
     * @param array $dataTable  Instance of CurrencyDataTable
     * @return datatable
     */
    public function index(CurrencyDataTable $dataTable)
    {
        return $dataTable->render('admin.currency.view');
    }

    /**
     * Add a New Currency
     *
     * @param array $request  Input values
     * @return redirect     to Currency view
     */
    public function add(Request $request)
    {
        if(!$_POST)
        {
            return view('admin.currency.add');
        }
        else if($request->submit)
        {
            $rules = array(
                    'name'   => 'required|unique:currency',
                    'code'   => 'required|unique:currency',
                    'symbol' => 'required',
                    'rate'   => 'required|numeric|min:0.01',
                    'status' => 'required'
                    );

            $niceNames = array(
                        'name'   => 'Name',
                        'code'   => 'Code',
                        'symbol' => 'Symbol',
                        'rate'   => 'Rate',
                        'status' => 'Status'
                        );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else
            {
                $currency = new Currency;

                $currency->name   = $request->name;
                $currency->code   = $request->code;
                $currency->symbol = $request->symbol;
                $currency->rate   = $request->rate;
                $currency->default_currency = '0';
                $currency->status = $request->status;

                $currency->save();

                $this->helper->flash_message('success', 'Added Successfully'); // Call flash message function

                return redirect('admin/currency');
            }
        }
        else
        {
            return redirect('admin/currency');
        }
    }

    /**
     * Update Currency Details
     *
     * @param array $request    Input values
     * @return redirect     to Currency View
     */
    public function update(Request $request)
    {
        if(!$_POST)
        {
			$data['result'] = Currency::find($request->id);
            if(!$data['result'])
            {
                $this->helper->flash_message('danger', 'Invalid ID'); // Call flash message function
                return redirect('admin/currency');
            }
            return view('admin.currency.edit', $data);
        }
        else if($request->submit)
        {
            $rules = array(
                    'name'   => 'required|unique:currency,name,'.$request->id,
                    'code'   => 'required|unique:currency,code,'.$request->id,
                    'symbol' => 'required',
                    'rate'   => 'required|numeric|min:0.01',
                    'status' => 'required'
                    );

            $niceNames = array(
                        'name'   => 'Name',
                        'code'   => 'Code',
                        'symbol' => 'Symbol',
                        'rate'   => 'Rate',
                        'status' => 'Status'
                        );

            $validator = Validator::make($request->all(), $rules);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else
            {
                $currency = Currency::find($request->id);

                if($request->status == 'Inactive' || $request->code != $currency->code)
                {
                    $result= $this->canDestroy($currency->id, $currency->code);
                    if($result['status'] == 0)
                    {
                        $this->helper->flash_message('error',$result['message']);
                        return back();
                    }
                }

			    $currency->name   = $request->name;
                $currency->code   = $request->code;
                $currency->symbol = $request->symbol;
                $currency->rate   = $request->rate;
                $currency->status = $request->status;
                try
                {
                    $currency->save();
                }
                catch(\Exception $e)
                {
                    $this->helper->flash_message('error','Sorry this currency is already in use. So canont update the code.');
                    return back();
                }

                $this->helper->flash_message('success', 'Updated Successfully'); // Call flash message function

                return redirect('admin/currency');
            }
        }
        else
        {
            return redirect('admin/currency');
        }
    }

    /**
     * Delete Currency
     *
     * @param array $request    Input values
     * @return redirect     to Currency View
     */
    public function delete(Request $request)
    {
        $currency = Currency::find($request->id);

       $default_currency = Currency::where('default_currency','1')->first();
        
        $result= $this->canDestroy($currency->id, $currency->code);
        if($result['status'] == 0)
        {
            $this->helper->flash_message('error',$result['message']);
            return back();
        }
        try
         {   
             // Default Currency Apply when delete the user currency 

             User::where('currency_code',$currency->code)
             ->update(['currency_code' => $default_currency->code]);
      
            Currency::find($request->id)->delete();
        }
        catch(\Exception $e)
        {
            $this->helper->flash_message('error','Sorry this currency is already in use. So canont delete.');
            return back();
        }
        $this->helper->flash_message('success', 'Deleted Successfully'); // Call flash message function

        return redirect('admin/currency');
    }

    public function canDestroy($id, $code)
    {
        $active_currency_count = Currency::where('status', 'Active')->count();
        $is_default_currency = Currency::find($id)->default_currency;
        $payment_currency = site_settings('payment_currency');

        $return  = ['status' => '1', 'message' => ''];
        if($active_currency_count < 1)
        {
            $return = ['status' => 0, 'message' => 'Sorry, Minimum one Active currency is required.'];
        }
        else if($is_default_currency == 1)
        {
            $return = ['status' => 0, 'message' => 'Sorry, This currency is Default Currency. So, change the Default Currency.'];
        }
        else if($payment_currency == $code)
        {
            $return = ['status' => 0, 'message' => 'Sorry, This currency is Payment Currency. So, change the Payment Currency.'];
        }

        return $return;
    }
}
