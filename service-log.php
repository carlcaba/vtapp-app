<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();

	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId("services.php");
	
	require_once("core/__check-session.php");
	
	$result = checkSession("services.php",true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);

	$idSrv = "";
	
	//Captura las variables
    if(empty($_POST['id'])) {
        //Verifica el GET
        if(!empty($_GET['id'])) {
            $idSrv = $_GET['id'];
        }
    }
    else {
        $idSrv = $_POST['id'];
    }

	require_once("core/classes/service_log.php");
    $serv = new service_log();
    $serv->setService($idSrv);

    if($serv->nerror > 0) {
        $_SESSION["vtappcorp_user_alert"] = $serv->error;
        $inter->redirect("services.php");        
    }

    //Completa los recursos de cada clase
    $serv->service->getComments();
    $serv->getComments();
	
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
							<h1 class="m-0 text-dark"><i class="fa fa-history"></i> <?= $_SESSION["SERVICE_LOG"] ?></h1>
						</div
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
					<div class="row">
						<div class="col-md-4">
							<!-- Profile Image -->
							<div class="card card-primary">
                                <div class="card-header">
                                    <h5 class="m-0"><i class="fa fa-motorcycle"></i> <?= $_SESSION["SERVICE"] ?></h5>
								</div>
                                <div class="card-body">
                                    <dl>
										<!--
                                        <dt><?= $serv->service->arrColComments["ID"] ?></dt>
                                        <dd><?= $serv->service->ID ?></dd>
										-->
                                        <dt><?= $serv->service->arrColComments["DELIVER_TO"] ?></dt>
                                        <dd><?= $serv->service->DELIVER_TO ?></dd>
                                        <dt><?= $serv->service->arrColComments["DELIVER_ADDRESS"] ?></dt>
                                        <dd><?= $serv->service->DELIVER_ADDRESS ?></dd>
                                        <dt><?= $serv->service->arrColComments["REQUESTED_BY"] ?></dt>
                                        <dd><?= $serv->service->REQUESTED_BY ?></dd>
                                        <dt><?= $serv->service->arrColComments["CLIENT_ID"] ?></dt>
                                        <dd><?= $serv->service->client->CLIENT_NAME ?></dd>
                                        <dt><?= $serv->service->arrColComments["DELIVERY_TYPE"] ?></dt>
                                        <dd><?= $serv->service->type->getResource() ?></dd>
                                        <dt><?= $serv->service->arrColComments["STATE_ID"] ?></dt>
                                        <dd><?= $serv->service->state->getResource() ?></dd>
                                        <dt><?= $serv->service->arrColComments["REGISTERED_BY"] ?></dt>
                                        <dd><?= $serv->service->REGISTERED_BY ?></dd>
                                        <dt><?= $serv->service->arrColComments["REGISTERED_ON"] ?></dt>
                                        <dd><?= date("d-M-Y h:nn", strtotime($serv->service->REGISTERED_ON)) ?></dd>

                                    </dl>
                                </div>
								<!-- /.card-body -->
							</div>
							<!-- /.card -->
						</div>
						<!-- /.col -->
						<div class="col-md-8">
							<div class="card card-primary">
                                <div class="card-header">
                                    <h5 class="m-0"><i class="fa fa-clock-o"></i> <?= $_SESSION["TIMELINE"] ?></h5>
								</div>
								<!-- /.card-header -->
								<div class="card-body">
									<!-- The timeline -->
									<ul class="timeline timeline-inverse">
<?= $serv->showTimelineActivity() ?>
									</ul>
									<!-- /.tab-content -->
								</div>
								<!-- /.card-body -->
							</div>
							<!-- /.nav-tabs-custom -->
						</div>
						<!-- /.col -->
					</div>
					<!-- /.row -->
				</div>
				<!-- /.container-fluid -->
			</section>
			<!-- /.content -->
		</div>
		
<?
	include("core/templates/__footer.tpl");
?>
	<!-- FastClick -->
	<script src="plugins/fastclick/fastclick.js"></script>
    <script>
	$(document).ready(function() {

	});
	function showMap(lat,lng) {
		$("#divMapModal").modal("toggle");
		$('#divMapModal').on('shown.bs.modal', function() {
			var pos = {
				lat: lat,
				lng: lng
			};
			infoWindow.setPosition(pos);
			infoWindow.setContent('<?= $_SESSION["LOCATION_FOUND"] ?>');
			infoWindow.open(map);		
		});
	}
	
    </script>

<?
	$titleMapModal = $_SESSION["CURRENT_LOCATION"];
	$showOkMap = false;
	include("core/templates/__mapModal.tpl");
	include("core/templates/__messages.tpl");
?>

</body>
</html>