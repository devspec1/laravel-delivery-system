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
<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 md-pull-right height-safety" style="padding:0px !important;">
<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10" style="padding:0px;">
<div class="slide-img safety-img drive-require-img"></div>
</div>
<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="height:100%;">
<div class="pattern" style="height: 100% !important; width: 100% !important; right: 0px; position: absolute; z-index: 10;"><div style="background-color: #A6DAEC; height: 100%; overflow: hidden;"><div aria-label="Decorative pattern" style="height: 100%;"><div class="isvg loaded" style="height: 100%;">
<img src="{{url('images/icon/patten_94_390.jpg')}}">
</div>
</div>
</div>
</div>
</div>
@if(Auth::user()==null)
<div class="mini-green ride-mini-green" >
    <div href="#" class="_style_4jQAPw green-mini-div" style="width: 206px; padding: 32px 20px 20px 32px; display: block; position: relative; height: 206px; background-color: rgb(55, 112, 55)">
    <div class="_style_1PPmFR" style="font-weight: 500; color: rgb(255, 255, 255);font-size: 21px; line-height: 1.4;">{{trans('messages.drive.start_drive_with')}} {{$site_name}}</div>
<a href="{{ url('signup_driver') }}" class="btn btn--primary btn--arrow position--relative error-retry-btn" style="    background: transparent !important;   border: none !important;    float: right;margin-top: 55px;    margin-right: -16px;">
<div class="block-context soft-small--right">{{trans('messages.home.siginup')}}</div>
<i class="icon_right-arrow-thin icon transition delta position--absolute"></i>
</a>
    </div>
    </div>
@endif
</div>
<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12">
<div class="pattern-content">
<h1 class="slide-head ride-head">{{trans('messages.drive.driver_requirments')}}</h1><p class="slide-content">
{{trans('messages.drive.how_to_drive')}} {{$site_name}}</p>
<p class="cmln__paragraph" style="    font-size: 18px; line-height: 27px;">{{$site_name}} {{trans('messages.drive.own_boss')}} {{$site_name}} {{trans('messages.drive.help_you_every')}}</p>
</div>
</div>

</div>
</div>
<div class="container-fluid" style="background:#f8f8f9 !important">
<div class="col-lg-10 col-md-12 col-sm-12 col-xs-12 pattern-content">
<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12 ">
<img src="images/icon/cont18.png">
</div>
<div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 pad-md">
<p class="_style_ZJW1y" style="margin: 10px 0px 20px !important; min-height: unset;">{{trans('messages.drive.min_requirments')}}</p>
<p class="cmln__paragraph">{{trans('messages.drive.commercial_license')}}
 {{$site_name}} {{trans('messages.drive.help_you_both')}}</p>
<p class="cmln__paragraph">
{{trans('messages.drive.what_you_need')}}</p>
<li class="cmln__paragraph"><a href="#">
{{trans('messages.drive.driver_license')}}</a></li>
<li class="cmln__paragraph"><a href="#">
{{trans('messages.drive.tlc_license')}} </a></li>
<li class="cmln__paragraph"><a href="#">
{{trans('messages.drive.tlc_license_vehicle')}}</a></li>
</div>
</div>
<div class="col-lg-10 col-md-12 col-sm-12 col-xs-12 pattern-content" style="padding-top:0px !important;">
<div class="col-lg-2 col-md-2 col-sm-3 col-xs-12 ">
<img src="images/icon/cont19.png">
</div>
<div class="col-lg-8 col-md-8 col-sm-7 col-xs-12 pad-md">
<p class="_style_ZJW1y" style="margin: 10px 0px 20px !important; min-height: unset;">{{trans('messages.drive.require_documents')}}</p>
<p class="cmln__paragraph">
{{trans('messages.drive.addition_documents')}}</p>
<li class="cmln__paragraph"><a href="#">
{{trans('messages.drive.proof_insurance')}}</a></li>
<li class="cmln__paragraph"><a href="#">
{{trans('messages.drive.proof_vehicle_registration')}}</a></li>
<li class="cmln__paragraph"><a href="#">
{{trans('messages.drive.vehicle_permit')}}</a></li>
</div>
</div>
</div>

@if(Auth::user()==null)
<div class="container-fluid" style="background:#f1f1f1 !important">
<div class="container pad-container-small">
<div class=""><h3 class="flush bottom-head"><div class="primary-font--thin" style="font-weight:400 !important;">{{trans('messages.drive.ready_make_money')}}</div></h3>
<h3 class="flush bottom-head"><div class="primary-font--thin">{{trans('messages.drive.first_step')}}</div></h3>
</div>
<div class="soft-large--bottom palm-hard--bottom app-footer__city-picker
                       position--relative two-fifths palm-one-whole" style="padding-top:20px;width:43%;"><div class="autocomplete-container"><div class="autocomplete position--relative"><div class="autocomplete__input hard flush--bottom autocomplete__input--icon">
                       <div>
                       	<a href="{{ url('signup_driver') }}" class="btn btn--primary btn--arrow position--relative error-retry-btn">
<div class="block-context soft-small--right" style="    width: 180px;">{{trans('messages.home.siginup_drive')}}</div>
<i class="icon_right-arrow-thin icon transition delta position--absolute"></i>
</a>
                       </div>
                       </div>
                       </div></div>
                    </div>
</div>
</div>
@endif
<div class="container-fluid" style="background:#fff !important">
<div class="container pad-container-small" style="padding-top:40px !important;">
<small style="font-size:10.5px !important;">{{trans('messages.drive.information')}}</small>
</div>
</div>
</main>
@stop
<style type="text/css">
	.height-safety .mini-green.ride-mini-green{right: 55px;bottom: -30px;}
	.arrive-content {
    width: 100% !important;
}
.btn-input:hover, .btn:hover, .file-input:hover, .tooltip:hover , .btn, .btn-input, .file-input, .tooltip{background:transparent !important; border: none !important}
.btn.btn--primary:hover{background:transparent !important; color: #fff !important;}
.column-content:before{display: none !important;}
.footer-img{margin-top: 0px !important;}
	@media (min-width: 1100px){
		.trip-content.slide-content{font-size: 36px !important;}
	}
</style>