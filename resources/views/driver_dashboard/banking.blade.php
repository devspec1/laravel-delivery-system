<title>Banking</title>
@extends('template_driver_dashboard') 

@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px;"><div class="page-lead separated--bottom  text--center text--uppercase"><h1 class="flush-h1 flush">{{trans('messages.driver_dashboard.banking_details')}}</h1>
</div>
<div class="" style="padding:0px 15px;">

<div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom:0px !important;">
<p style="color:#bc1218;"><i class="icon icon_alert" style="padding-right:5px;"></i> {{trans('messages.driver_dashboard.payment_details')}}</p>
<p style="color:#000;margin:0px;"><i class="icon icon_circle-check" style="padding-right:5px;"></i>{{trans('messages.driver_dashboard.weekly_payout')}}</p>
</div>

<div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="    padding: 25px 0px 15px;">
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:25px;">
<label class="" style="padding:6px 0px;">{{trans('messages.driver_dashboard.ifsc')}} </label>
<div class="" style="padding:6px 0px;">
{{trans('messages.driver_dashboard.ifsc_code')}}
</div>
</div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:25px;">
<label class="" style="padding:6px 0px;">{{trans('messages.driver_dashboard.bank')}} </label>
<div class="" style="padding:6px 0px;">
{{trans('messages.driver_dashboard.bank_name')}}
</div>
</div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:25px;">
<label class="" style="padding:6px 0px;"> {{trans('messages.driver_dashboard.account')}}</label>
<div class="" style="padding:6px 0px;">
{{trans('messages.driver_dashboard.account_no')}}
</div>
</div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:25px;">
<label class="" style="padding:6px 0px;"> {{trans('messages.driver_dashboard.account_name')}}</label>
<div class="" style="padding:6px 0px;">
Test
</div>
</div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:25px;">
<label class="" style="padding:6px 0px;">{{trans('messages.driver_dashboard.driver_address')}}</label>
<div class="" style="padding:6px 0px;">
{{trans('messages.driver_dashboard.address')}}
</div>
</div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:25px;">
<label class="" style="padding:6px 0px;">{{trans('messages.driver_dashboard.driver_city')}}</label>
<div class="" style="padding:6px 0px;">
{{trans('messages.driver_dashboard.city')}}
</div>
</div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:25px;">
<label class="" style="padding:6px 0px;">{{trans('messages.driver_dashboard.driver_postal_code')}}</label>
<div class="" style="padding:6px 0px;">
{{trans('messages.driver_dashboard.postal_code')}}
</div>
</div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style="margin-bottom:25px;">
<label class="" style="padding:6px 0px;color:#bc1218;">{{trans('messages.driver_dashboard.driver_dob')}}</label>
<div class="" style="padding:6px 0px;color:#bc1218;" >
{{trans('messages.driver_dashboard.dob')}}<br>
{{trans('messages.driver_dashboard.under_req_age')}}
</div>
</div>
</div>


</div>
</div>
</div>
</div>
</div>
</main>
@stop