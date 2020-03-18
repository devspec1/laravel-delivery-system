    var map;
    var ajaxMarker = [];
    var googleMarker = [];
    var mapIcons = {
        Active: APP_URL+'/images/marker_green.png',
        Online: APP_URL+'/images/marker_dgreen.png',
        Trip: APP_URL+'/images/marker_dgreen.png',
        Offline: APP_URL+'/images/marker_pink.png',
        Inactive: APP_URL+'/images/marker_pink_plus.png',
    }


      function initMap() {
        var mapCanvas = document.getElementById('map');
        
        if(!mapCanvas){
            return false;
        }
        var mapOptions = {
           zoom: 2,
           minZoom: 1,
           zoomControl: true,
           center:{lat: 0, lng: 0},
           mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        map = new google.maps.Map(mapCanvas, mapOptions);

        setInterval(ajaxMapData, 100000);
        ajaxMapData();

      }

function ajaxMapData() {
  clearOverlays();
  $.ajax({
      url: COMPANY_ADMIN_URL+'/mapdata',
      dataType: "JSON",
      type: "GET",
      success: function(data) {
          ajaxMarker = data;
          if (ajaxMarker.length != 0) {
               angular.forEach(ajaxMarker, function(value, key){
                var icon_img = value.status;
                if(value.status != 'Inactive')
                {
                if(value.user_type == 'Driver' )
                {
                    if(value.status== 'Active' && value.driver_location != null)
                    {
                      var icon_img = value.driver_location.status;
                    }
                    else if(value.status!= 'Active') {
                      var icon_img = 'Inactive';    
                    }
                }
                else
                {
                  if(value.rider_location != null && value.status == 'Active') 
                  {
                    var icon_img = 'Active';
                  }
                }
                  var icon = {
                          url: mapIcons[icon_img], // url
                          scaledSize: new google.maps.Size(23, 30), // scaled size
                          origin: new google.maps.Point(0,0), // origin
                          anchor: new google.maps.Point(0, 0) // anchor
                      };

                   marker = new google.maps.Marker({
                      position: {
                          lat: parseFloat(value.latitude),
                          lng: parseFloat(value.longitude)
                      },
                      id: value.id,
                      map: map,
                      title: value.first_name + " " +value.last_name,
                      icon : icon,
                  });

                  googleMarker.push(marker);
                  google.maps.event.addListener(marker, 'click', function() {
                    var html ='<span class="close_user_details"><i class="fa fa-times"></i></span>'; 
                    html += '<div class="user_background col-md-3">';    
                    html +='<img src="'+value.profile_picture.src+'" class="img-circle"></div>';
                    html +='<div class="user_details col-md-9">';
                    html +='<h3 class="text-capitalize">'+value.first_name + " " +value.last_name+' ('+value.user_type+')</h3> ';
                    if(LOGIN_USER_TYPE == 'admin') {
                      html +='<p class="text-capitalize">'+value.company_name +'</p> ';
                    }
                    html +='<p title="'+value.email+'"><i class="fa fa-envelope" aria-hidden="true"></i> : <span class="sety">'+value.email+'</span></p>';
                    html +='<p title="'+value.hidden_mobile_number+'"><i class="fa fa-phone" aria-hidden="true"></i> : <span class="sety">'+value.hidden_mobile_number+'</span></p>';
                    html +='</div>';
                  $('#user_details').show();

                  $('#user_details').html(html);
                      
                  });
                }
               });
            }
      }
  });  
}

function clearOverlays()
{
  for (var i = 0; i < googleMarker.length; i++ ) {
   googleMarker[i].setMap(null);
  }
  googleMarker.length = 0;
}

$(document).on("click",".close_user_details",function(){
  $('#user_details').hide();
})