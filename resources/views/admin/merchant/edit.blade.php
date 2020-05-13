@extends('admin.template')
@section('main')
<div class="content-wrapper" ng-controller="driver_management">
	<section class="content-header">
		<h1> Add Driver </h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"> <i class="fa fa-dashboard"></i> Home </a>
			</li>
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/merchants') }}"> Merchants </a>
			</li>
			<li class="active"> Edit </li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-8 col-sm-offset-2 ne_ed">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Edit Merchant Form</h3>
					</div>
					{!! Form::open(['url' => LOGIN_USER_TYPE.'/edit_merchant/'.$result->id, 'class' => 'form-horizontal','files' => true]) !!}
					@if($referrer)

					{!! Form::hidden('referrer', $referrer, ['id' => 'referrer']) !!}
					
					@endif
					<div class="box-body ed_bld">
						<span class="text-danger">(*)Fields are Mandatory</span>
						<div class="form-group">
							<label for="input_name" class="col-sm-3 control-label">Merchant Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('name', $result->name, ['class' => 'form-control', 'id' => 'input_name', 'placeholder' => 'Name']) !!}
								<span class="text-danger">{{ $errors->first('name') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_description" class="col-sm-3 control-label">Description<em class="text-danger">*</em></label>
							<div class="col-sm-6">
                                {!! Form::textarea('description', $result->description, ['class'=>'form-control', 'id' => 'input_description', 'rows' => 2, 'cols' => 40]) !!}
								<span class="text-danger">{{ $errors->first('description') }}</span>
							</div>
                        </div>
						<div class="form-group">
							<label for="input_cuisine_type" class="col-sm-3 control-label">Type of Cuisine<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('cuisine_type', $result->cuisine_type, ['class' => 'form-control', 'id' => 'input_cuisine_type', 'placeholder' => 'Type of Cuisine']) !!}
								<span class="text-danger">{{ $errors->first('cuisine_type') }}</span>
							</div>
						</div>
                        <div class="form-group" style="margin-bottom: 1em">
                            <label for="input_integration_type" class="col-sm-3 control-label">Integration Type</label>
                            <div class="col-sm-6">
                                {!! Form::select('integration_type',  $integrations,  $result->integration_type, ['class'=>'form-control', 'id' => 'input_integration_type']) !!}
                                <span class="text-danger">{{ $errors->first('integration_type') }}</span>
                           </div>	
                        </div>
						<div class="form-group">
							<label for="input_first_name" class="col-sm-3 control-label">First Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('first_name', $result_info->first_name, ['class' => 'form-control', 'id' => 'input_first_name', 'placeholder' => 'First Name']) !!}
								<span class="text-danger">{{ $errors->first('first_name') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_last_name" class="col-sm-3 control-label">Last Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('last_name', $result_info->last_name, ['class' => 'form-control', 'id' => 'input_last_name', 'placeholder' => 'Last Name']) !!}
								<span class="text-danger">{{ $errors->first('last_name') }}</span>
							</div>
                        </div>
                        <div class="form-group">
                            <label for="referral_code" class="col-sm-3 control-label">Member Number</label>
                            <div class="col-sm-6">
                              {!! Form::text('referral_code', $result_info->referral_code, ['class' => 'form-control', 'id' => 'input_referral_code', 'placeholder' => 'Referral Code']) !!}
                              <span class="text-danger">{{ $errors->first('referral_code') }}</span>
                            </div>
                          </div>
						{{-- <div class="form-group">
							<label for="used_referral_code" class="col-sm-3 control-label">Invitation Code<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('used_referral_code', $result_info->used_referral_code, ['class' => 'form-control', 'id' => 'input_used_referral_code', 'placeholder' => 'Used referral code']) !!}
								<span class="text-danger">{{ $errors->first('used_referral_code') }}</span>
							</div>
						</div> --}}
						<div class="form-group" style="margin-bottom: 1em">
							<label for="input-tags3" class="col-sm-3 control-label">Used Referral Code</label>
							<div class="col-sm-6">
								<input type="text" id="input-tags3" name="referrer_id" value="" />						
							</div>	
						</div>
						<div class="form-group">
							<label for="input_email" class="col-sm-3 control-label">Email<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('email', $result_info->email, ['class' => 'form-control', 'id' => 'input_email', 'placeholder' => 'Email']) !!}
								<span class="text-danger">{{ $errors->first('email') }}</span>
							</div>
						</div>
						{!! Form::hidden('user_type','Merchant', ['class' => 'form-control', 'id' => 'user_type', 'placeholder' => 'Select']) !!}						
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Mobile Number </label>
							<div class="col-sm-6">
								{!! Form::text('mobile_number',$result_info->mobile_number, ['class' => 'form-control', 'id' => 'mobile_number', 'placeholder' => 'Mobile Number']) !!}
								<span class="text-danger">{{ $errors->first('mobile_number') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Address Line 1 </label>
							<div class="col-sm-6">
								{!! Form::text('address_line1',@$address->address_line1, ['class' => 'form-control', 'id' => 'address_line1', 'placeholder' => 'Address Line 1']) !!}
								<span class="text-danger">{{ $errors->first('address_line1') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Address Line 2 </label>
							<div class="col-sm-6">
								{!! Form::text('address_line2',@$address->address_line2, ['class' => 'form-control', 'id' => 'address_line2', 'placeholder' => 'Address Line 2']) !!}
								<span class="text-danger">{{ $errors->first('address_line2') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">City </label>
							<div class="col-sm-6">
								
								{!! Form::text('city',@$address->city, ['class' => 'form-control', 'id' => 'city', 'placeholder' => 'City']) !!}
								<span class="text-danger">{{ $errors->first('city') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">State</label>
							<div class="col-sm-6">
								
								{!! Form::text('state',$address->state, ['class' => 'form-control', 'id' => 'state', 'placeholder' => 'State']) !!}
								<span class="text-danger">{{ $errors->first('state') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Postal Code </label>
							<div class="col-sm-6">
								{!! Form::text('postal_code',@$address->postal_code, ['class' => 'form-control', 'id' => 'postal_code', 'placeholder' => 'Postal Code']) !!}
								<span class="text-danger">{{ $errors->first('postal_code') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Country Code<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								<select class ='form-control' id = 'input_status' name='country_code'>
									<option value="" disabled=""> Select </option>
									@foreach($country_code_option as $country_code)
									<option value="{{@$country_code->phone_code}}" {{ ($country_code->phone_code == $result->country_code) ? 'Selected' : ''}}>{{$country_code->long_name}}</option>
									@endforeach
								</select>
								<span class="text-danger">{{ $errors->first('country_code') }}</span>
							</div>
						</div>
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
		  if (value.user_type.toLowerCase() == 'driver')
          	selectize.addOption({value:value.id,text:value.first_name + ' ' +  value.last_name+' - ' + value.user_type + ' - ' + value.mobile_number + ' - ' + value.referral_code});
        });
        selectize.enable();
		
        if(v = $("#referrer").val())
            selectize.setValue(v, false);

      }
    });
  }

</script>

@endpush

