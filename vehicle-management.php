<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();

	$source = "";
	if(!empty($_GET['src'])) {
		$source = $_GET['src'];
	}
	
	$filename = "vehicles.php" . ($source == "" ? "" : "?src=" . $source);

	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId($filename);
	
	require_once("core/__check-session.php");
	
	$result = checkSession($filename,true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);
	
	require_once("core/classes/users.php");
	$uscli = new users($_SESSION["vtappcorp_userid"]);

	$action = "";
	$id = "";
	if(!empty($_GET['id'])) {
		$id = $_GET['id'];
	}
	if(!empty($_GET['action'])) {
		$action = $_GET['action'];
	}
	
	if($id == "" && $action != "new") {
		//Verifica si es un aliado 
		if(substr($uscli->access->PREFIX,0,2) == "AL")
			$id = $uscli->REFERENCE;
		//Si sigue siendo vacio
		if($id == "")
			$inter->redirect("vehicles.php");		
	}
	
	require_once("core/classes/vehicle.php");
	require_once("core/classes/vehicle_protection.php");
	$vehicle = new vehicle();
	$protection = new vehicle_protection();
	$protection->dataForm();
	
	if($id != "") {
		//Asigna la informacion
		$vehicle->ID = $id;
		$vehicle->__getInformation();
		//Si hay error
		if($vehicle->nerror > 0) {
			$_SESSION["vtappcorp_user_alert"] = $_SESSION["NOT_REGISTERED"];
			$id = "";
		}
	}
	
	switch($action) {
		case "new": {
			$titlepage = $_SESSION["MENU_NEW"];
			$text_title =  "Ingrese la información solicitada para crear un nuevo registro. <small>Los campos marcados con * son requeridos.</small>";
			break;
		}
		case "edit": {
			$titlepage = "Editar";
			$text_title =  "Modifique la información disponible. No todos los campos son editables. <small>Los campos marcados con * son requeridos</small>";
			break;
		}
		case "delete": {
			$titlepage = "Confirme que desea eliminar este registro.";
			$text_title =  $_SESSION["DELETE_TEXT"];
			break;
		}
		case "view": {
			$titlepage = $_SESSION["VIEW"];
			$text_title =  "Información";
			break;
		}
	}
	
	$dataForm = $vehicle->dataForm($action);
	//Inicia el contador
	$cont = 0;
	
?>
<!DOCTYPE html>
<html>
<head>
<?
	include("core/templates/__header.tpl");
?>
	<!-- bootstrap toogle -->
	<link rel="stylesheet" href="plugins/bootstrap-toggle/css/bootstrap-toggle.min.css"></link>	
	<!-- daterange picker -->
	<link rel="stylesheet" href="plugins/daterangepicker/daterangepicker-bs3.css">
	<!-- Select2 -->
	<link rel="stylesheet" href="plugins/select2/select2.css">
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
							<h1 class="m-0 text-dark"><i class="fa fa-car"></i> <?= $titlepage ?> <?= $_SESSION["VEHICLE"] ?></h1>
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
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<p class="card-title">
									<?= $text_title ?>
								</p>
							</div>
							<!-- /.card-header -->
							<div class="card-body">
								<form id="frmVehicle" name="frmVehicle">
									<div class="row">
										<div class="col-md-4">
											<label><?= $vehicle->arrColComments["VEHICLE_TYPE_ID"] ?> *</label>
											<select class="form-control" id="cbVehicleType" name="cbVehicleType" <?= $dataForm["readonly"][$cont++] ?>>
												<?= $vehicle->type->showOptionList(9,$vehicle->type->ID) ?>
											</select>
										</div>
										<div class="col-md-4">
											<?= $vehicle->showField("BRAND", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $vehicle->showField("MODEL", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<?= $vehicle->showField("PLATE", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $vehicle->showField("YEAR", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $vehicle->showField("SERIAL_NUMBER", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<?= $vehicle->showField("INSURANCE_COMPANY", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $vehicle->showField("INSURANCE_NUMBER", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $vehicle->showField("EXPIRATION_DATE", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<p class="card-title">
									<?= $vehicle->arrColComments["EMPLOYEE_ID"] ?>
								</p>
							</div>
							<!-- /.card-header -->
							<div class="card-body">
								<div class="row">
									<div class="col-md-6">
										<label><?= $vehicle->arrColComments["EMPLOYEE_ID"] ?> *</label>
										<select class="form-control" id="cbEmployee" name="cbEmployee" <?= $dataForm["readonly"][$cont++] ?>>
											<?= $vehicle->employee->showOptionList(9,$vehicle->employee->ID) ?>
										</select>
									</div>
									<div class="col-md-6">
										<label><?= explode(",",$protection->arrColComments["RESOURCE_NAME"])[1] ?> *</label>
										<select class="form-control" id="cbProtection" name="cbProtection" multiple="multiple">
											<?= $protection->showOptionList(9,"") ?>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<?= $vehicle->showField("LICENCE_NUMBER", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
									</div>
									<div class="col-md-4">
										<?= $vehicle->showField("LICENCE_CATEGORY_ID", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
									</div>
									<div class="col-md-4">
										<?= $vehicle->showField("LICENCE_EXPIRATION", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
									</div>
								</div>
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label><?= $vehicle->arrColComments["TECHNICAL_REVISION"] ?> *</label>
											<div class="input-group">
												<input id="cbTechRevision" name="cbTechRevision" type="checkbox" class="form-control" <?= ($vehicle->TECHNICAL_REVISION ? "checked" : " ") ?> data-toggle="toggle" data-on="<?= $_SESSION["MSG_YES"] ?>" data-off="<?= $_SESSION["MSG_NO"] ?>" data-onstyle="success" data-offstyle="danger" <?= $dataForm["readonly"][$cont++] ?> />
											</div>
										</div>
									</div>
									<div class="col-md-3">
										<?= $vehicle->showField("TECHNICAL_REVISION_EXPIRATION", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
									</div>
									<div class="col-md-2">
										<?= $vehicle->showField("EXPERIENCE_YEARS", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
									</div>
									<div class="col-md-2">
										<label><?= $vehicle->arrColComments["JOURNEY_ID"] ?> *</label>
										<select class="form-control" id="cbJourney" name="cbJourney" <?= $dataForm["readonly"][$cont++] ?>>
											<?= $vehicle->journey->showOptionList(9,$vehicle->journey->ID) ?>
										</select>
									</div>
									<div class="col-md-2">
										<div class="form-group">
											<label><?= $vehicle->arrColComments["IS_BLOCKED"] ?> *</label>
											<div class="input-group">
												<input id="cbBlocked" name="cbBlocked" type="checkbox" class="form-control" <?= ($vehicle->IS_BLOCKED ? "checked" : " ") ?> data-toggle="toggle" data-on="<?= $_SESSION["MSG_YES"] ?>" data-off="<?= $_SESSION["MSG_NO"] ?>" data-onstyle="danger" data-offstyle="success" <?= $dataForm["readonly"][$cont++] ?> />
											</div>
										</div>
									</div>
								</div>
<?
	if($action != "new") {
?>
									<div class="row">
										<div class="col-md-3">
											<?= $vehicle->showField("REGISTERED_BY", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $vehicle->showField("REGISTERED_ON", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $vehicle->showField("MODIFIED_BY", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $vehicle->showField("MODIFIED_ON", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
<?
	}
?>
								</form>
							</div>
							<!-- /.card-body -->
							<div class="card-footer">
								<div class="btn-group float-right">
<?
	if($action != "view") {
?>
									<button type="button" class="btn btn-success" id="btnSaveVehicle" name="btnSaveVehicle" title="<?= $_SESSION["SAVE_CHANGES"] ?>"><i class="fa fa-floppy-o"></i> <?= $_SESSION["SAVE_CHANGES"] ?></button>
									<button type="button" class="btn btn-danger" id="btnCancel" name="btnCancel" title="<?= $_SESSION["MENU_CANCEL"] ?>" onclick="location.href='vehicles.php?src=<?= $source ?>';"><i class="fa fa-times-circle"></i> <?= $_SESSION["MENU_CANCEL"] ?></button>
<?
	}
	else {
?>
									<button type="button" class="btn btn-primary" id="btnReturn" name="btnReturn" title="<?= $_SESSION["MENU_CANCEL"] ?>" onclick="location.href='vehicles.php?src=<?= $source ?>';"><i class="fa fa-arrow-left"></i> <?= $_SESSION["MENU_CANCEL"] ?></button>
<?
	}
?>
									<input type="hidden" name="hfAction" id="hfAction" value="<?= $dataForm["actiontext"] ?>" /> 
									<input type="hidden" name="hfLinkAction" id="hfLinkAction" value="<?= $dataForm["link"] ?>" /> 
								</div>							
							</div>
						</div>
						<!-- /.card -->
					</div>
					<!-- /.col -->
				</div>
				<!-- /.row -->
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->
	
<?
	$title = $_SESSION["USER"];
	$icon = "<i class=\"fa fa-user\"></i>";
	$userModal = true;
	include("core/templates/__modals.tpl");
	include("core/templates/__footer.tpl");
?>

	<!-- SlimScroll -->
	<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="plugins/fastclick/fastclick.js"></script>
	<!-- bootstrap toogle -->
	<script src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>	
	<!-- date-range-picker -->
	<script src="plugins/moment/moment.min.js"></script>
	<script src="plugins/daterangepicker/daterangepicker.js"></script>
	<!-- Select 2 -->
	<script src="plugins/select2/select2.full.js"></script>
	<!-- Resources -->
	<script src="js/resources.js"></script>	
	
    <script>
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();
		$('#cbProtection').select2();
		$("#cbVehicleType").on("change", function(e) {
			var value = $(this).val();
			$('#cbProtection option[data-vehicletype="' + value + '"]').attr('disabled',false);
			$('#cbProtection option[data-vehicletype!="' + value + '"]').attr('disabled',true);
			$("#cbProtection").attr("disabled",($("#cbProtection option:enabled").length == 0));
			$('#cbProtection').select2().trigger('change')
		});
<?
	if($_SESSION["LANGUAGE"] != "1") {
?>
			$.getJSON("plugins/daterangepicker/lang/<?= $_SESSION["LANGUAGE"] ?>.json", function(json) { 
				$('#txtEXPIRATION_DATE').daterangepicker({
					locale: json,
					startDate: "<?= date("Y-m-d") ?>",
					singleDatePicker: true, 
					showDropdowns: true,
					minYear: <?= date("Y") ?>,
					maxYear: <?= intval(date("Y")) + 2 ?>
				});
				$('#txtLICENCE_EXPIRATION').daterangepicker({
					locale: json,
					startDate: "<?= date("Y-m-d") ?>",
					singleDatePicker: true, 
					showDropdowns: true,
					minYear: <?= date("Y") ?>,
					maxYear: <?= intval(date("Y")) + 5 ?>
				});
				$('#txtTECHNICAL_REVISION_EXPIRATION').daterangepicker({
					locale: json,
					startDate: "<?= date("Y-m-d") ?>",
					singleDatePicker: true, 
					showDropdowns: true,
					minYear: <?= date("Y") ?>,
					maxYear: <?= intval(date("Y")) + 2 ?>
				});
			});
<?
	}
	else {
?>		
			$('#txtEXPIRATION_DATE').daterangepicker({
				startDate: "<?= date("Y-m-d") ?>",
				singleDatePicker: true, 
				showDropdowns: true,
				minYear: <?= date("Y") ?>,
				maxYear: <?= intval(date("Y")) + 2 ?>
			});
			$('#txtLICENCE_EXPIRATION').daterangepicker({
				startDate: "<?= date("Y-m-d") ?>",
				singleDatePicker: true, 
				showDropdowns: true,
				minYear: <?= date("Y") ?>,
				maxYear: <?= intval(date("Y")) + 5 ?>
			});
			$('#txtTECHNICAL_REVISION_EXPIRATION').daterangepicker({
				startDate: "<?= date("Y-m-d") ?>",
				singleDatePicker: true, 
				showDropdowns: true,
				minYear: <?= date("Y") ?>,
				maxYear: <?= intval(date("Y")) + 1 ?>
			});
<?
	}
?>
		$("#btnSaveVehicle").on("click", function(e) {
			var form = document.getElementById('frmVehicle');
			var noty;
			if (form.checkValidity() === false) {
				window.event.preventDefault();
				window.event.stopPropagation();
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				return false;
			}
			if (!$("#cbProtection").attr("disabled")) {
				var prot = $("#cbProtection option:enabled").length;
				var sele = $('#cbProtection').select2('data');
				if (prot > sele.length) {
					notify("", "danger", "", "<?= $_SESSION["ERROR_ON_PROTECTION"] ?>", "");
					return false;
				}
			}
			var title = $("#hfAction").val() + " <?= $_SESSION["VEHICLE"] ?>";
			var url = $("#hfLinkAction").val();
			var $frm = $("#frmVehicle");
			var datasObj = $frm.serializeObject();
			if(!datasObj.hasOwnProperty("cbTechRevision")) {
				datasObj["cbTechRevision"] = $("#cbTechRevision").is(':checked');
			}
			if(!datasObj.hasOwnProperty("cbBlocked")) {
				datasObj["cbBlocked"] = $("#cbBlocked").is(':checked');
			}
			var datas = JSON.stringify(datasObj);
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
						notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
						if(data.success)
							location.href = data.link;
					}
				});
			});
			$("#divActivateModal").modal("toggle");			
		});
		$('#cbVehicleType').trigger('change')		
	});
	
    </script>
<?
	include("core/templates/__messages.tpl");
?>
</body>
</html>
