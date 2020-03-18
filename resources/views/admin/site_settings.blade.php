@extends('admin.template')
@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<h1> Site Settings </h1>
		<ol class="breadcrumb">
			<li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
			<li><a href="#">Site Settings</a></li>
			<li class="active">Edit</li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-8 col-sm-offset-2">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Site Settings Form</h3>
					</div>
					{!! Form::open(['url' => 'admin/site_setting', 'class' => 'form-horizontal', 'files' => true]) !!}
					<div class="box-body">
						<span class="text-danger">(*)Fields are Mandatory</span>
						<div class="form-group">
							<label for="input_site_name" class="col-sm-3 control-label">Site Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('site_name', site_settings('site_name'), ['class' => 'form-control', 'id' => 'input_site_name', 'placeholder' => 'Site Name']) !!}
								<span class="text-danger">{{ $errors->first('site_name') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_site_name" class="col-sm-3 control-label">Version</label>
							<div class="col-sm-6">
								{!! Form::text('version', site_settings('version'), ['class' => 'form-control', 'id' => 'input_version', 'placeholder' => 'Version']) !!}
								<span class="text-danger">{{ $errors->first('version') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_logo" class="col-sm-3 control-label">Header Logo</label>
							<em>Size: 140x80</em>
							<div class="col-sm-6">
								{!! Form::file('logo', ['class' => 'form-control', 'id' => 'input_logo', 'accept' => 'image/*']) !!}
								<span class="text-danger">{{ $errors->first('logo') }}</span>
								<img src="{{ $logo_url }}" class="image-cls">
							</div>
						</div>
						<div class="form-group">
							<label for="input_logo" class="col-sm-3 control-label">Header white logo</label>
							<em>Size: 140x80</em>
							<div class="col-sm-6">
								{!! Form::file('page_logo', ['class' => 'form-control', 'id' => 'input_page_logo', 'accept' => 'image/*']) !!}
								<span class="text-danger">{{ $errors->first('page_logo') }}</span>
								<img src="{{ url(PAGE_LOGO_URL).'?v='.rand() }}" class="image-cls">
							</div>
						</div>
						<div class="form-group">
							<label for="input_favicon" class="col-sm-3 control-label">Favicon</label>
							<em>Size: 16x16</em>
							<div class="col-sm-6">
								{!! Form::file('favicon', ['class' => 'form-control', 'id' => 'input_favicon', 'accept' => 'image/*']) !!}
								<span class="text-danger">{{ $errors->first('favicon') }}</span>
								<img src="{{ $favicon.'?v='.rand() }}" class="fav_class">
							</div>
						</div>
						<div class="form-group">
							<label for="input_default_currency" class="col-sm-3 control-label">Default Currency</label>
							<div class="col-sm-6">
								{!! Form::select('default_currency', $currency, $default_currency, ['class' => 'form-control', 'id' => 'input_default_currency']) !!}
								<span class="text-danger">{{ $errors->first('default_currency') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_payment_currency" class="col-sm-3 control-label">Payment Currency</label>
							<div class="col-sm-6">
								{!! Form::select('payment_currency', $currency, site_settings('payment_currency'), ['class' => 'form-control', 'id' => 'input_payment_currency']) !!}
								<span class="text-danger">{{ $errors->first('payment_currency') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_trip_default_paymode" class="col-sm-3 control-label">Trip Default Payment Method</label>
							<div class="col-sm-6" ng-init="trip_default = '{{payment_gateway('trip_default','Common')}}'">
								<select name="trip_default_paymode" ng-model="trip_default" class="form-control" id="input_trip_default_paymode">
									@foreach($payment_methods as $pay_method)
										<option value="{{ $pay_method['key'] }}"> {{ $pay_method['value'] }} </option>
									@endforeach
								</select>
								<span class="text-danger">{{ $errors->first('trip_default_paymode') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_site_name" class="col-sm-3 control-label">Driver Kilo Meter</label>
							<div class="col-sm-6">
								{!! Form::text('driver_km', site_settings('driver_km'), ['class' => 'form-control', 'id' => 'input_head_code', 'placeholder' => 'Driver kilo meter']) !!}
								<span class="text-danger">{{ $errors->first('driver_km') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_site_name" class="col-sm-3 control-label">Add code to the < head >(for tracking codes such as google analytics)</label>
							<div class="col-sm-6">
								{!! Form::textarea('head_code', site_settings('head_code'), ['class' => 'form-control', 'id' => 'input_head_code', 'placeholder' => 'Head Code']) !!}
								<span class="text-danger">{{ $errors->first('head_code') }}</span>
							</div>
						</div>
						
						<div class="form-group">
							<label for="input_default_language" class="col-sm-3 control-label">Default Language</label>
							<div class="col-sm-6">
								{!! Form::select('default_language', $language, $default_language[0]->value, ['class' => 'form-control', 'id' => 'input_default_language']) !!}
								<span class="text-danger">{{ $errors->first('default_language') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_admin_country_code" class="col-sm-3 control-label">
								Country Code  <em class="text-danger">*</em>
							</label>
							<div class="col-sm-6">
								<select class='form-control' id = 'input_admin_country_code' name='admin_country_code' >
									<option value="" disabled> Select </option>
									@foreach($countries as $country_code)
									<option value="{{ $country_code->phone_code }}" {{ ($country_code->phone_code == old('admin_country_code',site_settings('admin_country_code'))) ? 'Selected' : ''}} >{{$country_code->long_name}}</option>
									@endforeach
								</select>
								<span class="text-danger">{{ $errors->first('admin_country_code') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_mobile_number" class="col-sm-3 control-label">Manual Booking Contact Number <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('admin_contact', old('admin_contact',site_settings('admin_contact')), ['class' => 'form-control', 'id' => 'input_head_code', 'placeholder' => 'Contact Number']) !!}
								<span class="text-danger">{{ $errors->first('admin_contact') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_heat_map" class="col-sm-3 control-label">Heat Map <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::select('heat_map',['On'=>'On','Off'=>'Off'], old('heat_map',site_settings('heat_map')), ['class' => 'form-control', 'id' => 'input_heat_map']) !!}
								<span class="text-danger">{{ $errors->first('heat_map') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_heat_map_hours" class="col-sm-3 control-label">Heat Map Hours <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('heat_map_hours', old('heat_map_hours',site_settings('heat_map_hours')), ['class' => 'form-control', 'id' => 'input_heat_map_hours', 'placeholder' => 'Heat Map Hours']) !!}
								<span class="text-danger">{{ $errors->first('heat_map_hours') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_update_loc_interval" class="col-sm-3 control-label"> Update Location Interval <i id="interval-tooltip" data-toggle="tooltip" data-html="true"  class="glyphicon  glyphicon-info-sign" title="Interval in Seconds to update driver's current location"></i> <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('update_loc_interval', old('update_loc_interval',site_settings('update_loc_interval')), ['class' => 'form-control', 'id' => 'input_update_loc_interval', 'placeholder' => 'Update Location Interval']) !!}
								<span class="text-danger">{{ $errors->first('update_loc_interval') }}</span>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
						<button type="reset" class="btn btn-default pull-left" name="cancel" value="cancel">Cancel</button>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
@push('scripts')
<style type="text/css">
  .image-cls{
    width: 140px;
    height: 80px;
  }
  .fav_class{
    height: 16px;
    width: 16px;
  }
</style>
@endpush