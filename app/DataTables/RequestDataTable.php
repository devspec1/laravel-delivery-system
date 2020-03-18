<?php

/**
 * Rider DataTable
 *
 * @package     Gofer
 * @subpackage  DataTable
 * @category    Rider
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\DataTables;

use App\Models\Request as RideRequest;
use Yajra\DataTables\Services\DataTable;
use DB;

class RequestDataTable extends DataTable
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
            ->addColumn('date_time',function($get_request) {
                $now = new \DateTime;
                $ago = new \DateTime($get_request->updated_at);
                $diff = $now->diff($ago);

                $diff->w = floor($diff->d / 7);
                $diff->d -= $diff->w * 7;

                $string = array('y' => 'year','m' => 'month','w' => 'week','d' => 'day','h' => 'hour','i' => 'minute','s' => 'second');
                foreach ($string as $k => &$v) {
                    if ($diff->$k) {
                        $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
                    } else {
                        unset($string[$k]);
                    }
                }
                $string = array_slice($string, 0, 1);

                return $string ? implode(', ', $string) . ' ago' : 'just now';
            })
            ->addColumn('request_status', function ($get_request) {
                $request_status=DB::table('request')->where('group_id',$get_request->group_id)->where('status','Accepted');
                $pending_request_status=DB::table('request')->where('group_id',$get_request->group_id)->where('status','Pending');
                if($request_status->count()) {
                    $req_id=$request_status->get()->first()->id;
                    $trip_status=@DB::table('trips')->where('request_id',$req_id)->get()->first()->status;
                    return $trip_status;
                }
                elseif($pending_request_status->count()) {
                    return "Searching";
                }
                else {
                    return "No one accepted";
                }
            })
            ->addColumn('payment_status', function ($get_request) {
                return ($get_request->payment_status != null ) ? $get_request->payment_status : "Not Paid";
            })
            ->addColumn('total_amount', function ($get_request) {
                return ($get_request->total_fare!= null ) ? html_entity_decode($get_request->currency_symbol)." ".$get_request->total_fare : "N/A";
            })
            ->addColumn('action', function ($get_request) {
                return '<a href="'.url(LOGIN_USER_TYPE.'/detail_request/'.$get_request->id).'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-eye-open"></i></a>&nbsp;';
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param Request $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(RideRequest $model)
    {
        $get_request = DB::Table('request')
                        ->where(function($query)  {
                            if(LOGIN_USER_TYPE=='company') {
                                $query->join('users as drivers', function($join) {
                                    $join->on('drivers.id', '=', 'request.driver_id')
                                        ->where('drivers.company_id',auth('company')->id());
                                });
                            }
                        })
                        ->Leftjoin('trips', function($join) {
                            $join->on('trips.request_id', '=', 'request.id');
                        })
                        ->Leftjoin('currency', function($join) {
                            $join->on('currency.code', '=', 'trips.currency_code');
                        })
                        ->join('users', function($join) {
                            $join->on('users.id', '=', 'request.user_id');
                        })
                        ->join('car_type', function($join) {
                            $join->on('car_type.id', '=', 'request.car_id');
                        })                        
                        ->groupBy('group_id')
                        ->select(['request.id as id', 'users.first_name as first_name',DB::raw('CONCAT(currency.symbol, trips.total_fare) AS total_amount'),'request.group_id','request.payment_mode as payment_mode','trips.payment_status','request.updated_at','trips.total_fare','currency.symbol AS currency_symbol']);

        if(LOGIN_USER_TYPE=='company') {
            $get_request = $get_request
                ->join('users as drivers', function($join) {
                    $join->on('drivers.id', '=', 'request.driver_id')
                        ->where('drivers.company_id',auth('company')->id());
                });
        }
        return $get_request->get();
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
                    ->addAction()
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
            ['data' => 'id', 'name' => 'id', 'title' => 'Request id'],
            ['data' => 'first_name', 'name' => 'first_name', 'title' => 'Rider Name'],
            ['data' => 'date_time', 'name' => 'date_time', 'title' => 'Date and Time ' ,'orderable' => false, 'searchable' => false],
            ['data' => 'request_status', 'name' => 'request_status', 'title' => 'Status','orderable' => false, 'searchable' => false],
        ];
        if(LOGIN_USER_TYPE != 'company') {
            $columns[] = ['data' => 'total_amount', 'name' => 'trips.total_fare', 'title' => 'Amount'];
        }
        return array_merge($columns,[
            ['data' => 'payment_mode', 'name' => 'payment_mode', 'title' => 'Payment mode'],
            ['data' => 'payment_status', 'name' => 'payment_status', 'title' => 'Payment Status'],
        ]);
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'requests_' . date('YmdHis');
    }
}