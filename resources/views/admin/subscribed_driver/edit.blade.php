@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" ng-controller="company_management">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Edit Subscribed Driver
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        @if(LOGIN_USER_TYPE == 'admin')
        <li><a href="{{ url(LOGIN_USER_TYPE.'/subscriptions/driver') }}">Subscribed Drivers</a></li>
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
              <h3 class="box-title">Edit Subscribed Driver Form</h3>
              
            </div>
            {!! Form::open(['url' => LOGIN_USER_TYPE.'/subscriptions/edit_driver/'.$result->id, 'class' => 'form-horizontal','files' => true,'id'=>'subscribed_driver_form']) !!}
              <div class="box-body ed_bld">
              <span class="text-danger">(*)Fields are Mandatory</span>
                
                <div class="form-group">
                  <label for="input_status" class="col-sm-3 control-label">Plan Name <em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                  <select class ='form-control' id = 'input_status' name='plan' >
                    @foreach($all_plans as $plan)
                      <option value="{{@$plan->id}}" {{ ($plan->id == $result->plan) ? 'Selected' : ''}}>{{$plan->plan_name}}</option>
                    @endforeach
                  </select>
                    <span class="text-danger">{{ $errors->first('plan') }}</span>
                  </div>
                </div>                
                <div class="form-group">
                <label for="input_status" class="col-sm-3 control-label">Status <em class="text-danger">*</em></label>

                <div class="col-sm-6">
                    {!! Form::select('status', array('subscribed' => 'subscribed', 'cancelled' => 'cancelled', 'paused' => 'paused'), $result->status, ['class' => 'form-control', 'id' => 'input_status']) !!}
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                </div>
                </div>

              <!-- /.box-body -->
              <div class="box-footer">
                <button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
                  <a href="{{url(LOGIN_USER_TYPE.'/subscriptions/driver')}}"><span class="btn btn-default pull-left">Cancel</span></a>
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