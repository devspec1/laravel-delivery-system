@extends('admin.template')
@section('main')
<style type="text/css">
	.loader {
		border: 2px solid #f3f3f3;
		border-radius: 50%;
		border-top: 2px solid blue;
		border-bottom: 2px solid blue;
		width: 20px;
		height: 20px;
		-webkit-animation: spin 2s linear infinite;
		animation: spin 2s linear infinite;
	}

	@-webkit-keyframes spin {
		0% { -webkit-transform: rotate(0deg); }
		100% { -webkit-transform: rotate(360deg); }
	}

	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}
</style>
<div class="manual-booking content-wrapper" ng-controller='manual_booking' ng-init="vehicle_types = {{$vehicle_types}}">
	<section class="content-header">
		<h1>
			Manual Booking
		</h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}">
					<i class="fa fa-dashboard"></i> Home
				</a>
			</li>
			<li class="active">Manual Booking</li>
		</ol>
	</section>
	{!! Form::open(['method'=>'POST','url' => LOGIN_USER_TYPE.'/manual_booking/store', 'class' => 'form-horizontal manual_booking','id'=>'manual_booking','name'=>'myForm']) !!}
	<section class="content">
		{!! Form::hidden('manual_booking_id', @$schedule_ride->id, ['id' => 'manual_booking_id']) !!}
		{!! Form::hidden('auto_assign_id', @$schedule_ride->driver_id, ['id' => 'auto_assign_id']) !!}
		{!! Form::hidden('utc_offset', '', ['id' => 'utc_offset']) !!}
		{!! Form::hidden('location_id', @$schedule_ride->location_id, ['id' => 'location_id']) !!}
		{!! Form::hidden('peak_id', @$schedule_ride->peak_id, ['id' => 'peak_id']) !!}
		<h4>Manual Taxi Dispatch</h4>
		<div class="row">
			<div class="col-md-3" ng-init="country_code={{(@$schedule_ride->users->country_code==null)?$country_code_option[0]->phone_code:@$schedule_ride->users->country_code}}">
				<select class ='form-control selectpicker' data-live-search="true" id="input_country_code" name='country_code' ng-model="country_code">
					@foreach($country_code_option as $country_code)
					<option value="{{@$country_code->phone_code}}">{{$country_code->long_name}}</option>
					@endforeach
				</select>
				<span class="text-danger error_msg">{{ $errors->first('country_code') }}</span>
			</div>
			<div class="col-md-3 number-field">
				<div class="col-md-5 col-lg-4">
					<input type="text" disabled name="country_code_view" class ='form-control' id = 'country_code_view'>
					<span class="text-danger error_msg">{{ $errors->first('country_code') }}</span>
				</div>
				<div class="col-md-5 col-lg-4">
					<span class="text-danger error_msg">{{ $errors->first('country_code') }}</span>
				</div>
				<div class="col-md-7 col-lg-8">
					{!! Form::text('mobile_number', @$schedule_ride->users->mobile_number, ['class' => 'form-control', 'id' => 'input_mobile_number', 'placeholder' => 'Phone No', 'autocomplete' => 'off']) !!}
					<span class="text-danger error_msg">{{ $errors->first('mobile_number') }}</span>
				</div>
			</div>
			<div class="col-md-2 form-group m-0">
				{!! Form::text('first_name', @$schedule_ride->users->first_name, ['class' => 'form-control', 'id' => 'input_first_name', 'placeholder' => 'First Name', 'autocomplete' => 'off']) !!}
				<span class="text-danger error_msg">{{ $errors->first('first_name') }}</span>
			</div>
			<div class="col-md-2 form-group m-0">
				{!! Form::text('last_name', @$schedule_ride->users->last_name, ['class' => 'form-control', 'id' => 'input_last_name', 'placeholder' => 'Last Name', 'autocomplete' => 'off']) !!}
				<span class="text-danger error_msg">{{ $errors->first('last_name') }}</span>
			</div>
			<div class="col-md-2 form-group m-0">
				{!! Form::text('email', @$schedule_ride->users->email, ['class' => 'form-control', 'id' => 'input_email', 'placeholder' => 'Email', 'autocomplete' => 'off']) !!}
				<span class="text-danger error_msg">{{ $errors->first('email') }}</span>
			</div>
		</div>
		<div class="clearfix">
			<div class="col-md-4 location-form">
				<div class="row pick-location clearfix">
					<div class="col-md-12" ng-init='pickup_latitude = "{{@$schedule_ride->pickup_latitude}}"'>
						{!! Form::hidden('pickup_latitude', @$schedule_ride->pickup_latitude, ['id' => 'pickup_latitude']) !!}
						{!! Form::hidden('pickup_longitude', @$schedule_ride->pickup_longitude, ['id' => 'pickup_longitude']) !!}
						{!! Form::text('pickup_location', @$schedule_ride->pickup_location, ['class' => 'form-control change_field', 'id' => 'input_pickup_location', 'placeholder' => 'Pick Up Location', 'autocomplete' => 'off']) !!}
						<span class="text-danger error_msg error_pickup_location">{{ $errors->first('pickup_location') }}</span>
					</div>
				</div>
				<div class="row pick-location clearfix">
					<div class="col-md-12">
						{!! Form::hidden('drop_latitude', @$schedule_ride->drop_latitude, ['id' => 'drop_latitude']) !!}
						{!! Form::hidden('drop_longitude', @$schedule_ride->drop_longitude, ['id' => 'drop_longitude']) !!}

						{!! Form::text('drop_location', @$schedule_ride->drop_location, ['class' => 'form-control change_field', 'id' => 'input_drop_location', 'placeholder' => 'Drop Off Location', 'autocomplete' => 'off']) !!}
						<span class="text-danger error_msg error_drop_location">{{ $errors->first('drop_location') }}</span>
					</div>
				</div>
				<div class="row clearfix">
					<div class="col-md-12" ng-init='date_time = "{{@$schedule_ride->schedule_date}} {{@$schedule_ride->schedule_time}}"'>
						{!! Form::text('date_time',(@$schedule_ride->schedule_date==null)?'':@$schedule_ride->schedule_date.' '.@$schedule_ride->schedule_time, ['class' => 'form-control change_field', 'id' => 'input_date_time', 'placeholder' => 'Select Date/Time Location','ng-cloak','disabled'=>isset($schedule_ride->id)?false:true]) !!}
						<span class="text-danger error_msg"></span>
					</div>
				</div>
				<div class="row clearfix" ng-init="vehicle_type_value = '{{@$schedule_ride->car_id}}'">
					<div class="col-md-12">
						<select class='form-control change_field' ng-cloak placeholder ='Select Vehicle Type' name="vehicle_type" id="input_vehicle_type" ng-model="vehicle_type_value" ng-change="list_driver()" disabled >
							<option value="" disabled>Select Vehicle Type</option>
							<option ng-repeat="(key,vehicle_type) in vehicle_types" value="@{{vehicle_type.id}}" key=@{{key}} @if(isset($schedule_ride->id)) ng-selected="@{{key}} == @{{vehicle_type_value}}" @endif >@{{vehicle_type.car_name}}</option>
						</select>
						<span class="text-danger error_msg error_vehicle_type"></span>
					</div>
				</div>
				<div class="row clearfix" ng-init="auto_assign_status = {{(@$schedule_ride->driver_id==0 && @$schedule_ride->id != '')?'true':'false'}}">
					<div class="col-md-12">
						<p id="auto_assign_status_error">
							<input class="change_field" id="input_auto_assign_status" ng-model="auto_assign_status" type="checkbox" name="auto_assign_status" data-error-placement="container" data-error-container="#auto_assign_status_error"> Auto Assign Driver <br>
							<span class="text-danger error_msg"></span>
						</p>
					</div>
				</div>
				<div class="row assigned_driver" style="display: none">
					<div class="col-md-12" ng-cloak style="background-color: #222c31;color: white">
						Assigned Driver: @{{assigned_driver.first_name}} @{{assigned_driver.hidden_mobile_number}}</div>
				</div>
				<div class="row clearfix">
					<div class="col-md-12">
						{!! Form::text('search_driver', '', ['class' => 'form-control', 'id' => 'input_search_driver', 'placeholder' => 'Type driver name to search from below list','ng-model'=>'search_driver']) !!}
						<span class="text-danger error_msg"></span>
					</div>
				</div>
				<div class="driver_list_div">
					<div style="display: none;" class="loader"></div>
					<div style="display: none;" class="driver_list">
						<div class="row clearfix" ng-repeat="driver in drivers | filter:Driverfilter(search_driver) | filter:{ driver_current_status: driver_availability }">
							<span class="driver_detail_view" id=@{{driver.id}} first_name=@{{driver.first_name}} last_name=@{{driver.last_name}} phone=@{{driver.hidden_mobile_number}} email=@{{driver.email}} company=@{{driver.company}}> 
								<a href="{{ url(LOGIN_USER_TYPE.'/edit_driver') }}/@{{driver.id}}" target="_blank" style="color: black">
									<div class="col-md-3">
										<img ng-src="@{{driver.src}}" width="100%" height="auto">
									</div>
									<div class="col-md-6">
										<p>@{{driver.first_name}}</p>
										<p>@{{driver.hidden_mobile_number}}</p>
									</div>
								</a>
								<div class="col-md-3">
									<span class="btn btn-primary" ng-click="auto_assign(driver.id)">Assign</span>
								</div>
							</span>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-8 map-wrap">
				<div class="map-route-option">
					<div>
						<div class="clearfix driver_detail_popup" style="width:100%;background:white;display:none;position: absolute;z-index: 100">
							<h4>Driver Details</h4>
							<div class="row">
								<div class="col-lg-4">
									Driver :
								</div>
								<div class="col-lg-8 driver_name_detail">

								</div>
							</div>
							@if(LOGIN_USER_TYPE!='company')
								<div class="row driver_company_show">
									<div class="col-lg-4">
										Company :
									</div>
									<div class="col-lg-8 driver_company_detail">

									</div>
								</div>
							@endif
							<div class="row">
								<div class="col-lg-4">
									Driver Email :
								</div>
								<div class="col-lg-8 driver_email_detail">

								</div>
							</div>
							<div class="row">
								<div class="col-lg-4">
									Phone No :
								</div>
								<div class="col-lg-8 driver_phone_detail">

								</div>
							</div>
						</div>
						<div class="clearfix">
							<div class="col-md-4 p-0">
								<label>
									Select Driver Availability
								</label>
							</div>
							<div class="col-md-6" ng-init="driver_availability = ''">
								<select class ='form-control change_field' id = 'input_driver_availability' name='driver_availability' ng-model="driver_availability" ng-change="list_driver()">
								</select>
								<span class="text-danger error_msg">{{ $errors->first('driver_availability') }}</span>
							</div>
						</div>
						<div class="clearfix map_zoom_level">
							<div class="col-md-4 p-0">
								<label>
									Map Zoom Level
								</label>
							</div>
							<div class="col-md-6" ng-init="map_radius=0">
								<select class ='form-control' id = 'input_map_zoom' name='map_zoom' ng-model="map_radius" ng-change="map_zoom(map_radius)">
									<option value="0">Select Radius</option>
									<option value="5">5 Miles Radius</option>
									<option value="10">10 Miles Radius</option>
									<option value="20">20 Miles Radius</option>
									<option value="30">30 Miles Radius</option>
								</select>
								<span class="text-danger error_msg">{{ $errors->first('map_zoom') }}</span>
							</div>
						</div>
					</div>
					
					<div class="map-view clearfix">
						<div id="map"></div>
					</div>
					<div class="fare-table clearfix">
						<h3>Fare Estimation</h3>
						<div class="row">
							<div class="col-md-8">
								Minimum Fare :
							</div>
							<div class="col-md-4" ng-cloak>
								{{$currency_symbol}}@{{vehicle_detail_minimum_fare | number: 2}}
							</div>
						</div>
						<div class="row">
							<div class="col-md-8">
								Base Fare :
							</div>
							<div class="col-md-4" ng-cloak>
								{{$currency_symbol}}@{{vehicle_detail_base_fare | number: 2}}
							</div>
						</div>
						<div class="row">
							<div class="col-md-8">
								Distance ( <span ng-cloak> @{{vehicle_detail_km}} </span> Km) :
							</div>
							<div class="col-md-4" ng-cloak>
								{{$currency_symbol}}@{{vehicle_detail_km_fare | number: 2}}
							</div>
						</div>
						<div class="row">
							<div class="col-md-8">
								Time ( <span ng-cloak> @{{vehicle_detail_minutes}} </span> Minutes) :
							</div>
							<div class="col-md-4" ng-cloak>
								{{$currency_symbol}}@{{vehicle_detail_min_fare | number: 2}}
							</div>
						</div>
						<div class="row">
							<div class="col-md-8">
								Peak Price x <span  ng-cloak> @{{vehicle_detail_peak_price}} </span> :
							</div>
							<div class="col-md-4" ng-cloak>
								{{$currency_symbol}}@{{(vehicle_detail_peak_fare) | number: 2}}
							</div>
						</div>


						<div class="row">
							<div class="col-md-8">
								Total Fare :
							</div>
							<div class="col-md-4" ng-cloak>
								{{$currency_symbol}}@{{vehicle_detail_total_fare | number: 2}}
							</div>
						</div>
					</div>
					<div class="fare-btn clearfix">
						<button type="button" class="btn btn-primary submit_button change_field" disabled ng-disabled="myForm.$invalid && page_loading==0" ng-click="submitForm($event);">
							@if(!isset($schedule_ride->id))
								Book Now
							@else
								Submit
							@endif
						</button>
						@if(!isset($schedule_ride->id))
						<button class="btn btn-primary reset">Reset</button>
						@endif
					</div>
				</div>
			</div>
		</div>

		<div class="manual-notes">
			<h4>Notes:</h4>
			<ul>
				<li>
					Administrator can Add / Edit Ride later booking on this page.
				</li>
				<li>
					Driver current availability is not connected with booking being made.
				</li>
				<li>
					Adding booking from here will not send request to Driver immediately.
				</li>
			</ul>
			<h5>Manual Auto Booking :</h5>
			<ul>
				<li>
					In case of "Auto Assign Driver" option selected, Driver(s) get automatic request before 5 minutes of actual booking time.
				</li>
				<li>
					In case of "Auto Assign Driver" option not selected, Driver(s) get booking confirmation sms as well as reminder sms before 30 minutes of actual booking.
				</li>
				<li>
					Driver has to start the scheduled Trip by going to "Your Trip" >> Upcoming section from Driver App.
				</li>
				<li>
					In case of "Auto Assign Driver", the competitive algorithm will be followed.
				</li>
			</ul>
			<br>
			<h5>Manual Assign Booking :</h5>
			<ul>
				<li>
					Please confirm future avaialbility of Driver before doing booking.
				</li>
				<li>
					Driver has to start the scheduled Trip by going to "Your Trip" >> Upcoming section from Driver App.
				</li>
			</ul>
		</div>
	</section>
	{!! Form::close() !!}
</div>
@endsection
@push('scripts')
<script type="text/javascript">
	var REQUEST_URL = "{{url('/'.LOGIN_USER_TYPE)}}"; 
	var old_edit_date = "{{isset($schedule_ride->id)?@$schedule_ride->schedule_date.' '.@$schedule_ride->schedule_time:''}}"
	var page = "{{isset($schedule_ride->id)?'edit':'new'}}"
</script>
@endpush
