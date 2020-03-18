<?php

/**
 * Promo Code Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Promo Code
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\DataTables\PromoCodeDataTable;
use App\Models\PromoCode;
use App\Models\Currency;
use App\Models\UsersPromoCode;
use App\Http\Start\Helpers;
use Validator;

class PromocodeController extends Controller
{
    protected $helper;  // Global variable for instance of Helpers

    public function __construct()
    {
        $this->helper = new Helpers;
    }

    /**
     * Load Datatable for Promo Code
     *
     * @param array $dataTable  Instance of CouponCodeDataTable
     * @return datatable
     */
    public function index(PromoCodeDataTable $dataTable)
    {
        return $dataTable->render('admin.promo_code.view');
    }

    /**
     * Add a New Promo Code
     *
     * @param array $request  Input values
     * @return redirect     to Promo Code view
     */
    public function add(Request $request)
    {
        $data['currency'] = Currency::codeSelect();
        $data['promo_currency'] = Currency::where('default_currency','1')->first()->id;

        if(!$_POST)
        {
            return view('admin.promo_code.add',$data);
        }
        else if($request->submit)
        {

            $rules = array(
                    'code'          => 'required|regex:/(^[A-Za-z0-9 ]+$)+/|min:4|max:12|unique:promo_code,code',
                    'amount'        => 'required|numeric|digits_between:1,4',
                    'expire_date'   => 'required',
                    'currency_code' => 'required',
                    'status'        => 'required'
                    );
            
            $niceNames = array(
                        'code'          => 'Promo Code',
                        'amount'        => 'Amount',
                        'currency_code' => 'Currency code',
                        'expire_date'   => 'Expired Date',
                        'status'        => 'Status'
                        );

            $message=array(
                    'code.regex' =>'Special Characters not allowed.'
                );

            $validator = Validator::make($request->all(), $rules,$message);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else
            {   
                
                // $currency_code = Currency::where('id',$request->promo_currency)->first()->code;

                $promo = new PromoCode;

                $promo->code           = $request->code;
                $promo->amount         = $request->amount;
                $promo->expire_date    = date('Y-m-d', strtotime($request->expire_date));
                $promo->currency_code  = $request->currency_code;
                $promo->status         = $request->status;

                $promo->save();

                $this->helper->flash_message('success', 'Added Successfully'); // Call flash message function

                return redirect('admin/promo_code');
            }
        }
        else
        {
            return redirect('admin/promo_code');
        }
    }

    /**
     * Update Promo Code Details
     *
     * @param array $request    Input values
     * @return redirect     to Promo Code View
     */
    public function update(Request $request)
    {   
        $data['result'] = PromoCode::find($request->id);
    
        $data['currency'] = Currency::codeSelect();

        $data['promo_currency'] = Currency::where('code',$data['result']->currency_code)->first()->id;


        if(!$_POST)
        {

            return view('admin.promo_code.edit', $data);
        }
        else if($request->submit)
        {
            $rules = array(
                    'code'          => 'required|regex:/(^[A-Za-z0-9 ]+$)+/|min:4|max:12|unique:promo_code,code,'.$request->id,
                    'amount'        => 'required|numeric|digits_between:1,4',
                    'expire_date'   => 'required',
                    'currency_code' => 'required',
                    'status'        => 'required'
                    );

            $niceNames = array(
                        'code'          => 'Promo Code',
                        'amount'        => 'Amount',
                        'currency_code' => 'Currency code',
                        'expire_date'   => 'Expired Date',
                        'status'        => 'Status'
                        );

            $message=array(
                    'code.regex' =>'Special Characters not allowed.'
                );

            $validator = Validator::make($request->all(), $rules,$message);
            $validator->setAttributeNames($niceNames); 

            if ($validator->fails()) 
            {
                return back()->withErrors($validator)->withInput(); // Form calling with Errors and Input values
            }
            else
            {   
                // $currency_code = Currency::where('id',$request->promo_currency)->first()->code;

                $promo = PromoCode::find($request->id);

                $promo->code           = $request->code;
                $promo->amount         = $request->amount;
                $promo->expire_date    = date('Y-m-d',strtotime($request->expire_date));
                $promo->currency_code  = $request->currency_code;
                $promo->status         = $request->status;

                $promo->save();

                $this->helper->flash_message('success', 'Update Successfully'); // Call flash message function

                return redirect('admin/promo_code');

            }
        }
        else
        {
            return redirect('admin/promo_code');
        }
    }

    /**
     * Delete Promo Code
     *
     * @param array $request    Input values
     * @return redirect     to Promo Code View
     */
    public function delete(Request $request)
    {
            UsersPromoCode::where('promo_code_id',$request->id)->delete();
            PromoCode::find($request->id)->delete();
            
            $this->helper->flash_message('success', 'Deleted Successfully'); // Call flash message function

        return redirect('admin/promo_code');
    }
}
