<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	function url(){
		return sprintf(
			"%s://%s%s",
			isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
			$_SERVER['SERVER_NAME'],
			$_SERVER['REQUEST_URI']
		);
	}

    include("core/__load-resources.php");
	require_once("core/classes/configuration.php");

	$conf = new configuration("WEB_SITE");
	$website = $conf->verifyValue();
	$conf = new configuration("SITE_ROOT");
	$siteroot = $conf->verifyValue();
	$conf = new configuration("MAIN_MAIL");
	$mainmail = $conf->verifyValue();
	
	include_once("core/classes/interfaces.php");
	include_once("core/classes/resources.php");
		
	//Verifica si debe redireccionarse a otro script
	if(empty($_GET['ref']))
		$link = "";
	else
		$link = $_GET['ref'];	
?>	
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<!-- Meta, title, CSS, favicons, etc. -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="apple-touch-icon" sizes="57x57" href="img/logo/icons/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="img/logo/icons/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="img/logo/icons/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="img/logo/icons/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="img/logo/icons/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="img/logo/icons/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="img/logo/icons/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="img/logo/icons/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="img/logo/icons/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192" href="img/logo/icons/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="img/logo/icons/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="img/logo/icons/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="img/logo/icons/favicon-16x16.png">
		<meta name="msapplication-TileImage" content="img/logo/icons/ms-icon-144x144.png">
		
		<title>Vtapp Corporate</title>
		<!-- Font Awesome -->
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<!-- Ionicons -->
		<link rel="stylesheet" href="css/ionicons.min.css">
		<!-- Theme style -->
		<link rel="stylesheet" href="css/adminlte.min.css">
		<!-- Google Font: Source Sans Pro -->
		<link href="css/fonts.css" rel="stylesheet">
		<!-- iCheck -->
		<link rel="stylesheet" href="plugins/iCheck/flat/blue.css">
		<!-- Morris chart -->
		<link rel="stylesheet" href="plugins/morris/morris.css">
		<!-- jvectormap -->
		<link rel="stylesheet" href="plugins/jvectormap/jquery-jvectormap-1.2.2.css">
		<!-- Date Picker -->
		<link rel="stylesheet" href="plugins/datepicker/datepicker3.css">
		<!-- Daterange picker -->
		<link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
		<!-- bootstrap wysihtml5 - text editor -->
		<link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
		<style type="text/css">
			.jqstooltip { position: absolute;left: 0px;top: 0px;visibility: hidden;background: rgb(0, 0, 0) transparent;background-color: rgba(0,0,0,0.6);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000);-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#99000000, endColorstr=#99000000)";color: white;font: 10px arial, san serif;text-align: left;white-space: nowrap;padding: 5px;border: 1px solid white;z-index: 10000;}.jqsfield { color: white;font: 10px arial, san serif;text-align: left;}
		</style>
	</head>
	<body class="hold-transition sidebar-mini <?= $skin[2] ?>">
		<div class="wrapper">
				<!-- Navbar -->
			<nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
				<!-- Left navbar links -->
				<ul class="navbar-nav">
					<li class="nav-item">
						<a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
					</li>
					<li class="nav-item d-none d-sm-inline-block">
						<a href="dashboard.php" class="nav-link"><?= $_SESSION["MENU_1"] ?></a>
					</li>
				</ul>
			</nav>
			<!-- /.navbar -->
		<!-- End Header Top Area -->
			<!-- Main Sidebar Container -->
			<aside class="main-sidebar  elevation-4 <?= $skin[1] ?>" style="min-height: 772px;">
				<!-- Brand Logo -->
				<a href="dashboard.php" class="brand-link">
					<img src="img/logo/only_logo.png" alt="Vtapp Corporate" class="brand-image elevation-3" style="opacity: .8">
					<span class="brand-text font-weight-light">
						Vtapp Corporate
					</span>
				</a>
				<!-- Sidebar -->
				<div class="sidebar">
					<!-- Sidebar user panel (optional) -->
					<div class="user-panel mt-3 pb-3 mb-3 d-flex">
						<div class="brand-link"><span class="brand-text">Error 404</span></div>
					</div>
					<!-- Sidebar Menu -->
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
								<h1 class="m-0 text-dark"><?= $_SESSION["ERROR_PAGE"] ?> 404</h1>
							</div>
							<!-- /.col -->
							<div class="col-sm-6">
								<ol class="breadcrumb float-sm-right">
									<li class="breadcrumb-item"><a href="dashboard.php"><?= $_SESSION["MENU_1"] ?></a></li>
									<li class="breadcrumb-item active"><?= $_SESSION["ERROR_PAGE"] ?> 404</li>
								</ol>
							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->
					</div>
					<!-- /.container-fluid -->
				</div>
				<!-- /.content-header -->

				<!-- Main content -->
				<section class="content">
					<div class="error-page">
						<h2 class="headline text-warning"> 404 <?= 	url() ?></h2>
						<br />
						<h3><i class="fa fa-warning text-warning"></i> <?= $_SESSION["OOPS_PAGE_NOT_FOUND"] ?></h3>
						<p><?= $_SESSION["NEW_ERROR_404"] ?></p>
						<form class="search-form">
							<div class="input-group">
								<input type="text" name="search" class="form-control" placeholder="<?= $_SESSION["SEARCH"] ?>">
								<div class="input-group-append">
									<button type="submit" name="submit" class="btn btn-warning"><i class="fa fa-search"></i></button>
								</div>
							</div>
							<!-- /.input-group -->
						</form>
						<!-- /.error-content -->
					</div>
					<!-- /.error-page -->
				</section>
				<!-- /.content -->
			</div>
			<!-- /.content-wrapper -->
			
			<footer class="main-footer">
				<strong><a href="http://www.vtapp.com" target="_blank">Vtapp</a> - Copyright © 2019</strong>
				<div class="float-right d-none d-sm-inline-block"><b>AdmLTE Version</b> 3.0.0</div>
			</footer>
		<div id="sidebar-overlay"></div></div>
		<!-- ./wrapper -->

		<!-- jQuery -->
		<script src="plugins/jquery/jquery.min.js"></script>
		<!-- jQuery UI 1.11.4 -->
		<script src="plugins/jQueryUI/jquery-ui.1.12.1.min.js"></script>
		<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
		<script>
		  $.widget.bridge('uibutton', $.ui.button)
		</script>
		<!-- Bootstrap 4 -->
		<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
		<!-- Morris.js charts -->
		<script src="plugins/raphael/raphael-min.js"></script>
		<script src="plugins/morris/morris.min.js"></script>
		<!-- Sparkline -->
		<script src="plugins/sparkline/jquery.sparkline.min.js"></script>
		<!-- jvectormap -->
		<script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
		<script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
		<!-- jQuery Knob Chart -->
		<script src="plugins/knob/jquery.knob.js"></script>
		<!-- daterangepicker -->
		<script src="plugins/moment/moment.min.js"></script>
		<script src="plugins/daterangepicker/daterangepicker.js"></script>
		<!-- datepicker -->
		<script src="plugins/datepicker/bootstrap-datepicker.js"></script>
		<!-- Bootstrap WYSIHTML5 -->
		<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
		<!-- Slimscroll -->
		<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
		<!-- FastClick -->
		<script src="plugins/fastclick/fastclick.js"></script>
		<!-- AdminLTE App -->
		<script src="js/adminlte.js"></script>
		<!-- AdminLTE for demo purposes -->
		<script src="js/demo.js"></script>			
	</body>
</html>