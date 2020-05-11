<title>Edit Profile</title>
@extends('template_driver_dashboard_new') 
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px !important;" ng-controller="facebook_account_kit">
  @include('common.driver_dashboard_header_new')
  <div id="deliveryMainWrp">
           <div style="display: flex;width: 100%">
              <span style="font-size: 200%; color: #1B187F; font-weight: normal; font-family:'MontserratBold'">Delivery</span>
              
            </div>
            <div style="display: flex; flex-direction: column; width: 100%" id="deliverySubWrp">
              <div class="deliverySubHeader">
                <span>Order Completed</span>
                <span>Venue</span>
                <span>Address</span>
                <span>Driver Name</span>
                <span>Total</span>
               
              </div>
               <div>
                <div> <span>04/02/2020 | 11:25</span> <span> Order No. 1246098 </span> </div>
               
                <span>The Ants Cafe, 2259 14A</span>
                <span>The Ants Cafe, 2259 14A</span>
               <span>Joe Corrigan</span>
                <span>$76.00</span>
                
              </div>
              <div>
                <div> <span>04/02/2020 | 11:25</span> <span> Order No. 1246098 </span> </div>
               
                <span>The Ants Cafe, 2259 14A</span>
                <span>The Ants Cafe, 2259 14A</span>
               <span>Joe Corrigan</span>
                <span>$76.00</span>
                
              </div>
              <div>
                <div> <span>04/02/2020 | 11:25</span> <span> Order No. 1246098 </span> </div>
               
                <span>The Ants Cafe, 2259 14A</span>
                <span>The Ants Cafe, 2259 14A</span>
               <span>Joe Corrigan</span>
                <span>$76.00</span>
                
              </div>
              <div>
                <div> <span>04/02/2020 | 11:25</span> <span> Order No. 1246098 </span> </div>
               
                <span>The Ants Cafe, 2259 14A</span>
                <span>The Ants Cafe, 2259 14A</span>
               <span>Joe Corrigan</span>
                <span>$76.00</span>
                
              </div>
              <div>
                <div> <span>04/02/2020 | 11:25</span> <span> Order No. 1246098 </span> </div>
               
                <span>The Ants Cafe, 2259 14A</span>
                <span>The Ants Cafe, 2259 14A</span>
               <span>Joe Corrigan</span>
                <span>$76.00</span>
                
              </div>
              <div>
                <div> <span>04/02/2020 | 11:25</span> <span> Order No. 1246098 </span> </div>
               
                <span>The Ants Cafe, 2259 14A</span>
                <span>The Ants Cafe, 2259 14A</span>
               <span>Joe Corrigan</span>
                <span>$76.00</span>
                
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