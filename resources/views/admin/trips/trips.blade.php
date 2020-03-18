@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" ng-controller="reports">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Trips
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/trips') }}">Trips</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- right column -->
        <div class="col-md-12">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Trips Form</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="form-group">
                  <label class="col-sm-1 control-label">From</label>
                  <div class="col-sm-2">
                  <input type="text" ng-model="from" ng-change="report(from, to)" class="form-control date" placeholder="From Date">
                  </div>
                  <label class="col-sm-1 control-label">To</label>
                  <div class="col-sm-2">
                  <input type="text" ng-model="to" ng-change="report(from, to)" class="form-control date" placeholder="To Date">
                  </div>
                 
                </div>
            </div>
            
            <div class="box-body print_area" id="trips" ng-show="reservations_report.length">
            <div class="text-center"><h4>Trips Report (@{{ from }} - @{{ to }})</h4></div>
              <table class="table">
                <thead>
                  <th>Id</th>
                  <th>From Location</th>
                  <th>To Location</th>
                  <th>Date</th>
                  <th>Driver Name</th>
                  <th>Rider Name</th>
                  <th>Fare</th>
                  <th>Vehicle Details</th>
                  <th>Status</th>
                  <th>Created At</th>
                  <th>Action</th>
                </thead>
                <tbody>
                  <tr ng-repeat="item in reservations_report">
                    <td>@{{ item.id }}</td>
                    <td>@{{ item.pickup_location }}</td>
                    <td>@{{ item.drop_location }}</td>
                    <td>@{{ item.begin_trip }}</td>
                    <td>@{{ item.driver_name }}</td>
                    <td>@{{ item.rider_name }}</td>
                    <td><span ng-bind-html="item.total_amount"></span></td>
                    <td>@{{ item.car_name }}</td>
                    <td>@{{ item.status }}</td>
                    <td>@{{ item.created_at }}</td>
                    <td ><a href="{{url('admin/view_trips')}}/@{{item.id}}"><i class="glyphicon glyphicon-edit"></i></a></td>
                  </tr>
                </tbody>
              </table>
              <br>
            </div>
            <div class="text-center" id="print_footer" ng-show="users_report.length || rooms_report.length || reservations_report.length">
              <a class="btn btn-success" id="export" href="{{ url('admin/trips/export') }}/@{{ from }}/@{{ to }}"><i class="fa fa-file-excel-o"></i> Export</a>
              <button class="btn btn-info" ng-click="print(category)"><i class="fa fa-print"></i> Print</button>
            </div>
            <br>
          </div>
          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <style type="text/css">
  @media print {
  body * {
    visibility: hidden;
  }
  .print_area * {
    visibility: visible;
  }
  .print_area {
    position: absolute;
    left: 0;
    top: 0;
  }
}
</style>
@stop