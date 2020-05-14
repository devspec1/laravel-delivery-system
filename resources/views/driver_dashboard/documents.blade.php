<title>Documents</title>
@extends('template_driver_dashboard') 

@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" ng-controller="payment" style="padding:0px;">
  <div class="" style="padding:0px 15px;" ng-init="license_back= '{{trans('messages.driver_dashboard.driver_license_back')}}';license_front= '{{trans('messages.driver_dashboard.driver_license_front')}}';insurance= '{{trans('messages.driver_dashboard.motor_insurance')}}';rc= '{{trans('messages.driver_dashboard.reg_certificate')}}';permit= '{{trans('messages.driver_dashboard.carriage_permit')}}';select_file= '{{trans('messages.driver_dashboard.select_file')}}';upload_file ='{{trans('messages.dashboard.upload_file')}}';">
    <div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="border-bottom:0px !important;">
      <a class="btn btn--link hard--bottom rider-signup primary-font--bold  borderless--left col-lg-12 col-md-12 col-sm-12 col-xs-12" href="{{ url('driver_profile') }}" style="    text-transform: capitalize !important;color:#2ec5e1 !important;    text-align: left;
    padding-bottom: 40px;font-weight: bold;"><i class="icon icon_left-arrow push-tiny--left"></i>{{trans('messages.driver_dashboard.back_profile')}}</i></a>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6">
    <div class="img--circle img--bordered img--shadow driver-avatar profile_picture doc_avatar">
    @if(@Auth::user()->profile_picture->src == '')
    <img src="https://d1w2poirtb3as9.cloudfront.net/default.jpeg" class="">
    @else
    <img src="{{ @Auth::user()->profile_picture->src }}" class="">
    @endif
    </div>
    </div>
    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
    <label class="col-lg-4 col-md-4 col-sm-4 col-xs-6" style="   font-weight: normal;
        font-size: 20px;
        padding-top: 20px;"> {{ $user->first_name }}</label>

    </div>
<div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="    padding: 25px 0px 15px;">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12" style="padding:6px 0px;"> {{trans('messages.driver_dashboard.driver_license_back')}}</div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 " >
    <div class="profile_img_mark image-show" data-title="Driver's License - (Back/Reverse)" style="cursor: pointer;">
    @if(@$user->driver_documents->license_back)
      <img style="max-width: 75px;
    max-height: 75px;width: 100%;height: 100%;" src="{{ $user->driver_documents->license_back }}">
    @else
     <img style="width: 75px;height: 50px;" src="{{ url('images/driver_doc.png')}}">
    @endif
  </div>
    </div>
    
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6" style="padding:6px 15px;">
     <a href="#" ng-click="upload_document(license_back,'license_back')" class="btn-blue-border pull-right popup-btn1">{{trans('messages.driver_dashboard.upload')}}</a>
    </div>
  </div>
</div>
<div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="    padding: 25px 0px 15px;">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12" style="padding:6px 0px;" > {{trans('messages.driver_dashboard.driver_license_front')}}</div>

    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 ">
    <div class="profile_img_mark image-show" data-title="Driver's License - (Front)" style="cursor: pointer;">
    @if(@$user->driver_documents->license_front)
     <img style="max-width: 75px;
    max-height: 75px;width: 100%;height: 100%;" src="{{ $user->driver_documents->license_front }}">
    @else
     <img style="width: 75px;height: 50px;" src="{{ url('images/driver_doc.png')}}">
    @endif
  </div>
    </div>

    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6" style="padding:6px 15px;">
     <a href="#" ng-click="upload_document(license_front,'license_front')"  class="btn-blue-border pull-right popup-btn1">{{trans('messages.driver_dashboard.upload')}}</a>
    </div>
  </div>
</div>
@if(Auth::user()->company_id==1)
  <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="    padding: 25px 0px 15px;">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12" style="padding:6px 0px;"> {{trans('messages.driver_dashboard.motor_insurance')}} </div>
      
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 " >
        <div class="profile_img_mark image-show" data-title="Motor Insurance Certificate" style="cursor: pointer;">
      @if(@$user->driver_documents->insurance)
       <img style="max-width: 75px;width: 100%;height: 100%;
      max-height: 75px;" src="{{ $user->driver_documents->insurance }}">
      @else
      <img style="width: 75px;height: 50px;" src="{{ url('images/driver_doc.png')}}">
      @endif
    </div>
      </div>

      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6" style="padding:6px 15px;">
       <a href="#" ng-click="upload_document(insurance,'insurance')" class="btn-blue-border pull-right popup-btn1"> {{trans('messages.driver_dashboard.upload')}}</a>
      </div>
    </div>
  </div>
  <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="    padding: 25px 0px 15px;">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12" style="padding:6px 0px;"> {{trans('messages.driver_dashboard.reg_certificate')}} </div>
      
      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 ">
      <div class="profile_img_mark image-show" data-title="Certificate of Registration" style="cursor: pointer;">
      @if(@$user->driver_documents->rc)
       <img style="max-width: 75px;
      max-height: 75px;width: 100%;height: 100%;" src="{{ $user->driver_documents->rc }}">
      @else
        <img style="width: 75px;height: 50px;" src="{{ url('images/driver_doc.png')}}">
      @endif
    </div>
      </div>

      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6" style="padding:6px 15px;">
       <a href="#" ng-click="upload_document(rc,'rc')"  class="btn-blue-border pull-right popup-btn1">{{trans('messages.driver_dashboard.upload')}}</a>
      </div>
    </div>
  </div>
  <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="    padding: 25px 0px 15px;">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12" style="padding:6px 0px;"> {{trans('messages.driver_dashboard.carriage_permit')}}  </div>

      <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 ">
      <div class="profile_img_mark image-show" data-title="{{trans('messages.driver_dashboard.carriage_permit')}}" style="cursor: pointer;">
      @if(@$user->driver_documents->permit)
       <img style="max-width: 75px;
      max-height: 75px;width: 100%;height: 100%;" src="{{ $user->driver_documents->permit }}">
      @else
      <img style="width: 75px;height: 50px;" src="{{ url('images/driver_doc.png')}}">
      @endif
    </div>
      </div>
      
      <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6" style="padding:6px 15px;">
       <a href="#" ng-click="upload_document(permit,'permit')" class="btn-blue-border pull-right popup-btn1">{{trans('messages.driver_dashboard.upload')}}</a>
      </div>
    </div>
  </div>
@endif
</div>
</div>
</div>
</div>
</div>
</div>


<div class="popup1">
 <div class="container page-container-auth">
  <div class="row">
    <div class="col-md-7 col-lg-5 col-center">
     <span style="padding: 7px;" class="icon-remove remove-bold pull-right close-btn"></span>
      <form action="" enctype="multipart/form-data" method="post" name="uploadForm" id="uploadForm">
      <div class="panel top-home">
        <p class="vehicle-p document_upload">{{trans('messages.driver_dashboard.upload_driver_license')}}</p>
        <input type="hidden" name="id" id="driver_id" value="{{ $user->id }}">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="document_type" value="" id="document_type">
            <input id="document_upload" name="document" type="file" style="display:none">
         
          <button type="button" name="document" value="1" id="btn-pad" style="padding: 4px 30px;font-size: 15px;width: 100%;margin: 20px 0px 0px 0px;border-radius: 0px;max-width: 450px !important;max-height: 40px !important;" class="btn btn--primary btn-blue doc-button doc_upload">  
            <span style="padding: 7px;" class="icon icon_file" ></span>
            <span id="span-cls" > </span></button>          
            <span class="text-danger" id="error_msg"></span>
      </div> 
      </form>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body text-center">
                <img class="modal-image" src="" style="max-width: 100%;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>                
            </div>
        </div>
    </div>
</div>

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
@media (max-width: 400px){
    #btn-pad.btn.btn--primary.btn-blue{
font-size: 11px !important;
padding:0px 20px !important;
    }
}
</style>