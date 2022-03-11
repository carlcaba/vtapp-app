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
	
	$filename = "users-manager.php" . ($source == "" ? "" : "?src=" . $source);

	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId($filename);
	
	require_once("core/__check-session.php");
	
	$result = checkSession($filename,true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);
	
	require_once("core/classes/configuration.php");
	$conf = new configuration("INIT_PASSWORD");

	$action = "new";

	if(!empty($_GET['id'])) {
		$id = $_GET['id'];
		$id = $inter->decrypt($id);
	}
	else {
		$id = $_SESSION["vtappcorp_userid"];
	}
	
	if(!empty($_GET['action'])) {
		$action = $_GET['action'];
	}
	
	$usua = new users($_SESSION["vtappcorp_userid"]);
	
	switch($action) {
		case "new": {
			$titlepage = $_SESSION["MENU_NEW"];
			$text_title =  $_SESSION["NEW_TEXT"];
			$user = new users();
			break;
		}
		case "edit": {
			$titlepage = $_SESSION["MENU_EDIT"];
			$text_title =  $_SESSION["EDIT_TEXT"];
			$user = new users($id);
		break;
		}
		case "delete": {
			$titlepage = $_SESSION["MENU_DELETE"];
			$text_title =  $_SESSION["DELETE_TEXT"];
			$user = new users($id);
			break;
		}
		case "view": {
			$titlepage = $_SESSION["VIEW"];
			$text_title =  $_SESSION["INFORMATION"];
			$user = new users($id);
			break;
		}
	}
	
	$dataForm = $user->dataForm($action);
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
							<h1 class="m-0 text-dark"><i class="fa fa-user"></i> <?= $titlepage ?> <?= $_SESSION["USER"] ?> <small><?= explode(" ",$_SESSION["USER_" . strtoupper($source)])[1] ?></small></h1>
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
								<form id="frmUser" name="frmUser">
									<div class="row">
										<div class="col-md-2">
											<?= $user->showField("ID", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-2">
											<?= $user->showField("THE_PASSWORD", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $user->showField("FIRST_NAME", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $user->showField("LAST_NAME", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<?= $user->showField("IDENTIFICATION", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++], $doc_type->getDataToForm()) ?>
										</div>
										<div class="col-md-4">
											<?= $user->showField("EMAIL", $dataForm["tabs"], "fa fa-envelope", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $user->showField("ADDRESS", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<?= $user->showField("PHONE", $dataForm["tabs"], "fa fa-phone", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<?= $user->showField("CELLPHONE", $dataForm["tabs"], "fa fa-mobile", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-4">
											<div class="form-group">
												<label><?= $user->arrColComments["CITY_ID"] ?> *</label>
												<select class="form-control" id="cbCity" name="cbCity" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $user->city->showAllOptionList(9,$user->city->ID) ?>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label><?= $user->arrColComments["ACCESS_ID"] ?> *</label>
												<select class="form-control" id="cbAccess" name="cbAccess" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $user->access->showOptionList(9,$user->access->ID,0,strtoupper($source)) ?>
												</select>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label><?= $user->arrColComments["REFERENCE"] ?> *</label>
												<select class="form-control" id="cbReference" name="cbReference" <?= $dataForm["readonly"][$cont++] ?>>
												</select>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label><?= $_SESSION["USER_TYPE"] ?> *</label>
<?
	$comFB = explode(",",$user->arrColComments["FACEBOOK_USER"]);
	$comGO = explode(",",$user->arrColComments["GOOGLE_USER"]);
	$disabledAccess = $user->ACCESS_ID == 10 ? $dataForm["readonly"][$cont++] : "disabled";
	if($disabledAccess == "disabled")
		$cont++;
?>
												<div class="input-group">
													<input id="chkUserType" name="chkUserType" type="checkbox" class="form-control" checked data-toggle="toggle" data-on="<?= $comFB[1] ?>" data-off="<?= $comGO[1] ?>" <?= $disabledAccess ?> /> <!-- data-onstyle="primary" data-offstyle="danger"/> -->
												</div>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label><?= $user->arrColComments["CHANGE_PASSWORD"] ?> *</label>
												<div class="input-group">
													<input id="cbChangePassword" name="cbChangePassword" type="checkbox" class="form-control" <?= ($user->CHANGE_PASSWORD ? "checked" : " ") ?> data-toggle="toggle" data-on="<?= $_SESSION["MSG_YES"] ?>" data-off="<?= $_SESSION["MSG_NO"] ?>" data-onstyle="success" <?= $dataForm["readonly"][$cont++] ?> />
												</div>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label><?= $user->arrColComments["IS_BLOCKED"] ?> *</label>
												<div class="input-group">
													<input id="cbBlocked" name="cbBlocked" type="checkbox" class="form-control" <?= ($user->IS_BLOCKED ? "checked" : " ") ?> data-toggle="toggle" data-on="<?= $_SESSION["MSG_YES"] ?>" data-off="<?= $_SESSION["MSG_NO"] ?>" data-onstyle="danger" data-offstyle="success" <?= $dataForm["readonly"][$cont++] ?> />
												</div>
											</div>
										</div>
									</div>
<?
	if($action != "new") {
?>
									<div class="row">
										<div class="col-md-3">
											<?= $user->showField("REGISTERED_BY", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $user->showField("REGISTERED_ON", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $user->showField("MODIFIED_BY", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $user->showField("MODIFIED_ON", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
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
									<button type="button" class="btn btn-danger" id="btnCancel" name="btnCancel" title="<?= $_SESSION["MENU_CANCEL"] ?>"><i class="fa fa-times-circle"></i> <?= $_SESSION["MENU_CANCEL"] ?></button>
<?
	}
	else {
?>
									<button type="button" class="btn btn-primary" id="btnReturn" name="btnReturn" title="<?= $_SESSION["MENU_CANCEL"] ?>" onclick="location.href='users-manager.php?src=<?= $source ?>';"><i class="fa fa-arrow-left"></i> <?= $_SESSION["MENU_CANCEL"] ?></button>
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
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();
		$('#txtEMAIL').on('input',function(e){
			var nameParts = $(this).val().split("@");
			$("#txtID").val(nameParts.length == 2 ? nameParts[0] : "");
		});		
		$("#cbAccess").on("change", function() {
			var value = $(this).val();
			$('#chkUserType').attr("disabled", value != "10");
			$('#chkUserType').bootstrapToggle(value == "10" ? 'enable' : 'disable');
			if (parseInt(value) >= 20 && parseInt(value) < 90) {
				var noty;
				$.ajax({
					url: 'core/actions/_load/__loadReferences.php',
					data: { 
						value: value,
						ref: "<?= $usua->REFERENCE ?>"
					},
					dataType: "json",
					beforeSend: function (xhrObj) {
						var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_LOADING"] ?>";
						noty = notify("", "dark", "", message, "", false);												
					},
					success:function(data){
						noty.close();
						$("#cbReference").empty();
						$.each(data, function(i, item) {
							$('#cbReference').append($('<option>').text(item.text).attr('value', item.value).attr('selected', item.selected));
						});
						//$('#cbReference').attr("disabled",false);
						$('#cbReference').focus();
					}
				});
			}
		});		
		$("#btnSave").on("click", function(e) {
			var form = document.getElementById('frmUser');
			var noty;
			if (form.checkValidity() === false) {
				window.event.preventDefault();
				window.event.stopPropagation();
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				return false;
			}
			var title = $("#hfAction").val() + " <?= $_SESSION["USER"] ?>";
			var url = $("#hfLinkAction").val();
			var $frm = $("#frmUser");
			var datasObj = $frm.serializeObject();
			if(!datasObj.hasOwnProperty("txtID")) {
				datasObj["txtID"] = $("#txtID").val();
			}
			if(!datasObj.hasOwnProperty("cbBlocked")) {
				datasObj["cbBlocked"] = $("#cbBlocked").is(':checked');
			}
			if(!datasObj.hasOwnProperty("chkUserType")) {
				datasObj["chkUserType"] = $("#chkUserType").is(':checked');
			}
			datasObj["hfSocialNetwork"] = $('#chkUserType').attr('disabled');
			datasObj["cbChangePassword"] = $("#cbChangePassword").is(':checked');
			datasObj["cbTBL_SYSTEM_USER_IDENTIFICATION"] = $("#hfDocType_" + $("#cbTBL_SYSTEM_USER_IDENTIFICATION").val()).val();
			datasObj["src"] = "<?= $source ?>";
			var datas = JSON.stringify(datasObj);
			$("#spanTitle").html(title);
			$("#spanTitleName").html("");
			$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM"] ?>");
			$("#btnActivate").unbind("click");
			$("#btnActivate").bind("click", function() {
				var noty;
				$.ajax({
					url: url,
					method: "POST",
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
		$("#cbAccess").trigger("change");
	});
	
    </script>
<?
	include("core/templates/__mapModal.tpl");
	include("core/templates/__messages.tpl");
?>
</body>
</html>
