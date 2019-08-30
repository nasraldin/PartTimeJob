
( function( $ ) {
	var bmap, mapclass, autocomplete;
	 var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
      };

	window.box_map = {

		init: function() {
			var h_nav = $(".row-nav").css('height');
			var admin_bar = $("#wpadminbar").css('height');
			var h_window = $( window ).height();
			var h_map = parseInt(h_window) - parseInt(h_nav);
			if(parseInt(admin_bar) > 0 ){
				h_map = h_map- parseInt(admin_bar);
			}
			if( ! bx_global.is_archive ){
				var h_css =  h_map+'px';
				$("#bmap").css('height', h_css);
			}


			var mapclass = this;
			$("#range_02").ionRangeSlider({
			    min: 0,
			    max: 1000,
			    from: 0
			});

			mapclass.bmap = new google.maps.Map(document.getElementById('bmap'), {
		      	zoom: 10,
		    	center: new google.maps.LatLng(-33.92, 151.25),
		      	mapTypeId: google.maps.MapTypeId.ROADMAP
		    });

		    if (navigator.geolocation) {
		    	console.log('allowed share location.');
		    	console.log('navigar.geolocation ok.');

	          	navigator.geolocation.getCurrentPosition(function(position) {
		            var pos = {
		              	lat: position.coords.latitude,
		              	lng: position.coords.longitude
		            };
		            var current_pos = new google.maps.LatLng( pos.lat, pos.lng );
					var geocoder  = new google.maps.Geocoder();             // create a geocoder object
					var location  = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);    // turn coordinates into an object
					geocoder.geocode({'latLng': location}, function (results, status) {
						if(status == google.maps.GeocoderStatus.OK) {           // if geocode success
							var add=results[0].formatted_address;
							console.log('Address' + add);
							$("#autocomplete").val(add);
							$("#lat_address").val(pos.lat);
							$("#long_address").val(pos.lng);
							window.search_args.lat_address =pos.lat;
        					window.search_args.long_address =pos.lng;
						}
					});
		            // infoWindow.setPosition(pos);
		            // infoWindow.setContent('Location found.');
		            // infoWindow.open(map);
		           	// map.setCenter(pos);
	          }, function() {
	          	console.log('navigar.geolocation fail.');
	          	var element = document.getElementById("wrap_range");
   				//element.classList.add("hidden");



	          });
	        } else {
	        	console.log('browser doesnt allow share your location');
	          // Browser doesn't support Geolocation
	          handleLocationError(false, infoWindow, map.getCenter());
	        }

		    mapclass.locations = [];
		    mapclass.profiles =JSON.parse( jQuery('#json_list_profile').html() );

		    mapclass.profiles.forEach(function(entry) {
			    var temp = [entry.html_marker, entry.lat_address, entry.long_address];
			    mapclass.locations.push(temp);
			});

		    mapclass.showMapMarkers( mapclass.bmap , mapclass.locations );

			$('.job_filters' ).on( 'submit', this.mapFilterFreelance );
			//$('#autocompletee' ).on( 'focusin', this.geoGoogleLocation );

		},
		initMap:function(){

		},
		geoGoogleLocation: function(event){
			 autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
            {types: ['geocode']});

	        // When the user selects an address from the dropdown, populate the address
	        // fields in the form.
	        autocomplete.addListener('place_changed', window.box_map.fillInAddress);
		},

		fillInAddress : function () {
			// Get the place details from the autocomplete object.
			var place = autocomplete.getPlace();
			var lat = place.geometry.location.lat();
		    var lng = place.geometry.location.lng();
		    $("#lat_address").val(lat);
		    $("#long_address").val(lng);
			var placeId = place.place_id;


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
			}
     	},
		showMapMarkers:function(map, locations){

			var infowindow = new google.maps.InfoWindow();
			var marker, i;
			var bounds = new google.maps.LatLngBounds();
			var markers = [];
			for (i = 0; i < locations.length; i++) {

				marker = new google.maps.Marker({
					position: new google.maps.LatLng(locations[i][1], locations[i][2]),
					map: map,
					title:locations[i][3],
				});
				//box_map.addMarker(locations[i], box_map.bmap);
				bounds.extend(marker.getPosition());
				google.maps.event.addListener(marker, 'click', (function(marker, i) {
					return function() {
					  infowindow.setContent(locations[i][0]);
					  infowindow.open(map, marker);
					}
				})(marker, i));
				map.fitBounds(bounds);
				markers.push(marker);

			}
			var markerCluster = new MarkerClusterer(map, markers, {imagePath: 'https://googlemaps.github.io/js-marker-clusterer/images/m'});
		},
		renderResults: function(data){

			$("#text_result").html(data.rows_txt);
			//reset map to default- no markers
			window.box_map.bmap = new google.maps.Map(document.getElementById('bmap'), {
		      	zoom: 10,
		    	center: new google.maps.LatLng(data.center_lat, data.center_lng),
		      	mapTypeId: google.maps.MapTypeId.ROADMAP
		    });


		    var new_locations = [];
		    $.each(data, function(i, item) {
			   	var temp = [item.html_marker, item.lat_address, item.long_address];
			    new_locations.push(temp);
			});

			window.box_map.showMapMarkers( window.box_map.bmap , new_locations );
		},
		resetMap:function(lat, long){
			box_map.bmap = new google.maps.Map(document.getElementById('bmap'), {
		      	zoom: 10,
		    	center: new google.maps.LatLng(lat, long),
		      	mapTypeId: google.maps.MapTypeId.ROADMAP
		    });
		},
		addMarker: function (location,map) {
			 marker = new google.maps.Marker({
			    position: new google.maps.LatLng(location[1], location[2]),
			    map: map,
			    title: location[0],
			 });
		  	marker.setMap(map);
		},
		mapFilterFreelance: function(event){

			event.preventDefault();

			var form 	= $(event.currentTarget);
			var data = {};
			form.find(' input[type=text], input[type=number],  input[type=hidden], input[type=email],textarea,select').each(function() {
		    	var key 	= $(this).attr('name');
		        data[key] 	= $(this).val();
		    });
			form.find('input:radio:checked').each(function() {
		    	var key 	= $(this).attr('name');
		        data[key] 	= $(this).val();
		    });

		    $.ajax({
		        emulateJSON: true,
		        method :'post',
		        url : bx_global.ajax_url,
		        data: {
		                action: 'nearby_filter',
		                request: data,
		        },
		        beforeSend  : function(event){
		        	console.log('beforeSend');
		        	form.find(".btn-submit").addClass("loading");
		        },
		        success: function(res){
		        	//if(res && res.data.length > 0){
		        		window.box_map.renderResults(res.data.posts);
		        	//}
		        },
		    });
		},

	}
	$(document).ready(function(){
		window.box_map.init();
	})
})( jQuery, window.ajaxSend );