<?php

/**
 * Driver Payment DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Driver Payment
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\User;
use App\Models\Trips;
use App\Models\DriverPayment;
use Yajra\DataTables\Services\DataTable;
use DB;

class DriverPaymentDataTable extends DataTable
{
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
            ->addColumn('trip_ids', function ($trips) {
                return '<div class="min_width">'.$trips->trip_ids.'</div>';
            })
            ->addColumn('user_id', function ($trips) {
                return $trips->driver_id;
            })
            ->addColumn('first_name', function ($trips) {
                return $trips->first_name;
            })
            ->addColumn('cash_trip_amount', function ($trips) {
                $total_fare = $trips->driver_trips->whereIn('payment_mode',['Cash & Wallet','Cash'])
                ->whereIn('status',['Payment','Completed'])->sum('total_fare');
                $driver_payments = DriverPayment::where('driver_id', $trips->driver_id)->first();
                $cash_trip_amount = $total_fare - @$driver_payments->paid_amount;
                $cash_trip_amount = $cash_trip_amount <= 0 ? 0:$cash_trip_amount;

                return currency_symbol().number_format($cash_trip_amount,2,'.','');
            })
            ->addColumn('paid_amount', function ($trips) {
                $driver_payments = DriverPayment::where('driver_id', $trips->driver_id)->first();
                return isset($driver_payments)? currency_symbol().$driver_payments->paid_amount : '-';
            })
            ->addColumn('action', function ($trips) {
                $cash_trip_amount = $trips->driver_trips->whereIn('payment_mode',['Cash & Wallet','Cash'])
                ->whereIn('status',['Payment','Completed'])->sum('total_fare');
                $driver_payments = DriverPayment::where('driver_id', $trips->driver_id)->first();
                $payable_amount = $cash_trip_amount - ($driver_payments->paid_amount ?? 0);
                $payable_amount = $payable_amount <= 0 ? 0 : number_format($payable_amount,2,'.','');

                $paid_btn = '<form action="'.route('update_payment').'" method="GET">
                            <input type="hidden" name="driver_id" value="'.$trips->id.'">
                            <input type="hidden" name="currency_code" value="'.$trips->driver_trips->first()->currency_code.'">
                            <input type="hidden" name="payable_amount" value="'.$payable_amount.'">
                            <button type="submit" class="btn btn-xs btn-primary"> Paid </button>
                            </form>';

                return $payable_amount == 0 ? '' : $paid_btn;
            })
            ->rawcolumns(['trip_ids','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        $owe = User::join('trips', function($join) {
                    $join->on('users.id', '=', 'trips.driver_id');
                })
                ->where('company_id',auth('company')->user()->id)
                ->whereIn('trips.payment_mode',['Cash & Wallet','Cash'])
                ->whereIn('trips.status',['Payment','Completed'])
                ->select('trips.id as trip_id','users.id As id', 'trips.driver_id as driver_id', 'users.first_name', 'trips.currency_code as currency_code',DB::raw("GROUP_CONCAT(trips.id) as trip_ids"),DB::raw('SUM(trips.total_fare) as total_fare'))
                ->groupBy('driver_id');
        return $owe;
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
        return [
            ['data' => 'id', 'name' => 'users.id', 'title' => 'Driver Id'],
            ['data' => 'first_name', 'name' => 'first_name', 'title' => 'First Name'],
            ['data' => 'trip_ids', 'name' => 'trip_ids', 'title' => 'Trip Ids','orderable' => false, 'searchable' => false],
            ['data' => 'cash_trip_amount', 'name' => 'cash_trip_amount', 'title' => 'Amount To Company','orderable' => false],
            ['data' => 'paid_amount', 'name' => 'paid_amount', 'title' => 'Amount Paid','orderable' => false],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'exportable' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'driver_payments_' . date('YmdHis');
    }
}