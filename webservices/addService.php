<?
    //Inicio de sesion
    session_name('vtappcorp_session');
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
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
		<!-- Navbar -->
		<nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
			<div class="container">
			<!-- Brand Logo -->
				<a href="index.php" class="brand-link <?= $skn[1] ?>">
					<img src="<?= $image ?>" alt="<?= APP_NAME ?>" class="brand-image" style="opacity: .8" />
				</a>
				<button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse order-3" id="navbarCollapse">
					<!-- Left navbar links -->
					<ul class="navbar-nav">
						<li class="nav-item">
							<a href="index3.html" class="nav-link">Inicio</a>
						</li>
						<li class="nav-item">
							<a href="#" class="nav-link">Contacto</a>
						</li>
						<li class="nav-item dropdown">
							<a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Nuestros aliados</a>
							<ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
								<li><a href="#" class="dropdown-item">Interrapidísimo </a></li>
								<li><a href="#" class="dropdown-item">Servientrega</a></li>
								<li><a href="#" class="dropdown-item">4-72</a></li>
								<li><a href="#" class="dropdown-item">Centauros</a></li>
								<li class="dropdown-divider"></li>
								<!-- Level two dropdown-->
								<li class="dropdown-submenu dropdown-hover">
									<a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Precios</a>
									<ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
										<li>
											<a tabindex="-1" href="#" class="dropdown-item">level 2</a>
										</li>
										<!-- Level three dropdown-->
										<li class="dropdown-submenu">
											<a id="dropdownSubMenu3" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">level 2</a>
											<ul aria-labelledby="dropdownSubMenu3" class="dropdown-menu border-0 shadow">
												<li><a href="#" class="dropdown-item">3rd level</a></li>
												<li><a href="#" class="dropdown-item">3rd level</a></li>
											</ul>
										</li>
										<!-- End Level three -->
										<li><a href="#" class="dropdown-item">level 2</a></li>
										<li><a href="#" class="dropdown-item">level 2</a></li>
									</ul>
								</li>
								<!-- End Level two -->
							</ul>
						</li>
					</ul>
					<!-- SEARCH FORM -->
					<form class="form-inline ml-0 ml-md-3">
						<div class="input-group input-group-sm">
							<input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
							<div class="input-group-append">
								<button class="btn btn-navbar" type="submit">
									<i class="fa fa-search"></i>
								</button>
							</div>
						</div>
					</form>
				</div>
				<!-- Right navbar links -->
				<ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
					<!-- Messages Dropdown Menu -->
					<li class="nav-item dropdown">
						<a class="nav-link" data-toggle="dropdown" href="#">
							<i class="fa fa-comments"></i>
							<span class="badge badge-danger navbar-badge">3</span>
						</a>
						<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
							<a href="#" class="dropdown-item">
								<!-- Message Start -->
								<div class="media">
									<img src="../../dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
									<div class="media-body">
										<h3 class="dropdown-item-title">
											Brad Diesel
											<span class="float-right text-sm text-danger"><i class="fa fa-star"></i></span>
										</h3>
										<p class="text-sm">Call me whenever you can...</p>
										<p class="text-sm text-muted"><i class="fa fa-clock mr-1"></i> 4 Hours Ago</p>
									</div>
								</div>
								<!-- Message End -->
							</a>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item">
								<!-- Message Start -->
								<div class="media">
									<img src="../../dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
									<div class="media-body">
										<h3 class="dropdown-item-title">
											John Pierce
											<span class="float-right text-sm text-muted"><i class="fa fa-star"></i></span>
										</h3>
										<p class="text-sm">I got your message bro</p>
										  <p class="text-sm text-muted"><i class="fa fa-clock mr-1"></i> 4 Hours Ago</p>
									</div>
								</div>
								<!-- Message End -->
							</a>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item">
								<!-- Message Start -->
								<div class="media">
									<img src="../../dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
									<div class="media-body">
										<h3 class="dropdown-item-title">
											Nora Silvester
											<span class="float-right text-sm text-warning"><i class="fa fa-star"></i></span>
										</h3>
										<p class="text-sm">The subject goes here</p>
										<p class="text-sm text-muted"><i class="fa fa-clock mr-1"></i> 4 Hours Ago</p>
									</div>
								</div>
								<!-- Message End -->
							</a>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
						</div>
					</li>
					<!-- Notifications Dropdown Menu -->
					<li class="nav-item dropdown">
						<a class="nav-link" data-toggle="dropdown" href="#">
							<i class="fa fa-bell"></i>
							<span class="badge badge-warning navbar-badge">15</span>
						</a>
						<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
							<span class="dropdown-header">15 Notifications</span>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item">
								<i class="fa fa-envelope mr-2"></i> 4 new messages
								<span class="float-right text-muted text-sm">3 mins</span>
							</a>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item">
								<i class="fa fa-users mr-2"></i> 8 friend requests
								<span class="float-right text-muted text-sm">12 hours</span>
							</a>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item">
								<i class="fa fa-file mr-2"></i> 3 new reports
								<span class="float-right text-muted text-sm">2 days</span>
							</a>
							<div class="dropdown-divider"></div>
							<a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
						</div>
					</li>
					<li class="nav-item">
						<a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
							<i class="fa fa-th-large"></i>
						</a>
					</li>
				</ul>
			</div>
		</nav>
		<!-- /.navbar -->

		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<div class="content-header">
				<div class="container">
					<div class="row mb-2">
						<div class="col-md-6">
							<h1 class="m-0 text-dark">
								<i class="fa fa-motorcycle"></i>
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
				<div class="container">
					<div class="row">
						<div class="col-lg-7">
							<div class="card card-warning card-outline">
								<div class="card-header">
									<h5 class="card-title m-0">
										<i class="fa fa-map-marker"></i>
										Origen
									</h5>
								</div>
								<div class="card-body">
									<div id="map1"></div>
									<p class="card-text">
										Selecciona o ingresa la dirección de recogida
									</p>
									<div id="map-container-google-8" class="z-depth-1-half map-container-5">
										<iframe src="https://maps.google.com/maps?q=Barcelona&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" style="border:0;width:100%;height:465px;" allowfullscreen></iframe>
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
												<input type="text" class="form-control" placeholder="Nombre remitente">
											</div>									
										</div>
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">
														<i class="fa fa-map-marker"></i>
													</span>
												</div>
												<input type="text" class="form-control" placeholder="Dirección de origen">
												<div class="input-group-append">
													<div class="input-group-text"><strong>1</strong></div>
												</div>
											</div>									
										</div>
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">
														<i class="fa fa-phone"></i>
													</span>
												</div>
												<input type="text" class="form-control" placeholder="Contacto">
											</div>									
										</div>
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">
														<i class="fa fa-user-plus"></i>
													</span>
												</div>
												<input type="text" class="form-control" placeholder="Nombre destinatario">
											</div>									
										</div>
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">
														<i class="fa fa-map-pin"></i>
													</span>
												</div>
												<input type="text" class="form-control" placeholder="Dirección destino">
												<div class="input-group-append">
													<div class="input-group-text"><strong>2</strong></div>
												</div>
											</div>
										</div>									
										<div class="form-group">
											<div class="input-group">
												<div class="input-group-prepend">
													<span class="input-group-text">
														<i class="fa fa-phone"></i>
													</span>
												</div>
												<input type="text" class="form-control" placeholder="Contacto destinatario">
											</div>									
										</div>
										<div class="form-group">
											<label for="exampleFormControlTextarea1">Observaciones</label>
											<textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
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
</body>
</html>
