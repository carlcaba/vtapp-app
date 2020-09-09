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
	$conf = new configuration("INIT_PASSWORD");

	$action = "";
	$id = "";
	if(!empty($_GET['id'])) {
		$id = $_GET['id'];
	}
	if(!empty($_GET['action'])) {
		$action = $_GET['action'];
	}
	
	if($id == "" && $action != "new") {
		//Busca el aliado
		require_once("core/classes/users.php");
		$uscli = new users($_SESSION["vtappcorp_userid"]);
		//Verifica si es un cliente 
		if(substr($uscli->access->PREFIX,0,2) == "AL")
			$id = $uscli->REFERENCE;
		//Si sigue siendo vacio
		if($id == "")
			$inter->redirect("partners.php");		
	}
	
	require_once("core/classes/partner.php");
	$partner = new partner();
	
	if($id != "") {
		//Asigna la informacion
		$partner->ID = $id;
		$partner->__getInformation();
		//Si hay error
		if($partner->nerror > 0) {
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
	
	$dataForm = $partner->dataForm($action);
	//Inicia el contador
	$cont = 0;
	
	require_once("core/classes/document_type.php");
	$doc_type = new document_type();

	require_once("core/classes/color.php");
	$color = new color();
	$color->getInformationByClassName($partner->SKIN);
?>
<!DOCTYPE html>
<html>
<head>
<?
	include("core/templates/__header.tpl");
?>
	<!-- bootstrap toogle -->
	<link rel="stylesheet" href="plugins/bootstrap-toggle/css/bootstrap-toggle.min.css"></link>	
	<!-- FileInput -->
    <link href="plugins/fileinput/css/fileinput.css" media="all" rel="stylesheet" type="text/css"/>
    <link href="plugins/fileinput/themes/explorer-fas/theme.css" media="all" rel="stylesheet" type="text/css"/>
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
							<h1 class="m-0 text-dark"><i class="fa fa-handshake-o"></i> <?= $title ?> <?= $_SESSION["PARTNER"] ?></h1>
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
								<form id="frmPartner" name="frmPartner">
									<div class="row">
										<div class="col-md-6">
											<?= $partner->showField("PARTNER_NAME", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-6">
											<?= $partner->showField("IDENTIFICATION", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++], $doc_type->getDataToForm()) ?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<?= $partner->showField("PHONE", $dataForm["tabs"], "fa fa-phone", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $partner->showField("CELLPHONE", $dataForm["tabs"], "fa fa-mobile", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $partner->showField("EMAIL", $dataForm["tabs"], "fa fa-envelope", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<?= $partner->showField("PHONE_ALT", $dataForm["tabs"], "fa fa-phone", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $partner->showField("CELLPHONE_ALT", $dataForm["tabs"], "fa fa-mobile", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $partner->showField("EMAIL_ALT", $dataForm["tabs"], "fa fa-envelope", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<?= $partner->showField("ADDRESS", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label><?= $partner->arrColComments["CITY_ID"] ?> *</label>
												<select class="form-control" id="cbCity" name="cbCity" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $partner->city->showAllOptionList(9,$partner->city->ID) ?>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<?= $partner->showField("CONTACT_NAME", $dataForm["tabs"], "fa fa-user", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $partner->showField("EMAIL_CONTACT", $dataForm["tabs"], "fa fa-envelope", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $partner->showField("CELLPHONE_CONTACT", $dataForm["tabs"], "fa fa-mobile", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label><?= $partner->arrColComments["IS_BLOCKED"] ?> *</label>
												<div class="input-group">
													<input id="cbBlocked" name="cbBlocked" type="checkbox" class="form-control" <?= ($partner->IS_BLOCKED ? "checked" : " ") ?> data-toggle="toggle" data-on="<?= $_SESSION["MSG_YES"] ?>" data-off="<?= $_SESSION["MSG_NO"] ?>" data-onstyle="danger" data-offstyle="success" <?= $dataForm["readonly"][$cont++] ?> />
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label><?= $_SESSION["CUSTOMIZE_GUI"] ?> *</label>
												<div class="input-group">
													<input id="chkCustomize" name="chkCustomize" type="checkbox" class="form-control" data-toggle="toggle" data-on="<?= $_SESSION["MSG_YES"] ?>" data-off="<?= $_SESSION["MSG_NO"] ?>" data-onstyle="success" data-offstyle="warning" <?= $dataForm["readonly"][$cont++] ?> <?= $color->ID != "0" ? "checked" : "" ?>/>
												</div>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label><?= $partner->arrColComments["SKIN"] ?> *</label>
												<select class="form-control" id="cbSkin" name="cbSkin" <?= $dataForm["readonly"][$cont++] ?> disabled>
													<?= $color->showOptionList(9,$color->ID) ?>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label><?= $_SESSION["IMAGE_FILE"] ?> *</label>
												<div class="input-group">
													<button type="button" data-toggle="tooltip" data-placement="top" title="<?= $_SESSION["UPLOAD_PARTNER_IMAGE"] ?>" id="FilePartnerImage" name="FilePartnerImage" class="btn btn-default" onclick="upload();" disabled>
														<i class="fa fa-cloud-upload"></i>
														<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["UPLOAD_PARTNER_IMAGE"] ?></span>
													</button>
												</div>
											</div>
										</div>
									</div>
<?
	if($action != "new") {
?>
									<div class="row">
										<div class="col-md-3">
											<?= $partner->showField("REGISTERED_BY", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $partner->showField("REGISTERED_ON", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $partner->showField("MODIFIED_BY", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $partner->showField("MODIFIED_ON", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
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
									<button type="button" class="btn btn-danger" id="btnCancel" name="btnCancel" title="<?= $_SESSION["MENU_CANCEL"] ?>" onclick="location.href='partners.php?src=<?= $source ?>';"><i class="fa fa-times-circle"></i> <?= $_SESSION["MENU_CANCEL"] ?></button>
<?
	}
	else {
?>
									<button type="button" class="btn btn-primary" id="btnReturn" name="btnReturn" title="<?= $_SESSION["MENU_CANCEL"] ?>" onclick="location.href='partners.php?src=<?= $source ?>';"><i class="fa fa-arrow-left"></i> <?= $_SESSION["MENU_CANCEL"] ?></button>
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
	$title = $_SESSION["PARTNER"];
	$icon = "<i class=\"fa fa-map-marker\"></i>";
	$userModal = true;
	include("core/templates/__modals.tpl");
	include("core/templates/__footer.tpl");

	$titleUpload = $_SESSION["UPLOAD_PARTNER_IMAGE"];
	$textUpload = $_SESSION["LOAD_TITLE_3"];
	$parameters = "?class=partner&link=partner-management.php&file=partner";
	$saveUpload = "core/actions/_save/__saveUploadedPartnerImage.php";
	$imageToUpload = true;
	include("core/templates/__modalUpload.tpl");

?>

	<!-- SlimScroll -->
	<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="plugins/fastclick/fastclick.js"></script>
	<!-- bootstrap toogle -->
	<script src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>	
	<!-- FileInput -->
    <script src="plugins/fileinput/js/plugins/sortable.js" type="text/javascript"></script>
    <script src="plugins/fileinput/js/fileinput.js" type="text/javascript"></script>
    <script src="plugins/fileinput/themes/fa/theme.js" type="text/javascript"></script>
    <script src="plugins/fileinput/themes/explorer-fa/theme.js" type="text/javascript"></script>
	<!-- Select 2 -->
	<script src="plugins/select2/select2.full.js"></script>
<?
	$fup = "false";
	if($_SESSION["LANGUAGE"] != "1") {
		$fup = "true";
?>
    <script src="plugins/fileinput/js/locales/<?= $_SESSION["LANGUAGE"] ?>.js" type="text/javascript"></script>
    <script src="plugins/select2/i18n/<?= $_SESSION["LANGUAGE"] ?>.js"></script>
<?
	}
?>
	<!-- Resources -->
	<script src="js/resources.js"></script>	
	
    <script>
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();
		$('#chkCustomize').change(function() {
			if("<?= $action ?>" == "new") {
				$('#cbSkin').attr('disabled', !$(this).prop('checked'));
				$('#FilePartnerImage').attr('disabled', !$(this).prop('checked'));
				if(!$(this).prop('checked')) {
					$('#cbSkin').val("").trigger('change');
				}
			}
    	});
		function colorFormatTemplate (color) {
			if (!color.id) {
				return color.text;
			}
			var colorHex = color.element.dataset.hexadecimal;
			var $color = $('<span style="color: ' + colorHex + ';"><i class="fa fa-square fa-border"></i><span style="color: #000000;"> ' + color.element.text + '</span></span>');
			return $color;
		};
		$('#cbSkin').select2({
			templateSelection: colorFormatTemplate
		});
		$("#btnSave").on("click", function(e) {
			var form = document.getElementById('frmPartner');
			var noty;
			if (form.checkValidity() === false) {
				window.event.preventDefault();
				window.event.stopPropagation();
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				return false;
			}
			var title = $("#hfAction").val() + " <?= $_SESSION["PARTNER"] ?>";
			var url = $("#hfLinkAction").val();
			var $frm = $("#frmPartner");
			var datasObj = $frm.serializeObject();
			if(!datasObj.hasOwnProperty("cbBlocked")) {
				datasObj["cbBlocked"] = $("#cbBlocked").is(':checked');
			}
			datasObj["chkCustomize"] = $("#chkCustomize").is(':checked');
			datasObj["cbSkin"] = "";
			datasObj["FilePartnerImage"] = "";
			if(datasObj["chkCustomize"]) {
				if($("#cbSkin").val() == "") {
					notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
					return false;
				}
				if($("#hfFile0").val() == "") {
					notify("", "danger", "", "<?= $_SESSION["NO_FILE_FOR_UPLOAD"] ?>", "");
					return false;
				}
				datasObj["cbSkin"] = $("#cbSkin").find('option:selected').data("classname");
				datasObj["FilePartnerImage"] = $("#hfFile0").val();
			}
			datasObj["cbTBL_PARTNER_IDENTIFICATION"] = $("#hfDocType_" + $("#cbTBL_PARTNER_IDENTIFICATION").val()).val();
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
						notify("", (data.success ? 'info' : 'danger'), "", data.message + "\n" + data.loadimage, "");
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