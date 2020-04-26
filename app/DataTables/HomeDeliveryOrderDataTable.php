<?php

/**
 * HomeDeliveryOrders DataTable
 *
 * @package     Rideon Driver
 * @subpackage  DataTable
 * @category    HomeDelivery
 * @author      pardusurbanus@gmail.com
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\User;
use App\Models\HomeDeliveryOrder;
use Yajra\DataTables\Services\DataTable;
use DB;

class HomeDeliveryOrderDataTable extends DataTable
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
            ->addColumn('action', function ($orders) {
                $edit = (LOGIN_USER_TYPE=='company' || auth('admin')->user()->can('update_driver')) ? '<a href="'.url(LOGIN_USER_TYPE.'/edit_home_delivery/'.$orders->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
                $delete = (auth()->guard('company')->user()!=null || auth('admin')->user()->can('delete_driver')) ? '<a data-href="'.url(LOGIN_USER_TYPE.'/delete_home_delivery/'.$orders->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;':'';
                return $edit.$delete;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \HomeDeliveryOrder $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(HomeDeliveryOrder $model)
    {
        return $model->all();
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
            ['data' => 'id', 'name' => 'id', 'title' => 'Id'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Created At'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'driver_id', 'name' => 'driver_id', 'title' => 'Assigned Driver'],
            ['data' => 'estimate_time', 'name' => 'orders.estimate_time', 'title' => 'Estimate time'],
            ['data' => 'fee', 'name' => 'fee', 'title' => 'Fee'],
            ['data' => 'pick_up_location', 'name' => 'pick_up_location', 'title' => 'Pick Up'],
            ['data' => 'drop_off_location', 'name' => 'drop_off_location', 'title' => 'Drop Off'],
            ['data' => 'customer_name', 'name' => 'customer_name', 'title' => 'Customer Name'],
            ['data' => 'customer_phone_number', 'name' => 'customer_phone_number', 'title' => 'Customer Phone'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'home_delivery_' . date('YmdHis');
    }
}