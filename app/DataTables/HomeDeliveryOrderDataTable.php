<?php

/**
 * HomeDeliveryOrders DataTable
 *
 * @package     Rideon Driver
 * @subpackage  DataTable
 * @category    HomeDelivery
 * @author      pardusurbanus@protonmail.com
 * @version     2.2
 * @link        https://rideon.co
 */

namespace App\DataTables;

use App\Models\User;
use App\Models\HomeDeliveryOrder;

use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Html\Editor\Editor;

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
                $detail = '<a href="'.url(LOGIN_USER_TYPE.'/home_delivery_orders/'.$orders->id).'" class="btn btn-xs btn-primary"><i class="fa fa-eye" ></i></a>&nbsp;';
                $edit = (LOGIN_USER_TYPE=='company' || auth('admin')->user()->can('update_driver')) ? '<a href="'.url(LOGIN_USER_TYPE.'/edit_home_delivery/'.$orders->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
                $delete = (auth()->guard('company')->user()!=null || auth('admin')->user()->can('delete_driver')) ? '<a data-href="'.url(LOGIN_USER_TYPE.'/delete_home_delivery/'.$orders->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;':'';
                return $detail.$edit.$delete;
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
        return $model->whereIn('delivery_orders.status',['new','assigned','delivered','expired'])
            ->join('users as rider', function($join) {
                $join->on('rider.id', '=', 'delivery_orders.customer_id');
            })
            ->join('request as ride_request', function($join) {
                $join->on('ride_request.id', '=', 'delivery_orders.ride_request');
            })
            ->join('merchants', function($join) {
                $join->on('merchants.id', '=', 'delivery_orders.merchant_id');
            })
            ->select([
                'delivery_orders.id as id',
                DB::raw('CONCAT(delivery_orders.estimate_time," mins") as estimate_time'),
                'delivery_orders.driver_id as driver_id',
                'delivery_orders.created_at as created_at',
                'merchants.name as merchant_name',
                'delivery_orders.order_description as order_description',
                DB::raw('CONCAT(delivery_orders.distance/1000," KM") as distance'),
                DB::raw('CONCAT(delivery_orders.estimate_time," mins") as estimate_time'),
                'delivery_orders.fee as fee',
                'delivery_orders.status as status',
                'ride_request.pickup_location as pick_up_location',
                'ride_request.drop_location as drop_off_location',
                DB::raw('CONCAT(rider.first_name," ",rider.last_name) as customer_name'),
                DB::raw('CONCAT("+",rider.country_code,rider.mobile_number) as mobile_number'),
            ]);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('delivery-orders-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('lBfr<"table-responsive"t>ip')
            ->orderBy(0)
            ->buttons(
                Button::make('csv'),
                Button::make('excel'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
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
            Column::make('id')
                ->title('Order ID'),
            Column::make('created_at')
                ->title('Create at'),
            Column::make('status')
                ->title('Status'),
            Column::make('driver_id')
                ->title('Assigned Driver'),
            Column::make('estimate_time')
                ->title('Estimate time'),
            Column::make('fee')
                ->title('Fee'),
            Column::make('pick_up_location')
                ->title('Pick Up')
                ->name('ride_request.pickup_location'),
            Column::make('drop_off_location')
                ->title('Drop Off')
                ->name('ride_request.drop_location'),
            Column::make('distance')
                ->title('Distance'),
            Column::make('order_description')
                ->title('Order Description'),
            Column::make('customer_name')
                ->title('Customer Name')
                ->name('rider.first_name'),
            Column::make('mobile_number')
                ->title('Customer Phone')
                ->name('rider.mobile_number'),
            Column::make('merchant_name')
                ->title('Merchant')
                ->name('merchants.name'),
            Column::make('action', 'Action')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-center'),
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