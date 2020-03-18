@extends('template')

@section('main')
<div class="container-fluid fixed-header" style="background:rgb(248, 248, 249);z-index:99;">
<ul class="sub-header">
<li><a href="{{ url('requirements') }}">{{trans('messages.header.require')}}</a></li>
<li><a href="{{ url('driver_app') }}">{{trans('messages.header.app')}}</a></li>
<li><a href="{{ url('drive_safety') }}">{{trans('messages.footer.safety')}}</a></li>
</ul>
</div>
<div class="container-fluid ride-div-main drive-div-main" style="padding:0px !important;">
<div style="position:relative;float:left;width:100%;">
<div class="slide-img slide-img-drive-safety"></div>

<div class="pattern" style="height: 100%; width: 205px; left: 0px; position: absolute; z-index: 10;"><div style="background-color: #A6DAEC; height: 100%; overflow: hidden;"><div aria-label="Decorative pattern" style="height: 100%;"><div class="isvg loaded" style="height: 100%;"><!-- <svg>
    <defs>
        <pattern id="a___288468778" width="60" height="60" patternUnits="userSpaceOnUse">
            <path class="pattern-stroke" d="M8.5 13L0 30l17-8.5c1.9-1 3.5-2.5 4.5-4.5L30 0 13 8.5c-2 1-3.5 2.5-4.5 4.5zM47 8.5L30 0l8.5 17c1 1.9 2.5 3.5 4.5 4.5L60 30l-8.5-17c-1-2-2.5-3.5-4.5-4.5zM51.5 47L60 30l-17 8.5c-1.9 1-3.5 2.5-4.5 4.5L30 60l17-8.5c2-1 3.5-2.5 4.5-4.5z"></path>
            <path class="pattern-stroke" d="M38.5 13L30 30l17-8.5c1.9-1 3.5-2.5 4.5-4.5L60 0 43 8.5c-2 1-3.5 2.5-4.5 4.5zm-17 34L30 30l-17 8.5c-2 1-3.5 2.5-4.5 4.5L0 60l17-8.5c2-1 3.5-2.5 4.5-4.5zM17 8.5L0 0l8.5 17c1 1.9 2.5 3.5 4.5 4.5L30 30l-8.5-17c-1-2-2.5-3.5-4.5-4.5z"></path>
            <path class="pattern-stroke" d="M17 38.5L0 30l8.5 17c1 1.9 2.5 3.5 4.5 4.5L30 60l-8.5-17c-1-2-2.5-3.5-4.5-4.5zm30 0L30 30l8.5 17c1 1.9 2.5 3.5 4.5 4.5L60 60l-8.5-17c-1-2-2.5-3.5-4.5-4.5z"></path>
        </pattern>
    </defs>
    <rect fill="url(#a___288468778)" height="100%" width="100%"></rect>
</svg> -->
<img src="{{url('images/icon/patten_274_520.jpg')}}">
</div>
</div>
</div>
</div>

</div>
</div>
<div class="container-fluid pad-sm-20 drive-pad-sm" style="padding:0px;background:#f8f8f9 !important;">
<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12 ride-always drive-always pattern-content">
<div class="">
<ul style="padding-left:0px !important;text-transform: uppercase; margin: 0px; list-style: none; font-size: 12px; padding-bottom: 24px;"><li style="margin-bottom: 0px; padding-bottom: 0px; font-weight: 600; display: inline-block;"><a href="{{ url('drive') }}" class="_style_1OaDaU" style="transition: all 400ms ease;">{{trans('messages.header.drive')}}</a></li>
<i class="icon icon_right-arrow faq-right push-tiny--left" style="padding: 12px;"></i>
<li style="margin-bottom: 0px; padding-bottom: 0px; font-weight: 600; display: inline-block; color: rgb(147, 147, 147);">{{trans('messages.header.safety')}}</li></ul>
<h1 class="slide-head ride-head">{{trans('messages.drive.safety_behind')}}</h1><p style="margin-bottom: 20px !important;" class="ride-content slide-content">
{{trans('messages.drive.our_commitment')}}</p>
<div class="col-lg-6 col-md-10 col-sm-10 col-xs-12" style="padding:0px !important;">
<p class="cmln__paragraph" style="font-size: 16px;">{{$site_name}} {{trans('messages.drive.keeping_safe')}}</p>
</div>
</div>
</div>
</div>
<div class="container-fluid" style="background:#f8f8f9 !important">
<div class="col-lg-10 col-md-12 col-sm-12 col-xs-12 pattern-content">
<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12 ">
<img src="images/icon/cont11.png">
</div>
<div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 pad-md">
<p class="_style_ZJW1y" style="margin: 10px 0px 0px !important; min-height: unset;">{{trans('messages.drive.before_trip')}}</p>
<p class="slide-content trip-content" style="margin-bottom:10px !important;">{{trans('messages.drive.picking_up')}}</p>
<p class="cmln__paragraph">
<b>{{trans('messages.drive.anonymous_pickup')}}</b><br>
{{trans('messages.drive.riders_account')}}</p>
<p class="cmln__paragraph">
<b>{{trans('messages.drive.sub_number')}}</b><br>
{{trans('messages.drive.location_around_world')}} {{$site_name}} {{trans('messages.drive.stays_private')}} </p>
</div>
</div>
</div>

<div class="container-fluid" style="background:#fff !important;padding:0px;">
<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 height-safety trip-height pull-right" style="padding:0px;">
<div class="slide-img safety-img safety-trip-img"></div>
</div>
<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 pattern-content" style=" padding-right: 90px;">
<p class="_style_ZJW1y" style="margin: 10px 0px 0px !important; min-height: unset;">{{trans('messages.drive.on_trip')}}</p>
<p class="slide-content trip-content" style="margin-bottom:10px !important;">{{trans('messages.drive.getting_destination')}}</p>
<p class="cmln__paragraph">
<b>{{trans('messages.drive.app_navigation')}}</b><br>
{{trans('messages.drive.riders_enter')}}</p>
<p class="cmln__paragraph">
<b>{{trans('messages.drive.on_the_map')}}</b><br>
{{trans('messages.drive.gps_data')}}</p>
</div>

</div>
<div class="container-fluid" style="background:#f8f8f9 !important">
<div class="col-lg-10 col-md-12 col-sm-12 col-xs-12 pattern-content">
<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12 ">
<img src="images/icon/cont6.jpg">
</div>
<div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 pad-md">
<p class="_style_ZJW1y" style="margin: 10px 0px 0px !important; min-height: unset;">{{trans('messages.drive.after_trip')}}</p>
<p class="slide-content trip-content" style="margin-bottom:10px !important;">{{trans('messages.drive.improving')}} {{$site_name}} {{trans('messages.drive.experience')}}</p>
<p class="cmln__paragraph">
<b>{{trans('messages.drive.no_change')}}</b><br>
{{trans('messages.drive.fares_auto')}}</p>
<p class="cmln__paragraph">
<b>{{trans('messages.drive.driver_feedback')}}</b><br>
{{trans('messages.drive.rate_your_driver')}} {{$site_name}}.</p>
</div>
</div>
</div>
<div class="container-fluid" style="padding:0px;background:#f8f8f9;">
  <div class="col-lg-11 col-lg-push-1 col-sm-push-1 col-md-12 col-sm-11 col-xs-12 pattern-ride" style="margin-top:0px !important;">
  <div class="pattern" id="pattern" style="display:block !important;height: 100% !important; left: 0px; position: absolute; z-index: 10;">
  <div style="background-color:#A6DAEC; height: 100%; overflow: hidden;"><div aria-label="Decorative pattern" style="height: 100%;"><div class="isvg loaded" style="height: 100%;">
<img src="{{url('images/icon/patten_90_238.jpg')}}">
</div>
</div>
</div>
</div>
<div class="col-lg-10 col-md-10 col-sm-12 col-xs-11 pad-ride-red">
<p class="ride-content slide-content col-lg-7 col-md-7 col-sm-6 col-xs-12">
{{trans('messages.drive.drive_safely')}} {{$site_name}}</p>
<a href="{{ url('signup_driver') }}" class="pull-right btn btn--primary btn--arrow position--relative error-retry-btn width-sm mar-top-37">
<div class="block-context soft-small--right" style="    width: 180px;    font-size: 13px !important;">{{trans('messages.drive.siginup_drive')}}</div>
<i class="icon_right-arrow-thin icon transition delta position--absolute"></i>
</a>
</div>
  </div>
  </div>
<div class="container-fluid" style="background:#f8f8f9 !important">
<div class="container pad-container-small" style="padding-top:40px !important;">
<small style="font-size:10.5px !important;">{{trans('messages.drive.information')}}</small>
</div>
</div>
</main>
@stop
<style type="text/css">
	.arrive-content{width: 100% !important; }
	.arrive-content ._style_ZJW1y {
    font-weight: 200 !important;
    color: #494949 !important;
    font-size: 26px !important;
    line-height: 30px;
    min-height: 60px;
}
#mySliderTabsContainer .btn--bit{padding:0px;
    height: auto !important;}
#mySliderTabsContainer .btn--bit i {
    background: #fff;
    padding: 11px;
}
.page-footer-back{
	background: #f8f8f9;
}
#mySliderTabsContainer .btn--bit i:hover {
    background: #e3e3e3;
   color: #000;
}
.drive-div-main .mini-green.ride-mini-green{
  left: 145px;
  right: auto !important;
  bottom: 60px;
}
.drive-arrive .arrive-content {
    width: 100% !important;
    padding-right: 160px;
}
.pos-abs-hide , .bx-wrapper .bx-pager{background: #f1f1f1 !important;}
.container.pad-container-small {
    padding-left: 70px !important;
}
.btn-input:hover, .btn:hover, .file-input:hover, .tooltip:hover , .btn, .btn-input, .file-input, .tooltip{background:transparent !important; border: none !important}
.btn.btn--primary:hover{background:transparent !important; color: #fff !important;}
.bx-wrapper .bx-viewport{background: #f1f1f1 !important;}
@media (min-width: 1100px){
  .slide-head.ride-head, .pattern-ride .slide-content.ride-content{line-height: 115px !important;}
}
</style>