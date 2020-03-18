@extends('admin.template')

@section('main')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <!-- Main content -->
  <section class="content" ng-clock >
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          
          <!-- /.box-header -->
          <div class="box-body">
            <br/>
            <input type="hidden" value="{{ $driver_id }}" id="driver_id">
            <div class="box-header">
            <h3 class="box-title">{{ $count_text }}</h3>
            </div>
            <div class="box-header">
            <div class="row">
              <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-dblue">
                  <div class="inner">
                    <h3>{{ $overall_earning }}</h3>
                    <p>{{LOGIN_USER_TYPE=='company'?'Earnings':'Total Amount Received'}}</p>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-orange">
                  <div class="inner">
                    <h3>{{ $overall_commission }}</h3>
                    <p>{{LOGIN_USER_TYPE=='company'?'Admin Commission':'Total Earnings'}}</p>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                  <div class="inner">
                    <h3>{{ $overall_rides }}</h3>
                    <p>Completed Rides</p>
                  </div>
                </div>
              </div>
              <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                  <div class="inner">
                    <h3>{{ $cancelled_rides }}</h3>
                    <p>Cancelled Rides</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
            <table id="driver_statement_table" class="table table-condensed">
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection

@push('scripts')
<style>
.dataTables_length
{
  display: inline-block;
  float: right;
  padding: 0 10px;
}
</style>
<link rel="stylesheet" href="{{ url('css/buttons.dataTables.css') }}">
<script src="{{ url('js/dataTables.buttons.js') }}"></script>
<script src="{{ url('js/buttons.server-side.js') }}"></script>
<script>
  var oTable = $('#driver_statement_table').DataTable({
    dom:"lBfrtip",
    buttons:["csv","excel","print"],
    order:[0, 'desc'],
    processing: true,
    serverSide: true,
    ajax: {
      url: '{{ url(LOGIN_USER_TYPE."/driver_statement")  }}',
      data: function (d) {
        d.driver = $('#driver_id').val();
      }
    },
    columns: [
    {data: 'id', name: 'id', title: 'Booking ID'},
    {data: 'pickup_location',name: 'pickup_location',title: 'Pickup Location'},
    {data: 'drop_location',name: 'drop_location',title: 'Drop Location'},
    {data: 'action',name: 'action',title: 'Trips Details',orderable: false,searchable: false},
    {data: 'commission',name: 'commission',title: 'Admin Commission',searchable: false},
    {data: 'dated_on',name: 'dated_on',title: 'Dated on'},
    {data: 'status',name: 'status',title: 'Status'},
    {data: 'total_amount',name: 'total_amount',title: 'Earned'}
    ]
  });
  
  $('#custom_statement').on('submit', function(e) {
    oTable.draw();
    e.preventDefault();
  });
</script>
@endpush

