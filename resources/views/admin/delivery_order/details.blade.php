@extends('admin.template')
@section('main')
<div class="content-wrapper">
		<section class="content-header">
			<h1> View Merchant Orders </h1>
			<ol class="breadcrumb">
				<li>
					<a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home </a>
				</li>
				<li>
					<a href="{{ url(LOGIN_USER_TYPE.'/merchant_orders') }}"> Merchant Orders </a>
				</li>
				<li class="active">
					Details
				</li>
			</ol>
		</section>
	<section class="content">
		<div class="row">
			<div class="col-md-8 col-sm-offset-2" style="margin-left: 0px; width:100%;">
				<div class="box" style="overflow: scroll;">
					<div class="box-header with-border">
						<h3 class="box-title">{{ $merchant_name }} Orders</h3>
					</div>
					<div class="box-body">
						<table class="table dataTable no-foote" id="merchant_orders">
							<thead>
								<tr class="text-truncate">
									<th> Id </th>
									<th> Status </th>
									<th> Driver Id </th>
									<th> Estimate Time </th>
                                    <th> Fee </th>
                                    <th> Distance </th>
                                    <th> Order Description </th>
                                    <th> Pick Up </th>
                                    <th> Drop Off </th>
                                    <th> Customer </th>
                                    <th> Customer Phone </th>
								</tr>
							</thead>
							<tbody>
								@foreach($merchant_orders as $order)
								<tr class="text-truncate">
									<td> {{ $order->id }} </td>
									<td> {{ $order->status }} </td>
									<td> {{ $order->driver_id }} </td>
									<td> {{ $order->estimate_time }} </td>
                                    <td> {{ $order->fee }} </td>
                                    <th> {{ $order->distance }} </th>
                                    <td> {{ $order->order_description }} </td>
                                    <td> {{ $order->pick_up_location }} </td>
                                    <td> {{ $order->drop_off_location }} </td>
                                    <td> {{ $order->customer_name }} </td>
                                    <td> {{ $order->mobile_number }} </td>
								</tr>
								@endforeach
							</tbody>
						</table>
					
						<div class="box-footer text-center">
							<a class="btn btn-default" href="{{ url(LOGIN_USER_TYPE.'/merchants') }}">Back</a>
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
	@endsection