@extends('admin.template')

@section('main')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Dashboard
      <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  @if(LOGIN_USER_TYPE=='company' || auth('admin')->user()->can('manage_trips'))
  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-dblue">
          <div class="inner">
            <h3> {{ html_string($currency_code) }} {{ round($total_revenue) }}</h3>
            <p>Total Earnings</p>
          </div>
          <div class="icon">
            <i class="fa fa-dollar"></i>
          </div>
          <a href="{{ url(LOGIN_USER_TYPE.'/trips') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>

      @if(LOGIN_USER_TYPE == 'company')
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3>{{ html_string($currency_code) }} {{ round($admin_paid_amount) }}</h3>

            <p> Received Amount </p>
          </div>
          <div class="icon">
            <i class="fa fa-dollar"></i>
          </div>
          <a href="{{ url(LOGIN_USER_TYPE.'/statements/overall') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      @endif

      @if(LOGIN_USER_TYPE!='company')
      <!-- ./col -->
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-yellow">
          <div class="inner">
            <h3>{{ $total_rider }}</h3>

            <p>Total Riders</p>
          </div>
          <div class="icon">
            <i class="fa fa-user"></i>
          </div>
          <a href="{{ url('admin/rider') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      @endif

      <!-- ./col -->
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
          <div class="inner">
            <h3>{{$total_driver}}</h3>

            <p>Total Drivers</p>
          </div>
          <div class="icon">
            <i class="fa fa-user-plus"></i>
          </div>
          <a href="{{ url(LOGIN_USER_TYPE.'/driver') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
          <div class="inner">
            <h3>{{$total_trips}}</h3>

            <p>Total Trips</p>
          </div>
          <div class="icon">
            <i class="fa fa-cab"></i>
          </div>
          <a href="{{ url(LOGIN_USER_TYPE.'/trips') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <!-- ./col -->
    </div>
    <!-- ./col -->
    <!-- /.row -->
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-light-blue">
          <div class="inner">
            <h3> {{ html_string($currency_code) }} {{ round($today_revenue) }}</h3>
            <p>Today Earnings</p>
          </div>
          <div class="icon">
            <i class="fa fa-dollar"></i>
          </div>
          <a href="{{ url(LOGIN_USER_TYPE.'/trips') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      @if(LOGIN_USER_TYPE == 'company')
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-orange">
          <div class="inner">
            <h3> {{ html_string($currency_code) }} {{ round($admin_pending_amount) }}</h3>

            <p> Pending Amount </p>
          </div>
          <div class="icon">
            <i class="fa fa-dollar"></i>
          </div>
          <a href="{{ url(LOGIN_USER_TYPE.'/statements/overall') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      @endif
      <!-- ./col -->
      <!-- ./col -->
      @if(LOGIN_USER_TYPE!='company')
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-teal">
          <div class="inner">
            <h3>{{ $today_rider_count }}</h3>

            <p>Today Riders</p>
          </div>
          <div class="icon">
            <i class="fa fa-user"></i>
          </div>
          <a href="{{ url('admin/rider') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      @endif
      <!-- ./col -->
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-maroon">
          <div class="inner">
            <h3>{{$today_driver_count}}</h3>

            <p>Today Drivers</p>
          </div>
          <div class="icon">
            <i class="fa fa-user-plus"></i>
          </div>
          <a href="{{ url(LOGIN_USER_TYPE.'/driver') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
          <div class="inner">
            <h3>{{$today_trips}}</h3>

            <p>Today Trips</p>
          </div>
          <div class="icon">
            <i class="fa fa-cab"></i>
          </div>
          <a href="{{ url(LOGIN_USER_TYPE.'/trips') }}" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
        </div>
      </div>

    </div>
    <!-- /.row -->
    <!-- Main row -->
    <div class="row">
      <!-- Left col -->
      <section class="col-lg-7 connectedSortable">
        <input type="hidden" value='{{ $line_chart_data }}' id="line-chart-data">
        <!-- [ {"y": "2013 Q1", "amount": 2666}, {"y": "2011 Q2", "amount": 2778}, {"y": "2011 Q3", "amount": 4912}, {"y": "2012 Q1", "amount": 6810}, {"y": "2012 Q2", "amount": 5670}, {"y": "2012 Q3", "amount": 4820}, {"y": "2013 Q1", "amount": 10687}, {"y": "2013 Q2", "amount": 8432}, {"y": "2016 Q3", "amount": 8432} ] -->
        <!-- solid sales graph -->
        <div class="box box-solid bg-teal-gradient">
          <div class="box-header">
            <i class="fa fa-th"></i>

            <h3 class="box-title">Sales Graph</h3>

            <div class="box-tools pull-right">
              <button type="button" class="btn bg-teal btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
              </button>
              <button type="button" class="btn bg-teal btn-sm" data-widget="remove"><i class="fa fa-times"></i>
              </button>
            </div>
          </div>
          <div class="box-body border-radius-none">
            <div class="chart" id="line-chart" style="height: 250px;"></div>
          </div>
        </div>
      </section>
      <!-- /.Left col -->
      <!-- right col (We are only adding the ID to make the widgets sortable)-->
      <section class="col-lg-5 connectedSortable">
        <!-- Calendar -->
        <div class="box box-solid bg-green-gradient">
          <div class="box-header">
            <i class="fa fa-calendar"></i>

            <h3 class="box-title">Calendar</h3>
            <div class="pull-right box-tools">
              <button type="button" class="btn btn-success btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
              </button>
              <button type="button" class="btn btn-success btn-sm" data-widget="remove"><i class="fa fa-times"></i>
              </button>
            </div>
          </div>
          <div class="box-body no-padding">
            <div id="calendar" style="width: 100%"></div>
          </div>
        </div>
      </section>
      <!-- right col -->
    </div>
    <!-- /.row (main row) -->
  </section>
  <!-- /.content -->

  <section class="content-header" style="padding: 0px 15px 15px 15px;">
    <div class="col-lg-6 recent_rides_section nopadding">
      <h3>
        Recent Ride Requests
        <span id="close_recent"><i class="fa fa-close"></i></span>
      </h3>
      @if($recent_trips->count())
      <table class="recent_rides_table">
        <tr>
          <th>Group ID</th>
          <th>Rider name</th>
          <th>Dated on</th>
          <th>Status</th>
          <th></th>
        </tr>
        @foreach($recent_trips as $row_trips)
        <tr data-toggle="collapse" data-target="#accordion{{  $row_trips->group_id }}" class="clickable">
          <td><a href="{{ url('/').'/'.LOGIN_USER_TYPE }}/detail_request/{{ $row_trips->id }}">#{{ $row_trips->id }}</a></td>
          <td>{{ $row_trips->users->first_name }}</td>
          <td class="text-nowrap">{{ $row_trips->date_time }}</td>
          @php
          $request_status=DB::table('request')->where('group_id',$row_trips->group_id)->where('status','Accepted');
          $pending_request_status=DB::table('request')->where('group_id',$row_trips->group_id)->where('status','Pending')->count();
          @endphp
          @if($request_status->count() > 0)
          @php
          $req_id=$request_status->get()->first()->id;
          $trip_status=@DB::table('trips')->where('request_id',$req_id)->get()->first()->status;
          @endphp
          <td class="text-nowrap"><span class="dash_status {{ @$trip_status }}">{{ @$trip_status }}</span></td>
          @elseif($pending_request_status)
          <td class="text-nowrap"><span class="dash_status Searching">Searching</span></td>
          @else
          <td class="text-nowrap"><span class="dash_status Searched">No one accepted</span></td>
          @endif
          <td>
            <i class="fa fa-caret-down" aria-hidden="true"></i>
          </td>
        </tr>
        <tr id="accordion{{  $row_trips->group_id }}" class="table-wrap-row collapse">
         <td colspan="5">
          <table>
            <tr><th>Driver Name</th><th>status</th></tr>
            @foreach($row_trips->request as $val)
            <tr>
             <td>{{ $val->driver->first_name }}</td>
             <td>{{ ($val->status=="Cancelled") ? 'Not Accepted' : $val->status }}</td></tr>
             @endforeach             
           </table>
         </td>
       </tr>
       @endforeach
     </table>
     @else
     <small>Recently no Rides found</small>
     @endif
   </div>
 </section>
 @else
 <div style="height: 80vh;text-align: center;padding-top: 150px;font-size: 15px;">
  Welcome to Dispatcher panel
</div>
@endif
</div>
<!-- /.content-wrapper -->
@stop