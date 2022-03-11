<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();

	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId(basename($_SERVER['REQUEST_URI']));
	
	require_once("core/__check-session.php");
	
	$result = checkSession("notifications.php",true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);

	$type = "ALL";
	//Captura las variables
    if(empty($_POST['type'])) {
        //Verifica el GET
        if(!empty($_GET['type'])) {
            $type = $_GET['type'];
        }
    }
    else {
        $type = $_POST['type'];
    }
	
	require_once("core/classes/notification.php");
	$noti = new notification($type);

?>
<!DOCTYPE html>
<html>
<head>
<?
	include("core/templates/__header.tpl");
?>
	<!-- DataTables -->
	<link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap4.css">
	<link rel="stylesheet" href="plugins/datatables/extensions/Responsive/css/responsive.bootstrap4.min.css">
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
							<h1 class="m-0 text-dark"><i class="fa fa-bell"></i> <?= $_SESSION["NOTIFICATIONS"] ?></h1>
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
			<section class="content">
				<div class="row">
					<div class="col-md-12">
						<div class="card card-primary card-outline">
							<div class="card-header">
								<h3 class="card-title"><?= $_SESSION["INBOX"] ?></h3>
								<div class="card-tools">
									<div class="input-group input-group-sm">
										<input type="text" class="form-control" placeholder="<?= $_SESSION["SEARCH_NOTIFICATION"] ?>">
										<div class="input-group-append">
											<div class="btn btn-primary">
												<i class="fa fa-search"></i>
											</div>
										</div>
									</div>
								</div>
								<!-- /.card-tools -->
							</div>
							<!-- /.card-header -->
							<div class="card-body p-0">
								<div class="mailbox-controls">
									<!-- Check all button -->
									<button type="button" class="btn btn-default btn-sm btnRefresh"><i class="fa fa-refresh"></i></button>
								</div>
								<div class="table-responsive mailbox-messages">
									<table class="table table-hover table-striped">
									<tbody id="bodyNotification">
<?= $noti->listTable() ?>
									</tbody>
									</table>
									<!-- /.table -->
								</div>
								<!-- /.mail-box-messages -->
							</div>
							<!-- /.card-body -->
							<div class="card-footer p-0">
								<!-- Check all button -->
								<button type="button" class="btn btn-default btn-sm btnRefresh"><i class="fa fa-refresh"></i></button>
							</div>
						</div>
						<!-- /. box -->
					</div>
				</div>
				<!-- /.row -->
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->

<?
	$title = $_SESSION["EMPLOYEES"];
	$icon = "<i class=\"fa fa-user\"></i>";
	include("core/templates/__modals.tpl");
	include("core/templates/__footer.tpl");
?>

    <!-- Data Table JS
		============================================ -->
	<!-- DataTables -->
	<script src="plugins/datatables/jquery.dataTables.js"></script>
	<script src="plugins/datatables/dataTables.bootstrap4.js"></script>
    <script src="plugins/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
	<!-- SlimScroll -->
	<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="plugins/fastclick/fastclick.js"></script>
	
    <script>
	var fields = ["EMPLOYEE_ID", "FULL_NAME", "IDNUMBER", "CODE", "EMAIL", "AREA_NAME", "ACCESS_NAME", "COSTCENTER", "BLOCKED", "ID_ACCESS"];
	$(document).ready(function() {
		$("#notificationCount").html("");
		$(".btnRefresh").on("click", function(e) {
			var $this = $(this);
			$.ajax({
				url:'core/actions/_load/__loadNotification.php',
				beforeSend: function (xhrObj) {
					$this.html("<i class=\"fa fa-refresh fa-spin\"></i>");
				},
				success:function(data){
					$this.html("<i class=\"fa fa-refresh\"></i>");
					$("#bodyNotification").html(data);
				}
			});		
		});
	});
	function activate(id,activate,name) {
		var _msg = (activate ? "<?= $_SESSION["ACTIVATE"] ?> " : "<?= $_SESSION["DEACTIVATE"] ?> ") + "<?= $_SESSION["EMPLOYEE"] ?> ";
		$("#spanTitle").html(_msg);
		$("#spanTitleName").html(name);
		$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
		$("#btnActivate").unbind("click");
		$("#btnActivate").bind("click", function() {
			var noty;
			$.ajax({
				url:'core/actions/_save/__activateData.php',
				data: { 
					txtId: id,
					activate: activate,
					txtClass: "employee", 
					txtLink: "employees.php",
					txtPre: "EMPLOYEE"
				},
				dataType: "json",
				beforeSend: function (xhrObj) {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
					noty = notify("", "dark", "", message, "", false);												
				},
				success:function(data){
					noty.close();
					notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
					table.ajax.reload();
				}
			});
		});
		$("#divActivateModal").modal("toggle");
	}
	
    </script>
<?
	include("core/templates/__messages.tpl");
?>

</body>
</html>
