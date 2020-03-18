<?php

/**
 * Company OWE DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Company OWE
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\User;
use App\Models\DriverOweAmountPayment;
use App\Models\DriverOweAmount;
use Yajra\DataTables\Services\DataTable;
use DB;

class CompanyOweDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $default_currency = view()->shared('default_currency');
        $this->currency_symbol = html_entity_decode($default_currency->symbol);
        return datatables()
            ->of($query)
            ->addColumn('trip_ids', function ($owe) {
                return '<div class="min_width">'.$owe->trip_ids.'</div>';
            })
            ->addColumn('owe_amount', function ($owe) {
                $owe_amount = $owe->total_owe_amount;

                return $this->currency_symbol.number_format($owe_amount,2,'.','');
            })
            ->addColumn('applied_owe_amount', function ($owe) {
                $applied_owe_amount = $owe->trip_applied_owe_amount;
                return $this->currency_symbol.number_format($applied_owe_amount,2,'.','');
            })
            ->addColumn('paid_amount', function ($owe) {
                $paid_amount = $owe->paid_amount;
                return $this->currency_symbol.number_format($paid_amount,2,'.','');
            })
            ->addColumn('remaining_owe_amount', function ($owe) {
                return $this->currency_symbol.number_format($owe->remaining_owe_amount,2,'.','');
            })
            ->addColumn('action', function ($owe) {
                $remaining_owe_amount = $owe->remaining_owe_amount;

                $paid_btn = '<form action="'.route('update_owe_payment').'" method="POST">
                            <input type="hidden" name="driver_id" value="'.$owe->id.'">
                            <input type="hidden" name="_token" value="'.csrf_token().'">
                            <button type="submit" class="btn btn-xs btn-primary"> Paid </button>
                            </form>';

                return $remaining_owe_amount == 0 ? '' : $paid_btn;
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
        $company_id = request()->id;
        $owe = $model->join('trips', function($join) {
                    $join->on('users.id', '=', 'trips.driver_id');
                })
                ->where('company_id',$company_id)
                ->whereIn('trips.payment_mode',['Cash & Wallet','Cash'])
                ->where('trips.owe_amount','>','0')
                ->whereIn('trips.status',['Payment','Completed'])
                ->select('trips.id as trip_id','users.id As id', 'trips.driver_id as driver_id', 'users.first_name', 'trips.currency_code as currency_code',DB::raw("GROUP_CONCAT(trips.id) as trip_ids"),DB::raw('SUM(trips.total_fare) as total_fare'))
                ->groupBy('driver_id')
                ->get();

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
                    ->addAction()
                    ->minifiedAjax()
                    ->dom('lBfr<"table-responsive"t>ip')
                    ->orderBy(0)
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
            ['data' => 'id', 'name' => 'id', 'title' => 'Driver Id'],
            ['data' => 'first_name', 'name' => 'first_name', 'title' => 'First Name'],
            ['data' => 'trip_ids', 'name' => 'trip_ids', 'title' => 'Trip Ids','orderable' => false, 'searchable' => false],
            ['data' => 'owe_amount', 'name' => 'owe_amount', 'title' => 'Owe Amount'],
            ['data' => 'paid_amount', 'name' => 'paid_amount', 'title' => 'Paid Amount'],
            ['data' => 'remaining_owe_amount', 'name' => 'remaining_owe_amount', 'title' => 'Remaining Owe Amount'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'owe_' . date('YmdHis');
    }
}