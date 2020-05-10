@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Add Subscription Plan
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/roles') }}">Subscription Plans</a></li>
        <li class="active">Add</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- right column -->
        <div class="col-md-8 col-sm-offset-2">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Add Subscription Plan Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
              {!! Form::open(['url' => 'admin/subscriptions/add_plan', 'class' => 'form-horizontal']) !!}
              <div class="box-body">
              <span class="text-danger">(*)Fields are Mandatory</span>
                <div class="form-group">
                  <label for="input_plan_id" class="col-sm-3 control-label">Plan ID<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::text('plan_id', '', ['class' => 'form-control', 'id' => 'input_plan_id', 'placeholder' => 'Plan ID']) !!}
                    <span class="text-danger">{{ $errors->first('plan_id') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_plan_name" class="col-sm-3 control-label">Plan Name<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::text('plan_name', '', ['class' => 'form-control', 'id' => 'input_plan_name', 'placeholder' => 'Plan Name']) !!}
                    <span class="text-danger">{{ $errors->first('plan_name') }}</span>
                  </div>
                </div>        
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
                 <button type="submit" class="btn btn-default pull-left" name="cancel" value="cancel">Cancel</button>
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