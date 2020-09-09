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
							<h1 class="m-0 text-dark">404 <?= $_SESSION["ERROR_PAGE"] ?></h1>
						</div>
						<!-- /.col -->
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="dashboard.php"><?= $_SESSION["MENU_1"] ?></a></li>
								<li class="breadcrumb-item active">404 <?= $_SESSION["ERROR_PAGE"] ?></li>
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
					<div class="error-content">
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
					</div>
					<!-- /.error-content -->
				</div>
				<!-- /.error-page -->
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->
		
<?
	include("core/templates/__footer.tpl");
	include("core/templates/__messages.tpl");
?>
</body>
</html>
