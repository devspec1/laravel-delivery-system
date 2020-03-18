<?php

/**
 * Company DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Company
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\Company;
use Yajra\DataTables\Services\DataTable;
use DB;

class CompanyDataTable extends DataTable
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
            ->addColumn('email', function ($companies) {
                return protectedString($companies->email);
            })
            ->addColumn('drivers', function($companies) {
                $driver_ids = $companies->drivers->implode('id',',');
                return '<p class="company_driver_list">'.$driver_ids.'</p>';
            })
            ->addColumn('action', function ($companies) {
                $edit = (auth('admin')->user()->can('update_company')) ? '<a href="'.url('admin/edit_company/'.$companies->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;' : '';
                $delete = (auth('admin')->user()->can('delete_company')) ? '<a data-href="'.url('admin/delete_company/'.$companies->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>':'';

                return $edit.$delete;
            })
            ->rawColumns(['drivers','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param Company $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Company $model)
    {
        /* only for Package */
        /*$companies = Company::select('companies.id', 'companies.name','companies.email','companies.country_code','companies.mobile_number', 'companies.status',DB::raw('CONCAT("+",companies.country_code," ",companies.mobile_number) AS mobile'))->with('drivers');*/

        $companies = Company::select('companies.id', 'companies.name','companies.email','companies.country_code','companies.mobile_number', 'companies.status',DB::raw('CONCAT("XXXXXX",Right(mobile_number,4)) AS hidden_mobile'))->with('drivers');
        return $companies;
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
        $mobile_number_column = (isLiveEnv()) ?'hidden_mobile':'mobile_number';

        return [
            ['data' => 'id', 'name' => 'companies.id', 'title' => 'Id'],
            ['data' => 'name', 'name' => 'companies.name', 'title' => 'Name'],
            ['data' => 'drivers', 'name' => 'drivers', 'title' => 'Drivers','width' => "10%"],
            ['data' => 'email', 'name' => 'companies.email', 'title' => 'Email'],
            ['data' => $mobile_number_column, 'name' => 'companies.mobile_number', 'title' => 'Mobile Number'],
            ['data' => 'status', 'name' => 'companies.status', 'title' => 'Status'],
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
        return 'companies_' . date('YmdHis');
    }
}