<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();
	$conf = new configuration("PAYMENT_MERCHANT_ID");
	$merchId = $conf->verifyValue();

	$source = "";
	if(!empty($_GET['src'])) {
		$source = $_GET['src'];
	}
	
	$filename = "services.php" . ($source == "" ? "" : "?src=" . $source);

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
	$userId = "";
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
			$inter->redirect("services.php");		
	}
	
	require_once("core/classes/service.php");
	$service = new service();
	
	if($id != "") {
		//Asigna la informacion
		$service->ID = $id;
		$service->__getInformation();
		//Si hay error
		if($service->nerror > 0) {
			$_SESSION["vtappcorp_user_alert"] = $_SESSION["NOT_REGISTERED"];
			$id = "";
		}
	}
	
	switch($action) {
		case "new": {
			$titlepage = substr($_SESSION["MENU_NEW"],-1) == "o" ? substr($_SESSION["MENU_NEW"],0,-1) . "a" : $_SESSION["MENU_NEW"];
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
	
	$dataForm = $service->dataForm($action);
	
	//Inicia el contador
	$cont = 0;
	$payment = true;
	$userId = $uscli->ID;
	
	//Verifica si es un aliado 
	if(substr($uscli->access->PREFIX,0,2) == "AL") {
		$dataForm["readonly"][14] = "disabled";
		$payment = false;
	}
	else if(substr($uscli->access->PREFIX,0,2) == "CL") {
		$dataForm["readonly"][12] = "disabled";
		$dataForm["readonly"][14] = "disabled";
		$service->setClient($uscli->REFERENCE);
	}

?>
<!DOCTYPE html>
<html>
<head>
<?
	include("core/templates/__header.tpl");
?>
	<!-- bootstrap toogle -->
	<link rel="stylesheet" href="plugins/bootstrap-toggle/css/bootstrap-toggle.min.css"></link>	
	<!-- form step -->
	<link rel="stylesheet" href="plugins/step-wizard/css/step-wizard.css"></link>	
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
							<h1 class="m-0 text-dark"><i class="fa fa-motorcycle"></i> <?= $titlepage ?> <?= $_SESSION["SERVICE"] ?> <small>(<?= $title ?>)</small></h1>
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
								<div class="stepwizard">
									<div class="stepwizard-row setup-panel">
										<div class="stepwizard-step col-xs-3"> 
											<a href="#step-1" type="button" class="btn btn-success btn-circle">1</a>
											<p><small><?= $_SESSION["SERVICE_STEP_1"] ?></small></p>
										</div>
										<div class="stepwizard-step col-xs-3"> 
											<a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
											<p><small><?= $_SESSION["SERVICE_STEP_2"] ?></small></p>
										</div>
										<div class="stepwizard-step col-xs-3"> 
											<a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
											<p><small><?= $_SESSION["SERVICE_STEP_3"] ?></small></p>
										</div>
										<div class="stepwizard-step col-xs-3" id="anchorStep4"> 
											<a href="#step-4" type="button" class="btn btn-default btn-circle" disabled="disabled">4</a>
											<p><small><?= $_SESSION["SERVICE_STEP_4"] ?></small></p>
										</div>
										
									</div>
								</div>
							</div>
						</div>
						<!-- /.card-header -->
						<div class="card-body">
							<form id="frmService" name="frmService">
								<div class="panel panel-primary setup-content" id="step-1">
									<div class="panel-heading">
										<h3 class="panel-title"><?= $_SESSION["COMPLETE_SERVICE_TITLE_1"] ?></h3>
										<p class="panel-title"><?= $_SESSION["COMPLETE_SERVICE_SUBTITLE_1"] ?></p>
									</div>
									<div class="panel-body">
										<div class="row">
											<div class="col-md-6">
												<?= $service->showField("REQUESTED_BY", $dataForm["tabs"], "fa fa-user", "", false, $uscli->getFullName(), false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
											</div>
											<div class="col-md-6">
												<?= $service->showField("REQUESTED_EMAIL", $dataForm["tabs"], "fa fa-envelope", "", false, $uscli->EMAIL, false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<?= $service->showField("REQUESTED_PHONE", $dataForm["tabs"], "fa fa-phone", "", false, $uscli->PHONE, false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
											</div>
											<div class="col-md-6">
												<?= $service->showField("REQUESTED_CELLPHONE", $dataForm["tabs"], "fa fa-mobile", "", false, $uscli->CELLPHONE, false, "9,9,12", $dataForm["readonly"][$cont++]) ?>				
											</div>
										</div>
										<div class="row">
											<div class="col-md-10">
												<?= $service->showField("REQUESTED_ADDRESS", $dataForm["tabs"], "", "", false, $uscli->ADDRESS, false, "9,9,12", $dataForm["readonly"][$cont++]) ?>				
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label>&nbsp;</label>
													<div class="input-group">
														<a href="#" class="anc-another-way" data-type="true" data-text="<?= $_SESSION["ORIGIN"] ?>" data-field="txtREQUESTED_ADDRESS"><small><?= $_SESSION["SELECT_ADD"] ?></small></a> 
													</div>
												</div>
											</div>
										</div>
										<div class="row" id="ZoneREQUESTED">
											<div class="col-md-6">
												<label><?= $service->arrColComments["REQUESTED_ZONE"] ?></label>
												<select class="form-control" id="cbZoneRequest" name="cbZoneRequest" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $service->request_zone->showOptionList(9,$service->request_zone->ID) ?>
												</select>
											</div>
											<div class="col-md-6">
<?
	$arr = explode(" ",$service->arrColComments["REQUESTED_ZONE"]);
	$arr2 = array_shift($arr);
	$ctrltitle = $_SESSION["SUB_ZONE_NAME"] . " " . implode(" ", $arr);
?>												
												<label><?= $ctrltitle ?></label>												
												<select class="form-control" id="cbZoneRequestSub" name="cbZoneRequestSub" disabled>
													<?= $service->request_zone->showOptionList(9,"",0,false) ?>
												</select>
											</div>
										</div>
									</div>
									<div class="panel-footer">
										<button type="button" title="<?= $_SESSION["NEXT"] ?>" class="btn btn-primary nextBtn pull-right">
											<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["NEXT"] ?> </span>
											<i class="fa fa-arrow-circle-right"></i>
										</button>
									</div>
								</div>
								<div class="panel panel-primary setup-content" id="step-2">
									<div class="panel-heading">
										<h3 class="panel-title"><?= $_SESSION["COMPLETE_SERVICE_TITLE_2"] ?></h3>
										<p class="panel-title"><?= $_SESSION["COMPLETE_SERVICE_SUBTITLE_2"] ?></p>
									</div>
									<div class="panel-body">
										<div class="row">
											<div class="col-md-6">
												<?= $service->showField("DELIVER_TO", $dataForm["tabs"], "fa fa-user", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
											</div>
											<div class="col-md-6">
												<?= $service->showField("DELIVER_EMAIL", $dataForm["tabs"], "fa fa-envelope", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<?= $service->showField("DELIVER_PHONE", $dataForm["tabs"], "fa fa-phone", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
											</div>
											<div class="col-md-6">
												<?= $service->showField("DELIVER_CELLPHONE", $dataForm["tabs"], "fa fa-mobile", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>				
											</div>
										</div>
										<div class="row">
											<div class="col-md-10">
												<?= $service->showField("DELIVER_ADDRESS", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>				
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label>&nbsp;</label>
													<div class="input-group">
														<a href="#" class="anc-another-way" data-type="false" data-text="<?= $_SESSION["DESTINY"] ?>" data-field="txtDELIVER_ADDRESS"><small><?= $_SESSION["SELECT_ADD"] ?></small></a> 
													</div>
												</div>
											</div>
										</div>
										<div class="row" id="ZoneDELIVER">
											<div class="col-md-6">
												<label><?= $service->arrColComments["DELIVER_ZONE"] ?></label>
												<select class="form-control" id="cbZoneDeliver" name="cbZoneDeliver" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $service->deliver_zone->showOptionList(9,$service->deliver_zone->ID) ?>
												</select>
											</div>
											<div class="col-md-6">
<?
	$arr = explode(" ",$service->arrColComments["DELIVER_ZONE"]);
	$arr2 = array_shift($arr);
	$ctrltitle = $_SESSION["SUB_ZONE_NAME"] . " " . implode(" ", $arr);
?>												
												<label><?= $ctrltitle ?></label>
												<select class="form-control" id="cbZoneDeliverSub" name="cbZoneDeliverSub" disabled>
													<?= $service->deliver_zone->showOptionList(9,"",0,false) ?>
												</select>
											</div>
										</div>
									</div>
									<div class="panel-footer">
										<button type="button" title="<?= $_SESSION["NEXT"] ?>" class="btn btn-primary nextBtn pull-right">
											<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["NEXT"] ?> </span>
											<i class="fa fa-arrow-circle-right"></i>
										</button>
									</div>
								</div>
								<div class="panel panel-primary setup-content" id="step-3">
									<div class="panel-heading">
										<h3 class="panel-title"><?= $_SESSION["COMPLETE_SERVICE_TITLE_3"] ?></h3>
										<p class="panel-title"><?= $_SESSION["COMPLETE_SERVICE_SUBTITLE_3"] ?></p>
									</div>
									<div class="panel-body">
										<div class="row">
											<div class="col-md-3">
												<label><?= $service->arrColComments["CLIENT_ID"] ?> *</label>
												<select class="form-control" id="cbClient" name="cbClient" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $service->client->showOptionList(9,$service->client->ID, true) ?>
												</select>
											</div>
											<div class="col-md-3">
												<label><?= $service->arrColComments["DELIVERY_TYPE"] ?> *</label>
												<select class="form-control" id="cbDeliverType" name="cbDeliverType" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $service->type->showOptionList(9,$service->type->ID) ?>
												</select>
											</div>
											<div class="col-md-2">
												<?= $service->showField("QUANTITY", $dataForm["tabs"], "", "", false, "1", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label><?= $service->arrColComments["FRAGILE"] ?> *</label>
													<div class="input-group">
														<input id="cbFragile" name="cbFragile" type="checkbox" class="form-control" <?= ($service->FRAGILE ? "checked" : " ") ?> data-toggle="toggle" data-on="<?= $_SESSION["MSG_YES"] ?>" data-off="<?= $_SESSION["MSG_NO"] ?>" data-onstyle="danger" data-offstyle="primary" <?= $dataForm["readonly"][$cont++] ?> />
													</div>
												</div>
											</div>
											<div class="col-md-2">
												<div class="form-group">
													<label><?= $service->arrColComments["ROUND_TRIP"] ?> *</label>
													<div class="input-group">
														<input id="cbRoundTrip" name="cbRoundTrip" type="checkbox" class="form-control" <?= ($service->ROUND_TRIP ? "checked" : " ") ?> data-toggle="toggle" data-on="<?= $_SESSION["MSG_YES"] ?>" data-off="<?= $_SESSION["MSG_NO"] ?>" data-onstyle="success" data-offstyle="primary" <?= $dataForm["readonly"][$cont++] ?> />
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-2">
												<label><?= $service->arrColComments["TIME_START_TO_DELIVER"] ?> *</label>
												<select class="form-control" id="cbDeliverTime" name="cbDeliverTime" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $service->showTimeOptionList(9) ?>
												</select>
											</div>
											<div class="col-md-2">
												<?= $service->showField("TOTAL_WIDTH", $dataForm["tabs"], "fa fa-arrows-h", "", $dataForm["showvalue"], "0", false, "9,9,12", "") ?>
											</div>
											<div class="col-md-2">
												<?= $service->showField("TOTAL_HEIGHT", $dataForm["tabs"], "fa fa-arrows-v", "", $dataForm["showvalue"], "0", false, "9,9,12", "") ?>
											</div>
											<div class="col-md-2">
												<?= $service->showField("TOTAL_LENGTH", $dataForm["tabs"], "fa fa-expand", "", $dataForm["showvalue"], "0", false, "9,9,12", "") ?>
											</div>
											<div class="col-md-2">
												<?= $service->showField("TOTAL_WEIGHT", $dataForm["tabs"], "fa fa-balance-scale", "", $dataForm["showvalue"], "0", false, "9,9,12", "") ?>
											</div>
											<div class="col-md-2">
												<?= $service->showField("PRICE", $dataForm["tabs"], "fa fa-usd", "", $dataForm["showvalue"], "0", false, "9,9,12", "disabled") ?>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<?= $service->showField("DELIVER_DESCRIPTION", $dataForm["tabs"], "fa fa-question-circle-o", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
											</div>
											<div class="col-md-6">
												<?= $service->showField("OBSERVATION", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
											</div>
										</div>
									</div>
									<div class="panel-footer">
										<div class="float-left">
											<p><small><?= $_SESSION["PRICE_CALCULATED_MESSAGE"] ?></small></p>
										</div>
										<button type="button" title="<?= $_SESSION["NEXT"] ?>" class="btn btn-primary nextBtn pull-right">
											<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["NEXT"] ?> </span>
											<i class="fa fa-arrow-circle-right"></i>
										</button>
									</div>
								</div>
								<div class="panel panel-primary setup-content" id="step-4">
									<div class="panel-heading">
										<h3 class="panel-title"><?= $_SESSION["COMPLETE_SERVICE_TITLE_4"] ?></h3>
										<p class="panel-title"><?= $_SESSION["COMPLETE_SERVICE_SUBTITLE_4"] ?></p>
									</div>
									<div class="panel-body" id="panelBodyPartners"></div>
									<div class="panel-footer">
										<div class="float-left">
											<p><small><?= $_SESSION["PRICE_CALCULATED_MESSAGE"] ?></small></p>
										</div>
										<div class="btn-group float-right">
<?
	if($service->client->CLIENT_PAYMENT_TYPE_ID != 1) {
?>
											<button type="button" data-toggle="tooltip" data-placement="top" title="<?= $_SESSION["GO_TO_PAY"] ?>" id="btnPayment" name="btnPayment" class="btn btn-warning pull-right" onclick="payment();" disabled>
												<i class="fa fa-money"></i>
												<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $_SESSION["GO_TO_PAY"] ?></span>
											</button>
<?
	}
?>
											<button type="button" title="<?= $_SESSION["SAVE"] ?>" id="btnSave" name="btnSave" class="btn btn-success pull-right" disabled>
												<i class="fa fa-floppy-o"></i>
												<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"> <?= $_SESSION["SAVE"] ?></span>
											</button>
											<input type="hidden" name="hfPRICE" id="hfPRICE" value="0" />
											<input type="hidden" name="hfQUOTAID" id="hfQUOTAID" value="" />
											<input type="hidden" name="hfUSERID" id="hfUSERID" value="<?= $userId ?>" />
											<input type="hidden" name="hfOBJPAY" id="hfOBJPAY" value="" />
											<input type="hidden" name="hfMERCH_ID" id="hfMERCH_ID" value="<?= $merchId ?>" />
											<input type="hidden" name="hfDISTANCE" id="hfDISTANCE" value="" />
											<input type="hidden" name="hfIsMarco" id="hfIsMarco" value="" />
											<input type="hidden" name="hfState" id="hfState" value="SERVICE_STATE_1" />
											<input type="hidden" name="hfTimeStart" id="hfTimeStart" value="" />
											<input type="hidden" name="hfTimeEnd" id="hfTimeEnd" value="" />
											<input type="hidden" name="hfPayed" id="hfPayed" value="false" />
											<input type="hidden" name="hfRateId" id="hfRateId" value="" />
											<input type="hidden" name="hfPartnerName" id="hfPartnerName" value="" />
											<input type="hidden" name="cbVehicleType" id="cbVehicleType" value="" />
											<input type="hidden" name="hfPartnerId" id="hfPartnerId" value="" />
										</div>
									</div>
								</div>
							</form>
						</div>
						<!-- /.card-body -->
					</div>
					<!-- /.card -->
				</div>
				<!-- /.col -->
			<!-- /.row -->
			</section>
			<!-- /.content -->
		</div>
	
<?
	$titlepage = $_SESSION["SERVICE"];
	$icon = "<i class=\"fa fa-motorcycle\"></i>";
	$userModal = true;
	include("core/templates/__modals.tpl");
	include("core/templates/__footer.tpl");

	if($payment) {
		require_once("core/classes/configuration.php");
		$conf = new configuration("PAYMENT_MERCHANT_ID");
		$merchId = $conf->verifyValue();

		include("core/templates/__modalPayment.tpl");
	}

	$titlepageUpload = $_SESSION["UPLOAD_SERVICES"];
	$textUpload = str_replace("{__filename__}","services",$_SESSION["LOAD_TITLE_2"]);
	$parameters = "?class=service&link=services.php&file=service";
	$saveUpload = "core/actions/_save/__saveUploadedServices.php";
	include("core/templates/__modalUpload.tpl");

?>
	<!-- /.content-wrapper -->

	<!-- SlimScroll -->
	<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="plugins/fastclick/fastclick.js"></script>
	<!-- bootstrap toogle -->
	<script src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>	
	<!-- form-step -->
	<script src="plugins/step-wizard/js/step-wizard.js"></script>	
	<!-- Resources -->
	<script src="js/resources.js"></script>	
	
    <script>
	var address = "txtREQUESTED_ADDRESS",
		alt_address = "txtDELIVER_ADDRESS",
		latitude = "hfLATITUDE_REQUESTED_ADDRESS",
		longitude = "hfLONGITUDE_REQUESTED_ADDRESS";
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();
		$("select[name^='cbZone']").on("change", function () {
			var value = $(this).val();
			var name = $(this).attr("id");
			var hfLat = "";
			var hfLng = "";
			if(name.indexOf("Request") > -1) {
				hfLat = "#hfLATITUDE_REQUESTED_ADDRESS";
				hfLng = "#hfLONGITUDE_REQUESTED_ADDRESS";
			}
			else {
				hfLat = "#hfLATITUDE_DELIVER_ADDRESS";
				hfLng = "#hfLONGITUDE_DELIVER_ADDRESS";
			}
			if(name.indexOf("Sub") > -1) {
				$(hfLat).val($(this).find("option:selected").data("latitude"));
				$(hfLng).val($(this).find("option:selected").data("longitude"));
				setDistance();
				return false;
			}
			var sub = name + "Sub";
			$("#" + sub).removeAttr("disabled");
			$("#" + sub).find("option[data-parent='" + value + "']").removeAttr("disabled");
			$("#" + sub).find("option[data-parent!='" + value + "']").attr("disabled","disabled");
			$('#' + sub + ' option:not([disabled]):first').attr('selected', 'selected');
			$('#' + sub).trigger("change");
		});
		$("select[name^='cbZone']").trigger("change");
		$('#cbRoundTrip').change(function() {
			$('#cbDeliverType').trigger("change");
		});
		$(".anc-another-way").click(function() {
			$("#hfDestinyField").val($(this).data("field"));
			$("#typeAddress").html($(this).data("text"));
			$.ajax({
				url: "core/actions/_load/__loadAddress.php",
				data: { 
					type: $(this).data("type")
				},
				dataType: "json",
				beforeSend: function (xhrObj) {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
					noty = notify("", "dark", "", message, "", false);												
				},
				success:function(data){
					noty.close();
					if (data.success) {
						if($.fn.DataTable.isDataTable('#tableAddresses')) {
							$('#tableAddresses').DataTable().clear().draw();
							$('#tableAddresses').DataTable().destroy();
						}
						$("#tableAddressesBody").empty();
						$.each(data.data, function(key, value) {
							$('#tableAddressesBody').append('<tr><td>' + value.name + '</td><td>' + value.address + '</td><td>' + value.zone + '</td><td>' + value.button + '</td></tr>');
						});							
						makeTableAddress();
					}
					else 
						notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
				}
			});
			
			$("#mdlAddress").modal("show");
		});
		$('#cbDeliverTime').change(function() {
			var start = $(this).find("option:selected").val();
			var end = $(this).find("option:selected").data("end");
			$('#hfTimeStart').val(start);
			$('#hfTimeEnd').val(end);
		});
		$('#cbClient').change(function() {
			var opt = $(this).find("option:selected").data("optionpy")
			$('#btnPayment').attr("disabled", opt == "off");
			$('#btnSave').attr("disabled", opt != "off");
			$("#hfIsMarco").val(opt);
		});
		$("#cbDeliverType").on("change", function(e) {
			var selected = $("option:selected", this);
			distance = setDistance();
			var noty;
			if(distance > 0) {
				$.ajax({
					url: "core/actions/_load/__checkRate.php",
					data: { 
						distance: distance,
						round: $("#cbRoundTrip").is(':checked')
					},
					dataType: "json",
					beforeSend: function (xhrObj) {
						var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["CALCULATING"] ?>";
						noty = notify("", "dark", "", message, "", false);												
					},
					success:function(data){
						noty.close();
						$("#hfPRICE").val("");
						$("#txtPRICE").val("");
						$("#hfRateId").val("");
						$("#hfPartnerName").val("");
						$("#hfPartnerId").val("");
						$("#panelBodyPartners").html("");
						$("#btnPayment").attr("disabled",true);
						$("#btnSave").attr("disabled",true);
						$("[id^='spanSelected_']" ).hide();
						if(data.success) {
							$("#txtPRICE").val(FormatNumber(data.min,2) + " - " + FormatNumber(data.max,2));
							$("#panelBodyPartners").html(data.message);
							$(".optPartner").on("click", function() {
								var datas = $(this).data();
								$("#hfRateId").val($(this).val());
								$("#hfPRICE").val(datas.rate);
								$("#txtPRICE").val(FormatNumber(datas.rate,2));
								$("#hfPartnerName").val(datas.partner);
								$("#cbVehicleType").val(datas.vehicle);
								$("#hfPartnerId").val(datas.partnerid);
								$("[id^='spanSelected_']" ).hide();
								$("#spanSelected_" + $(this).val()).show();
								$("#btnPayment").attr("disabled",false);
								$("#btnSave").attr("disabled",false);
							});
						}
						else 
							notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
					}
				});
			}
			else {
				if($("#cbDeliverType").is(":visible"))
					notify("", 'danger', "", "<?= $_SESSION["CANT_CALCULATE_DISTANCE"] ?>", "");
			}
		});
		var checkAddressChange = function(obj) {
			var val = $(obj).val().toUpperCase();
			var id = $(obj).attr('id');
			var ref = id.split("_")[0];
			if(val.indexOf("BOGOTÁ") > -1)
				$("#" + ref.replace("txt","Zone")).fadeIn();
			else 
				$("#" + ref.replace("txt","Zone")).fadeOut();
			$('#cbDeliverType').trigger("change");
		};
		$("[id*=_ADDRESS]").on('input', function () { checkAddressChange(this) });
		$("[id$=_ADDRESS]").on('change', function () { checkAddressChange(this) });
		$("[id$=_ADDRESS]").focusout(function () { checkAddressChange(this) });
		$("#btnSave").on("click", function(e) {
			if($("#txtPRICE").val() == "0") {
				$('#cbDeliverType').trigger("change");
			}
			var form = document.getElementById('frmService');
			var noty;
			if (form.checkValidity() === false) {
				window.event.preventDefault();
				window.event.stopPropagation();
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				return false;
			}
			var title = "<?= $_SESSION["SAVE_SERVICE"] ?>";
			var url = "core/actions/_save/__newService.php";
			var $frm = $("#frmService");
			var datasObj = $frm.serializeObject();
			datasObj = checkSerializedObject(datasObj);
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
		$('#cbDeliverType').trigger("change");
		$("#cbDeliverTime").trigger("change");
		$("#cbClient").trigger("change");

	});
	function setDistance() {
		var distance;
		var orig = {
			lat: parseFloat($("#hfLATITUDE_REQUESTED_ADDRESS").val()),
			lng: parseFloat($("#hfLONGITUDE_REQUESTED_ADDRESS").val())
		};
		var dest = {
			lat: parseFloat($("#hfLATITUDE_DELIVER_ADDRESS").val()),
			lng: parseFloat($("#hfLONGITUDE_DELIVER_ADDRESS").val())
		};
		if(isNaN(orig.lat) || isNaN(orig.lng) || isNaN(dest.lat) || isNaN(dest.lng)) 
			distance = 0;
		else 
			distance = getDistance(orig, dest);
		$("#hfDISTANCE").val(distance);
		return distance;
	}
	function showMap(control) {
		address = "txt" + control;
		latitude = "hfLATITUDE_" + control;
		longitude = "hfLONGITUDE_" + control;
		$("#divMapModal").modal("toggle");
	}
	function SaveData(url, data, pay) {
		$.ajax({
			url: url,
			data: { 
				strModel: data,
				payment: pay
			},
			dataType: "json",
			beforeSend: function (xhrObj) {
				var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
				noty = notify("", "dark", "", message, "", false);												
			},
			success:function(data){
				noty.close();
				return data.success;
			}
		});
	}
	function payment() {
		if($("#txtPRICE").val() == "0") {
			$('#cbDeliverType').trigger("change");
		}
		var form = document.getElementById('frmService');
		var noty;
		for (var i = 0; i < form.elements.length; i++) {
			var e = form.elements[i];
			try {
				if (e.dataset.custom != "NotRequired") {
					if (e.value == "" && e.type != "hidden") {
						var placeholder = $("#" + e.id).attr("placeholder");
						if(typeof(placeholder) !== "undefined" && placeholder) {
							notify("", "danger", "", "<?= $_SESSION["MUST_SELECT"] ?> " + placeholder, "");
							e.classList.add('error');
							return false;
						}
					}
				}
				else {
					break;
				}
			}
			catch(e) {
				continue;
			}
		}
		$.ajax({
			url: "core/actions/_load/__checkUserQuota.php",
			data: { 
				user: "<?= $userId ?>",
				value: $("#hfPRICE").val()
			},
			dataType: "json",
			beforeSend: function (xhrObj) {
				var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
				noty = notify("", "dark", "", message, "", false);												
			},
			success: function(data) {
				noty.close();
				$("#hfPayed").val(data.success);
				if(data.success) {
					$("#hfQUOTAID").val(data.id);
					$("#hfOBJPAY").val(JSON.stringify(data.message));
					var $frm = $("#frmService");
					var datasObj = $frm.serializeObject();
					datasObj = checkSerializedObject(datasObj);
					var datas = JSON.stringify(datasObj);
					$("#spanTitle").html("<?= $_SESSION["SAVE_AND_PAY"] ?>");
					$("#spanTitleName").html("");
					$("#modalBody").html("<?= $_SESSION["MSG_CONFIRM_AND_DISCOUNT"] ?>");
					$("#btnActivate").unbind("click");
					$("#btnActivate").bind("click", function() {
						var noty;
						$.ajax({
							url: "core/actions/_save/__newService.php",
							data: { 
								strModel: datas,
								strPayment: $("#hfOBJPAY").val()
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
					});
					$("#divActivateModal").modal("toggle");			
				}
				else {
					$("#frmPayment").attr("action", 'core/actions/_save/__newCheckout.php');
					$("#frmPayment").empty();
					$("#frmPayment").append('<input type="hidden" name="serviceData" value="' + $("#frmPayment").serialize() + '">');
					notify("", 'danger', "", data.message, "");
					var kushki = new KushkiCheckout({
						form: "frmPayment",
						merchant_id: "<?= $merchId ?>",
						amount: $("#hfPRICE").val(),
						currency: "COP", 
						is_subscription: false,
						inTestEnvironment: true,
						regional: false 
					});					
					$("#divPayment").modal("toggle");			
				}
			}
		});
	}
	function checkSerializedObject(datasObj) {
		if(!datasObj.hasOwnProperty("txtUSER_ID")) {
			datasObj["txtUSER_ID"] = $("#hfUSERID").val();
		}
		if(!datasObj.hasOwnProperty("cbClient")) {
			datasObj["cbClient"] = $("#cbClient").val();
		}
		if(!datasObj.hasOwnProperty("txtREQUESTED_BY")) {
			datasObj["txtREQUESTED_BY"] = $("#txtREQUESTED_BY").val();
		}
		if(!datasObj.hasOwnProperty("txtREQUESTED_EMAIL")) {
			datasObj["txtREQUESTED_EMAIL"] = $("#txtREQUESTED_EMAIL").val();
		}
		if(!datasObj.hasOwnProperty("cbRoundTrip")) {
			datasObj["cbRoundTrip"] = $("#cbRoundTrip").is(':checked');
		}
		if(!datasObj.hasOwnProperty("cbFragile")) {
			datasObj["cbFragile"] = $("#cbFragile").is(':checked');
		}
		if(!datasObj.hasOwnProperty("txtQUANTITY")) {
			datasObj["txtQUANTITY"] = $("#txtQUANTITY").val();
		}
		if(!datasObj.hasOwnProperty("hfPayed")) {
			datasObj["hfPayed"] = $("#hfPayed").val();
		}
		if(!datasObj.hasOwnProperty("hfRateId")) {
			datasObj["hfRateId"] = $("#hfRateId").val();
		}
		if(!datasObj.hasOwnProperty("cbVehicleType")) {
			datasObj["cbVehicleType"] = $("#cbVehicleType").val();
		}
		if(!datasObj.hasOwnProperty("hfPartnerId")) {
			datasObj["hfPartnerId"] = $("#hfPartnerId").val();
		}
		return datasObj;	
	}
	</script>
<?
	include("core/templates/__modalAddress.tpl");
	include("core/templates/__mapModal.tpl");
	include("core/templates/__messages.tpl");
?>
</body>
</html>
