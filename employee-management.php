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
	
	$filename = "employees.php" . ($source == "" ? "" : "?src=" . $source);

	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId($filename);
	
	require_once("core/__check-session.php");
	
	$result = checkSession($filename,true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);
	
	require_once("core/classes/configuration.php");
	$conf = new configuration("INIT_PASSWORD");
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
			$inter->redirect("employees.php");		
	}
	
	require_once("core/classes/employee.php");
	$employee = new employee();
	
	if($id != "") {
		//Asigna la informacion
		$employee->ID = $id;
		$employee->__getInformation();
		//Si hay error
		if($employee->nerror > 0) {
			$_SESSION["vtappcorp_user_alert"] = $_SESSION["NOT_REGISTERED"];
			$id = "";
		}
	}
	
	switch($action) {
		case "new": {
			$title = $_SESSION["MENU_NEW"];
			$text_title =  $_SESSION["NEW_TEXT"];
			break;
		}
		case "edit": {
			$title = $_SESSION["MENU_EDIT"];
			$text_title =  $_SESSION["EDIT_TEXT"];
			break;
		}
		case "delete": {
			$title = $_SESSION["MENU_DELETE"];
			$text_title =  $_SESSION["DELETE_TEXT"];
			break;
		}
		case "view": {
			$title = $_SESSION["VIEW"];
			$text_title =  $_SESSION["INFORMATION"];
			break;
		}
	}
	
	$dataForm = $employee->dataForm($action);
	//Inicia el contador
	$cont = 0;
	
	require_once("core/classes/document_type.php");
	$doc_type = new document_type();
	
?>
<!DOCTYPE html>
<html>
<head>
<?
	include("core/templates/__header.tpl");
?>
	<!-- bootstrap toogle -->
	<link rel="stylesheet" href="plugins/bootstrap-toggle/css/bootstrap-toggle.min.css"></link>	
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
							<h1 class="m-0 text-dark"><i class="fa fa-user"></i> <?= $title ?> <?= $_SESSION["EMPLOYEE"] ?></h1>
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
								<form id="frmEmployee" name="frmEmployee">
									<div class="row">
										<div class="col-md-4">
											<?= $employee->showField("FIRST_NAME", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $employee->showField("MIDDLE_NAME", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $employee->showField("LAST_NAME", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<?= $employee->showField("IDENTIFICATION", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++], $doc_type->getDataToForm()) ?>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label><?= $employee->arrColComments["PARTNER_ID"] ?> *</label>
												<select class="form-control" id="cbPartner" name="cbPartner" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $employee->partner->showOptionList(9,$employee->partner->ID) ?>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label><?= $employee->arrColComments["AREA_ID"] ?> *</label>
												<select class="form-control" id="cbArea" name="cbArea" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $employee->area->showOptionList(9,$employee->area->ID) ?>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label><?= $employee->arrColComments["USER_ID"] ?> *</label>
												<select class="form-control" id="cbUser" name="cbUser" <?= $dataForm["readonly"][$cont++] ?> style="width: 100%;">
													<option value=""></option>
													<option value="new"><?= $_SESSION["NEW_USER"] ?></option>
													<?= $uscli->showOptionList(9,$employee->ID, true) ?>
												</select>
												<input type="hidden" name="hfIdUser" id="hfIdUser" value="" />
												<input type="hidden" name="hfPassword" id="hfPassword" value="" />
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label><?= $employee->arrColComments["ACCESS_ID"] ?> *</label>
												<select class="form-control" id="cbAccess" name="cbAccess" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $employee->access->showOptionList(9,$employee->access->ID,0,strtoupper($source)) ?>
												</select>
											</div>
										</div>
										<div class="col-md-4">
											<?= $employee->showField("PHONE", $dataForm["tabs"], "fa fa-phone", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<?= $employee->showField("EMAIL", $dataForm["tabs"], "fa fa-envelope", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $employee->showField("CELLPHONE", $dataForm["tabs"], "fa fa-mobile", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label><?= $employee->arrColComments["IMEI"] ?> *</label>
												<div class="input-group">
													<input id="cbIMEI" name="cbIMEI" type="checkbox" class="form-control" <?= ($employee->IMEI ? "checked" : " ") ?> data-toggle="toggle" data-on="<?= $_SESSION["MSG_YES"] ?>" data-off="<?= $_SESSION["MSG_NO"] ?>" data-onstyle="danger" data-offstyle="success" <?= $dataForm["readonly"][$cont++] ?> />
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<?= $employee->showField("ADDRESS", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label><?= $employee->arrColComments["CITY_ID"] ?> *</label>
												<select class="form-control" id="cbCity" name="cbCity" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $employee->city->showAllOptionList(9,$employee->city->ID) ?>
												</select>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label><?= $employee->arrColComments["RECORDS"] ?> *</label>
												<div class="input-group">
													<input id="cbRecords" name="cbRecords" type="checkbox" class="form-control" <?= ($employee->RECORDS ? "checked" : " ") ?> data-toggle="toggle" data-on="<?= $_SESSION["MSG_YES"] ?>" data-off="<?= $_SESSION["MSG_NO"] ?>" data-onstyle="danger" data-offstyle="success" <?= $dataForm["readonly"][$cont++] ?> />
												</div>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label><?= $employee->arrColComments["PENALTIES"] ?> *</label>
												<div class="input-group">
													<input id="cbPenalties" name="cbPenalties" type="checkbox" class="form-control" <?= ($employee->PENALTIES ? "checked" : " ") ?> data-toggle="toggle" data-on="<?= $_SESSION["MSG_YES"] ?>" data-off="<?= $_SESSION["MSG_NO"] ?>" data-onstyle="danger" data-offstyle="success" <?= $dataForm["readonly"][$cont++] ?> />
												</div>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label><?= $employee->arrColComments["IS_BLOCKED"] ?> *</label>
												<div class="input-group">
													<input id="cbBlocked" name="cbBlocked" type="checkbox" class="form-control" <?= ($employee->IS_BLOCKED ? "checked" : " ") ?> data-toggle="toggle" data-on="<?= $_SESSION["MSG_YES"] ?>" data-off="<?= $_SESSION["MSG_NO"] ?>" data-onstyle="danger" data-offstyle="success" <?= $dataForm["readonly"][$cont++] ?> />
												</div>
											</div>
										</div>
									</div>
<?
	if($action != "new") {
?>
									<div class="row">
										<div class="col-md-3">
											<?= $employee->showField("REGISTERED_BY", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $employee->showField("REGISTERED_ON", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $employee->showField("MODIFIED_BY", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $employee->showField("MODIFIED_ON", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
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
									<button type="button" class="btn btn-success" id="btnSaveEmployee" name="btnSaveEmployee" title="<?= $_SESSION["SAVE_CHANGES"] ?>"><i class="fa fa-floppy-o"></i> <?= $_SESSION["SAVE_CHANGES"] ?></button>
									<button type="button" class="btn btn-danger" id="btnCancel" name="btnCancel" title="<?= $_SESSION["MENU_CANCEL"] ?>" onclick="location.href='employees.php?src=<?= $source ?>';"><i class="fa fa-times-circle"></i> <?= $_SESSION["MENU_CANCEL"] ?></button>
<?
	}
	else {
?>
									<button type="button" class="btn btn-primary" id="btnReturn" name="btnReturn" title="<?= $_SESSION["MENU_CANCEL"] ?>" onclick="location.href='employees.php?src=<?= $source ?>';"><i class="fa fa-arrow-left"></i> <?= $_SESSION["MENU_CANCEL"] ?></button>
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
	<!-- Resources -->
	<script src="js/resources.js"></script>	
	
    <script>
	$(document).ready(function() {
		$("#cbPartner").trigger("change");
		$('[data-toggle="tooltip"]').tooltip();
		$("#cbPartner").on("change", function () {
			var value = $(this).val();
			$("#cbArea").removeAttr("disabled");
			$("#cbArea").find("option[data-client-id='" + value + "']").removeAttr("disabled");
			$("#cbArea").find("option[data-client-id!='" + value + "']").attr("disabled","disabled");
			$('#cbArea option:not([disabled]):first').attr('selected', 'selected');
		});
		$("#cbUser").on("change", function () {
			var value = $(this).val();
			var datas = $(this).children("option:selected").data();
			if(value == "")
				return false;
			var id = "<?= $id == "" ? "" : $id ?>";
			var action = "<?= $action ?>";
			if(value == "new") {
				$.ajax({
					url:'core/actions/_load/__showFormData.php',
					data: { 
						txtId: id,
						txtAction: action, 
						txtClass: "users",
						txtForm: "user"
					},
					beforeSend: function (xhrObj) {
						var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
						noty = notify("", "dark", "", message, "", false);												
					},
					success:function(data){
						noty.close();
						$("#modalForm").html(data);
						$("#actionId").html($("#hfAction").val());
						if(action == "view")
							$("#btnSave").hide();
						else {
							$("#btnSave").show();
							$("#btnSave").unbind("click");
							$("#btnSave").bind("click", function() {
								$("#hfIdUser").val($("#txtID").val());
								$("#hfPassword").val($("#txtTHE_PASSWORD").val());
								if($("#cbUser option[value='" + $("#hfIdUser").val() + "']").length <= 0) {
									var text = $("#hfIdUser").val() + " (*)";
									var option = new Option(text, $("#hfIdUser").val(), true, true);
									$("#cbUser").append($(option));
								}
								$("#cbUser").val($("#hfIdUser").val());
								$("#cbAccess").removeAttr("disabled");
								$('#divEditModal').modal('toggle');
							});
						}
						$('#divEditModal').modal('toggle');
					}
				});
			}
			else {
				$("#hfIdUser").val($("#cbUser").val());
				$("#cbAccess").val(datas.access);
				$("#txtPHONE").val(datas.phone);
				$("#txtEMAIL").val(datas.email);
				$("#txtCELLPHONE").val(datas.cellphone);
				$("#txtADDRESS").val(datas.address);
				$("#cbCity").val(datas.cityid);
				$("#txtFIRST_NAME").val(datas.firstname);
				$("#txtLAST_NAME").val(datas.lastname);
				changeResourceText(1,'TBL_EMPLOYEE_IDENTIFICATION',datas.typeid,'idDocType');
				$("#txtTBL_EMPLOYEE_IDENTIFICATION").val(datas.identification);
			}
		});
		$("#btnSaveEmployee").on("click", function(e) {
			var form = document.getElementById('frmEmployee');
			var noty;
			if (form.checkValidity() === false) {
				window.event.preventDefault();
				window.event.stopPropagation();
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				return false;
			}
			var title = $("#hfAction").val() + " <?= $_SESSION["EMPLOYEE"] ?>";
			var url = $("#hfLinkAction").val();
			var $frm = $("#frmEmployee");
			var datasObj = $frm.serializeObject();
			if(!datasObj.hasOwnProperty("cbRecords")) {
				datasObj["cbRecords"] = $("#cbRecords").is(':checked');
			}
			if(!datasObj.hasOwnProperty("cbIMEI")) {
				datasObj["cbIMEI"] = $("#cbIMEI").is(':checked');
			}
			if(!datasObj.hasOwnProperty("cbPenalties")) {
				datasObj["cbPenalties"] = $("#cbPenalties").is(':checked');
			}
			if(!datasObj.hasOwnProperty("cbBlocked")) {
				datasObj["cbBlocked"] = $("#cbBlocked").is(':checked');
			}
			if(!datasObj.hasOwnProperty("cbArea")) {
				datasObj["cbArea"] = $("#cbArea").val();
			}
			if(!datasObj.hasOwnProperty("cbAccess")) {
				datasObj["cbAccess"] = $("#cbAccess").val();
			}
			datasObj["newEmployee"] = $("#cbAccess").attr("disabled") != "disabled";
			datasObj["cbTBL_EMPLOYEE_IDENTIFICATION"] = $("#hfDocType_" + $("#cbTBL_EMPLOYEE_IDENTIFICATION").val()).val();
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
	});
	
    </script>
<?
	include("core/templates/__mapModal.tpl");
	include("core/templates/__messages.tpl");
?>
</body>
</html>
