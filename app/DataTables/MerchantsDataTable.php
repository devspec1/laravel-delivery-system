<?php

/**
 * Merchants DataTable
 *
 * @package     Rideon Driver
 * @subpackage  DataTable
 * @category    HomeDelivery
 * @author      pardusurbanus@protonmail.com
 * @version     2.2
 * @link        https://rideon.co
 */

namespace App\DataTables;

use App\Models\Merchant;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Html\Editor\Editor;

use DB;

class MerchantsDataTable extends DataTable
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
        ->addColumn('action', function ($merchant) {
            $detail = '<a href="'.url(LOGIN_USER_TYPE.'/merchant_orders/'.$merchant->id).'" class="btn btn-xs btn-primary"><i class="fa fa-eye" ></i></a>&nbsp;';
            $edit = (LOGIN_USER_TYPE=='company' || auth('admin')->user()->can('update_driver')) ? '<a href="'.url(LOGIN_USER_TYPE.'/edit_merchant/'.$merchant->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
            $delete = (auth()->guard('company')->user()!=null || auth('admin')->user()->can('delete_driver')) ? '<a data-href="'.url(LOGIN_USER_TYPE.'/delete_merchant/'.$merchant->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;':'';
            return $detail.$edit.$delete;
        });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Merchant $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Merchant $model)
    {
        return $model->join('merchants_integration_types as integration', function($join) {
            $join->on('integration.id', '=', 'merchants.integration_type');
        })
        ->select([
            'merchants.id as id',
            'integration.name as integration',
            'merchants.name as name', 
            'merchants.description as description',
            'merchants.delivery_fee as base_fee',
            'merchants.delivery_fee_base_distance as base_distance_km',
            'merchants.delivery_fee_per_km as surcharge_fee',
            DB::raw('(SELECT COALESCE(sum(o2.fee), 0) FROM delivery_orders AS o2 WHERE o2.merchant_id = merchants.id) as owe_fees'),
            'merchants.shared_secret as shared_secret',
            'merchants.created_at as created_at',
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
                    ->setTableId('merchants-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
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
            Column::make('id')->title('Merchant ID'),
            Column::make('name'),
            Column::make('description'),
            Column::make('integration')->name('integration.name'),
            Column::make('base_fee')->searchable(false),
            Column::make('base_distance_km')->searchable(false),
            Column::make('surcharge_fee')->searchable(false),
            Column::make('shared_secret'),
            Column::make('created_at'),
            Column::make('action')
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
        return 'merchants_' . date('YmdHis');
    }
}
