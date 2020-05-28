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
                <h4 class="box-title">Customer data</h4>
            </div>
            <div class="row" style="padding: 10px;">

                <div class="col-md-3">
                    <pre>{{$customer->country}}</pre>
                </div>
                <div class="col-md-2">
                    <pre> + {{$customer->country_code}}</pre>
                </div>
                <div class="col-md-3">
                    <pre>{{$customer->mobile_number}}</pre>
                </div>
                <div class="col-md-4">
                    <pre>{{$customer->first_name}} {{$customer->last_name}}</pre>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-sm-offset-2" style="margin-left: 0px; width:100%;">
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title"> Order data</h4>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-4 col-md-4" style="padding-top: 20px;">
                                <pre> Order_ID : {{$order_result->id}}</pre>
                                <pre> Created_At :{{$order_result->created_at}}</pre>
                                <pre> Delivery_Status : {{$order_result->status}}</pre>
                                <pre>Estimate_Time : {{$order_result->estimate_time}}</pre>
                                <pre>Time to Deliver : {{$order_result->delivery_time}}</pre>
                                <pre>Merchant_Name : {{ $order_result->merchant_name }}</pre>
                                <pre>Fee : {{ $order_result->fee }}</pre>
                                <pre>Order_Description : {{ $order_result->order_description }}</pre>
                            </div>

                            <div class="col-sm-8 col-md-8">

                                <div class="clearfix" ng-controller='delivery_order'>
                                    <div class="location-form">
                                        <div class="row pick-location clearfix">
                                            <div class="col-md-12" ng-init='pick_up_latitude = ""'>
                                                {!! Form::hidden('pick_up_latitude', @$location_result->pickup_latitude, ['id' => 'pick_up_latitude']) !!}
                                                {!! Form::hidden('pick_up_longitude', @$location_result->pickup_longitude, ['id' => 'pick_up_longitude']) !!}
                                                {!! Form::hidden('pick_up_location', @$location_result->pickup_location, ['class' => 'form-control change_field', 'id' => 'input_pick_up_location', 'placeholder' => 'Pick Up Location', 'autocomplete' => 'off']) !!}
                                            </div>
                                        </div>
                                        <div class="row pick-location clearfix">
                                            <div class="col-md-12">
                                                {!! Form::hidden('drop_off_latitude', @$location_result->drop_latitude, ['id' => 'drop_off_latitude']) !!}
                                                {!! Form::hidden('drop_off_longitude', @$location_result->drop_longitude, ['id' => 'drop_off_longitude']) !!}
                                                {!! Form::hidden('drop_off_location', @$location_result->drop_location, ['class' => 'form-control change_field', 'id' => 'input_drop_off_location', 'placeholder' => 'Drop Off Location', 'autocomplete' => 'off']) !!}
                                            </div>
                                        </div>
                                    </div>

                                        <div class="map-route-option">
                                            <div class="map-view clearfix">
                                                <div id="map"></div>
                                            </div>
                                        </div>

                                </div>
                            </div>
                        </div>

                        <div class="box-footer text-center">
                            <a class="btn btn-default" href="{{ url(LOGIN_USER_TYPE.'/home_delivery') }}">Back</a>
                        </div>
                    </div>
                </div>
                <div class="box">
                    <div class="box-header with-border">
                        <h4 class="box-title"> Location data</h4>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-sm-6 col-md-6">
                                <pre> Pickup_Location : {{$location_result->pickup_location}}</pre>
                                <pre> Pickup_Latitude :{{$location_result->pickup_latitude}}</pre>
                                <pre> Pickup_Longitude : {{$location_result->pickup_longitude}}</pre>
                                <pre>Drop_location : {{$location_result->drop_location}}</pre>
                                <pre>Drop_Latitude  : {{$location_result->drop_latitude}}</pre>
                                <pre>Drop_Longitude : {{ $location_result->drop_longitude }}</pre>
                                <pre>Distance : {{ $location_result->distance}} km</pre>
                            </div>

                            <div class="col-sm-6 col-md-6">
                                <pre>Real Drop_location : {{$real_location_result['real_drop_location']}}</pre>
                                <pre>Real Drop_Latitude  : {{$real_location_result['real_drop_latitude']}}</pre>
                                <pre>Real Drop_Longitude : {{$real_location_result['real_drop_longitude']}}</pre>
                            </div>
                        </div>

                        <div class="box-footer text-center">
                            <a class="btn btn-default" href="{{ url(LOGIN_USER_TYPE.'/home_delivery') }}">Back</a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>
</div>

@endsection
