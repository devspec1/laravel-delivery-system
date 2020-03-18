<?php

/**
 * Vehicle DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Vehicle
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\Vehicle;
use Yajra\DataTables\Services\DataTable;
use DB;

class VehicleDataTable extends DataTable
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
            ->addColumn('action', function ($vehicle) {
                 $edit = '<a href="'.url(LOGIN_USER_TYPE.'/edit_vehicle/'.$vehicle->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>' ;
                $delete = '<a data-href="'.url(LOGIN_USER_TYPE.'/delete_vehicle/'.$vehicle->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>';

                return $edit.'&nbsp'.$delete;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Vehicle $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Vehicle $model)
    {
        $vehicles = DB::Table('vehicle')
            ->select('vehicle.id as id','vehicle.status as status','vehicle.vehicle_name as vehicle_name','vehicle.vehicle_number as vehicle_number','car_type.car_name as vehicle_type', 'users.first_name as driver_name','companies.name as company_name')
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'vehicle.user_id');
            })
            ->join('car_type', function ($join) {
                $join->on('vehicle.vehicle_id', '=', 'car_type.id');
            })
            ->leftJoin('companies', function ($join) {
                $join->on('companies.id', '=', 'vehicle.company_id');
            });

        //If login user is company then get that company vehicles only
        if (LOGIN_USER_TYPE=='company') {
            $vehicles = $vehicles->where('vehicle.company_id',auth()->guard('company')->user()->id);
        }
        return $vehicles;
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
        return [
            ['data' => 'id', 'name' => 'vehicle.id', 'title' => 'Id'],
            ['data' => 'company_name', 'name' => 'companies.name', 'title' => 'Company Name'],
            ['data' => 'driver_name', 'name' => 'users.first_name', 'title' => 'Driver Name'],
            ['data' => 'vehicle_type', 'name' => 'vehicle.vehicle_type', 'title' => 'Vehicle Type'],
            ['data' => 'vehicle_name', 'name' => 'vehicle.vehicle_name', 'title' => 'Vehicle Name'],
            ['data' => 'vehicle_number', 'name' => 'vehicle.vehicle_number', 'title' => 'Vehicle Number'],
            ['data' => 'status', 'name' => 'vehicle.status', 'title' => 'Status'],
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
        return 'vehicles_' . date('YmdHis');
    }
}