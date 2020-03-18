@extends('templatesign')

@section('main')
<div id="app-wrapper" class="sigin-riders">
   <header class="funnel" style="background:url('images/global.png') center center repeat;" data-reactid="5">
      <a href="{{ url('signin')}}" data-reactid="8">
      <div class=" text--center" data-reactid="6" style="padding-top: 35px;">
         <a href="{{ url('/') }}" style="background-image: url('{{ $logo_url }}'); background-size: contain;  background-position: 50% 50% !important;
    display: block;
    height: 50px !important;
    margin: auto;
    left: 0;
    background-repeat: no-repeat;
    width: 109px !important;
    object-fit: contain;" href=""  ></a>
      </div>
      </a>
   </header>
   <div class="stage-wrapper narrow portable-one-whole forward" id="app-body" data-reactid="10">
      <div class="soft-tiny" data-reactid="11">
         <div data-reactid="12">
            {!! Form::open(['action' => 'UserController@forgotpassword', 'class' => 'push--top-small forward']) !!}
            <input type="hidden" name="user_type" value="{{ Route::current()->uri() == 'forgot_password_rider' ? 'Rider' : (Route::current()->uri() == 'forgot_password_driver' ? 'Driver' : 'Company')}}">
               <h4 data-reactid="14">{{trans('messages.user.forgot_paswrd')}}</h4>
               <div data-reactid="15">
                  <div style="-moz-box-sizing:border-box;font-family:ff-clan-web-pro, &quot;Helvetica Neue&quot;, Helvetica, sans-serif;font-weight:500;font-size:12px;line-height:24px;text-align:none;color:#939393;box-sizing:border-box;margin-bottom:0;margin-top:0;" data-reactid="16"></div>
                  <div style="width:100%;" data-reactid="17">
                     <div style="font-family:ff-clan-web-pro, &quot;Helvetica Neue&quot;, Helvetica, sans-serif;font-weight:500;font-size:14px;line-height:24px;text-align:none;color:#3e3e3e;box-sizing:border-box;margin-bottom:24px;" data-reactid="19">
                        <div class="_style_CZTQ8" data-reactid="20">
                        {!! Form::email('email', '', ['class' => 'text-input input-group-addon  ae-form-field','placeholder' => 'Enter your email','autocorrect' => 'off','autocapitalize' => 'off' ]) !!}  
                        </div>
                        <span class="text-danger">{{$errors->first('email')}}</span>
                     </div>
                  </div>
               </div>
               <button class="btn btn--arrow btn--full blue-signin-btn" data-reactid="22" type="submit"><span class="push-small--right" data-reactid="23">{{trans('messages.user.next')}}</span><i class="fa fa-long-arrow-right icon icon_right-arrow-thin"></i></button>
            {!! Form::close() !!}  
         </div> 
         <div class="small push-small--bottom push-small--top" id="sign-up-link-only" data-reactid="26">
            <p class=" display--inline" data-reactid="27">{{trans('messages.user.no_account')}} 
            <a href="{{ Route::current()->uri() == 'forgot_password_rider' ? url('signup_rider') : url('signup_driver')}}">{{trans('messages.home.siginup')}}</a></p>
         </div>
      </div>
   </div>
</div>


</main>

@stop
<style type="text/css">
   body{
      background:#f1f1f1;
   }
   .logo-link
   {
      display: none;
   }
   .funnel
   {
      height: 0px !important;
   }
</style>