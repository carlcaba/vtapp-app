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
			$text_title =  "Ingrese la información solicitada para crear un nuevo registro. <small>Los campos marcados con * son requeridos.</small>";
			//TODO Nativapps
			$titlepage = $is_client_ally ? 'Nueva Usuario Aliado' : $_SESSION["MENU_NEW"];
			require_once("core/classes/affiliate_subscription.php");
			require_once("core/classes/client.php");
			$affiliate_subscription = new affiliate_subscription();
			$as_dataForm = $affiliate_subscription->dataForm($action);
			$client = new client();
			$c_dataForm = $client->dataForm($action);
			////////
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
	
	//TODO Nativapps
	$conf = new configuration("USER_AFFILIATE_BASIC_RATE");
	$user_affiliate_basic_rate =  $conf->verifyValue();
	$conf = new configuration("USER_AFFILIATE_ALLIED_COMPANY");
	$user_affiliate_allied_company =  $conf->verifyValue();
	$conf = new configuration("USER_AFFILIATE_COMPANY_USERS");
	$user_affiliate_company_users =  $conf->verifyValue();
	$conf = new configuration("USER_AFFILIATE_DELIVERY_ALLIED");
	$user_affiliate_delivery_allied =  $conf->verifyValue();
	$conf = new configuration("MAX_USERS_AFFILIATION_BASIC_RATE");
	$max_users_affiliation_basic_rate =  $conf->verifyValue();
	$conf = new configuration("MAX_USERS_AFFILIATION_ALLIED_COMPANY");
	$max_users_affiliation_allied_company =  $conf->verifyValue();
	$conf = new configuration("MAX_USERS_AFFILIATION_COMPANY");
	$max_users_affiliation_company =  $conf->verifyValue();
	$conf = new configuration("MAX_USERS_AFFILIATION_DELIVERY_ALLIED");
	$max_users_affiliation_delivery_allied =  $conf->verifyValue();
	////////////////////////////	


	$buttonText = $action == "new" ? $_SESSION["PAY"] : $_SESSION["ADD_FUNDS"];
	
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
	
?>
<!DOCTYPE html>
<html>
<head>
<?
	include("core/templates/__header.tpl");
?>
	<!-- bootstrap toogle -->
	<link rel="stylesheet" href="plugins/bootstrap-toggle/css/bootstrap-toggle.min.css"></link>	
	<link rel="stylesheet" href="plugins/bs-stepper/css/bs-stepper.min.css"></link>	
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
											<label><?= $quota->arrColComments["CLIENT_ID"] ?> *</label>
											<select class="form-control" id="cbClient" name="cbClient" <?= $dataForm["readonly"][$cont++] ?> <?= $uscli->REFERENCE != "" ? "disabled" : "" ?>>
												<?= $quota->client->showOptionList(9,$uscli->REFERENCE) ?>
											</select>
										</div>
										<div class="col-md-4">
											<label><?= $quota->arrColComments["QUOTA_TYPE_ID"] ?> *</label>
											<select class="form-control" id="cbQuotaType" name="cbQuotaType" <?= $dataForm["readonly"][$cont++] ?>>
											</select>
										</div>
										<div class="col-md-4">
											<?= $quota->showField("AMOUNT", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
									<div class="row" style="display:none;" id="divAffiliate">
										<div class="col-md-12">
											<div id="stepperCompanyUserAffiliation" class="bs-stepper">
												<div class="bs-stepper-header">
													<div class="step" data-target="#test-l-1">
														<button type="button" class="step-trigger">
															<span class="bs-stepper-circle">1</span>
															<span class="bs-stepper-label"><?= $_SESSION["AFFILIATION_RATE_STEP_LABEL_1"] ?></span>
														</button>
													</div>
													<div class="line"></div>
													<div class="step" data-target="#test-l-2">
														<button type="button" class="step-trigger">
															<span class="bs-stepper-circle">2</span>
															<span class="bs-stepper-label"><?= $_SESSION["AFFILIATION_RATE_STEP_LABEL_2"] ?></span>
														</button>
													</div>
													<!--
													<div class="line"></div>
													<div class="step" data-target="#test-l-3">
														<button type="button" class="step-trigger">
															<span class="bs-stepper-circle">3</span>
															<span class="bs-stepper-label"><?= $_SESSION["AFFILIATION_RATE_STEP_LABEL_3"] ?></span>
														</button>
													</div>
													<div class="line"></div>
													<div class="step" data-target="#test-l-4">
														<button type="button" class="step-trigger">
															<span class="bs-stepper-circle">4</span>
															<span class="bs-stepper-label">Fin</span>
														</button>
													</div>
													-->
												</div>
												<div class="bs-stepper-content">
													<div id="test-l-1" class="content">
														<h2 class="text-center"><?= $_SESSION["AFFILIATION_RATE_STEP1_H2"] ?></h2>
														<p class="text-center"><?= $_SESSION["AFFILIATION_RATE_STEP1_P"] ?></p>
														<div class="form-check text-center">
															<input class="form-check-input form-control" id="acceptTermsConditionsId" name="acceptTermsConditions" type="checkbox" data-toggle="toggle" data-on="Si" data-off="No" data-onstyle="success">
															<label class="form-check-label" for="acceptTermsConditions">
																<?= $_SESSION["AFFILIATION_RATE_ACCEPT_TERMS_CONDITIONS"] ?>
															</label>
														</div>
													</div>
													<div id="test-l-2" class="content">
														<h2 class="text-center"><?= $_SESSION["AFFILIATION_RATE_STEP2_H4"] ?></h2>
														<div class="container my-4 clearfix">
															<!-- Shopping cart table -->
															<div class="card">
																<div class="card-body">
																	<div class="table-responsive">
																		<table class="table table-bordered m-0">
																			<thead>
																				<tr>
																					<!-- Set columns width -->
																					<th class="text-center py-3 px-4" style="min-width: 200px;"><?= $_SESSION["AFFILIATION_RATE_STEP2_TB_COL1"] ?></th>
																					<th class="text-right py-3 px-4" style="width: 180px;"><?= $_SESSION["AFFILIATION_RATE_STEP2_TB_COL2"] ?></th>
																					<th class="text-center py-3 px-4" style="width: 120px;"><?= $_SESSION["AFFILIATION_RATE_STEP2_TB_COL3"] ?></th>
																					<th class="text-right py-3 px-4" style="width: 200px;"><?= $_SESSION["AFFILIATION_RATE_STEP2_TB_COL4"] ?></th>
																				</tr>
																			</thead>
																			<tbody>
																				<tr>
																					<td class="font-weight-semibold align-middle p-3">
																						<div class="rate-name-basic"><?= $_SESSION["AFFILIATION_RATE_NAME_BASIC"] ?></div>
																					</td>
																					<td class="text-right font-weight-semibold align-middle p-3"><?= "$ " . number_format($user_affiliate_basic_rate,2,",",".") ?></td>
																					<td class="align-middle p-3">
																						<input type="number" name="number_users_rate_basic" data-resource-name="AFFILIATION_RATE_NAME_BASIC" data-rate-value="<?= $user_affiliate_basic_rate ?>" class="form-control text-center number-users-affiliation " min="1" value="1" max="<?= $max_users_affiliation_basic_rate ?>" disabled>
																					</td>
																					<td class="text-right font-weight-semibold align-middle p-3"><span class="number-users-total-rate-basic"><?= "$ " . number_format($user_affiliate_basic_rate,2,",",".") ?></span></td>
																				</tr>
																				<tr>
																					<td class="font-weight-semibold align-middle p-3"><?= $_SESSION["AFFILIATION_RATE_NAME_1"] ?></td>
																					<td class="text-right font-weight-semibold align-middle p-3"><?= "$ " . number_format($user_affiliate_allied_company,2,",",".") ?></td>
																					<td class="align-middle p-3">
																						<input type="number" name="number_users_rate_1" data-resource-name="AFFILIATION_RATE_NAME_1" data-rate-value="<?= $user_affiliate_allied_company ?>" class="form-control text-center number-users-affiliation " min="1" value="1" max="<?= $max_users_affiliation_allied_company ?>">
																					</td>
																					<td class="text-right font-weight-semibold align-middle p-3"><span class="number-users-total-rate-1"><?= "$ " . number_format($user_affiliate_allied_company,2,",",".") ?></span></td>
																				</tr>
																				<tr>
																					<td class="font-weight-semibold align-middle p-3"><?= $_SESSION["AFFILIATION_RATE_NAME_2"] ?></td>
																					<td class="text-right font-weight-semibold align-middle p-3"><?= "$ " . number_format($user_affiliate_company_users,2,",",".") ?></td>
																					<td class="align-middle p-3">
																						<input type="number" name="number_users_rate_2" data-resource-name="AFFILIATION_RATE_NAME_2" data-rate-value="<?= $user_affiliate_company_users ?>" class="form-control text-center number-users-affiliation " min="1" value="1" max="<?= $max_users_affiliation_company ?>">
																					</td>
																					<td class="text-right font-weight-semibold align-middle p-3"><span class="number-users-total-rate-2"><?= "$ " . number_format($user_affiliate_company_users,2,",",".") ?></span></td>
																				</tr>
																				<tr>
																					<td class="font-weight-semibold align-middle p-3"><?= $_SESSION["AFFILIATION_RATE_NAME_3"] ?></td>
																					<td class="text-right font-weight-semibold align-middle p-3"><?= "$ " . number_format($user_affiliate_delivery_allied,2,",",".") ?></td>
																					<td class="align-middle p-3">
																						<input type="number" name="number_users_rate_3" data-resource-name="AFFILIATION_RATE_NAME_3" data-rate-value="<?= $user_affiliate_delivery_allied ?>" class="form-control text-center number-users-affiliation " min="1" value="1" max="<?= $max_users_affiliation_delivery_allied ?>">
																					</td>
																					<td class="text-right font-weight-semibold align-middle p-3"><span class="number-users-total-rate-3"><?= "$ " . number_format($user_affiliate_delivery_allied,2,",",".") ?></span></td>
																				</tr>
																			</tbody>
																		</table>
																	</div>
																	<!-- / Shopping cart table -->
																	<div class="d-flex flex-wrap justify-content-between align-items-center pb-4">
																		<div class="mt-4"></div>
																		<div class="d-flex">
																			<div class="text-right mt-4 mr-5"></div>
																			<div class="text-right mt-4">
																				<label class="text-muted font-weight-normal m-0"><?= $_SESSION["AFFILIATION_RATE_STEP2_LB_TOTAL_VALUE"] ?></label>
																				<div class="text-large"><strong class="total-membership-value"><?= "$ " . number_format($user_affiliate_basic_rate,2,",",".") ?></strong></div>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<!--
													<div id="test-l-3" class="content">
														<div class="card">
															<h5 class="card-header bg-info"><?= $_SESSION["AFFILIATION_RATE_STEP3_TITLE_BILLING_DATA"] ?></h5>
															<div class="card-body">
																<form id="frmBillingData">
																	<div class="form-row">
																		<div class="form-group col-md-12">
																			<label for="business_name"><?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_BUSINESS_NAME"] ?></label>
																			<input type="text" class="form-control" id="business_name" name="business_name" placeholder="<?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_BUSINESS_NAME"] ?>" disabled>
																		</div>
																		<input type="hidden" name="client_id" id="client_id" />
																	</div>
																	<div class="form-row">
																		<div class="form-group col-md-6">
																			<label for="nit"><?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_NIT"] ?></label>
																			<input type="text" class="form-control" id="nit" name="nit" placeholder="<?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_NIT"] ?>" disabled>
																		</div>
																		<div class="form-group col-md-6">
																			<label for="main_phone"><?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_MAIN_PHONE"] ?></label>
																			<div class="input-group mb-2 mr-sm-2">
																				<div class="input-group-prepend">
																					<div class="input-group-text"><i class="fa fa-phone"></i></div>
																				</div>
																				<input type="text" class="form-control" id="main_phone" name="main_phone" placeholder="<?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_MAIN_PHONE"] ?>" disabled>
																			</div>
																		</div>
																	</div>
																	<div class="form-row">
																		<div class="form-group col-md-12">
																			<label for="main_address"><?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_MAIN_ADDRESS"] ?></label>
																			<div class="input-group mb-2 mr-sm-2">
																				<div class="input-group-prepend">
																					<div class="input-group-text"><i class="fa fa-map"></i></div>
																				</div>
																				<input type="email" class="form-control" id="main_address" name="main_address" placeholder="<?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_MAIN_ADDRESS"] ?>" disabled>
																			</div>
																		</div>
																	</div>
																	<div class="form-row">
																		<div class="form-group col-md-12">
																			<label for="legal_representative"><?= explode(',', $client->arrColComments["LEGAL_REPRESENTATIVE"])[1] ?> *</label>
																			<div class="input-group mb-2 mr-sm-2">
																				<div class="input-group-prepend">
																					<div class="input-group-text"><i class="fa fa-user"></i></div>
																				</div>
																				<input type="<?= explode(',', $client->arrColComments["LEGAL_REPRESENTATIVE"])[0] ?>" class="form-control" id="legal_representative" name="legal_representative" placeholder="<?= explode(',', $client->arrColComments["LEGAL_REPRESENTATIVE"])[2] ?>" required>
																			</div>
																		</div>
																	</div>
																</form>
															</div>
														</div>
														<div class="card">
															<h5 class="card-header bg-info"><?= $_SESSION["AFFILIATION_RATE_STEP3_TITLE_CARD_DETAILS"] ?></h5>
															<div class="card-body">
																<form id="frmCardDetails">
																	<div class="form-row">
																		<div class="form-group col-md-12">
																			<?= $affiliate_subscription->showField("CREDIT_CARD_NUMBER", $as_dataForm["tabs"], "fa fa-credit-card-alt", "", false, "", false, "9,9,12", '') ?>
																		</div>
																		<input type="hidden" name="hfValidCard" id="hfValidCard" value="false" />
																	</div>
																	<div class="form-row">
																		<div class="form-group col-md-12">
																			<?= $affiliate_subscription->showField("CREDIT_CARD_NAME", $as_dataForm["tabs"], "fa fa-user", "", false, "", false, "9,9,12", '') ?>
																		</div>
																	</div>
																	<div class="form-row">

																		<div class="form-group col-md-6">
																			<?= $affiliate_subscription->showField("DATE_EXPIRATION", $as_dataForm["tabs"], "fa fa-calendar-times-o", "", false, "", false, "9,9,12", '') ?>
																		</div>

																		<div class="form-group col-md-6">
																			<?= $affiliate_subscription->showField("VERIFICATION_CODE", $as_dataForm["tabs"], "fa fa-cc", "", false, "", false, "9,9,12", '') ?>
																		</div>
																	</div>
																</form>
															</div>
														</div>
													</div>
													<div id="test-l-4" class="content"></div>
													-->
												</div>
											</div>
											<div class="justify-content-between text-center mb-3">
												<button class="btn btn-secondary" id="previousBtn" type="button" style="display: none;"><?= $_SESSION["AFFILIATION_RATE_PREVIOUS_BUTTON"] ?></button>
												<button class="btn btn-primary" id="nextBtn" type="button"><?= $_SESSION["AFFILIATION_RATE_NEXT_BUTTON"] ?></button>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<?= $quota->showField("CREDIT_CARD_NUMBER", $dataForm["tabs"], "fa fa-credit-card-alt cardDetails", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
											<input type="hidden" name="hfValidCard" id="hfValidCard" value="false" />
										</div>
										<div class="col-md-6">
											<?= $quota->showField("CREDIT_CARD_NAME", $dataForm["tabs"], "fa fa-user cardDetails", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<?= $quota->showField("DATE_EXPIRATION", $dataForm["tabs"], "fa fa-calendar-times-o cardDetails", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $quota->showField("VERIFICATION_CODE", $dataForm["tabs"], "fa fa-cc cardDetails", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $quota->showField("DIFERRED_TO", $dataForm["tabs"], "fa fa-calendar cardDetails", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
										<div class="col-md-3">
											<?= $quota->showField("PAYMENT_ID", $dataForm["tabs"], "fa fa-money-bill-1 cardDetails", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]) ?>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<div class="form-group">
												<label><?= $quota->arrColComments["IS_REPEATED"] ?> *</label>
												<div class="input-group">
													<input id="chkRepeated" name="chkRepeated" type="checkbox" class="form-control" <?= (filter_var($quota->IS_REPEATED, FILTER_VALIDATE_BOOLEAN) ? "checked" : " ") ?> data-toggle="toggle" data-on="<?= $_SESSION["MSG_YES"] ?>" data-off="<?= $_SESSION["MSG_NO"] ?>" data-onstyle="success" data-offstyle="primary" <?= $dataForm["readonly"][$cont++] ?> />
												</div>
											</div>
										</div>
										<div class="col-md-4">
											<label><?= $quota->arrColComments["PERIOD"] ?> *</label>
											<select class="form-control" id="cbPeriod" name="cbPeriod" <?= $dataForm["readonly"][$cont++] ?> <?= (filter_var($quota->IS_REPEATED, FILTER_VALIDATE_BOOLEAN) ? "" : "disabled") ?>>
												<?= $quota->showPeriodOptionList(9,$quota->PERIOD) ?>
											</select>
										</div>
										<div class="col-md-4">
											<?= $quota->showField("LAST_DATE", $dataForm["tabs"], "", "", $dataForm["showvalue"], "", false, "9,9,12", (filter_var($quota->IS_REPEATED, FILTER_VALIDATE_BOOLEAN) ? "" : "disabled")) ?>
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
									<input type="hidden" name="hfIdQuota" id="hfIdQuota" value="<?= $quota->ID ?>" />
									<input type="hidden" name="hfActionName" id="hfActionName" value="<?= $action ?>" />
								</form>
							</div>
							<!-- /.card-body -->
							<div class="card-footer">
								<div class="btn-group float-right">
<?
	if($action != "view") {
		if($action != "delete") {
?>
									<button type="button" class="btn btn-warning" id="btnPay" name="btnPay" title="<?= $buttonText ?>" onclick="pay();">
										<i class="fa fa-money-bill-1"></i>
										<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"><?= $buttonText ?></span>
									</button>
<?
		}
?>
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
									<input type="hidden" name="hfIsSaved" id="hfIsSaved" value="false" /> 
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
	<!-- date-range-picker -->
	<script src="plugins/moment/moment.min.js"></script>
	<script src="plugins/daterangepicker/daterangepicker.js"></script>

	<!-- TODO Nativapps -->
	<!-- bs-stepper -->
	<script src="plugins/bs-stepper/js/bs-stepper.min.js"></script>	
	<!-- Credit card number validator -->
	<script src="plugins/jquery.cc.validator/jquery.creditCardValidator.js"></script>
	<!-- Cleave -->
	<script src="plugins/cleave/cleave.min.js"></script>
	<!-- ------------------- -->

<?
	if(!$accTok) {
?>
	<!-- Kushki -->
	<script src="https://cdn.kushkipagos.com/kushki.min.js"></script>	
<?
	}
?>
	<!-- Resources -->
	<script src="js/resources.js"></script>	
	
    <script>
	var stepperCompanyUserAffiliation, stepper = null;
	$(document).ready(function() {
		$.getJSON("core/actions/_load/__loadQuota.php", function(data) {
			if(data.success) {
				$.each(data.message, function(key, value) {
					$("#cbQuotaType").append("<option value='" + value.id + "' data-amount=\"" + value.amount + "\" data-ismarco=\"" + value.ismarco + "\" data-action=\"" + value.action + "\">" + value.text + "</option>");
				});
				$("#cbClient").on("change", function(e) {
					let selected = $("option:selected", this);
					let ismarco = selected.data("optionpy") == "off" ? "1" : "0";
					let compare = parseInt(selected.data("pymttype")) == 2 ? "2" : ismarco;
					$("#cbQuotaType > option").each(function() {
						if($(this).data("ismarco") == compare)
							$(this).show();
						else 
							$(this).hide();
					});
					$('#cbQuotaType option').each(function () {
						if ($(this).css('display') != 'none') {
							$(this).prop("selected", true);
							return false;
						}
					});
					$("#cbQuotaType").trigger("change");
				});
				$("#cbClient").trigger("change");		
			}
		});
<?
	if($_SESSION["LANGUAGE"] != "1") {
?>
			$.getJSON("plugins/daterangepicker/lang/<?= $_SESSION["LANGUAGE"] ?>.json", function(json) { 
				$('#txtLAST_DATE').daterangepicker({
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
			$('#txtLAST_DATE').daterangepicker({
				startDate: "<?= date("Y-m-d") ?>",
				singleDatePicker: true, 
				showDropdowns: true,
				minYear: <?= date("Y") ?>,
				maxYear: <?= intval(date("Y")) + 2 ?>
			});
<?
	}
?>
		$("#cbQuotaType").on("change", function(e) {
			var selected = $("option:selected", this);
			if(selected.data("action") != "none") {
				$("#divAffiliate").fadeIn();
				if(stepperCompanyUserAffiliation == null) {
					stepperCompanyUserAffiliation = $("#stepperCompanyUserAffiliation");
					stepper = new Stepper(stepperCompanyUserAffiliation[0]);
				}
				else {
					$('#previousBtn').hide(); 
					$('#nextBtn').show();
					$('#nextBtn').html('<?= $_SESSION["AFFILIATION_RATE_BTN_START_HERE"] ?>');
					stepper.to(0);
				}
				$('#acceptTermsConditionsId').change(function(){
					$('#nextBtn').prop('disabled', !($(this).is(':checked')));
				});			
				$('#nextBtn').click(function(event) {
					stepper.next();
				});
				$('#previousBtn').click(function() {
					stepper.previous();
				});				
				$(".number-users-affiliation").change(function() {
					var max = parseInt($(this).attr('max'));
					var min = parseInt($(this).attr('min'));
					if ($(this).val() > max) {
						$(this).val(max);
					}
					else if ($(this).val() < min) {
						$(this).val(min);
					}
					calculateUnitTotal($(this));
				});
				$(".number-users-affiliation").on('input', function() {
					calculateUnitTotal($(this));
				});	
				$("#stepperCompanyUserAffiliation")[0].addEventListener('show.bs-stepper', function (event) {
					var indexStep = event.detail.indexStep;
					if (indexStep === 0) {
						$('#previousBtn').hide(); 
						$('#nextBtn').show();
						$('#nextBtn').html('<?= $_SESSION["AFFILIATION_RATE_BTN_START_HERE"] ?>');
					}
					else if (indexStep === 1) {
						var datos = getDataSubscription();
						$('#previousBtn').hide(); 
						$('#nextBtn').hide();
						if (datos) {
							$("#btnActivate").click();
						} 
						else { 
							setTimeout(() => {
								stepper.to(indexStep)
							}, 50);
						}
					}
				});				
			}
			else {
				if($("#divAffiliate").is(":visible")) {
					$("#divAffiliate").fadeOut();
				}
			}
			$("#txtAMOUNT").val(selected.data("amount"));
		});
		$('[data-toggle="tooltip"]').tooltip();
		$("#cbPeriod").on("change", function(e) {
			var selected = $("option:selected", this);
			$("#txtLAST_DATE").attr("disabled",selected.val() == "N");
		});		
		$('#chkRepeated').change(function() {
			var state = $(this).prop('checked');
			$("#cbPeriod").attr("disabled", !state);
			if(state) 
				$("#cbPeriod").trigger("change");
			else 
				$("#txtLAST_DATE").attr("disabled",true);
			$("#btnPay").attr("disabled",state);
		})		
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
				var icon = "fa fa-cc-" + type + " cardDetails";
				$("#icontxtCREDIT_CARD_NUMBER").removeClass().addClass(icon);
				$("#hfValidCard").val(result.valid);
			});
		});
		$("#btnSaveQuota").on("click", function(e) {
			if($("#hfIsSaved").val() != "false")
				return false;
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
			if(!datasObj.hasOwnProperty("cbClient")) {
				datasObj["cbClient"] = $("#cbClient option:selected").val();
			}
			if($("#cbQuotaType").find("option:selected").data("action") != "none") {
				var datos = getDataSubscription();
				if(datos)
					datasObj["custom_plan"] = datos;
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
					data: { 
						strModel: datas,
						payment: false
					},
					dataType: "json",
					method: "POST",
					beforeSend: function (xhrObj) {
						var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
						noty = notify("", "dark", "", message, "", false);												
					},
					success:function(data){
						noty.close();
						notify("", (data.success ? 'info' : 'danger'), "", data.message, "");
						if(data.success) {
							$("#hfIsSaved").val("true");
							location.href = data.link;
						}
					}
				});
			});
			$("#divActivateModal").modal("toggle");			
		});
		if(<?= $err ?>) {
			notify("", 'danger', "", "<?= $_SESSION["ERROR_ON_PAYMENT"] . $accTokRet["message"] ?>", "");
		}
	});
	function calculateUnitTotal(field) {
		var amount = parseInt(field.val());
		var price = parseInt(field.data('rateValue'))
		var total = amount * price;
		var nameField = field.attr('name')
		switch (nameField) {
			case 'number_users_rate_basic':
				$('.number-users-total-rate-basic').text(new Intl.NumberFormat(undefined, {
															style: 'currency',
															currency: 'COP',
															currencyDisplay: 'symbol'
														}).format(total));
				break;
			case 'number_users_rate_1':
				$('.number-users-total-rate-1').text(new Intl.NumberFormat(undefined, {
															style: 'currency',
															currency: 'COP',
															currencyDisplay: 'symbol'
														}).format(total));
				break;
			case 'number_users_rate_2':
				$('.number-users-total-rate-2').text(new Intl.NumberFormat(undefined, {
															style: 'currency',
															currency: 'COP',
															currencyDisplay: 'symbol'
														}).format(total));
				break;
			case 'number_users_rate_3':
				$('.number-users-total-rate-3').text(new Intl.NumberFormat(undefined, {
															style: 'currency',
															currency: 'COP',
															currencyDisplay: 'symbol'
														}).format(total));
				break;
			default:
				break;
		}
		calculateTotalPrice();
	}

	function calculateTotalPrice() {
		var quantities = 0;
		var totalValue = 0;
		$( ".number-users-affiliation" ).each(function() {
			var price = parseInt($(this).data('rateValue'))
			quantities = parseInt($(this).val()) * price;
			totalValue = totalValue + quantities;
		})
		$('.total-membership-value').text(new Intl.NumberFormat(undefined, {
											style: 'currency',
											currency: 'COP',
											currencyDisplay: 'symbol'
										}).format(totalValue));
		$("#txtAMOUNT").val(totalValue);
		$("#cbQuotaType").find("option:selected").data("amount",totalValue);		
		return totalValue;
	}

	function getDataSubscription () {
		dataPersonalizePlan = []
		dataBillingData = {}
		dataCardDetails = {}
		subscriptionFormValidation = true;
		$(".number-users-affiliation").each(function() {
			var data = {};
			data['field'] = $(this).attr('name');
			data['quantities'] = $(this).val();
			data['unit_value'] = $(this).data('rateValue');
			data['resource_name'] = $(this).data('resourceName');
			dataPersonalizePlan.push(data)
		});

		var name = "business_name";
		var client = $("#cbClient").find("option:selected");
		var is_required = true;
		var value = client.text();
		var placeholder = "<?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_BUSINESS_NAME"] ?>";
		//formsValidationRequired
		if (is_required && value === '') {
			subscriptionFormValidation = false;
			notify("", "danger", "", formsValidationRequired.replace(":attribute", placeholder), "");
		}
		dataBillingData[name] = value;
		
		name = "client_id";
		is_required = true;
		value = client.val();
		placeholder = "";
		//formsValidationRequired
		if (is_required && value === '') {
			subscriptionFormValidation = false;
			notify("", "danger", "", formsValidationRequired.replace(":attribute", placeholder), "");
		}
		dataBillingData[name] = value;

		name = "main_phone";
		is_required = true;
		value = client.data("mainphone");
		placeholder = "<?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_MAIN_PHONE"] ?>";
		//formsValidationRequired
		if (is_required && value === '') {
			subscriptionFormValidation = false;
			notify("", "danger", "", formsValidationRequired.replace(":attribute", placeholder), "");
		}
		dataBillingData[name] = value;

		name = "main_address";
		is_required = true;
		value = client.data("address");
		placeholder = "<?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_MAIN_ADDRESS"] ?>";
		//formsValidationRequired
		if (is_required && value === '') {
			subscriptionFormValidation = false;
			notify("", "danger", "", formsValidationRequired.replace(":attribute", placeholder), "");
		}
		dataBillingData[name] = value;

		name = "nit";
		is_required = true;
		value = client.data("nit");
		placeholder = "<?= $_SESSION["AFFILIATION_RATE_STEP3_INPUT_NIT"] ?>";
		//formsValidationRequired
		if (is_required && value === '') {
			subscriptionFormValidation = false;
			notify("", "danger", "", formsValidationRequired.replace(":attribute", placeholder), "");
		}
		dataBillingData[name] = value;

		name = "legal_representative";
		is_required = true;
		value = client.data("contactname");
		placeholder = "<?= explode(',', $client->arrColComments["LEGAL_REPRESENTATIVE"])[2] ?>";
		//formsValidationRequired
		if (is_required && value === '') {
			subscriptionFormValidation = false;
			notify("", "danger", "", formsValidationRequired.replace(":attribute", placeholder), "");
		}
		dataBillingData[name] = value;

		$(".cardDetails").each(function() {
			let id = $(this).attr("id");
			if(typeof id === "undefined") {
				id = $($(this).parent().parent().parent().children()[1]).attr("id");
			}
			else {
				id = id.substr(4);
			}
			dataCardDetails[id] = $("#" + id).val();
		});

		var totalSubscription = calculateTotalPrice();

		if (subscriptionFormValidation) {
			return { dataPersonalizePlan, dataBillingData, dataCardDetails, totalSubscription }
		}
		return subscriptionFormValidation;

	}

	function pay() {
		if($("#hfIsSaved").val() != "false")
			return false;
		var form = document.getElementById('frmQuota');
		var noty;
		var bodyHtml = "<?= $_SESSION["MSG_CONFIRM_AND_PAY"] ?>";
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
		if(<?= ($accTok && !$err) ?>) {
			var link = "<?= str_replace("{0}", $accTokData->data->presigned_acceptance->permalink, $_SESSION["ACCEPTANCE_TOKEN_TEXT"]) ?>";
			bodyHtml += "<br><br><div class=\"form-check\"><input class=\"form-check-input\" type=\"checkbox\" value=\"\" id=\"chkAccToken\" name=\"chkAccToken\"><label class=\"form-check-label\" for=\"chkAccToken\">" + link + "</label></div>";
			datasObj["acceptance_token"] = "<?= $accTokData->data->presigned_acceptance->acceptance_token ?>";
		}
		if(!datasObj.hasOwnProperty("txtAMOUNT")) {
			datasObj["txtAMOUNT"] = $("#txtAMOUNT").val();
		}
		if(!datasObj.hasOwnProperty("cbClient")) {
			datasObj["cbClient"] = $("#cbClient option:selected").val();
		}
		if($("#cbQuotaType").find("option:selected").data("action") != "none") {
			var datos = getDataSubscription();
			if(datos)
				datasObj["custom_plan"] = datos;
		}		
		var datas = JSON.stringify(datasObj);
		$("#spanTitle").html(title);
		$("#spanTitleName").html("");
		$("#modalBody").html(bodyHtml);
		$("#btnActivate").unbind("click");
		$("#btnActivate").bind("click", function() {
			if(<?= ($accTok && !$err) ?>) {
				if(!$("#chkAccToken").is(':checked')) {  
					notify("", "danger", "", "<?= $_SESSION["ERROR_ACCEPT_TOKEN_TEXT"] ?>", "");
					return false;
				}  				
			}
			var noty;
			$.ajax({
				url: url,
				data: { 
					strModel: datas,
					payment: "true"
				},
				method: "POST",
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
						$("#hfIsSaved").val("true");
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
										$.ajax({
											url: "core/actions/_save/__inactivateQuota.php",
											data: { 
												id: id,
												reason: response.code + ": " + response.message
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
								});
							});
						}
						else {
							notify("", 'info', "", data.message, "");
							setTimeout(function() { location.href = data.link; }, 5000);
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
