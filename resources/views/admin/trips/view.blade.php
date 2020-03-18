@extends('admin.template')
@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<h1> Manage Trips Details </h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home</a>
			</li>
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/trips') }}">Trips</a>
			</li>
			<li class="active">
				Details
			</li>
		</ol>
	</section>
	<section class="content">
		<div class="row">
			<div class="col-md-8 col-sm-offset-2">
				<div class="box box-info">
					<div class="box-header with-border">
						<h3 class="box-title">Trips Details</h3>
					</div>
					{!! Form::open(['url' => '', 'class' => 'form-horizontal', 'style' => 'word-wrap: break-word']) !!}
					<div class="box-body">
						<div class="form-group">
							<label class="col-sm-3 control-label">
								Vehicle name
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
						@if(LOGIN_USER_TYPE != 'company')
						<div class="form-group">
							<label class="col-sm-3 control-label">
								Company name
							</label>
							<div class="col-sm-6 col-sm-offset-1 form-control-static">
								{{ $result->driver->company->name }}
							</div>
						</div>
						@endif
						<div class="form-group">
							<label class="col-sm-3 control-label">
								Trip date
							</label>
							<div class="col-sm-6 col-sm-offset-1 form-control-static">
								{{ $result->begin_date }}
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">
								Arrive Time
							</label>
							<div class="col-sm-6 col-sm-offset-1 form-control-static">
								{{ $result->getFormattedTime('arrive_time') }}
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">
								Begin Trip
							</label>
							<div class="col-sm-6 col-sm-offset-1 form-control-static">
								{{ $result->getFormattedTime('begin_trip') }}
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">
								End Trip
							</label>
							<div class="col-sm-6 col-sm-offset-1 form-control-static">
								{{ $result->getFormattedTime('end_trip') }}
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
								Currency
							</label>
							<div class="col-sm-6 col-sm-offset-1 form-control-static">
								{{ $result->currency_code }}
							</div>
						</div>

						@foreach($invoice_data as $invoice)
						<div class="form-group">
							<label class="col-sm-3 control-label">
								{{ $invoice['key'] }}
							</label>
							<div class="col-sm-6 col-sm-offset-1 form-control-static">
								{{ $invoice['value'] }}
							</div>
						</div>
						@endforeach
						
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
								{{ @$result->cancel->cancel_reason->reason }}
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

						@if(LOGIN_USER_TYPE != 'company')
							<div class="form-group">
								<label class="col-sm-3 control-label">
									Transaction ID
								</label>
								<div class="col-sm-6 col-sm-offset-1 form-control-static">
									{{ @$result->paykey }}
								</div>
							</div>
							@if($result->driver->company->id == 1 && $result->driver->default_payout_credentials == '')
							<div class="form-group">
								<label class="col-sm-3 control-label">
								</label>
								<div class="col-sm-6 col-sm-offset-1 form-control-static">
									Yet, Driver doesn't enter his Payout details.
								</div>
							</div>
							@elseif($result->status == "Completed" && $result->payout_status == "Paid")
							<div class="form-group">
								<label class="col-sm-3 control-label">
									Payout Status
								</label>
								<div class="col-sm-6 col-sm-offset-1 form-control-static">
									Payout successfully sent..
								</div>
							</div>
							@endif
						@endif


						@if($result->driver->company_id != 1)
							@if($result->driver->company->default_payout_credentials == '')
								<div class="form-group">
									<label class="col-sm-3 control-label">
									</label>
									<div class="col-sm-6 col-sm-offset-1 form-control-static">
										Yet, Company doesn't enter his Payout details.
									</div>
								</div>
							@else
								
							@endif						
						@endif
					</form>
					<div class="box-footer text-center">
						<a class="btn btn-default" href="{{$back_url}}">Back</a>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
@endsection