$(document).ready(function() {
    var phone_select = 0;
    var user_list = []
        //Auto complete to mobile number
    $("#input_mobile_number").autocomplete({
        source: function(request, response) {
            $.ajax({
                type: 'POST',
                url: REQUEST_URL + '/search_phone',
                data: {
                    type: 'rider',
                    text: $("#input_mobile_number").val(),
                    country_code: $("#input_country_code").val()
                },
                dataType: "json",
                success: function(data) {
                    var users = [];
                    user_list = [];
                    for (var i = 0; i < data.length; i++) {
                        user_list[data[i].mobile_number] = users[i] = { value: data[i].mobile_number, first_name: data[i].first_name, last_name: data[i].last_name, email: data[i].email }
                    }
                    response(users);
                }
            });
        },
        select: function(event, ui) {
            $('#input_first_name').val(ui.item.first_name);
            $('#input_last_name').val(ui.item.last_name);
            $('#input_email').val(ui.item.email);
            $('#input_first_name').prop('readonly', true);
            $('#input_last_name').prop('readonly', true);
            $('#input_email').prop('readonly', true);
            phone_select = 1;
        }
    })
    $("#input_mobile_number").keyup(function() {
        if (typeof user_list[$(this).val()] !== 'undefined') {
            $('#input_first_name').val(user_list[$(this).val()].first_name);
            $('#input_last_name').val(user_list[$(this).val()].last_name);
            $('#input_email').val(user_list[$(this).val()].email);
            $('#input_first_name').prop('readonly', true);
            $('#input_last_name').prop('readonly', true);
            $('#input_email').prop('readonly', true);
        } else {
            $('#input_first_name').prop('readonly', false);
            $('#input_last_name').prop('readonly', false);
            $('#input_email').prop('readonly', false);
            $('#input_first_name').val('');
            $('#input_last_name').val('');
            $('#input_email').val('');
        }
    });
});

Date.dateDiff = function(datepart, fromdate, todate) {
    datepart = datepart.toLowerCase();
    var diff = todate - fromdate;
    var divideBy = {
        w: 604800000,
        d: 86400000,
        h: 3600000,
        m: 60000,
        s: 1000
    };

    return Math.floor(diff / divideBy[datepart]);
}

app.controller('delivery_order', ['$scope', '$http', '$compile', '$filter', function($scope, $http, $compile, $filter) {
    $scope.Driverfilter = function(driver) {
        return function(item) {
            if (typeof item === 'undefined' || typeof driver === 'undefined') {
                return true;
            }
            return ((item.first_name).toLowerCase()).includes(driver.toLowerCase()) || (item.mobile_number).includes(driver);
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
    $scope.from_pin = APP_URL + '/images/PinFrom.png'
    $scope.to_pin = APP_URL + '/images/PinTo.png'
    $scope.vehicle_detail_peak_fare = 0
    initAutocomplete();
    initMap();

    var autoCompleteOptions = {
        fields: ['place_id', 'name', 'types', 'formatted_address', 'address_components', 'geometry', 'utc_offset']
    };

    //Auto complete to pickup & drop location
    function initAutocomplete() {
        pick_up_location_autocomplete = new google.maps.places.Autocomplete(document.getElementById('input_pick_up_location'), autoCompleteOptions);
        pick_up_location_autocomplete.addListener('place_changed', pick_up_location_address);

        drop_off_location_autocomplete = new google.maps.places.Autocomplete(document.getElementById('input_drop_off_location'), autoCompleteOptions);
        drop_off_location_autocomplete.addListener('place_changed', drop_off_location_Address);

    }

    function pick_up_location_address() {
        pickup_place = pick_up_location_autocomplete.getPlace();
        $('#input_pick_up_location').val(pickup_place.formatted_address);
        $scope.utc_offset = pickup_place.utc_offset;

        $scope.pick_up_latitude = pickup_place.geometry.location.lat();
        $scope.pick_up_longitude = pickup_place.geometry.location.lng();
        $('#pick_up_latitude').val($scope.pick_up_latitude)
        $('#pick_up_longitude').val($scope.pick_up_longitude)
        $('#utc_offset').val($scope.utc_offset)
        if (typeof $scope.from_marker !== 'undefined') {
            $scope.from_marker.setMap(null);
        }

        $scope.from_marker = new google.maps.Marker({
            map: $scope.map,
            draggable: true,
            icon: $scope.from_pin,
            animation: google.maps.Animation.DROP,
            position: { lat: $scope.pick_up_latitude, lng: $scope.pick_up_longitude }
        });
        $scope.map.setZoom($scope.mapRadius);
        $scope.map.panTo($scope.from_marker.position);
        $scope.from_marker.addListener('dragend', fromMarkerDrag);
        calculateAndDisplayRoute();
        if (typeof $scope.pick_up_latitude === "undefined" || typeof $scope.pick_up_longitude === "undefined") {
            $('#input_map_zoom').attr("disabled", true);
        } else {
            $('#input_map_zoom').attr("disabled", false);
        }
    }

    function drop_off_location_Address() {
        drop_place = drop_off_location_autocomplete.getPlace();
        $('#input_drop_off_location').val(drop_place.formatted_address);
        $scope.drop_off_latitude = drop_place.geometry.location.lat();
        $scope.drop_off_longitude = drop_place.geometry.location.lng();
        $('#drop_off_latitude').val($scope.drop_off_latitude)
        $('#drop_off_longitude').val($scope.drop_off_longitude)
        if (typeof $scope.to_marker !== 'undefined') {
            $scope.to_marker.setMap(null);
        }
        $scope.to_marker = new google.maps.Marker({
            map: $scope.map,
            draggable: true,
            icon: $scope.to_pin,
            animation: google.maps.Animation.DROP,
            position: { lat: $scope.drop_off_latitude, lng: $scope.drop_off_longitude }
        });
        $scope.to_marker.addListener('dragend', toMarkerDrag);
        $scope.map.setZoom($scope.mapRadius);
        $scope.map.panTo($scope.to_marker.position);
        calculateAndDisplayRoute();
    }


    $('#input_pick_up_location,#input_drop_off_location').change(function() {
        if ($('#input_pick_up_location').val() == '' || $('#input_drop_off_location').val() == '') {
            $('#input_date_time').attr('disabled', true)
        } else {
            $('#input_date_time').attr('disabled', false)
        }
    })

    //init map
    function initMap() {
        $scope.directionsService = new google.maps.DirectionsService;
        $scope.directionsDisplay = new google.maps.DirectionsRenderer({ preserveViewport: true });
        $scope.geocoder = new google.maps.Geocoder;
        var mapCanvas = document.getElementById('map');
        if (!mapCanvas) {
            return false;
        }
        var mapOptions = {
            zoom: 2,
            minZoom: 1,
            zoomControl: true,
            center: { lat: 0, lng: 0 },
            mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        $scope.map = new google.maps.Map(mapCanvas, mapOptions);

        $scope.directionsDisplay.setMap($scope.map);

        if ($('#pick_up_latitude').val() != '' && $('#pick_up_longitude').val() != '' && $('#drop_off_latitude').val() != '' && $('#drop_off_longitude').val() != '') {
            $scope.pick_up_latitude = parseFloat($('#pick_up_latitude').val())
            $scope.pick_up_longitude = parseFloat($('#pick_up_longitude').val())
            $scope.drop_off_latitude = parseFloat($('#drop_off_latitude').val())
            $scope.drop_off_longitude = parseFloat($('#drop_off_longitude').val())
            $scope.from_marker = new google.maps.Marker({
                map: $scope.map,
                draggable: true,
                icon: $scope.from_pin,
                animation: google.maps.Animation.DROP,
                position: { lat: $scope.pick_up_latitude, lng: $scope.pick_up_longitude }
            });
            $scope.to_marker = new google.maps.Marker({
                map: $scope.map,
                icon: $scope.to_pin,
                draggable: true,
                animation: google.maps.Animation.DROP,
                position: { lat: $scope.drop_off_latitude, lng: $scope.drop_off_longitude }
            });
            $scope.map.setZoom($scope.mapRadius);
            $scope.map.panTo($scope.to_marker.position);
            $scope.from_marker.addListener('dragend', fromMarkerDrag);
            $scope.to_marker.addListener('dragend', toMarkerDrag);
            calculateAndDisplayRoute();
        }
    }

    //Show route on map
    function calculateAndDisplayRoute() {
        if (typeof $scope.pick_up_latitude === "undefined" || typeof $scope.pick_up_longitude === "undefined" || typeof $scope.drop_off_latitude === "undefined" || typeof $scope.drop_off_longitude === "undefined") {
            return false;
        }
        $scope.directionsService.route({
            origin: {
                lat: parseFloat($scope.pick_up_latitude),
                lng: parseFloat($scope.pick_up_longitude)
            },
            destination: {
                lat: parseFloat($scope.drop_off_latitude),
                lng: parseFloat($scope.drop_off_longitude)
            },
            travelMode: 'DRIVING'
        }, function(response, status) {
            if (status === 'OK') {
                $scope.directionsDisplay.setDirections(response);
                $scope.directionsDisplay.setOptions({ suppressMarkers: true });
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

    function fromMarkerDrag(evt) {
        $scope.pick_up_latitude = evt.latLng.lat()
        $scope.pick_up_longitude = evt.latLng.lng()
        $('#pick_up_latitude').val($scope.pick_up_latitude)
        $('#pick_up_longitude').val($scope.pick_up_longitude)
        var latlng = { lat: $scope.pick_up_latitude, lng: $scope.pick_up_longitude };
        calculateAndDisplayRoute();
        getLocation(latlng, 'input_pick_up_location')
            // $scope.utc_offset  = pickup_place.utc_offset;
            // $('#utc_offset').val($scope.utc_offset)
    }

    function toMarkerDrag(evt) {
        $scope.drop_off_latitude = evt.latLng.lat()
        $scope.drop_off_longitude = evt.latLng.lng()
        $('#drop_off_latitude').val($scope.drop_off_latitude)
        $('#drop_off_longitude').val($scope.drop_off_longitude)
        var latlng = { lat: $scope.drop_off_latitude, lng: $scope.drop_off_longitude };
        calculateAndDisplayRoute();
        getLocation(latlng, 'input_drop_off_location')
    }

    //find location from latlang
    function getLocation(latlng, field) {
        $scope.geocoder.geocode({ 'location': latlng }, function(results, status) {
            if (status === 'OK') {
                if (results[0]) {
                    $('#' + field).val(results[0].formatted_address);
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
            if ($scope.pick_up_latitude != '' && $scope.pick_up_longitude != '') {
                $scope.map.setZoom($scope.mapRadius);
            }
        } else {
            var newRadius = Math.round(24 - Math.log(radius) / Math.LN2);
            $scope.mapRadius = newRadius - 9;
            if ($scope.pick_up_latitude != '' && $scope.pick_up_longitude != '') {
                var pt = new google.maps.LatLng($scope.pick_up_latitude, $scope.pick_up_longitude);
                $scope.map.setCenter(pt);
                $scope.map.setZoom($scope.mapRadius);
            }
        }
    }
    $scope.utc_offset = ''
    $(function() {
        moment.tz.setDefault('Australia/Brisbane');
        if ($scope.date_time == '') {
            $scope.date_time = moment().tz('Australia/Brisbane').format('ha z');
        }
        var today = new Date().toLocaleString("en-US", { timeZone: "Australia/Brisbane" });
        //console.log(today);
        $('#input_date_time').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            //minDate: new Date().toLocaleString("en-US", { timeZone: "Australia/Brisbane" }),
            ignoreReadonly: true,
            sideBySide: true,
        }).on('dp.hide', function(e) {
            $('#input_date_time').data("DateTimePicker").minDate(formatDate(today))
                // $(this).data('DateTimePicker').show();
        });
    });

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

    $('#input_auto_assign_status').change(function() {
        if ($('#input_auto_assign_status').is(":checked")) {
            $('.assigned_driver').hide();
            $('.driver_list').hide();
            $('#auto_assign_id').val(0)
            $scope.auto_assign(0);
        } else {
            if ($('#auto_assign_id').val() != 0) {
                $('.assigned_driver').show();
            }
            $('.driver_list').show();
            $('.submit_button').attr('disabled', true)
        }
    })

    //Driver auto assign functionality
    $scope.auto_assign = function(driver_id) {
        $('.submit_button').attr('disabled', true)
        date_time = $('#input_date_time').val()
        if (date_time == '') {
            var today = new Date().toLocaleString("en-US", { timeZone: "Australia/Brisbane" });
            $('#input_date_time').val($filter('date')(new Date(today), 'yyyy-MM-dd HH:mm'))
        }
        date_time = $('#input_date_time').val()
        $http.post(REQUEST_URL + '/get_driver', {
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
            } else if (driver_id == 0) {
                $('.submit_button').attr('disabled', false);
            } else {
                $scope.assigned_driver = response.driver;
                $('.assigned_driver').show();
                $scope.auto_assign_status = false;
                $('#auto_assign_id').val($scope.assigned_driver.id);
                $('.submit_button').attr('disabled', false);
            }
        });
    };
    var mapIcons = {
        Online: APP_URL + '/images/car_green.png',
        Scheduled: APP_URL + '/images/car_red.png',
        Begin_trip: APP_URL + '/images/car_blue.png',
        End_trip: APP_URL + '/images/car_yellow.png',
        Offline: APP_URL + '/images/car_black.png',
    }
    var googleMarker = [];
    var map;
    var infoWindow = new google.maps.InfoWindow();

    //Show drivers on map
    $scope.MapData = function() {
        clearOverlays();
        if ($scope.vehicle_types.length != 0) {
            drivers = $scope.drivers
            angular.forEach(drivers, function(value, key) {
                if (value.driver_current_status == $scope.driver_availability || $scope.driver_availability == '') {
                    var icon_img = value.driver_current_status;
                    if (icon_img == 'Begin trip') {
                        icon_img = 'Begin_trip'
                    } else if (icon_img == 'End trip') {
                        icon_img = 'End_trip'
                    }
                    var icon = {
                        url: mapIcons[icon_img], // url
                        scaledSize: new google.maps.Size(23, 30), // scaled size
                        origin: new google.maps.Point(0, 0), // origin
                        anchor: new google.maps.Point(0, 0) // anchor
                    };

                    marker = new google.maps.Marker({
                        position: {
                            lat: parseFloat(value.latitude),
                            lng: parseFloat(value.longitude)
                        },
                        id: value.id,
                        map: $scope.map,
                        title: value.first_name + " " + value.last_name,
                        icon: icon,
                    });
                    googleMarker.push(marker);
                    google.maps.event.addListener(marker, 'click', function() {
                        var html = '';
                        html += '<div class="user_background col-md-3">';
                        html += '<img src="' + value.src + '" class="img-circle" width="100%" height="auto"></div>';
                        html += '<div class="user_details col-md-9">';
                        html += '<h3 class="text-capitalize">' + value.first_name + '</h3> ';
                        html += '<p title="' + value.email + '"><i class="fa fa-envelope" aria-hidden="true"></i> : <span class="sety">' + value.email + '</span></p>';
                        html += '<p title="' + value.hidden_mobile_number + '"><i class="fa fa-phone" aria-hidden="true"></i> : <span class="sety">' + value.hidden_mobile_number + '</span></p>';
                        html += '</div>';
                        infoWindow.setContent(html)
                        infoWindow.open(map, marker);
                        /*$('#user_details').show();
                        $('#user_details').html(html);*/
                    });
                }
            });
        }
    }

    function clearOverlays() {
        for (var i = 0; i < googleMarker.length; i++) {
            googleMarker[i].setMap(null);
        }
        googleMarker.length = 0;
    }
    $(document).on("click", ".close_user_details", function() {
        $('#user_details').hide();
    })

    //form validation
    var v = $("#delivery_order").validate({
        rules: {
            currency_code: { required: true },
            mobile_number: { required: true },
            first_name: { required: true },
            last_name: { required: true },
            email: { required: true, email: true },
            pick_up_location: { required: true },
            drop_off_location: { required: true },
            vehicle_type: { required: true },
            auto_assign_status: {
                required: {
                    depends: function(element) {
                        if ($('#auto_assign_id').val() == 0) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                }
            },
            date_time: {
                required: true,
                min_date_time: true,
            },
        },
        messages: {
            auto_assign_status: {
                required: 'This field is required if no driver assigned.'
            },
        },
        errorElement: "span",
        errorClass: "text-danger",
        errorPlacement: function(label, element) {
            if (element.attr("data-error-placement") === "container") {
                container = element.attr('data-error-container');
                $(container).append(label);
            } else {
                label.insertAfter(element);
            }
        },
    });

    $.validator.addMethod("min_date_time", function(value, element, param) {
        if (page == 'edit') {
            var old_date_value = new Date(old_edit_date);
            var today = new Date().toLocaleString("en-US", { timeZone: "Australia/Brisbane" });
            var alignFillDate = new Date($('#input_date_time').val());
            if ($scope.utc_offset == '') {
                var valid_date = new Date(today.getTime() + (14 * 60 * 1000));
                var currenct_date = new Date(today.getTime() + (15 * 60 * 1000));
            } else {
                var valid_date = new Date(today.getTime() + ((today.getTimezoneOffset() + $scope.utc_offset + 14) * 60000));
                var currenct_date = new Date(today.getTime() + ((today.getTimezoneOffset() + $scope.utc_offset + 15) * 60000));
            }
            if (!moment(old_date_value).isBefore(alignFillDate) && !moment(valid_date).isBefore(alignFillDate)) {
                if (moment(old_date_value).isBefore(valid_date))
                    $('#input_date_time').val($filter('date')(new Date(old_date_value), 'yyyy-MM-dd HH:mm'))
                else
                    $('#input_date_time').val($filter('date')(new Date(currenct_date), 'yyyy-MM-dd HH:mm'))
            }
            var alignFillDate = new Date($('#input_date_time').val());
            return moment(old_date_value).isBefore(alignFillDate) || moment(valid_date).isBefore(alignFillDate) || moment(old_date_value).isSame(alignFillDate);
        } else {
            var today = new Date().toLocaleString("en-US", { timeZone: "Australia/Brisbane" });
            var alignFillDate = new Date($('#input_date_time').val());
            if ($scope.utc_offset == '') {
                var valid_date = new Date(today.getTime() + (14 * 60 * 1000));
                var currenct_date = new Date(today.getTime() + (15 * 60 * 1000));
            } else {
                var valid_date = new Date(today.getTime() + ((today.getTimezoneOffset() + $scope.utc_offset + 14) * 60000));
                var currenct_date = new Date(today.getTime() + ((today.getTimezoneOffset() + $scope.utc_offset + 15) * 60000));
            }
            if (!moment(valid_date).isBefore(alignFillDate)) {
                $('#input_date_time').val($filter('date')(new Date(currenct_date), 'yyyy-MM-dd HH:mm'))
            }
            var alignFillDate = new Date($('#input_date_time').val());
            return moment(valid_date).isBefore(alignFillDate);
        }
    }, $.validator.format((page == 'edit') ? "Please make sure that the booking time is ahead of current time" : "Please make sure that the booking time is 15 minutes ahead from pickup location current time"));

    var input1 = document.getElementById('input_pick_up_location');
    google.maps.event.addDomListener(input1, 'keydown', function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
        }
    });
    var input2 = document.getElementById('input_drop_off_location');
    google.maps.event.addDomListener(input2, 'keydown', function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
        }
    });


    $scope.page_loading = 1;

    $(document).ready(function() {
        $scope.page_loading = 0;
        $('#country_code_view').val('+' + $('#input_country_code').val())
        $('#input_country_code').change(function() {
            $('#country_code_view').val('+' + $('#input_country_code').val())
        })
    })

    $scope.checkInvalidTime = function() {
        var today = new Date().toLocaleString("en-US", { timeZone: "Australia/Brisbane" });
        var alignFillDate = new Date($('#input_date_time').val());

        if ($scope.utc_offset == '') {
            var valid_date = new Date(today.getTime() + (14 * 60 * 1000));
            var currenct_date = new Date(today.getTime() + (15 * 60 * 1000));
        } else {
            var valid_date = new Date(today.getTime() + ((today.getTimezoneOffset() + $scope.utc_offset + 14) * 60000));
            var currenct_date = new Date(today.getTime() + ((today.getTimezoneOffset() + $scope.utc_offset + 15) * 60000));
        }

        return !moment(valid_date).isBefore(alignFillDate);
    }

    $scope.submitForm = function($event) {
        // if ($scope.checkInvalidTime()) {
        //     return true;
        // }

        $('#customer_phone_number').val($("#country_code_view").val() + $("#input_mobile_number").val());
        $('#customer_name').val($("#input_first_name").val() + ' ' + $("#input_last_name").val());

        var today = new Date().toLocaleString("en-US", { timeZone: "Australia/Brisbane" });
        var alignFillDate = new Date($('#input_date_time').val());
        var diff = 0 - Date.dateDiff('m', alignFillDate, today)
        $('#input_date_time').val(diff);

        $("form[name='deliveryAddForm']").submit();
        //$('.submit_button').attr('disabled', true);
    };
}]);