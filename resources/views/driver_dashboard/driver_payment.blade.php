<title>Edit Profile</title>
@extends('template_driver_dashboard_new') 
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px !important;" ng-controller="facebook_account_kit">
  @include('common.driver_dashboard_header_new')
  <div style="height: 100%; width: 100%; display: flex" id="profileWrp" class="mainWrp1">

  <div style="display: flex; width: 100%; height: 100%; padding-top: 1.5em" id="profileRightWrp">
       <div  style="width: 100%; flex-direction: column; padding-left: 1.5em; height: 53em" data-tab="payment">
          
           <div id="paymentMethodWrp" style="display: flex; width: 100%; flex-direction: row;justify-content: space-between;">

            <div style="display: flex; flex-direction: column; margin-left: 1em; width: 54%">
              <span style="font-size: 140%; color: #1B187F;font-weight: bold; margin-bottom: 0.8em; font-family:'MontserratReg'">Bank Account</span>
              <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left: 0">
                <div style="margin-bottom: 1em; display: flex; flex-direction: column">
                  <label style="padding:2px 0px;">
                    Bank Name <em class="text-danger">*</em>
                  </label>
                  <div  style="padding:2px 0px;">
                    <input class="_style_3vhmZK" name="email"style="background: white" value="{{ @$result->email}}" placeholder="{{trans('messages.profile.email')}}">
                    <span class="text-danger"> {{ $errors->first('email') }} </span>
                  </div>
                </div>
              </div>
               <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left: 0">
                <div style="margin-bottom: 1em; display: flex; flex-direction: column">
                  <label style="padding:2px 0px;">
                    BSB <em class="text-danger">*</em>
                  </label>
                  <div  style="padding:2px 0px;">
                    <input class="_style_3vhmZK" name="email"style="background: white" value="" placeholder="BSB Number">
                    <span class="text-danger"> {{ $errors->first('email') }} </span>
                  </div>
                  <div  style="padding:2px 0px;">
                    <input class="_style_3vhmZK" name="email"style="background: white" value="" placeholder="Branch">
                    <span class="text-danger"> {{ $errors->first('email') }} </span>
                  </div>
                  <div  style="padding:2px 0px;">
                    <input class="_style_3vhmZK" name="email" style="background: white"value="" placeholder="Bank">
                    <span class="text-danger"> {{ $errors->first('email') }} </span>
                  </div>
                </div>
              </div>
               <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left: 0" >
                <div style="margin-bottom: 1em; display: flex; flex-direction: column">
                  <label style="padding:2px 0px;">
                    Account details <em class="text-danger">*</em>
                  </label>
                  <div  style="padding:2px 0px;">
                    <input class="_style_3vhmZK" name="email" style="background: white" value="{{ @$result->email}}" placeholder="Please enter your account number">
                    <span class="text-danger"> {{ $errors->first('email') }} </span>
                  </div>
                </div>
              </div>
              <div class="page-lead separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: flex; border-bottom:0px !important;">
          <button style="    padding: 0px 40px !important;
          font-size: 12px !important;" type="submit" class="btn btn--primary btn-blue" id="update_btn">{{trans('messages.user.update')}}</button>
         
        </div>
             
           </div>

            <div style="display: flex; flex-direction: column; height: 40em; width: 40%; padding-left: 2em; border-left: 1px solid rgba(0, 0, 0, 0.15);  ">
               <span style="font-size: 140%; color: #1B187F;font-weight: bold; margin-bottom: 0.8em; font-family:'MontserratReg'">Credit Card</span>
              <div style="display: flex; flex-direction: column; ; width: 90%;margin:; border: 1px solid rgba(0, 0, 0, 0.15);">
              <div class="paymentMethodItem"> 
                <div>
                  <img src="{{ asset('images/icon/visa.png') }}"> 
                  <span> 1234 XXXX XXXX XXXX </span>
                </div>
                </div>
                 <div style="justify-content: space-between;" class="paymentMethodItem"> 
                  <div> 
                    <img src="{{ asset('images/icon/visa.png') }}"> 
                    <span> 1234 XXXX XXXX XXXX </span>
                  </div>
                  <img src="{{ asset('images/icon/green_check.png') }}"> 
                </div>
                 <div class="paymentMethodItem"> 
                  <div>
                    <img src="{{ asset('images/icon/apple_pay.png') }}"> 
                    <span> Apple Pay </span>
                  </div>
                </div>
                <div class="paymentMethodItem">
                      <div><img style="height: 1.5em;  padding: 0; border: none; width: 1.5em" src="{{ asset('images/icon/plus.png')}}">
                      <span style="color: #1B187F; font-family: 'MontserratBold"> Add new card... </span>
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
</div>
</div>
</main>
@stop

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>
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