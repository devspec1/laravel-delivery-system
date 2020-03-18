<title>Documents</title>
@extends('template_driver_dashboard') 

@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px;">

<div class="" style="padding:0px 15px;">
<div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom:0px !important;">
<a data-toggle="collapse" href="#trip-filterer" class=" collapsed  btn btn--link hard--bottom rider-signup primary-font--bold  borderless--left col-lg-12 col-md-12 col-sm-12 col-xs-12" href="#" style="    text-transform: capitalize !important;color:#2ec5e1 !important;    text-align: left;
    padding-bottom: 40px;font-weight: bold;"><i class="icon icon_plus push-tiny--left" style="padding-right:10px;"></i>{{trans('messages.driver_dashboard.add_vehicle')}}</i></a>
        <div id="trip-filters" class="trip-filters pull-left col-lg-12 col-sm-12 col-md-12 col-xs-12" style="padding:0px;">
    <div id="trip-filters-active" class="trip-filters__active"></div>
    <form id="trip-filterer" data-replace="data-replace" data-button-loader="#trip-filterer-loader" data-button-loader-parent="#trip-filterer-button" class="trip-filters__form trip-filter collapse" style="height: auto;">
    <div class="trip-filter__item" style="overflow:hidden;">
    <div class="grid">
<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding:0px;">
<label class="col-lg-12 col-md-12 col-sm-12 col-xs-12" > {{trans('messages.driver_dashboard.make')}}<sup style="    font-size: 9px;">*</sup></label>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:6px 15px;">
<input class="add-input">
<span class="alpha icon icon_search input-search-icon"></span>
</div>
</div>
<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding:0px;">
<label class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >{{trans('messages.driver_dashboard.model')}}<sup style="    font-size: 9px;">*</sup></label>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:6px 15px;">
<input class="add-input">
<span class="alpha icon icon_search input-search-icon"></span>
</div>
</div>
<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding:0px;">
<label class="col-lg-12 col-md-12 col-sm-12 col-xs-12" > {{trans('messages.driver_dashboard.year')}}<sup style="    font-size: 9px;">*</sup></label>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:6px 15px;">
<input class="add-input">
</div>
</div>
<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding:0px;">
<label class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >{{trans('messages.driver_dashboard.license_plate')}}<sup style="    font-size: 9px;">*</sup></label>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:6px 15px;">
<input class="add-input">
</div>
</div>
<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding:0px;">
<label class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >{{trans('messages.driver_dashboard.vehicle_color')}}</label>
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding:6px 15px;">
<input class="add-input">
</div>
</div>
<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12" style="    padding: 30px 15px 0px;">
<a href="#" style="    padding: 0px 0px !important;
    font-size: 13px !important;white-space: normal;
    width: 100%;" type="submit" class="btn btn--primary btn-blue">{{trans('messages.driver_dashboard.reupload')}}</a>
</div>
    </div>
    </div>
    </form></div>
<div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="    padding: 40px 0px 20px;">
  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6" style="padding:0px;">
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
<div class="img--circle img--bordered img--shadow driver-avatar ">
<img src="images/car-user.png">
</div>
</div>
<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
<small>{{trans('messages.driver_dashboard.make_year')}}</small><br>
<label style="margin:0px !important;"> {{trans('messages.driver_dashboard.make_company')}}</label><br>
<label> {{trans('messages.driver_dashboard.make_product')}}</label><br>
</div>
</div>

<div class="col-lg-3 col-sm-3 col-md-3 col-xs-6">
<small>{{trans('messages.driver_dashboard.license_plate')}}</small><br>
 <label>{{trans('messages.driver_dashboard.reg_no')}}</label><br>
</div>
<div class="col-lg-2 col-sm-2 col-md-1 col-xs-6">
<small>{{trans('messages.driver_dashboard.type')}}</small><br>
<small>-</small>
</div>
<div class="col-lg-4 col-sm-4 col-md-5 col-xs-12">
<a href="#" style="    padding: 0px 30px !important;
    font-size: 14px !important;" type="submit" class="btn btn--primary btn-blue">{{trans('messages.driver_dashboard.reupload')}}</a>
</div>
</div>
<div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="    padding: 40px 0px 20px;">
  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6" style="padding:0px;">
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
<div class="img--circle img--bordered img--shadow driver-avatar ">
<img src="images/car-user.png">
</div>
</div>
<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
<small>{{trans('messages.driver_dashboard.make_year')}}</small><br>
<label style="margin:0px !important;"> {{trans('messages.driver_dashboard.make_company')}}</label><br>
<label> {{trans('messages.driver_dashboard.make_product')}}</label><br>
</div>
</div>

<div class="col-lg-3 col-sm-3 col-md-3 col-xs-6">
<small>{{trans('messages.driver_dashboard.license_plate')}}</small><br>
 <label>{{trans('messages.driver_dashboard.reg_no')}}</label><br>
</div>
<div class="col-lg-2 col-sm-2 col-md-1 col-xs-6">
<small>{{trans('messages.driver_dashboard.type')}}</small><br>
<small>-</small>
</div>
<div class="col-lg-4 col-sm-4 col-md-5 col-xs-12">
<a href="#" style="    padding: 0px 30px !important;
    font-size: 14px !important;" type="submit" class="btn btn--primary btn-blue">{{trans('messages.driver_dashboard.reupload')}}</a>
</div>
</div>
<div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="    padding: 40px 0px 20px;">
  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6" style="padding:0px;">
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
<div class="img--circle img--bordered img--shadow driver-avatar ">
<img src="images/car-user.png">
</div>
</div>
<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
<small>{{trans('messages.driver_dashboard.make_year')}}</small><br>
<label style="margin:0px !important;"> {{trans('messages.driver_dashboard.make_company')}}</label><br>
<label> {{trans('messages.driver_dashboard.make_product')}}</label><br>
</div>
</div>

<div class="col-lg-3 col-sm-3 col-md-3 col-xs-6">
<small>{{trans('messages.driver_dashboard.license_plate')}}</small><br>
 <label>{{trans('messages.driver_dashboard.reg_no')}}</label><br>
</div>
<div class="col-lg-2 col-sm-2 col-md-1 col-xs-6">
<small>{{trans('messages.driver_dashboard.type')}}</small><br>
<small>-</small>
</div>
<div class="col-lg-4 col-sm-4 col-md-5 col-xs-12">
<a href="#" style="    padding: 0px 30px !important;
    font-size: 14px !important;" type="submit" class="btn btn--primary btn-blue">{{trans('messages.driver_dashboard.reupload')}}</a>
</div>
</div>
<div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="    padding: 40px 0px 20px;">
  <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6" style="padding:0px;">
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
<div class="img--circle img--bordered img--shadow driver-avatar ">
<img src="images/car-user.png">
</div>
</div>
<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
<small>{{trans('messages.driver_dashboard.make_year')}}</small><br>
<label style="margin:0px !important;"> {{trans('messages.driver_dashboard.make_company')}}</label><br>
<label> {{trans('messages.driver_dashboard.make_product')}}</label><br>
</div>
</div>

<div class="col-lg-3 col-sm-3 col-md-3 col-xs-6">
<small>{{trans('messages.driver_dashboard.license_plate')}}</small><br>
 <label>{{trans('messages.driver_dashboard.reg_no')}}</label><br>
</div>
<div class="col-lg-2 col-sm-2 col-md-1 col-xs-6">
<small>{{trans('messages.driver_dashboard.type')}}</small><br>
<small>-</small>
</div>
<div class="col-lg-4 col-sm-4 col-md-5 col-xs-12">
<a href="#" style="    padding: 0px 30px !important;
    font-size: 14px !important;" type="submit" class="btn btn--primary btn-blue">{{trans('messages.driver_dashboard.reupload')}}</a>
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

<div class="login-close">
<div class="popup1">
 <div class="container page-container-auth">
  <div class="row">
    <div class="col-md-7 col-lg-5 col-center">
     <span style="padding: 7px;" class="icon-remove remove-bold pull-right close-btn"></span>
      <div class="panel top-home">

    <p class="vehicle-p  ">{{trans('messages.driver_dashboard.upload_documents')}}</p>
      <a href="#" id="btn-pad" style="       padding: 4px 30px;
    font-size: 15px;
    width: 100%;
    margin: 20px 0px 0px 0px;
    border-radius: 0px;" type="submit" class="btn btn--primary btn-blue">
<span style="padding: 7px;" class="icon icon_file"></span>
    {{trans('messages.driver_dashboard.select_file')}} </a>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<div class="login-close">
<div class="popup2">
 <div class="container page-container-auth">
  <div class="row">
    <div class="col-md-7 col-lg-5 col-center">
     <span style="padding: 7px;" class="icon-remove remove-bold pull-right close-btn"></span>
      <div class="panel top-home">

    <p class="vehicle-p  ">{{trans('messages.driver_dashboard.contract_carriage')}}</p>

       <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
<div class="img--circle img--bordered img--shadow driver-avatar ">
<img src="images/car-user.png">
</div>
</div>
<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
<small>{{trans('messages.driver_dashboard.make_year')}}</small><br>
<label style="margin:0px !important;">{{trans('messages.driver_dashboard.make_product')}}</label><br>
<label> {{trans('messages.driver_dashboard.make_model')}}</label><br>
</div>
</div>
 <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
 <small>{{trans('messages.driver_dashboard.license_plate')}}</small><br>
 <label>{{trans('messages.driver_dashboard.reg_no')}}</label><br>
 </div>
      <a href="#" id="btn-pad" style="       padding: 4px 30px;
    font-size: 15px;
    width: 100%;
    margin: 20px 0px 0px 0px;
    border-radius: 0px;" type="submit" class="btn btn--primary btn-blue">
<span style="padding: 7px;" class="icon icon_file"></span>
    {{trans('messages.driver_dashboard.select_file')}}</a>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</main>
@stop
<style type="text/css">
    .btn-input:hover, .btn:hover, .file-input:hover, .tooltip:hover, .btn, .btn-input, .file-input, .tooltip {
    background: transparent !important;
    border: none !important;
}
.btn--link .icon_left-arrow {
    -webkit-transition: left .4s ease;
    transition: left .4s ease;
    position: relative;
    left: -2;
    padding-left: 10px;
}
.btn--link:focus .icon_left-arrow, .btn--link:hover .icon_left-arrow {
    left: -6px;
}
.trip-filter {
    color: #555 !important;
    background: #f7f7f7 !important;
    border: 1px solid #ddd;
}
.trip-filter__item{border-bottom: 0px !important;}
@media (max-width: 400px){
    #btn-pad.btn.btn--primary.btn-blue{
font-size: 11px !important;
padding:0px 20px !important;
    }
}
</style>