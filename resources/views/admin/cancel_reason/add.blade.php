@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Add Cancel Reason
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/cancel-reason') }}">Cancel Reason</a></li>
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
              <h3 class="box-title">Add Cancel Reason Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {!! Form::open(['url' => 'admin/add-cancel-reason', 'class' => 'form-horizontal']) !!}
              <div class="box-body">
              <span class="text-danger">(*)Fields are Mandatory</span>
                <div class="form-group">
                  <label for="input_reason" class="col-sm-3 control-label">Reason<em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::text('reason', '', ['class' => 'form-control', 'id' => 'input_reason', 'placeholder' => 'Reason']) !!}
                    <span class="text-danger">{{ $errors->first('reason') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_cancelled_by" class="col-sm-3 control-label">Cancelled By<em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::select('cancelled_by', array('Rider' => 'Rider', 'Driver' => 'Driver', 'Admin' => 'Admin', 'Company' => 'Company'), '', ['class' => 'form-control', 'id' => 'input_cancelled_by', 'placeholder' => 'Select']) !!}
                    <span class="text-danger">{{ $errors->first('cancelled_by') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_status" class="col-sm-3 control-label">Status<em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::select('status', array('Active' => 'Active', 'Inactive' => 'Inactive'), '', ['class' => 'form-control', 'id' => 'input_status', 'placeholder' => 'Select']) !!}
                    <span class="text-danger">{{ $errors->first('status') }}</span>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
                <a href="{{ url(LOGIN_USER_TYPE.'/cancel-reason') }}" class="btn btn-default pull-left">Cancel</a>
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