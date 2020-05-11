<title>Edit Profile</title>
@extends('template_driver_dashboard_new') 
@section('main')
<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 flexbox__item four-fifths page-content" style="padding:0px !important;" ng-controller="facebook_account_kit">
  @include('common.driver_dashboard_header_new')
  
 
  <div id="homeMainWrp">
      <div style="width: 100%; display: flex; justify-content: space-between;">
          <div class="newDashCardWrp">
          <span style="font-size: 130%; font-weight: bold">Deliveries</span>
          <div style="display: flex; flex-direction: column; align-items: center">
            <b style="font-size: 400%; margin-top: 0.2em; margin-bottom: 0.2em; font-family: 'MontserratBold'">3</b>
            <span style="font-size: 100%; ">+1 this week</span>
          </div>
          
        </div>
        <div class="newDashCardWrp">
          <span style="font-size: 130%; font-weight: bold">Drive Team</span>
          <div style="display: flex; flex-direction: column; align-items: center">
            <b style="font-size: 400%; margin-top: 0.2em; margin-bottom: 0.2em; font-family: 'MontserratBold'">8</b>
            <span style="font-size: 100%; ">+1 this week</span>
          </div>
          
        </div>
        <div class="newDashCardWrp">
          <span style="font-size: 130%; font-weight: bold">Merchants</span>
          <div style="display: flex; flex-direction: column; align-items: center">
            <b style="font-size: 400%; margin-top: 0.2em; margin-bottom: 0.2em; font-family: 'MontserratBold'">8</b>
            <span style="font-size: 100%; ">+1 this week</span>
          </div>
          
        </div>
      </div>
      <iframe id="ytplayer" type="text/html" style="width: 100%; height: 140%; margin: auto; margin-top: 1em"
    src="https://www.youtube.com/embed/aE5jrQs6P-0?autoplay=1&origin=http://example.com"
    frameborder="0"/>
      
    


  
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