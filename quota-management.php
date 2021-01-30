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
	
	$filename = "quotas.php" . ($source == "" ? "" : "?src=" . $source);

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
			$inter->redirect("quotas.php");		
	}
	
	require_once("core/classes/quota.php");
	$quota = new quota();
	
	if($id != "") {
		//Asigna la informacion
		$quota->ID = $id;
		$quota->__getInformation();
		//Si hay error
		if($quota->nerror > 0) {
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
	
	$dataForm = $quota->dataForm($action);
	//Inicia el contador
	$cont = 0;

	require_once("core/classes/configuration.php");
	$conf = new configuration("PAYMENT_MERCHANT_ID");
	$merchId = $conf->verifyValue();

	$conf = new configuration("PAYMENT_REQUEST_TOKEN");
	$urlToken = $conf->verifyValue();

	$conf = new configuration("PAYMENT_REQUEST_CHARGE");
	$urlCharge = $conf->verifyValue();
	
	
	
	$buttonText = $action == "new" ? $_SESSION["PAY"] : $_SESSION["ADD_FUNDS"];
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
							<h1 class="m-0 text-dark"><i class="fa fa-credit-card"></i> <?= $titlepage ?> <?= $_SESSION["QUOTA"] ?></h1>
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
								<form id="frmQuota" name="frmQuota">
									<div class="row">
										<div class="col-md-4">
											<label><?= $quota->arrColComments["QUOTA_TYPE_ID"] ?> *</label>
											<select class="form-control" id="cbQuotaType" name="cbQuotaType" <?= $dataForm["readonly"][$cont++] ?>>
												<?= $quota->type->showOptionList(9,$quota->type->ID) ?>
											</select>
										</div>
										<div class="col-md-4">
											<label><?= $quota->arrColComments["CLIENT_ID"] ?> *</label>
											<select class="form-control" id="cbClient" name="cbClient" <?= $dataForm["readonly"][$cont++] ?> <?= $uscli->REFERENCE != "" ? "disabled" : "" ?>>
												<?= $quota->client->showOptionList(9,$uscli->REFERENCE) ?>
											</select>
										</div>
										<div class="col-md-4">
											<?= $quota->showField("AMOUNT", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<?= $quota->showField("CREDIT_CARD_NUMBER", $dataForm["tabs"], "fa fa-credit-card-alt", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-6">
											<?= $quota->showField("CREDIT_CARD_NAME", $dataForm["tabs"], "fa fa-user", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<?= $quota->showField("DATE_EXPIRATION", $dataForm["tabs"], "fa fa-calendar-times-o", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $quota->showField("VERIFICATION_CODE", $dataForm["tabs"], "fa fa-cc", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $quota->showField("DIFERRED_TO", $dataForm["tabs"], "fa fa-calendar", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $quota->showField("PAYMENT_ID", $dataForm["tabs"], "fa fa-money", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
<?
	if($action != "new") {
?>
									<div class="row">
										<div class="col-md-3">
											<?= $quota->showField("REGISTERED_BY", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $quota->showField("REGISTERED_ON", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $quota->showField("MODIFIED_BY", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $quota->showField("MODIFIED_ON", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
<?
	}
?>
									<input type="hidden" name="hfValidCard" id="hfValidCard" value="false" />
								</form>
							</div>
							<!-- /.card-body -->
							<div class="card-footer">
								<div class="btn-group float-right">
<?
	if($action != "view") {
?>
									<button type="button" class="btn btn-warning" id="btnPay" name="btnPay" title="<?= $buttonText ?>" onclick="pay();">
										<i class="fa fa-money"></i>
										<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $buttonText ?></span>
									</button>
									<button type="button" class="btn btn-success" id="btnSaveQuota" name="btnSaveQuota" title="<?= $_SESSION["SAVE_CHANGES"] ?>"><i class="fa fa-floppy-o"></i> <?= $_SESSION["SAVE_CHANGES"] ?></button>
									<button type="button" class="btn btn-danger" id="btnCancel" name="btnCancel" title="<?= $_SESSION["MENU_CANCEL"] ?>" onclick="location.href='quotas.php?src=<?= $source ?>';"><i class="fa fa-times-circle"></i> <?= $_SESSION["MENU_CANCEL"] ?></button>
<?
	}
	else {
?>
									<button type="button" class="btn btn-primary" id="btnReturn" name="btnReturn" title="<?= $_SESSION["MENU_CANCEL"] ?>" onclick="location.href='quotas.php?src=<?= $source ?>';"><i class="fa fa-arrow-left"></i> <?= $_SESSION["MENU_CANCEL"] ?></button>
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
	$title = $_SESSION["QUOTA"];
	$icon = "<i class=\"fa fa-credit-card\"></i>";
	$userModal = true;
	include("core/templates/__modals.tpl");
	include("core/templates/__footer.tpl");
	
	include("core/templates/__modalPayment.tpl");
?>

	<!-- SlimScroll -->
	<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="plugins/fastclick/fastclick.js"></script>
	<!-- bootstrap toogle -->
	<script src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>	
	<!-- Credit card number validator -->
	<script src="plugins/jquery.cc.validator/jquery.creditCardValidator.js"></script>
	<!-- Cleave -->
	<script src="plugins/cleave/cleave.min.js"></script>
	<!-- Kushki -->
	<script src="https://cdn.kushkipagos.com/kushki.min.js"></script>	
	<!-- Resources -->
	<script src="js/resources.js"></script>	
	
    <script>
	$(document).ready(function() {
		$("#cbQuotaType").on("change", function(e) {
			var selected = $("option:selected", this);
			$("#txtAMOUNT").val(selected.data("amount"));
			
		});
		$("#cbQuotaType").trigger("change");
		$('[data-toggle="tooltip"]').tooltip();
		new Cleave('#txtCREDIT_CARD_NUMBER', {
			creditCard: true
		});
		new Cleave('#txtDATE_EXPIRATION', {
		   date: true,
		   datePattern: ['m', 'y']
		});
		$("#txtCREDIT_CARD_NUMBER").on("change", function(e) {
			$('#txtCREDIT_CARD_NUMBER').validateCreditCard(function(result) {
				var type = result.card_type.name;
				type = (type == "diners") ? "diners-club" : type;
				var icon = "fa fa-cc-" + type;
				$("#icontxtCREDIT_CARD_NUMBER").removeClass().addClass(icon);
				$("#hfValidCard").val(result.valid);
			});
		});
		$("#btnSaveQuota").on("click", function(e) {
			var form = document.getElementById('frmQuota');
			var noty;
			if (form.checkValidity() === false) {
				window.event.preventDefault();
				window.event.stopPropagation();
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				return false;
			}
			var title = $("#hfAction").val() + " <?= $_SESSION["QUOTA"] ?>";
			var url = $("#hfLinkAction").val();
			var $frm = $("#frmQuota");
			var datasObj = $frm.serializeObject();
			if(!datasObj.hasOwnProperty("txtAMOUNT")) {
				datasObj["txtAMOUNT"] = $("#txtAMOUNT").val();
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
	});
	function pay() {
		var form = document.getElementById('frmQuota');
		var noty;
		if (form.checkValidity() === false) {
			window.event.preventDefault();
			window.event.stopPropagation();
			notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
			return false;
		}
		if($("#hfValidCard").val() != "true") {
			notify("", "danger", "", "<?= $_SESSION["INVALID_CREDIT_CARD"] ?>", "");
			$("#txtCREDIT_CARD_NUMBER").focus();
			return false;
		}
		var title = $("#hfAction").val() + " <?= $_SESSION["QUOTA"] ?>";
		var url = $("#hfLinkAction").val();
		var $frm = $("#frmQuota");
		var datasObj = $frm.serializeObject();
		if(!datasObj.hasOwnProperty("txtAMOUNT")) {
			datasObj["txtAMOUNT"] = $("#txtAMOUNT").val();
		}
		var datas = JSON.stringify(datasObj);
		$("#spanTitle").html(title);
		$("#spanTitleName").html("");
		$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM_AND_PAY"] ?>");
		$("#btnActivate").unbind("click");
		$("#btnActivate").bind("click", function() {
			var noty;
			$.ajax({
				url: url,
				data: { 
					strModel: datas,
					payment: "true"
				},
				dataType: "json",
				beforeSend: function (xhrObj) {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
					noty = notify("", "dark", "", message, "", false);												
				},
				success:function(data){
					noty.close();
					if(!data.success) {
						notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
					}
					else {
						if(data.continue) {
							$("#btnSaveQuota").attr("disabled","disabled");
							var id = data.message;
							var day = $("#txtDATE_EXPIRATION").val().split('/');
							var objCard = {
								name: $("#txtCREDIT_CARD_NAME").val(),
								number: $("#txtCREDIT_CARD_NUMBER").val().split(' ').join(''),
								expiryMonth: day[0],
								expiryYear: day[1],
								cvv: $("#txtVERIFICATION_CODE").val()
							};
							var objData = {
								card: objCard,
								totalAmount: parseFloat($("#txtAMOUNT").val()),
								currency: "COP"
							};
							var settings = {
								"async": true,
								"crossDomain": true,
								"url": "<?= $urlToken ?>",
								"method": "POST",
								"headers": {
									"public-merchant-id": "<?= $merchId ?>",
									"content-type": "application/json"
								},
								"processData": false,
								"data": JSON.stringify(objData),
								"dataType": "json",
								"beforeSend": function (xhrObj) {
									var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["PROCESSING_PAYMENT"] ?>";
									noty = notify("", "dark", "", message, "", false);												
								}
							}

							$.ajax(settings).done(function (response) {
								if(response.token != "") {
									var token = response.token;
									var objAmount = {
										subtotalIva: 0,
										subtotalIva0: parseFloat($("#txtAMOUNT").val()),
										ice: 0,
										iva: 0,
										currency: "COP"
									};
									var objDeferred = {
										graceMonths: "00",
										creditType: "01",
										months: parseInt($("#txtDIFERRED_TO").val())
									};
									var objMeta = {
										contractID: id
									};
									var objData = {
										token: token,
										amount: objAmount,
										deferred: objDeferred,
										metadata: objMeta,
										fullResponse: true
									};
								}
								var settings = {
									"async": true,
									"crossDomain": true,
									"url": "<?= $urlCharge ?>",
									"method": "POST",
									"headers": {
										"private-merchant-id": "<?= $merchId ?>",
										"content-type": "application/json"
									},
									"processData": false,
									"data": JSON.stringify(objData),
									"dataType": "json",
									"error": function (jqXHR, textStatus) {
										var response = jqXHR.responseJSON;
										var msg = "<?= $_SESSION["ERROR_ON_PAYMENT"] ?><br />" + response.code + ": " + response.message;
										notify("", "danger", "", msg, "");
									},
									"always": function() {
										noty.close();
									}				
								}
								$.ajax(settings).done(function (response) {
									if(response.ticketNumber != "") {
										$.ajax({
											url: "core/actions/_save/__newPayment.php",
											data: { 
												strModel: JSON.stringify(response),
												payment: "true"
											},
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
									}
									else {
										var msg = "<?= $_SESSION["ERROR_ON_PAYMENT"] ?><br />" + response.code + ": " + response.message;
										notify("", "danger", "", msg, "");
									}
								});
							});
						}
						//Si ya genero la transacción de pago
						else {
							notify("", 'info', "", data.message, "");
						}
					}
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
