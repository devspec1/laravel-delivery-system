<?php

/**
 * Car Type DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Car Type
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\ScheduleRide;
use Yajra\DataTables\Services\DataTable;
use DB;

class LaterBookingDataTable extends DataTable
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
            ->editColumn('driver_name', function($schedule_rides) {
                $button = '';
                if ($schedule_rides->status=='Pending' && $schedule_rides->booking_type=='Manual Booking') {
                    $button = '<br><a href="'.url(LOGIN_USER_TYPE.'/manual_booking/'.$schedule_rides->id).'" class="btn btn-primary edit_'.$schedule_rides->id.'"><i class="fa fa-edit"></i></a>';
                }

                if ($schedule_rides->status=='Completed' && $schedule_rides->driver_name==null && $schedule_rides->trip_id != null) {
                    $driver_name = $schedule_rides->trip_driver_name;
                }else{
                    $driver_name = ($schedule_rides->driver_name==null?'Auto Assign':$schedule_rides->driver_name);
                }
                return $driver_name.' '.$button;
            })
            ->editColumn('status', function($schedule_rides) {
                if ($schedule_rides->status=='Pending' && $schedule_rides->booking_type=='Manual Booking') {
                    $status = '<span class="cancel_'.$schedule_rides->id.'">'.$schedule_rides->status.'</span><br><span data-toggle="modal" data-target="#cancel_popup" class="btn btn-primary cancel_button" schedule_id="'.$schedule_rides->id.'">Cancel</span>';
                }elseif($schedule_rides->status=='Cancelled'){
                    if ($schedule_rides->schedule_cancel==null) {
                        $status = $schedule_rides->status;
                    }else{
                        $status = 'Cancelled by '.$schedule_rides->schedule_cancel->cancel_by.'<br><span data-toggle="modal" data-target="#cancel_reason_popup" class="btn btn-primary cancel_button" schedule_id="'.$schedule_rides->id.'" cancel_by="'.$schedule_rides->schedule_cancel->cancel_by.'" reason="'.$schedule_rides->schedule_cancel->cancel_reason.'" cancel_reason="'.$schedule_rides->schedule_cancel->cancel_reasons->reason.'">Cancel Reason</a>';
                    }
                }elseif($schedule_rides->status=='Car Not Found'){
                    $status = '<span class="immediate_request_'.$schedule_rides->id.'">'.$schedule_rides->status.'</span><br><span id="immediate_request" class="btn btn-primary" schedule_id="'.$schedule_rides->id.'">immediate Request</span>';
                }elseif($schedule_rides->status=='Completed' && $schedule_rides->booking_type=='Manual Booking' && $schedule_rides->trip_id != null && $schedule_rides->trip_status == 'Cancelled'){
                    $status = 'Trip cancelled';
                }elseif($schedule_rides->status=='Completed' && $schedule_rides->trip_id != null ){
                    $status = $schedule_rides->trip_status;
                }else{
                    $status = $schedule_rides->status;
                }
                return $status;
            })
            ->addColumn('date_time', function ($schedule_rides) {
                return date("Y-m-d H:i a",strtotime($schedule_rides->schedule_date.' '.$schedule_rides->schedule_time));
            })
            ->addColumn('company_name', function ($schedule_rides) {
                return $schedule_rides->company_name != '' ? $schedule_rides->company_name : '-';
            })
            ->addColumn('trip_details', function ($schedule_rides) {
                if ($schedule_rides->trip_id==null) {
                    return '---';
                }
                return '<a href="'.url(LOGIN_USER_TYPE.'/view_trips/'.$schedule_rides->trip_id).'" class="btn btn-primary"><i class="fa fa-eye"></i></a>';
            })
            ->addColumn('action', function ($car_type) {
                $edit = '<a href="'.url('admin/edit_car_type/'.$car_type->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;';
                $delete = '<a data-href="'.url('admin/delete_car_type/'.$car_type->id).'" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#confirm-delete"><i class="glyphicon glyphicon-trash"></i></a>';

                return $edit.$delete;
            })
            ->rawColumns(['driver_name','status', 'trip_details','action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param ScheduleRide $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ScheduleRide $model)
    {
        $schedule_rides = $model->where('schedule_ride.location_id','!=',0)
                        ->join('users as rider', function($join) {
                            $join->on('rider.id', '=', 'schedule_ride.user_id');
                        })
                        ->leftJoin('users as driver', function($join) {
                            $join->on('driver.id', '=', 'schedule_ride.driver_id');
                        })
                        ->leftJoin('companies', function($join) {
                            $join->on('driver.company_id', '=', 'companies.id');
                        })
                        ->join('car_type', function($join) {
                            $join->on('car_type.id', '=', 'schedule_ride.car_id');
                        })
                        ->leftJoin('request as ride_request', function($join) {
                            $join->on('ride_request.schedule_id', '=', 'schedule_ride.id')
                            ->whereIn('ride_request.id', function($query) {
                                $query->selectRaw('MAX(request.id) from request,schedule_ride where schedule_ride.id = request.schedule_id')->groupBy('request.schedule_id');
                            });
                        })
                        ->leftJoin('trips', function($join) {
                            $join->on('ride_request.id', '=', 'trips.request_id');
                        })
                        ->leftJoin('users as trip_driver', function($join) {
                            $join->on('trips.driver_id', '=', 'trip_driver.id');
                        })
                        ->with('schedule_cancel.cancel_reasons')
                        ->select([
                            'schedule_ride.*',
                            'rider.first_name as rider_name',
                            'driver.first_name as driver_name',
                            DB::raw('DATE_FORMAT(CONCAT(schedule_ride.schedule_date," ", schedule_ride.schedule_time), "%d %M %Y %H:%i") as date_time'),
                            'companies.name as company_name',
                            'trips.id as trip_id',
                            'trips.status as trip_status',
                            'trip_driver.first_name as trip_driver_name',
                        ])
                        ->where(function($query)  {
                            if(LOGIN_USER_TYPE=='company') {
                                $query->where('schedule_ride.company_id',auth('company')->user()->id)
                                ->orWhereHas('driver',function($q1){
                                    $q1->where('driver.company_id',auth('company')->user()->id);
                                });
                            }
                        })
                        ->distinct();
        return $schedule_rides;
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
        $company_columns = array();
        if(LOGIN_USER_TYPE == 'admin') {
            $company_columns = array(
                ['data' => 'company_name', 'name' => 'companies.name', 'title' => 'Company Name']
            );
        }
        $columns = [
            ['data' => 'id', 'name' => 'schedule_ride.id', 'title' => 'Id'],
            ['data' => 'booking_type', 'name' => 'schedule_ride.booking_type', 'title' => 'Booked By'],
            ['data' => 'date_time', 'name' => 'schedule_ride.schedule_date', 'title' => 'Date'],
            ['data' => 'driver_name', 'name' => 'driver.first_name', 'title' => 'Driver Name'],
        ];
        $other_columns = [
            ['data' => 'rider_name', 'name' => 'rider.first_name', 'title' => 'Rider Name'],
            ['data' => 'pickup_location', 'name' => 'schedule_ride.pickup_location', 'title' => 'Pickup Location'],
            ['data' => 'drop_location', 'name' => 'schedule_ride.drop_location', 'title' => 'Drop Location'],
            ['data' => 'trip_details', 'name' => 'schedule_ride.id', 'title' => 'Trip Details'],
            ['data' => 'status', 'name' => 'schedule_ride.status', 'title' => 'Status'],
        ];
        return array_merge($columns,$company_columns,$other_columns);
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'vehicles_type_' . date('YmdHis');
    }
}