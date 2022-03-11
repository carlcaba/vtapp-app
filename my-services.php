<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();
	
	$filename = basename(__FILE__);	
	
	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId($filename);
	
	require_once("core/__check-session.php");
	
	$result = checkSession($filename,true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);

	require_once("core/classes/service.php");
	
	$service = new service();
	$qty = $service->loadCount();
	$btncompl = "btn-default";
	$btnicon = "fa-info-circle";
	$btnbadge = "";
	if($qty > 0) {
		$btncompl = "btn-warning";
		$btnicon = "fa-exclamation-triangle fa-lg";
		$btnbadge = "<span class=\"badge badge-danger\">$qty</span>";
	}
	
	$template = "services.xlsx";
	
	$disabled = "disabled";
	if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "CL") {
		$disabled = "";
	}
	else if($_SESSION["vtappcorp_useraccess"] == "GOD") {
		$disabled = "";
	}
	
?>
<!DOCTYPE html>
<html>
<head>
<?
	include("core/templates/__header.tpl");
?>
	<!-- Ekko-LightBox -->
	<link rel="stylesheet" href="plugins/ekko-lightbox/ekko-lightbox.css">
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
						<div class="col-sm-6">&nbsp;</div>
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
					<div class="col-12">
						<div class="card card-primary">
							<div class="card-header">
								<h1 class="card-title">
									<i class="fa fa-motorcycle"></i> <?= $_SESSION["MY_SERVICES"] ?>
								</h1>
								<div class="btn-group float-right">
									<button type="button" data-toggle="tooltip" data-placement="top" title="<?= $_SESSION["MENU_NEW"] ?>" id="btnNewService" name="btnNewService" class="btn btn-primary pull-right btn-sm" onclick="show('','new');">
										<i class="fa fa-plus-circle"></i>
										<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["MENU_NEW"] ?></span>
									</button>
								</div>
							</div>
<?= $service->showCards($_SESSION["vtappcorp_referenceid"]) ?>
						</div>
					</div>
				</div>
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->

<?
	$title = $_SESSION["SERVICES"];
	$icon = "<i class=\"fa fa-motorcycle\"></i>";
	include("core/templates/__modals.tpl");
	include("core/templates/__footer.tpl");
	
	$titleUpload = $_SESSION["UPLOAD_SERVICES"];
	$textUpload = str_replace("{__filename__}","services",$_SESSION["LOAD_TITLE_2"]);
	$parameters = "?class=service&link=services.php&file=service";
	$saveUpload = "core/actions/_save/__saveUploadedServices.php";
	include("core/templates/__modalUpload.tpl");
	
?>
	<!-- SlimScroll -->
	<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="plugins/fastclick/fastclick.js"></script>
	<!-- Ekko Lightbox -->
	<script src="plugins/ekko-lightbox/ekko-lightbox.min.js"></script>
	<!-- Filterizr-->
	<script src="plugins/filterizr/jquery.filterizr.min.js"></script>	
	<!-- Resources -->
	<script src="js/resources.js"></script>	
	
    <script>
	var notty;
	$(document).ready(function() {
		$('[data-toggle="lightbox"]').on('click', function(event) {
			event.preventDefault();
			$(this).ekkoLightbox({
				alwaysShowClose: true,
				loadingMessage: "<?= $_SESSION["MSG_PROCESSING"] ?>",
				onContentLoaded: function() {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
					notty = notify("", "dark", "", message, "", false);												
				},
				onShown: function() {
					notty.close();
					console.log('Checking our the events huh?');
				},
				onNavigate: function(direction, itemIndex) {
					console.log('Navigating '+direction+'. Current item: '+itemIndex);
				}				
			});
		});
		$('.filter-container').filterizr({gutterPixels: 3});
		$('.btn[data-filter]').on('click', function() {
			$('.btn[data-filter]').removeClass('active');
			$(this).addClass('active');
		});		
		$('#divActivateModal').on('shown.bs.modal', function (e) {
			if($("#hfTextButton").val() != "")
				$("#btnActivate").html($("#hfTextButton").val());
			else 
				$("#btnActivate").html($("#hfDefaultTextButton").val());
		});
		$('[data-toggle="tooltip"]').tooltip();		
	});
	function show(id, action) {
		if(action != "new")
			location.href = "service-management.php?id=" + id + "&action=" + action;
		else 
			location.href = "service-management.php?action=" + action;
	}
	function information(id) {
		if(id != "")
			location.href = "service-log.php?id=" + id;
		else 
			notify("", 'danger', "", "<?= $_SESSION["NO_INFORMATION"] ?>", "");
		}
	function activate(id,activate,name) {
		var _msg = (activate ? "<?= $_SESSION["ACTIVATE"] ?> " : "<?= $_SESSION["DEACTIVATE"] ?> ") + "<?= $_SESSION["SERVICE"] ?> ";
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
					txtClass: "service", 
					txtLink: "services.php",
					txtPre: "SERVICE"
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
	function markAsPayed(id) {
		var _msg = "<?= $_SESSION["PAY"] ?>";
		$("#spanTitle").html(_msg);
		$("#spanTitleName").html("");
		$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
		$("#btnActivate").unbind("click");
		$("#btnActivate").bind("click", function() {
			var noty;
			$.ajax({
				url:'core/actions/_save/__saveMarkAsPayed.php',
				dataType: "json",
				data: {
					service: id
				},
				beforeSend: function (xhrObj) {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
					noty = notify("", "dark", "", message, "", false);												
				},
				success:function(data) {
					noty.close();
					notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
					if(data.success) {
						if($("#bidBtn" + id).attr("disabled")) $("#bidBtn" + id).attr("disabled", false);
						if($("#assBtn" + id).attr("disabled")) $("#assBtn" + id).attr("disabled", false);
						$("#payBtn" + id).attr("disabled", true);						
					}
				}
			});
		});
		$("#divActivateModal").modal("toggle");
	}
	function startBid(id) {
		$.ajax({
			url:'core/actions/_load/__getOnlineEmployees.php',
			dataType: "json",
			beforeSend: function (xhrObj) {
				var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
				noty = notify("", "dark", "", message, "", false);												
			},
			success:function(data) {
				noty.close();
				if(data.success) {
					var _msg = data.message;
					$("#spanTitle").html(_msg);
					$("#spanTitleName").html(name);
					$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
					$("#hfTextButton").val(data.btnText);
					$("#btnActivate").unbind("click");
					$("#btnActivate").bind("click", function() {
						var noty;
						$.ajax({
							url:'core/actions/_save/__startBid.php',
							data: { 
								users: JSON.stringify(data.data),
								id: id
							},
							dataType: "json",
							beforeSend: function (xhrObj) {
								var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
								noty = notify("", "dark", "", message, "", false);												
							},
							success:function(data){
								$("#hfTextButton").val("");
								noty.close();
								notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
								if(data.success) {
									$("#bidBtn" + id).attr("disabled", true);
									$("#assBtn" + id).attr("disabled", true);
								}
							}
						});
					});
					$("#divActivateModal").modal("toggle");
				}
				else {
					notify("", 'info', "", data.message, "");
				}
			}
		});
	}
	function assign(id) {
		var noty;
		$.ajax({
			url:'core/actions/_load/__loadToAssign.php',
			data: { 
				id: id,
				src: "<?= $source ?>"
			},
			dataType: "json",
			beforeSend: function (xhrObj) {
				var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
				noty = notify("", "dark", "", message, "", false);												
			},
			success:function(data){
				noty.close();
				if(data.success) {
					$("#h5ModalLabel").html(data.message.icon + data.message.title);
					$("#modalForm").html(data.message.form);
					$("#btnSave").show();
					$("#btnSave").unbind();
					$('#btnSave').bind("click", function(e) {
						var form = document.getElementById('frmAssignService');
						var noty;
						if (form.checkValidity() === false) {
							window.event.preventDefault();
							window.event.stopPropagation();
							notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
							return false;
						}
						var title = "<?= $_SESSION["MENU_NEW"] . " " . $_SESSION["ASSIGN_SERVICE"] ?>";
						var url = $("#hfLinkAction").val();
						var $frm = $("#frmAssignService");
						var datas = $frm.serializeObject();
						if(!datas.hasOwnProperty("cbPartner")) {
							datas["cbPartner"] = $("#cbPartner option:selected").val()
						}
						if(!datas.hasOwnProperty("cbEmployee")) {
							datas["cbEmployee"] = $("#cbEmployee option:selected").val()
						}
						if(!datas.hasOwnProperty("cbVehicle")) {
							datas["cbVehicle"] = $("#cbVehicle option:selected").val()
						}
						datas["hfIdVehicle"] = $("#cbEmployee option:selected").data("vehicleid");
						datas = JSON.stringify(datas);
						$("#spanTitle").html(title);
						$("#spanTitleName").html("");
						$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
						$("#btnActivate").unbind("click");
						$("#btnActivate").bind("click", function() {
							var noty;
							$.ajax({
								url: url,
								data: { 
									strModel: datas,
									link: "services.php"
								},
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
					$.getScript("js/resources.js");
					$('#divEditModal').modal('toggle');
					$("#cbPartner").on("change",function (e) {
						var value = $(this).find("option:selected").val();
						var vehicle = $("#cbVehicle option:selected").val();
						$("#cbEmployee").find("option[data-partnerid!='" + value + "']").attr("disabled","disabled");
						$("#cbEmployee").find("option[data-partnerid='" + value + "']").removeAttr("disabled");
						if($("#hfNoVehicle").val() != vehicle) {
							$("#cbEmployee option:enabled").each(function () {
								var val = $(this).data("vehicletype");
								if(val != vehicle) {
									$(this).attr("disabled","disabled");
								}
							});
						}
						$("#cbEmployee").find("option[value='']").removeAttr("disabled");
						$("#cbEmployee").val("");
						$("#hfVehicleTypeId").val("");
						$("#cbEmployee").trigger("change");
					});
					$("#cbEmployee").on("change",function (e) {
						var value = $(this).find("option:selected").val();
						var type = $(this).find("option:selected").data("vehicletype");
						if(value != "" && $("#hfChangeVehicle").val() == "true") {
							$("#hfVehicleTypeId").val(type);
						}
					});
					$("#cbPartner").trigger("change");
				}
				else {
					notify("", 'danger', "", data.message, "");
				}
			}
		});
	}
    </script>
<?
	include("core/templates/__messages.tpl");
?>

</body>
</html>
