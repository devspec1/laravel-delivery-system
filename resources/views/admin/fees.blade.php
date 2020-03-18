@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
<div class="fees-wrap content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Fees
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}">
          <i class="fa fa-dashboard"></i> 
          Home
        </a>
      </li>
      <li>
        <a href="#">
          Fees
        </a>
      </li>
      <li class="active">
        Edit
      </li>
    </ol>
  </section>
  <!-- Main content -->

  <div class="row">
    <!-- right column -->
    <div class="col-md-8 col-sm-offset-2">
      <!-- Horizontal Form -->
      <div class="box box-info">
        <div class="box-header with-border">
          <h3 class="box-title">Fees Form</h3>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        {!! Form::open(['url' => 'admin/fees', 'class' => 'form-horizontal']) !!}
        <div class="box-body">                
          <div class="form-group">
            <label for="input_service_fee" class="col-sm-3 control-label">Rider Service Fee</label>
            <div class="col-sm-7 col-md-5">
              <div class="input-group"> 
                {!! Form::text('access_fee', $result[0]->value, ['class' => 'form-control', 'id' => 'input_service_fee', 'placeholder' => 'Rider Service Fee']) !!}
                <div class="input-group-addon" style="background-color:#eee;">%</div>
                <span class="text-danger">{{ $errors->first('access_fee') }}</span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="input_service_fee" class="col-sm-3 control-label">
              Driver Peak Fare
            </label>
            <div class="col-sm-7 col-md-5">
              <div class="input-group"> 
                {!! Form::text('driver_peak_fare', $result[1]->value, ['class' => 'form-control', 'id' => 'input_driver_peak_fare', 'placeholder' => 'Driver Peak Fare']) !!}
                <div class="input-group-addon" style="background-color:#eee;">%</div>
                <span class="text-danger">{{ $errors->first('input_driver_peak_fare') }}</span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="input_service_fee" class="col-sm-3 control-label">
              Driver Service Fee
            </label>
            <div class="col-sm-7 col-md-5">
              <div class="input-group"> 
                {!! Form::text('driver_service_fee', $result[2]->value, ['class' => 'form-control', 'id' => 'input_driver_service_fee', 'placeholder' => 'Driver Service Fee']) !!}
                <div class="input-group-addon" style="background-color:#eee;">%</div>
                <span class="text-danger">{{ $errors->first('driver_service_fee') }}</span>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="input_additional_fee" class="col-sm-3 control-label">
              Apply Trip Additional Fee
            </label>
            <div class="col-sm-7 col-md-5">
              <div class="input-group"> 
                {!! Form::select('additional_fee', array_merge(['Yes' =>'Yes','No' =>'No']),$result[3]->value, ['class' => 'form-control', 'id' => 'input_additional_fee']) !!}
                <span class="text-danger">{{ $errors->first('additional_fee') }}</span>
              </div>
            </div>
          </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
          <button type="submit" class="btn btn-default" name="cancel" value="cancel">Cancel</button>
          <button type="submit" class="btn btn-info pull-right" name="submit" value="submit">Submit</button>
        </div>
        <!-- /.box-footer -->
        {!! Form::close() !!}
      </div>
      <!-- /.box -->
    </div>
    <!--/.col (right) -->
  </div>
  <!-- /.row -->
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
@stop

