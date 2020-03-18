@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Join Us
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/join_us') }}">Join Us</a></li>
        <li class="active">Edit</li>
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
              <h3 class="box-title">Join Us Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
              {!! Form::open(['url' => 'admin/join_us', 'class' => 'form-horizontal']) !!}
              <div class="box-body">
                <div class="form-group">
                  <label for="input_facebook" class="col-sm-3 control-label">Facebook</label>
                  <div class="col-sm-6">
                    {!! Form::text('facebook', $result[0]->value, ['class' => 'form-control', 'id' => 'input_facebook', 'placeholder' => 'Facebook']) !!}
                    <span class="text-danger">{{ $errors->first('facebook') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_twitter" class="col-sm-3 control-label">Twitter</label>
                  <div class="col-sm-6">
                    {!! Form::text('twitter', $result[2]->value, ['class' => 'form-control', 'id' => 'input_twitter', 'placeholder' => 'Twitter']) !!}
                    <span class="text-danger">{{ $errors->first('twitter') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_linkedin" class="col-sm-3 control-label">Linkedin</label>
                  <div class="col-sm-6">
                    {!! Form::text('linkedin', $result[3]->value, ['class' => 'form-control', 'id' => 'input_linkedin', 'placeholder' => 'Linkedin']) !!}
                    <span class="text-danger">{{ $errors->first('linkedin') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_pinterest" class="col-sm-3 control-label">Pinterest</label>
                  <div class="col-sm-6">
                    {!! Form::text('pinterest', $result[4]->value, ['class' => 'form-control', 'id' => 'input_pinterest', 'placeholder' => 'Pinterest']) !!}
                    <span class="text-danger">{{ $errors->first('pinterest') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_youtube" class="col-sm-3 control-label">Youtube</label>
                  <div class="col-sm-6">
                    {!! Form::text('youtube', $result[5]->value, ['class' => 'form-control', 'id' => 'input_youtube', 'placeholder' => 'Youtube']) !!}
                    <span class="text-danger">{{ $errors->first('youtube') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_instagram" class="col-sm-3 control-label">Instagram</label>
                  <div class="col-sm-6">
                    {!! Form::text('instagram', $result[6]->value, ['class' => 'form-control', 'id' => 'input_instagram', 'placeholder' => 'Instagram']) !!}
                    <span class="text-danger">{{ $errors->first('instagram') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_app_store" class="col-sm-3 control-label">App store link to rider</label>
                  <div class="col-sm-6">
                    {!! Form::text('app_store_rider', $result[7]->value, ['class' => 'form-control', 'id' => 'input_app_store', 'placeholder' => 'App store link']) !!}
                    <span class="text-danger">{{ $errors->first('app_store_rider') }}</span>
                  </div>
                </div>

                   <div class="form-group">
                  <label for="input_app_store" class="col-sm-3 control-label">App store link to driver</label>
                  <div class="col-sm-6">
                    {!! Form::text('app_store_driver', $result[8]->value, ['class' => 'form-control', 'id' => 'input_app_store', 'placeholder' => 'App store link']) !!}
                    <span class="text-danger">{{ $errors->first('app_store_driver') }}</span>
                  </div>
                </div>

                <div class="form-group">
                  <label for="input_play_store" class="col-sm-3 control-label">Play store link to rider</label>
                  <div class="col-sm-6">
                    {!! Form::text('play_store_rider', $result[9]->value, ['class' => 'form-control', 'id' => 'input_play_store', 'placeholder' => 'Play store link']) !!}
                    <span class="text-danger">{{ $errors->first('play_store_rider') }}</span>
                  </div>
                </div>
                    <div class="form-group">
                  <label for="input_play_store" class="col-sm-3 control-label">Play store link to driver</label>
                  <div class="col-sm-6">
                    {!! Form::text('play_store_driver', $result[10]->value, ['class' => 'form-control', 'id' => 'input_play_store', 'placeholder' => 'Play store link']) !!}
                    <span class="text-danger">{{ $errors->first('play_store_driver') }}</span>
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