@extends('admin.template')
@section('main')
<div class="content-wrapper" ng-controller="destination_admin">
	<section class="content-header">
		<h1> Edit Rider </h1>
		<ol class="breadcrumb">
			<li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home </a></li>
			<li><a href="{{ url(LOGIN_USER_TYPE.'/rider') }}"> Riders </a></li>
			<li class="active"> Edit </li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-8 col-sm-offset-2">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Edit Rider Form</h3>
					</div>
					{!! Form::open(['url' => 'admin/edit_rider/'.$result->id, 'class' => 'form-horizontal']) !!}

					@if($referrer)

					{!! Form::hidden('referrer',$referrer, ['id' => 'referrer']) !!}

					@endif
					<div class="box-body">
						<span class="text-danger">(*)Fields are Mandatory</span>
						<div class="form-group">
							<label for="input_first_name" class="col-sm-3 control-label">First Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('first_name', $result->first_name, ['class' => 'form-control', 'id' => 'input_first_name', 'placeholder' => 'First Name']) !!}
								<span class="text-danger">{{ $errors->first('first_name') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_last_name" class="col-sm-3 control-label">Last Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('last_name', $result->last_name, ['class' => 'form-control', 'id' => 'input_last_name', 'placeholder' => 'Last Name']) !!}
								<span class="text-danger">{{ $errors->first('last_name') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_email" class="col-sm-3 control-label">Email<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('email', $result->email, ['class' => 'form-control', 'id' => 'input_email', 'placeholder' => 'Email']) !!}
								<span class="text-danger">{{ $errors->first('email') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_password" class="col-sm-3 control-label">Password</label>
							<div class="col-sm-6">
								{!! Form::text('password', '', ['class' => 'form-control', 'id' => 'input_password', 'placeholder' => 'Password']) !!}
								<span class="text-danger">{{ $errors->first('password') }}</span>
							</div>
						</div>
						{!! Form::hidden('user_type','Rider', ['class' => 'form-control', 'id' => 'user_type', 'placeholder' => 'Select']) !!}
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Country Code<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								<select class='form-control' id = 'input_country_code' name='country_code' >
									@foreach($country_code_option as $country_code)
									<option value="{{@$country_code->phone_code}}" {{ ($country_code->phone_code == $result->country_code) ? 'Selected' : ''}} >{{$country_code->long_name}}</option>
									@endforeach
								</select>
								<span class="text-danger">{{ $errors->first('country_code') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="mobile_number" class="col-sm-3 control-label">Mobile Number <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('mobile_number', old('mobile_number',$result->mobile_number), ['class' => 'form-control', 'id' => 'mobile_number', 'placeholder' => 'Mobile Number']) !!}
								<span class="text-danger">{{ $errors->first('mobile_number') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_password" class="col-sm-3 control-label">Home Location</label>
							<div class="col-sm-6">
								<div class="autocomplete-input-container">
									<div class="autocomplete-input">
										{!! Form::text('home_location', @$location->home, ['class' => 'form-control', 'id' => 'input_home_location', 'placeholder' => 'Home Location','autocomplete' => 'off']) !!}
									</div>
									<ul class="autocomplete-results home-autocomplete-results">
									</ul>
								</div>
								<span class="text-danger">{{ $errors->first('home_location') }}</span>
							</div>
						</div>
						{!! Form::hidden('home_latitude',@$location->home_latitude, ['class' => 'form-control', 'id' => 'home_latitude', 'placeholder' => 'Select']) !!}
						{!! Form::hidden('home_longitude',@$location->home_longitude, ['class' => 'form-control', 'id' => 'home_longitude', 'placeholder' => 'Select']) !!}
						<div class="form-group">
							<label for="input_password" class="col-sm-3 control-label">Work Location</label>
							<div class="col-sm-6">
								<div class="autocomplete-input-container">
									<div class="autocomplete-input">
										{!! Form::text('work_location', @$location->work, ['class' => 'form-control', 'id' => 'input_work_location', 'placeholder' => 'Work Location','autocomplete' => 'off']) !!}
									</div>
									<ul class="autocomplete-results work-autocomplete-results">
									</ul>
								</div>
								<span class="text-danger">{{ $errors->first('work_location') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_password" class="col-sm-3 control-label">Used Referral Code</label>
							<div class="col-sm-6">
								<div class="autocomplete-input-container">
									<div class="autocomplete-input">
										{!! Form::text('used_referral_code', @$result->used_referral_code, ['class' => 'form-control', 'id' => 'input_work_location', 'placeholder' => 'Used Referral Code','autocomplete' => 'off', 'value' => 'Test']) !!}
									</div>
									<ul class="autocomplete-results work-autocomplete-results">
									</ul>
								</div>
								<span class="text-danger">{{ $errors->first('work_location') }}</span>
							</div>
						</div>
						 <div class="form-group" style="margin-bottom: 1em">
                          	<label for="input-tags3" class="col-sm-3 control-label">Referrer<em class="text-danger"></em></label>
                          	<div class="col-sm-6">
	                    	<input type="text" id="input-tags3" name="referrer_id" value="" />
	                    
	                 		</div>	
	                  	</div>
						{!! Form::hidden('work_latitude',@$location->work_latitude, ['class' => 'form-control', 'id' => 'work_latitude', 'placeholder' => 'Select']) !!}
						{!! Form::hidden('work_longitude',@$location->work_longitude, ['class' => 'form-control', 'id' => 'work_longitude', 'placeholder' => 'Select']) !!}
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
						<button type="submit" class="btn btn-default pull-left" name="cancel" value="cancel">Cancel</button>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</section>
</div>
@endsection


@push('scripts')
<script src="{{ url('js/selectize.js') }}"></script>
<script>
	$(function() {
		$('#input-tags3').selectize({
		    plugins: ['remove_button'],
		    maxItems: 1
    		
		});
		init_user();
	})
	function init_user()
{
  var usertype= 'all';
    var select = $("#input-tags3").selectize();
    var selectize = select[0].selectize;
    selectize.disable();
    $.ajax({
      type: 'POST',
      url: APP_URL+'/{{LOGIN_USER_TYPE}}/get_send_users',
      data: "type="+usertype,
      dataType: "json",
      success: function(resultData) {
        var select = $("#input-tags3").selectize();
        var selectize = select[0].selectize;
        selectize.clear();
        selectize.clearOptions();
        $.each(resultData, function (key, value) {
          selectize.addOption({value:value.id,text:value.first_name + ' - ' +value.mobile_number});
        });
        selectize.enable();

        if(v = $("#referrer").val())
       	 selectize.setValue(v, false);

      }
    });
  }

</script>

@endpush