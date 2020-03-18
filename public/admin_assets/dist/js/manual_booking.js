$(document).ready(function(){
	if ($('#manual_booking_id').val() == '') {
		$('#input_first_name').val('');
		$('#input_last_name').val('');
		$('#input_email').val('');
		$('#input_mobile_number').val('')
		$('#input_date_time').val('')
	}
	if ($('#input_first_name').val()!='') {
		$('#input_first_name').prop('readonly', true);
		$('#input_last_name').prop('readonly', true);
		$('#input_email').prop('readonly', true);
	}
	var phone_select = 0;
	var user_list = []
	//Auto complete to mobile number
		$( "#input_mobile_number" ).autocomplete({
		    source: function( request, response ) {
		      	$.ajax({
			        type: 'POST',
			      	url: REQUEST_URL+'/search_phone',
			      	data: {
			            type: 'rider',
			            text: $( "#input_mobile_number" ).val(),
			            country_code: $( "#input_country_code" ).val()
			        },
			      	dataType: "json",
			        success: function( data ) {
			        	var users = [];
			        	user_list = [];
			        	for (var i = 0; i < data.length; i++) {
			        		user_list[data[i].mobile_number] = users[i] = { value: data[i].mobile_number, first_name: data[i].first_name, last_name: data[i].last_name, email: data[i].email }
			        	}
			           	response( users );
			        }
		       	});
		    },
		    select: function( event, ui ) {
		        $('#input_first_name').val(ui.item.first_name);
		        $('#input_last_name').val(ui.item.last_name);
		        $('#input_email').val(ui.item.email);
				$('#input_first_name').prop('readonly', true);
				$('#input_last_name').prop('readonly', true);
				$('#input_email').prop('readonly', true);
		        phone_select = 1;
		    }
		})
	$( "#input_mobile_number" ).keyup(function(){
		if (typeof user_list[$(this).val()] !== 'undefined') {
			$('#input_first_name').val(user_list[$(this).val()].first_name);
	        $('#input_last_name').val(user_list[$(this).val()].last_name);
	        $('#input_email').val(user_list[$(this).val()].email);
			$('#input_first_name').prop('readonly', true);
			$('#input_last_name').prop('readonly', true);
			$('#input_email').prop('readonly', true);
		}else {
			$('#input_first_name').prop('readonly', false);
			$('#input_last_name').prop('readonly', false);
			$('#input_email').prop('readonly', false);
			$('#input_first_name').val('');
			$('#input_last_name').val('');
			$('#input_email').val('');
		}
	});
});
app.controller('manual_booking', ['$scope', '$http', '$compile', '$filter', function($scope, $http, $compile, $filter) {

    $scope.Driverfilter = function( driver ) {
	  return function( item ) {
	  	if (typeof item === 'undefined' || typeof driver === 'undefined') {
	  		return true;
	  	}
	    return ((item.first_name).toLowerCase()).includes(driver.toLowerCase()) || (item.mobile_number).includes(driver) ;
	  };
	};

	var autocomplete;
	$scope.from_marker;
	$scope.to_marker;
	$scope.vehicle_detail_km = 0
    $scope.vehicle_detail_minutes = 0
    $scope.vehicle_detail_km_fare = 0
    $scope.vehicle_detail_min_fare = 0
    $scope.vehicle_detail_total_fare = 0
	$scope.vehicle_detail_minimum_fare = 0
	$scope.vehicle_detail_base_fare = 0
	$scope.vehicle_detail_peak_price = 0
	$scope.ignore_assigned = []
	$scope.mapRadius = 13
	$scope.from_pin= APP_URL+'/images/PinFrom.png'
	$scope.to_pin= APP_URL+'/images/PinTo.png'
	$scope.vehicle_detail_peak_fare = 0
	initAutocomplete();
	initMap();

	var autoCompleteOptions = {
		fields: ['place_id', 'name', 'types','formatted_address','address_components','geometry','utc_offset']
	};

	//Auto complete to pickup & drop location
	function initAutocomplete()
	{
  		pickup_location_autocomplete = new google.maps.places.Autocomplete(document.getElementById('input_pickup_location'),autoCompleteOptions);
  	  	pickup_location_autocomplete.addListener('place_changed', pickup_location_address);

  	  	drop_location_autocomplete = new google.maps.places.Autocomplete(document.getElementById('input_drop_location'),autoCompleteOptions);
  	  	drop_location_autocomplete.addListener('place_changed', drop_location_Address);
   
	}

	function pickup_location_address() 
	{
	    pickup_place = pickup_location_autocomplete.getPlace();
	    $('#input_pickup_location').val(pickup_place.formatted_address);
		$scope.utc_offset  = pickup_place.utc_offset;

		$scope.pickup_latitude  = pickup_place.geometry.location.lat();
		$scope.pickup_longitude = pickup_place.geometry.location.lng();
		$('#pickup_latitude').val($scope.pickup_latitude)
		$('#pickup_longitude').val($scope.pickup_longitude)
		$('#utc_offset').val($scope.utc_offset)
		if (typeof $scope.from_marker !== 'undefined') {
			$scope.from_marker.setMap(null);
		}
	   	
		$scope.from_marker = new google.maps.Marker({
          	map: $scope.map,
          	draggable: true,
          	icon: $scope.from_pin,
          	animation: google.maps.Animation.DROP,
          	position: {lat: $scope.pickup_latitude, lng: $scope.pickup_longitude}
        });
        $scope.map.setZoom($scope.mapRadius);
		$scope.map.panTo($scope.from_marker.position);
        $scope.from_marker.addListener('dragend', fromMarkerDrag);
		calculateAndDisplayRoute();
		getVehicleTypes();
		if (typeof $scope.pickup_latitude === "undefined"||typeof $scope.pickup_longitude === "undefined") {
        	$('#input_map_zoom').attr("disabled", true); 
      	}else{
        	$('#input_map_zoom').attr("disabled", false); 
      	}
	}
	
	function drop_location_Address() 
	{
	    drop_place = drop_location_autocomplete.getPlace();
	    $('#input_drop_location').val(drop_place.formatted_address);
		$scope.drop_latitude  = drop_place.geometry.location.lat();
		$scope.drop_longitude = drop_place.geometry.location.lng();
		$('#drop_latitude').val($scope.drop_latitude)
		$('#drop_longitude').val($scope.drop_longitude)
		if (typeof $scope.to_marker !== 'undefined') {
			$scope.to_marker.setMap(null);
		}
		$scope.to_marker = new google.maps.Marker({
          	map: $scope.map,
          	draggable: true,
          	icon: $scope.to_pin,
          	animation: google.maps.Animation.DROP,
          	position: {lat: $scope.drop_latitude, lng: $scope.drop_longitude}
        });
        $scope.to_marker.addListener('dragend', toMarkerDrag);
        $scope.map.setZoom($scope.mapRadius);
		$scope.map.panTo($scope.to_marker.position);
		calculateAndDisplayRoute();
		getVehicleTypes();
	}


	$('#input_pickup_location,#input_drop_location').change(function(){
		if($('#input_pickup_location').val() == '' || $('#input_drop_location').val() == ''){
			$('#input_date_time').attr('disabled',true)
		}else{ 
			$('#input_date_time').attr('disabled',false)
		}
	})

	/*$('#input_date_time').change(function(){
		getVehicleTypes();
	})*/

	//get car types list
	function getVehicleTypes(id=0) {
		date_time=$('#input_date_time').val()
		
		if (date_time=='' || date_time==' ' ) {
			var today = new Date();
			if ($scope.utc_offset == '') {
        		var currenct_date = new Date(today.getTime() + (15 * 60 * 1000));
        	}else{
				var currenct_date = new Date( today.getTime() + ((today.getTimezoneOffset()+$scope.utc_offset+15) * 60000));
        	}
			$('#input_date_time').val($filter('date')(new Date(currenct_date),'yyyy-MM-dd HH:mm'))
		}
      	if (typeof $scope.pickup_latitude === "undefined"||typeof $scope.pickup_longitude === "undefined"||typeof $scope.drop_latitude === "undefined"||typeof $scope.drop_longitude === "undefined") {
        	return false;
      	}
	    if(!$('#input_auto_assign_status').is(":checked")){
			$('.loader').show()
			$('.driver_list').hide()
			$('.submit_button').attr('disabled',true)
		}
		$scope.from_marker.setDraggable(false);
		$scope.to_marker.setDraggable(false);
		$('.change_field').attr('disabled', true)
		if (typeof $('#input_driver_availability')[0].selectize !== 'undefined') {
			$('#input_driver_availability')[0].selectize.disable(); 
		}
		date_time=$('#input_date_time').val()
      	$http.post(REQUEST_URL+'/search_cars', {
      			type: 'online',
	            pickup_latitude: $scope.pickup_latitude,
	            pickup_longitude: $scope.pickup_longitude,
	            drop_latitude: $scope.drop_latitude,
	            drop_longitude: $scope.drop_longitude,
	            pickup_location: $('#input_pickup_location').val(),
	            drop_location: $('#input_drop_location').val(),
	            date_time: date_time
      		}).then(function(response) {
      		response = response.data
        	if (response.status_code==0) {
        		$('.error_msg').hide();
        		$('.error_msg').html('');
        		if (response.status_message == 'location_unavailable') {
        			$('.error_pickup_location').show();
        			$('.error_pickup_location').html(response.trans_message);
        		}else if(response.status_message == 'location_country'){
        			$('.error_drop_location').show();
        			$('.error_drop_location').html(response.trans_message);
        		}else if(response.status_message == 'no_cars_found'){
        			$('.error_vehicle_type').show();
        			$('.error_vehicle_type').html(response.trans_message);
        		}
        		$('#input_vehicle_type').attr("disabled", true); 

	        	$scope.vehicle_type_value = ''
	        	$scope.vehicle_types = []
				$('.driver_list').hide();
				$('.submit_button').attr('disabled',true)
				$('.loader').hide();
				$('.change_field').attr('disabled', false)
				$('#input_driver_availability')[0].selectize.enable(); 
				$scope.from_marker.setDraggable(true);
				$scope.to_marker.setDraggable(true);
				$('#auto_assign_id').val(0)
				$('.assigned_driver').hide();

        	}else{
        		$('.error_msg').hide();
        		$('#input_vehicle_type').attr("disabled", false);
        		$.each(response.vehicle_type, function( index, value ) {
        			if (typeof $scope.vehicle_type_value !== "undefined" && $scope.vehicle_type_value != '') {
        				index = $scope.vehicle_type_value
        			}
        			$scope.default_car_index = index
        			$scope.vehicle_types = response.vehicle_type
	        		$scope.vehicle_type_value = index
	        		if (id!=0) {
	        			$scope.auto_assign(id);
	        		}
					$scope.list_driver()
	        		return false
        		});
        	}
       	});
    }

	//get drivers list
    $scope.list_driver= function(){
    	if(!$('#input_auto_assign_status').is(":checked")){
			$('.loader').show()
			$('.submit_button').attr('disabled',true)
			$('.driver_list').hide()
		}
		if (typeof $('#input_driver_availability')[0].selectize !== 'undefined') {
			$('#input_driver_availability')[0].selectize.disable(); 
		}
		$scope.from_marker.setDraggable(false);
		$scope.to_marker.setDraggable(false);
		$('.change_field').attr('disabled', true)
    	if (typeof $scope.pickup_latitude === "undefined"||typeof $scope.pickup_longitude === "undefined"||typeof $scope.drop_latitude === "undefined"||typeof $scope.drop_longitude === "undefined") {
        	return false;
      	}
      	$('.assigned_driver').hide();
		$('#auto_assign_id').val(0)
      	date_time=$('#input_date_time').val()
		if (date_time=='') {
			var today = new Date();
			$('#input_date_time').val($filter('date')(new Date(today),'yyyy-MM-dd HH:mm'))
		}
    	var element = $('#input_vehicle_type').find('option:selected'); 
    	var key = element.attr("key"); 
    	if (typeof key === "undefined" || key==0) {
	    	key = $scope.default_car_index
	    }
    	$http.post(REQUEST_URL+'/driver_list', {
	            pickup_latitude: $scope.pickup_latitude,
	            pickup_longitude: $scope.pickup_longitude,
	            drop_latitude: $scope.drop_latitude,
	            drop_longitude: $scope.drop_longitude,
	            date_time: date_time,
	            car_id: key,
	            schedule_id: ($('#manual_booking_id').val()=='')?0:$('#manual_booking_id').val(),
      		}).then(function(response) {
      			response = response.data
      			$scope.drivers = response
      			if(!$('#input_auto_assign_status').is(":checked")){
					$('.driver_list').show();
				}
				$('.submit_button').removeAttr('disabled')
				$('.loader').hide();
				$('.change_field').attr('disabled', false)
				$('#input_driver_availability')[0].selectize.enable(); 
				$scope.from_marker.setDraggable(true);
				$scope.to_marker.setDraggable(true);
		        $scope.vehicle_detail = $scope.vehicle_types[key]
		        fare_detail()
			    $scope.MapData()
       	});
	    
	}

	//show fare details
	function fare_detail()
	{
    	$scope.vehicle_detail_km = $scope.vehicle_detail.km
    	$scope.vehicle_detail_minutes = $scope.vehicle_detail.minutes
    	$scope.vehicle_detail_km_fare = parseFloat($scope.vehicle_detail_km) * parseFloat($scope.vehicle_detail.per_km)
    	$scope.vehicle_detail_min_fare = parseFloat($scope.vehicle_detail_minutes) * parseFloat($scope.vehicle_detail.per_min)
    	$scope.vehicle_detail_total_fare = $scope.vehicle_detail.fare_estimation
    	$scope.vehicle_detail_minimum_fare = $scope.vehicle_detail.min_fare
    	$scope.vehicle_detail_base_fare = $scope.vehicle_detail.base_fare
    	peak_price = Number($scope.vehicle_detail.peak_price)
    	$scope.vehicle_detail_peak_price = peak_price.toFixed(0)
    	$scope.vehicle_detail_peak_fare = $scope.vehicle_detail.peak_fare
    	$('#location_id').val($scope.vehicle_detail.location_id)
    	$('#peak_id').val($scope.vehicle_detail.peak_id)
	}

	//init map
	function initMap() {
		$scope.directionsService = new google.maps.DirectionsService;
        $scope.directionsDisplay = new google.maps.DirectionsRenderer({preserveViewport: true});
        $scope.geocoder = new google.maps.Geocoder;
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
		$scope.map = new google.maps.Map(mapCanvas, mapOptions);

        $scope.directionsDisplay.setMap($scope.map);
        
	    if($('#pickup_latitude').val() != '' && $('#pickup_longitude').val() != '' && $('#drop_latitude').val() != '' && $('#drop_longitude').val() != ''){
	    	$scope.pickup_latitude = parseFloat($('#pickup_latitude').val())
		    $scope.pickup_longitude = parseFloat($('#pickup_longitude').val())
		    $scope.drop_latitude = parseFloat($('#drop_latitude').val())
		    $scope.drop_longitude = parseFloat($('#drop_longitude').val())
		    $scope.from_marker = new google.maps.Marker({
		      	map: $scope.map,
		      	draggable: true,
          		icon: $scope.from_pin,
		      	animation: google.maps.Animation.DROP,
		      	position: {lat: $scope.pickup_latitude, lng: $scope.pickup_longitude}
		    });
			$scope.to_marker = new google.maps.Marker({
		      	map: $scope.map,
          		icon: $scope.to_pin,
		      	draggable: true,
		      	animation: google.maps.Animation.DROP,
		      	position: {lat: $scope.drop_latitude, lng: $scope.drop_longitude}
		    });
		    $scope.map.setZoom($scope.mapRadius);
			$scope.map.panTo($scope.to_marker.position);
			$scope.from_marker.addListener('dragend', fromMarkerDrag);
			$scope.to_marker.addListener('dragend', toMarkerDrag);
    		calculateAndDisplayRoute();
			getVehicleTypes($('#auto_assign_id').val());
		}
    }

	//Show route on map
    function calculateAndDisplayRoute() {
      	if (typeof $scope.pickup_latitude === "undefined"||typeof $scope.pickup_longitude === "undefined"||typeof $scope.drop_latitude === "undefined"||typeof $scope.drop_longitude === "undefined") {
        	return false;
      	}
        $scope.directionsService.route({
          	origin:  {
                    	lat: parseFloat($scope.pickup_latitude),
                        lng: parseFloat($scope.pickup_longitude)
                    },
          	destination: {
                        lat: parseFloat($scope.drop_latitude),
                        lng: parseFloat($scope.drop_longitude)
                    },
          travelMode: 'DRIVING'
        }, function(response, status) {
			if (status === 'OK') {
				$scope.directionsDisplay.setDirections(response);
				$scope.directionsDisplay.setOptions( { suppressMarkers: true } );
			} else {
console.log(status);
				// if (status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
				// 	calculateAndDisplayRoute();
				// 	return;
				// }
         
				// window.alert('Directions request failed due to ' + status);
			}
        });
    }

	function fromMarkerDrag(evt){
    	$scope.pickup_latitude = evt.latLng.lat()
    	$scope.pickup_longitude = evt.latLng.lng()
    	$('#pickup_latitude').val($scope.pickup_latitude)
    	$('#pickup_longitude').val($scope.pickup_longitude)
		var latlng = {lat: $scope.pickup_latitude, lng: $scope.pickup_longitude};
		calculateAndDisplayRoute();
		getLocation(latlng,'input_pickup_location')
		// $scope.utc_offset  = pickup_place.utc_offset;
		// $('#utc_offset').val($scope.utc_offset)
	}

	function toMarkerDrag(evt){
    	$scope.drop_latitude = evt.latLng.lat()
    	$scope.drop_longitude = evt.latLng.lng()
    	$('#drop_latitude').val($scope.drop_latitude)
		$('#drop_longitude').val($scope.drop_longitude)
		var latlng = {lat: $scope.drop_latitude, lng: $scope.drop_longitude};
		calculateAndDisplayRoute();
		getLocation(latlng,'input_drop_location')
	}

	//find location from latlang
	function getLocation(latlng,field) {
    	$scope.geocoder.geocode({'location': latlng}, function(results, status) {
		    if (status === 'OK') {
		      	if (results[0]) {
	    			$('#'+field).val(results[0].formatted_address);
					getVehicleTypes();
		      	} else {
		        	window.alert('No results found');
		      	}
		    } else {
		      window.alert('Please choose the valid location');
		    }
		});
	}

	//Map zoom by filter
	$scope.map_zoom = function(radius) {
		if (radius == 0) {
			$scope.mapRadius = 13
			if ($scope.pickup_latitude != '' && $scope.pickup_longitude != '') {
	    		$scope.map.setZoom($scope.mapRadius);
	    	}
		}else{
		    var newRadius = Math.round(24 - Math.log(radius) / Math.LN2);
		    $scope.mapRadius = newRadius - 9;
		    if ($scope.pickup_latitude != '' && $scope.pickup_longitude != '') {
				var pt = new google.maps.LatLng($scope.pickup_latitude, $scope.pickup_longitude);
			    $scope.map.setCenter(pt);
		    	$scope.map.setZoom($scope.mapRadius);
			}
		}
	}
	$scope.utc_offset=''
	$( function() {
		if ($scope.date_time == '') {
			$scope.date_time = moment()
		}
		var today = new Date();
	    $('#input_date_time').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            // minDate:  moment(),
            ignoreReadonly: true,
            sideBySide: true,
        }).on('dp.hide', function (e) {
            $('#input_date_time').data("DateTimePicker").minDate(formatDate(today))
             // $(this).data('DateTimePicker').show();
			getVehicleTypes();
        });
	} );

	//datetime picker
	function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    }
    $('.reset').click(function(e) {
    	e.preventDefault();
	    location.reload();
	});

	$('#input_auto_assign_status').change(function(){
		if($('#input_auto_assign_status').is(":checked")){
			$('.assigned_driver').hide();
			$('.driver_list').hide();
			$('#auto_assign_id').val(0)
			$scope.auto_assign(0);
		}
		else{ 
			if ($('#auto_assign_id').val()!=0) {
				$('.assigned_driver').show();
			}
			$('.driver_list').show();
			$('.submit_button').attr('disabled',true)
		}
	})

	//Driver auto assign functionality
	$scope.auto_assign = function(driver_id) {
		$('.submit_button').attr('disabled',true)
		date_time=$('#input_date_time').val()
		if (date_time=='') {
			var today = new Date();
			$('#input_date_time').val($filter('date')(new Date(today),'yyyy-MM-dd HH:mm'))
		}
		date_time=$('#input_date_time').val()
		$http.post(REQUEST_URL+'/get_driver', {
      			driver_id: driver_id,
      			date_time: date_time,
      			utc_offset: $scope.utc_offset,
      		}).then(function(response) {
      		response = response.data
        	if (response.status_code == 0) {
        		if (response.status_message == 'select_ahead_time') {
        			alert(response.trans_message);
        		}
        		$('#input_vehicle_type').attr("disabled", true); 
        	}
        	else if (driver_id == 0) {
        		 $('.submit_button').attr('disabled',false);
        	}
        	else {
        		$scope.assigned_driver = response.driver;
				$('.assigned_driver').show();
				$scope.auto_assign_status = false;
				$('#auto_assign_id').val($scope.assigned_driver.id);
				$('.submit_button').attr('disabled',false);
        	}
       	});
    };
    var mapIcons = {
        Online: APP_URL+'/images/car_green.png',
        Scheduled: APP_URL+'/images/car_red.png',
        Begin_trip: APP_URL+'/images/car_blue.png',
        End_trip: APP_URL+'/images/car_yellow.png',
        Offline: APP_URL+'/images/car_black.png',
    }
    var googleMarker = [];
    var map;
	var infoWindow = new google.maps.InfoWindow();

	//Show drivers on map
    $scope.MapData = function() {
	  	clearOverlays();
      	if ($scope.vehicle_types.length != 0) {
       		drivers = $scope.drivers
       		angular.forEach(drivers, function(value, key){
       			if (value.driver_current_status == $scope.driver_availability || $scope.driver_availability == '') {
            		var icon_img = value.driver_current_status;
            		if (icon_img == 'Begin trip') {
            			icon_img = 'Begin_trip'
            		}else if (icon_img == 'End trip') {
            			icon_img = 'End_trip'
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
						map: $scope.map,
						title: value.first_name + " " +value.last_name,
						icon : icon,
          			});
          			googleMarker.push(marker);
          			google.maps.event.addListener(marker, 'click', function() {
		                var html =''; 
		                html += '<div class="user_background col-md-3">';    
		                html +='<img src="'+value.src+'" class="img-circle" width="100%" height="auto"></div>';
		                html +='<div class="user_details col-md-9">';
		                html +='<h3 class="text-capitalize">'+value.first_name+'</h3> ';
		                html +='<p title="'+value.email+'"><i class="fa fa-envelope" aria-hidden="true"></i> : <span class="sety">'+value.email+'</span></p>';
		                html +='<p title="'+value.hidden_mobile_number+'"><i class="fa fa-phone" aria-hidden="true"></i> : <span class="sety">'+value.hidden_mobile_number+'</span></p>';
		                html +='</div>';
		                infoWindow.setContent(html)
		                infoWindow.open(map, marker);
          				/*$('#user_details').show();
          				$('#user_details').html(html);*/
          			});
       			}
       		});
    	}
	}
	function clearOverlays(){
	  	for (var i = 0; i < googleMarker.length; i++ ) {
	   		googleMarker[i].setMap(null);
	  	}
	  	googleMarker.length = 0;
	}
	$(document).on("click",".close_user_details",function(){
	  	$('#user_details').hide();
	})

	//form validation
    var v = $("#manual_booking").validate({
      rules: {
        currency_code: { required: true },
        mobile_number: { required: true },
        first_name: { required: true },
        last_name: { required: true },
        email: { required: true,email: true },
        pickup_location: { required: true },
        drop_location: { required: true },
        vehicle_type: { required: true },
        auto_assign_status: { 
        	required:{ 
              	depends: function(element){
	                if($('#auto_assign_id').val()==0){
	                  	return true;
	                }else{
	                  	return false;
	                }
              	}
            }
        },
        date_time : {
        	required: true,
            min_date_time: true,
         },
      },
      messages: {
        auto_assign_status : {
          required : 'This field is required if no driver assigned.'
        },
      },
      errorElement: "span",
      errorClass: "text-danger",
       errorPlacement: function( label, element ) {
        	if(element.attr( "data-error-placement" ) === "container" ){
          		container = element.attr('data-error-container');
          		$(container).append(label);
        	} else {
          		label.insertAfter( element ); 
        	}
      	},
    });

    $.validator.addMethod("min_date_time", function(value, element, param) {
    	if (page == 'edit') {
    		var old_date_value = new Date(old_edit_date);
			var today = new Date();
	    	var alignFillDate = new Date($('#input_date_time').val());
	    	if ($scope.utc_offset == '') {
	    		var valid_date = new Date(today.getTime() + (14 * 60 * 1000));
	    		var currenct_date = new Date(today.getTime() + (15 * 60 * 1000));
	    	}else{
				var valid_date = new Date( today.getTime() + ((today.getTimezoneOffset()+$scope.utc_offset+14) * 60000));
				var currenct_date = new Date( today.getTime() + ((today.getTimezoneOffset()+$scope.utc_offset+15) * 60000));
	  		}
	  		if(!moment(old_date_value).isBefore(alignFillDate) && !moment(valid_date).isBefore(alignFillDate)){
	  			if(moment(old_date_value).isBefore(valid_date))
	  				$('#input_date_time').val($filter('date')(new Date(old_date_value),'yyyy-MM-dd HH:mm'))
	  			else
	  				$('#input_date_time').val($filter('date')(new Date(currenct_date),'yyyy-MM-dd HH:mm'))
	  		}
	    	var alignFillDate = new Date($('#input_date_time').val());
	  		return moment(old_date_value).isBefore(alignFillDate) || moment(valid_date).isBefore(alignFillDate) || moment(old_date_value).isSame(alignFillDate);
    	}else{
			var today = new Date();
	    	var alignFillDate = new Date($('#input_date_time').val());
	    	if ($scope.utc_offset == '') {
	    		var valid_date = new Date(today.getTime() + (14 * 60 * 1000));
	    		var currenct_date = new Date(today.getTime() + (15 * 60 * 1000));
	    	}else{
				var valid_date = new Date( today.getTime() + ((today.getTimezoneOffset()+$scope.utc_offset+14) * 60000));
				var currenct_date = new Date( today.getTime() + ((today.getTimezoneOffset()+$scope.utc_offset+15) * 60000));
	  		}
	  		if(!moment(valid_date).isBefore(alignFillDate)){
	  			$('#input_date_time').val($filter('date')(new Date(currenct_date),'yyyy-MM-dd HH:mm'))
	  		}
	    	var alignFillDate = new Date($('#input_date_time').val());
	  		return moment(valid_date).isBefore(alignFillDate);
	  	}
  	}, $.validator.format((page == 'edit')?"Please make sure that the booking time is ahead of current time":"Please make sure that the booking time is 15 minutes ahead from pickup location current time"));

    var input1 = document.getElementById('input_pickup_location');
	google.maps.event.addDomListener(input1, 'keydown', function(event) { 
	    if (event.keyCode === 13) { 
	        event.preventDefault(); 
	    }
	});
	var input2 = document.getElementById('input_drop_location');
	google.maps.event.addDomListener(input2, 'keydown', function(event) { 
	    if (event.keyCode === 13) { 
	        event.preventDefault(); 
	    }
	}); 


	$(function() {
		$select_driver_availability = $('#input_driver_availability').selectize({
    		valueField: 'value',
    		labelField: 'name',
    		setValue:'All',
    		options: [
	    		{
	    			value: '',
	        		name: 'All',
	    		},{
	    			value: 'Online',
	        		name: 'Available Driver',
	        		imageUrl: APP_URL+'/images/green-icon.png'
	    		},{
	    			value: 'Scheduled',
	        		name: 'Enroute to Pickup',
	        		imageUrl: APP_URL+'/images/red_icon.png'
	    		},{
	    			value: 'Begin trip',
	        		name: 'Reached Pickup',
	        		imageUrl: APP_URL+'/images/blue_icon.png'
	    		},{
	    			value: 'End trip',
	        		name: 'Journey Started',
	        		imageUrl: APP_URL+'/images/yellow_icon.png'
	    		},{
	    			value: 'Offline',
	        		name: 'Offline',
	        		imageUrl: APP_URL+'/images/black_icon.png'
	    		}
	    	],
		    render: {
		        option: function (item, escape) {
		            return '<div class="option">' +
		                    '<div>' + ((typeof item.imageUrl=='undefined')?'':'<img style="margin-right: 5px" class="avatar" width="30px" height="30px" style="padding=10px;" src="' + item.imageUrl + '" />') +
		                        '<span class="name">' + escape(item.name) + '</span>' +
		                   '</div>' +
		                '</div>';
		        }
		    }
		});
		var selectize = $select_driver_availability[0].selectize;
		selectize.setValue('', false);
		$('#input_driver_availability')[0].selectize.disable(); 
	});

	$(document).on("click",'.driver_detail_view',function(){
		old_id = $('.driver_detail_popup').attr('id')
		new_id = $(this).attr('id')
		$('.driver_detail_popup').attr('id',$(this).attr('id'))
		$('.driver_name_detail').html($(this).attr('first_name'))
		$('.driver_email_detail').html($(this).attr('email'))
		$('.driver_phone_detail').html($(this).attr('phone'))
		$('.driver_company_detail').html($(this).attr('company'))
		
		if (old_id==new_id) {
			$('.driver_detail_popup').toggle("slide", { direction: "right" }, 700)
		}else{
			$('.driver_detail_popup').hide();
			$('.driver_detail_popup').show("slide", { direction: "right" }, 700);
		}
	});
	$(document).click(function(){
		if(!$(event.target).closest( ".driver_detail_view" ).length){
			$('.driver_detail_popup').hide("slide", { direction: "right" }, 700)
		}
	});
	$scope.page_loading = 1
	$(document).ready(function(){
		$scope.page_loading = 0;
		$('#country_code_view').val('+'+$('#input_country_code').val())
		$('#input_country_code').change(function(){
			$('#country_code_view').val('+'+$('#input_country_code').val())
		})
	})

	$scope.checkInvalidTime = function() {
		var today = new Date();
    	var alignFillDate = new Date($('#input_date_time').val());

    	if ($scope.utc_offset == '') {
    		var valid_date = new Date(today.getTime() + (14 * 60 * 1000));
    		var currenct_date = new Date(today.getTime() + (15 * 60 * 1000));
    	}
    	else {
			var valid_date = new Date( today.getTime() + ((today.getTimezoneOffset()+$scope.utc_offset+14) * 60000));
			var currenct_date = new Date( today.getTime() + ((today.getTimezoneOffset()+$scope.utc_offset+15) * 60000));
  		}

  		return !moment(valid_date).isBefore(alignFillDate);
	}

	$scope.submitForm = function($event) {
		if($scope.checkInvalidTime()) {
			alert("Please make sure that the booking time is 15 minutes ahead from current time. So if your current time is 3:00 PM then please select 3:15 PM as booking time. This gives a room to auto assign drivers properly.");
			return true;
		}
		
        $("form[name='myForm']").submit();
        $('.submit_button').attr('disabled',true);
    };
}]);