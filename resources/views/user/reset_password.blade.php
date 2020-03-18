@extends('templatesign')

@section('main')
<div id="app-wrapper" class="sigin-riders" ng-controller="user">
   <header class="funnel" style="background:url('images/global.png') center center repeat;" data-reactid="5">
      <a href="{{ url('/')}}" data-reactid="8">
      <div class="bit bit--logo text--center">
      <a href="{{ url('/') }}">
         <img class="white_logo" src="{{ $logo_url }}" style="width: 109px; height:50px;background-size: contain;">
      </a> 
      </div></a>
   </header>
   <div class="stage-wrapper narrow portable-one-whole forward" id="app-body" data-reactid="10">
      <div class="soft-tiny" data-reactid="11">
         <div data-reactid="12">
            {!! Form::open(['url' => (Request::path() == 'company/reset_password')?'company/reset_password':'reset_password', 'class' => 'push--top-small forward', 'accept-charset' => 'UTF-8' , 'novalidate' => 'true']) !!}  
              <input type="hidden" name="id" value="{{ $result->id }}">
              <h3> {{trans('messages.user.reset_paswrd')}} {{ ucfirst($result->first_name)}}</h3>
               <div data-reactid="15">

                  <div style="-moz-box-sizing:border-box;font-family:ff-clan-web-pro, &quot;Helvetica Neue&quot;, Helvetica, sans-serif;font-weight:500;font-size:12px;line-height:24px;text-align:none;color:#939393;box-sizing:border-box;margin-bottom:0;margin-top:0;" data-reactid="16"></div>
                  <div style="width:100%;" data-reactid="17">
                     <div style="font-family:ff-clan-web-pro, &quot;Helvetica Neue&quot;, Helvetica, sans-serif;font-weight:500;font-size:14px;line-height:24px;text-align:none;color:#3e3e3e;box-sizing:border-box;margin-bottom:24px;" data-reactid="19">
                        <div class="_style_CZTQ8" data-reactid="20">
                          <input class="text-input input-group-addon" id="new_password" placeholder="Enter New Password" autocorrect="off" autocapitalize="off" name="new_password" data-reactid="21" type="password">
                        </div>
                        <span class="text-danger">{{ $errors->first('new_password')}}</span>
                     </div>
                  </div>
                  <div style="width:100%;" data-reactid="17">
                     <div style="font-family:ff-clan-web-pro, &quot;Helvetica Neue&quot;, Helvetica, sans-serif;font-weight:500;font-size:14px;line-height:24px;text-align:none;color:#3e3e3e;box-sizing:border-box;margin-bottom:24px;" data-reactid="19">
                        <div class="_style_CZTQ8" data-reactid="20">
                          <input class="text-input input-group-addon" id="confirm_password" placeholder="Enter Confirm Password" autocorrect="off" autocapitalize="off" name="confirm_password" data-reactid="21" type="password">
                        </div>
                        <span class="text-danger">{{ $errors->first('confirm_password')}}</span>
                     </div>
                  </div>
               </div>
               
               <button class="btn btn--arrow btn--full blue-signin-btn" data-reactid="22" data-type='email'><span class="push-small--right" data-reactid="23">{{trans('messages.user.next')}}</span><i class="fa fa-long-arrow-right icon icon_right-arrow-thin"></i></button>               
            {!! Form::close() !!}    
         </div>
      </div>
   </div>
</div>


</main>
<style>
   .logo-link
   {
      display: none;
   }
   .funnel
   {
      height: 0px !important;
   }

   
</style>
@stop
   