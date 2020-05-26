<title>Edit Profile</title>
@extends('template_driver_dashboard_new') 
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px !important;" ng-controller="facebook_account_kit">
  @include('common.driver_dashboard_header_new')
  <div style="height: 100%; width: 100%; display: flex" id="profileWrp" class="mainWrp1">

  <div style="display: flex; width: 100%; height: 100%; padding-top: 1.5em" id="profileRightWrp">
       <div  style="width: 100%; flex-direction: column; padding-left: 1.5em; height: 53em" data-tab="payment">
          {{ Form::open(array('url' => 'driver_update_payment/'.$result->id,'id'=>'form','class' => 'layout layout--flush','name'=>'driver_payment')) }}
           <div id="paymentMethodWrp" style="display: flex; width: 100%; flex-direction: row;justify-content: space-between;">

            <div style="display: flex; flex-direction: column; margin-left: 1em; width: 54%">
              <span style="font-size: 140%; color: #1B187F;font-weight: bold; margin-bottom: 0.8em; font-family:'MontserratReg'">Bank Account</span>
              <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left: 0">
                <div style="margin-bottom: 1em; display: flex; flex-direction: column">
                  <label style="padding:2px 0px;">
                    Bank Name <em class="text-danger">*</em>
                  </label>
                  <div  style="padding:2px 0px;">
                    <input class="_style_3vhmZK" name="bank_name"style="background: white" value="@if(isset($payout->bank_name)) {{ $payout->bank_name }} @endif" placeholder="Bank Name">
                    <span class="text-danger"> {{ $errors->first('bank_name') }} </span>
                  </div>
                </div>
              </div>
               <div class="parter-info separated--bottom col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding-left: 0">
                <div style="margin-bottom: 1em; display: flex; flex-direction: column">
                  <label style="padding:2px 0px;">
                    BSB <em class="text-danger">*</em>
                  </label>
                  <div  style="padding:2px 0px;">
                    <input class="_style_3vhmZK" name="bsb" style="background: white" value="@if(isset($payout->branch_code)) {{ $payout->branch_code }} @endif" placeholder="BSB Number">
                    <span class="text-danger"> {{ $errors->first('bsb') }} </span>
                  </div>
                  <div  style="padding:2px 0px;">
                    <input class="_style_3vhmZK" name="branch"style="background: white" value="@if(isset($payout->branch_name)) {{ $payout->branch_name }} @endif " placeholder="Branch">
                    <span class="text-danger"> {{ $errors->first('email') }} </span>
                  </div>
                  <div  style="padding:2px 0px;">
                    <input class="_style_3vhmZK" name="bank" style="background: white"value="@if(isset($payout->bank_name)) {{ $payout->bank_name }} @endif" placeholder="Bank">
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
          {{ Form::close() }}
         
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

                      <div style="cursor: pointer" id="addNewCard"><img style="height: 1.5em;  padding: 0; border: none; width: 1.5em" src="{{ asset('images/icon/plus.png')}}">
                      <span style="color: #1B187F; font-family: 'MontserratBold"> Add new card... </span>
                    </div>
                </div> 
              </div>
              
               
           </div>

      </div>
     
      </div>

      <div id="cardModalWrp" style="height: 100%; width: 100%; position:fixed; top:0; left:0; background: rgba(0, 0, 0, 0.65); display: none; justify-content: center; align-items: center">
          <div id="cardModal" style=" width: 50em; display: flex; flex-direction: column;justify-content: space-between; background: white; box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.9); padding: 1.5em; font-size: 115%">
              <span style="font-size: 140%; color: #1B187F;font-weight: bold; margin-left: auto;margin-right: auto; margin-bottom: 0.8em; font-family:'MontserratReg'">Add Credit Card</span>
                  <div style="display: flex; flex-direction: column; height: 100%; margin-bottom: 3em">
                           <label style="padding:2px 0px;">
                               Card Type <em class="text-danger">*</em>
                              </label>
                          <div style="display: flex; align-items: center; margin-top: 1em">
                            <img src="{{ asset('images/icon/visa.png') }}" style="margin-right: 1em"> 
                            <img src="{{ asset('images/icon/apple_pay.png') }}"> 
                          </div>
                          <div style="display: flex; align-items: center; margin-top: 1.9em">
                            <div style="display: flex; flex-direction: column;width: 80%; margin-right: 1em">
                               <label style="padding:2px 0px;">
                               Card Number <em class="text-danger">*</em>
                              </label>
                              <input class="_style_3vhmZK" name="card_number" style="background: white" value="" placeholder="4242 4242 4242 4242">
                            </div>
                             <div style="display: flex; flex-direction: column; width: 20%; ">
                              <label style="padding:2px 0px;">
                               Last 4 <em class="text-danger">*</em>
                              </label>
                              <input class="_style_3vhmZK" name="ssn_last_4" style="background: white" value="" placeholder="">
                            </div>
                          </div>
                </div>
               <button style="    padding: 0px 40px !important; width: 12em; margin: auto;
          font-size: 12px !important;" type="submit" class="btn btn--primary btn-blue" id="update_btn">{{trans('messages.user.update')}}</button>
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

    $("#addNewCard").click(function() {
      $("#cardModalWrp").fadeIn(200).css("display", "flex");
    })
    $("#cardModalWrp").click(function(e) {
      if(!$(e.target).is("#cardModal") && !$(e.target).parents("#cardModal").length)
        $("#cardModalWrp").fadeOut(200);
    })
    $("#cardModal img").click(function() {
      $("#cardModal .currentImg").removeClass("currentImg");
      $(this).addClass("currentImg");
    })
    $("#profileLeftWrp span").click(function() {
       $("#profileRightWrp > div.current").removeClass("current");
       $("#profileRightWrp > div[data-tab='" + $(this).data("tab") + "']").addClass("current");
      $("#profileLeftWrp span.current").removeClass("current");
      $(this).addClass("current");

    })
  });
</script>