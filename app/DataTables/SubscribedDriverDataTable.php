<?php

/**
 * Subscribed Driver DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Subscribed Driver
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Services\DataTable;
use DB;

class SubscribedDriverDataTable extends DataTable
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
            ->addColumn('action', function ($subscribed_drivers) {
                $edit = '<a href="'.url('admin/subscriptions/edit_driver/'.$subscribed_drivers->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;';
                return $edit;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {
        /* only for Package */
        /*$users = DB::Table('users')->select('users.id as id', 'users.first_name', 'users.last_name','users.email','users.country_code','users.mobile_number', 'users.status','companies.name as company_name','users.created_at',DB::raw('CONCAT("+",users.country_code," ",users.mobile_number) AS mobile'))
            ->leftJoin('companies', function($join) {
                $join->on('users.company_id', '=', 'companies.id');
            })->where('user_type','Driver')->groupBy('id');*/

        $subscribed_drivers = DB::Table('stripe_subscriptions')->select('stripe_subscriptions.id as id', 'users.first_name', 'users.last_name','users.email', 'stripe_subscription_plans.plan_name', 'stripe_subscriptions.status', 'stripe_subscriptions.updated_at')
            ->leftJoin('users', function($join) {
                $join->on('users.id', '=', 'stripe_subscriptions.user_id');
            })->leftJoin('stripe_subscription_plans', function($join) {
                $join->on('stripe_subscription_plans.id', '=', 'stripe_subscriptions.plan');
            })->groupBy('id');

        return $subscribed_drivers;
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
            ['data' => 'id', 'name' => 'stripe_subscriptions.id', 'title' => 'Id'],
            ['data' => 'first_name', 'name' => 'users.first_name', 'title' => 'First Name'],
            ['data' => 'last_name', 'name' => 'users.last_name', 'title' => 'Last Name'],
            ['data' => 'email', 'name' => 'users.email', 'title' => 'Email'],
            ['data' => 'plan_name', 'name' => 'stripe_subscription_plans.plan_name', 'title' => 'Plan Name'],
            ['data' => 'status', 'name' => 'stripe_subscriptions.status', 'title' => 'Status'],
            ['data' => 'updated_at', 'name' => 'stripe_descriptions.updated_at', 'title' => 'Updated At'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'exportable' => false],
        ];

        return array_merge($columns);
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'subscribed_drivers_' . date('YmdHis');
    }
}