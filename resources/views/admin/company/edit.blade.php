@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" ng-controller="company_management">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Edit Company
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        @if(LOGIN_USER_TYPE == 'admin')
        <li><a href="{{ url(LOGIN_USER_TYPE.'/company') }}">Companies</a></li>
        @endif
        <li class="active">Edit</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- right column -->
        <div class="col-md-8 col-sm-offset-2 ne_ed">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Edit Company Form</h3>
              
            </div>
            {!! Form::open(['url' => LOGIN_USER_TYPE.'/edit_company/'.$result->id, 'class' => 'form-horizontal','files' => true,'id'=>'company_form']) !!}
              <div class="box-body ed_bld">
              <span class="text-danger">(*)Fields are Mandatory</span>
                <div class="form-group">
                  <label for="input_name" class="col-sm-3 control-label">Name <em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::text('name', $result->name, ['class' => 'form-control', 'id' => 'input_name', 'placeholder' => 'Name']) !!}
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_vat_number" class="col-sm-3 control-label">VAT Number</label>

                  <div class="col-sm-6">
                    {!! Form::text('vat_number', $result->vat_number, ['class' => 'form-control', 'id' => 'input_vat_number', 'placeholder' => 'VAT Number']) !!}
                    <span class="text-danger">{{ $errors->first('vat_number') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_email" class="col-sm-3 control-label">Email <em class="text-danger">*</em></label>

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
                <div class="form-group">
                  <label for="input_status" class="col-sm-3 control-label">Country Code <em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                  <select class ='form-control' id = 'input_status' name='country_code' >
                    @foreach($country_code_option as $country_code)
                      <option value="{{@$country_code->phone_code}}" {{ ($country_code->phone_code == $result->country_code) ? 'Selected' : ''}}>{{$country_code->long_name}}</option>
                    @endforeach
                  </select>
                    <!-- {!! Form::select('country_code', $country_code_option, '', ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!} -->
                    <span class="text-danger">{{ $errors->first('country_code') }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label for="input_status" class="col-sm-3 control-label">Mobile Number 
                  </label>

                  <div class="col-sm-6">
                     {!! Form::text('mobile_number', $result->env_mobile_number, ['class' => 'form-control', 'id' => 'mobile_number', 'placeholder' => 'Mobile Number']) !!}
                    <span class="text-danger">{{ $errors->first('mobile_number') }}</span>
                  </div>
                </div> 
                @if(LOGIN_USER_TYPE!='company') 
                  <div class="form-group">
                    <label for="input_status" class="col-sm-3 control-label">Status <em class="text-danger">*</em></label>

                    <div class="col-sm-6">
                      {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive', 'Pending' => 'Pending'), $result->status, ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
                      <span class="text-danger">{{ $errors->first('status') }}</span>
                    </div>
                  </div>
                @endif

                 <div class="form-group">
                  <label for="input_status" class="col-sm-3 control-label">Address Line <em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                     {!! Form::text('address_line',$result->address, ['class' => 'form-control', 'id' => 'address_line', 'placeholder' => 'Address Line']) !!}
                    <span class="text-danger">{{ $errors->first('address_line') }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label for="input_status" class="col-sm-3 control-label">City </label>

                  <div class="col-sm-6">
                     
                     {!! Form::text('city',$result->city, ['class' => 'form-control', 'id' => 'city', 'placeholder' => 'City']) !!}
                    <span class="text-danger">{{ $errors->first('city') }}</span>
                  </div>
                </div>

                
                 <div class="form-group">
                  <label for="input_status" class="col-sm-3 control-label">State</label>

                  <div class="col-sm-6">
                     {!! Form::text('state',$result->state, ['class' => 'form-control', 'id' => 'state', 'placeholder' => 'State']) !!}
                    <span class="text-danger">{{ $errors->first('state') }}</span>
                  </div>
                </div> 
                <div class="form-group">
                  <label for="input_status" class="col-sm-3 control-label">Postal Code <em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    
                     {!! Form::text('postal_code',$result->postal_code, ['class' => 'form-control', 'id' => 'postal_code', 'placeholder' => 'Postal Code']) !!}
                    <span class="text-danger">{{ $errors->first('postal_code') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_profile" class="col-sm-3 control-label">Profile </label>
                  <div class="col-sm-6">
                    {!! Form::file('profile', ['class' => 'form-control', 'id' => 'input_profile', 'accept' => 'image/*']) !!}
                    <span class="text-danger">{{ $errors->first('profile') }}</span><br>
                    @if(@$result->profile)
                      <a href="{{@$result->profile }}" target="_blank"> <img style="width: 200px;height: 100px" src="{{@$result->profile }}" id="profile_photo"></a>
                    @else
                      <img style="width: 100px;height: 100px; padding-top: 5px;" src="{{ url('images/user.jpeg')}}">
                    @endif
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_license" class="col-sm-3 control-label">License</label>
                  <div class="col-sm-6">
                    {!! Form::file('license', ['class' => 'form-control', 'id' => 'input_license', 'accept' => 'image/*']) !!}
                    <span class="text-danger">{{ $errors->first('license') }}</span><br>
                    @if(@$documents->license_photo)
                      <a href="{{@$documents->license_photo }}" target="_blank"> <img style="width: 200px;height: 100px" src="{{@$documents->license_photo }}" id="license_photo"></a>
                    @else
                      <img style="width: 100px;height: 100px; padding-top: 5px;" src="{{ url('images/driver_doc.png')}}">
                    @endif
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_license_exp_date" class="col-sm-3 control-label">License Expiry Date</label>
                  
                  <div class="col-sm-6">
                    {!! Form::text('license_exp_date', @$documents->license_exp_date , ['class' => 'form-control', 'id' => 'license_exp_date', 'placeholder' => 'License Expiry Date', 'autocomplete' => 'off']) !!}
                    <span class="text-danger">{{ $errors->first('license_exp_date') }}</span>
                    
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_license_front" class="col-sm-3 control-label">Insurance</label>
                  <div class="col-sm-6">
                    {!! Form::file('insurance', ['class' => 'form-control', 'id' => 'input_insurance', 'accept' => 'image/*']) !!}
                    <span class="text-danger">{{ $errors->first('insurance') }}</span><br>
                    @if(@$documents->insurance_photo)
                      <a href="{{@$documents->insurance_photo }}" target="_blank"> <img style="width: 200px;height: 100px" src="{{@$documents->insurance_photo }}" id="insurance_photo"></a>
                    @else
                      <img style="width: 100px;height: 100px; padding-top: 5px;" src="{{ url('images/driver_doc.png')}}">
                    @endif
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_insurance_exp_date" class="col-sm-3 control-label">Insurance Expiry Date</label>
                  
                  <div class="col-sm-6">
                    {!! Form::text('insurance_exp_date', @$documents->insurance_exp_date, ['class' => 'form-control', 'id' => 'insurance_exp_date', 'placeholder' => 'Insurance Expiry Date', 'autocomplete' => 'off']) !!}
                    <span class="text-danger">{{ $errors->first('insurance_exp_date') }}</span>
                    
                  </div>
                </div>
                @if($result->id != 1)

                <div class="form-group">
                  <label for="input_service_fee" class="col-sm-3 control-label">Company Commission <em class="text-danger">*</em></label>
                  <div class="col-sm-7">
                  <div class="input-group"> 
                    {!! Form::text('company_commission', $result->company_commission, ['class' => 'form-control', 'id' => 'input_service_fee', 'placeholder' => 'Company Commission','readonly'=>LOGIN_USER_TYPE == 'company'?true:false]) !!}
                    <div class="input-group-addon" style="background-color:#eee;">%</div>
                    <span class="text-danger">{{ $errors->first('company_commission') }}</span>
                  </div>
                  </div>
                </div>

                @endif
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
                @if(LOGIN_USER_TYPE == 'company')
                  <a href="{{url(LOGIN_USER_TYPE.'/edit_company/'.$result->id)}}"><span class="btn btn-default pull-left">Cancel</span></a>
                @else
                  <a href="{{url(LOGIN_USER_TYPE.'/company')}}"><span class="btn btn-default pull-left">Cancel</span></a>
                @endif
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
  @endsection
  @push('scripts')
<script>
var datepicker_format = 'dd-mm-yy';
$('#license_exp_date').datepicker({ 'dateFormat': datepicker_format, maxDate: new Date()});
$(function () {
    $("#yearDate").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: '1950:' + new Date().getFullYear().toString(),
        dateFormat: datepicker_format,
    });
    $('.ui-datepicker').addClass('notranslate');
});
$('#insurance_exp_date').datepicker({ 'dateFormat': datepicker_format, maxDate: new Date()});
$(function () {
    $("#yearDate").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: '1950:' + new Date().getFullYear().toString(),
        dateFormat: datepicker_format,
    });
    $('.ui-datepicker').addClass('notranslate');
});
</script>
@endpush
