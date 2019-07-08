// JavaScript Document
var autocomplete;
var map;
var geocoder;
var infoWindow;
var marker;

function handleLocationError(browserHasGeolocation, infoWindow, pos) {
	infoWindow.setPosition(pos);
	infoWindow.setContent(browserHasGeolocation ?
						  'Error: The Geolocation service failed.' :
						  'Error: Your browser doesn\'t support geolocation.');
	infoWindow.open(map);
}

function initialize() {
	geocoder = new google.maps.Geocoder();
	var input = document.getElementById("billing_address_1");
	map = new google.maps.Map(document.getElementById('map'), {
		center: {lat: -34.397, lng: 150.644},
		zoom: 10
	});

	if(input.value==''){
		autocomplete = new google.maps.places.Autocomplete(input);
		autocomplete.addListener('place_changed', fillInAddress);
		infoWindow = new google.maps.InfoWindow;

		// Try HTML5 geolocation.
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {
			var pos = {
				lat: position.coords.latitude,
				lng: position.coords.longitude
			};

			infoWindow.setPosition(pos);
			infoWindow.setContent('Location found.');
			infoWindow.open(map);
			map.setCenter(pos);
			}, function() {
			handleLocationError(true, infoWindow, map.getCenter());
			});
		} else {
			// Browser doesn't support Geolocation
			handleLocationError(false, infoWindow, map.getCenter());
		}
			
	}else{
		geocoder.geocode( { 'address': input.value}, function(results, status) {
      if (status == 'OK') {
        map.setCenter(results[0].geometry.location);
        marker = new google.maps.Marker({
            map: map,
            position: results[0].geometry.location
				});
				map.setZoom(17);
			} else {
        alert('Geocode was not successful for the following reason: ' + status);
      }
		});	
		input.onkeydown = function(){
			if(autocomplete == undefined){
				autocomplete = new google.maps.places.Autocomplete(input);
				autocomplete.addListener('place_changed', fillInAddress);
			}
		}	
	}
}

google.maps.event.addDomListener(window, "load", initialize);

function fillInAddress() {

	var place = autocomplete.getPlace();
        var myLatlng = new google.maps.LatLng(place.geometry.location.lat(),place.geometry.location.lng());
	document.getElementById("cust_latitude").value=place.geometry.location.lat();
	document.getElementById("cust_longitude").value=place.geometry.location.lng();
        var myOptions = {
          zoom: 10,
          center: myLatlng,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }
  	map = new google.maps.Map(document.getElementById("map"), myOptions);
        marker = new google.maps.Marker({
            position: myLatlng, 
            map: map,
						draggable: true
        });  
	google.maps.event.addListener(marker, 'dragend', function() {
        geocodePosition(marker.getPosition());
        });
				map.setZoom(17);
  
  // Get the place details from the autocomplete object.
       /* var place = autocomplete.getPlace();

        for (var component in componentForm) {
          document.getElementById(component).value = '';
          document.getElementById(component).disabled = false;
        }

        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        for (var i = 0; i < place.address_components.length; i++) {
          var addressType = place.address_components[i].types[0];
          if (componentForm[addressType]) {
            var val = place.address_components[i][componentForm[addressType]];
            document.getElementById(addressType).value = val;
          }
        } */
}
	  
function geocodePosition(pos) {
  geocoder.geocode({
    latLng: pos
  }, function(responses) {
    if (responses && responses.length > 0) {
      marker.formatted_address = responses[0].formatted_address;
	  document.getElementById("billing_address_1").value = marker.formatted_address;
          console.log(responses);
	  console.log(marker.formatted_address);
    } else {
      marker.formatted_address = 'Cannot determine address at this location.';
  	  document.getElementById("billing_address_1").value = "";
    }
    infoWindow.setContent(marker.formatted_address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
//    infoWindow.open(map, marker);
  });
}