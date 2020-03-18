function initMap()
{
	var trip_image = document.getElementById('trip_image');
	
	if(trip_image != undefined) {
		var trip_src = trip_image.getAttribute('data-original_src');
		if(trip_src != '') {
			return true;
		}
	}

	var mapCanvas = document.getElementById('map');

	if(!mapCanvas) {
		return false;
	}

	var mapOptions = {
		minZoom: 3,
		zoomControl: true,
	};

	var map = new google.maps.Map(mapCanvas, mapOptions);

	var marker = new google.maps.Marker({
		map: map,
		icon: APP_URL+'/images/point_circle.png',
		scaledSize: new google.maps.Size(23, 30),
		anchorPoint: new google.maps.Point(0, -29)
	});

	var markerSecond = new google.maps.Marker({
		map: map,
		icon: APP_URL+'/images/point.png',
		scaledSize: new google.maps.Size(23, 30),
		anchorPoint: new google.maps.Point(0, -29)
	});

	var pickup_latitude  = parseFloat(document.getElementById('pickup_latitude').value);
	var pickup_longitude = parseFloat(document.getElementById('pickup_longitude').value);
	var drop_latitude    = parseFloat(document.getElementById('drop_latitude').value);
	var drop_longitude   = parseFloat(document.getElementById('drop_longitude').value);

	var bounds = new google.maps.LatLngBounds();

	start = new google.maps.LatLng(pickup_latitude,pickup_longitude);
	end = new google.maps.LatLng(drop_latitude,drop_longitude);

	marker.setPosition(start);
	markerSecond.setPosition(end);

	path = trip_path.value;

	var polyline = new google.maps.Polyline({
		path: google.maps.geometry.encoding.decodePath(trip_path.value),
		map: map
	});

	var bounds = new google.maps.LatLngBounds();
	for (var i = 0; i < polyline.getPath().getLength(); i++) {
		bounds.extend(polyline.getPath().getAt(i));
	}
	map.fitBounds(bounds);
	mapCanvas.classList.remove("hide");
}