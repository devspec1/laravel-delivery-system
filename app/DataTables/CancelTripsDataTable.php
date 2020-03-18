<?php

/**
 * Cancel Trips DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Cancel Trips
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\Cancel;
use Yajra\DataTables\Services\DataTable;
use DB;

class CancelTripsDataTable extends DataTable
{
    protected $exportColumns = ['id','trips_created_at','rider_name','driver_name','pickup_location','drop_location','cancel_reason','cancel_comments','cancelled_by','created_at'];

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->of($query);
    }

    /**
     * Get query source of dataTable.
     *
     * @param Cancel $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Cancel $model)
    {
        $cancel = $model->where(function($query)  {
                            if(LOGIN_USER_TYPE=='company') {
                                $query->whereHas('trip.driver',function($q1) {
                                    $q1->where('company_id',auth('company')->id());
                                });
                            }
                        })
                        ->join('trips', function($join) {
                            $join->on('trips.id', '=', 'cancel.trip_id');
                        })
                        ->join('users', function($join) {
                            $join->on('users.id', '=', 'trips.user_id');
                        })
                        ->join('cancel_reasons', function($join) {
                            $join->on('cancel_reasons.id', '=', 'cancel.cancel_reason_id');
                        })
                        ->leftJoin('users as u', function($join) {
                            $join->on('u.id', '=', 'trips.driver_id');
                        })
                        ->leftJoin('companies', function($join) {
                            $join->on('u.company_id', '=', 'companies.id');
                        })
                        ->select(['cancel.id as id', 'cancel.created_at', 'u.first_name as driver_name', 'users.first_name as rider_name','cancel_reasons.reason as cancel_reason', 'cancel.cancel_comments as cancel_comments ','cancel.cancelled_by as cancelled_by', 'cancel.created_at as cancel_created_at','cancel.*','trips.pickup_location as pickup_location','trips.drop_location as drop_location','trips.created_at as trips_created_at','companies.name as company_name']);
        return $cancel;
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
        $columns = [
            ['data' => 'id', 'name' => 'cancel.id', 'title' => 'Id'],
            ['data' => 'trips_created_at', 'name' => 'trips.created_at', 'title' => 'Trip Date'],
        ];

        if(LOGIN_USER_TYPE == 'admin') {
            $columns[] = ['data' => 'company_name', 'name' => 'companies.name', 'title' => 'Company Name'];
        }

        return array_merge($columns,[
            ['data' => 'driver_name', 'name' => 'u.first_name', 'title' => 'Driver Name'],
            ['data' => 'rider_name', 'name' => 'users.first_name', 'title' => 'Rider Name'],
            ['data' => 'pickup_location', 'name' => 'trips.pickup_location', 'title' => 'Pickup Location'],
            ['data' => 'drop_location', 'name' => 'trips.drop_location', 'title' => 'Drop Location'],
            ['data' => 'cancel_reason', 'name' => 'cancel_reasons.reason', 'title' => 'Reason'],
            ['data' => 'cancel_comments', 'name' => 'cancel.cancel_comments', 'title' => 'Comments'],
            ['data' => 'cancelled_by', 'name' => 'cancel.cancelled_by', 'title' => 'Canceled By'],
            ['data' => 'cancel_created_at', 'name' => 'cancel.created_at', 'title' => 'Cancel At'],
        ]);
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'cancel_trips_' . date('YmdHis');
    }
}