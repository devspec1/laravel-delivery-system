<?php

/**
 * Payout Reports DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Payout Reports
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\Trips;
use Yajra\DataTables\Services\DataTable;
use DB;

class PayoutReportsDataTable extends DataTable
{
    protected $filter_type,$from,$to,$date;

    // Set the Type of Filter applied to Payout
    public function setFilter($filter_type)
    {
        $this->filter_type = $filter_type;
        return $this;
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->of($query)
            ->addColumn('day', function ($trips) {
                return date('l', strtotime($trips->created_at));
            })
            ->addColumn('total_fare', function ($trips) {
            	return currency_symbol().$trips->admin_total_amount;
            })
            ->addColumn('driver_payout', function ($trips) {
                $payment_pending_trips = Trips::DriverPayoutTripsOnly()->where('driver_id',$trips->driver_id)->whereDate('created_at', date('Y-m-d',strtotime($trips->created_at)));
                if($this->filter_type == 'day_report') {
                    $payment_pending_trips = $payment_pending_trips->where('created_at', $trips->created_at);
                }
                $total_payout = $payment_pending_trips->get()->sum('driver_payout');
                return currency_symbol().$total_payout;
            })
            ->addColumn('action', function ($trips) {
                $payout_credentials = $trips->driver->default_payout_credentials;
                $payout_text = (LOGIN_USER_TYPE == 'company') ? 'Paid' : 'Make Payout';
                $payout_data['has_payout_data'] = true;
                if($payout_credentials == '') {
                    $payout_data['has_payout_data'] = false;
                    $payout_data['payout_message'] = "Yet, Driver doesnt enter his Payout details.";
                }
                else if($payout_credentials->type == 'BankTransfer') {
                    $payout_data['Payout Account Number'] = $payout_credentials->payout_id;
                    $payout_data['Account Holder Name'] = $payout_credentials->payout_preference->holder_name;
                    $payout_data['Bank Name'] = $payout_credentials->payout_preference->bank_name;
                    $payout_data['Bank Location'] = $payout_credentials->payout_preference->bank_location;
                    $payout_data['Bank Code'] = $payout_credentials->payout_preference->branch_code;
                }
                else if($payout_credentials->type == 'Stripe') {
                    $payout_data['Payout Account Number'] = $payout_credentials->payout_id;
                }
                else if($payout_credentials->type == 'Paypal') {
                    $payout_data['Paypal Email'] = $payout_credentials->payout_id;
                }

                $driver_payout = '<a data-href="#" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#payout-details" data-payout_details=\''.json_encode($payout_data).'\'><i class="glyphicon glyphicon-list-alt"></i></a>&nbsp;';
                
                if($this->filter_type == 'day_report') {
                    $action_url = url(LOGIN_USER_TYPE.'/view_trips/'.$trips->id).'?source=reports';
                    $payment_action = '<form action="'.url(LOGIN_USER_TYPE.'/make_payout').'" method="post" name="payout_form" style="display:inline-block">
                        <input type="hidden" name="type" value="driver_trip">
                        <input type="hidden" name="_token" value="'.csrf_token().'">
                        <input type="hidden" name="trip_id" value="'.$trips->id.'">
                        <input type="hidden" name="driver_id" value="'.$trips->driver_id.'">
                        <input type="hidden" name="redirect_url" value="'.LOGIN_USER_TYPE.'/per_day_report/'.$trips->driver_id.'/'.request()->date.'">
                        <button type="submit" class="btn btn-xs btn-primary" name="submit" value="submit"> '.$payout_text.' </button>
                        
                        </form>';
                }
                else {
                    $action_url = url(LOGIN_USER_TYPE.'/per_day_report/'.$trips->driver_id).'/'.date('Y-m-d',strtotime($trips->created_at));
                    $payment_action = '<form action="'.url(LOGIN_USER_TYPE.'/make_payout').'" method="post" name="payout_form" style="display:inline-block">
                        <input type="hidden" name="type" value="driver_day">
                        <input type="hidden" name="_token" value="'.csrf_token().'">
                        <input type="hidden" name="driver_id" value="'.$trips->driver_id.'">
                        <input type="hidden" name="day" value="'.date('Y-m-d',strtotime($trips->created_at)).'">
                        <input type="hidden" name="redirect_url" value="'.LOGIN_USER_TYPE.'/per_week_report/'.$trips->driver_id.'/'.request()->start_date.'/'.request()->end_date.'">
                        <button type="submit" class="btn btn-xs btn-primary make-pay-btn" name="submit" value="submit"> '.$payout_text.' </button>
                        
                        </form>';
                }
                
                if($payout_credentials == '') {
                    $payment_action = '<button type="button" class="btn btn-xs btn-primary" disabled> '.$payout_text.' </button>';
                }

                return '<div>'.'<a href="'.$action_url.'" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a> '.$driver_payout.''.$payment_action.'<div>';
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Trips $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Trips $model)
    {
        $this->from = date('Y-m-d' . ' 00:00:00', strtotime(request()->start_date));
        $this->to = date('Y-m-d' . ' 23:59:59', strtotime(request()->end_date));
        $this->date = date('Y-m-d', strtotime(request()->date));

        $driver_id = request()->driver_id;

        $trips = $model->with(['currency','driver'])->DriverPayoutTripsOnly()->Where('driver_id',$driver_id);

        if($this->filter_type == 'day_report') {
            $trips->whereDate('created_at', $this->date);
        }
        else {
            $trips->whereBetween('created_at', [$this->from, $this->to])->groupBy(DB::raw('DATE(created_at)'));
        }

        return $trips->get();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->columns($this->getColumns())
                    ->addAction()
                    ->minifiedAjax()
                    ->dom('lBfr<"table-responsive"t>ip')
                    ->orderBy(0,'DESC')
                    ->buttons(
                        ['csv', 'excel', 'print', 'reset']
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        if($this->filter_type == 'day_report') {
            return array(
                ['data' => 'id', 'name' => 'id', 'title' => 'Trip Id'],
                ['data' => 'total_fare', 'name' => 'total_fare', 'title' => 'Total Fare'],
                ['data' => 'driver_payout', 'name' => 'driver_payout', 'title' => 'Payout Amount'],
                ['data' => 'payment_status', 'name' => 'payment_status', 'title' => 'Payment Status']
            );
        }
        return array(
            ['data' => 'day', 'name' => 'created_at', 'title' => 'Day'],
            ['data' => 'driver_payout', 'name' => 'driver_payout', 'title' => 'Payout Amount'],
        );
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'driver_payouts_' . date('YmdHis');
    }
}