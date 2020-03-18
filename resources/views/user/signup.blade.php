@extends('templatesign')

@section('main')
<div class="signin-selector clearfix">
<div class="signin-container">
<div class="signin-center-content">
<div class="desk-wrapper clearfix">
  <h1 class="signin-maincontent">{{trans('messages.home.siginup')}}</h1>
@if(Auth::user()==null)
  <div class="col-md-{{Auth::guard('company')->user()==null?'4':6}} p-0">
  <h4 class="driver-sub-head">{{trans('messages.profile.driver')}}</h4>
  <p class="driver-sub-cont">{{trans('messages.profile.track_every')}}</p>
  <a href="{{ url('signup_driver') }}" class="btn btn-for-driver">
  <div class="block-context soft-small--right">{{trans('messages.profile.driver_signup' )}}</div>
 <i class="fa fa-long-arrow-right icon icon_right-arrow-thin"></i>
  </a>
  </div>
   <div class="col-md-{{Auth::guard('company')->user()==null?4:6}} p-0">
  <h4 class="driver-sub-head">{{trans('messages.profile.rider')}}</h4>
  <p class="driver-sub-cont">{{trans('messages.profile.trip_history')}}</p>
  <a href="{{ url('signup_rider') }}" class="btn btn-for-rider">
  <div class="block-context soft-small--right">{{trans('messages.profile.rider_signup')}}</div>
 <i class="fa fa-long-arrow-right icon icon_right-arrow-thin"></i>
  </a>
  </div>
@endif
@if(Auth::guard('company')->user()==null)
  <div class="col-md-{{Auth::user()==null?4:6}} p-0">
  <h4 class="driver-sub-head">{{trans('messages.home.company')}}</h4>
  <p class="driver-sub-cont">{{trans('messages.home.company_history')}}</p>
  <a href="{{ url('signup_company') }}" class="btn btn-for-rider">
  <div class="block-context soft-small--right">{{trans('messages.home.company_signup')}}</div>
 <i class="fa fa-long-arrow-right icon icon_right-arrow-thin"></i>
  </a>
  </div>
@endif
</div>
  
</div>
</div>
<div class="theme-pattern">
</div>
</div>

</main>
@stop