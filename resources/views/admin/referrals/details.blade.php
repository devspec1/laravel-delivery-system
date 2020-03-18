@extends('admin.template')
@section('main')
<div class="content-wrapper">
	<section class="content-header">
		<h1> View Referral Details </h1>
		<ol class="breadcrumb">
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home </a>
			</li>
			<li>
				<a href="{{ url(LOGIN_USER_TYPE.'/referrals/'.$user_type) }}"> Referral </a>
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
						<h3 class="box-title">Referral Details</h3>
					</div>
					<div class="box-body">
						<table class="table table-striped" id="payout_methods">
							<thead>
								<tr class="text-truncate">
									<th> Referral name </th>
									<th> Trips </th>
									<th> Remaining Trips </th>
									<th> Remaining Days </th>
									<th> Amount </th>
									<th> Status </th>
								</tr>
							</thead>
							<tbody>
								@foreach($referral_details as $referral)
								<tr class="text-truncate">
									<td> {{ $referral->referral_user->full_name }} </td>
									<td> {{ $referral->trips }} </td>
									<td> {{ $referral->remaining_trips }} </td>
									<td> {{ $referral->remaining_days }} </td>
									<td> {{ html_string($referral->currency_symbol) }} {{ $referral->amount }} </td>
									<td> {{ $referral->payment_status }} </td>
								</tr>
								@endforeach
							</tbody>
						</table>
					
						<div class="box-footer text-center">
							<a class="btn btn-default" href="{{ url(LOGIN_USER_TYPE.'/referrals/'.$user_type) }}">Back</a>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
	@endsection