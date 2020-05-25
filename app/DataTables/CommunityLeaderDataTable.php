<?php

/**
 * Driver DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Driver
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Services\DataTable;
use DB;

class CommunityLeaderDataTable extends DataTable
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
            ->addColumn('email', function ($user) {
                return protectedString($user->email);
            })
            ->addColumn('merchants_count', function ($user) {
                return DB::table('merchants')->select('merchants.id as id', 'users.used_referral_code')
                    ->leftJoin('users', 'merchants.user_id', '=', 'users.id')
                    ->where('used_referral_code', $user->referral_code)
                    ->get()
                    ->count();
            })
            ->addColumn('drivers_count', function ($user) {
                return DB::table('users')
                    ->where('user_type', 'Driver')
                    ->where('used_referral_code', $user->referral_code)
                    ->get()
                    ->count();
            })
            ->addColumn('deliveries_count', function ($user) {
                $drivers = DB::table('users')
                    ->where('user_type', 'Driver')
                    ->where('used_referral_code', $user->referral_code)
                    ->get();
                $driver_ids = array();
                foreach ($drivers as $driver)
                {
                    $driver_ids[] = $driver->id;
                }
                return DB::table('delivery_orders')
                    ->whereIn('driver_id', $driver_ids)
                    ->where('status', 'delivered')
                    ->get()
                    ->count();
            })
            ->addColumn('action', function ($user) {
                $detail = '<a href="'.url(LOGIN_USER_TYPE.'/community_leader/'.$user->id).'" class="btn btn-xs btn-primary"><i class="fa fa-eye" ></i></a>&nbsp;';
                $edit = (LOGIN_USER_TYPE=='company' || auth('admin')->user()->can('update_driver')) ? '<a href="'.url(LOGIN_USER_TYPE.'/edit_community_leader/'.$user->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
                $delete = (auth()->guard('company')->user()!=null || auth('admin')->user()->can('delete_driver')) ? '<a data-href="'.url(LOGIN_USER_TYPE.'/delete_community_leader/'.$user->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;':'';
                return $detail.$edit.$delete;
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

        $users = DB::Table('users')->select('users.id as id', 'users.first_name', 'users.last_name','users.email','users.country_code','users.mobile_number', 'users.status', 'users.referral_code', 'companies.name as company_name', 'stripe_subscription_plans.plan_name', 'users.created_at',DB::raw('CONCAT("XXXXXX",Right(users.mobile_number,4)) AS hidden_mobile'))
            ->leftJoin('companies', function($join) {
                $join->on('users.company_id', '=', 'companies.id');
            })->leftJoin('stripe_subscriptions', function($join) {
                $join->on('users.id', '=', 'stripe_subscriptions.user_id');
            })->leftJoin('stripe_subscription_plans', function($join) {
                $join->on('stripe_subscriptions.plan', '=', 'stripe_subscription_plans.id');
            })->where('user_type','Driver')->whereIn('plan_name', ['Regular', 'Founder', 'Executive'])->groupBy('id');
        
        //If login user is company then get that company drivers only
        if (LOGIN_USER_TYPE=='company') {
            $users = $users->where('company_id',auth()->guard('company')->user()->id);
        }
        return $users;
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
        $mobile_number_column = (isLiveEnv())?'hidden_mobile':'mobile_number';
        $columns = [
            ['data' => 'id', 'name' => 'users.id', 'title' => 'Id'],
            ['data' => 'first_name', 'name' => 'users.first_name', 'title' => 'First Name'],
            ['data' => 'last_name', 'name' => 'users.last_name', 'title' => 'Last Name'],
        ];
        if (LOGIN_USER_TYPE!='company') {
            $columns[] = ['data' => 'company_name', 'name' => 'companies.name', 'title' => 'Company Name'];
        }
        $more_columns = [
            ['data' => 'email', 'name' => 'users.email', 'title' => 'Email'],
            ['data' => 'status', 'name' => 'users.status', 'title' => 'Status'],
            ['data' => $mobile_number_column, 'name' => 'users.mobile_number', 'title' => 'Mobile Number'],
            ['data' => 'plan_name', 'name' => 'stripe_subscription_plans.plan_name', 'title' => 'Subscription Name'],
            ['data' => 'merchants_count', 'name' => 'users.id', 'title' => 'Merchants Count'],
            ['data' => 'drivers_count', 'name' => 'users.id', 'title' => 'Drivers Count'],
            ['data' => 'deliveries_count', 'name' => 'users.id', 'title' => 'Deliveries Count'],
            ['data' => 'created_at', 'name' => 'users.created_at', 'title' => 'Created At'],
            ['data' => 'action', 'name' => 'action', 'class' => 'text-center', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'exportable' => false],
        ];

        return array_merge($columns,$more_columns);
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'drivers_' . date('YmdHis');
    }
}