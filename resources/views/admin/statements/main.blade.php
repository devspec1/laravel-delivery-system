@extends('admin.template')
@section('main')
<div class="content-wrapper" ng-controller="statements" ng-cloak>
	<section class="content-header">
		<h1> Overall <small> Statements </small>
		</h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a>
			</li>
			<li class="active">
				Statements
			</li>
		</ol>
	</section>
	<section class="content" ng-clock >
		<div class="row">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-body">
						<div class="panel panel-default">
							<div class="panel-body">
								<form method="POST" id="custom_statement" class="form-inline" role="form">
									<div class="form-group">
										<label for="name">Filter By</label><br>
										<select  ng-init="filter_by = filter_by || 'overall'" class="form-control" name="filter_by" ng-model="filter_by"  id="filter_by">
											<option value="overall">Overall</option>
											<option value="daily">Today</option>
											<option value="weekly">Weekly</option>
											<option value="monthly">Monthly</option>
											<option value="yearly">Yearly</option>
											<option value="custom">Custom</option>
										</select>
									</div>
									<div class="form-group" ng-if="filter_by=='custom'">
										<label for="email">From Date</label><br>
										<input type="text" class="form-control date" name="from_date" id="from_date" placeholder="From Date">
									</div>
									<div class="form-group" ng-if="filter_by=='custom'">
										<label for="email">To Date</label><br>
										<input type="text" class="form-control date" name="to_date" id="to_date" placeholder="To Date">
									</div>
									<div class="form-group">
										<br>
										<button style="margin-bottom: 5px;" type="submit" class="btn btn-primary">Search</button>
									</div>
									
								</form>
							</div>
						</div>
						<div class="box-header">
							<h3 class="box-title">@{{ count_text }}</h3>
						</div>
						<div class="box-header">
							<div class="row">
								<div class="col-lg-3 col-xs-6">
									<div class="small-box bg-dblue">
										<div class="inner">
											<h3>@{{ overall_earning }}</h3>
											<p>{{LOGIN_USER_TYPE=='company'?'Earnings':'Total Amount Received'}}</p>
										</div>
									</div>
								</div>
								<div class="col-lg-3 col-xs-6">
									<div class="small-box bg-orange">
										<div class="inner">
											<h3>@{{ overall_commission }}</h3>
											<p>{{LOGIN_USER_TYPE=='company'?'Admin Commission':'Total Earnings'}}</p>
										</div>
									</div>
								</div>
								<div class="col-lg-3 col-xs-6">
									<div class="small-box bg-green">
										<div class="inner">
											<h3>@{{ overall_rides }}</h3>
											<p>Completed Rides</p>
										</div>
									</div>
								</div>
								<div class="col-lg-3 col-xs-6">
									<div class="small-box bg-yellow">
										<div class="inner">
											<h3>@{{ cancelled_rides }}</h3>
											<p>Cancelled Rides</p>
										</div>
									</div>
								</div>
							</div>
						</div>
						<table id="statement_table" class="table table-condensed">
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
.dataTables_length {
	display: inline-block;
	float: right;
	padding: 0 10px;
}
</style>

<link rel="stylesheet" href="{{ url('css/buttons.dataTables.css') }}">
<script src="{{ url('js/dataTables.buttons.js') }}"></script>
<script src="{{ url('js/buttons.server-side.js') }}"></script>

<script>
var table_columns = [{data: 'id', name: 'id', title: 'Booking ID'}];
if(LOGIN_USER_TYPE == 'admin') {
	table_columns.push({data: 'company_name', name: 'company_name', title: 'Company Name'});
}
table_columns.push(
	{data: 'pickup_location',name: 'pickup_location',title: 'Pickup Location'},
	{data: 'drop_location',name: 'drop_location',title: 'Drop Location'},
	{data: 'action',name: 'action',title: 'Trips Details',orderable: false,searchable: false},
	{data: 'commission',name: 'commission',title: 'Admin commission',searchable: false},
	{data: 'admin_payout_status',name: 'admin_payout_status',title: 'Admin Payout Status',orderable: false,searchable: false},
	{data: 'created_at',name: 'created_at',title: 'Dated on'},
	{data: 'status',name: 'status',title: 'Status'},
	{data: 'total_amount',name: 'driver_payout',title: 'Earned'}
);

var oTable = $('#statement_table').DataTable({
	dom:"lBfrtip",
	buttons:["csv","excel","print"],
	order:[0, 'desc'],
	processing: true,
	serverSide: true,
	ajax: {
		url: '{{ url(LOGIN_USER_TYPE."/statement_all")  }}',
		data: function (d) {
			d.filter_type = $('#filter_by').val();
			d.from_dates = $('#from_date').val();
			d.to_dates = $('#to_date').val();
		}
	},
	columns: table_columns
});

app.controller('statements', ['$scope', '$http', function($scope, $http) {
	$scope.count_text="Overall Statement";
	$('#custom_statement').on('submit', function(e) {
		oTable.draw();
		$scope.count_section();
		e.preventDefault();
	});
	$scope.count_section=function() {
		$http.post(APP_URL+"/{{LOGIN_USER_TYPE}}/get_statement_counts", { from_dates: $('#from_date').val(), to_dates: $('#to_date').val(), filter_type: $('#filter_by').val() }).then(function( response ) {
			$scope.count_text=response.data.count_text;
			$scope.overall_earning=response.data.overall_earning;
			$scope.overall_commission=response.data.overall_commission;
			$scope.overall_rides=response.data.total_rides;
			$scope.cancelled_rides=response.data.cancelled_rides;
			setTimeout(() => $('button[type="submit"]').prop('disabled', false) , 0);
		});
	};
	
	$(document).ready(function() {
		$scope.count_section();
		$( "#filter_by").change(function() {
			var value = $("#filter_by option:selected").val();
			if(value =='orders') {
				$('#from_date').datepicker('option', 'maxDate', '')
				$('#from_date').datepicker('refresh');
				$('#to_date').datepicker('option', 'maxDate', '')
				$('#to_date').datepicker('refresh');
			}
			else {
				var nowTemp = new Date();
				var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
				var checkin = $('#from_date').datepicker({
					minDate: '-1',
					forceParse: false,
					multidate: false,
					autoclose: true
				}).on('changeDate', function(ev) {
					if (ev.date.valueOf() > checkout.datepicker("getDate").valueOf() || !checkout.datepicker("getDate").valueOf()) {
						var newDate = new Date(ev.date);
						newDate.setDate(newDate.getDate() + 1);
						checkout.datepicker("update", newDate);
					}
					var newDate = new Date(ev.date);
					newDate.setDate(newDate.getDate() + 1);
					checkout.datepicker("setStartDate", newDate);
					$('#to_date')[0].focus();
				});
				var checkout = $('#to_date').datepicker({
					minDate: '0',
					forceParse: false,
					beforeShowDay: function(date) {
						if (!checkin.datepicker("getDate").valueOf()) {
							return date.valueOf() >= new Date().valueOf();
						}
						return date.valueOf() > checkin.datepicker("getDate").valueOf();
					},
					autoclose: true
				});
			}
		});
	});
}]);
</script>
@endpush