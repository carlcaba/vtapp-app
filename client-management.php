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
	
	require_once("core/classes/configuration.php");
	$conf = new configuration("CLIENT_PAYMENT_TYPES");
	
	$pymtype = $conf->verifyValue();

	$conf->KEY_NAME = "INIT_PASSWORD";

	$action = "";
	$id = "";
	$aliado = "";
	$link = "clients.php?src=" . $source;
	if(!empty($_GET['id'])) {
		$id = $_GET['id'];
	}
	if(!empty($_GET['action'])) {
		$action = $_GET['action'];
	}

	//Busca el cliente
	require_once("core/classes/users.php");
	$uscli = new users($_SESSION["vtappcorp_userid"]);
	
	if($id == "" && $action != "new") {
		//Verifica si es un cliente 
		if(substr($uscli->access->PREFIX,0,2) == "CL") {
			$id = $uscli->REFERENCE;
			$action = "edit";
			$link = "clients-management.php";
		}
		//Si sigue siendo vacio
		if($id == "")
			$inter->redirect("clients.php");		
	}
	
	if($id == "" && $action == "new") {
		//Verifica si es un aliado
		if(substr($uscli->access->PREFIX,0,2) == "AL")
			$aliado = $uscli->REFERENCE;
	}
	
	require_once("core/classes/client.php");
	$client = new client();
	
	if($id != "") {
		//Asigna la informacion
		$client->ID = $id;
		$client->__getInformation();
		//Si hay error
		if($client->nerror > 0) {
			$_SESSION["vtappcorp_user_alert"] = $_SESSION["NOT_REGISTERED"];
			$id = "";
		}
	}
	
	switch($action) {
		case "new": {
			$titlepage = $_SESSION["MENU_NEW"];
			$text_title =  $_SESSION["NEW_TEXT"];
			break;
		}
		case "edit": {
			$titlepage = $_SESSION["MENU_EDIT"];
			$text_title =  $_SESSION["EDIT_TEXT"];
			break;
		}
		case "delete": {
			$titlepage = $_SESSION["MENU_DELETE"];
			$text_title =  $_SESSION["DELETE_TEXT"];
			break;
		}
		case "view": {
			$titlepage = $_SESSION["VIEW"];
			$text_title =  $_SESSION["INFORMATION"];
			break;
		}
	}
	
	$dataForm = $client->dataForm($action);
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
							<h1 class="m-0 text-dark"><i class="fa fa-briefcase"></i> <?= $titlepage ?> <?= $_SESSION["CLIENT"] ?></h1>
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
								<form id="frmClient" name="frmClient">
									<div class="row">
										<div class="col-md-6">
											<?= $client->showField("CLIENT_NAME", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-6">
											<?= $client->showField("IDENTIFICATION", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++], $doc_type->getDataToForm()) ?>
										</div>
										<input type="hidden" name="hfID" id="hfID" value="<?= $client->ID ?>" /> 
									</div>
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label><?= $client->arrColComments["PAYMENT_TYPE_ID"] ?> *</label>
												<select class="form-control" id="cbClientType" name="cbClientType" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $client->client_type->client_type->showOptionList(9,$client->client_type->client_type->ID) ?>
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label><?= $client->arrColComments["CLIENT_PAYMENT_TYPE_ID"] ?> *</label>
												<select class="form-control" id="cbPaymentType" name="cbPaymentType" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $client->client_type->showOptionList(9,$client->client_type->ID) ?>
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<?= $client->showField("PHONE", $dataForm["tabs"], "fa fa-phone", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $client->showField("CELLPHONE", $dataForm["tabs"], "fa fa-mobile", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<?= $client->showField("EMAIL", $dataForm["tabs"], "fa fa-envelope", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $client->showField("EMAIL_ALT", $dataForm["tabs"], "fa fa-envelope", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $client->showField("PHONE_ALT", $dataForm["tabs"], "fa fa-phone", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $client->showField("CELLPHONE_ALT", $dataForm["tabs"], "fa fa-mobile", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<?= $client->showField("ADDRESS", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label><?= $client->arrColComments["CITY_ID"] ?> *</label>
												<select class="form-control" id="cbCity" name="cbCity" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $client->city->showAllOptionList(9,$client->city->ID) ?>
												</select>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label><?= $client->arrColComments["CLIENT_PAYMENT_TYPE_ID"] ?> *</label>
												<select class="form-control" id="cbClientPaymentType" name="cbClientPaymentType" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $client->client_payment->showOptionList(9,$client->client_payment->ID) ?>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<?= $client->showField("CONTACT_NAME", $dataForm["tabs"], "fa fa-user", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $client->showField("EMAIL_CONTACT", $dataForm["tabs"], "fa fa-envelope", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $client->showField("CELLPHONE_CONTACT", $dataForm["tabs"], "fa fa-mobile", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label><?= $client->arrColComments["IS_BLOCKED"] ?> *</label>
												<div class="input-group">
													<input id="cbBlocked" name="cbBlocked" type="checkbox" class="form-control" <?= ($client->IS_BLOCKED ? "checked" : " ") ?> data-toggle="toggle" data-on="<?= $_SESSION["MSG_YES"] ?>" data-off="<?= $_SESSION["MSG_NO"] ?>" data-onstyle="danger" data-offstyle="success" <?= $dataForm["readonly"][$cont++] ?> />
												</div>
											</div>
										</div>
									</div>
<?
	if($action != "new") {
?>
									<div class="row">
										<div class="col-md-3">
											<?= $client->showField("REGISTERED_BY", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $client->showField("REGISTERED_ON", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $client->showField("MODIFIED_BY", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $client->showField("MODIFIED_ON", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
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
									<button type="button" class="btn btn-success" id="btnSave" name="btnSave" title="<?= $_SESSION["SAVE_CHANGES"] ?>"><i class="fa fa-floppy-o"></i> <?= $_SESSION["SAVE_CHANGES"] ?></button>
									<button type="button" class="btn btn-danger" id="btnCancel" name="btnCancel" title="<?= $_SESSION["MENU_CANCEL"] ?>" onclick="location.href='<?= $link ?>';"><i class="fa fa-times-circle"></i> <?= $_SESSION["MENU_CANCEL"] ?></button>
<?
	}
	else {
?>
									<button type="button" class="btn btn-primary" id="btnReturn" name="btnReturn" title="<?= $_SESSION["MENU_CANCEL"] ?>" onclick="location.href='<?= $link ?>';"><i class="fa fa-arrow-left"></i> <?= $_SESSION["MENU_CANCEL"] ?></button>
<?
	}
?>
									<input type="hidden" name="hfAction" id="hfAction" value="<?= $dataForm["actiontext"] ?>" /> 
									<input type="hidden" name="hfLinkAction" id="hfLinkAction" value="<?= $dataForm["link"] ?>" /> 
									<input type="hidden" name="hfIdAliado" id="hfIdAliado" value="<?= $aliado ?>" /> 
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
	$title = $_SESSION["CLIENT"];
	$icon = "<i class=\"fa fa-map-marker\"></i>";
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
	var pymtype = JSON.parse('<?= $pymtype ?>');
	$(document).ready(function() {
		$("#cbClientType").trigger("change");
		$('[data-toggle="tooltip"]').tooltip();
		$("#cbClientType").on("change", function () {
			var value = $(this).val();
			$("#cbPaymentType").removeAttr("disabled");
			$("#cbPaymentType").find("option[data-client-type='" + value + "']").removeAttr("disabled");
			$("#cbPaymentType").find("option[data-client-type!='" + value + "']").attr("disabled","disabled");
			$('#cbPaymentType option:not([disabled]):first').attr('selected', 'selected');
			$("#cbPaymentType").trigger("change");
		});
		$("#cbPaymentType").on("change", function () {
			var value = $(this).val();
			var datatype = $("#cbPaymentType option:selected").data("clientType");
			var pyytype = pymtype.find((o) => { return (o["payment"] == value && o["client"] == datatype); });
			if (typeof(pyytype) != "undefined") {
				$("#cbClientPaymentType").removeAttr("disabled");
				$("#cbClientPaymentType").find("option[value='" + pyytype["type"] + "']").removeAttr("disabled");
				$("#cbClientPaymentType").find("option[value!='" + pyytype["type"] + "']").attr("disabled","disabled");
				$('#cbClientPaymentType option:not([disabled]):first').attr('selected', 'selected');
			}
		});
		$("#btnSave").on("click", function(e) {
			var form = document.getElementById('frmClient');
			var noty;
			if (form.checkValidity() === false) {
				window.event.preventDefault();
				window.event.stopPropagation();
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				return false;
			}
			var title = $("#hfAction").val() + " <?= $_SESSION["CLIENT"] ?>";
			var url = $("#hfLinkAction").val();
			var $frm = $("#frmClient");
			var datasObj = $frm.serializeObject();
			if(!datasObj.hasOwnProperty("cbPaymentType")) {
				datasObj["cbPaymentType"] = $("#cbPaymentType option:selected").val()
			}
			if(!datasObj.hasOwnProperty("cbBlocked")) {
				datasObj["cbBlocked"] = $("#cbBlocked").is(':checked');
			}
			datasObj["cbTBL_CLIENT_IDENTIFICATION"] = $("#hfDocType_" + $("#cbTBL_CLIENT_IDENTIFICATION").val()).val();
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
