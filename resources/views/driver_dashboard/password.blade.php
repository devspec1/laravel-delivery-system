<title>Edit Profile</title>
@extends('template_driver_dashboard_new') 
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px !important;" ng-controller="facebook_account_kit">
  @include('common.driver_dashboard_header_new')
  <div style="height: 100%; width: 100%; display: flex" id="profileWrp" class="mainWrp1">

  <div style="display: flex; width: 100%; height: 100%; padding-top: 1.5em" id="profileRightWrp">

      <div style=" width: 100%; flex-direction: column; padding-left: 1.5em" data-tab="profile">
          
            {{ Form::open(array('url' => 'driver_update_password/'.$result->id,'id'=>'form','class' => 'layout layout--flush','name'=>'driver_pass')) }}
          
        
          <input type="hidden" name="user_type" value="{{ $result->user_type }}">
          <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="code" id="code" />
          <input type="hidden" id="user_id" name="user_id" value="{{ $result->id }}">
         
          <div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12 profile_update-loader" style="display: flex; justify-content: space-between; align-items: center; border-bottom:0px !important; margin-bottom: 1.25em; padding-left: 0">
            <div style="display :flex">
              <span style="font-size: 130%; color: #1B187F; font-weight: bold; font-family:'MontserratReg'">Password</span>
             <div  style="display: flex; align-items: center;justify-content: space-between; margin-left: 2em">
           </div>
         </div>
          
          </div>
         
    
      
  
     


      <div style="display: flex; flex-direction: column; width: 50%">
        
         <label style="padding:2px 0px;">Current password</label>
            <div  style="padding:2px 0px;">
              <input class="_style_3vhmZK" type="password" name="currPass">
            </div>
           
              <div style="display: flex; flex-direction: column;margin-right: 1.2em; width: 100%">
                     <label style="padding:2px 0px;">New password</label>
                <div  style="padding:2px 0px;">
                  <input class="_style_3vhmZK" type="password" name="pass1">
                </div>
              </div>
              <div style="display: flex; flex-direction: column;width:100%; ">
                     <label style="padding:2px 0px;"> Confirm new password</label>
                <div  style="padding:2px 0px;">
                  <input class="_style_3vhmZK" type="password" name="pass2">
                </div>
              </div>
               @if($errors->any())
        <h4 style="font-size: 110%; color: #a84632">{{$errors->first()}}</h4>
        @endif
            
         <div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: flex; border-bottom:0px !important;">
          <button style="    padding: 0px 40px !important; margin-top: 1.2em;
          font-size: 12px !important;" type="submit" class="btn btn--primary btn-blue" id="update_btn">{{trans('messages.user.update')}}</button>



        {{ Form::close() }}
      </div>
     
          </div>
      
  </div>
</div>
</div>
</div>
</div>
</div>
</main>
@stop


<script>
  $(function() {
    $("#profileLeftWrp span").click(function() {
       $("#profileRightWrp > div.current").removeClass("current");
       $("#profileRightWrp > div[data-tab='" + $(this).data("tab") + "']").addClass("current");
      $("#profileLeftWrp span.current").removeClass("current");
      $(this).addClass("current");

    })
  });
</script>