<div class="modal otp-popup text-left poppayout fade" id="otp_popup" aria-hidden="false" style="" tabindex="-1">
	<div id="modal-add-otp-set-address" class="modal-content">
		<div class="panel-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h3>
				{{ trans('messages.signup.otp') }} @{{resend_otp}}
			</h3>
		</div>
		<div class="flash-container otp-flash-message alert-success success_msg" id="otp_resended_flash" style="display: none;">
			{{trans('messages.signup.otp_resended')}}
		</div>
		<div class="panel-body">
			<div class="otp-number row">
				<div class="col-xs-4">
					<div class="layout__item country-input" id="country">
						<div id="select-title-stage" class="country-code">{{old('country_code')!=null? '+'.old('country_code') : '+1'}}</div>
						<div class="select">
							<!-- <label for="mobile-country"><div class="flag US"></div></label> -->
							<select name="country_code" tabindex="-1" id="mobile_country" class="square borderless--right">
								@foreach($country as $key => $value)
								<option value="{{$value->phone_code}}" {{ ($value->phone_code == (old('country_code')!=null? old('country_code') : '1')) ? 'selected' : ''  }} data-value="+{{ $value->phone_code}}">{{ $value->long_name}}
								</option>
								@endforeach
							</select> 
							<span class="text-danger country_code_error">{{ $errors->first('country_code') }}</span>               
						</div>
					</div>
				</div>
				<div class="col-xs-8">
					{!! Form::number('mobile', '', ['id' => 'mobile_input','class'=>'mobile-input form-control','placeholder' => trans('messages.profile.mobile')]) !!}
					<span class="text-danger mobile_number_error"></span>
				</div>
			</div>

			<div class="otp-field">
				<div class="otp-input">
					{!! Form::number('otp', '', ['id' => 'otp_input','class'=>'form-control','placeholder' => trans('messages.signup.otp')]) !!}
					<span class="text-danger otp_error"></span>
				</div>
			</div>
		</div>
		<div class="panel-footer otp_footer">
			<input type="button" value="{{ trans('messages.signup.send_otp') }}" class="btn blue-signin-btn" ng-click="changeNumberPopup('send_otp');">
			<!-- <input type="button" ng-show="resend_otp" value="{{ trans('messages.signup.resend_otp') }}" class="btn blue-signin-btn" ng-click="changeNumberPopup('resend_otp');"> -->
			<input type="button" value="{{ trans('messages.user.submit') }}" class="btn blue-signin-btn" ng-click="changeNumberPopup('check_otp');">
		</div>
	</div>
</div>