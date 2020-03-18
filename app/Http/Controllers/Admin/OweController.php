<?php

/**
 * Owe Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Owe Ammount
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\DriverPayment;
use App\Models\DriverOweAmount;
use App\Models\DriverOweAmountPayment;
use App\DataTables\OweDataTable;
use App\DataTables\DriverPaymentDataTable;
use App\DataTables\CompanyOweDataTable;

class OweController extends Controller
{
    public function __construct()
    {
        $this->view_data = array();
    }

    /**
     * Load Datatable for Owe Amount
     *
     * @return view file
     */
    public function index(DriverPaymentDataTable $driver_payment, OweDataTable $owe_amount)
    {
        $this->view_data['main_title'] = 'Owe Amount';
        if(LOGIN_USER_TYPE == 'company') {
            $company = auth()->guard('company')->user();

            $this->view_data['sub_title'] = 'Manage Payment To Company';
            $this->view_data['currency_code'] 		   = currency_symbol();
            $this->view_data['total_owe_amount']       = $company->total_owe_amount;
            $this->view_data['applied_owe_amount']     = $company->applied_owe_amount;
            $this->view_data['remaining_owe_amount']   = $company->remaining_owe_amount;
            return $driver_payment->render('admin.owe.index',$this->view_data);
        }

        return $owe_amount->setFilterType('overall')->render('admin.owe.index',$this->view_data);
    }

    /**
     * Load Datatable for Company Owe
     *
     * @return view file
     */
    public function company_index(CompanyOweDataTable $owe_datatable)
    {
        if(request()->id != 1) {
            abort(404);
        }
        $this->view_data['main_title'] = 'Owe Amount';
        return $owe_datatable->render('admin.owe.index',$this->view_data);
    }

    public function owe_details(OweDataTable $dataTable,Request $request)
    {
        $type = $request->type;
        $this->view_data['main_title']  = ucfirst($type).' Owe Amount';
        return $dataTable->setFilterType($type)->render('admin.owe.index',$this->view_data);
    }

    public function update_payment(Request $request)
    {
        if(!auth()->guard('company')->check()) {
            abort(404);
        }

        $driver_id = $request->driver_id;
        $payable_amount = $request->payable_amount;
        $currency_code = $request->currency_code;

        if($payable_amount <= 0 ) {
            flashMessage('danger', 'Driver Payment Failed.');
            return back();
        }

        $driver_payment = DriverPayment::firstOrNew(['driver_id' => $driver_id]);

        if($driver_payment->paid_amount > 0) {
            $payable_amount = $driver_payment->paid_amount + $payable_amount;
        }
        $driver_payment->driver_id = $driver_id;
        $driver_payment->currency_code = $currency_code;
        $driver_payment->paid_amount = $payable_amount;
        $driver_payment->save();

        flashMessage('success', 'Payment Details Updated.');
        return back();
    }

    public function updateOwePayment(Request $request)
    {
        if(!auth('admin')->check()) {
            abort(404);
        }
        $driver_id = $request->driver_id;

        $driver_owe_amount = DriverOweAmount::where('user_id',$driver_id)->first();
        $payable_amount = $driver_owe_amount->amount;

        if($driver_owe_amount->amount <= 0 ) {
            flashMessage('danger', 'Driver Payment Failed.');
            return back();
        }

        $driver_owe_amount->amount = 0;
        $driver_owe_amount->save();

        $payment = new DriverOweAmountPayment;
        $payment->user_id = $driver_id;
        $payment->transaction_id = "";
        $payment->amount = $payable_amount;
        $payment->status = 1;
        $payment->currency_code = $driver_owe_amount->currency_code;
        $payment->save();
        
        flashMessage('success', 'Payment Details Updated.');
        return back();
    }

    public function update_company_payment(Request $request)
    {
        $company_id = $request->company_id;

        DriverOweAmount::whereHas('user',function($q) use ($company_id){
            $q->where('company_id',$company_id);
        })->update(['amount' => 0]);

        flashMessage('success', 'Payment Details Updated.');
        return back();
    }
}