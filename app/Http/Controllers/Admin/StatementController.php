<?php
/**
 * Statement Controller
 *
 * @package     Gofer
 * @subpackage  Controller
 * @category    Statements
 * @author      Trioangle Product Team
 * @version     2.2
 * @link        http://trioangle.com
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\DataTables\ProviderstatementDataTable;
use Yajra\DataTables\Services\DataTable;
use App\Models\Category;
use App\Models\Trips;
use App\Models\User;
use App\Models\Currency;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use DataTables;
use DB;
use Carbon\Carbon;

class StatementController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,ProviderstatementDataTable $providerdataTable)
    {
        $type=$request->type;
        $data['overall_earning']=0;
        $data['overall_commission']=0;
        $data['overall_rides']=0;
        $data['overall_cancelled_rides']=0;
        if($type == "overall") {
            return view('admin.statements.main');    
        }
        
        if($type=="driver") {
            return $providerdataTable->render('admin.statements.provider');
        }

        abort("404");
    }

    public function custom_statement(Request $request)
    {
        $trips = Trips::with(['currency','driver_payment'])
                ->join('users', function ($q) {
                    $q->on('trips.driver_id', '=', 'users.id');
                })
                ->leftJoin('companies', function ($q) {
                    $q->on('users.company_id', '=', 'companies.id');
                });

        if (LOGIN_USER_TYPE=='company') { //If login user is company then get that company driver trips only
            $trips = $trips->whereHas('driver',function($q){
                $q->where('company_id',auth('company')->id());
            });
        }
        
        $filter_type=$request->filter_type;
        if($filter_type=="custom") {
            $from=date('Y-m-d' . ' 00:00:00', strtotime($request->from_dates));
            if($request->has('to_dates'))
            {
                $to=date('Y-m-d' . ' 23:59:59', strtotime($request->to_dates));
                $trips = $trips->whereBetween('trips.created_at', array($from, $to));    
            }
        }
        elseif($filter_type=="daily") {
            $trips = $trips->whereRaw('Date(trips.created_at) = CURDATE()');    
        }
        elseif($filter_type=="weekly") {
            $fromDate = Carbon::now()->subDay()->startOfWeek()->toDateString();
            $tillDate = Carbon::now()->subDay()->endOfWeek()->toDateString();
            $trips = $trips->whereBetween( DB::raw('date(trips.created_at)'), [$fromDate, $tillDate] );    
        }
        elseif($filter_type=="monthly") {
            $trips = $trips->whereRaw('MONTH(trips.created_at) = ?',[date('m')]);    
        }
        elseif($filter_type=="yearly") {
            $trips = $trips->whereRaw('YEAR(trips.created_at) = ?',[date('Y')]);    
        }
        $trips = $trips->select('trips.*','companies.name as company_name')->get();

        $datatable=DataTables::of($trips)
            ->addColumn('id', function ($trips) {   
                return @$trips->id;
            })
            ->addColumn('pickup_location', function ($trips) {   
                return @$trips->pickup_location;
            })
            ->addColumn('drop_location', function ($trips) {   
                return @$trips->drop_location;
            })
            ->addColumn('action', function ($trips) {
                return '<a href="'.url(LOGIN_USER_TYPE.'/view_trips/'.$trips->id).'?s=overall" class="btn btn-xs btn-primary">View Trip Details</a>';
            })
            ->addColumn('commission', function ($trips) { 
                if (LOGIN_USER_TYPE == 'company') {
                    //If login user is company then commission value is company commission to admin
                    return html_entity_decode(@$trips->currency->symbol).number_format($trips->company_admin_commission, 2);
                }
                else {
                    //If login user is admin then commission value is trip commission (Sum of all commission to admin)
                    return html_entity_decode(@$trips->currency->symbol).number_format($trips->commission, 2);
                }
            })
            ->addColumn('total_amount', function ($trips) {
                return html_entity_decode(@$trips->currency->symbol).number_format($trips->company_driver_earnings, 2);
            })
            ->addColumn('company_name', function ($trips) {
                return @$trips->company_name;
            })
            ->addColumn('admin_payout_status', function ($trips) {
                $admin_payout_status = ($trips->payment_mode == 'Cash' || $trips->payment_mode == 'Cash & Wallet' || $trips->driver_payout == 0) ? '-' : @$trips->driver_payment->admin_payout_status;
                return $admin_payout_status;
            })
            ->addColumn('dated_on', function ($trips) {   
                return @date('Y-m-d',strtotime($trips->created_at));
            });
        $columns = ['id','pickup_location', 'drop_location', 'commission','dated_on','status','total_amount'];
        $base = new DataTableBase($trips, $datatable, $columns, 'statements_');
        return $base->render(null);
    }

    public function get_statement_counts(Request $request)
    {
        $trips = Trips::with(['users','driver', 'currency','car_type']);
        if (LOGIN_USER_TYPE=='company') {   //If login user is company then get that company driver trips only
            $trips = $trips->whereHas('driver',function($q){
                $q->where('company_id',auth('company')->user()->id);
            });
        }
        $filter_type=$request->filter_type;
        $count_text="Overall Statement";
        if($filter_type=="custom") {
            $from=date('Y-m-d' . ' 00:00:00', strtotime($request->from_dates));
            if($request->has('to_dates')) {
                $to=date('Y-m-d' . ' 23:59:59', strtotime($request->to_dates));
                $trips = $trips->whereBetween('created_at', array($from, $to));    
                $count_text="Statement from ".$request->from_dates." to ".$request->to_dates;
            }
        }
        elseif($filter_type=="daily") {
            $trips = $trips->whereRaw('Date(created_at) = CURDATE()');
            $count_text = "Today Statement - ".date('d M Y');    
        }
        elseif($filter_type=="weekly") {
            $fromDate = Carbon::now()->subDay()->startOfWeek()->toDateString();
            $tillDate = Carbon::now()->subDay()->endOfWeek()->toDateString();
            $trips = $trips->whereBetween( DB::raw('date(created_at)'), [$fromDate, $tillDate] );    
            $count_text = "This Week Statement : ".$fromDate." to ".$tillDate;
        }
        elseif($filter_type=="monthly") {
            $trips = $trips->whereRaw('MONTH(created_at) = ?',[date('m')]);   
            $count_text = "This Month Statement - ".date('F'); 
        }
        elseif($filter_type=="yearly") {
            $trips = $trips->whereRaw('YEAR(created_at) = ?',[date('Y')]);    
            $count_text = "This Year Statement - ".date('Y');
        }
        $tripsmy=clone($trips);
        $default_currency = view()->shared('default_currency');

        //If login user is company then get currency from session
        if (LOGIN_USER_TYPE=='company' && session('currency') != null) {
            $default_currency = Currency::whereCode(session('currency'))->whereStatus('Active')->first();
            if(!$default_currency)
            {
               $default_currency = Currency::DefaultCurrency()->first();
            }
        }
        // $trips->select(DB::raw('SUM(total_fare) as total_amount'),DB::raw('SUM(access_fee) as total_commission'),DB::raw('COUNT(id) as total_rides'));
        $trips_other=$trips->where('status', 'Completed')->get();

        $total_amount = $trips_other->sum('company_driver_earnings');

        $total_commission = $trips_other->sum(function ($trip) {
            if (LOGIN_USER_TYPE == 'company') {  //If login user is company then commission value is company commission to admin
                return $trip->company_admin_commission;
            }else{  //If login user is admin then commission value is trip commission (Sum of all commission to admin)
                return $trip->commission;
            }
        });

        $trips_cancelled=$tripsmy->where('trips.status','Cancelled')->get()->count();

        $return_data['overall_earning']=html_entity_decode($default_currency->symbol) .' '.@$total_amount;

        $return_data['overall_commission']=html_entity_decode($default_currency->symbol) .' '.@$total_commission;
        $return_data['total_rides']=@$trips_other->count();
        $return_data['cancelled_rides']=@$trips_cancelled;
        $return_data['count_text']=@$count_text;
        return json_encode($return_data);
    }

    public function view_driver_statement(Request $request)
    {
        $trips = Trips::with(['users','driver', 'currency','car_type'])->where('trips.driver_id',$request->driver_id);

        if(LOGIN_USER_TYPE=='company') {  //If login user is company then get that company driver trips only
            $trips = $trips->whereHas('driver',function($q1){
                $q1->where('company_id',auth('company')->user()->id);
            });
        }

        $filter_type=$request->filter_type;
        
        $tripsmy=clone($trips);
        $driver_trip=clone($trips);

        $trips_other=$trips->where('status', 'Completed')->get();

        $total_amount = $trips_other->sum(function ($trip) {
            return $trip->company_driver_earnings;
        });

        $total_commission = $trips_other->sum(function ($trip) {
            if (LOGIN_USER_TYPE=='company') {  //If login user is company then commission value is company commission to admin
                return $trip->company_admin_commission;
            }else{  //If login user is admin then commission value is trip commission (Sum of all commission to admin)
                return $trip->commission;
            }
        });

        $trips_cancelled=$tripsmy->where('trips.status','Cancelled')->get()->count();
        $driver_trip=$driver_trip->get();
        $count_text=@$driver_trip[0]->driver->first_name." 's Overall Statement - Joined ".@$driver_trip[0]->driver->date_time_join;
        $data['overall_earning']=html_entity_decode(@$trips_other[0]->currency->symbol).$total_amount;
        $data['overall_commission']=html_entity_decode(@$trips_other[0]->currency->symbol).$total_commission;
        $data['overall_rides']=@$trips_other->count('id');
        $data['cancelled_rides']=@$trips_cancelled;
        $data['count_text']=@$count_text;
        $data['driver_id']=$request->driver_id;
        return view('admin.statements.driver_statement',$data);  
    }

    public function driver_statement(Request $request)
    {
        $trips = Trips::with(['users','driver','currency','car_type']);
        if(LOGIN_USER_TYPE=='company') {  //If login user is company then get that company driver trips only
            $trips = $trips->whereHas('driver',function($q1){
                $q1->where('company_id',auth('company')->user()->id);
            });
        }
        $trips=$trips->where('trips.driver_id',$request->driver)->get();
        $datatable=DataTables::of($trips)
            ->addColumn('id', function ($trips) {   
                return @$trips->id;
            })
            ->addColumn('pickup_location', function ($trips) {   
                return @$trips->pickup_location;
            })
            ->addColumn('drop_location', function ($trips) {   
                return @$trips->drop_location;
            })
            ->addColumn('action', function ($trips) {   
                return '<a href="'.url(LOGIN_USER_TYPE.'/view_trips/'.$trips->id).'?s=driver" class="btn btn-xs btn-primary">View Trip Details</a>';
            })
            ->addColumn('commission', function ($trips) {
                if (LOGIN_USER_TYPE=='company') {  //If login user is company then commission value is company commission to admin
                    return html_entity_decode(@$trips->currency->symbol).$trips->company_admin_commission;
                }else{  //If login user is admin then commission value is trip commission (Sum of all commission to admin)
                    return html_entity_decode(@$trips->currency->symbol).($trips->access_fee + ( $trips->peak_amount - $trips->driver_peak_amount) + $trips->schedule_fare  + $trips->driver_or_company_commission);
                }
            })
            ->addColumn('total_amount', function ($trips) {   
                return html_entity_decode(@$trips->currency->symbol).($trips->company_driver_earnings);
            })
            ->addColumn('dated_on', function ($trips) {   
                return @date('Y-m-d',strtotime($trips->created_at));
            });
        $columns = ['id','pickup_location', 'drop_location', 'commission','dated_on','status','total_amount'];
        $base = new DataTableBase($trips, $datatable, $columns,'driver_statements_');
        return $base->render(null);
    }
}