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
			$text_title =  "Ingrese la información solicitada para crear un nuevo registro. <small>Los campos marcados con * son requeridos.</small>";
			$user = new users();
			//TODO Nativapps
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
			$user = new users($id);
		break;
		}
		case "delete": {
			$titlepage = "Confirme que desea eliminar este registro.";
			$text_title =  $_SESSION["DELETE_TEXT"];
			$user = new users($id);
			break;
		}
		case "view": {
			$titlepage = $_SESSION["VIEW"];
			$text_title =  "Información";
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
	//TODO Nativapps
	if ($action === 'new') {
		include("core/templates/__modalAffiliate.php"); 
	}
	/////////////
?>

	<!-- SlimScroll -->
	<script src="plugins/slimScroll/jquery.slimscroll.min.js"></script>
	<!-- FastClick -->
	<script src="plugins/fastclick/fastclick.js"></script>
	<!-- bootstrap toogle -->
	<script src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>	
	<!-- Resources -->
	<script src="js/resources.js"></script>	
	<!-- TODO Nativapps -->
	<!-- bs-stepper -->
	<script src="plugins/bs-stepper/js/bs-stepper.min.js"></script>	
	<!-- Credit card number validator -->
	<script src="plugins/jquery.cc.validator/jquery.creditCardValidator.js"></script>
	<!-- Cleave -->
	<script src="plugins/cleave/cleave.min.js"></script>
	<!-- ------------------- -->
	
    <script>
	$(document).ready(function() {
		//TODO Nuevo desarrollo Nativapps
		var action = "<?= $action ?>";
		// Inicializar el stepper
		if (action === 'new') {
			var stepperCompanyUserAffiliation = $("#stepperCompanyUserAffiliation");
			var stepper = new Stepper(stepperCompanyUserAffiliation[0]);
			var nextBtn = $('#nextBtn');
			var previousBtn = $('#previousBtn');
			var btnCancelActivate = $('#btnCancelActivate');
			var acceptTermsConditionsId = $('#acceptTermsConditionsId');
			var numberUsersAffiliation = $( ".number-users-affiliation" );
			var numberUsersTotalBasic = $('.number-users-total-rate-basic');
			var numberUsersTotalRate1 = $('.number-users-total-rate-1');
			var numberUsersTotalRate2 = $('.number-users-total-rate-2');
			var numberUsersTotalRate3 = $('.number-users-total-rate-3');
			var totalMembershipValue = $('.total-membership-value');
			var frmAffiliateRates = $("#frmAffiliateRates input");
			var idFrmBillingData = "#frmBillingData";
			var idFrmCardDetails = "#frmCardDetails";
			var frmBillingData = $(idFrmBillingData + " input");
			var frmCardDetails = $(idFrmCardDetails + " input");
			var formsValidationRequired = "<?= $_SESSION["FORMS_VALIDATION_REQUIRED"] ?>";
			var dataPersonalizePlan = [];
			var dataBillingData = {};
			var dataCardDetails = {};
			var subscriptionFormValidation = true;

			var number_users_rate_basic = $("input[name='number_users_rate_basic']");
			var number_users_rate_1 = $("input[name='number_users_rate_1']");

			var rateNameBasic = $(".rate-name-basic");
	
			new Cleave('#txtCREDIT_CARD_NUMBER', {
				creditCard: true
			});
	
			new Cleave('#txtDATE_EXPIRATION', {
				date: true,
				datePattern: ['m', 'y']
			});
		
			var lastStep = 2;
			previousBtn.hide();
			nextBtn.html('<?= $_SESSION["AFFILIATION_RATE_BTN_START_HERE"] ?>');
			previousBtn.html('<?= $_SESSION["AFFILIATION_RATE_PREVIOUS_BUTTON"] ?>');
			var unitPrice = parseFloat("<?= $user_affiliate_basic_rate ?>");
	
			stepperCompanyUserAffiliation[0].addEventListener('show.bs-stepper', function (event) {
				var indexStep = event.detail.indexStep;
				if (indexStep === 0) {previousBtn.hide(); nextBtn.html('<?= $_SESSION["AFFILIATION_RATE_BTN_START_HERE"] ?>')};
				if (indexStep === 1) {previousBtn.show(); nextBtn.html('<?= $_SESSION["AFFILIATION_RATE_NEXT_BUTTON"] ?>')}
				if (indexStep === lastStep)  {nextBtn.show(); nextBtn.html("<?= $btnText ?>")} else {nextBtn.show()}
				if (indexStep === 3) {nextBtn.hide()} 
	
				//TODO prueba para obtener los datos de un formulario en jquery
				if (indexStep === 3) {
					var datos = getDataSubscription();
					if (datos) {
						console.log(datos)
						$("#btnActivate").click();
					} else { 
						setTimeout(() => {
							stepper.to(indexStep)
						}, 50);
					 }
				}
				
			})
	
			stepperCompanyUserAffiliation[0].addEventListener('shown.bs-stepper', function(event){
				
			});
	
			nextBtn.click(function(event) {
				stepper.next();
			});
	
			previousBtn.click(function() {
				stepper.previous();
			});
	
			$('#divActivateModalAffiliateUsers').on('hidden.bs.modal', function (event) {
				stepper.reset();
			})
		}
		//////////////////////////////////////////////////////////////////////////////

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
			//$("#divActivateModalAffiliateUsers").modal("toggle"); //TODO Quitar al pasar a produccion	
			//TODO Nativapps
			if (action === 'new') {
				
				acceptTermsConditionsId.bootstrapToggle('off');
				nextBtn.prop("disabled", true);
				numberUsersAffiliation.each(function() {
					var min = parseInt($(this).attr('min'));
					$(this).val(min)
					calculateUnitTotal($(this))
				})
				$(idFrmBillingData)[0].reset()
				$(idFrmCardDetails)[0].reset()
			}
			/////////////////////

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
				// TODO Nativapps
				var data = { strModel: datas };
				if (action === "new") {
					var dataSubscription = getDataSubscription();
					data = { strModel: datas, dataSubscription: JSON.stringify(dataSubscription) }
				}
				////////////////////
				var noty;
				$.ajax({
					url: url,
					method: "POST",
					data: data, //TODO Nativapps
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
			//TODO Aca se va a realizar la lógica para afilia tu empresa
			if (action === "new") {
				checkClientIsAffiliationType(datas).then(function(resp) {
					if (resp.is_affiliated_client) {
						$('#acceptTermsConditions').prop('checked', false);
						$("#divActivateModalAffiliateUsers").modal("toggle");
						var data = resp.data;

						$("#business_name").val(data.CLIENT_NAME);
						$("#client_id").val(data.ID);
						$("#nit").val(data.IDENTIFICATION);
						$("#main_phone").val(data.CELLPHONE);
						$("#main_address").val(data.ADDRESS);
						$("#legal_representative").val(data.LEGAL_REPRESENTATIVE);

					} else {
						$("#divActivateModal").modal("toggle");
					}
				}).catch(function(error) {

				});
			} else {
				$("#divActivateModal").modal("toggle");
			}
			////////////////////
		});

		//TODO Nativapps

		$("#txtCREDIT_CARD_NUMBER").on("change", function(e) {
			$('#txtCREDIT_CARD_NUMBER').validateCreditCard(function(result) {
				
				var type = result.card_type.name;
				type = (type == "diners") ? "diners-club" : type;
				var icon = "fa fa-cc-" + type;
				$("#icontxtCREDIT_CARD_NUMBER").removeClass().addClass(icon);
				$("#hfValidCard").val(result.valid);
			});
		});

		if (action === 'new') {
			acceptTermsConditionsId.change(function(){
				if ($(this).is(':checked')) {
					nextBtn.prop('disabled', false);
				} else {
					nextBtn.prop('disabled', true);
				}
			});
			numberUsersAffiliation.change(function() {
				var max = parseInt($(this).attr('max'));
				var min = parseInt($(this).attr('min'));
				if ($(this).val() > max)
				{
					$(this).val(max);
				}
				else if ($(this).val() < min)
				{
					$(this).val(min);
				}
				calculateUnitTotal($(this))
			});
			numberUsersAffiliation.on('input', function() {
				calculateUnitTotal($(this))
			});
		}


		function calculateUnitTotal(field) {
			var amount = parseInt(field.val());
			var price = parseInt(field.data('rateValue'))
			var total = amount * price;

			var nameField = field.attr('name')

			switch (nameField) {
				case 'number_users_rate_basic':
					numberUsersTotalBasic.text(total);
					break;
				case 'number_users_rate_1':
					numberUsersTotalRate1.text(total);
					break;
				case 'number_users_rate_2':
					numberUsersTotalRate2.text(total);
					break;
				case 'number_users_rate_3':
					numberUsersTotalRate3.text(total);
					break;
			
				default:
					break;
			}

			calculateTotalPrice();
		}

		function calculateTotalPrice() {
			var quantities = 0;
			var totalValue = 0;
			numberUsersAffiliation.each(function() {
				var price = parseInt($(this).data('rateValue'))
				quantities = parseInt($(this).val()) * price;
				totalValue = totalValue + quantities;
			})
			
			totalMembershipValue.text(totalValue)

			return totalValue;
		}

		function getDataSubscription () {
			dataPersonalizePlan = []
			dataBillingData = {}
			dataCardDetails = {}
			subscriptionFormValidation = true;
			frmAffiliateRates.each(function() {
				var name = $(this).attr('name');
				var quantities = $(this).val();
				var unitValue = $(this).data('rateValue');
				var resourceName = $(this).data('resourceName');
				var data = {};
				data['field'] = name;
				data['quantities'] = quantities;
				data['unit_value'] = unitValue;
				data['resource_name'] = resourceName;
				dataPersonalizePlan.push(data)
			})

			frmBillingData.each(function() {
				var name = $(this).attr('name');
				var is_required = $(this).prop('required');
				var value = $(this).val();
				var placeholder = $(this).attr('placeholder')
				//formsValidationRequired
				if (is_required && value === '') {
					subscriptionFormValidation = false;
					notify("", "danger", "", formsValidationRequired.replace(":attribute", placeholder), "");
				}
				dataBillingData[name] = value;
			})
			
			frmCardDetails.each(function() {
				var name = $(this).attr('name');
				var is_required = $(this).prop('required');
				var value = $(this).val();
				var placeholder = $(this).attr('placeholder')
				//formsValidationRequired
				if (is_required && value === '') {
					subscriptionFormValidation = false;
					notify("", "danger", "", formsValidationRequired.replace(":attribute", placeholder), "");
				}
				dataCardDetails[name] = value;
			})

			var totalSubscription = calculateTotalPrice();

			if (subscriptionFormValidation) {
				return { dataPersonalizePlan, dataBillingData, dataCardDetails, totalSubscription }
			}
			return subscriptionFormValidation;

		}
		
		///////////////////

		$("#cbAccess").trigger("change");

		//TODO Nativapps
		function checkClientIsAffiliationType(data) {
			return new Promise(function(resolve, reject) {
				var noty;
				$.ajax({
					url: 'core/actions/_load/__checkClientIsAffiliationType.php',
					method: "POST",
					data: data,
					contentType: 'application/json',
					beforeSend: function (xhrObj) {
						
						// var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_PROCESSING"] ?>";
						// noty = notify("", "dark", "", message, "", false);												
					},
					success:function(response){
						console.log(response.client_type, <?= $max_users_affiliation_basic_rate ?>)
						client_type = response.client_type;
						if (client_type === 'client') {
							number_users_rate_basic.prop('readonly', true);							
							number_users_rate_1.prop('readonly', false);

							number_users_rate_basic.prop('max', <?= $max_users_affiliation_basic_rate ?>);
							number_users_rate_1.prop('max', <?= $max_users_affiliation_allied_company ?>);

							rateNameBasic.text('<?= $_SESSION["AFFILIATION_RATE_NAME_BASIC"] ?>');
							number_users_rate_basic.attr('data-resource-name', 'AFFILIATION_RATE_NAME_BASIC');
						} else {
							number_users_rate_basic.prop('readonly', false);							
							number_users_rate_1.prop('readonly', true);
							
							number_users_rate_basic.prop('max', <?= $max_users_affiliation_allied_company ?>);
							number_users_rate_1.prop('max', <?= $max_users_affiliation_basic_rate ?>);

							rateNameBasic.text('<?= $_SESSION["AFFILIATION_RATE_NAME_BASIC_2"] ?>');
							number_users_rate_basic.attr('data-resource-name', 'AFFILIATION_RATE_NAME_BASIC_2');
						}
						resolve(response)
					},error: function(xhr, status, error) {
						reject(error)
					}
				});
			})
		}
		///////////////
	});

	
    </script>
<?
	include("core/templates/__mapModal.tpl");
	include("core/templates/__messages.tpl");
	
	// error_log(date('d.m.Y h:i:s') . " - " . json_encode($_SESSION) . PHP_EOL, 3, 'my-errors.log');	
?>
</body>
</html>
