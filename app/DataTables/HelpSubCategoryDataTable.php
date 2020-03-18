<?php

/**
 * Help Sub Category DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Help Sub Category
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\HelpSubCategory;
use Yajra\DataTables\Services\DataTable;
use DB;

class HelpSubCategoryDataTable extends DataTable
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
            ->addColumn('action', function ($help_subcategory) {
                $edit = '<a href="'.url('admin/edit_help_subcategory/'.$help_subcategory->subcategory_id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;';
                $delete = '<a data-href="'.url('admin/delete_help_subcategory/'.$help_subcategory->subcategory_id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>';

                return $edit.$delete;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param HelpSubCategory $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(HelpSubCategory $model)
    {
        $help_subcategory = $model->join('help_category', function($join) {
                                $join->on('help_category.id', '=', 'help_subcategory.category_id');
                            })
                            ->select(['help_subcategory.id as subcategory_id', 'help_subcategory.name as subcategory_name', 'help_subcategory.description as subcategory_description', 'help_subcategory.status as subcategory_status', 'help_subcategory.category_id', 'help_category.*']);
        return $help_subcategory;
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
                    ->addAction(["printable" => false])
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
        return [
            ['data' => 'subcategory_id', 'name' => 'help_subcategory.id', 'title' => 'Id'],
            ['data' => 'category_name', 'name' => 'help_category.name', 'title' => 'Category Name'],
            ['data' => 'subcategory_name', 'name' => 'help_subcategory.name', 'title' => 'Name'],
            ['data' => 'subcategory_description', 'name' => 'help_subcategory.description', 'title' => 'Description'],
            ['data' => 'subcategory_status', 'name' => 'help_subcategory.status', 'title' => 'Status'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'help_sub_category_' . date('YmdHis');
    }
}