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
	
	$result = checkSession("profile.php",true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);

	$idUsr = $_SESSION["vtappcorp_userid"];
	
	//Captura las variables
    if(empty($_POST['id'])) {
        //Verifica el GET
        if(!empty($_GET['id'])) {
            $idUsr = $_GET['id'];
        }
    }
    else {
        $idUsr = $_POST['id'];
    }

	require_once("core/classes/users.php");
	require_once("core/classes/employee.php");
	require_once("core/classes/logs.php");
	$usua = new users($_SESSION["vtappcorp_userid"]);
	
	$log = new logs();
	$admin = ($usua->ACCESS_ID >= 90);
	
	if($usua->ACCESS_ID < 90)
		$idUsr = $_SESSION["vtappcorp_userid"];

	$usua = new users($idUsr);
	
	$empl = new employee();
	$empl->ID_USER = $idUsr;
	$empl->getInformationByOtherInfo("ID_USER");
	
	$sendMessage = ($idUsr == $_SESSION["vtappcorp_userid"]) ? "disabled" : "";
	$display = ($idUsr != $_SESSION["vtappcorp_userid"]) ? "d-none" : "";

	if($empl->nerror > 0) {
		$profileForm = $usua->showProfileForm(($admin) ? "" : $sendMessage);
	}
	else {
		$profileForm = $empl->showProfileForm(($admin) ? "" : $sendMessage);
	}
	
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
	<!-- daterange picker -->
	<link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
	<!-- Select2 -->
	<link rel="stylesheet" href="plugins/select2/select2.min.css">
	<!-- FileInput -->
    <link href="plugins/fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="plugins/fileinput/themes/explorer-fas/theme.css" media="all" rel="stylesheet" type="text/css"/>
	
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
							<h1 class="m-0 text-dark"><i class="fa fa-user"></i> <?= $_SESSION["USER"] ?></h1>
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
					<div class="row">
						<div class="col-md-3">
							<!-- Profile Image -->
							<div class="card card-primary card-outline">
								<div class="card-body box-profile">
									<form class="form form-vertical text-center" method="post" enctype="multipart/form-data" role="form" id="frmLoadImage" name="frmLoadImage">
										<div class="le-avatar">
											<div class="file-loading">
												<input id="fiAvatar" name="fiAvatar" type="file" required>
											</div>
										</div>		
										<input type="hidden" name="hfIdUser" id="hfIdUser" value="<?= $idUsr ?>" />
									</form>
									<h3 class="profile-username text-center"><?= $usua->FIRST_NAME . " " . $usua->LAST_NAME ?></h3>
									<p class="text-muted text-center"><?= $usua->access->getResource() ?></p>
									<ul class="list-group list-group-unbordered mb-3">
										<li class="list-group-item">
											<b><?= $_SESSION["EMPLOYEE_TABLE_TITLE_4"] ?></b><a class="float-right"><?= $empl->nerror > 0 ? $_SESSION["ONLY_USER"] : $empl->CODE ?></a>
										</li>
										<li class="list-group-item">
											<b><?= $_SESSION["EMPLOYEE_TABLE_TITLE_3"] ?></b><a class="float-right"><?= $empl->nerror > 0 ? $_SESSION["DATA_UNKNOWN"] : $empl->IDNUMBER ?></a>
										</li>
										<li class="list-group-item">
											<small class="center"><?= $usua->EMAIL ?></small>
										</li>
									</ul>
									<a href="#" class="btn btn-primary btn-block <?= $sendMessage ?>" id="btnSendMessage" name="btnSendMessage">
										<i class="fa fa-comment"></i> 
										<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><b><?= $_SESSION["SEND_MESSAGE"] ?></b></span>
									</a>
									<a class="btn btn-default btn-block <?= $display ?>" href="change-password.php?txtUser=<?= $idUsr ?>&hfLink=profile.php">
										<i class="fa fa-user-secret"></i>
										<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["CHANGE_PASSWORD"] ?></span>
									</a>
									<a class="btn btn-default btn-block <?= $display ?>" href="core/__exit.php?lockscreen=true&ref=dashboard.php">
										<i class="fa fa-lock"></i>
										<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["MENU_72"] ?></span>
									</a>
									<a class="btn btn-default btn-block <?= $display ?>" href="notifications.php">
										<i class="fa fa-bell"></i>
										<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["NOTIFICATIONS"] ?></span>
									</a>
									<a class="btn btn-default btn-block <?= $display ?>" href="#">
										<i class="fa fa-comments"></i>
										<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["CHAT"] ?></span>
									</a>
									
								</div>
								<!-- /.card-body -->
							</div>
							<!-- /.card -->

						</div>
						<!-- /.col -->
						<div class="col-md-9">
							<div class="card">
								<div class="card-header p-2">
									<ul class="nav nav-pills">
										<li class="nav-item"><a class="nav-link active" href="#timeline" data-toggle="tab"><?= $_SESSION["ACTIVITY"] ?></a></li>
										<li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab"><?= $_SESSION["SETTINGS"] ?></a></li>
									</ul>
								</div>
								<!-- /.card-header -->
								<div class="card-body">
									<div class="tab-content">
										<!-- /.tab-pane -->
										<div class="active tab-pane" id="timeline">
											<!-- The timeline -->
											<ul class="timeline timeline-inverse">
<?= $log->showTimelineActivity($idUsr) ?>
											</ul>
										</div>
										<!-- /.tab-pane -->

										<div class="tab-pane" id="settings">
											<form class="form-horizontal" role="form" id="frmProfile" name="frmProfile">
<?= $profileForm ?>												
												<div class="form-group">
													<div class="col-sm-offset-2 col-sm-12">
														<button type="button" id="btnSaveProfile" name="btnSaveProfile" class="btn btn-success pull-right">
															<i class="fa fa-floppy-o"></i>
															<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["SAVE_CHANGES"] ?></span>
														</button>
													</div>
												</div>
											</form>
										</div>
										<!-- /.tab-pane -->
									</div>
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
	$title = $_SESSION["PROFILE"];
	$icon = "<i class=\"fa fa-user\"></i>";
	$noEdit = true;
	include("core/templates/__modals.tpl");
	include("core/templates/__footer.tpl");
?>
	<!-- FastClick -->
	<script src="plugins/fastclick/fastclick.js"></script>

	<!-- FileInput -->
    <script src="plugins/fileinput/js/plugins/sortable.js" type="text/javascript"></script>
    <script src="plugins/fileinput/js/fileinput.js" type="text/javascript"></script>
    <script src="plugins/fileinput/themes/fa/theme.js" type="text/javascript"></script>
    <script src="plugins/fileinput/themes/explorer-fa/theme.js" type="text/javascript"></script>
	<!-- Select2 -->
	<script src="plugins/select2/select2.full.min.js"></script>
	
<?
	$fup = "false";
	if($_SESSION["LANGUAGE"] != "1") {
		$fup = "true";
?>
    <script src="plugins/fileinput/js/locales/<?= $_SESSION["LANGUAGE"] ?>.js" type="text/javascript"></script>
    <script src="plugins/select2/i18n/<?= $_SESSION["LANGUAGE"] ?>.js"></script>
    <script src="js/resources.js"></script>
<?
	}
?>
    <script>
	$(document).ready(function() {
		$("#btnSaveProfile").on("click", function(e) {
			var form = document.getElementById('frmProfile');
			var noty;
			if (form.checkValidity() === false) {
				window.event.preventDefault();
				window.event.stopPropagation();
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				return false;
			}
			var title = "<?= $_SESSION["EDIT"] . " " . $_SESSION["PROFILE"] ?>";
			var url = $("#hfLinkAction").val();
			var $frm = $("#frmProfile");
			var datas = JSON.stringify($frm.serializeObject());
			$("#spanTitle").html(title);
			$("#spanTitleName").html("");
			$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
			$("#btnActivate").unbind("click");
			$("#btnActivate").bind("click", function() {
				var noty;
				$.ajax({
					url: url,
					data: { strModel: datas },
					dataType: "json",
					beforeSend: function (xhrObj) {
						var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
						noty = notify("", "dark", "", message, "", false);												
					},
					success:function(data){
						noty.close();
						$("#divEditModal").modal('hide');
						notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
						if(data.success)
							location.href = data.link;
					}
				});
			});
			$("#divActivateModal").modal("toggle");			
		});
		$("#fiAvatar").fileinput({
			overwriteInitial: false,
			maxFileSize: 2000,
			showClose: false,
			showCaption: false,
			showBrowse: false,
			browseOnZoneClick: true,
			removeLabel: '',
			removeIcon: '<i class="fa fa-trash"></i>',
			removeTitle: '<?= $_SESSION["CANCEL_OR_RESET"] ?>',
			msgErrorClass: 'alert alert-block alert-danger',
			defaultPreviewContent: '<img src="<?= $usua->getUserPicture(true) ?>" alt="<?= $_SESSION["USER_PROFILE_IMAGE"] ?>"><h6 class="text-muted"><?= $_SESSION["CLICK_TO_SELECT"] ?></h6>',
			allowedFileExtensions: ["jpg", "png"]
		}).on('fileselect', function(event, data) {
			var $parent = $(this);
			var $frm = $("#frmLoadImage");
			var datas = JSON.stringify($frm.serializeObject());
			$("#spanTitle").html("<?= $_SESSION["AVATAR_CHANGE_IMAGE"] ?>");
			$("#spanTitleName").html("");
			$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
			$("#btnActivate").unbind("click");
			$("#btnActivate").bind("click", function() {
				var noty;
				var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
				var link = "core/actions/_save/__uploadAvatar.php?hfIdUser=" + $("#hfIdUser").val();
				var file = document.getElementById("fiAvatar").files[0];
				var xhr = new XMLHttpRequest();				

				noty = notify("", "dark", "", message, "", false);												
				xhr.addEventListener('readystatechange', function(event) {
					if(xhr.readyState == 4){
						noty.close();
						var data = JSON.parse(xhr.responseText);
						if(data.success) {
							//notify("", 'info', "", data.message, "");
							location.reload(true);
						}
						else {
							notify("", 'danger', "", data.message + "<br />" + data.result1 + "<br />" + data.result2, "");
						}
					}
				}, false);
				xhr.open("POST", link);
				xhr.setRequestHeader("Cache-Control", "no-cache");
				xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
				xhr.setRequestHeader("X-File-Name", file.name);
				xhr.send(file);
			});
			$("#divActivateModal").modal("toggle");
		});
	});
	
    </script>

<?
	include("core/templates/__messages.tpl");
?>

</body>
</html>