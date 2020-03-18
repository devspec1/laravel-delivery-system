<?php

/**
 * Rating DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Rating
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\Rating;
use Yajra\DataTables\Services\DataTable;

class RatingDataTable extends DataTable
{
    protected $exportColumns = ['id','rider_name','driver_name','car_name','rider_rading','driver_rading','driver_name','rider_comments','driver_comments', 'status'];

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->of($query);
    }

    /**
     * Get query source of dataTable.
     *
     * @param Rating $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Rating $model)
    {
        $rating = $model->where(function($query)  {
                            if(LOGIN_USER_TYPE=='company') {
                                $query->whereHas('driver',function($q1){
                                    $q1->where('company_id',auth('company')->id());
                                });
                            }
                        })
                        ->join('users', function($join) {
                            $join->on('users.id', '=', 'rating.user_id');
                        })
                        ->join('trips', function($join) {
                            $join->on('trips.id', '=', 'rating.trip_id');
                        })
                        ->join('car_type', function($join) {
                            $join->on('car_type.id', '=', 'trips.car_id');
                        })
                        ->leftJoin('users as u', function($join) {
                            $join->on('u.id', '=', 'rating.driver_id');
                        })
                        ->leftJoin('companies', function($join) {
                            $join->on('u.company_id', '=', 'companies.id');
                        })
                        ->select(['rating.id as id', 'u.first_name as driver_name', 'users.first_name as rider_name', 'car_type.car_name as car_name','rating.rider_rating as rider_rating', 'rating.driver_rating as driver_rading','rating.rider_comments as rider_comments', 'rating.driver_comments as driver_comments','trips.created_at as date','rating.*','companies.name as driver_company_name']);
        return $rating;
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
        $columns = [
            ['data' => 'trip_id', 'name' => 'trip_id', 'title' => 'Trip Number'],
            ['data' => 'date', 'name' => 'trips.created_at', 'title' => 'Trip Date'],
            ['data' => 'driver_name', 'name' => 'u.first_name', 'title' => 'Driver Name'],
            ['data' => 'rider_name', 'name' => 'users.first_name', 'title' => 'Rider Name'],
        ];

        if(LOGIN_USER_TYPE == 'admin') {
            $columns[] = ['data' => 'driver_company_name', 'name' => 'companies.name', 'title' => 'Company Name'];
        }
        return array_merge($columns,[
            ['data' => 'car_name', 'name' => 'car_type.car_name', 'title' => 'Car name'],
            ['data' => 'driver_rating', 'name' => 'rating.driver_rating', 'title' => 'Driver Rating'],
            ['data' => 'rider_rating', 'name' => 'rating.rider_rating', 'title' => 'Rider Rating'],
            ['data' => 'rider_comments', 'name' => 'rating.rider_comments', 'title' => 'Rider Comments'],
            ['data' => 'driver_comments', 'name' => 'rating.driver_comments', 'title' => 'Driver Comments'],
        ]);
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'rating_' . date('YmdHis');
    }
}