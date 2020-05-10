@extends('admin.template')
@section('main')
<div class="content-wrapper" ng-controller="driver_management">
	<section class="content-header">
		<h1>
		Edit Driver
		</h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"> <i class="fa fa-dashboard"></i> Home </a>
			</li>
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/driver') }}"> Drivers </a>
			</li>
			<li class="active">Edit</li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-8 col-sm-offset-2 ne_ed">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Edit Driver Form</h3>
					</div>

					{!! Form::open(['url' => LOGIN_USER_TYPE.'/edit_driver/'.$result->id, 'class' => 'form-horizontal','files' => true]) !!}

					@if($referrer)

					{!! Form::hidden('referrer', $referrer, ['id' => 'referrer']) !!}
					
					@endif
					<div class="box-body ed_bld">
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
                            <label for="referral_code" class="col-sm-3 control-label">Referral Code</label>
                            <div class="col-sm-6">
                              {!! Form::text('referral_code', $result->referral_code, ['class' => 'form-control', 'id' => 'input_referral_code', 'placeholder' => 'Referral Code']) !!}
                              <span class="text-danger">{{ $errors->first('referral_code') }}</span>
                            </div>
                          </div>
                           {{-- <div class="form-group">
                            <label for="used_referral_code" class="col-sm-3 control-label">Used Referral Code<em class="text-danger">*</em></label>
                            <div class="col-sm-6">
                              {!! Form::text('used_referral_code', $result->used_referral_code, ['class' => 'form-control', 'id' => 'input_used_referral_code', 'placeholder' => 'Used referral code']) !!}
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
							<label for="input_status" class="col-sm-3 control-label">Plan Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								<select class ='form-control' id = 'input_plan' name='plan_id'>
									<option value="" disabled=""> Select </option>
									@foreach($all_plans as $plan)
									<option value="{{@$plan->id}}" @if(isset($current_plan)) {{ ($plan->id == $current_plan->id) ? 'Selected' : ''}} @endif>{{$plan->plan_name}}</option>
									@endforeach
								</select>
								<span class="text-danger">{{ $errors->first('plan_id') }}</span>
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
						{!! Form::hidden('user_type','Driver', ['class' => 'form-control', 'id' => 'user_type', 'placeholder' => 'Select']) !!}
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
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Mobile Number </label>
							<div class="col-sm-6">
								{!! Form::text('mobile_number',$result->env_mobile_number, ['class' => 'form-control', 'id' => 'mobile_number', 'placeholder' => 'Mobile Number']) !!}
								<span class="text-danger">{{ $errors->first('mobile_number') }}</span>
							</div>
						</div>
						@if (LOGIN_USER_TYPE!='company')
						<div class="form-group">
							<label for="input_company" class="col-sm-3 control-label">Company Name<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::select('company_name_view', $company, $result->company_id, ['class' => 'form-control', 'id' => 'input_company_name', 'placeholder' => 'Select','disabled']) !!}
								{!! Form::hidden('company_name', $result->company_id) !!}
								<span class="text-danger">{{ $errors->first('company_name') }}</span>
							</div>
						</div>
						@endif
						<div class="form-group">
							<label for="input_status" class="col-sm-3 control-label">Status<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive', 'Pending' => 'Pending', 'Car_details' => 'Car_details', 'Document_details' => 'Document_details'), $result->status, ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
								<span class="text-danger">{{ $errors->first('status') }}</span>
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
							<label for="input_license_back" class="col-sm-3 control-label">Driver's License - ( Back/Reverse)  <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::file('license_back', ['class' => 'form-control', 'id' => 'input_license_back', 'accept' => "image/*"]) !!}
								<span class="text-danger">{{ $errors->first('license_back') }}</span>
								<div class="license-img">
									@if(@$driver_documents->license_back)
									<a href="{{@$driver_documents->license_back }}" target="_blank">
										<img style="width: 200px;height: 100px" src="{{@$driver_documents->license_back }}">
									</a>
									@else
									<img style="width: 100px;height: 100px; padding-top: 5px;" src="{{ url('images/driver_doc.png')}}">
									@endif
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="input_license_front" class="col-sm-3 control-label">Driver's License - (Front) <em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::file('license_front', ['class' => 'form-control', 'id' => 'input_license_front', 'accept' => "image/*"]) !!}
								<span class="text-danger">{{ $errors->first('license_front') }}</span>
								<div class="license-img">
									@if(@$driver_documents->license_front)
									<a href="{{@$driver_documents->license_front }}" target="_blank"><img style="width: 200px;height: 100px" src="{{@$driver_documents->license_front }}"> </a>
									@else
									<img style="width: 100px;height: 100px; padding-top: 5px;" src="{{ url('images/driver_doc.png')}}">
									@endif
								</div>
							</div>
                        </div>
                        <div class="form-group">
							<label for="input_profile_image" class="col-sm-3 control-label">Driver's profile photo<em class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::file('profile_image', ['class' => 'form-control', 'id' => 'input_profile_image', 'accept' => "image/*"]) !!}
								<span class="text-danger">{{ $errors->first('license_front') }}</span>
								<div class="license-img">
                                    @if(@$profile_image->src)
									<a href="{{@$profile_image->src }}" target="_blank"><img style="width: 200px;height: 100px" src="{{@$profile_image->src }}"> </a>
									@else
									<img style="width: 100px;height: 100px; padding-top: 5px;" src="{{ url('images/user.jpeg')}}">
									@endif
								</div>
							</div>
						</div>
						@if(LOGIN_USER_TYPE!='company' || Auth::guard('company')->user()->id != 1)
						<span class="bank_detail">
							<div class="form-group">
								<label for="input_status" class="col-sm-3 control-label">Account Holder Name <em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('account_holder_name',@$result->default_payout_credentials->payout_preference->holder_name, ['class' => 'form-control', 'id' => 'account_holder_name', 'placeholder' => 'Account Holder Name']) !!}
									<span class="text-danger">{{ $errors->first('account_holder_name') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_status" class="col-sm-3 control-label">Account Number <em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('account_number',@$result->default_payout_credentials->payout_preference->account_number, ['class' => 'form-control', 'id' => 'account_number', 'placeholder' => 'Account Number']) !!}
									<span class="text-danger">{{ $errors->first('account_number') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_status" class="col-sm-3 control-label">Name of Bank <em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('bank_name',@$result->default_payout_credentials->payout_preference->bank_name, ['class' => 'form-control', 'id' => 'bank_name', 'placeholder' => 'Name of Bank']) !!}
									<span class="text-danger">{{ $errors->first('bank_name') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_status" class="col-sm-3 control-label">Bank Location <em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('bank_location',@$result->default_payout_credentials->payout_preference->bank_location, ['class' => 'form-control', 'id' => 'bank_location', 'placeholder' => 'Bank Location']) !!}
									<span class="text-danger">{{ $errors->first('bank_location') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_status" class="col-sm-3 control-label">BIC/SWIFT Code <em class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('bank_code',@$result->default_payout_credentials->payout_preference->branch_code, ['class' => 'form-control', 'id' => 'bank_code', 'placeholder' => 'BIC/SWIFT Code']) !!}
									<span class="text-danger">{{ $errors->first('bank_code') }}</span>
								</div>
							</div>
						</span>
						@endif
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

