<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");
	
	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();

	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId(basename(__FILE__));
	
	require_once("core/__check-session.php");
	
	$result = checkSession("dashboard.php",true);
	
	if($result["success"] !== true) 
		$inter->redirect($result["link"]);
	
	//Verifica si debe redireccionarse a otro script
	if(empty($_GET['ref']))
		$link = "";
	else
		$link = $_GET['ref'];	
	
	require_once("core/classes/users.php");
    $usua = new users($_SESSION["vtappcorp_userid"]);
    $usua->__getInformation();

	require_once("core/classes/logs.php");
	$log = new logs();
	
?>
<!DOCTYPE html>
<html>
<head>
	<script src='https://api.mapbox.com/mapbox-gl-js/v2.0.1/mapbox-gl.js'></script>
	<link href='https://api.mapbox.com/mapbox-gl-js/v2.0.1/mapbox-gl.css' rel='stylesheet' />
<?
	include("core/templates/__header.tpl");
?>
</head>
<body class="hold-transition sidebar-mini <?= $skin[2] ?>">
	<div class="wrapper">
<?
	include("core/templates/__toparea.tpl");
?>
		<!-- Main Sidebar Container -->
		<aside class="main-sidebar  elevation-4 <?= $skin[1] ?>">
<?
	include("core/templates/__appname.tpl");
?>
			<!-- Sidebar -->
			<div class="sidebar">
<?
	include("core/templates/__userinfo.tpl");
	include("core/templates/__menu.tpl");
?>
			<!-- /.sidebar-menu -->
			</div>
			<!-- /.sidebar -->
		</aside>
		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0 text-dark"><?= $_SESSION["DASHBOARD"] ?></h1>
						</div>
						<!-- /.col -->
<?
	include("core/templates/__breadcum.tpl");
?>
						<!-- /.col -->
					</div>
					<!-- /.row -->
				</div>
				<!-- /.container-fluid -->
			</div>
			<!-- /.content-header -->

			<!-- Main content -->
			<section class="content">
				<div class="container-fluid">
<?
	include("core/templates/__widgets.tpl");
	include("core/templates/__graphic.tpl");
?>
					<!-- Main row -->
					<div class="row">
						<!-- Left col -->
						<div class="col-md-12">
<?
	include("core/templates/__worldMap.tpl");
?>
						</div>
						<!-- /.col -->
					</div>
					<!-- /.card -->
					<div class="row">
						<div class="col-md-8">
<?
	include("core/templates/__directChat.tpl");
?>
						</div>
						<div class="col-md-4">
							<div class="card direct-chat direct-chat-warning">
								<div class="card-header">
									<h3 class="card-title"><?= $_SESSION["CONTACTS"] ?></h3>
									<div class="card-tools">
										<?= $chatBadge ?>
										<button type="button" class="btn btn-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
										<button type="button" class="btn btn-tool" data-widget="remove"><i class="fa fa-times"></i></button>
									</div>
								</div>
								<!-- /.card-header -->
								<div class="card-body">
<?
	include("core/templates/__contacts.tpl");
?>
								</div>
								<!-- /.card-body -->
							</div>
							<!--/.direct-chat -->
						</div>
						<!-- /.col -->
					</div>
				</div><!-- /.container-fluid -->
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->
<?
	include("core/templates/__footer.tpl");
	include("core/templates/__messages.tpl");
?>
	<!-- PAGE PLUGINS -->
	<!-- SparkLine -->
	<script src="plugins/sparkline/jquery.sparkline.min.js"></script>
	<!-- jVectorMap -->
	<script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
	<script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
	<!-- SlimScroll 1.3.0 -->
	<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- LeafLetJS -->
	<script src="plugins/leaflet/leaflet.js"></script>
	
	<script>
		var mymap = L.map('world-map-markers').setView([<?= $_SESSION["vtappcorp_location"] ?>], 19);		
		L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoiY2FybGNhYmEiLCJhIjoiY2s5MGdvd2txMDBtYzNsbWpld3p3OG1xaCJ9.NCcu4dFUyaPvyTO3tXYmnA', {
			attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
			maxZoom: 20,
			id: 'mapbox/streets-v11',
			tileSize: 512,
			zoomOffset: -1,
			accessToken: 'pk.eyJ1IjoiY2FybGNhYmEiLCJhIjoiY2s5MGdvd2txMDBtYzNsbWpld3p3OG1xaCJ9.NCcu4dFUyaPvyTO3tXYmnA'
		}).addTo(mymap);
		function onMapClick(e) {
			alert("You clicked the map at " + e.latlng);
			console.log(e);
		}
		function success(pos) {
			var crd = pos.coords;
			mymap.setView([crd.latitude, crd.longitude], 19);		
			var popup = L.popup()
				.setLatLng([crd.latitude, crd.longitude])
				.setContent("<b><?= $_SESSION["INFORMATION"] ?></b><br /><?= $_SESSION["YOUR_LOCATION"] ?>")
				.openOn(mymap);
		};
		function error(err) {
			var popup = L.popup()
				.setLatLng([<?= $_SESSION["vtappcorp_location"] ?>])
				.setContent("<?= $_SESSION["CANT_FIND_LOCATION"] ?>")
				.openOn(mymap);			
			console.warn('ERROR(' + err.code + '): ' + err.message);
		};		

		mymap.on('click', onMapClick);		
		$(function () {
			var options = {
				enableHighAccuracy: true,
				timeout: 5000,
				maximumAge: 0
			};			
			navigator.geolocation.getCurrentPosition(success, error, options);
		});
	</script>
</body>
</html>
