<?php

/**
 * Trips DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Trips
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\Trips;
use Yajra\DataTables\Services\DataTable;
use DB;

class TripsDataTable extends DataTable
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
            ->addColumn('total_amount', function ($trips) {
                if (LOGIN_USER_TYPE=='company') {
                    return html_entity_decode($trips->currency->symbol).@($trips->driver_or_company_earning);
                }
                else{
                    return html_entity_decode($trips->currency->symbol).@($trips->admin_total_amount);
                }
            })
            ->addColumn('action', function ($trips) {
                $edit = '<a href="'.url(LOGIN_USER_TYPE.'/view_trips/'.$trips->id).'" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i></a>';

                return $edit;
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
        $trips =  $result = $model->
                        join('users as rider', function($join) {
                                $join->on('rider.id', '=', 'trips.user_id');
                            })
                        ->join('currency', function($join) {
                                $join->on('currency.code', '=', 'trips.currency_code');
                            })
                        ->join('car_type', function($join) {
                                $join->on('car_type.id', '=', 'trips.car_id');
                            })
                        ->leftJoin('users as driver', function($join) {
                                $join->on('driver.id', '=', 'trips.driver_id');
                            })
                        ->leftJoin('companies', function($join) {
                                $join->on('driver.company_id', '=', 'companies.id');
                            })
                        ->select(['trips.id as id','trips.begin_trip as begin_trip','trips.pickup_location as pickup_location','trips.drop_location as drop_location', 'driver.first_name as driver_name', 'rider.first_name as rider_name',  DB::raw('CONCAT(currency.symbol, trips.total_fare) AS total_amount'), 'trips.total_fare as total','trips.status as status','car_type.car_name as car_name', 'trips.created_at as trip_date', 'trips.updated_at as updated_at', 'trips.*', 'companies.name as company_name']);
        //If login user is company then get that company drivers only
        if (LOGIN_USER_TYPE=='company') {
            $trips = $trips->whereHas('driver',function($q){
                $q->where('company_id',auth('company')->user()->id);
            });
        }
        return $trips;
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
        $col_list_1 = [
            ['data' => 'id', 'name' => 'trips.id', 'title' => 'Id'],
            ['data' => 'driver_name', 'name' => 'driver.first_name', 'title' => 'Driver Name'],
            ['data' => 'rider_name', 'name' => 'rider.first_name', 'title' => 'Rider Name'],
        ];

        $col_list_2 = [
            ['data' => 'pickup_location', 'name' => 'trips.pickup_location', 'title' => 'Pickup Location'],
            ['data' => 'drop_location', 'name' => 'trips.drop_location', 'title' => 'Drop Location'],
            ['data' => 'trip_date', 'name' => 'trips.created_at', 'title' => 'Trip Date'],
        ];

        $col_list_3 = [
            ['data' => 'car_name', 'name' => 'car_type.car_name', 'title' => 'Vehicle Details'],
            ['data' => 'status', 'name' => 'trips.status', 'title' => 'Status'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'exportable' => false],
        ];

        if(LOGIN_USER_TYPE == 'company') {
            $payout_columns = array(
                ['data' => 'total_amount', 'name' => 'total_fare', 'title' => 'Earned'],
            );              
            $company_columns = array();                
        }
        else {
            $payout_columns = array(
                ['data' => 'total_amount', 'name' => 'total_fare', 'title' => 'Earned'],
            );
            $company_columns = array(
                ['data' => 'company_name', 'name' => 'companies.name', 'title' => 'Company Name']
            );
        }

        return array_merge($col_list_1,$payout_columns,$col_list_2,$company_columns,$col_list_3);
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'trips_' . date('YmdHis');
    }
}