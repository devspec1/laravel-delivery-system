<header style="height:66px;">

  <div class="container-fluid fixed-header" style="line-height: 35px;">
  <button type="button" class="navbar-toggle nav-click" data-toggle="collapse" data-target="#menu-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a href="{{ url('/') }}"  class="pull-left logo-link"><img style="width: 109px;background-color: white;    margin-top: 15px;height: 50px !important;" src="{{ $logo_url }}"></a>
        <ul  class="header-left-link">
        <li><a href="{{ url('ride') }}">{{trans('messages.footer.ride')}}</a></li>
        <li ><a href="{{ url('drive') }}">{{trans('messages.footer.drive')}}</a></li>
        
        </ul>
        <div class="pull-right">
        
        <ul class="header-right-link">
       
        @if(!Auth::user())
        <li ><a href="{{ url('signin') }}">{{trans('messages.header.signin')}}</a></li>
        <li ><a href="{{ url('signup') }}">{{trans('messages.home.siginup')}}</a></li>
        @else
        <li><a href="{{ @Auth::user()->user_type == 'Rider' ? url('profile') : url('driver_profile') }}">{{ @Auth::user()->first_name }} {{ @Auth::user()->last_name }}</li>
        <li ><a href="{{ url('sign_out') }}">{{trans('messages.header.logout')}}</a></li>
        @endif
        <li ><a href="{{ url('/') }}/help">{{trans('messages.header.help')}}</a></li>
        </ul>
        
        <div class="pull-right">
        @if(!Auth::user())
        <div class="become-driver"><a href="{{ url('signup_driver') }}" class="btn btn--primary" ><!-- react-text: 52 -->{{trans('messages.footer.driver')}}<!-- /react-text --></a>
        @endif
      </div>
      
      <div class="map-tooltip pull-right">
      <div class="inner-tooltip">
      <div class="trans-tip">
      <svg  width="23.82842712474619" height="11.914213562373096" class="svg-arrow">
      <path d="M 0.7071067811865476 11.914213562373096 L 11.914213562373096 0.7071067811865476 L 23.121320343559645 11.914213562373096" fill="white" stroke="#E5E5E4" stroke-width="1" stroke-linecap="square"></path>
      </svg>
      <div class="tip-box">
      <div style="width: max-content !important;">
      <div style="padding: 0px;">
      <div class="tip-width">
      <div class="tip-inside"><p class="tip-head">{{trans('messages.header.location')}}</p><p class="tip-content">{{trans('messages.header.use_location')}}</p></div>
      <div class="tip-button">
      <a class="btn btn--link hard--bottom borderless--left"><!-- react-text: 9176 -->{{trans('messages.header.chng_location')}}<!-- /react-text -->
      <i class="icon icon_right-arrow push-tiny--left"></i></a></div></div>
      </div>
      </div>
      </div>
      </div>
     </div></div>
      </div>
        </div>
  </div>

 </header>
   <div class="flash-container">
    @if(Session::has('message'))
      <div class="alert text-center participant-alert " style="    background: #1fbad6 !important;color: #fff !important;margin-bottom: 0;" role="alert">
        <a href="#" class="alert-close text-white" data-dismiss="alert">&times;</a>
      {!! Session::get('message') !!}
      </div>
    @endif
  </div>
 <div class="nav-div">
 <div class="icon-remove remove-bold pull-left"> </div>
 <p class="head-logo pull-left menu_head"><img src="{{ url(PAGE_LOGO_URL) }}"></p>
 @if(Auth::user()==null && Auth::guard('company')->user()==null)
 <a href="{{ url('signin') }}" class="pull-right signin-link">
<span class="icon-user-inside-circle" style="    font-size: 22px;
    padding: 0px 5px;"></span>
<span style="    position: relative;
    top: -5px;">{{trans('messages.header.signin')}}</span></a>
@endif
    <div class="show-list-nav">
 <div class="button-div">
@if(Auth::user()==null)
 <a href="{{ url('signup_rider') }}" class="btn btn--reverse"><!-- react-text: 2468 -->{{trans('messages.footer.siginup_ride')}}<!-- /react-text --></a>
<a href="{{ url('signup_driver') }}" class="btn btn--reverse-outline">{{trans('messages.footer.driver')}}</a>
@if(Auth::guard('company')->user()==null)
<a href="{{ url('signup_company') }}" class="btn btn--reverse-outline" style="margin-top: 15px;">{{trans('messages.home.become_company')}}</a>
@endif
@endif
 </div>
 <ul class="nav-list-one">
 <li><a href="{{ url('ride') }}" class="ride-link">{{trans('messages.footer.ride')}}
 <span class="icon-chevron-right"></span>
 </a></li>
 <li ><a href="{{ url('drive') }}"  class="drive-link">{{trans('messages.footer.drive')}}<span class="icon-chevron-right"></span>
 </a></li>
 
 </ul>
  <ul class="nav-list-one" style="padding-top:30px;">

 
 </ul>
  
 </div>
 <!--  <ul class="nav-list-one ride-div" style="padding-top:30px;">
  <li class="back-li">
  <a href="#" class="back-ride">
  <svg viewBox="0 0 64 64" width="16px" height="16px" class=" _style_3fIIOP"><path d="M39.425 53.21L23.16 36.947l-4.242-4.243a1 1 0 0 1 0-1.414l4.242-4.243 16.264-16.263a1 1 0 0 1 1.414 0l4.242 4.242a1 1 0 0 1 0 1.414L29.525 31.997l15.556 15.556a1 1 0 0 1 0 1.414L40.84 53.21a1 1 0 0 1-1.414 0z"></path></svg>Back</a></li>
 <li>
 <a href="{{ url('safety') }}" >Safety

 </a>
 </li>
  <li>
 <a href="{{ url('how_it_works') }}" >How it Works

 </a>
 </li>
 </ul> -->
   {{--<ul class="nav-list-one drive-div" style="padding-top:30px;">
  <li class="back-li">
  <a href="#" class="back-drive">
  <svg viewBox="0 0 64 64" width="16px" height="16px" class=" _style_3fIIOP"><path d="M39.425 53.21L23.16 36.947l-4.242-4.243a1 1 0 0 1 0-1.414l4.242-4.243 16.264-16.263a1 1 0 0 1 1.414 0l4.242 4.242a1 1 0 0 1 0 1.414L29.525 31.997l15.556 15.556a1 1 0 0 1 0 1.414L40.84 53.21a1 1 0 0 1-1.414 0z"></path></svg><!-- react-text: 3776 -->{{trans('messages.header.back')}}<!-- /react-text --></a></li>
 <li>
 <a href="{{ url('requirements') }}" ><!-- react-text: 3111 -->{{trans('messages.header.require')}}<!-- /react-text -->

 </a>
 </li>
  <li>
 <a href="{{ url('driver_app') }}" ><!-- react-text: 3111 -->{{trans('messages.header.app')}}<!-- /react-text -->

 </a>
 </li>
  <li>
 <a href="{{ url('safety') }}" ><!-- react-text: 3111 -->{{trans('messages.footer.safety')}}<!-- /react-text -->

 </a>
 </li>

 </ul>--}}
 <ul class="nav-list-one city-div" style="padding-top:30px;">
  <li class="back-li">
  <a href="#" class="back-city">
  <svg viewBox="0 0 64 64" width="16px" height="16px" class=" _style_3fIIOP"><path d="M39.425 53.21L23.16 36.947l-4.242-4.243a1 1 0 0 1 0-1.414l4.242-4.243 16.264-16.263a1 1 0 0 1 1.414 0l4.242 4.242a1 1 0 0 1 0 1.414L29.525 31.997l15.556 15.556a1 1 0 0 1 0 1.414L40.84 53.21a1 1 0 0 1-1.414 0z"></path></svg><!-- react-text: 3776 -->{{trans('messages.footer.back')}}<!-- /react-text --></a>
<div>
<div >
<div >
<div >
<div >
<div>
<input value="Kochi" placeholder="Tell us what city you are in." autocomplete="off" aria-label="Find a city by entering a city, city and state, or city and country"></div></div></div></div><div class="city-content">{{trans('messages.header.area')}}</div></div></div>
  </li>
 
 </ul>
 </div>