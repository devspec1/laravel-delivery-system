<?php

/**
 * OWE DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    OWE
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\User;
use App\Models\Trips;
use App\Models\DriverOweAmount;
use Yajra\DataTables\Services\DataTable;
use DB;

class OweDataTable extends DataTable
{
    protected $filter_type;

    // Set the value for User Type 
    public function setFilterType($filter_type){
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
            ->addColumn('trip_ids', function ($owe) {
                if(LOGIN_USER_TYPE == 'admin') {
                    $trips_ids = Trips::CompanyTripsOnly($owe->company_id)->whereIn('payment_mode',['Cash & Wallet','Cash'])
                        ->where('owe_amount','>','0')
                        ->get()
                        ->pluck('id')
                        ->toArray();
                }
                else {
                    $trips_ids = $owe->driver_trips->whereIn('payment_mode',['Cash & Wallet','Cash'])->whereIn('status',['Payment','Completed'])->pluck('id')->toArray();
                }

                return '<div class="min_width">'.implode(',', $trips_ids).'</div>';
            })
            ->addColumn('owe_amount', function ($owe) {
                if(LOGIN_USER_TYPE == 'admin'){
                    $owe_amount = $owe->company->total_owe_amount;
                }
                else
                    $owe_amount = $owe->driver_trips->sum('owe_amount');

                return currency_symbol().number_format($owe_amount,2,'.','');
            })
            ->addColumn('applied_owe_amount', function ($owe) {
                if(LOGIN_USER_TYPE == 'admin'){
                    $applied_owe_amount = $owe->company->applied_owe_amount;
                }
                else
                    $applied_owe_amount = $owe->driver_trips->sum('applied_owe_amount');
                return currency_symbol().number_format($applied_owe_amount,2,'.','');
            })
            ->addColumn('remaining_owe_amount', function ($owe) {
                if(LOGIN_USER_TYPE == 'admin') {
                    $remaining_owe_amount = $owe->company->remaining_owe_amount;
                }
                else {
                    $remaining_owe_amount = $owe->driver_trips->sum('owe_amount') - $owe->driver_trips->sum('applied_owe_amount');
                }
                return currency_symbol().number_format($remaining_owe_amount,2,'.','');
            })
            ->addColumn('action', function ($owe) {
                if($owe->company_id != '1') {
                    $remaining_owe_amount = DriverOweAmount::whereHas('user',function($q) use ($owe) {
                        $q->where('company_id',$owe->company_id);
                    })->get()->sum('amount');

                    $paid_btn = '<form action="'.route('update_company_payment').'" method="POST">
                                <input type="hidden" name="company_id" value="'.$owe->company_id.'">
                                <input type="hidden" name="_token" value="'.csrf_token().'">
                                <button type="submit" class="btn btn-xs btn-primary"> Paid </button>
                                </form>';

                    return $remaining_owe_amount == 0 ? '' : $paid_btn;
                }
                $view = '<a href="'.url(LOGIN_USER_TYPE.'/company_owe/'.$owe->company_id).'" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>';

                return $view;
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
        $owe = $model->with('company')
                ->where(function($query)  {
                    if(LOGIN_USER_TYPE=='company') {
                        //If login user is company then get that company drivers only
                        $query->where('company_id',auth('company')->user()->id);
                    }
                })
                ->join('trips', function($join) {
                    $join->on('users.id', '=', 'trips.driver_id');
                })
                ->leftJoin('companies', function($join) {
                    $join->on('users.company_id', '=', 'companies.id');
                })
                ->select('trips.id as trip_id','users.id As id', 'users.first_name', 'users.last_name','users.email','trips.currency_code as currency_code',DB::raw("GROUP_CONCAT(trips.id) as trip_ids"),DB::raw('SUM(trips.owe_amount) as owe_amount'),DB::raw('SUM(trips.remaining_owe_amount) as remaining_owe_amount'),DB::raw('SUM(trips.applied_owe_amount) as applied_owe_amount'),'companies.name as driver_company_name','companies.id as company_id');
        if($this->filter_type == 'applied') {
            $owe = $owe->where('applied_owe_amount','>','0');
        }
        else {
            $owe = $owe->where('owe_amount','>','0');
        }

        if(LOGIN_USER_TYPE=='company') {
            $owe = $owe->groupBy('id');
        }
        else {
            $owe = $owe->groupBy('company_id');
        }
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
                    ->addAction()
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
        $owe_columns = array();

        if(LOGIN_USER_TYPE == 'admin') {
            $columns = array(
                ['data' => 'company_id', 'name' => 'companies.id', 'title' => 'Company Id'],
                ['data' => 'driver_company_name', 'name' => 'companies.name', 'title' => 'Company Name'],
                ['data' => 'trip_ids', 'name' => 'trip_ids', 'title' => 'Trip Ids','orderable' => false, 'searchable' => false],
                ['data' => 'owe_amount', 'name' => 'owe_amount', 'title' => 'Owe Amount', 'orderable' => false],
            );

            $owe_columns = array(
                ['data' => 'remaining_owe_amount', 'name' => 'remaining_owe_amount', 'title' => 'Remaining Owe Amount', 'orderable' => false]
            );
        }
        else {
            $columns = array(
                ['data' => 'id', 'name' => 'users.id', 'title' => 'Driver Id'],
                ['data' => 'first_name', 'name' => 'users.first_name', 'title' => 'First Name'],
                ['data' => 'trip_ids', 'name' => 'trip_ids', 'title' => 'Trip Ids','orderable' => false, 'searchable' => false],
            );
            if($this->filter_type != 'applied') {
                $owe_columns = array(['data' => 'owe_amount', 'name' => 'owe_amount', 'title' => 'Owe Amount', 'orderable' => false]);
            }
        }
        return array_merge($columns, $owe_columns);
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