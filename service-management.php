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

	//Verifica si tiene servicio asignado
	require_once("core/classes/assign_service.php");
	$assi = new assign_service();
	
	switch($action) {
		case "new": {
			$titlepage = substr($_SESSION["MENU_NEW"],-1) == "o" ? substr($_SESSION["MENU_NEW"],0,-1) . "a" : $_SESSION["MENU_NEW"];
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
			
			$assi->setService($id);
			$assi->getInformationByService();
			if($assi->nerror > 0)
				$assi = new assign_service();
				
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

	$gate = $conf->verifyValue("PAYMENT_GATEWAY");
	$accTok = 0;
	$err = 0;

	//Verifica la pasarela
	if($gate == "WOMPI") {
		//Libreria requerida
		require_once("core/classes/ws_query.php");
		require_once("core/actions/_save/__wompiGatewayFunctions.php");

		$pubkey = $conf->verifyValue("PAYMENT_WOMPI_PUBLIC_KEY");
		$urlAccToken = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_GET_ACCEPTANCE_TOKEN");
		$urlReturn = $conf->verifyValue("WEB_SITE") . $conf->verifyValue("SITE_ROOT") . $conf->verifyValue("PAYMENT_WOMPI_REDIRECT");
		
		$accTok = 1;
		
		//Obtiene el acceptance token
		$accTokRet = getAcceptanceToken($urlAccToken, $pubkey);

		//Si no es null
		if($accTokRet["token"] != null) {
			$accTokData = $accTokRet["token"];
		}
		else {
			$err = 1;
		}
	}

	if($uscli->PHONE == "" && $uscli->CELLPHONE != "")
		$uscli->PHONE = $uscli->CELLPHONE;
	
	$max_size = $conf->verifyValue("MAXIMUM_SIZE");
	$max_weight = $conf->verifyValue("MAXIMUM_WEIGHT");
	
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
												<select class="form-control" id="cbZoneRequested" name="cbZoneRequested" <?= $dataForm["readonly"][$cont++] ?>>
													<?= $service->request_zone->showOptionList(9,$service->request_zone->PARENT_ZONE) ?>
												</select>
												<input type="hidden" value="<?= $service->request_zone->PARENT_ZONE ?>" name="hfZoneReqSel" id="hfZoneReqSel" />
											</div>
											<div class="col-md-6">
<?
	$arr = explode(" ",$service->arrColComments["REQUESTED_ZONE"]);
	$arr2 = array_shift($arr);
	$ctrltitle = $_SESSION["SUB_ZONE_NAME"] . " " . implode(" ", $arr);
?>												
												<label><?= $ctrltitle ?></label>												
												<select class="form-control" id="cbZoneRequestedSub" name="cbZoneRequestedSub" disabled>
													<?= $service->request_zone->showOptionList(9,$service->request_zone->ID,0,false) ?>
												</select>
												<input type="hidden" value="<?= $service->request_zone->ID ?>" name="hfSubZoneReqSel" id="hfSubZoneReqSel" />
											</div>
										</div>
									</div>
									<div class="panel-footer">
										<button type="button" title="<?= $_SESSION["NEXT"] ?>" class="btn btn-primary nextBtn float-right">
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
												<?= $service->showField("DELIVER_PHONE", $dataForm["tabs"], "fa fa-mobile", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
											</div>
											<div class="col-md-6">
												<?= $service->showField("DELIVER_CELLPHONE", $dataForm["tabs"], "fa fa-mobile", "", $dataForm["showvalue"], "", false, "9,9,12", "", null, false, true, $dataForm["readonly"][$cont++], "data-compare=\"true\" data-compareto=\"txtDELIVER_PHONE\"") ?>				
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
													<?= $service->deliver_zone->showOptionList(9,$service->deliver_zone->PARENT_ZONE) ?>
												</select>
												<input type="hidden" value="<?= $service->deliver_zone->PARENT_ZONE ?>" name="hfZoneDelSel" id="hfZoneDelSel" />
											</div>
											<div class="col-md-6">
<?
	$arr = explode(" ",$service->arrColComments["DELIVER_ZONE"]);
	$arr2 = array_shift($arr);
	$ctrltitle = $_SESSION["SUB_ZONE_NAME"] . " " . implode(" ", $arr);
?>												
												<label><?= $ctrltitle ?></label>
												<select class="form-control" id="cbZoneDeliverSub" name="cbZoneDeliverSub" disabled>
													<?= $service->deliver_zone->showOptionList(9,$service->deliver_zone->ID,0,false) ?>
												</select>
												<input type="hidden" value="<?= $service->deliver_zone->ID ?>" name="hfSubZoneDelSel" id="hfSubZoneDelSel" />
											</div>
										</div>
									</div>
									<div class="panel-footer">
										<button type="button" title="<?= $_SESSION["NEXT"] ?>" class="btn btn-primary nextBtn float-right">
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
												<?= $service->showField("TOTAL_WIDTH", $dataForm["tabs"], "fa fa-arrows-h", "", $dataForm["showvalue"], "0", false, "9,9,12", "", null, false, true, true, "data-maximum=\"$max_size\"") ?>
											</div>
											<div class="col-md-2">
												<?= $service->showField("TOTAL_HEIGHT", $dataForm["tabs"], "fa fa-arrows-v", "", $dataForm["showvalue"], "0", false, "9,9,12", "", null, false, true, true, "data-maximum=\"$max_size\"") ?>
											</div>
											<div class="col-md-2">
												<?= $service->showField("TOTAL_LENGTH", $dataForm["tabs"], "fa fa-expand", "", $dataForm["showvalue"], "0", false, "9,9,12", "", null, false, true, true, "data-maximum=\"$max_size\"") ?>
											</div>
											<div class="col-md-2">
												<?= $service->showField("TOTAL_WEIGHT", $dataForm["tabs"], "fa fa-balance-scale", "", $dataForm["showvalue"], "0", false, "9,9,12", "", null, false, true, true, "data-maximumw=\"$max_weight\"") ?>
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
										<button type="button" title="<?= $_SESSION["NEXT"] ?>" class="btn btn-primary nextBtn float-right">
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
									<div class="panel-body" id="panelBodyPartners" data-state="0">
										<label class="partner-selection btn btn-lg btn-block">
											<div class="info-box mb-3 ">
												<span class="info-box-icon"><img src="img/partners/oops.png" class="img-fluid"></span>
												<div class="info-box-content">
													<div class="row">
														<div class="col-md-3">
															<span class="info-box-number">¡OOPS!</span>
															<span class="info-box-text">Algo pasó</span>
														</div>
														<div class="col-md-9">
															<span class="info-box-number">¡Ocurrió algo inesperado!</span>
															<span class="info-box-text">No se puede calcular la distancia y el precio</span>
														</div>
													</div>
												</div>
											</div>
										</label>									
									</div>
									<div class="panel-footer">
										<div class="float-left">
											<p><small><?= $_SESSION["PRICE_CALCULATED_MESSAGE"] ?></small></p>
										</div>
										<div class="btn-group float-right" id="grpButtons"></div>
										<input type="hidden" name="hfAction" id="hfAction" value="<?= $action ?>" />
										<input type="hidden" name="hfPRICE" id="hfPRICE" value="0" />
										<input type="hidden" name="hfQUOTAID" id="hfQUOTAID" value="" />
										<input type="hidden" name="hfUSERID" id="hfUSERID" value="<?= $userId ?>" />
										<input type="hidden" name="hfOBJPAY" id="hfOBJPAY" value="" />
										<input type="hidden" name="hfMERCH_ID" id="hfMERCH_ID" value="<?= $merchId ?>" />
										<input type="hidden" name="hfDISTANCE" id="hfDISTANCE" value="" />
										<input type="hidden" name="hfIsMarco" id="hfIsMarco" value="" />
										<input type="hidden" name="hfState" id="hfState" value="1" />
										<input type="hidden" name="hfTimeStart" id="hfTimeStart" value="" />
										<input type="hidden" name="hfTimeEnd" id="hfTimeEnd" value="" />
										<input type="hidden" name="hfPayed" id="hfPayed" value="false" />
										<input type="hidden" name="hfRateId" id="hfRateId" value="" />
										<input type="hidden" name="hfPartnerName" id="hfPartnerName" value="<?= $assi->partner->PARTNER_NAME ?>" />
										<input type="hidden" name="cbVehicleType" id="cbVehicleType" value="" />
										<input type="hidden" name="hfPartnerId" id="hfPartnerId" value="<?= $assi->PARTNER_ID ?>" />
										<input type="hidden" name="hfAssignedPartner" id="hfAssignedPartner" value="" />
										<input type="hidden" name="hfAssignedEmployee" id="hfAssignedEmployee" value="" />
										<input type="hidden" name="hfGateWay" id="hfGateWay" value="<?= $gate ?>" />
										<input type="hidden" name="hfContinueStep" id="hfContinueStep" value="false" />
										<input type="hidden" name="hfPayOnDeliver" id="hfPayOnDeliver" value="false" />
										<input type="hidden" name="hfIsRepeated" id="hfIsRepeated" value="false" />
										<input type="hidden" name="hfPeriod" id="hfPeriod" value="" />
										<input type="hidden" name="hfLastDate" id="hfLastDate" value="" />
										<input type="hidden" name="hfIsQuota" id="hfIsQuota" value="false" />
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
	var chkRt = null;
	var zones = null;
	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();
		$('[data-compare="true"]').change(function() {
			var ctrl = $(this).data("compareto");
			var value = "true";
			console.log($("#hfAction").val());
			if($("#hfAction").val() != "view") {
				if ($(this).val() != $("#" + ctrl).val()) {
					notify("", 'danger', "", "<?= $_SESSION["PHONE_NO_MATCH"] ?>", "");
					value = "false";
					$("#" + ctrl).focus();
				}
			}
			$("#hfContinueStep").val(value);
		});
		$('[data-maximum="<?= $max_size ?>"]').change(function() {
			var maxim = $(this).data("maximum");
			if (parseFloat($(this).val()) >= parseFloat(maxim) && !$(this).is('[disabled=disabled]')) {
				notify("", 'warning', "", "<?= $_SESSION["OVERSIZED"] ?>", "");
			}
		});
		$('[data-maximumw="<?= $max_weight ?>"]').change(function() {
			var maxim = $(this).data("maximumw");
			if (parseFloat($(this).val()) >= parseFloat(maxim) && !$(this).is('[disabled=disabled]')) {
				notify("", 'warning', "", "<?= $_SESSION["OVERWEIGHTED"] ?>", "");
			}
		});
		$.getJSON( "core/actions/_load/__loadZonesNew.php", { q: "all" } ).done(function( data ) {
			var items = [];
			zones = data;
			$.each(zones, function( key, val ) {
				if(val.parent == null) {
					items.push( "<option value='" + val.id + "'>" + val.label + "</option>" );
				}
			});
			$("#cbZoneRequested").find('option').remove().end().append(items.join(""));
			$("#cbZoneRequested").val($("#hfZoneReqSel").val());
			$("#cbZoneRequested").trigger('change');
			$("#cbZoneDeliver").find('option').remove().end().append(items.join(""));
			$("#cbZoneDeliver").val($("#hfZoneDelSel").val());
			$("#cbZoneDeliver").trigger('change');
			$("#cbZone").find('option').remove().end().append(items.join(""));
			$("#cbZone").val($("#cbZone option:first").val());
			$("#cbZone").trigger('change');
		}).fail(function( jqxhr, textStatus, error ) {
			var err = textStatus + ", " + error;
			console.log( "Request Failed: " + err );
		});
		$("select[name^='cbZone']").on("change", function () {
			var value = $(this).val();
			var name = $(this).attr("id");
			var hfLat = "";
			var hfLng = "";
			var sub = name + "Sub";
			var items = [];
			if(name.indexOf("Request") > -1) {
				hfLat = "#hfLATITUDE_REQUESTED_ADDRESS"; 
				hfLng = "#hfLONGITUDE_REQUESTED_ADDRESS";
			}
			else {
				hfLat = "#hfLATITUDE_DELIVER_ADDRESS";
				hfLng = "#hfLONGITUDE_DELIVER_ADDRESS";
			}
			if(name.indexOf("Sub") > -1) {
				if($(hfLat).val() == "") $(hfLat).val($(this).find("option:selected").data("latitude"));
				if($(hfLng).val() == "") $(hfLng).val($(this).find("option:selected").data("longitude"));
				setDistance();
				return false;
			}
			else {
				$.each(zones, function( key, val ) {
					if(val.parent == value) {
						items.push( "<option value='" + val.id + "' data-parent='" + value + "' data-latitude='" + val.lat + "' data-longitude='" + val.lng + "'>" + val.label + "</option>" );
					}
				});
				$("#" + sub).removeAttr("disabled");
				$("#" + sub).find('option').remove().end().append(items.join(""));
				$('#' + sub).trigger("change");
			}				
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
			var opt = $(this).find("option:selected").data("optionpy");
			var val = $(this).find("option:selected").val();
			$('#btnPayment').attr("disabled", opt == "off" && $("#panelBodyPartners").data("state") == "0");
			$('#btnSave').attr("disabled", opt != "off");
			$("#hfIsMarco").val(opt);
			if(opt == "off") {
				$.ajax({
					url: "core/actions/_load/__loadClientQuota.php",
					data: { 
						client: $("#cbClient").val(),
						user: $("#hfUSERID").val()
					},
					dataType: "json",
					beforeSend: function (xhrObj) {
						var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
						noty = notify("", "dark", "", message, "", false);												
					},
					success:function(data){
						noty.close();
						if (data.success) {
							$("#hfQUOTAID").val(data.quota_id);
							$("#hfPeriod").val(data.period);							
							$("#hfIsRepeated").val(data.repeated);
							$("#hfLastDate").val(data.lastDate);
							$("#hfIsQuota").val(data.is_quota);
						}
						else {
							$("#hfQUOTAID").val("");
							$("#hfPeriod").val("");							
							$("#hfIsRepeated").val(false);
							$("#hfLastDate").val("");
							$("#hfIsQuota").val(false);
						}
					}
				});
			}
		});
		$("#cbDeliverType").on("change", function(e) {
			if(chkRt != null)
				return;
			chkRt = true;
			var selected = $("option:selected", this);
			var distance = setDistance();
			var noty;
			var dats = selected.data();
			$("#txtTOTAL_WIDTH").attr("disabled", dats.block);
			$("#txtTOTAL_HEIGHT").attr("disabled", dats.block);
			$("#txtTOTAL_WEIGHT").attr("disabled", dats.block);
			$("#txtTOTAL_LENGTH").attr("disabled", dats.block);
			$("#txtTOTAL_WIDTH").val(dats.block ? dats.width : "");
			$("#txtTOTAL_HEIGHT").val(dats.block ? dats.height : "");
			$("#txtTOTAL_WEIGHT").val(dats.block ? dats.weight : "");
			$("#txtTOTAL_LENGTH").val(dats.block ? dats.length : "");
			distance = parseFloat($("#hfDISTANCE").val());
			if(distance > 0) {
				$.ajax({
					url: "core/actions/_load/__checkRate.php",
					data: { 
						distance: distance,
						client: $("#cbClient").val(),
						round: $("#cbRoundTrip").is(':checked'),
						action: $("#hfAction").val(),
						pid: $("#hfPartnerId").val()
					},
					dataType: "json",
					beforeSend: function (xhrObj) {
						var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["CALCULATING"] ?>";
						noty = notify("", "dark", "", message, "", false);												
					},
					success:function(data){
						noty.close();
						if($("#hfAction").val() != "view") {
							$("#hfPRICE").val("");
							$("#txtPRICE").val("");
							$("#hfRateId").val("");
							$("#hfPartnerName").val("");
							$("#hfPartnerId").val("");
							$("#btnPayment").attr("disabled",true);
							$("#btnSave").attr("disabled",true);
							$("#panelBodyPartners").data("state", 0);
							$("[id^='spanSelected_']" ).hide();
						}
						if(data.success) {
							$("#txtPRICE").val(FormatNumber(data.min,2) + " - " + FormatNumber(data.max,2));
							$("#panelBodyPartners").html(data.message);
							$("#panelBodyPartners").data("state", 1);
							$("#grpButtons").html(data.buttons);
							if(data.change)
								$("#hfQUOTAID").val = "";
							if(data.filtered && data.filter != "") {
								$("#spanSelected_" + data.filter).show();
								$("#hfAssignedPartner").val(data.filter);
							}
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
							if($("#hfAction").val() == "view") {
								var datas = $(".optPartner[data-partnerid=" + $("#hfPartnerId").val() + "]").data();
								console.log(datas);
								$("#hfRateId").val($(".optPartner[data-partnerid=" + $("#hfPartnerId").val() + "]").val());
								$("#hfPRICE").val(datas.rate);
								$("#txtPRICE").val(FormatNumber(datas.rate,2));
								$("#hfPartnerName").val(datas.partner);
								$("#cbVehicleType").val(datas.vehicle);
								$("#hfPartnerId").val(datas.partnerid);
								$("[id^='spanSelected_']" ).hide();
								$("#spanSelected_" + $(".optPartner[data-partnerid=" + $("#hfPartnerId").val() + "]").val()).show();
							}
						}
						else 
							notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
						chkRt = null;
					}
				});
			}
			else {
				if($("#cbDeliverType").is(":visible"))
					notify("", 'danger', "", "<?= $_SESSION["CANT_CALCULATE_DISTANCE"] ?>", "");
				else if(parseInt($('.nextBtn').closest(".setup-content").attr("id").substr(5,1)) >= 2)
					notify("", 'danger', "", "<?= $_SESSION["CANT_CALCULATE_DISTANCE"] ?>", "");
				chkRt = null;
			}
		});
		var checkAddressChange = function(obj) {
			var val = removeAccents($(obj).val().toUpperCase());
			var id = $(obj).attr('id');
			var ref = id.split("_")[0];

			if(val.indexOf("BOGOTA") > -1)
				$("#" + ref.replace("txt","Zone")).fadeIn();
			else 
				$("#" + ref.replace("txt","Zone")).fadeOut();
			$('#cbDeliverType').trigger("change");
		};
		$("[id$=_ADDRESS]").on('input', function () { checkAddressChange(this) });
		$("[id$=_ADDRESS]").on('change', function () { checkAddressChange(this) });
		$("[id$=_ADDRESS]").focusout(function () { checkAddressChange(this) });
		$('#cbDeliverType').trigger("change");
		$("#cbClient").trigger("change");
		if($("#hfLATITUDE_REQUESTED_ADDRESS").val() != "" && $("#hfLONGITUDE_REQUESTED_ADDRESS").val() != "")
			$("#ZoneREQUESTED").fadeOut();

		if($("#hfSubZoneReqSel").val() != "") {
			$("#cbZoneRequested").trigger("change");
			$("#cbZoneRequestedSub option[value=" + $("#hfSubZoneReqSel").val() + "]").attr('selected','selected').change();
		}
		if($("#hfSubZoneDelSel").val() != "") {
			$("#cbZoneDeliver").trigger("change");
			$("#cbZoneDeliverSub option[value=" + $("#hfSubZoneDelSel").val() + "]").attr('selected','selected').change();
		}
		if($("#hfAction").val() == "view") {
			$("#cbDeliverType").trigger("change");
		}
	});
	function onDeliver() {
		$("#hfPayOnDeliver").val("true");
		Save();
	}
	function Save() {
		if($("#txtPRICE").val() == "0") {
			$('#cbDeliverType').trigger("change");
		}
		if($("#hfQUOTAID").val() == "") {
			$('#cbClient').trigger("change");
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
				method: "POST",
				beforeSend: function (xhrObj) {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
					noty = notify("", "dark", "", message, "", false);												
				},
				error: function( jqxhr, textStatus, error ) {
					var err = textStatus + ", " + error;
					notify("", 'danger', "", "Request Failed: " + err , "");
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
					if($("#hfIsRepeated").val() == "true" && $("#hfPeriod").val() == "N") {
						$.ajax({
							url: "core/actions/_save/__newApplyPayment.php",
							data: { 
								qid: $("#hfQUOTAID").val(),
								sid: Math.floor(Math.random() * 100000) + 1,
								payment: $("#hfIsQuota").val(),
								value: $("#hfPRICE").val()
							},
							dataType: "json",
							beforeSend: function (xhrObj) {
								var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
								noty = notify("", "dark", "", message, "", false);												
							},
							success: function(data) {
								if(data.success) {
									notify("", 'success', "", "<?= $_SESSION["PAYMENT_REGISTERED"] ?>", "");
									var url = "core/actions/_save/__newService.php";
									$("#hfPayed").val("true");
									$("#hfOBJPAY").val(JSON.stringify(transaction));
									$("#hfQUOTAID").val("");
									var $frm = $("#frmService");
									var datasObj = $frm.serializeObject();
									datasObj = checkSerializedObject(datasObj);
									var datas = JSON.stringify(datasObj);
									$.ajax({
										url: url,
										data: { 
											strModel: datas,
											sid: data.sid,
											payment: data.payment
										},
										dataType: "json",
										method: "POST",
										beforeSend: function (xhrObj) {
											var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
											noty = notify("", "dark", "", message, "", false);												
										},
										success:function(data) {
											noty.close();
											notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
											if(data.success) {
												noty.close();
												location.href = data.link
											}
											noty.close();
										}
									});
								}
								else {
									notify("", 'danger', "", "<?= $_SESSION["ERROR_ON_PAYMENT"] ?> <br />State: " + data.error, "");
								}
							}
						});
					}
					else if(<?= ($accTok && !$err) ?>) {
						var reference = "<?= uniqid() ?>";
						$.getScript("<?= $script ?>", function( data, textStatus, jqxhr ) {
							var checkout = new WidgetCheckout({
								currency: 'COP',
								amountInCents: (parseInt($("#hfPRICE").val()) * 100),
								reference: reference,
								publicKey: '<?= $pubkey ?>'
								//,redirectUrl: '<?= $urlReturn ?>'
							});
							checkout.open(function ( result ) {
								var transaction = result.transaction
								if(transaction.status == "APPROVED") {
									notify("", 'success', "", "<?= $_SESSION["PAYMENT_REGISTERED"] ?>", "");
									var url = "core/actions/_save/__newService.php";
									$("#hfPayed").val("true");
									$("#hfOBJPAY").val(JSON.stringify(transaction));
									var $frm = $("#frmService");
									var datasObj = $frm.serializeObject();
									datasObj = checkSerializedObject(datasObj);
									var datas = JSON.stringify(datasObj);
									var noty;
									$.ajax({
										url: url,
										data: { strModel: datas },
										dataType: "json",
										method: "POST",
										beforeSend: function (xhrObj) {
											var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
											noty = notify("", "dark", "", message, "", false);												
										},
										success:function(data) {
											noty.close();
											notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
											if(data.success) {
												$.ajax({
													url: "core/actions/_save/__processPayFromGateway.php",
													data: { 
														id: data.srvid,
														strdata: $("#hfOBJPAY").val(),
														gate: $("#hfGateWay").val(),
														ref: reference
													},
													dataType: "json",
													beforeSend: function (xhrObj) {
														var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
														noty = notify("", "dark", "", message, "", false);												
													},
													success:function(data) {
														noty.close();
														notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
														if(data.success) {
															location.href = data.link
														}
													}
												});
											}
										}
									});
								}
								else {
									notify("", 'danger', "", "<?= $_SESSION["ERROR_ON_PAYMENT"] ?> <br />State: " + transaction.status + "<br />Err:" + transaction.statusMessage, "");
								}
							});						
						});
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
			}
		});
	}
	function checkSerializedObject(datasObj) {
		if(!datasObj.hasOwnProperty("hfTimeStart") || datasObj["hfTimeStart"] == "") {
			if($("#hfTimeStart").val() == "")
				$('#cbDeliverTime').trigger("change");
			datasObj["hfTimeStart"] = $("#hfTimeStart").val();
			datasObj["hfTimeEnd"] = $("#hfTimeEnd").val();
		}
		if(!datasObj.hasOwnProperty("hfTimeEnd") || datasObj["hfTimeEnd"] == "") {
			if($("#hfTimeEnd").val() == "")
				$('#cbDeliverTime').trigger("change");
			datasObj["hfTimeStart"] = $("#hfTimeStart").val();
			datasObj["hfTimeEnd"] = $("#hfTimeEnd").val();
		}
		if(!datasObj.hasOwnProperty("txtREQUESTED_ADDRESS")) {
			datasObj["txtREQUESTED_ADDRESS"] = $("#txtREQUESTED_ADDRESS").val();
		}
		if(!datasObj.hasOwnProperty("txtDELIVER_ADDRESS")) {
			datasObj["txtDELIVER_ADDRESS"] = $("#txtDELIVER_ADDRESS").val();
		}
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
		if(!datasObj.hasOwnProperty("hfPayOnDeliver")) {
			datasObj["hfPayOnDeliver"] = $("#hfPayOnDeliver").val();
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
		if(!datasObj.hasOwnProperty("txtTOTAL_HEIGHT")) {
			datasObj["txtTOTAL_HEIGHT"] = $("#txtTOTAL_HEIGHT").val();
		}
		if(!datasObj.hasOwnProperty("txtTOTAL_LENGTH")) {
			datasObj["txtTOTAL_LENGTH"] = $("#txtTOTAL_LENGTH").val();
		}
		if(!datasObj.hasOwnProperty("txtTOTAL_WEIGHT")) {
			datasObj["txtTOTAL_WEIGHT"] = $("#txtTOTAL_WEIGHT").val();
		}
		if(!datasObj.hasOwnProperty("txtTOTAL_WIDTH")) {
			datasObj["txtTOTAL_WIDTH"] = $("#txtTOTAL_WIDTH").val();
		}
		if(!datasObj.hasOwnProperty("txtTOTAL_WIDTH")) {
			datasObj["txtTOTAL_WIDTH"] = $("#txtTOTAL_WIDTH").val();
		}
		if($("#hfZoneReqSel").val() == "0") {
			$("#hfZoneReqSel").val($("#cbZoneRequested option:selected").val());
			datasObj["hfZoneReqSel"] = $("#hfZoneReqSel").val();
		}
		if($("#hfSubZoneReqSel").val() == "0") {
			$("#hfSubZoneReqSel").val($("#cbZoneRequestedSub option:selected").val());
			datasObj["hfSubZoneReqSel"] = $("#hfSubZoneReqSel").val();
		}
		if($("#hfZoneDelSel").val() == "0") {
			$("#hfZoneDelSel").val($("#cbZoneDeliver option:selected").val());
			datasObj["hfZoneDelSel"] = $("#hfZoneDelSel").val();
		}
		if($("#hfSubZoneDelSel").val() == "0") {
			$("#hfSubZoneDelSel").val($("#cbZoneDeliverSub option:selected").val());
			datasObj["hfSubZoneDelSel"] = $("#hfSubZoneDelSel").val();
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
