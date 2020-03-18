@extends('admin.template')
@section('main')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Edit Peak Based Fare
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="{{ url('admin/manage_fare') }}">Peak Based Fare</a></li>
      <li class="active">Edit</li>
    </ol>
  </section>
  <!-- Main content -->
  <section class="content" ng-controller='manage_peak_fare'>
    <div class="row" ng-cloak>
      <!-- right column -->
      <div class="col-md-8 col-sm-offset-2">
        <!-- Horizontal Form -->
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Edit Peak Based Fare Form</h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          {!! Form::open(['url' => 'admin/edit_manage_fare/'.$result->id, 'class' => 'form-horizontal','id' => 'manage_peak_fare_form']) !!}
          <div class="box-body" ng-init="time_options={{ json_encode($time_options) }};day_options={{json_encode($day_options)}};">
            <span class="text-danger">(*)Fields are Mandatory</span>
            <div class="form-group">
              <label for="input_location" class="col-sm-3 control-label">
                Location <em class="text-danger">*</em>
              </label>
              <div class="col-sm-6" ng-init="location='{{ old('location','') }}'">
                {!! Form::select('location', $locations, $result->location_id, ['class' => 'form-control', 'id' => 'input_location','placeholder' => 'Select']) !!}
                <span class="text-danger">{{ $errors->first('location') }}</span>
              </div>
            </div>
            <div class="form-group">
              <label for="input_vehicle_type" class="col-sm-3 control-label">
                Vehicle Name <em class="text-danger">*</em>
              </label>
              <div class="col-sm-6">
                {!! Form::select('vehicle_type', $vehicle_types, old('vehicle_type',$result->vehicle_id), ['class' => 'form-control', 'id' => 'input_vehicle_type','placeholder' => 'Select']) !!}
                <span class="text-danger">{{ $errors->first('vehicle_type') }}</span>
                <span class="text-danger">{{ $errors->first('same_loc_error') }}</span>
              </div>
            </div>

            <div class="form-group">
                  <label for="input_base_fare" class="col-sm-3 control-label">Base Fare<em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::text('base_fare', $result->base_fare, ['class' => 'form-control', 'id' => 'input_base_fare', 'placeholder' => 'Base Fare']) !!}
                    <span class="text-danger">{{ $errors->first('base_fare') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_capacity" class="col-sm-3 control-label">Capacity<em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::text('capacity', $result->capacity, ['class' => 'form-control', 'id' => 'input_capacity', 'placeholder' => 'Capacity']) !!}
                    <span class="text-danger">{{ $errors->first('capacity') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_min_fare" class="col-sm-3 control-label">Minimum Fare<em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::text('min_fare',  $result->min_fare, ['class' => 'form-control', 'id' => 'input_min_fare', 'placeholder' => 'Minimum Fare']) !!}
                    <span class="text-danger">{{ $errors->first('min_fare') }}</span>
                  </div>
                </div>
              <div class="form-group">
                  <label for="input_per_min" class="col-sm-3 control-label">Per Minutes<em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::text('per_min', $result->per_min, ['class' => 'form-control', 'id' => 'input_per_min', 'placeholder' => 'Per Minutes']) !!}
                    <span class="text-danger">{{ $errors->first('per_min') }}</span>
                  </div>
                </div>
              <div class="form-group">
                  <label for="input_per_km" class="col-sm-3 control-label">Per Kilometer<em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::text('per_km', $result->per_km, ['class' => 'form-control', 'id' => 'input_per_km', 'placeholder' => 'Per Kilometer']) !!}
                    <span class="text-danger">{{ $errors->first('per_km') }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label for="input_waiting_time" class="col-sm-3 control-label">
                    Waiting Time Limit (In Minutes) <em class="text-danger">*</em>
                  </label>
                  <div class="col-sm-6">
                    {!! Form::text('waiting_time', old('waiting_time',$result->waiting_time), ['class' => 'form-control', 'id' => 'input_waiting_time', 'placeholder' => 'Waiting Time']) !!}
                    <span class="text-danger"> {{ $errors->first('waiting_time') }} </span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_waiting_charge" class="col-sm-3 control-label">
                    Waiting Charges (Per Minute) <em class="text-danger">*</em>
                  </label>
                  <div class="col-sm-6">
                    {!! Form::text('waiting_charge', old('waiting_charge',$result->waiting_charge), ['class' => 'form-control', 'id' => 'input_waiting_charge', 'placeholder' => 'Waiting Charge']) !!}
                    <span class="text-danger"> {{ $errors->first('waiting_charge') }} </span>
                  </div>
                </div>

              <div class="form-group">
                  <label for="input_schedule_fare" class="col-sm-3 control-label">Schedule Ride Fare<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::text('schedule_fare', $result->schedule_fare, ['class' => 'form-control', 'id' => 'input_schedule_fare', 'placeholder' => 'Schedule Ride Fare']) !!}
                    <span class="text-danger">{{ $errors->first('schedule_fare') }}</span>
                  </div>
              </div>

                
              <div class="form-group">
                  <label for="input_currency_code" class="col-sm-3 control-label">Currency code<em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::select('currency_code', $currency, $result->currency_code, ['class' => 'form-control', 'id' => 'input_currency_code', 'placeholder' => 'Select']) !!}
                    <span class="text-danger">{{ $errors->first('currency_code') }}</span>
                  </div>
                </div>

            <div class="form-group">
              <label for="input_peak_fare" class="col-sm-3 control-label">
                Apply Peak Fare <em class="text-danger">*</em>
              </label>
              <div class="col-sm-6" ng-init="apply_peak='{{ old('apply_peak',$result->apply_peak) }}'">
                {!! Form::select('apply_peak', array_merge([''=>'Select','Yes' =>'Yes','No' =>'No']),'', ['class' => 'form-control', 'id' => 'input_peak_fare','ng-model'=>'apply_peak']) !!}
                <span class="text-danger" ng-hide="apply_peak != ''">{{ $errors->first('apply_peak') }}</span>
                <span class="text-danger" ng-hide="apply_peak == 'Yes' || apply_night =='Yes'">{{ $errors->first('not_fare') }}</span>
              </div>
            </div>
            <div class="box-body fare-body" ng-show="apply_peak == 'Yes'">
              <div class="col-md-12 peak_fare_wrapper">
                <div class="panel panel-info">
                  <div class="panel-header">
                    <h4> Peak Fare Details </h4>
                  </div>
                  <div class="panel-body" ng-init="peak_fare_details = {{json_encode(old('peak_fare_details') ?: $peak_fare_details)}};errors = {{json_encode($errors->getMessages())}};removed_fares=''">
                    <input type='hidden' name="removed_fares" ng-model="removed_fares" value="@{{ removed_fares }}">
                    <div class="row">
                      <div class="peak-row" ng-repeat="fare_detail in peak_fare_details">
                        <input type='hidden' name="peak_fare_details[@{{$index}}][id]" ng-model="fare_detail.id" value="@{{ fare_detail.id }}">
                        <div class="row">
                          <div class="col-md-3">
                            <div class="input-addon">
                              <label>
                                Day
                              </label>
                              <select name="peak_fare_details[@{{$index}}][day]" ng-model="fare_detail.day" id="peak_fare_day_@{{ $index }}" class="peak_fare_details peak_fare_day_details form-control" data-old_day = "@{{fare_detail.day}}" ng-change="update_day($index,fare_detail.day)" data-index="@{{ $index }}">
                                <option value="" disabled>Select</option>
                                <option ng-repeat="(key, value) in day_options" value="@{{ key }}" ng-selected="key == fare_detail.day" ng-disabled="ifDayDisabled($parent.$index,key)">@{{value}}</option>
                              </select>
                              <span class="input-suffix"></span>
                            </div>
                            <span class="text-danger ">@{{ errors['peak_fare_details.'+$index+'.day'][0] }}</span>
                          </div>
                          <div class="col-md-3">
                            <div class="input-addon">
                              <label>
                                Start Time
                              </label>
                              <select name="peak_fare_details[@{{$index}}][start_time]" ng-model="fare_detail.start_time" id="peak_fare_starttime_@{{ $index }}" class="peak_fare_details form-control" ng-change="update_time($index,fare_detail.day)" ng-init="update_time($index,fare_detail.day)">
                                <option value="" disabled>Select</option>
                                <option  ng-repeat="(key, value) in time_options" value="@{{ key }}" ng-selected="key == fare_detail.start_time" ng-disabled="checkIfDisabled($parent.$index,fare_detail.day,key,'start_time')"> @{{value}}</option>
                              </select>
                              <span class="input-suffix"></span>
                            </div>
                            <span class="text-danger">@{{ errors['peak_fare_details.'+$index+'.start_time'][0] }}</span>
                          </div>
                          <div class="col-md-3">
                            <div class="input-addon">
                              <label>
                                End Time
                              </label>
                              <select name="peak_fare_details[@{{$index}}][end_time]" ng-model="fare_detail.end_time" id="peak_fare_endtime_@{{ $index }}" class="peak_fare_details form-control" ng-change="update_time($index,fare_detail.day)">
                                <option value="" disabled>Select</option>
                                <option  ng-repeat="(key, value) in time_options" value="@{{ key }}" ng-selected="key == fare_detail.end_time" ng-disabled="key <= fare_detail.start_time || checkIfDisabled($parent.$index,fare_detail.day,key,'end_time')">@{{value}}</option>
                              </select>
                              <span class="input-suffix"></span>
                            </div>
                            <span class="text-danger">@{{ errors['peak_fare_details.'+$index+'.end_time'][0] }}</span>
                          </div>
                          <div class="col-md-2 peak-fare-wrap">
                            <div class="input-addon">
                              <label class="peak-fare-label">
                                Peak Fare x
                                <i id="peak-fare-tooltip" data-toggle="tooltip" data-html="true"  class="glyphicon  glyphicon-info-sign admin_fare_tooltip" title="Normal fare = 100<br>Peak Fare x = 2 <br>Peak Time Pricing x2 = 100<br>sub total = 200"></i>
                              </label>
                              <input type="text" name="peak_fare_details[@{{$index}}][price]" ng-model="fare_detail.price" id="peak_fare_price_@{{$index}}" class="form-control peak_fare_details" placeholder="">
                              <span class="input-suffix">
                              </span>
                            </div>
                            <span class="text-danger">@{{ errors['peak_fare_details.'+$index+'.price'][0] }}</span>
                          </div>
                          <div class="col-md-1 delete-btn-wrap">
                            <a href="javascript:void(0)" class="btn btn-danger btn-xs" ng-click="remove_price_rule('peak', $index,fare_detail.day)" ng-if="peak_fare_details.length > 1">
                              <span class="fa fa-trash">
                              </span>
                            </a>
                          </div>
                        </div>
                        <span class="text-danger peak_fare_error hide" id="Peak_fare_error_@{{$index}}">Please Choose Valid Time</span>
                      </div>
                      <div class="clearfix add-btn-wrap text-right">
                        <a href="javascript:void(0)" class="btn btn-success btn-sm" ng-click="add_price_rule('peak')">
                          <span class="fa fa-plus"></span> Add
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="input_night_fare" class="col-sm-3 control-label">
                Apply Night Fare <em class="text-danger">*</em>
              </label>
              <div class="col-sm-6" ng-init="apply_night='{{ old('apply_night',$result->apply_night) }}'">
                {!! Form::select('apply_night', array_merge([''=>'Select','Yes' =>'Yes','No' =>'No']), '', ['class' => 'form-control', 'id' => 'input_night_fare','ng-model'=>'apply_night']) !!}
                <span class="text-danger" ng-hide="apply_night != ''">{{ $errors->first('apply_night') }}</span>
              </div>
            </div>
            <div class="box-body fare-body" ng-show="apply_night == 'Yes'">
              <div class="col-md-12 night_fare_wrapper">
                <div class="panel panel-info">
                  <div class="panel-header">
                    <h4> Night Fare Details </h4>
                  </div>
                  <div class="panel-body" ng-init="night_fare_details = {{json_encode(old('night_fare_details') ?: $night_fare_details)}};">
                    <div class="row">
                      <input type='hidden' name="night_fare_details[id]" value="@{{ night_fare_details.id }}">
                      <div class="night-fare-row">
                        <div class="row">
                          <div class="col-md-3">
                            <div class="input-addon">
                              <label>
                                Start Time
                              </label>
                              <select name="night_fare_details[start_time]" ng-model="night_fare_details.start_time" id="night_fare_starttime" class="peak_fare_details form-control" ng-change="updateNightTimeOptions();update_night_fare_time();">
                                <option value="" disabled>Select</option>
                                <option  ng-repeat="(key, value) in time_options" value="@{{ key }}" ng-selected="key == night_fare_details.start_time">@{{value}}</option>
                              </select>
                              <span class="input-suffix"></span>
                            </div>
                            <span class="text-danger night_fare_text_danger">@{{ errors['night_fare_details.start_time'][0] }}</span>
                          </div>
                          <div class="col-md-3">
                            <div class="input-addon">
                              <label>
                                End Time
                              </label>
                              <select name="night_fare_details[end_time]" ng-model="night_fare_details.end_time" id="night_fare_endtime" class="peak_fare_details form-control">
                                <option value="" disabled>Select</option>
                                <option ng-repeat="(key, value) in before_times" value="@{{ key }}" ng-selected="key == night_fare_details.end_time"> @{{value}} @{{ (key <= night_fare_details.start_time) ? '*' :'' }} </option>
                                <option ng-repeat="(key, value) in after_times" value="@{{ key }}" ng-selected="key == night_fare_details.end_time"> @{{value}} @{{ (key <= night_fare_details.start_time) ? '*' :'' }} </option>
                              </select>
                              <span class="input-suffix"></span>
                            </div>
                            <span class="text-danger night_fare_text_danger">@{{ errors['night_fare_details.end_time'][0] }}</span>
                          </div>
                          <div class="col-md-3 night-fare-wrap">
                            <div class="input-addon">
                              <label class="night-fare-label">
                                Night Fare x
                                <i id="night-fare-tooltip" data-toggle="tooltip" data-html="true"  class="glyphicon  glyphicon-info-sign admin_fare_tooltip" title="Normal fare = 100<br>Night Fare x = 2 <br>Night Time Pricing x2 = 100<br>sub total = 200"></i>
                              </label>
                              <input type="text" name="night_fare_details[price]" ng-model="night_fare_details.price" id="night_fare_price" class="form-control night_fare_details" placeholder="">
                              <span class="input-suffix">
                              </span>
                            </div>
                            <span class="text-danger night_fare_text_danger">@{{ errors['night_fare_details.price'][0] }}</span>
                          </div>
                        </div>
                        <span class="text-danger night_fare_error hide" id="night_fare_error">Please Choose Valid Time</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <button type="submit" class="btn btn-info pull-right manage_fare_submit" name="submit" value="submit" ng-click="disableButton($event)">Submit</button>
            <a href="{{ url('admin/manage_fare') }}" class="btn btn-default pull-left" name="cancel" value="Cancel">
              Cancel
            </a>
          </div>
          <!-- /.box-footer -->
          {!! Form::close() !!}
        </div>
        <!-- /.box -->
      </div>
      <!--/.col (right) -->
    </div>
    <!-- /.row -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@stop