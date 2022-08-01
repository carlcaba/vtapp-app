<?
	//Load Google Maps Information
	require_once("core/classes/configuration.php");
	
	$conf = new configuration("MAPS_API_URL");
	$map_url = $conf->verifyValue();
	$conf = new configuration("MAPS_API_KEY");
	$map_api = $conf->verifyValue();
	$map_url = $map_url . $map_api;
	$conf = new configuration("MAPS_DEFAULT_ZOOM");
	$map_zoom = $conf->verifyValue();
	$conf = new configuration("MAPS_API_CALLBACK_LOCATION");
	$location_callback = $conf->verifyValue();
	$conf = new configuration("MAPS_API_CALLBACK_AUTOCOMPLETE");
	$autocomplete_callback = $conf->verifyValue();

	if(!isset($titleMapModal))
		$titleMapModal = "";

	if($titleMapModal == "") {
		$titleMapModal = $_SESSION["SELECT_LOCATION"];
		$showOkMap = true;
	}
?>
	<style>
		#modalMap {
			height: 100%;
		}
	</style>

	<!-- Modal Map -->
	<div class="modal fade" id="divMapModal" tabindex="-1" role="dialog" aria-labelledby="h5ModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="h5ModalLabel"><i class="fa fa-map-marker"></i> <?= $titleMapModal ?></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>					
				</div>
				<div class="modal-body"><div id="modalMap"></div></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?= $_SESSION["CLOSE"] ?></button>
<?
	if($showOkMap) {
?>
					<button type="button" class="btn btn-primary" id="btnOkMap" name="btnOkMap"><?= $_SESSION["DONE"] ?></button>
<?
	}
?>
					<input type="hidden" name="locality" id="locality" value="" /> 
					<input type="hidden" name="country" id="country" value="" /> 
				</div>
			</div>
		</div>
	</div>

	<!-- MAPS -->
    <script src="<?= $map_url . $autocomplete_callback ?>" async defer></script>

	<script>
		var map, infoWindow, marker, geocoder, pos, placeSearch, autocomplete, autocomplete2,
			componentForm = {
				locality: 'long_name',
				country: 'long_name'
			};
		$(document).ready(function() {
			$('#divMapModal').on('show.bs.modal', function() {
				var url = "<?= $map_url ?>";
				$(this).find('.modal-body').css({
					width:'auto', //probably not needed
					height: '600px',
					'max-height': '100%'
				});
				$("#btnOkMap").on("click", function(e) {
					$("#divMapModal").modal("toggle");
				});
				function initMap() {
					map = new google.maps.Map(document.getElementById('modalMap'), {
						center: {
							lat: -34.397, 
							lng: 150.644
						},
						zoom: <?= $map_zoom ?>
					});
					infoWindow = new google.maps.InfoWindow;

					// Try HTML5 geolocation.
					if (navigator.geolocation) {
						navigator.geolocation.getCurrentPosition(function(position) {
							pos = {
								lat: position.coords.latitude,
								lng: position.coords.longitude
							};

							infoWindow.setPosition(pos);
							infoWindow.setContent('<?= $_SESSION["LOCATION_FOUND"] ?>');
							infoWindow.open(map);
							map.setCenter(pos);
							map.addListener('center_changed', function() {
								window.setTimeout(function() {
									try {
										map.panTo(marker.getPosition());
									}
									catch(ex) {
										console.log(ex);
									}
								}, 3000);
							});	
						}, function() {
							handleLocationError(true, infoWindow, map.getCenter());
						});
					} 
					else {
						// Browser doesn't support Geolocation
						handleLocationError(false, infoWindow, map.getCenter());
					}

					// Create new marker on double click event on the map
					google.maps.event.addListener(map,'click',function(event) {
						marker = new google.maps.Marker({
							position: event.latLng, 
							map: map, 
							title: event.latLng.lat()+', '+event.latLng.lng()
						});
						geocoder = new google.maps.Geocoder();
						// Update lat/long value of div when the marker is clicked
						marker.addListener('click', function() {
							$("#hfLATITUDE").val(event.latLng.lat());
							$("#hfLONGITUDE").val(event.latLng.lng());
							geocoder.geocode({
								latLng: event.latLng
							}, function(responses) {
								if (responses && responses.length > 0) {
									marker.formatted_address = responses[0].formatted_address;
								} 
								else {
									marker.formatted_address = '<?= $_SESSION["ADDRESS_NOT_DETERMINED"] ?>';
								}
								infoWindow.setContent(marker.formatted_address + "<br><?= $_SESSION["COORDINATES"] ?>: " + marker.getPosition().toUrlValue(6));
								infoWindow.open(map, marker);
							});					
						});            
					});
					
					var clickHandler = new ClickEventHandler(map, origin);
				}

				var ClickEventHandler = function(map, origin) {
					this.origin = origin;
					this.map = map;
					this.directionsService = new google.maps.DirectionsService;
					this.directionsDisplay = new google.maps.DirectionsRenderer;
					this.directionsDisplay.setMap(map);
					//this.placesService = new google.maps.places.PlacesService(map);
					this.infoWindow = new google.maps.InfoWindow;
					this.infoWindowContent = document.getElementById('infowindow-content');
					this.infoWindow.setContent(this.infoWindowContent);
					// Listen for clicks on the map.
					this.map.addListener('click', this.handleClick.bind(this));
				};

				ClickEventHandler.prototype.handleClick = function(event) {
					var elementLat = "hfLATITUDE",
						elementLon = "hfLONGITUDE";
					if(typeof(latitude) !== "undefined" && latitude) {
						elementLat = latitude;
					}
					if(typeof(longitude) !== "undefined" && longitude) {
						elementLon = longitude;
					}
					$("#" + latitude).val(event.latLng.lat());
					$("#" + longitude).val(event.latLng.lng());
					// If the event has a placeId, use it.
					if (event.placeId) {
						console.log('<?= $_SESSION["PLACE_SELECTED"] ?>' + event.placeId);
						// Calling e.stop() on the event prevents the default info window from
						// showing.
						// If you call stop here when there is no placeId you will prevent some
						// other map click event handlers from receiving the event.
						event.stop();
						this.getPlaceInformation(event.placeId);
					}
					else {
						var geocoder = new google.maps.Geocoder;
						var latlng = {lat: event.latLng.lat(), lng: event.latLng.lng()};
						geocoder.geocode({'location': latlng}, function(results, status) {
							if (status === 'OK') {
								if (results[0]) {
									var element = "txtADDRESS";
									if(typeof(address) !== "undefined" && address) {
										element = address;
									}
									else if(typeof(alt_address) !== "undefined" && alt_address) {
										element = alt_address;
									}
									$("#" + element).val(results[0].formatted_address);
									infoWindow.setContent(results[0].formatted_address);
									infoWindow.open(map, marker);
								} 
								else {
									console.log('No results found');
								}
							} 
							else {
								console.log('Geocoder failed due to: ' + status);
							}
						});
					}
				};

				ClickEventHandler.prototype.getPlaceInformation = function(placeId) {
					var me = this;
					try {
						this.placesService.getDetails({placeId: placeId}, function(place, status) {
							if (status === 'OK') {
								me.infoWindow.close();
								me.infoWindow.setPosition(place.geometry.location);
								me.infoWindowContent.children['place-icon'].src = place.icon;
								me.infoWindowContent.children['place-name'].textContent = place.name;
								me.infoWindowContent.children['place-id'].textContent = place.place_id;
								me.infoWindowContent.children['place-address'].textContent = place.formatted_address;
								me.infoWindow.open(me.map);
							}
						});
					}
					catch(ex) {
						console.log(ex);
					}
				};

				function handleLocationError(browserHasGeolocation, infoWindow, pos) {
					infoWindow.setPosition(pos);
					infoWindow.setContent(browserHasGeolocation ?
									  '<?= $_SESSION["GEOLOCATION_FAILED"] ?>' :
									  '<?= $_SESSION["GEOLOCATION_NOT_SUPPORTED"] ?>');
					infoWindow.open(map);
				}			
				$.ajax({
					url: url,
					dataType: "script",
					success: initMap
				});			
			});
		});
	
		function initAutocomplete() {
			// Create the autocomplete object, restricting the search predictions to
			// geographical location types.
			var element = "txtADDRESS";
			if($("#" + address).is(":visible"))
				element = address;
			else if($("#" + alt_address).is(":visible"))
				element = alt_address;

			autocomplete = new google.maps.places.Autocomplete(
				document.getElementById(element), {
					strictBounds: false,
					types: ['geocode', 'establishment']
				}
			);
			
			// Avoid paying for data that you don't need by restricting the set of
			// place fields that are returned to just the address components.
			autocomplete.setFields(['address_component', 'geometry', 'formatted_address', 'name']);

			// When the user selects an address from the drop-down, populate the
			// address fields in the form.
			autocomplete.addListener('place_changed', fillInAddress);
			
			if(typeof(alt_address) !== "undefined" && alt_address) {
				autocomplete2 = new google.maps.places.Autocomplete(
					document.getElementById(alt_address), {
						strictBounds: false,
						types: ['geocode', 'establishment']
					}
				);
				
				// Avoid paying for data that you don't need by restricting the set of
				// place fields that are returned to just the address components.
				autocomplete2.setFields(['address_component', 'geometry', 'formatted_address', 'name']);

				// When the user selects an address from the drop-down, populate the
				// address fields in the form.
				autocomplete2.addListener('place_changed', fillInAddress);
			}
			
		}

		function fillInAddress() {
			// Get the place details from the autocomplete object.
			var place;
			var element = "txtADDRESS";
			if($("#" + address).is(":visible"))
				place = autocomplete.getPlace();
			else if($("#" + alt_address).is(":visible"))
				place = autocomplete2.getPlace();
			else 
				place = autocomplete.getPlace();
			
			for (var component in componentForm) {
				document.getElementById(component).value = '';
				document.getElementById(component).disabled = false;
			}
			
			if(typeof(place) !== "undefined" && place) {
				if (!place.geometry || !place.geometry.location) {
					// User entered the name of a Place that was not suggested and
					// pressed the Enter key, or the Place Details request failed.
					console.log("No details available for input: '" + place.name + "'");
				}
				else {
					if($("#" + address).is(":visible"))
						element = address;
					else if($("#" + alt_address).is(":visible"))
						element = alt_address;			
					$("#" + element.replace("txt", "hfLATITUDE_")).val(place.geometry.location.lat());
					$("#" + element.replace("txt", "hfLONGITUDE_")).val(place.geometry.location.lng());
				}
				
				if(place != null) {
					var typ = element.replace("txt","");
					var sele = "";
					var neigh = "";
					typ = typ.replace("_ADDRESS","");
					typ = typ.charAt(0).toUpperCase() + typ.substr(1).toLowerCase();
					for (var i = 0; i < place.address_components.length; i++) {
						var addressType = place.address_components[i].types[0];
						if (componentForm[addressType]) {
							var val = place.address_components[i][componentForm[addressType]];
							document.getElementById(addressType).value = val;
						}
						else if(addressType == "neighborhood") {
							neigh = removeAccents(place.address_components[i].long_name.toUpperCase());
						}
						else if(addressType == "sublocality_level_1") {
							sele = removeAccents(place.address_components[i].long_name.toUpperCase());
						}
					}
					if(sele != "" && neigh != "") {
						$("#cbZone" + typ + ' option').map(function() {
							if ($(this).text() == sele) return this;
						}).attr('selected', 'selected');						
						$("#cbZone" + typ).trigger("change");
						$("#cbZone" + typ + 'Sub option').map(function() {
							if ($(this).text() == neigh + " (" + sele + ")") return this;
						}).attr('selected', 'selected');						
						$("#Zone" + typ.toUpperCase()).fadeIn();
					}
				}
				
				//Verify the value
				if ($("#locality").val() != "" && $("#country").val() != "") {
					var searchterm = $("#locality").val() + " (" + $("#country").val() + ")";
					$("#cbCity option:contains(" + searchterm + ")").attr('selected', 'selected');
				}
			}
		}

		// Bias the autocomplete object to the user's geographical location,
		// as supplied by the browser's 'navigator.geolocation' object.
		function geolocate() {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(function(position) {
					var geolocation = {
						lat: position.coords.latitude,
						lng: position.coords.longitude
					};
					var circle = new google.maps.Circle({
						center: geolocation, 
						radius: position.coords.accuracy
					});
					autocomplete.setBounds(circle.getBounds());
				});
			}
		}
	
		function GetDistanceRoute(pos1, pos2) {
			var distance = 0;
			let directionsService = new google.maps.DirectionsService();
			const route = {
				origin: pos1,
				destination: pos2,
				travelMode: 'DRIVING'
			}
			directionsService.route(route,
				function(response, status) { // anonymous function to capture directions
					console.log(response);
					if (status !== 'OK') {
						console.log('Directions request failed due to ' + status);
						return;
					}
					else {
						var directionsData = response.routes[0].legs[0]; // Get data about the mapped route
						if (!directionsData) {
							console.log('Directions request failed');
							return;
						}
						else {
							distance = parseFloat(directionsData.distance.text);
						}
					}
					return distance;
				}
			);		
		}
		
	</script>
