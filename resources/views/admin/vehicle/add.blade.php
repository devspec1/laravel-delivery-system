@extends('admin.template')
@section('main')
<div class="content-wrapper" ng-controller="vehicle_management">
	<section class="content-header" ng-init='vehicle_id=0'>
		<h1>
		Add Vehicles
		</h1>
		<ol class="breadcrumb">
			<li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="{{ url(LOGIN_USER_TYPE.'/vehicle') }}">Vehicles</a></li>
			<li class="active">Add</li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-8 col-sm-offset-2 ne_ed">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Add Vehicles Form</h3>
					</div>
					{!! Form::open(['url' => LOGIN_USER_TYPE.'/add_vehicle', 'class' => 'form-horizontal','files' => true,'id'=>'vehicle_form']) !!}
					<div class="box-body ed_bld">
						<span class="text-danger">(*)Fields are Mandatory</span>
						@if (LOGIN_USER_TYPE!='company')
							<div class="form-group">
								<label for="input_company" class="col-sm-3 control-label">Company Name<em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::select('company_name', $company, '', ['class' => 'form-control', 'id' => 'input_company_name', 'placeholder' => 'Select','ng-model' => 'company_name','ng-change' => 'get_driver()']) !!}
									<span class="text-danger">{{ $errors->first('company_name') }}</span>
								</div>
							</div>
						@else
							<span ng-init='company_name="{{Auth::guard("company")->user()->id}}";get_driver()'></span>
						@endif
						<div class="form-group">
							<label for="input_company" class="col-sm-3 control-label">Driver Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								<span class="loading" style="display: none;padding-left: 50%"><img src="{{ url('images/loader.gif') }}" style="width: 25px;height: 25px; "><br></span>
								<select class='form-control' ng-cloak name="driver_name" id="input_driver_name">
									<option value="">Select</option>
									<option ng-repeat="driver in drivers" value="@{{driver.id}}">@{{driver.first_name}} @{{driver.last_name}} - @{{driver.id}} </option>
								</select>
								<span class="text-danger" id="driver-error">{{ $errors->first('driver_name') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Status<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), '', ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
								<span class="text-danger">{{ $errors->first('status') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="vehicle_id" class="col-sm-3 control-label">Vehicle Type <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::select('vehicle_id', $car_type,'', ['class' => 'form-control', 'id' => 'vehicle_id', 'placeholder' => 'Select']) !!}
								<span class="text-danger">{{ $errors->first('vehicle_id') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="vehicle_name" class="col-sm-3 control-label">Vehicle Name <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('vehicle_name','', ['class' => 'form-control', 'id' => 'vehicle_name', 'placeholder' => 'Vehicle Name']) !!}
								<span class="text-danger">{{ $errors->first('vehicle_name') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="vehicle_number" class="col-sm-3 control-label">Vehicle Number <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								
								{!! Form::text('vehicle_number','', ['class' => 'form-control', 'id' => 'vehicle_number', 'placeholder' => 'Vehicle Number']) !!}
								<span class="text-danger">{{ $errors->first('vehicle_number') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_insurance" class="col-sm-3 control-label">Motor insurance Certificate  <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::file('insurance', ['class' => 'form-control', 'id' => 'input_insurance', 'accept' => 'image/*']) !!}
								<span class="text-danger">{{ $errors->first('insurance') }}</span>
								
							</div>
						</div>
						<div class="form-group">
							<label for="input_license_back" class="col-sm-3 control-label">Certificate of Registration   <em class="text-danger">*</em></label>
							
							<div class="col-sm-6">
								{!! Form::file('rc', ['class' => 'form-control', 'id' => 'rc', 'accept' => 'image/*']) !!}
								<span class="text-danger">{{ $errors->first('rc') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="permit" class="col-sm-3 control-label">{{trans('messages.driver_dashboard.carriage_permit')}} <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::file('permit', ['class' => 'form-control', 'id' => 'permit', 'accept' => 'image/*']) !!}
								<span class="text-danger">{{ $errors->first('permit') }}</span>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-info pull-right" name="submit" value="submit"> Submit </button>
						<a href="{{url(LOGIN_USER_TYPE.'/vehicle')}}"><span class="btn btn-default pull-left">Cancel</span></a>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</section>
</div>
@endsection