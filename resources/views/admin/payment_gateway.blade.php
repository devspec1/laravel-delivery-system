@extends('admin.template')
@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<h1> Payment Gateway </h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"> <i class="fa fa-dashboard"></i> Home </a>
			</li>
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/payment_gateway') }}"> Payment Gateway </a>
			</li>
			<li class="active"> Edit </li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-8 col-sm-offset-2">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title"> Payment Gateway Form </h3>
					</div>
					{!! Form::open(['url' => 'admin/payment_gateway', 'class' => 'form-horizontal']) !!}
					<div class="box-body">
						<span class="text-danger">(*)Fields are Mandatory</span>
						<!-- Paypal Section Start -->
						<div class="box-body">
							<div class="form-group" ng-init="paypal_enabled={{ old('paypal_enabled',payment_gateway('is_enabled','Paypal')) }}">
								<label for="input_paypal_enabled" class="col-sm-3 control-label">Is Paypal Enabled <em class="text-danger">*</em> </label>
								<div class="col-sm-6">
									{!! Form::select('paypal_enabled', array('0' => 'No', '1' => 'Yes'), '', ['class' => 'form-control', 'id' => 'input_paypal_enabled', 'ng-model' => 'paypal_enabled']) !!}
									<span class="text-danger">{{ $errors->first('paypal_enabled') }}</span>
								</div>
							</div>

							<div class="form-group">
								<label for="input_paypal_mode" class="col-sm-3 control-label">PayPal Mode <em ng-show="paypal_enabled == '1'" class="text-danger">*</em> </label>
								<div class="col-sm-6">
									{!! Form::select('paypal_mode', array('sandbox' => 'Sandbox', 'live' => 'Live'), old('paypal_mode',payment_gateway('mode','Paypal')), ['class' => 'form-control', 'id' => 'input_paypal_mode']) !!}
									<span class="text-danger">{{ $errors->first('paypal_mode') }}</span>
								</div>
							</div>

							<div class="form-group">
								<label for="input_paypal_id" class="col-sm-3 control-label">PayPal Id <em ng-show="paypal_enabled == '1'" class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('paypal_id', old('paypal_id',payment_gateway('paypal_id','Paypal')), ['class' => 'form-control', 'id' => 'input_paypal_id', 'placeholder' => 'PayPal Id']) !!}
									<span class="text-danger">{{ $errors->first('paypal_id') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_paypal_client" class="col-sm-3 control-label">PayPal Client ID <em ng-show="paypal_enabled == '1'" class="text-danger">*</em> </label>
								<div class="col-sm-6">
									{!! Form::text('paypal_client', old('paypal_client',payment_gateway('client','Paypal')), ['class' => 'form-control', 'id' => '', 'placeholder' => 'PayPal Client']) !!}
									<span class="text-danger">{{ $errors->first('paypal_client') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_paypal_secret" class="col-sm-3 control-label"> PayPal Secret <em ng-show="paypal_enabled == '1'" class="text-danger">*</em> </label>
								<div class="col-sm-6">
									{!! Form::text('paypal_secret', old('paypal_secret',payment_gateway('secret','Paypal')), ['class' => 'form-control', 'id' => '', 'placeholder' => 'PayPal Secret']) !!}
									<span class="text-danger">{{ $errors->first('paypal_secret') }}</span>
								</div>
							</div>
						</div>
						<!-- Paypal Section End -->
						<!-- Stripe Section Start -->
						<div class="box-body" ng-init="stripe_enabled={{ old('stripe_enabled',payment_gateway('is_enabled','Stripe')) }}">
							<div class="form-group">
								<label for="input_stripe_enabled" class="col-sm-3 control-label">Is Stripe Enabled <em class="text-danger">*</em> </label>
								<div class="col-sm-6">
									{!! Form::select('stripe_enabled', array('0' => 'No', '1' => 'Yes'), old('stripe_enabled',payment_gateway('is_enabled','Stripe')), ['class' => 'form-control', 'id' => 'input_stripe_enabled','ng-model' => 'stripe_enabled']) !!}
									<span class="text-danger">{{ $errors->first('stripe_enabled') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_stripe_publish_key" class="col-sm-3 control-label"> Stripe Key <em ng-show="stripe_enabled == '1'" class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('stripe_publish_key', old('stripe_publish_key',payment_gateway('publish','Stripe')), ['class' => 'form-control', 'id' => 'input_stripe_publish_key', 'placeholder' => 'Stripe Key']) !!}
									<span class="text-danger">{{ $errors->first('stripe_publish_key') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_stripe_secret_key" class="col-sm-3 control-label"> Stripe Secret <em ng-show="stripe_enabled == '1'" class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('stripe_secret_key', old('stripe_secret_key',payment_gateway('secret','Stripe')), ['class' => 'form-control', 'id' => 'input_stripe_secret_key', 'placeholder' => 'Stripe Secret']) !!}
									<span class="text-danger">{{ $errors->first('stripe_secret_key') }}</span>
								</div>
							</div>
							<div class="form-group">
								<label for="input_stripe_api_version" class="col-sm-3 control-label"> Stripe API Version <em ng-show="stripe_enabled == '1'" class="text-danger">*</em></label>
								<div class="col-sm-6">
									{!! Form::text('stripe_api_version', old('stripe_api_version',payment_gateway('api_version','Stripe')), ['class' => 'form-control', 'id' => 'input_stripe_api_version', 'placeholder' => 'Stripe API Version']) !!}
									<span class="text-danger">{{ $errors->first('stripe_api_version') }}</span>
								</div>
							</div>
						</div>
					</div>
					<!-- Stripe Section End -->
					
					<!-- Braintree Section Start -->
					<div class="box-body" ng-init="bt_enabled={{ old('bt_enabled',payment_gateway('is_enabled','Braintree')) }}">
						<div class="form-group">
								<label for="input_bt_enabled" class="col-sm-3 control-label">Is Braintree Enabled <em class="text-danger">*</em> </label>
								<div class="col-sm-6">
									{!! Form::select('bt_enabled', array('0' => 'No', '1' => 'Yes'), old('bt_enabled',payment_gateway('is_enabled','Braintree')), ['class' => 'form-control', 'id' => 'input_bt_enabled','ng-model' => 'bt_enabled']) !!}
									<span class="text-danger">{{ $errors->first('bt_enabled') }}</span>
								</div>
							</div>
						<div class="form-group">
							<label for="input_mode" class="col-sm-3 control-label"> Payment Mode <em ng-show="bt_enabled == '1'" class="text-danger">*</em> </label>
							<div class="col-sm-6">
								{!! Form::select('bt_mode', array('sandbox' => 'Sandbox', 'production' => 'Production'), old('bt_mode'
								,payment_gateway('mode','Braintree')), ['class' => 'form-control', 'id' => 'input_mode']) !!}
								<span class="text-danger">{{ $errors->first('mode') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_merchant_id" class="col-sm-3 control-label"> Braintree Merchant ID <em ng-show="bt_enabled == '1'" class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('bt_merchant_id', old('bt_merchant_id',payment_gateway('merchant_id','Braintree')), ['class' => 'form-control', 'id' => 'input_merchant_id', 'placeholder' => 'Merchant ID']) !!}
								<span class="text-danger">{{ $errors->first('bt_merchant_id') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_bt_public" class="col-sm-3 control-label"> Braintree Public Key <em ng-show="bt_enabled == '1'" class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('bt_public_key', old('bt_public_key',payment_gateway('public_key','Braintree')), ['class' => 'form-control', 'id' => 'input_bt_public', 'placeholder' => 'Public Key']) !!}
								<span class="text-danger">{{ $errors->first('bt_public_key') }}</span>
							</div>
						</div>
						<div class="form-group">
							<label for="input_bt_private_key" class="col-sm-3 control-label"> Braintree Private Key <em ng-show="bt_enabled == '1'" class="text-danger">*</em></label>
							<div class="col-sm-6">
								{!! Form::text('bt_private_key', old('bt_private_key',payment_gateway('private_key','Braintree')), ['class' => 'form-control', 'id' => 'input_bt_private_key', 'placeholder' => 'Private Key']) !!}
								<span class="text-danger">{{ $errors->first('bt_private_key') }}</span>
							</div>
						</div>
					</div>
					<!-- Braintree Section End -->

					<!-- Payout Methods Section Start -->
						<div class="box-body">
							<div class="form-group">
								<label for="input_payout_methods" class="col-sm-3 control-label"> Payout Methods <em class="text-danger">*</em> </label>
								<div class="col-sm-6">
									@foreach(PAYOUT_METHODS as $payout_method)
									<div ng-init="payout_method_{{ $payout_method['key'] }}={{ isPayoutEnabled($payout_method['key']) }}">
										<input type="checkbox" name="payout_methods[]" id="payout_method-{{ $payout_method['key'] }}" value="{{ $payout_method['key'] }}" ng-checked="{{ isPayoutEnabled($payout_method['key']) }}"> <label for="payout_method-{{ $payout_method['key'] }}" ng-model="payout_method_{{ $payout_method['key'] }}"> {{ $payout_method["value"] }} </label>
									</div>										
									@endforeach
								</div>
							</div>
					</div>
					<!-- Payout Methods Section End -->
					
					<div class="box-footer">
						<button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
						<button type="reset" class="btn btn-default pull-left"> Cancel </button>
					</div>
				</div>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</section>
</div>
@endsection