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

class ApplicationDriverDataTable extends DataTable
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
            ->addColumn('email', function ($application) {
                return protectedString($application->email);
            })
            ->addColumn('action', function ($application) {
                $download = (LOGIN_USER_TYPE=='company' || auth('admin')->user()->can('update_driver')) ? '<a href="'.$application->pdf.'" class="btn btn-xs btn-primary" target="_blank"><i class="glyphicon glyphicon-download-alt"></i></a>&nbsp;' : '';
                if ($application->status == 'Active') 
                    $check = '';
                else
                    $check = (auth()->guard('company')->user()!=null || auth('admin')->user()->can('delete_driver')) ? '<a data-href="'.url(LOGIN_USER_TYPE.'/active_driver_application/'.$application->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-active"><i class="glyphicon glyphicon-check"></i></a>&nbsp;':'';
                return $download.$check;
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

        $users = DB::Table('applications')->select('applications.id as id', 'applications.pdf', 'users.first_name', 'users.last_name','users.email','users.country_code','users.mobile_number', 'users.status', 'users.created_at',DB::raw('CONCAT("XXXXXX",Right(users.mobile_number,4)) AS hidden_mobile'))
            ->leftJoin('users', function($join) {
                $join->on('applications.user_id', '=', 'users.id');            
            })->where('applications.type','Driver')
            ->groupBy('id');

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
            ['data' => 'id', 'name' => 'applications.id', 'title' => 'Id'],
            ['data' => 'first_name', 'name' => 'users.first_name', 'title' => 'First Name'],
            ['data' => 'last_name', 'name' => 'users.last_name', 'title' => 'Last Name'],
        ];
        $more_columns = [
            ['data' => 'email', 'name' => 'users.email', 'title' => 'Email'],
            ['data' => 'country_code', 'name' => 'users.country_code', 'title' => 'Country Code'],
            ['data' => $mobile_number_column, 'name' => 'users.mobile_number', 'title' => 'Mobile Number'],
            ['data' => 'created_at', 'name' => 'users.created_at', 'title' => 'Created At'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Action', 'orderable' => false, 'searchable' => false, 'exportable' => false],
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