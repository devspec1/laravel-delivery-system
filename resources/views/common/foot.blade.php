  <div class="alert cookie-alert alert-dismissible" style="display:none">
    <a href="#" class="close close_cookie-alert" data-dismiss="alert" aria-label="close">&times;</a>
    <p>
      {{trans('messages.footer.using_cookies',['site_name'=>$site_name])}} <a href="{{url('privacy_policy')}}">{{trans('messages.user.privacy')}}.</a>
    </p>
  </div>
  {!! Html::script('js/jquery-1.11.3.js') !!}
  {!! Html::script('js/jquery-ui.js') !!}

  {!! Html::script('js/angular.js') !!}
  {!! Html::script('js/angular-sanitize.js') !!}
  <script>
    var app = angular.module('App', ['ngSanitize']);
    var APP_URL = {!! json_encode(url('/')) !!};
    var LOGIN_USER_TYPE = '{!! LOGIN_USER_TYPE !!}';
    var STRIPE_PUBLISH_KEY = "{{ payment_gateway('publish','Stripe') }}";
  </script>

  {!! Html::script('js/common.js?v='.$version) !!}
  {!! Html::script('js/user.js?v='.$version) !!}
  {!! Html::script('js/main.js?v='.$version) !!}
  {!! Html::script('js/bootstrap.min.js') !!}
  {!! Html::script('js/jquery.bxslider.min.js') !!}
  {!! Html::script('js/jquery.sliderTabs.min.js') !!}
  {!! Html::script('js/responsiveslides.js?v='.$version) !!}

  {!! $head_code !!}

  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{ $map_key }}&libraries=places"></script>
  
  <script type="text/javascript">
    if(document.getElementById("map_canvas")){
  	 google.maps.event.addDomListener(window, 'load', initialize);
    }
    function initialize()
    {
      var map;
      // Set the latitude & longitude for our location (London Eye)
      var myLatlng = new google.maps.LatLng(51.503454,-0.119562);
      var mapOptions = {
          center: myLatlng, // Set our point as the centre location
          zoom: 14, // Set the zoom level
          mapTypeId: 'roadmap' // set the default map type
      };

      // Create a styled map
      // Create an array of styles.
      var styles = [{
          "stylers": [
              { "saturation": -100 },
              { "lightness": 40 }
          ]
      }];

      // Create a new StyledMapType object, passing it the array of styles,
      // as well as the name to be displayed on the map type control.
      var styledMap = new google.maps.StyledMapType(styles, {name: "Styled Map"});
            
      // Display a map on the page
      map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
      // Allow our satellite view have a tilted display (This only works for certain locations) 
      map.setTilt(45);

      //Associate the styled map with the MapTypeId and set it to display.
      map.mapTypes.set('map_style', styledMap);
      map.setMapTypeId('map_style');
    }
  </script>

  <!-- Start Display Cookie Alert in foot -->
  <script type="text/javascript">
    $(document).on('click','.cookie-alert .close_cookie-alert',function() {
        writeCookie('status','1',10);
      })

    var getCookiebyName = function(){
      var pair = document.cookie.match(new RegExp('status' + '=([^;]+)'));
      var result = pair ? pair[1] : 0;  
      $('.cookie-alert').show();
      if(result) {
        $('.cookie-alert').hide();
        return false;
      }
    };

    var url = window.location.href;
    var arr = url.split("/");
    var result = arr[0] + "//" + arr[2];
    var domain =  result.replace(/(^\w+:|^)\/\//, '');

    writeCookie = function(cname, cvalue, days) {
      var dt, expires;
      dt = new Date();
      dt.setTime(dt.getTime()+(days*24*60*60*1000));
      expires = "; expires="+dt.toGMTString();
      document.cookie = cname+"="+cvalue+expires+'; domain='+domain;
    }

    getCookiebyName();

</script>
<!-- End Display Cookie Alert in foot -->

@if (Route::current()->uri() == 'trip' || Route::current()->uri() == 'driver_trip' || Route::current()->uri() == 'driver_invoice')
  {!! Html::script('js/trip.js?v='.$version) !!}
@endif
@if (Route::current()->uri() == 'driver_payment' || Route::current()->uri() == 'documents/{id}')
  {!! Html::script('js/payment.js?v='.$version) !!}
@endif

@if (Route::current()->uri() == 'signup_rider' || Route::current()->uri() == 'signup_driver' || Route::current()->uri() == 'signup_company' || Route::current()->uri() == 'driver_profile'|| Route::current()->uri() == 'driver/edit_profile')

<script src="{{url('js/accountkit.js?v='.$version)}}"></script>
@endif

@if (Route::current()->uri() == 'signin_rider')
<script type="text/javascript">
  var GOOGLE_CLIENT_ID  = "{{GOOGLE_CLIENT_ID}}";
</script>
<script src="https://apis.google.com/js/api:client.js"></script>   
<script src="{{url('js/googleapilogin.js?v='.$version)}}"></script>
@endif

@stack('scripts')