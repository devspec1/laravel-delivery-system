var map;
var ajaxMarker = [];
var googleMarker = [];
ajaxMapData();
function initMap(heatMapData) {
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

  heatmap = new google.maps.visualization.HeatmapLayer({
    data: heatMapData,
  });

  heatmap.setMap(map);
  // setInterval(ajaxMapData, 3000);
}

function ajaxMapData() {
  var heatMapData = [];
  $.ajax({
      url: COMPANY_ADMIN_URL+'/heat-map-data',
      dataType: "JSON",
      type: "GET",
      success: function(data) {
          for (var i = 0; i <= data.length - 1; i++) {
            if (data[i].weight>1) {
              heatMapData[i] = {location: new google.maps.LatLng(data[i].pickup_latitude, data[i].pickup_longitude), weight: data[i].weight};
            }else{
              heatMapData[i] = new google.maps.LatLng(data[i].pickup_latitude, data[i].pickup_longitude);
            }
          }
          initMap(heatMapData)
      }
  }); 
}