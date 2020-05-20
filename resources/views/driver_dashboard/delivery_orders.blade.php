<title>Edit Profile</title>
@extends('template_driver_dashboard_new') 
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px !important;" ng-controller="facebook_account_kit">
  @include('common.driver_dashboard_header_new')
  <div id="deliveryMainWrp">
           <div style="display: flex; justify-content: space-between;width: 100%">
              <span style="font-size: 200%; color: #1B187F; font-weight: normal; font-family:'MontserratBold'">Delivery Orders</span>
              <div style="display: flex" id="deliveryKmWrp">
                <span style="border-top-left-radius: 7px; border-bottom-left-radius: 7px; color: #1B187F; font-weight: bold; font-family: 'MontserratBold'">5 km</span>
                <span>15 km</span>
                <span style="border-top-right-radius: 7px; border-bottom-right-radius: 7px; ">25 km</span>
              </div>
            </div>
            <div style="display: flex; flex-direction: column; width: 100%" id="deliverySubWrp">
              <div>
                <span>Date & Time</span>
                <span>Pick up</span>
                <span>Est. distance</span>
                <span>Time</span>
                <span>Fee</span>
                <span style="width: 8%;text-align: center;">Action</span>
              </div>
               <div>
                <span>5 Apr. 2020 | 20:00</span>
                <span>Funky, Thai, Keilor</span>
                <span>3km</span>
                <span>Now</span>
                <span>$ 30.00 USD</span>
                <span class="acceptBtn1">Accept</span>
              </div>
              <div>
                <span>5 Apr. 2020 | 20:00</span>
                <span>Funky, Thai, Keilor</span>
                <span>3km</span>
                <span>Now</span>
                <span>$ 30.00 USD</span>
                <span class="acceptBtn1">Accept</span>
              </div>
              <div>
                <span>5 Apr. 2020 | 20:00</span>
                <span>Funky, Thai, Keilor</span>
                <span>3km</span>
                <span>Now</span>
                <span>$ 30.00 USD</span>
                <span class="acceptBtn1">Accept</span>
              </div>
              <div>
                <span>5 Apr. 2020 | 20:00</span>
                <span>Funky, Thai, Keilor</span>
                <span>3km</span>
                <span>Now</span>
                <span>$ 30.00 USD</span>
                <span class="acceptBtn1">Accept</span>
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