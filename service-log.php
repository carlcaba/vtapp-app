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
                                    <h5 class="m-0"><i class="fa fa-motorcycle"></i> <?= $_SESSION["SERVICE"] ?> <small>Id: <?= $serv->service->ID ?></small></h5>
								</div>
                                <div class="card-body">
                                    <dl>
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
									<div class="timeline timeline-inverse">
<!--
										<div class="time-label">
											<span class="bg-danger">
												10 Feb. 2014
											</span>
										</div>
										<div>
											<i class="fa fa-envelope bg-primary"></i>
											<div class="timeline-item">
												<span class="time"><i class="far fa-clock"></i> 12:05</span>
												<h3 class="timeline-header"><a href="#">Support Team</a> sent you an email</h3>
												<div class="timeline-body">
													Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
													weebly ning heekya handango imeem plugg dopplr jibjab, movity
													jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
													quora plaxo ideeli hulu weebly balihoo...
												</div>
												<div class="timeline-footer">
													<a href="#" class="btn btn-primary btn-sm">Read more</a>
													<a href="#" class="btn btn-danger btn-sm">Delete</a>
												</div>
											</div>
										</div>
										<div>
											<i class="fa fa-user bg-info"></i>
											<div class="timeline-item">
												<span class="time"><i class="far fa-clock"></i> 5 mins ago</span>
												<h3 class="timeline-header border-0"><a href="#">Sarah Young</a> accepted your friend request</h3>
											</div>
										</div>
										<div>
											<i class="fa fa-comments bg-warning"></i>
											<div class="timeline-item">
												<span class="time"><i class="far fa-clock"></i> 27 mins ago</span>
												<h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>
												<div class="timeline-body">
													Take me to your leader!
													Switzerland is small and neutral!
													We are more like Germany, ambitious and misunderstood!
												</div>
												<div class="timeline-footer">
													<a href="#" class="btn btn-warning btn-flat btn-sm">View comment</a>
												</div>
											</div>
										</div>
										<div class="time-label">
											<span class="bg-success">
												3 Jan. 2014
											</span>
										</div>
										<div>
											<i class="fa fa-camera bg-purple"></i>
											<div class="timeline-item">
												<span class="time"><i class="far fa-clock"></i> 2 days ago</span>
												<h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>
												<div class="timeline-body">
													<img src="http://placehold.it/150x100" alt="...">
													<img src="http://placehold.it/150x100" alt="...">
													<img src="http://placehold.it/150x100" alt="...">
													<img src="http://placehold.it/150x100" alt="...">
												</div>
											</div>
										</div>
									<ul class="timeline timeline-inverse">
-->
<?= $serv->showTimelineActivity() ?>
									<!-- </ul> -->
									<!-- /.tab-content -->
										<div>
											<i class="fa fa-clock-o bg-gray"></i>
										</div>
									</div>
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