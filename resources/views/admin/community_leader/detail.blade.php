@extends('admin.template')
@section('main')

<div class="content-wrapper">
    <section class="content-header">
        <h4>View delivery order</h4>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url(LOGIN_USER_TYPE.'/dashboard') }}"><i class="fa fa-dashboard"></i> Home </a>
            </li>
            <li>
                <a href="{{ url(LOGIN_USER_TYPE.'/home_delivery') }}"> Orders </a>
            </li>
            <li class="active">
                Details
            </li>
        </ol>
    </section>

    <section class="content">

        <div class="box" >
            <div class="box-header with-border">
                <h4 class="box-title">Community Leader Data</h4>
            </div>
            <div class="row" style="padding: 10px;">
                <div class="col-md-2">
                    <pre>Name - {{$community_leader->first_name . ' ' . $community_leader->last_name}}</pre>
                </div>
                <div class="col-md-3">
                    <pre>Email - {{$community_leader->email}}</pre>
                </div>
                <div class="col-md-3">
                    <pre>Mobile Number - {{$community_leader->country_code . ' ' . $community_leader->mobile_number}}</pre>
                </div>
                <div class="col-md-4">
                    <pre>Address - {{$community_leader->address_line1 . ' ' . $community_leader->address_line2 . ' ' . $community_leader->city . ' ' . $community_leader->state}}</pre>
                </div>
                <div class="col-md-4">
                    <pre>Referral Code - {{$community_leader->referral_code}}</pre>
                </div>
                <div class="col-md-4">
                    <pre>Subscription Plan - {{$community_leader->plan_name}}</pre>
                </div>
                <div class="col-md-4">
                    <pre>Status - {{$community_leader->status}}</pre>
                </div>
            </div>
        </div>

        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">Last 10 Referred Merchants Data</h4>
            </div>
            <div class="box-body">
                <?php 
                    if (sizeof($merchants) == 0) 
                        echo 'No referred merchants'; 
                    else { ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Trading Name</th>
                                    <th>Location</th>
                                    <th>Member since</th>
                                </tr>
                            </thead>
                                <?php
                                    foreach ($merchants as $merchant) {
                                ?>
                                <tr>
                                    <td>{{$merchant->name}}</td>
                                    <td>{{$merchant->address_line1 . ' ' . $merchant->address_line2 . ' ' . $merchant->city . ' ' . $merchant->state}}</td>
                                    <td>{{$merchant->created_at}}</td>
                                </tr>
                                <?php } ?>
                            <tbody>
                            </tbody>
                        </table>
                <?php } ?>
            </div>
        </div>

        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">Last 10 Referred Drivers Data</h4>
            </div>
            <div class="box-body">
                <?php 
                    if (sizeof($drivers) == 0) 
                        echo 'No referred drivers'; 
                    else { ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Member since</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                                <?php
                                    foreach ($drivers as $driver) {
                                ?>
                                <tr>
                                    <td>{{$driver->first_name . ' ' . $driver->last_name}}</td>
                                    <td>{{$driver->address_line1 . ' ' . $driver->address_line2 . ' ' . $driver->city . ' ' . $driver->state}}</td>
                                    <td>{{$driver->created_at}}</td>
                                    <td>{{$driver->status}}</td>
                                </tr>
                                <?php } ?>
                            <tbody>
                            </tbody>
                        </table>
                <?php } ?>
            </div>
        </div>

        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title">Last 10 Deliveries Data</h4>
            </div>
            <div class="box-body">
                <?php 
                    if (sizeof($deliveries) == 0) 
                        echo 'No deliveries yet'; 
                    else { ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Pick Location</th>
                                    <th>Drop Location</th>
                                    <th>Driver Name</th>
                                    <th>Total(fee)</th>
                                </tr>
                            </thead>
                                <?php
                                    foreach ($deliveries as $delivery) {
                                ?>
                                <tr>
                                    <td>{{$delivery->pickup_location}}</td>
                                    <td>{{$delivery->drop_location}}</td>
                                    <td>{{$delivery->first_name . ' ' . $delivery->last_name}}</td>
                                    <td>{{$delivery->fee}}</td>
                                </tr>
                                <?php } ?>
                            <tbody>
                            </tbody>
                        </table>
                <?php } ?>
            </div>
        </div>

    </section>
</div>

@endsection
