<?php

/**
 * Provider statement DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Provider statement
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\User;
use App\Models\Trips;
use Yajra\DataTables\Services\DataTable;
use DB;

class ProviderstatementDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->currency_symbol = html_entity_decode(session()->get('symbol'));
        return datatables()
            ->of($query)
            ->addColumn('total_earnings_driver', function ($driver) {  
                $user=User::where('id',$driver->id)->first();
                return $this->currency_symbol.$user->total_earnings;
            })
            ->addColumn('total_commission_driver', function ($driver) { 
                $user=User::where('id',$driver->id)->first();
                if (LOGIN_USER_TYPE == 'company') {
                    return $this->currency_symbol.$user->total_company_admin_commission;
                }else{
                    return $this->currency_symbol.$user->total_commission;
                }
            })
            ->addColumn('driver_joined_at', function ($driver) {   
                $user = User::where('id',$driver->id)->first();
                return $user->date_time_join;
            })
            ->addColumn('action', function ($driver) {   
                return '<a href="'.url(LOGIN_USER_TYPE.'/view_driver_statement/'.$driver->id).'" class="btn btn-xs btn-primary">View by Ride</a>';
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
        $drivers = DB::table('users')
                ->join('trips', function($join) {
                    $join->on('users.id', '=', 'trips.driver_id');
                })
                ->select('users.id as id', 'users.first_name', 'users.last_name','users.email','users.country_code','users.mobile_number', 'users.status','companies.name as company_name','users.created_at',DB::raw('CONCAT("*******",Right(users.mobile_number,4)) AS mobile'),DB::raw('COUNT(trips.id) AS total_rides_driver'))
                ->leftJoin('companies', function($join) {
                    $join->on('users.company_id', '=', 'companies.id');
                })
                ->groupBy('users.id');
        //If login user is company then get that company drivers only
        if (LOGIN_USER_TYPE=='company') {
            $drivers = $drivers->where('company_id',auth('company')->id());
        }
        return $drivers->get();
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
        $mobile_number_column = (isLiveEnv())?'mobile':'mobile_number';
        $columns = array(
            ['data' => 'id', 'name' => 'id', 'title' => 'Driver id'],
            ['data' => 'first_name', 'name' => 'first_name', 'title' => 'Driver Name'],
        );                
        if(LOGIN_USER_TYPE == 'admin') {
            $columns[] =['data' => 'company_name', 'name' => 'company_name', 'title' => 'Company Name'];
        }
        return array_merge($columns,[
            ['data' => $mobile_number_column, 'name' => 'mobile', 'title' => 'Mobile'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'total_rides_driver', 'name' => 'total_rides_driver', 'title' => 'Total Rides'],
            ['data' => 'total_earnings_driver', 'name' => 'total_earnings_driver', 'title' => 'Earnings'],
            ['data' => 'total_commission_driver', 'name' => 'total_commission_driver', 'title' => 'Admin commission'],
            ['data' => 'driver_joined_at', 'name' => 'driver_joined_at', 'title' => 'Joined at'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Details', 'orderable' => false, 'searchable' => false],
        ]);
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'riders_' . date('YmdHis');
    }
}