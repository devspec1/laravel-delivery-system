<?php

/**
 * Trips Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Trips
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\CompanyPayoutDataTable;
use App\DataTables\CompanyPayoutReportsDataTable;
use App\Models\Payout;
use App\Models\User;
use App\Models\SiteSettings;
use App\Models\Trips;
use App\Models\Payment;
use App\Models\Currency;
use App\Models\PaymentGateway;
use DB;

class CompanyPayoutController extends Controller
{    
    /**
    * View Over All Payout Details of All Drivers
    *
    * @param array $dataTable  Instance of PayoutDataTable DataTable
    * @return datatable
    */
    public function overall_payout(CompanyPayoutDataTable $dataTable)
    {
        $data['payout_title'] = 'Payouts';
        $data['sub_title'] = 'Payouts';
        return $dataTable->setFilter('OverAll')->render('admin.company_payouts.view',$data);
    }

    /**
    * View Weekly Payout Details of Drivers
    *
    * @param array $dataTable  Instance of PayoutDataTable DataTable
    * @return datatable
    */
    public function weekly_payout(CompanyPayoutDataTable $dataTable)
    {
        $company_id = request()->company_id;
        $data['payout_title'] = 'Weekly Payout for : '.$company_id;
        $data['sub_title'] = 'Payouts';

        return $dataTable->setFilter('Weekly')->render('admin.company_payouts.view',$data);
    }

    /**
    * View Week Day Payout Details of Drivers
    *
    * @param array $dataTable  Instance of CompanyPayoutReportsDataTable DataTable
    * @return datatable
    */
    public function payout_per_week_report(CompanyPayoutReportsDataTable $dataTable)
    {       
        $from = date('Y-m-d' . ' 00:00:00', strtotime(request()->start_date));
        $to = date('Y-m-d' . ' 23:59:59', strtotime(request()->end_date));
        $data['payout_title'] = 'Payout Details : '.request()->start_date.' to '.request()->end_date;
        $data['sub_title'] = 'Payout Details';
        return $dataTable->setFilter('week_report')->render('admin.company_payouts.view',$data);
    }

    /**
    * View Daily Payout Details of Drivers
    *
    * @param array $dataTable  Instance of CompanyPayoutReportsDataTable DataTable
    * @return datatable
    */
    public function payout_per_day_report(CompanyPayoutReportsDataTable $dataTable)
    {       
        $date = date('Y-m-d' . ' 00:00:00', strtotime(request()->date));
        $data['payout_title'] = 'Payout Details : '.request()->date;
        $data['sub_title'] = 'Payout Details';

        return $dataTable->setFilter('day_report')->render('admin.company_payouts.view',$data);
    }

    /**
    * Make Payout to driver based on the type of payout
    *
    * @param  \Illuminate\Http\Request  $request
    * 
    */
    public function payout_to_company(Request $request)
    {
        $type           = $request->type;
        $redirect_url   = $request->redirect_url;
        $trip_currency  = view()->shared('default_currency'); 
        $trip_currency  = $trip_currency->code;

        try {
            if($type == 'company_trip') {
                $trip_id            = $request->trip_id;
                $trip_details       = Trips::CompanyPayoutTripsOnly()->select('trips.*')->find($trip_id);
                $trip_currency      = $trip_details->currency_code;
                $trip_amount        = $trip_details->driver_payout;
                $payout_details     = $trip_details->driver->company->default_payout_credentials;
                $trip_ids           = array($trip_id);
                
            }
            else if($type == 'company_day') {
                $trip_details       = Trips::CompanyPayoutTripsOnly()->select('trips.*')
                ->whereHas('driver',function($q) use ($request){
                    $q->where('company_id',$request->company_id);
                })
                ->whereDate('trips.created_at',$request->day)->get();

                $trip_amount        = $trip_details->sum('driver_payout');
                $trip_ids           = $trip_details->pluck('id')->toArray();

                $payout_details     = $trip_details[0]->driver->company->default_payout_credentials;
                
            }
            else if($type == 'company_weekly') {
                $start_date = date('Y-m-d '.'00:00:00',strtotime($request->start_date));
                $end_date = date('Y-m-d '.'23:59:59',strtotime($request->end_date));

                $trip_details       = Trips::CompanyPayoutTripsOnly()->select('trips.*')
                ->whereHas('driver',function($q) use ($request){
                    $q->where('company_id',$request->company_id);
                })
                ->whereBetween('trips.created_at', [$start_date, $end_date])->get();
                
                $trip_amount        = $trip_details->sum('driver_payout');
                $trip_ids           = $trip_details->pluck('id')->toArray();

                $payout_details     = $trip_details[0]->driver->company->default_payout_credentials;

            }
            else if($type == 'company_overall') {
                $trip_details       = Trips::CompanyPayoutTripsOnly()
                ->select('trips.*')
                ->whereHas('driver',function($q) use ($request){
                    $q->where('company_id',$request->company_id);
                })->get();

                $trip_amount        = $trip_details->sum('driver_payout');
                $trip_ids           = $trip_details->pluck('id')->toArray();

                $payout_details     = $trip_details[0]->driver->company->default_payout_credentials;
            }
            else {
                flashMessage('danger', 'Invalid Request.Please Try Again.');
                return back();
            }
        }
        catch (\Exception $e) {
            flashMessage('danger', 'Invalid Request.Please Try Again.');
            return back();
        }

        if(count($trip_ids) == 0 || $trip_amount <= 0) {
            flashMessage('danger', 'Invalid Request.Please Try Again.');
            return back();
        }

        if($payout_details == null) {
            flashMessage('danger', 'Yet, Company doesn\'t enter his Payout details. Cannot Make Payout.');
            return back();
        }

        $payout_data = array();
        if($payout_details->type == 'Paypal') {
            $payout_currency = site_settings('payment_currency');
            $amount = currencyConvert($trip_currency, $payout_currency, $trip_amount);
            $data = [
                'sender_batch_header' => [
                    'email_subject' => urlencode('PayPal Payment'),    
                ],
                'items' => [
                    [
                        'recipient_type' => "EMAIL",
                        'amount' => [
                            'value' => "$amount",
                            'currency' => "$payout_currency"
                        ],
                        'receiver' => "$payout_details->payout_id",
                        'note' => 'payment of commissions',
                        'sender_item_id' => $trip_ids[0],
                    ],
                ],
            ];
            $payout_data = json_encode($data);
        }
        if($payout_details->type == 'Stripe') {
            $payout_data['currency'] = $payout_details->company_payout_preference->currency_code;
            $payout_data['amount'] = currencyConvert($trip_currency, $payout_data['currency'], $trip_amount);
        }

        $payout_service = resolve('App\Services\Payouts\\'.$payout_details->type.'Payout');
        $pay_result = $payout_service->makePayout($payout_details->payout_id,$payout_data);

        if(!$pay_result['status']) {
            flashMessage('danger','Payout Failed : '.$pay_result['status_message']);
            return redirect($redirect_url);
        }

        $payment_data['admin_transaction_id']   = $pay_result['transaction_id'];
        $payment_data['admin_payout_status']   = isset($pay_result['is_pending']) ? 'Processing':'Paid';

        Payment::whereIn('trip_id',$trip_ids)->update($payment_data);

        flashMessage('success', $pay_result['status_message']);

        return redirect($redirect_url);
    }
}