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
<div class="manual-booking content-wrapper" ng-controller='delivery_order'>
	<section class="content-header">
		<h1>
			Add delivery order
		</h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}">
					<i class="fa fa-dashboard"></i> Home
				</a>
			</li>
			<li class="active">Add delivery order</li>
		</ol>
	</section>
	{!! Form::open(['method'=>'POST','url' => LOGIN_USER_TYPE.'/add_home_delivery', 'class' => 'form-horizontal delivery_adding','id'=>'delivery_order','name'=>'deliveryAddForm']) !!}

    <section class="content">
        <h4>Add delivery order</h4>
        <h5>Customer data</h5>
		<div class="row">
			<div class="col-md-3" ng-init="country_code={{($country_code_option[12]->phone_code)}}">
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
                    {!! Form::hidden('customer_phone_number', '', ['id' => 'customer_phone_number']) !!}
					{!! Form::text('mobile_number', '', ['class' => 'form-control', 'id' => 'input_mobile_number', 'placeholder' => 'Phone No', 'autocomplete' => 'off']) !!}
					<span class="text-danger error_msg">{{ $errors->first('mobile_number') }}</span>
				</div>
			</div>
			<div class="col-md-2 form-group m-0">
                {!! Form::hidden('customer_name', '', ['id' => 'customer_name']) !!}
				{!! Form::text('first_name', '', ['class' => 'form-control', 'id' => 'input_first_name', 'placeholder' => 'First Name', 'autocomplete' => 'off']) !!}
				<span class="text-danger error_msg">{{ $errors->first('first_name') }}</span>
			</div>
			<div class="col-md-2 form-group m-0">
				{!! Form::text('last_name', '', ['class' => 'form-control', 'id' => 'input_last_name', 'placeholder' => 'Last Name', 'autocomplete' => 'off']) !!}
				<span class="text-danger error_msg">{{ $errors->first('last_name') }}</span>
			</div>
        </div>
        <h5>Delivery data</h5>
		<div class="clearfix">
			<div class="col-md-4 location-form">
                <div class="row pick-location clearfix">
					<div class="col-md-12">
                        <input type="text" id="input-merchant-id" name="merchant_id" placeholder="Merchant" value="" />
						<span class="text-danger error_msg error_merchant_id">{{ $errors->first('merchant_id') }}</span>
					</div>
                </div>
				<div class="row pick-location clearfix">
					<div class="col-md-12" ng-init='pick_up_latitude = ""'>
						{!! Form::hidden('pick_up_latitude', '', ['id' => 'pick_up_latitude']) !!}
						{!! Form::hidden('pick_up_longitude', '', ['id' => 'pick_up_longitude']) !!}
						{!! Form::text('pick_up_location', '', ['class' => 'form-control change_field', 'id' => 'input_pick_up_location', 'placeholder' => 'Pick Up Location', 'autocomplete' => 'off']) !!}
						<span class="text-danger error_msg error_pick_up_location">{{ $errors->first('pick_up_location') }}</span>
					</div>
				</div>
				<div class="row pick-location clearfix">
					<div class="col-md-12">
						{!! Form::hidden('drop_off_latitude', '', ['id' => 'drop_off_latitude']) !!}
						{!! Form::hidden('drop_off_longitude', '', ['id' => 'drop_off_longitude']) !!}
						{!! Form::text('drop_off_location', '', ['class' => 'form-control change_field', 'id' => 'input_drop_off_location', 'placeholder' => 'Drop Off Location', 'autocomplete' => 'off']) !!}
						<span class="text-danger error_msg error_drop_off_location">{{ $errors->first('drop_off_location') }}</span>
					</div>
				</div>
				<div class="row clearfix">
					<div class="col-md-12" ng-init='date_time = ""'>
						{!! Form::text('estimate_time','', ['class' => 'form-control change_field', 'id' => 'input_date_time', 'placeholder' => 'Estimate time','ng-cloak']) !!}
						<span class="text-danger error_msg"></span>
					</div>
                </div>
                <div class="row clearfix">
					<div class="col-md-12">
                        {!! Form::number('fee', '', ['class' => 'form-control', 'id' => 'input_fee', 'placeholder' => '0.00', 'autocomplete' => 'off',"step" => "0.01"]) !!}
				<span class="text-danger error_msg">{{ $errors->first('fee') }}</span>
					</div>
                </div>
                <div class="row clearfix">
					<div class="col-md-12">
						{!! Form::textArea('order_description','', ['class' => 'form-control change_field', 'id' => 'input_order_description', 'placeholder' => 'Order description']) !!}
						<span class="text-danger error_msg"></span>
					</div>
                </div>
			</div>
			<div class="col-md-8 map-wrap">
				<div class="map-route-option">
					<div>
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
					<div class="fare-btn clearfix">
						<button type="button" class="btn btn-primary submit_button change_field" disabled ng-disabled="deliveryAddForm.$invalid && page_loading==0" ng-click="submitForm($event);">
								Submit
						</button>
					</div>
				</div>
			</div>
		</div>

	</section>
	{!! Form::close() !!}
</div>
@endsection
@push('scripts')
<script type="text/javascript">
	var REQUEST_URL = "{{url('/'.LOGIN_USER_TYPE)}}"; 
	var old_edit_date = "{{''}}"
	var page = "{{'new'}}"
</script>
<script src="{{ url('js/selectize.js') }}"></script>
<script>
	$(function() {
		$('#input-merchant-id').selectize({
		    plugins: ['remove_button'],
		    maxItems: 1
    		
		});
		init_user();
	})
	function init_user()
{
  var usertype= 'all';
    var select = $("#input-merchant-id").selectize();
    var selectize = select[0].selectize;
    selectize.disable();
    
    $.ajax({
      type: 'GET',
      url: APP_URL+'/{{LOGIN_USER_TYPE}}/get_send_merchants',
      dataType: "json",
      success: function(resultData) {
        console.log(resultData);
        var select = $("#input-merchant-id").selectize();
        var selectize = select[0].selectize;
        selectize.clear();
        selectize.clearOptions();
        $.each(resultData, function (key, value) {
          selectize.addOption({value:value.id,text:value.id + ' - ' +  value.name});
        });
        selectize.enable();

        selectize.setValue(1, false);

      }
    });
  }

</script>
@endpush