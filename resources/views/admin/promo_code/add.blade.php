@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Add Promo Code
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/promo_code') }}">Promo Code</a></li>
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
              <h3 class="box-title">Add Promo Code Form</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {!! Form::open(['url' => 'admin/add_promo_code', 'class' => 'form-horizontal']) !!}
              <div class="box-body">
              <span class="text-danger">(*)Fields are Mandatory</span>
                <div class="form-group">
                  <label for="input_promo_code" class="col-sm-3 control-label">Promo Code<em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::text('code', '', ['class' => 'form-control', 'id' => 'input_promo_code', 'placeholder' => 'Promo Code']) !!}
                    <span class="text-danger">{{ $errors->first('code') }}</span>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="input_amount" class="col-sm-3 control-label">Amount<em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::text('amount', '', ['class' => 'form-control', 'id' => 'input_amount', 'placeholder' => 'Amount']) !!}
                    <span class="text-danger">{{ $errors->first('amount') }}</span>
                  </div>
                </div>
                 <div class="form-group">
                  <label for="input_currency_code" class="col-sm-3 control-label">Currency code</label>
                  <div class="col-sm-6">
                    {!! Form::select('currency_code',$currency, '', ['class' => 'form-control', 'id' => 'input_currency_code', 'placeholder' => 'Select']) !!}
                    <span class="text-danger">{{ $errors->first('currency_code') }}</span>
                  </div>
                </div>
                 <div class="form-group">
                  <label for="input_expired_at" class="col-sm-3 control-label">Expire Date<em class="text-danger">*</em></label>

                  <div class="col-sm-6">
                    {!! Form::text('expire_date', '', ['class' => 'form-control', 'id' => 'input_expired_at', 'placeholder' => 'Expire Date', 'autocomplete' => 'off']) !!}
                    <span class="text-danger">{{ $errors->first('expire_date') }}</span>
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
@endsection
  @push('scripts')
    <script>
    $('#input_expired_at').datepicker({ startDate: "today",autoclose: true});
    </script>
  @endpush