<title>Invoice</title>
@extends('template_without_header_footer')
@section('main')
<body onload="window.print()" onfocus="window.close()">
    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding-top: 10px;">
        <div class="page-lead separated--bottom  text--center text--uppercase pull-left" style="margin-bottom: 0px !important;padding-bottom: 5px !important;">
            <h1 class="flush-h1 flush">
            @lang('messages.dashboard.trip_invoice')
            </h1>
            <small style="text-transform: none;text-align: left;float: left;padding: 20px 20px 0px;">
            @lang('messages.dashboard.dwnld_invoice') {{ $site_name}} @lang('messages.dashboard.feedback')
            </small>
        </div>
        <div id="no-more-tables" style="overflow: visible;">
            <table class="col-sm-12 table-bordered table-striped table-condensed cf">
                <thead class="cf">
                    <tr>
                        <th>{{trans('messages.dashboard.invoice_no')}}</th>
                        <th >{{trans('messages.dashboard.trip_date')}}</th>
                        <th>{{trans('messages.dashboard.invoice')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="trip-expand__origin collapsed" >
                        <td data-title="Invoice Number">{{ $trip->id }}</td>
                        <td data-title="Trip date">{{ date('F d, Y',strtotime($trip->created_at))}}</td>
                        <td data-title="Invoice">{{ $trip->currency->original_symbol }} {{ $trip->total_invoice }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" >
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <img src="{{ $trip->rider_profile_picture }}" class='img--circle img--bordered img--shadow driver-avatar' >
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                <p>{{trans('messages.dashboard.invoice_issued')}}{{$site_name}}{{trans('messages.dashboard.behalf')}}</p>
                <p>{{ $trip->rider_name }}</p>
                <div class="text--left">
                    <div class="trip-address grid grid--full soft-double--bottom">
                        
                        <div class="grid__item one-tenth" style="margin:6px 0px;">
                            <div class="icon icon_route-dot color--positive"></div>
                        </div>
                        <div class="grid__item nine-tenths">
                            <p class="flush">{{ $trip->pickup_time }}</p>
                            <h6 class="color--neutral flush">{{ $trip->pickup_location }}</h6>
                        </div>
                    </div>
                    <div class="trip-address grid grid--full">
                        <div class="grid__item one-tenth" style="margin:6px 0px;">
                            <div class="icon icon_route-dot color--negative"></div>
                        </div>
                        <div class="grid__item nine-tenths">
                            <p class="flush">{{ $trip->drop_time }}</p>
                            <h6 class="color--neutral flush">{{ $trip->drop_location }}</h6>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <div id="no-more-tables" class="table-no-border" style="overflow: visible;">
            <table class="col-sm-12 table-bordered table-striped table-condensed cf">
                <thead class="cf">
                    <tr>
                        <th>{{trans('messages.dashboard.date')}}</th>
                        <th>{{trans('messages.dashboard.desc')}}</th>
                        <th>{{trans('messages.dashboard.net_amt')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice_data as $key => $invoice)
                    <tr class="trip-expand__origin collapsed text-{{ $invoice['colour'] }}">
                        <td> {{ ($key == 0) ? date('F d, Y') : ''}} </td>
                        <td class="text--left "> {{ $invoice['key'] }} </td>
                        <td class="text--right "> {{ $invoice['value'] }} </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
@endsection