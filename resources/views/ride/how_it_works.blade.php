@extends('template')

@section('main')



<div class="how_it_work_page">
<div class="container-fluid ride-div-main">
<div class="banner">
<div class="col-lg-5 col-md-12 col-sm-12 col-xs-12 md-pull-right height-safety height-how-it">
<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10" style="padding: 0;">
<div class="slide-img how-it-img"></div>
</div>
<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 image_height">
<div class="pattern">
<div aria-label="Decorative pattern" class="decorative_pattern">
<div class="isvg loaded">
<img src="{{ url('/') }}/images/icon/patten_145 (1).jpg">
</div>
</div>
</div>
</div>




@if(Auth::user()==null)
<div class="mini-green ride-mini-green how-it-green">
<div href="#" class="_style_4jQAPw green-mini-div">
<div class="_style_1PPmFR">{{trans('messages.ride.ride_with_gofer',['site_name'=>$site_name])}}</div>
<a class="btn btn--primary btn--arrow position--relative error-retry-btn" href="{{ url('signup_rider') }}">
<div class="block-context soft-small--right">{{trans('messages.home.siginup')}}</div>
<i class="icon_right-arrow-thin icon transition delta position--absolute"></i>
</a>
</div>
</div>
@endif
</div>
<div class="col-lg-7 col-md-12 col-sm-12 col-xs-12">
<div class="pattern-content how-it-content">
<p class="slide-content">
{{trans('messages.ride.how_works',['site_name'=>$site_name])}}</p>
<ul>
<li>
<div class="num-circle">1</div>
  <p class="_style_ZJW1y">{{trans('messages.ride.request')}}</p>
  <p class="cmln__paragraph">{{trans('messages.ride.request_content')}}</p>

</li>
<li>
<div class="num-circle">2</div>
  <p class="_style_ZJW1y" >{{trans('messages.ride.delivery')}}</p>
  <p class="cmln__paragraph">{{trans('messages.ride.delivery_content')}}</p>
</li>
<li>
<div class="num-circle">3</div>
  <p class="_style_ZJW1y">{{trans('messages.ride.pay_go')}}</p>
  <p class="cmln__paragraph">{{trans('messages.ride.pay_go_content')}}</p>

</li>
</ul>
</div>
</div>
</div>
</div>
</div>








<div class="container-fluid" style="background-color: #f8f8f9 !important;">
<div class="page-container-responsive">
<div class="features1">
<p class="slide-content trip-content how-column-content" data-reactid="287">{{trans('messages.ride.faq')}}</p>
<p class="slide-content sub_heading">{{trans('messages.ride.what_expect')}}</p>
<div class="row-questions ">  
<div class=" col-md-12 col-xs-12 col-sm-12 col-lg-12">
<div class="faq-partial part-section">
<ul id="category-tabs" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-0">
<li class="cat-tab-li">
  <a href="javascript:void" class="faq-question col-lg-12 col-md-12 col-sm-12 col-xs-12 full-anvhor"><i class="icon icon_right-arrow faq-right push-tiny--left"></i><p class="ride-reservation">{{trans('messages.ride.make_reserve')}}</p></a>

<ul class="faq-new-toogle col-lg-12 col-md-12 col-sm-12 col-xs-12 " style="display: none;">
<li><span class="faq-answer" href="javascript:void"> 
<div class="sub_list_view">
<div>
  <p>{{trans('messages.ride.make_reserve_content',['site_name'=>$site_name])}}'s </p>

<ol type="1" class="order-list">
  <li>{{trans('messages.ride.schedule_ride')}}</li>
  <li>{{trans('messages.ride.select_date')}} </li>
  <li>{{trans('messages.ride.select_pickup_location')}}</li>
  <li>{{trans('messages.ride.review_fare')}} </li>
  <li>{{trans('messages.ride.after_confirm')}}</li>
</ol>
</div></div>
</span>
</li>

</ul>
</li>
</ul>
</div>
<div class="faq-partial part-section">
<ul id="category-tabs" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 p-0">
<li class="cat-tab-li">
  <a href="javascript:void" class="faq-question col-lg-12 col-md-12 col-sm-12 col-xs-12 full-anvhor"><i class="icon icon_right-arrow faq-right push-tiny--left"></i><p class="ride-reservation">{{trans('messages.ride.do_i_need')}}</p></a>
<ul class="faq-new-toogle col-lg-12 col-md-12 col-sm-12 col-xs-12 " style="display: none;">
  <li>
  <span class="faq-answer" href="javascript:void">
 <div class="sub_list_view">
  <div>
    <p>{{trans('messages.ride.do_i_need_content',['site_name'=>$site_name])}}</p>
</div></div></span></li>

</ul>
</li>
</ul>
</div>
<div class="faq-partial  part-section">
<ul id="category-tabs" class="col-lg-12 col-md-12 col-sm-12 col-xs-12  p-0">
<li class="cat-tab-li"><a href="javascript:void" class="faq-question col-lg-12 col-md-12 col-sm-12 col-xs-12 full-anvhor"><i class="icon icon_right-arrow faq-right push-tiny--left"></i><p class="ride-reservation">{{trans('messages.ride.cancel_request')}}</p></a>
<ul class="faq-new-toogle col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: none;">
<li>
  <span class="faq-answer" href="javascript:void"> 
<div class="sub_list_view">
  <div>
    <p>{{trans('messages.ride.cancel_request_content',['site_name'=>$site_name])}}
</p>
</div></div></span></li>

</ul>
</li>
</ul>
</div>


</div>
</div>
</div>
</div>
</div>



<div class="container-fluid" style="background:#fff;">
<div class="page-container-responsive">
<div class="features1">
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 bor-bot-sm" id="pad-sm-zero"">

 <div class="arrive-content1">
<div style="position: relative">
  <p class="slide-content trip-content how-column-content">{{trans('messages.ride.start_riding')}}</p>
</div>
<div>
  <p class="cmln__paragraph">{{trans('messages.ride.start_riding_content')}}</p>
</div>
@if(Auth::user()==null)
<a class="btn btn--primary btn--arrow position--relative error-retry-btn" href="{{ url('signup_rider') }}">
<div class="block-context soft-small--right">{{trans('messages.footer.siginup_ride')}}</div>
<i class="icon_right-arrow-thin icon transition delta position--absolute"></i>
</a>
@endif
</div>
</div>
  
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 bor-bot-sm" id="pad-sm-zero" >
<div class="arrive-content1">
<div style="position: relative">
  <p class="slide-content trip-content how-column-content">{{trans('messages.ride.already_have_account')}}</p>
</div>
<div>
  <p class="cmln__paragraph">{{trans('messages.ride.invite',['site_name'=>$site_name])}}</p>
</div>
</div>
</div>
</div>
</div>
</div>
</div>

@stop

<style>
  .how_it_work_page .ride-div-main{
  padding: 0 !important;
}
.how_it_work_page .banner{
    position: relative;
    float: left;
    width: 100%
}
.how_it_work_page .height-safety{
    padding: 0px !important;
    background-image: url(../public/images/icon/hand_rise.jpg) !important;

}
.slide-img.how-it-img{
        height: 665px !important;
}
.how_it_work_page .image_height{
        height: 100%;
}
.how_it_work_page .pattern{
        height: 100% !important;
    width: 100% !important;
    right: 0px;
    position: absolute;
    z-index: 10;
}
.decorative_pattern{
    height: 100%;
}
.isvg.loaded{
    height: 100%;
}
.mini-green.ride-mini-green.how-it-green{
   bottom: -30px; right: 55px;
}
.green-mini-div{
        width: 206px;
    padding: 32px 20px 20px 32px;
    display: block;
    position: relative;
    height: 206px;
        background-color: rgb(55, 112, 55);
}
._style_4jQAPw.green-mini-div a{
background: transparent !important;
    border: none !important;
    float: right;
    margin-top: 55px;
    margin-right: -16px;

}
._style_4jQAPw.green-mini-div a.btn.btn--primary:hover {
     background: transparent !important; 
    color: #fff !important;
}
._style_1PPmFR{
    font-weight: 500; 
    color: rgb(255, 255, 255);
    font-size: 21px; 
    line-height: 1.4;
}
._style_1PPmFR a{
    background: transparent !important;
    border: none !important;
    float: right;
    margin-top: 55px;
    margin-right: -16px;
}
.pattern-content.how-it-content ul{
    padding:0px;
    padding-left: 50px;
}
.pattern-content.how-it-content ul li{
list-style:none;
}
.num-circle p._style_ZJW1y{
        margin: 0px 0px 10px !important;
    min-height: unset;
    font-weight: 600 !important;
}
p.slide-content.trip-content.how-column-content{
        margin-bottom: 10px !important;
    font-weight: 400 !important;
    padding: 0px 20px;
    font-size: 36px !important;
    position: relative;
}
.mar-height.ride-three{
    text-align: center;
}
.mar-height.ride-three ._style_ZJW1y{
    margin: 10px 0px 15px !important;
    min-height: unset;
}
.mar-height.ride-three .arrive-content{
    float: none;
    width: 100%;
}
.how-column-content:before {
    position: absolute;
    content: '';
    top: 12px;
    width: 40px;
    height: 2px;
    background-color: #C6C6C6;
    margin: -10px 0px;
}
.features{
    padding-top: 80px !important;
    display: inline-block;
    width: 100%;
}
.sub_heading{
     margin-bottom: 45px !important;
         padding: 0px 20px;
}
#category-tabs{
    border-top: 1px solid rgb(214, 214, 213);
}
.sub_list_view{
    margin-bottom: -16px; margin-top: 20px;
}
.sub_list_view p{
    margin-bottom: 16px
}
.sub_list_view .order-list{
    margin-bottom: 16px;
    padding:0px 0px 0px 20px;
}
.sub_list_view .order-list li{
margin-bottom: 4px;
}
.bor-bot-sm{
    padding:0px 120px 0px 0px;
}
.arrive-content1 {
    padding: 0 20px;
    width: 80%;
}
.arrive-content1 p.slide-content.trip-content.how-column-content{
    padding: 0;
}
.arrive-content1 .block-context{
    width: 180px;
    font-size: 13px !important;
}
.arrive-content1 .btn.btn--primary:hover {
    background-color: #285228 !important;
    border-color: #285228 !important;
}   
.features1 {
    padding-top: 80px !important;
    display: inline-block;
    width: 100%;
    padding-bottom: 80px;
}



</style>