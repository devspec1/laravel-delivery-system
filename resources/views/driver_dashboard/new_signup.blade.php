@extends('template_without_header_footer') 
@section('main')
<style> body, html { height: 100%; }</style>
<div id="newSignupWrp" style="height: 100%; width: 100%; display: flex; justify-content: center; align-items: center">
    <div class="newSignupWrp1 current" style="height: 90%; width: 30%; padding: 1em; padding-top: 4em; padding-bottom: 4em; display: flex; flex-direction: column; align-items: center; justify-content: space-between; box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.25); font-family: 'MontserratReg">

        <img src="{{ asset('images/logos/logo.png') }}" style="height: 8em">
        <img src="{{ asset('images/car_image/car4.png') }}" style="height: 15em">

        <div style="display: flex; flex-direction: column; width: 100%; margin-bottom: 5.5em; align-items: center"> <div style="display: flex; flex-direction: column; margin-top: 0.4em;margin-bottom: 0.9em;padding: 0.75em; width: 75%; background: rgba(0, 0, 0, 0.02); border:1px solid rgba(0, 0, 0, 0.12)">
            <span style="opacity: 0.5; font-family: 'MontserratReg'">Vehicle name  / eg: Toyoto Camry, Honda Accord...</span>
            <input style="border: none; background: transparent; font-size: 115%; font-weight: bold" type="text" placeholder="Volga Gaz"> </div>
        <div style="display: flex; flex-direction: column;  margin-top: 0.4em;margin-bottom: 0.9em;padding: 0.75em; width: 75%; background: rgba(0, 0, 0, 0.02); border:1px solid rgba(0, 0, 0, 0.12)">
            <span style="opacity: 0.5; font-family: 'MontserratReg'">Vehicle number / eg: WNF 382</span>
            <input style="border: none; background: transparent; font-size: 115%; font-weight: bold" type="text" placeholder="0123po"> </div>
        </div>
        <span class="spanB1 spanNext" style="background-color: #3B5998;">Continue <img src="{{ asset('images/icon/next.png') }}" style="position: relative; left: 5em;  height: 1.2em; width: 1.2em"></span>

        

    </div>

    <div class="newSignupWrp2" style="height: 90%; width: 30%; padding: 1em; padding-top: 1em; padding-bottom: 4em; display: none; flex-direction: column; align-items: center; justify-content: space-between; box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.25); font-family: 'MontserratReg">

        <img src="{{ asset('images/logos/logo.png') }}" style="height: 8em">

        <div style="margin:50px;margin-bottom: 0.5em; margin-top: 1em; width:60%;height:50px;z-index: 1000">
  <div style="display: flex; justify-content: space-between; width:auto; height:2px;background:rgba(0, 0, 0, 0.05);position:relative;top:5px;">
    <div style="float:left;width:27px;height:27px;background:#3B5998;border-radius:50%;position:relative;top:-14px;"></div>
    <div style="float:left;width:27px;height:27px;z-index:1001; background:rgba(0, 0, 0, 0.5);border-radius:50%;position:relative;top:-14px;;"></div>
    <div style="float:left;width:27px;height:27px;background:rgba(0, 0, 0, 0.5);border-radius:50%;position:relative;top:-14px;"></div>
    <div style="float:left;width:27px;height:27px;background:rgba(0, 0, 0, 0.5);border-radius:50%;position:relative;top:-14px;"></div>
   
  </div>
</div>
        <h1 style="font-family: 'MontserratReg'; font-weight: bold; font-size: 180%; width: 75%;margin-bottom: 0; text-align:left">Create a New Account</h1>
         <div style="display: flex; flex-direction: column; align-items: center; width: 100%"> <div style="display: flex; justify-content: space-between;width:75%">

            <div class="inputWrp" style="display: flex; align-items: center; margin-top: 0.4em;margin-bottom: 0.3em;padding: 0.9em; width: 47%; background: rgba(0, 0, 0, 0.02); border:1px solid rgba(0, 0, 0, 0.12)"><input  type="text" placeholder="First Name"> </div>
            <div class="inputWrp" style="display: flex; align-items: center; margin-top: 0.4em;margin-bottom: 0.3em;padding: 0.9em; width: 47%; background: rgba(0, 0, 0, 0.02); border:1px solid rgba(0, 0, 0, 0.12)"><input  type="text" placeholder="Last Name"> </div>
        </div>
        <div class="inputWrp"><input  type="text" placeholder="Email"> </div>
         <div style="display: flex; justify-content: space-between;width:75%">
            <div class="inputWrp" style="width: 14%"><input  type="text" value="+1"> </div>
            <div class="inputWrp" style="width: 84%" ><input type="text">  <img src="{{ asset('images/au_flag.png')}}" style=" width: 1.75em"></div>
        </div>
          <div class="inputWrp"><input  type="text" placeholder="Password"> </div>

           <div class="inputWrp"><input  type="text" placeholder="Date of birth"> <img src="{{ asset('images/icon/calendar.png')}}" style=" width: 1.5em; opacity: 0.5"> </div>
            <div class="inputWrp"><input  type="text" placeholder="Referral Code"> </div>
        </div>

        <div style="display: flex; flex-direction: column; align-items: center; width: 100%"> 
     
        <span class="spanB1 spanNext" style="background-color: #3B5998;">Next <img src="{{ asset('images/icon/next.png') }}" style="position: relative; left: 9em;  height: 1.4em; width: 1.4em"></span>

        
        <span class="spanB1" style="background-color: #4B4B4B;r">Already have an account ?</span>
      </div>

    </div>

     <div class="newSignupWrp2" style="height: 90%; width: 30%; padding: 1em; padding-top: 1em; padding-bottom: 4em; display: none; flex-direction: column; align-items: center; justify-content: space-between; box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.25); font-family: 'MontserratReg">

        <img src="{{ asset('images/logos/logo.png') }}" style="height: 8em">

        <div style="margin:50px;margin-bottom: 0.2em; margin-top: 1.6em; width:60%;height:50px;z-index: 1000">
  <div style="display: flex; justify-content: space-between; width:auto; height:2px;background:rgba(0, 0, 0, 0.05);position:relative;top:5px;">
    <div style="float:left;width:27px;height:27px;background:#3B5998;border-radius:50%;position:relative;top:-14px;"></div>
    <div style="float:left;width:27px;height:27px;z-index:1001; background: #3B5998;border-radius:50%;position:relative;top:-14px;;"></div>
    <div style="float:left;width:27px;height:27px;background:rgba(0, 0, 0, 0.5);border-radius:50%;position:relative;top:-14px;"></div>
    <div style="float:left;width:27px;height:27px;background:rgba(0, 0, 0, 0.5);border-radius:50%;position:relative;top:-14px;"></div>
   
  </div>
</div>
        <h1 style="font-family: 'MontserratReg'; font-weight: bold; font-size: 180%; width: 75%;margin-bottom: 0.6em; text-align:left">Address</h1>
         
         <div class="inputWrp"><input  type="text" placeholder=""><img src="{{ asset('images/icon/gps.png')}}" style=" width: 1.4em"> </div>
         <div class="inputWrp"><input  type="text" placeholder="Street Address Line 1"> </div>
        <div class="inputWrp"><input  type="text" placeholder="Street Address Line 2"> </div>
        <div class="inputWrp"><input  type="text" placeholder="City"> </div>
        <div class="inputWrp"><input  type="text" placeholder="State"> </div>
        <div class="inputWrp"><input  type="text" placeholder="Post Code"> </div>
        
          

        <div style="display: flex; flex-direction: column; align-items: center; margin-top: 0.7em; width: 100%"> 
     
        <span class="spanB1 spanNext" style="background-color: #3B5998;">Next <img src="{{ asset('images/icon/next.png') }}" style="position: relative; left: 9em;  height: 1.4em; width: 1.4em"></span>

        
        <span class="spanB1" style="background-color: #4B4B4B;r">Already have an account ?</span>
      </div>

    </div>

     <div class="newSignupWrp2" style="height: 90%; width: 30%; padding: 1em; padding-top: 1em; padding-bottom: 4em; display: none; flex-direction: column; align-items: center; justify-content: space-between; box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.25); font-family: 'MontserratReg">

        <img src="{{ asset('images/logos/logo.png') }}" style="height: 8em">

        <div style="margin:50px;margin-bottom: 0.2em; margin-top: 1.6em; width:60%;height:50px;z-index: 1000">
              <div style="display: flex; justify-content: space-between; width:auto; height:2px;background:rgba(0, 0, 0, 0.05);position:relative;top:5px;">
                <div style="float:left;width:27px;height:27px;background:#3B5998;border-radius:50%;position:relative;top:-14px;"></div>
                <div style="float:left;width:27px;height:27px;z-index:1001; background: #3B5998;border-radius:50%;position:relative;top:-14px;;"></div>
                <div style="float:left;width:27px;height:27px;background:#3B5998;border-radius:50%;position:relative;top:-14px;"></div>
                <div style="float:left;width:27px;height:27px;background:rgba(0, 0, 0, 0.5);border-radius:50%;position:relative;top:-14px;"></div>
               
              </div>
            </div>
        <h1 style="font-family: 'MontserratReg'; font-weight: bold; font-size: 180%; width: 75%;margin-bottom: 0.6em; text-align:left">Upload Your Profile Photo</h1>
         
         <div style="display: flex; flex-direction: column; align-items: center; width: 75%; margin-top: 1.5em; height: 70%">

            <div style="display: flex; flex-direction: column; padding: 1.6em; background: rgba(0, 0, 0, 0.02); border: 2px dotted #3B5998; align-items: center; width: 100%">
                <img src="{{ asset('images/icon/cloud_up.png')}}" style="height: 2.5em; width: 2.5em">
                <span style="font-size: 140%; font-weight: bold; margin-top: 0.3em; font-family: 'MontserratReg'; opacity: 0.35"> Drag and drop file here </span>
                <span style="font-size: 80%; font-weight: bold; font-family: 'MontserratReg'; opacity: 0.35"> Max size 10MB</span>
            </div>

            <div class="inputWrp" style="justify-content: space-between; padding: 0.7em;padding-left: 2em;padding-right: 2em; border-radius: 7px; margin-top: 0.7em; width: 100%">
                <div style="display: flex; flex-direction: column;">
                    <span style="font-weight: bold"> test.png</span>
                    <span style="font-size: 80%; opacity: 0.5"> 120kb</span>
                </div>
                <img src="{{ asset('images/icon/blue_check.png')}}" style="height: 1.5em; width: 1.5em">
            </div>


        </div>
        
          

        <div style="display: flex; flex-direction: column; align-items: center; margin-top: 0.7em; width: 100%"> 
     
        <span class="spanB1 spanNext" style="background-color: #3B5998;">Next <img src="{{ asset('images/icon/next.png') }}" style="position: relative; left: 9em;  height: 1.4em; width: 1.4em"></span>

        
        <span class="spanB1" style="background-color: #4B4B4B;r">Already have an account ?</span>
      </div>

    </div>

      <div class="newSignupWrp2" style="height: 87%; width: 30%; padding: 1em; padding-top: 1em; padding-bottom: 4em; display: none; flex-direction: column; align-items: center; justify-content: space-between; box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.25); font-family: 'MontserratReg">

        <img src="{{ asset('images/logos/logo.png') }}" style="height: 8em">

        <div style="margin:50px;margin-bottom: 0.2em; margin-top: 1.6em; width:60%;height:50px;z-index: 1000">
              <div style="display: flex; justify-content: space-between; width:auto; height:2px;background:rgba(0, 0, 0, 0.05);position:relative;top:5px;">
                <div style="float:left;width:27px;height:27px;background:#3B5998;border-radius:50%;position:relative;top:-14px;"></div>
                <div style="float:left;width:27px;height:27px;z-index:1001; background: #3B5998;border-radius:50%;position:relative;top:-14px;;"></div>
                <div style="float:left;width:27px;height:27px;background:#3B5998;border-radius:50%;position:relative;top:-14px;"></div>
                <img style="float:left;width:27px;height:27px;position:relative;top:-14px;" src="{{asset('images/icon/blue_check.png')}}">
               
              </div>
            </div>
        <h1 style="font-family: 'MontserratReg'; font-weight: bold; font-size: 180%; width: 75%;margin-bottom: 0.6em; text-align:left">Vehicle Type</h1>
         
         <div style="display: flex; flex-direction: column; align-items: center; width: 75%; margin-top: 1.5em; ">
            <div style="display: flex; width: 100%;  justify-content: space-between;">
                <div class="inputWrp" style="justify-content: space-between;width: 47%"><span> Car (Sedan)</span><img src="{{ asset('images/icon/gray_check.png')}}" style="height: 1.1em; width: 1.1em"></div>
                <div class="inputWrp" style="justify-content: space-between;width: 47%"><span> Car (SUV)</span><img src="{{ asset('images/icon/blue_check.png')}}" style="height: 1.1em; width: 1.1em"></div>
            </div>
            <div style="display: flex; width: 100%;  justify-content: space-between;">
                <div class="inputWrp" style="justify-content: space-between;width: 47%"><span> Car (Premium)</span><img src="{{ asset('images/icon/gray_check.png')}}" style="height: 1.1em; width: 1.1em"></div>
                <div class="inputWrp" style="justify-content: space-between;width: 47%"><span> XL /People Mover</span><img src="{{ asset('images/icon/gray_check.png')}}" style="height: 1.1em; width: 1.1em"></div>
            </div>
            <div style="display: flex; width: 100%;  justify-content: space-between;">
                <div class="inputWrp" style="justify-content: space-between;width: 47%"><span> Motorcycle </span><img src="{{ asset('images/icon/gray_check.png')}}" style="height: 1.1em; width: 1.1em"></div>
                <div class="inputWrp" style="justify-content: space-between;width: 47%"><span> Bicycle </span><img src="{{ asset('images/icon/gray_check.png')}}" style="height: 1.1em; width: 1.1em"></div>
            </div>
            <div style="display: flex; width: 100%;  justify-content: flex-start;">
                <div class="inputWrp" style="justify-content: space-between;width: 47%"><span> Other </span><img src="{{ asset('images/icon/gray_check.png')}}" style="height: 1.1em; width: 1.1em"></div>
              
            </div>
        

        </div>
         <h1 style="font-family: 'MontserratReg'; font-weight: bold; font-size: 180%; width: 75%;margin-bottom: 0.6em; text-align:left">Your ABN</h1>
          <div class="inputWrp"><input type="text"></div>

        <div style="display: flex; flex-direction: column; align-items: center; margin-top: 0.7em; width: 100%"> 
     
        <span class="spanB1 spanNext" style="background-color: #3B5998;">Finish<img src="{{ asset('images/icon/next.png') }}" style="position: relative; left: 9em;  height: 1.4em; width: 1.4em"></span>
        <span style="width: 75%; opacity: 0.9; font-size: 90%; margin-top: 1em">By proceeding, I agree that RideOn Australia or its representatives may contact me by email, phone, or SMS (including by automated means) at the email address or number I provide, including for marketing purposes.</span>
      </div>

    </div>
</div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>

<script>

    $(".spanNext").click(function() {
        var d = $(this).parents(".current").next("div");
        $(this).parents(".current").removeClass("current").hide();
        d.addClass("current").css("display", "flex").show();
    })
</script>
@stop