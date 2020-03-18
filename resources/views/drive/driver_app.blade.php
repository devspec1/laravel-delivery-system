<script type="text/javascript">
    $(function() {
    $(".rslides").responsiveSlides();
    $("#slider1").responsiveSlides({
        auto: false,
        pager: true,
        nav: true,
        speed: 500,
        maxwidth: 800,
        namespace: "centered-btns"
      });
  });
</script>

@extends('template')

@section('main')
<div class="container-fluid fixed-header" style="background:rgb(248, 248, 249);z-index:99;">
<ul class="sub-header">
<li><a href="{{ url('requirements') }}">{{trans('messages.header.require')}}</a></li>
<li><a href="{{ url('driver_app') }}">{{trans('messages.header.app')}}</a></li>
<li><a href="{{ url('drive_safety') }}">{{trans('messages.footer.safety')}}</a></li>
</ul>
</div>
<div class="container-fluid ride-div-main" style="padding:0px !important;">
<div style="position:relative;float:left;width:100%;">
<div class="col-lg-4 col-md-5 col-sm-12 col-xs-12 md-pull-right height-safety drive-app-height" style="padding:0px !important;">
<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10" style="padding:0px;">
<div class="slide-img safety-img drive-app-img"></div>
</div>
<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="height:100%;">
<div class="pattern" style="height: 100% !important; width: 100% !important; right: 0px; position: absolute; z-index: 10;"><div style="background-color: #A6DAEC; height: 100%; overflow: hidden;"><div aria-label="Decorative pattern" style="height: 100%;"><div class="isvg loaded" style="height: 100%;">
<img src="{{url('images/icon/patten_145_665.jpg')}}">
</div>
</div>
</div>
</div>
</div>

</div>
<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12">
<div class="pattern-content">
<div class="push-small--bottom"></div>
<h1 class="slide-head ride-head">{{trans('messages.drive.drivers_first')}}</h1><p class="slide-content">
{{trans('messages.drive.exp_around')}}</p>
<p class="cmln__paragraph" style="font-size: 18px; line-height: 27px;">{{trans('messages.drive.control_with')}} {{$site_name}}. {{trans('messages.drive.app_for_drivers')}}</p>
</div>
</div>

</div>
</div>
<div class="container-fluid" style="background:#f8f8f9;">
<div class="container">
<div class="col-lg-9 col-md-12 col-sm-12 col-xs-12 mobile-slide-pad hide-md show-sm">
  <div id="wrapper" class="">
  <div class="rslides_container">
<ul class="rslides" id="slider1">
  <li>
  <div class="col-lg-5 col-md-4 col-sm-5 col-xs-12">
  <img src="images/mobile-small1.png" alt="">
  </div>
<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12  drive-app">
<div class="arrive-content" style="width:100% !important;">
<div style="position: relative !important;">
<p class="_style_ZJW1y ride-support how-column-content" style="
    margin-bottom: 25px !important;">{{trans('messages.drive.going_online')}}</p>
</div><p class="cmln__paragraph">{{$site_name}} {{trans('messages.drive.always_avail')}}</p>
</div>
</div>
  </li>
 <li>
  <div class="col-lg-5 col-md-4 col-sm-5 col-xs-12">
  <img src="images/mobile-small2.png" alt="">
  </div>
<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12  drive-app">
<div class="arrive-content" style="width:100% !important;">
<div style="position: relative !important;">
<p class="_style_ZJW1y ride-support how-column-content" style="
    margin-bottom: 25px !important;">{{trans('messages.drive.accept_trip_request')}}</p>
</div><p class="cmln__paragraph">{{trans('messages.drive.receive_trip_request')}}</p>
</div>
</div>
  </li>
  <li>
  <div class="col-lg-5 col-md-4 col-sm-5 col-xs-12">
  <img src="images/mobile-small3.png" alt="">
  </div>
<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12  drive-app">
<div class="arrive-content" style="width:100% !important;">
<div style="position: relative !important;">
<p class="_style_ZJW1y ride-support how-column-content" style="
    margin-bottom: 25px !important;">{{trans('messages.drive.turn_by_turn')}}</p>
</div><p class="cmln__paragraph">{{trans('messages.drive.provides_navigation')}}</p>
</div>
</div>
  </li>
   <li>
  <div class="col-lg-5 col-md-4 col-sm-5 col-xs-12">
  <img src="images/mobile-small4.png" alt="">
  </div>
<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12  drive-app">
<div class="arrive-content" style="width:100% !important;">
<div style="position: relative !important;">
<p class="_style_ZJW1y ride-support how-column-content" style="
    margin-bottom: 25px !important;">{{trans('messages.drive.track_earnings')}}</p>
</div><p class="cmln__paragraph">{{trans('messages.drive.summaries')}}</p>
</div>
</div>
  </li>
   <li>
  <div class="col-lg-5 col-md-4 col-sm-5 col-xs-12">
  <img src="images/mobile-small5.png" alt="">
  </div>
<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12  drive-app">
<div class="arrive-content" style="width:100% !important;">
<div style="position: relative !important;">
<p class="_style_ZJW1y ride-support how-column-content" style="
    margin-bottom: 25px !important;">{{trans('messages.drive.rating')}}</p>
</div><p class="cmln__paragraph">{{trans('messages.drive.rider_driver_rate')}} {{$site_name}} {{trans('messages.drive.platform')}}</p>
</div>
</div>
  </li>
</ul>
</div>
</div>
</div>
<div class="col-lg-9 col-md-12 col-sm-12 col-xs-12 mobile-slide-pad hide-sm show-md">
  <div id="wrapper1" class="">
  <div class="rslides_container">
<ul class="rslides" id="slider2">
  <li>
  <div class="col-lg-5 col-md-4 col-sm-5 col-xs-12">
  <img src="images/mobile1.png" alt="">
  </div>
<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12  drive-app">
<div class="arrive-content" style="width:100% !important;">
<div style="position: relative !important;">
<p class="_style_ZJW1y ride-support how-column-content" style="
    margin-bottom: 25px !important;">{{trans('messages.drive.going_online')}}</p>
</div><p class="cmln__paragraph">{{$site_name}} {{trans('messages.drive.always_avail')}}</p>
</div>  
</div>
  </li>
 <li>
  <div class="col-lg-5 col-md-4 col-sm-5 col-xs-12">
  <img src="images/mobile2.png" alt="">
  </div>
<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12  drive-app">
<div class="arrive-content" style="width:100% !important;">
<div style="position: relative !important;">
<p class="_style_ZJW1y ride-support how-column-content" style="
    margin-bottom: 25px !important;">{{trans('messages.drive.accept_trip_request')}}</p>
</div><p class="cmln__paragraph">{{trans('messages.drive.receive_trip_request')}}</p>
</div>
</div>
  </li>
 
  <li>
  <div class="col-lg-5 col-md-4 col-sm-5 col-xs-12">
  <img src="images/mobile3.png" alt="">
  </div>
<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12  drive-app">
<div class="arrive-content" style="width:100% !important;">
<div style="position: relative !important;">
<p class="_style_ZJW1y ride-support how-column-content" style="
    margin-bottom: 25px !important;">{{trans('messages.drive.turn_by_turn')}}</p>
</div><p class="cmln__paragraph">{{trans('messages.drive.provides_navigation')}}</p>
</div>
</div>
  </li>
   <li>
  <div class="col-lg-5 col-md-4 col-sm-5 col-xs-12">
  <img src="images/mobile4.png" alt="">
  </div>
<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12  drive-app">
<div class="arrive-content" style="width:100% !important;">
<div style="position: relative !important;">
<p class="_style_ZJW1y ride-support how-column-content" style="
    margin-bottom: 25px !important;">{{trans('messages.drive.track_earnings')}}</p>
</div><p class="cmln__paragraph">{{trans('messages.drive.summaries')}}</p>
</div>
</div>
  </li>
   <li>
  <div class="col-lg-5 col-md-4 col-sm-5 col-xs-12">
  <img src="images/mobile5.png" alt="">
  </div>
<div class="col-lg-7 col-md-7 col-sm-7 col-xs-12  drive-app">
<div class="arrive-content" style="width:100% !important;">
<div style="position: relative !important;">
<p class="_style_ZJW1y ride-support how-column-content" style="
    margin-bottom: 25px !important;">{{trans('messages.drive.rating')}}</p>
</div><p class="cmln__paragraph">{{trans('messages.drive.rider_driver_rate')}} {{$site_name}} {{trans('messages.drive.platforms')}}</p>
</div>
</div>
  </li>
</ul>
</div>
</div>
</div>
</div>
</div>
<div class="container-fluid" style="background:#f8f8f9;">
<div class="col-lg-11 col-md-11 col-sm-11 col-xs-11 col-lg-push-1 col-sm-push-1 col-md-push-1 col-xs-push-1 pad-100 feed-div" style="background:#fff;">
<div class="_style_VxPAE" data-reactid="330"><p class="_style_ZJW1y" data-reactid="331">{{trans('messages.drive.communication')}}</p><h2 style="margin-bottom:40px !important;" class="_style_3zaJwR" data-reactid="332">{{trans('messages.drive.feedback_success')}}</h2></div>
<div class="col-lg-6 col-md-6 col-xs-12 col-sm-6">
<img src="images/icon/stay_info_Feedback_for_success.png" style="margin-bottom:20px;" >
<div>
<a style="text-decoration:none !important;font-size: 15px;">{{trans('messages.drive.stay_informed')}}</a>
<p class="cmln__paragraph">{{trans('messages.drive.customized_updates')}} {{$site_name}} {{trans('messages.drive.experience')}}. </p>
</div>
</div>
<div class="col-lg-6 col-md-6 col-xs-12 col-sm-6">
<img src="images/icon/rinder_comment_Feedback_for_success.png" style="margin-bottom:20px;">
<div>
<a style="text-decoration:none !important;font-size: 15px;">{{trans('messages.drive.rider_comment')}}</a>
<p class="cmln__paragraph">{{trans('messages.drive.rating_section')}}</p>
</div>
</div>
</div>
</div>
<div class="container-fluid" style="background:#f8f8f9;">
<div class="col-lg-11 col-md-12 col-sm-12 col-xs-12 pattern-content">
<div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 bor-bottom-ash" style="padding:30px 0px;">
<div class="_style_VxPAE" data-reactid="330"><p class="_style_ZJW1y" data-reactid="331">{{trans('messages.home.siginup')}}</p><h2 style="margin-bottom:10px !important;" class="_style_3zaJwR" data-reactid="332">{{trans('messages.drive.start_drive_now')}}</h2></div>
<p class="cmln__paragraph"> {{trans('messages.drive.start_drive')}} {{$site_name}} {{trans('messages.drive.earn_your_terms')}}</p>
<a class="btn btn--primary btn--arrow position--relative error-retry-btn" href="{{ url('signup_driver') }}">
<div class="block-context soft-small--right" style="    width: 180px;    font-size: 13px !important;">{{trans('messages.home.siginup_drive')}}</div>
<i class="icon_right-arrow-thin icon transition delta position--absolute"></i>
</a>
</div>
<div class="col-lg-8 col-md-8 col-sm-9 col-xs-12" style="padding:30px 0px;">
<div class="_style_VxPAE" data-reactid="330"><p class="_style_ZJW1y" data-reactid="331">{{trans('messages.drive.already_signed_up')}}</p></div>
<p class="cmln__paragraph">{{trans('messages.drive.invite_friends')}} {{$site_name}} {{trans('messages.drive.you_earn')}}</p>
</div>
</div>
</div>
<div class="container-fluid" style="background:#f8f8f9 !important">
<div class="container" >
<small style="font-size:10.5px !important;">{{trans('messages.drive.info_page')}}</small>
</div>
</div>
</main>
@stop
<style type="text/css">
	.height-safety .mini-green.ride-mini-green{right: 55px;bottom: -30px;}
	.arrive-content {
    width: 100% !important;
}
.drive-app .arrive-content{
    padding: 200px 0px 30px 70px;}
.drive-app .cmln__paragraph{font-size: 16px !important;}
.footer-img{ background-color:#f8f8f9 !important;}
.page-footer-back{
  background-color:#f8f8f9 !important;
  }
  .btn-input:hover, .btn:hover, .file-input:hover, .tooltip:hover , .btn, .btn-input, .file-input, .tooltip{background:transparent !important; border: none !important}
.btn.btn--primary:hover{background:transparent !important; color: #fff !important;}
	@media (min-width: 1100px){
		.trip-content.slide-content{font-size: 36px !important;}
}

.how-column-content:before {
    margin: -30px 0px !important;
}
.drive-app ._style_ZJW1y.ride-support {
    font-size: 29px !important;
}
}
.how-column-content:before {
    position: absolute;
    content: '';
    top: 12px;
    width: 40px;
    height: 2px;
    background-color: #C6C6C6;
    margin: -25px 0px;
}
</style>