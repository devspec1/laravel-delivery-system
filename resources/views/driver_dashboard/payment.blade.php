@extends('template_driver_dashboard')
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content driver_payment" style="padding:0px;" ng-controller="payment" ng-cloak>
    <div class="page-lead separated--bottom  text--center text--uppercase">
        <h1 class="flush-h1 flush"> @lang('messages.header.earnings.pay_statements') </h1>
    </div>
    <div style="padding:0px 15px;">
        <div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="    padding: 20px 20px 10px;border-bottom:0px !important;">
            <div class="pull-left">
                <small> @lang('messages.driver_dashboard.total_earnings') </small>
                <label class="pull-left full-width" style="    font-weight: normal;
                    font-size: 40px;">
                    {{ html_string(session('symbol')) }} {{$total_earnings}}
                </label>
            </div>
            <div class="pull-right pull-left-sm">
                <small class="pull-right pull-left-sm" style="padding-bottom:10px;">{{trans('messages.driver_dashboard.pay_period')}}</small>
                <select class="pay-period" id="pay_period" ng-init="pay_period='all'" ng-model="pay_period" ng-change="getPayment(undefined, 'pay_period')">
                    <option value="current"> <i class="icon-chevron-right"></i> @lang('messages.driver_dashboard.current_statement') </option>
                    <option value="{{ date('Y-m-d', strtotime('-7 days')) }}/{{ date('Y-m-d', strtotime('0 month'))}}">{{ date('M d', strtotime('-7 days')) }} - {{ date('M d') }}</option>
                    <option value="{{ date('Y-m-d', strtotime('-14 days')) }}/{{ date('Y-m-d', strtotime('-7 days')) }}">{{ date('M d', strtotime('-14 days')) }} - {{ date('M d', strtotime('-7 days')) }}</option>
                    <option value="{{ date('Y-m-d', strtotime('-21 days')) }}/{{ date('Y-m-d', strtotime('-14 days')) }}">{{ date('M d', strtotime('-21 days')) }} - {{ date('M d', strtotime('-14 days')) }}</option>
                    <option value="{{ date('Y-m-d', strtotime('-28 days')) }}/{{ date('Y-m-d', strtotime('-21 days')) }}">{{ date('M d', strtotime('-28 days')) }} - {{ date('M d', strtotime('-21 days')) }}</option>
                    <option value="{{ date('Y-m-d', strtotime('-35 days')) }}/{{ date('Y-m-d', strtotime('-28 days')) }}">{{ date('M d', strtotime('-35 days')) }} - {{ date('M d', strtotime('-28 days')) }}</option>
                    <option value="all"> @lang('messages.driver_dashboard.all_statement') </option>
                </select>
            </div>
        </div>
        <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12 pay_period_details" style="    padding: 15px 0px 15px;" >
            <div class="col-lg-4 col-md-3 col-sm-3 col-xs-6 bor-left">
                <label style="padding:6px 0px;" ng-init="completed_trips={{$completed_trips}}" ng-model="completed_trips"> {{ $completed_trips }} </label>
                <small style="padding:6px 0px;"> @lang('messages.driver_dashboard.completed_trips') </small>
            </div>
            <div class="col-lg-4 col-md-3 col-sm-3 col-xs-6 bor-left">
                <label style="padding:6px 0px;" ng-init="acceptance_rate='{{$acceptance_rate}}';" ng-model="acceptance_rate"> {{ $acceptance_rate }} </label>
                <small style="padding:6px 0px;"> @lang('messages.driver_dashboard.acceptance_rate') </small>
            </div>
            <div class="col-lg-4 col-md-3 col-sm-3 col-xs-6 bor-left" style="border-right:0px !important;">
                <label style="padding:6px 0px;" ng-init="cancelled_trips={{$cancelled_trips}}" ng-model="cancelled_trips"> {{ $cancelled_trips }} </label>
                <small style="padding:6px 0px;"> @lang('messages.driver_dashboard.cancelled_trips') </small>
            </div>
        </div>
        <h2 class="flush-h2 pull-left" style="padding: 0px 20px;
        "> @lang('messages.driver_dashboard.daily_earnings') </h2>
        <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12 " style="padding: 20px;">
            <div class="pull-left">
                <input type="text" id="begin_trip" readonly="readonly" class="checkin ui-datepicker-target" autocomplete="off" name="date" placeholder="{{trans('messages.user.begin_trip')}}"ng-model="begin_trip">
                <input type="text" placeholder="{{trans('messages.user.end_trip')}}" readonly="readonly" class="checkin ui-datepicker-target" autocomplete="off" id="end_trip" name="date" ng-model="end_trip">
            </div>
            <div class="pull-right driver-status">
                @lang('messages.driver_dashboard.status')
                <select style="font-size: 12px;padding: 9px 6px;margin: 0px 10px;" ng-model="earning_period" ng-init="earning_period='all_trips'" id='earning_period' ng-change="getPayment()">
                    <option value="all_trips"> @lang('messages.driver_dashboard.all_trips') </option>
                    <option value="completed_trips"> @lang('messages.driver_dashboard.completed_trips') </option>
                    <option value="cancelled_trips"> @lang('messages.driver_dashboard.cancelled_trips') </option>
                </select>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 guest-drive-payment">
            <div class="pull-left full-width border-full earning_period_details" style="margin-bottom:20px;">
                <table style="font-size: 14px !important;" class="col-sm-12 table-bordered table-striped table-condensed cf" ng-init="all_trips={{ $all_trips }};currentPage=all_trips.current_page;totalPages=all_trips.last_page">
                    <tbody>
                        <tr>
                            <th>
                                @lang('messages.driver_dashboard.pickup_time')
                            </th>
                            <th>
                                @lang('messages.driver_dashboard.vehicle')
                            </th>
                            <th>
                                @lang('messages.driver_dashboard.duration')
                            </th>
                            <th>
                                @lang('messages.driver_dashboard.distance')
                            </th>
                            <th>
                                @lang('messages.driver_dashboard.total_earnings')
                            </th>
                        </tr>
                    </tbody>
                    <tr ng-repeat="trips in all_trips.data">
                        <td>
                            @{{ trips.begin_date }} @{{ trips.pickup_time_formatted }}
                        </td>
                        <td>
                            @{{ trips.vehicle_name }}
                        </td>
                        <td>
                            @{{ trips.trip_time }}
                        </td>
                        <td>
                            @{{ trips.total_km }}
                        </td>
                        <td>
                            <span ng-bind-html="trips.currency.original_symbol">
                            </span>
                            @{{ trips.company_driver_earnings }}
                        </td>
                    </tr>
                    <tr >
                        <td ng-show="all_trips.data.length==0" colspan="7" style="height: 46px;text-align: center;">
                            @lang('messages.dashboard.no_details').
                        </td>
                    </tr>
                </table>
            </div>
            <div style="padding:25px;">
                <div class="pagination-buttons-container row-space-8 float--right" ng-cloak>
                    <div class="results_count pagination inline-group btn-group btn-group--bordered" style="float: right;margin-top: 20px;">
                        <div class="inline-group__item" ng-show ="all_trips.data.length>1">
                            <posts-pagination>
                            </posts-pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
@endsection
@push('scripts')
    <style type="text/css">
        .btn-input:hover, .btn:hover, .file-input:hover, .tooltip:hover, .btn, .btn-input, .file-input, .tooltip {
            background: transparent !important;
            border: none !important;
        }
        .btn--link .icon_right-arrow{left: 0px !important;}
        .btn-switch {
            background: #fff;
            border: 1px solid green !important;
        }
        .btn-switch::before{
            background: green !important;
        }
        @media (max-width: 400px){
            #btn-pad.btn.btn--primary.btn-blue{
                font-size: 11px !important;
                padding:0px 20px !important;
            }
        }
    </style>
@endpush