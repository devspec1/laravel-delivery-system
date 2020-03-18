<?php

/**
 * Payments DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Payments
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\Trips;
use Yajra\DataTables\Services\DataTable;
use DB;

class PaymentsDataTable extends DataTable
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
            ->addColumn('time_fare', function ($trips) {   
                return html_entity_decode(@$trips->currency->symbol).$trips->time_fare;
            })
            ->addColumn('distance_fare', function ($trips) {   
                return html_entity_decode(@$trips->currency->symbol).$trips->distance_fare;
            })
            ->addColumn('base_fare', function ($trips) {   
                return html_entity_decode(@$trips->currency->symbol).$trips->base_fare;
            })
            ->addColumn('driver_payout', function ($trips) {   
                return html_entity_decode(@$trips->currency->symbol).$trips->company_driver_earnings;
            })
            ->addColumn('driver_or_company_commission', function ($trips) {   
                return html_entity_decode(@$trips->currency->symbol).$trips->driver_or_company_commission;
            })
            ->addColumn('total_fare', function ($trips) {   
                return html_entity_decode(@$trips->currency->symbol).$trips->admin_total_amount;
            })
            ->addColumn('access_fee', function ($trips) {   
                return html_entity_decode(@$trips->currency->symbol).$trips->access_fee;
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
        $trips = Trips::where(function($query)  {
                            if(LOGIN_USER_TYPE=='company') {
                                $query->whereHas('driver',function($q1){
                                    $q1->where('company_id',auth('company')->user()->id);
                                });
                            }
                        })
                        ->join('currency', function($join) {
                                $join->on('currency.code', '=', 'trips.currency_code');
                            })
                        ->leftJoin('users as u', function($join) {
                                $join->on('u.id', '=', 'trips.driver_id');
                            })
                        ->leftJoin('users as rider', function($join) {
                            $join->on('rider.id', '=', 'trips.user_id');
                        })
                        ->leftJoin('companies', function($join) {
                            $join->on('u.company_id', '=', 'companies.id');
                        })
                        ->select(['trips.id as id','trips.begin_trip as begin_trip', 'u.first_name as driver_name','rider.first_name as rider_name','trips.time_fare AS time_fare', 'trips.distance_fare AS distance_fare','trips.base_fare AS base_fare','trips.tips AS tips','trips.toll_fee AS toll_fee','trips.access_fee AS access_fee','trips.total_fare AS total_fare','trips.driver_payout AS driver_amount','trips.payment_status','trips.*','trips.created_at as trip_date','companies.name as company_name']);
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
        $company_columns = array();
        if(LOGIN_USER_TYPE == 'company') {
            $payout_columns = array(
                 ['data' => 'total_fare', 'name' => 'total_fare', 'title' => 'Total Fare'],
                ['data' => 'driver_or_company_commission', 'name' => 'driver_or_company_commission', 'title' => 'Admin Commission'],
               
            );                
        }
        else {
            $payout_columns = array(
                ['data' => 'access_fee', 'name' => 'access_fee', 'title' => 'Access Fare'],
                ['data' => 'driver_or_company_commission', 'name' => 'driver_or_company_commission', 'title' => 'Admin Commission'],
                ['data' => 'total_fare', 'name' => 'total_fare', 'title' => 'Total Fare']
            );
            $company_columns = array(
                ['data' => 'company_name', 'name' => 'companies.name', 'title' => 'Company Name']
            );
        }

        $col_list_1 = [
            ['data' => 'id', 'name' => 'trips.id', 'title' => 'Id'],
            ['data' => 'trip_date', 'name' => 'trips.created_at', 'title' => 'Trip Date'],
        ];

        $col_list_2 = [
            ['data' => 'driver_name', 'name' => 'u.first_name', 'title' => 'Driver Name'],
            ['data' => 'rider_name', 'name' => 'rider.first_name', 'title' => 'Rider Name'],
            ['data' => 'time_fare', 'name' => 'time_fare', 'title' => 'Time Fare'],
            ['data' => 'distance_fare', 'name' => 'distance_fare', 'title' => 'Distance Fare'],
            ['data' => 'base_fare', 'name' => 'base_fare', 'title' => 'Base Fare'],
            ['data' => 'tips', 'name' => 'tips', 'title' => 'Tips'],
            ['data' => 'toll_fee', 'name' => 'toll_fee', 'title' => 'Additional Fee'],
        ];

        $col_list_3 = [
            ['data' => 'driver_payout', 'name' => 'driver_payout', 'title' => 'Earnings'],
            ['data' => 'status', 'name' => 'trips.status', 'title' => 'Status'],
        ];

        return array_merge($col_list_1,$company_columns,$col_list_2,$payout_columns,$col_list_3);
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'payments_' . date('YmdHis');
    }
}