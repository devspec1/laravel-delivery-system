@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Edit Meta
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/metas') }}">Meta</a></li>
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
              <h3 class="box-title">Edit Meta Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {!! Form::open(['url' => 'admin/edit_meta/'.$result->id, 'class' => 'form-horizontal']) !!}
              <div class="box-body">
              <span class="text-danger">(*)Fields are Mandatory</span>
                <div class="form-group">
                  <label for="input_url" class="col-sm-3 control-label">Page URL</label>
                  <div class="col-sm-6">
                    {!! Form::text('url', $result->url, ['class' => 'form-control', 'id' => 'input_url', 'placeholder' => 'Page URL', 'readonly' => 'true']) !!}
                    <span class="text-danger">{{ $errors->first('url') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_title" class="col-sm-3 control-label">Page Title<em class="text-danger">*</em></label>
                  <div class="col-sm-6">
                    {!! Form::text('title', $result->title, ['class' => 'form-control', 'id' => 'input_title', 'placeholder' => 'Page Title']) !!}
                    <span class="text-danger">{{ $errors->first('title') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_description" class="col-sm-3 control-label">Meta Description</label>
                  <div class="col-sm-6">
                    {!! Form::textarea('description', $result->description, ['class' => 'form-control', 'id' => 'input_description', 'placeholder' => 'Meta Description', 'rows' => 3]) !!}
                    <span class="text-danger">{{ $errors->first('description') }}</span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="input_keywords" class="col-sm-3 control-label">Keywords</label>
                  <div class="col-sm-6">
                    {!! Form::textarea('keywords', $result->keywords, ['class' => 'form-control', 'id' => 'input_keywords', 'placeholder' => 'Meta Keywords', 'rows' => 3]) !!}
                    <span class="text-danger">{{ $errors->first('keywords') }}</span>
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