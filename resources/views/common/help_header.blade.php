<title> Help | {{$site_name}} </title>  
<header style="height:66px;">

  <div class="container-fluid fixed-header" style="line-height: 35px;">
  <button type="button" class="navbar-toggle nav-click hide-md" data-toggle="collapse" data-target="#menu-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a href="{{ url('/') }}" class="pull-left logo-link"><img src="images/Gofer_logo.jpg"></a>
        <ul  class="header-left-link">
        
        </ul>

        <div class="pull-right">
          
        <ul class="header-right-link">
     
        <li ><a href="{{ url('signin') }}">{{trans('messages.header.signin')}}</a></li>
        </ul>
       
        <div class="pull-right">
        <div class="become-driver"><a href="{{ url('signup') }}" class="btn btn--primary" >{{trans('messages.home.siginup')}}</a>
      </div>
      </div>
     
        </div>
  </div>
 </header>
 <div class="nav-div">
 <div class="icon-remove remove-bold pull-left"> </div>
 <p class="head-logo pull-left">{{$site_name}}</p>
 <a href="#" class="pull-right signin-link">
<span class="icon-user-inside-circle" style="    font-size: 22px;
    padding: 0px 5px;"></span>
<span style="    position: relative;
    top: -5px;">{{trans('messages.header.signin')}}</span></a>
    <div class="show-list-nav">
 <div class="button-div">
 <a href="{{ url('signup_rider') }}" class="btn btn--reverse"><{{trans('messages.footer.siginup_ride')}}</a>
<a href="{{ url('signup_driver') }}" class="btn btn--reverse-outline">{{trans('messages.footer.driver')}}</a>
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
  <ul class="nav-list-one ride-div" style="padding-top:30px;">
  <li class="back-li">
  <a href="#" class="back-ride">
  <svg viewBox="0 0 64 64" width="16px" height="16px" class=" _style_3fIIOP"><path d="M39.425 53.21L23.16 36.947l-4.242-4.243a1 1 0 0 1 0-1.414l4.242-4.243 16.264-16.263a1 1 0 0 1 1.414 0l4.242 4.242a1 1 0 0 1 0 1.414L29.525 31.997l15.556 15.556a1 1 0 0 1 0 1.414L40.84 53.21a1 1 0 0 1-1.414 0z"></path></svg></span>{{trans('messages.header.back')}}</a></li>
 <li>
 <a href="{{ url('safety') }}" >{{trans('messages.footer.safety')}}

 </a>
 </li>
  <li>
 <a href="{{ url('how_it_works') }}" >{{trans('messages.header.how_it_works')}}

 </a>
 </li>
 </ul>
   <ul class="nav-list-one drive-div" style="padding-top:30px;">
  <li class="back-li">
  <a href="#" class="back-drive">
  <svg viewBox="0 0 64 64" width="16px" height="16px" class=" _style_3fIIOP"><path d="M39.425 53.21L23.16 36.947l-4.242-4.243a1 1 0 0 1 0-1.414l4.242-4.243 16.264-16.263a1 1 0 0 1 1.414 0l4.242 4.242a1 1 0 0 1 0 1.414L29.525 31.997l15.556 15.556a1 1 0 0 1 0 1.414L40.84 53.21a1 1 0 0 1-1.414 0z"></path></svg></span>{{trans('messages.header.back')}}</a></li>
 <li>
 <a href="{{ url('requirements') }}" >{{trans('messages.header.require')}}

 </a>
 </li>
  <li>
 <a href="{{ url('driver_app') }}" >{{trans('messages.header.app')}}

 </a>
 </li>
  <li>
 <a href="{{ url('safety') }}" >{{trans('messages.footer.safety')}}

 </a>
 </li>

 </ul>
 <ul class="nav-list-one city-div" style="padding-top:30px;">
  <li class="back-li">
  <a href="#" class="back-city">
  <svg viewBox="0 0 64 64" width="16px" height="16px" class=" _style_3fIIOP"><path d="M39.425 53.21L23.16 36.947l-4.242-4.243a1 1 0 0 1 0-1.414l4.242-4.243 16.264-16.263a1 1 0 0 1 1.414 0l4.242 4.242a1 1 0 0 1 0 1.414L29.525 31.997l15.556 15.556a1 1 0 0 1 0 1.414L40.84 53.21a1 1 0 0 1-1.414 0z"></path></svg></span>{{trans('messages.header.back')}}</a>
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
 <style type="text/css">
 	.logo-link {
    padding: 23px 15px 14px 10px !important;
}
.header-left-link{    text-transform: uppercase;    margin-left: -10px;}
.header-left-link {
    font-size: 14px;
    margin-bottom: 12px;
    margin-top: 3px !important;
    margin-left: -10px !important;
}
.header-left-link li a {
    font-size: 19px !important;
    font-weight: 200 !important;

}
.header-left-link li a:hover, .header-left-link li a:focus {
    color: #216C55 !important;
    border-bottom: none !important;
    border-top: 4px solid transparent !important;
 
}
.header-right-link li a{font-size: 13px !important;}
.header-right-link{margin-left: 0px !important}
.icon-map-marker:before {
    float: right;
    padding: 7px;
}
.svg-arrow{left: -55px !important;}
 </style>	
