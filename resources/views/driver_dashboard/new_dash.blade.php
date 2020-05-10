<title>Edit Profile</title>
@extends('template_driver_dashboard_new') 
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px !important;" ng-controller="facebook_account_kit">
  @include('common.driver_dashboard_header_new')
  
 
  <div id="newDashMainWrp">
      <div> 
        <div style="display: flex; flex-direction: column;  border-radius: 9px; border: 1px solid rgba(0, 0, 0, 0.1); box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.25);  font-size: 100%">
            <span style="opacity: 0.9; margin-top: 2em; font-size: 108%; margin-bottom: 2em; margin-left: 1em; font-family: 'MontserratReg'; font-weight: bold">Top performers</span>
            <div style="display: flex; justify-content: flex-start; align-items: center;  width: 100%; padding-left: 1.5em;padding-right: 1.5em; padding-top: 0.85em; padding-bottom: 0.85em; border-bottom: 1px solid rgba(0, 0, 0, 0.10);border-top: 1px solid rgba(0, 0, 0, 0.10)">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 3.5em; width: 3.5em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
              <div class="newDashSubItemRow">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 3.5em; width: 3.5em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
              <div class="newDashSubItemRow">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 3.5em; width: 3.5em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
              <div class="newDashSubItemRow">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 3.5em; width: 3.5em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
            <div class="newDashSubItemRow">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 3.5em; width: 3.5em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
              <div class="newDashSubItemRow">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 3.5em; width: 3.5em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
              <div class="newDashSubItemRow">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 3.5em; width: 3.5em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
              <div class="newDashSubItemRow">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 3.5em; width: 3.5em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
            <div style="display: flex; justify-content: center; padding-top: 0.7em; padding-bottom: 0.7em; align-items: center"><span style="color: #17609c; font-size: 100%; font-family: 'MontserratReg'; font-weight: bold">Expand</span></div>
        </div>
      </div>
      <div>
        <div class="newDashCardWrp">
          <span style="font-size: 110%; font-weight: bold">Deliveries</span>
          <div style="display: flex; flex-direction: column; align-items: center">
            <b style="font-size: 350%; opacity: 0.65; font-family: 'MontserratBold'">3</b>
            <span style="font-size: 90%; opacity: 0.8">+1 this week</span>
          </div>
          <span class="link1">Details</span>
        </div>
      </div>
      <div>
         <div class="newDashCardWrp">
          <span style="font-size: 110%; font-weight: bold">Drive Team</span>
          <div style="display: flex; flex-direction: column; align-items: center">
            <b style="font-size: 350%;opacity: 0.65;  font-family: 'MontserratBold';">8</b>
            <span style="font-size: 90%; opacity: 0.8">+1 this week</span>
          </div>
          <span class="link1">Details</span>
        </div>

         <div style="display: flex; flex-direction: column;  align-items:center; border-radius: 9px; border: 1px solid rgba(0, 0, 0, 0.1); box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.25); margin-top: 1.5em; font-size: 87%">
            <div class="newDashSubItemRow">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 2.6em; width: 2.6em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
              <div class="newDashSubItemRow">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 2.6em; width: 2.6em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
              <div class="newDashSubItemRow">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 2.6em; width: 2.6em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
              <div class="newDashSubItemRow">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 2.6em; width: 2.6em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
            <div style="display: flex; justify-content: center; padding-top: 0.7em; padding-bottom: 0.7em; align-items: center"><img style="width: 1.3em; height: 1.3em" src="{{ asset('images/icon/down-arrow.png') }}"></div>
        </div>
      </div>
      <div>
        <div class="newDashCardWrp">
          <span style="font-size: 110%; font-weight: bold">Merchants</span>
          <div style="display: flex; flex-direction: column; align-items: center">
            <b style="font-size: 350%;opacity: 0.65;  font-family: 'MontserratBold'">8</b>
            <span style="font-size: 90%; opacity: 0.8">+1 this week</span>
          </div>
          <span class="link1">Details</span>
        </div>

         <div style="display: flex; flex-direction: column;  align-items:center; border-radius: 9px; border: 1px solid rgba(0, 0, 0, 0.1); box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.25); margin-top: 1.5em; font-size: 87%">
            <div class="newDashSubItemRow">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 2.6em; width: 2.6em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
              <div class="newDashSubItemRow">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 2.6em; width: 2.6em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
              <div class="newDashSubItemRow">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 2.6em; width: 2.6em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
              <div class="newDashSubItemRow">
              <img class="randomPic" src="http://127.0.0.1:8000/images/user.jpeg" style="height: 2.6em; width: 2.6em; border-radius: 50%; margin-right: 0.9em">
              <div style="display: flex; flex-direction: column;">
                <b>Joe Corrigan</b>
                <span style="opacity: 0.5">Brisbane, Qld</span>
              </div>
            </div>
            <div style="display: flex; justify-content: center; padding-top: 0.7em; padding-bottom: 0.7em; align-items: center"><img style="width: 1.3em; height: 1.3em" src="{{ asset('images/icon/down-arrow.png') }}"></div>
        </div>
      </div>


  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.0/jquery.min.js"></script>
<script>
  $(function() {
    $.ajax({
  url: 'https://randomuser.me/api?results=50&gender=male',
  dataType: 'json',
  success: function(data) {
    data = data.results;
    for(d in data) {
      var item = data[d];
      if($(".randomPic").length) {
        var rp = $(".randomPic").eq(0);
        rp.attr("src", item.picture.thumbnail).removeClass("randomPic");

      }
      else if($(".randomPicLarge").length) {
        $(".randomPicLarge").eq(0).attr("src", item.picture.large).removeClass("randomPicLarge");
      }
    }
  }
});
  })
</script>
</main>
@stop