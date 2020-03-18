@extends('templatesign')

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
<div class="slide-img slide-img-drive"></div>

<div class="pattern" style="height: 100%; width: 205px; left: 0px; position: absolute; z-index: 10;"><div style=" height: 100%; overflow: hidden;"><div aria-label="Decorative pattern" style="height: 100%;"><div class="isvg loaded" style="height: 100%;">
<img src="{{url('images/icon/patten_274_520.jpg')}}">
</div>
</div>
</div>
</div>
@if(Auth::user()==null)
<div class="mini-green ride-mini-green" >
    <div href="#" class="_style_4jQAPw green-mini-div" style="width: 206px; padding: 32px 20px 20px 32px; display: block; position: relative; height: 206px; background-color: rgb(55, 112, 55);">
    <div class="_style_1PPmFR" style="font-weight: 500; color: rgb(255, 255, 255);font-size: 21px; line-height: 1.4;">{{trans('messages.drive.start_drive_with')}} {{$site_name}}</div>
<a class="btn btn--primary btn--arrow position--relative error-retry-btn" href="{{ url('signup_driver') }}" style= "background: transparent !important;     border: none !important;    float: right;margin-top: 55px;    margin-right: -16px;">
<div class="block-context soft-small--right">{{trans('messages.home.siginup')}}</div>
<i class="icon_right-arrow-thin icon transition delta position--absolute"></i>
</a>
    </div>
    </div>
@endif
</div>
</div>
<div class="container-fluid pad-sm-20 drive-pad-sm drivesafety" style="padding:0px;background:#f8f8f9 !important;">
<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12 ride-always drive-always pattern-content drivearrive">
<div class="">
<h1 class="slide-head ride-head">{{trans('messages.drive.works_first')}}</h1><p class="ride-content slide-content">
{{trans('messages.drive.drive_you_need')}}</p>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mar-height ride-three" style="padding-right: 20px; padding-left: 0px;">
<img src="images/icon/cont12.png" class="cont-img">
<div class="arrive-content">
<div style="position: relative !important;">
<p class="_style_ZJW1y" style="margin: 10px 0px 15px !important; min-height: unset;">{{trans('messages.drive.own_schedule')}}</p>
</div><div><p class="cmln__paragraph">{{trans('messages.drive.drive_with')}} {{$site_name}} {{trans('messages.drive.anytime')}} </p>
</div></div>
</div>
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mar-height ride-three" style="padding-right: 20px; padding-left: 0px;">
<img src="images/icon/cont13.png" class="cont-img">
<div class="arrive-content">
<div style="position: relative !important;">
<p class="_style_ZJW1y" style="margin: 10px 0px 15px !important; min-height: unset;">{{trans('messages.drive.every_turn')}}</p>
</div><div><p class="cmln__paragraph">{{trans('messages.drive.fare_start')}}</p>
</div></div>
</div>
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 mar-height ride-three" style="padding-right: 20px; padding-left: 0px;">
<img src="images/icon/cont11.png" class="cont-img">
<div class="arrive-content">
<div style="position: relative !important;">
<p class="_style_ZJW1y" style="margin: 10px 0px 15px !important; min-height: unset;">{{trans('messages.drive.app_lead')}}</p>
</div><div><p class="cmln__paragraph">{{trans('messages.drive.tap_go')}}</p>
</div></div>
</div>
</div>
</div>
</div>
</div>
<div class="container-fluid pad-sm-20 drive-pad-sm" style="padding:0px;background:#f8f8f9 !important;">
<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12  pattern-content goferpage">
<div class="">
<p class="_style_ZJW1y" data-reactid="287">{{trans('messages.drive.hit_road')}}</p>
<p class="slide-content" style="    margin-bottom: 30px !important;">
{{trans('messages.drive.easy_started')}}</p>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:0px;">
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12  ride-three drive-number-link" style="padding-right: 20px; padding-left: 0px;">
<div class="circle-width">
<div class="number-circle-after">1</div>
</div>
<div class="arrive-content" id="drive-circle-content">
<div style="position: relative !important;">
<p class="_style_ZJW1y" style="margin: 10px 0px 15px !important; min-height: unset;">{{trans('messages.drive.sign_online')}}</p>
</div><div><p class="cmln__paragraph">{{trans('messages.drive.about_yourself')}}</p>
</div></div>
</div>
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12  ride-three drive-number-link" style="padding-right: 20px; padding-left: 0px;">
<div class="circle-width">
<div class="number-circle-after">2</div>
</div>
<div class="arrive-content" id="drive-circle-content">
<div style="position: relative !important;">
<p class="_style_ZJW1y" style="margin: 10px 0px 15px !important; min-height: unset;">{{trans('messages.drive.share_doc')}}</p>
</div><div><p class="cmln__paragraph">{{trans('messages.drive.upload_license')}}</p>
</div></div>
</div>
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12  ride-three " style="padding-right: 20px; padding-left: 0px;">
<div class="circle-width">
<div class="number-circle-after">3</div>
</div>
<div class="arrive-content" id="drive-circle-content">
<div style="position: relative !important;">
<p class="_style_ZJW1y" style="margin: 10px 0px 15px !important; min-height: unset;">{{trans('messages.drive.get_app')}}</p>
</div><div><p class="cmln__paragraph">{{trans('messages.drive.approve_drive')}} {{$site_name}} {{trans('messages.drive.provide_you_need')}}</p>
</div></div>
</div>
@if(Auth::user()==null)
<a class=" btn btn--primary btn--arrow position--relative error-retry-btn" href="{{ url('signup_driver') }}">
<div class="block-context soft-small--right" style="    width: 180px;    font-size: 13px !important;">{{trans('messages.drive.signup_now')}}</div>
<i class="icon_right-arrow-thin icon transition delta position--absolute"></i>
</a>
@endif
</div>
</div>
</div>
</div>
<div class="container-fluid" style="background:#fff !important;padding:0px;">
<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 height-safety trip-height pull-right" style="padding:0px;">
<div class="slide-img safety-img safety-drive-img"></div>
</div>
<div class="col-lg-7 col-md-7 col-sm-12 col-xs-12 pattern-content goferpage" style=" padding-right: 90px;">
<p class="_style_ZJW1y" style="margin: 10px 0px 0px !important; min-height: unset;">{{trans('messages.drive.about_app')}}</p>
<p class="slide-content trip-content" style="margin-bottom:10px !important;">{{trans('messages.drive.design_drivers')}}</p>
<p class="cmln__paragraph">{{trans('messages.drive.make_money')}}</p>
</div>
</div>
<div class="container-fluid drive_seat" style="background:#f8f8f9 !important;padding:0px;">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12  pattern-content drive-arrive design-ride">
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mar-height ride-three ridehome" style="padding-right: 20px; padding-left: 20px;">
<img src="images/icon/cont14.png" class="cont-img">
<div class="arrive-content">
<div style="position: relative !important;">
<p class="_style_ZJW1y" style="margin: 10px 0px 15px !important; min-height: unset;">{{trans('messages.header.require')}}</p>
</div><div><p class="cmln__paragraph">{{trans('messages.drive.safety_screen')}}</p>
</div></div>
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mar-height ride-three ridehome" style="padding-right: 20px; padding-left: 20px;">
<img src="images/icon/cont15.png" class="cont-img">
<div class="arrive-content">
<div style="position: relative !important;">
<p class="_style_ZJW1y" style="margin: 10px 0px 15px !important; min-height: unset;">{{trans('messages.drive.rewards')}}</p>
</div><div><p class="cmln__paragraph">{{trans('messages.drive.drive_seat')}}</p>
</div></div>
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mar-height ride-three ridehome" style="padding-right: 20px; padding-left: 20px;">
<img src="images/icon/cont16.png" class="cont-img">
<div class="arrive-content">
<div style="position: relative !important;">
<p class="_style_ZJW1y" style="margin: 10px 0px 15px !important; min-height: unset;">{{trans('messages.drive.vehicle_solution')}}</p>
</div><div><p class="cmln__paragraph">{{trans('messages.drive.need_car')}}</p>
</div></div>
</div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mar-height ride-three ridehome" style="padding-right: 20px; padding-left: 20px;">
<img src="images/icon/half_plane.png" class="cont-img">
<div class="arrive-content">
<div style="position: relative !important;">
<p class="_style_ZJW1y" style="margin: 10px 0px 15px !important; min-height: unset;">{{trans('messages.footer.safety')}}</p>
</div><div><p class="cmln__paragraph">{{trans('messages.drive.you_with')}}  {{$site_name}},{{trans('messages.drive.support')}} </p>
</div></div>
</div>

</div>
</div>
 

<div class="container-fluid" style="background:#f8f8f9;">
<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12 pattern-content drive-circle-content">
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12  column-content bor-bot-sm" id="pad-sm-zero" style="padding:0px 120px 0px 0px;">

  <div class="arrive-content">
<div style="position: relative !important;"><p class="slide-content trip-content how-column-content" style="margin-bottom:10px !important;    font-weight: 400 !important; ">{{trans('messages.drive.making_money')}}</p>
</div><div><p class="cmln__paragraph">{{trans('messages.drive.ready_money')}}</p>
</div>
@if(Auth::user()==null)
<a class="btn btn--primary btn--arrow position--relative error-retry-btn" href="{{ url('signup_rider') }}">
<div class="block-context soft-small--right" style="    width: 180px;    font-size: 13px !important;">{{trans('messages.footer.siginup_ride')}}</div>
<i class="icon_right-arrow-thin icon transition delta position--absolute"></i>
</a>
@endif
</div>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12  column-content" id="pad-sm-zero" style="padding:0px 120px 0px 0px;">

  <div class="arrive-content">
<div style="position: relative !important;"><p class="slide-content trip-content how-column-content" style="margin-bottom:10px !important;    font-weight: 400 !important;">{{trans('messages.drive.support')}}</p>
</div><div><p class="cmln__paragraph">{{trans('messages.drive.we_want')}} {{$site_name}} {{trans('messages.drive.hassle_free')}}</p>
</div></div>
  </div>
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
.bx-wrapper .bx-viewport{background: #f1f1f1 !important;}
.btn-input:hover, .btn:hover, .file-input:hover, .tooltip:hover , .btn, .btn-input, .file-input, .tooltip{background:transparent !important; border: none !important}
.btn.btn--primary:hover{background:transparent !important; color: #fff !important;}
</style>