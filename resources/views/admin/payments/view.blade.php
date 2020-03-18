@extends('admin.template')

@section('main')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Payments Details
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url(LOGIN_USER_TYPE.'/payments') }}">Payments</a></li>
        <li class="active">Details</li>
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
              <h3 class="box-title">Payments Details</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            {!! Form::open(['url' => 'admin/payments/detail/'.$result->id, 'class' => 'form-horizontal', 'style' => 'word-wrap: break-word']) !!}
              <div class="box-body">
                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Room name
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ $result->car_type->car_name }}
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Driver name
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ $result->driver_name}}
                   </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Rider name
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ $result->users->first_name }}
                   </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Begin Trip
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ date('g:i a',strtotime($result->begin_trip)) }}
                   </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    End Trip
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ date('g:i a',strtotime($result->end_trip)) }}
                   </div>
                </div>
             
                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Base fare
                  </label>

                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ $result->currency->symbol }}{{ $result->base_fare }}
                   </div>
                </div>

                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Pickup Location
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ $result->pickup_location }}
                   </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Drop Location
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ $result->drop_location }}
                   </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Service fee
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ $result->currency->symbol }}{{ $result->access_fee }}
                   </div>
                </div>
                
             
                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Total amount
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ $result->currency->symbol }}{{ $result->total_fare }}
                   </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Currency
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ $result->currency_code }}
                   </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    @if($result->driver->default_payout_credentials->type == 'paypal')
                      Driver payout Email id
                    @else
                      Driver payout Account
                    @endif
                  </label>
                  <div class="col-sm-6 col-sm-offset-1">
                    {{ $result->driver->payout_id }}
                   </div>
                </div> 
                 <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Guest payout email id
                  </label>
                  <div class="col-sm-6 col-sm-offset-1">
                    {{ $result->users->payout_id }}
                   </div>
                </div>
           
                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Status
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ $result->status }}
                   </div>
                </div>
               
                @if($result->status == "Cancelled")
                  <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Cancelled Reason
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ @$result->cancel->cancelled_reason }}
                   </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Cancelled Message
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ @$result->cancel->cancel_comments }}
                   </div>
                </div>

                 <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Cancelled By
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ @$result->cancel->cancelled_by }}
                   </div>
                </div>
                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Cancelled Date
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ @$result->cancel->created_at }}
                   </div>
                </div>
                @endif
                <div class="form-group">
                  <label class="col-sm-3 control-label">
                    Transaction ID
                  </label>
                  <div class="col-sm-6 col-sm-offset-1 form-control-static">
                    {{ $result->transaction_id }}
                   </div>
                </div>
               
              
              </div>
              <!-- /.box-body -->
            </form> 
             
              <div class="box-footer text-center">
                <a class="btn btn-default" href="{{ url('admin/trips') }}">Back</a>
              </div>
              <!-- /.box-footer -->
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
  $('#input_dob').datepicker({ 'format': 'dd-mm-yyyy'});
</script>
@endpush
