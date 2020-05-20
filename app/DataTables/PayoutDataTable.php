<?php

/**
 * Payouts DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Payouts
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\Trips;
use Yajra\DataTables\Services\DataTable;
use DB;

class PayoutDataTable extends DataTable
{
    protected $filter_type;

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
            ->addColumn('driver_payout', function ($trips) {
                $payment_pending_trips = Trips::DriverPayoutTripsOnly()->where('driver_id',$trips->driver_id);
                
                if($this->filter_type == 'Weekly') {
                    $week_days = getWeekStartEnd($trips->created_at,'Y-m-d H:i:s');
                    $payment_pending_trips = $payment_pending_trips->whereBetween('trips.created_at', [$week_days['start'], $week_days['end']]);
                }

                $total_payout = $payment_pending_trips->get()->sum('driver_payout');
                return currency_symbol().$total_payout;
            })
            ->addColumn('week_day', function ($trips) {
                $week_days = getWeekStartEnd($trips->created_at);
                return $week_days['start'].' - '.$week_days['end'];
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

                if($this->filter_type == 'OverAll') {
                    $action = '<a href="'.url(LOGIN_USER_TYPE.'/weekly_payout/'.$trips->driver_id).'" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>';
                    $payment_action = '<form action="'.url(LOGIN_USER_TYPE.'/make_payout').'" method="post" name="payout_form" style="display:inline-block">
                        <input type="hidden" name="type" value="driver_overall">
                        <input type="hidden" name="_token" value="'.csrf_token().'">
                        <input type="hidden" name="driver_id" value="'.$trips->driver_id.'">
                        <input type="hidden" name="redirect_url" value="'.LOGIN_USER_TYPE.'/payout/overall">
                        <button type="submit" class="btn btn-xs btn-primary" name="submit" value="submit"> '.$payout_text.' </button>                      
                        </form>';
                }
                else if($this->filter_type == 'Weekly') {
                    $week_days = getWeekStartEnd($trips->created_at,'Y-m-d');
                    $action = '<a href="'.url(LOGIN_USER_TYPE.'/per_week_report/'.$trips->driver_id).'/'.$week_days['start'].'/'.$week_days['end'].'" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>';
                    $payment_action = '<form action="'.url(LOGIN_USER_TYPE.'/make_payout').'" method="post" name="payout_form" style="display:inline-block">
                        <input type="hidden" name="type" value="driver_weekly">
                        <input type="hidden" name="_token" value="'.csrf_token().'">
                        <input type="hidden" name="driver_id" value="'.$trips->driver_id.'">
                        <input type="hidden" name="start_date" value="'.$week_days['start'].'">
                        <input type="hidden" name="end_date" value="'.$week_days['end'].'">
                        <input type="hidden" name="redirect_url" value="'.LOGIN_USER_TYPE.'/weekly_payout/'.$trips->driver_id.'">
                        <button type="submit" class="btn btn-xs btn-primary" name="submit" value="submit"> '.$payout_text.' </button>
                        
                        </form>';
                }
                if($payout_credentials == '') {
                    $payment_action = '<button type="button" class="btn btn-xs btn-primary" disabled> '.$payout_text.' </button>';
                }
                
                return '<div>'.$action.' '.$driver_payout.' '.$payment_action.'</div>';
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
        $driver_id = request()->driver_id;

        $trips = $model->DriverPayoutTripsOnly()->with(['currency','driver']);

/*        dd($this->filter_type);*/

        if($this->filter_type == 'Weekly') {
            $trips = $trips->where('driver_id',$driver_id)->groupBy(DB::raw('WEEK(created_at,1)'));
        }
        else if($this->filter_type == 'OverAll') {

            $trips = $trips->groupBy('driver_id');
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
        if($this->filter_type == 'Weekly') {
            return array(
                ['data' => 'driver_id', 'name' => 'driver_id', 'title' => 'Driver Id'],
                ['data' => 'week_day', 'name' => 'week_day', 'title' => 'Week Day'],
                ['data' => 'driver_payout', 'name' => 'driver_payout', 'title' => 'Payout Amount'],
            );                
        }

        return array(
            ['data' => 'driver_id', 'name' => 'driver_id', 'title' => 'Driver Id'],
            ['data' => 'driver_name', 'name' => 'driver_name', 'title' => 'Driver Name'],
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
        return 'payouts_' . date('YmdHis');
    }
}