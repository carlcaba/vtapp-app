<?
    //Inicio de sesion
    session_name('vtappcorp_session');
	session_start();

    date_default_timezone_set('America/Bogota');

	//Incluye las clases requeridas
	include_once("../core/classes/configuration.php");

	if (!defined('APP_NAME')) {
		$conf = new configuration("APP_NAME");
		define("APP_NAME", $conf->verifyValue());
	}	
	if (!defined('LANGUAGE')) {
		define("LANGUAGE",2);
		$_SESSION["LANGUAGE"] = 2;
	}	

	$image = "../img/logo/logo.png";
	$title = $_SESSION["vtappcorp_appname"];
	$appname = APP_NAME;
	$title = substr($title,0,5);
	$appname .= "\n<small class=\"small-brand-text\">$title</small>\n";
	$skn = explode(" ",$skin[0]);
	if(count($skn) == 3)
		$skn[1] = $skn[2];
	
	$conf = new configuration("MAPS_API_URL");
	$map_url = $conf->verifyValue();
	$conf = new configuration("MAPS_API_KEY");
	$map_url = $map_url . $map_api;
	
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<!-- Meta, title, CSS, favicons, etc. -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="apple-touch-icon" sizes="57x57" href="../img/logo/icons/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="../img/logo/icons/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="../img/logo/icons/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="../img/logo/icons/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="../img/logo/icons/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="../img/logo/icons/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="../img/logo/icons/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="../img/logo/icons/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="../img/logo/icons/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="../img/logo/icons/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="../img/logo/icons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="../img/logo/icons/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="../img/logo/icons/favicon-16x16.png">
	<meta name="msapplication-TileImage" content="../img/logo/icons/ms-icon-144x144.png">
	
	<title><?= APP_NAME ?></title>
	<!-- Font Awesome -->
	<link rel="stylesheet" href="../css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="../css/ionicons.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="../css/adminlte.min.css">
	<!-- Google Font: Source Sans Pro -->
	<link href="../css/fonts.css" rel="stylesheet">
	<!-- iCheck -->
	<link rel="stylesheet" href="../plugins/iCheck/flat/blue.css">
	<!-- Morris chart -->
	<link rel="stylesheet" href="../plugins/morris/morris.css">
	<!-- leaflet -->
	<link rel="stylesheet" href="../plugins/leaflet/leaflet.css">
	<!-- Date Picker -->
	<link rel="stylesheet" href="../plugins/datepicker/datepicker3.css">
	<!-- Daterange picker -->
	<link rel="stylesheet" href="../plugins/daterangepicker/daterangepicker-bs3.css">
	<!-- bootstrap wysihtml5 - text editor -->
	<link rel="stylesheet" href="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
</head>
<body class="hold-transition layout-top-nav">
	<div class="wrapper">
		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<div class="content-header">
				<div class="container">
					<div class="row mb-2">
						<div class="col-md-6">
							<a href="index.php" class="brand-link add-service <?= $skn[1] ?>">
								<img src="<?= $image ?>" alt="<?= APP_NAME ?>" class="brand-image" style="opacity: .8" />
							</a>
							<h1 class="m-0 text-dark">
								Solicitar un mensajero <small><i>express</i></small>
							</h1>
						</div>
						<!-- /.col -->
						<div class="col-md-6"></div>
						<!-- /.col -->
					</div>
					<!-- /.row -->
				</div>
				<!-- /.container-fluid -->
			</div>
			<!-- /.content-header -->
			
			<!-- Main content -->
			<div class="content">
				<div class="container d-flex flex-column">
					<div class="row">
						<div class="col-lg-7">
							<div class="card card-warning card-outline">
								<div class="card-header">
									<h5 id="headerMap" class="card-title m-0">
										<i id="iconMap" class="fa fa-map-marker"></i>
										<span id="titleMap">Origen</span>
									</h5>
								</div>
								<div class="card-body">
									<p class="card-text">
										<span id="textMap">Selecciona o ingresa la dirección de recogida</span>
									</p>
									<div id="mapContainer" class="z-depth-1-half map-container-5">
										<!--
										<iframe src="https://maps.google.com/maps?z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" style="border:0;width:100%;height:465px;" allowfullscreen></iframe>
										-->
									</div>									
									<div class="custom-control custom-switch">
										<input type="checkbox" class="custom-control-input" id="chkUseCurrentLocation">
										<label class="custom-control-label" for="chkUseCurrentLocation">Usar mi ubicación actual</label>
									</div>
								</div>
							</div>
							<!-- /.card -->
						</div>
						<div class="col-lg-5">
							<div class="card card-success card-outline">
								<div class="card-header">
									<h5 class="card-title m-0">
										<i class="fa fa-info-circle"></i>
										Más información
									</h5>
								</div>
								<div class="card-body">
									<h6 class="card-title">Completa la información solicitada</h6>
									<p>&nbsp;</p>
									<form class="form">
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">
														<i class="fa fa-user"></i>
													</span>
												</div>
												<input type="text" class="form-control" id="txtNAME" name="txtNAME" placeholder="Nombre remitente *" required autocomplete="off">
											</div>									
										</div>
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">
														<i class="fa fa-map-marker"></i>
													</span>
												</div>
												<input type="text" class="form-control" id="txtADDRESS1" name="txtADDRESS1" data-toggle="map" data-icon="fa-map-marker" data-title="Origen" data-text="Selecciona o ingresa la dirección de recogida" data-radio="request" placeholder="Dirección de origen *" required>
												<div class="input-group-append">
													<span class="input-group-text">
														<input type="radio" id="optAddressrequest" name="optAddress" value="request">
													</span>
												</div>
												<input type="hidden" id="hfLATITUDE_ADDRESS1" name="hfLATITUDE_ADDRESS1" value="" />
												<input type="hidden" id="hfLONGITUDE_ADDRESS1" name="hfLONGITUDE_ADDRESS1" value="" />
											</div>									
										</div>
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">
														<i class="fa fa-phone"></i>
													</span>
												</div>
												<input type="text" class="form-control" id="txtCONTACT" name="txtCONTACT" placeholder="Contacto *" required autocomplete="off">
											</div>									
										</div>
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">
														<i class="fa fa-user-plus"></i>
													</span>
												</div>
												<input type="text" class="form-control" id="txtDELIVER_TO" name="txtDELIVER_TO" placeholder="Nombre destinatario *" required autocomplete="off">
											</div>									
										</div>
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">
														<i class="fa fa-map-pin"></i>
													</span>
												</div>
												<input type="text" class="form-control" id="txtADDRESS2" name="txtADDRESS2" data-toggle="map" data-icon="fa-map-pin" data-title="Destino" data-text="Selecciona o ingresa la dirección de entrega" data-radio="deliver" placeholder="Dirección destino *" required>
												<div class="input-group-append">
													<span class="input-group-text">
														<input type="radio" id="optAddressdeliver" name="optAddress" value="deliver">
													</span>
												</div>
												<input type="hidden" id="hfLATITUDE_ADDRESS2" name="hfLATITUDE_ADDRESS2" value="" />
												<input type="hidden" id="hfLONGITUDE_ADDRESS2" name="hfLONGITUDE_ADDRESS2" value="" />
											</div>
										</div>									
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">
														<i class="fa fa-phone"></i>
													</span>
												</div>
												<input type="text" class="form-control" id="txtDELIVER_CONTACT" name="txtDELIVER_CONTACT" placeholder="Contacto destinatario *" required autocomplete="off">
											</div>									
										</div>
										<div class="form-group">
											<textarea class="form-control" id="txtCOMMENTS" name="txtCOMMENTS" rows="1" placeholder="Observaciones adicionales"></textarea>
										</div>							
										<p><small>Los campos marcados con * son requeridos</small></p>
										<div class="form-group">
											<div class="custom-control custom-checkbox">
												<input class="custom-control-input" type="checkbox" value="" id="chkAcceptTerms" name="chkAcceptTerms" required>
												<label for="chkAcceptTerms" class="custom-control-label">
													Acepto los <a href="#" target="_blank">términos y condiciones de VTAPP®</a>
												</label>
											</div>										
										</div>										
										<a href="#" class="btn btn-primary">Solicitar</a>
									</form>
								</div>
							</div>
						</div>
						<!-- /.col-md-6 -->
					</div>
					<!-- /.row -->
				</div>
				<!-- /.container-fluid -->
			</div>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->
		
		<!-- Control Sidebar -->
		<aside class="control-sidebar control-sidebar-dark">
			<!-- Control sidebar content goes here -->
			<div class="p-3">
				<h5>Title</h5>
				<p>Sidebar content</p>
			</div>
		</aside>
		<!-- /.control-sidebar -->

		<footer class="main-footer">
			<strong><a href="http://www.vtapp.com" target="_blank">Vtapp</a> - v.2.5 - Copyright &copy; 2019-<a href="javascript: showVariables();"><?= date("Y") ?></a></strong>
			<div class="float-right d-none d-sm-inline-block"><b>AdmLTE Version</b> 3.0.8</div>
		</footer>
	</div>
	<!-- ./wrapper -->

	<!-- REQUIRED SCRIPTS -->
	
	<!-- jQuery -->
	<!-- jQuery -->
	<script src="../plugins/jquery/jquery.min.js"></script>
	<!-- jQuery UI 1.11.4 -->
	<script src="../plugins/jQueryUI/jquery-ui.1.12.1.min.js"></script>
	<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
	<!-- Bootstrap 4 -->
	<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- daterangepicker -->
	<script src="../plugins/moment/moment.min.js"></script>
	<script src="../plugins/daterangepicker/daterangepicker.js"></script>
	<!-- datepicker -->
	<script src="../plugins/datepicker/bootstrap-datepicker.js"></script>
	<!-- Bootstrap WYSIHTML5 -->
	<script src="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
	<!-- Slimscroll -->
	<script src="../plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="../plugins/fastclick/fastclick.js"></script>
	<!-- AdminLTE App -->
	<script src="../js/adminlte.js"></script>
	<!-- Google Maps -->
    <script src="<?= $map_url ?>" async defer></script>
	<script>
		var map;
		var marker = false; ////Has the user plotted their location marker? 
		//Function called to initialize / create the map.
		//This is called when the page has loaded.
		function initMap() {
			//The center location of our map.
			var centerOfMap = new google.maps.LatLng(52.357971, -6.516758);

			//Map options.
			var options = {
			  center: centerOfMap, //Set center.
			  zoom: 7 //The zoom value.
			};

			//Create the map object.
			map = new google.maps.Map(document.getElementById('mapContainer'), options);

			//Listen for any clicks on the map.
			google.maps.event.addListener(map, 'click', function(event) {                
				//Get the location that the user clicked.
				var clickedLocation = event.latLng;
				//If the marker hasn't been added.
				if(marker === false){
					//Create the marker.
					marker = new google.maps.Marker({
						position: clickedLocation,
						map: map,
						draggable: true //make it draggable
					});
					//Listen for drag events!
					google.maps.event.addListener(marker, 'dragend', function(event){
						markerLocation();
					});
				} else{
					//Marker has already been added, so just change its location.
					marker.setPosition(clickedLocation);
				}
				//Get the marker's location.
				markerLocation();
			});
		}
				
		//This function will get the marker's current location and then add the lat/long
		//values to our textfields so that we can save the location.
		function markerLocation(){
			//Get location.
			var currentLocation = marker.getPosition();
			//Add lat and lng values to a field that we can save.
			document.getElementById('lat').value = currentLocation.lat(); //latitude
			document.getElementById('lng').value = currentLocation.lng(); //longitude
		}
				
				
		//Load the map when the page has finished loading.
		google.maps.event.addDomListener(window, 'load', initMap);	
		$(document).ready(function() {
			$('[data-toggle="map"]').focusin(function() {
				var icon = $(this).data("icon");
				var title = $(this).data("title");
				var text = $(this).data("text");
				var radio = $(this).data("radio");
				$("#iconMap").attr("class", "fa " + icon);
				$("#textMap").html(text);
				$("#titleMap").html(title);
				$('#headerMap').addClass('highlight');
				$('#textMap').addClass('highlight');
				$("#optAddress" + radio).prop("checked", true);
				setTimeout(function() {
					$('#headerMap').removeClass("highlight");
					$('#textMap').removeClass("highlight");
				}, 1000);
			});
			$('input:radio[name="optAddress"]').on("click", function() {
				if ($(this).is(':checked')) {
					$("input[data-radio='" + $(this).val() + "']").focus();
				}
			});			
			$("#txtNAME").focus();
		});
	</script>
</body>
</html>
