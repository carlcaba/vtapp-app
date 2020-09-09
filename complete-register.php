<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");
	require_once("core/classes/configuration.php");
	
	$conf = new configuration("WEB_SITE");
	$website = $conf->verifyValue();
	$conf = new configuration("SITE_ROOT");
	$siteroot = $conf->verifyValue();
	$conf = new configuration("RECAPTCHA_API_KEY");
	$captcha_key = $conf->verifyValue();
	$conf = new configuration("RECAPTCHA_URL");
	$captcha_url = $conf->verifyValue();
	
	include_once("core/classes/interfaces.php");
	include_once("core/classes/resources.php");
	
	if(!isset($_POST['hfIPData'])) {
		//Verifica el GET
		if(isset($_GET['hfIPData'])) {
			$data = $_GET;
		}
	}
	else {
		$data = $_POST;
	}
	//Verifica que clase debe cargarse
	if(empty($data['ref']))
		$cname = "users";
	else
		$cname = $data['ref'];	
	
	require_once("core/classes/" . $cname . ".php");
	
	//Asigna la informacion
	$class = new $cname();
	$class->completeResources();
	
	require_once("core/classes/document_type.php");
	$doc_type = new document_type();
			
	$ip_data = json_decode($data["hfIPData"]);
	
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<!-- Meta, title, CSS, favicons, etc. -->
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="apple-touch-icon" sizes="57x57" href="img/logo/icons/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="img/logo/icons/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="img/logo/icons/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="img/logo/icons/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="img/logo/icons/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="img/logo/icons/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="img/logo/icons/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="img/logo/icons/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="img/logo/icons/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192"  href="img/logo/icons/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="img/logo/icons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="img/logo/icons/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="img/logo/icons/favicon-16x16.png">
	<meta name="msapplication-TileImage" content="img/logo/icons/ms-icon-144x144.png">
	
	<title><?= APP_NAME ?></title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Font Awesome -->
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="css/ionicons.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="css/adminlte.min.css">
	<!-- Google Font: Source Sans Pro -->
	<link href="css/fonts.css" rel="stylesheet">
	<!-- iCheck -->
	<link rel="stylesheet" href="plugins/iCheck/square/blue.css">
	<!-- bootstrap toogle -->
	<link rel="stylesheet" href="plugins/bootstrap-toggle/css/bootstrap-toggle.min.css"></link>	
	<!-- form step -->
	<link rel="stylesheet" href="plugins/step-wizard/css/step-wizard.css"></link>	
	
</head>

<body class="hold-transition register-page">
	<div class="complete-register-logo">
		<a href="index.php"><img src="img/logo/logo_app.png" /></a>
	</div>
	<div class="row">
		<div class="col-md-3">&nbsp;</div>
		<div class="col-md-6">
			<div class="card">
				<div class="card-header">
					<div class="stepwizard">
						<div class="stepwizard-row setup-panel">
							<div class="stepwizard-step col-xs-3"> 
								<a href="#step-1" type="button" class="btn btn-success btn-circle">1</a>
								<p><small><?= $_SESSION["COMPLETE_REGISTER_TITLE_1"] ?></small></p>
							</div>
							<div class="stepwizard-step col-xs-3"> 
								<a href="#step-2" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
								<p><small><?= $_SESSION["COMPLETE_REGISTER_TITLE_2"] ?></small></p>
							</div>
							<div class="stepwizard-step col-xs-3"> 
								<a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">3</a>
								<p><small><?= $_SESSION["COMPLETE_REGISTER_TITLE_3"] ?></small></p>
							</div>
						</div>
					</div>
				</div>
				<div class="card-body">
					<form role="form" id="frmCompleteRegister" name="frmCompleteRegister">
<? 
	if($cname == "users") {
?>
						<div class="panel panel-primary setup-content" id="step-1">
							<div class="panel-heading">
								<h3 class="panel-title"><?= $_SESSION["COMPLETE_REGISTER_TITLE_1"] ?></h3>
								<p class="panel-title"><?= $_SESSION["COMPLETE_REGISTER_SUBTITLE_1"] ?></p>
							</div>
							<div class="panel-body">
								<?= $class->showField("FIRST_NAME", "", "fa fa-user", "", false, strtoupper($data["hfFBFirstName"]), false, "9,9,12", ($data["hfFBFirstName"] == "" ? "" : "readonly=\"readonly\"")) ?>
								<?= $class->showField("LAST_NAME", "", "fa fa-user", "", false, strtoupper($data["hfFBLastName"]), false, "9,9,12", ($data["hfFBLastName"] == "" ? "" : "readonly=\"readonly\"")) ?>
								<?= $class->showField("IDENTIFICATION", "", "", "", false, "", false, "9,9,12", "", $doc_type->getDataToForm()) ?>
								<?= $class->showField("CELLPHONE", "", "fa fa-mobile", "", false, "", false, "9,9,12", "") ?>				
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
								<h3 class="panel-title"><?= $_SESSION["COMPLETE_REGISTER_TITLE_2"] ?></h3>
								<p class="panel-title"><?= $_SESSION["COMPLETE_REGISTER_SUBTITLE_2"] ?></p>
							</div>
							<div class="panel-body">
								<?= $class->showField("ADDRESS", "", "", "", false, "", false, "9,9,12", "") ?>
								<?= $class->showField("PHONE", "", "fa fa-phone", "", false, "", false, "9,9,12", "data-custom=\"NotRequired\"") ?>
								<div class="form-group">
									<label><?= $class->arrColComments["CITY_ID"] ?> *</label>
									<select class="form-control" id="cbCity" name="cbCity">
										<?= $class->city->showAllOptionList() ?>
									</select>
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
								<h3 class="panel-title"><?= $_SESSION["COMPLETE_REGISTER_TITLE_3"] ?></h3>
								<p class="panel-title"><?= $_SESSION["COMPLETE_REGISTER_SUBTITLE_3"] ?></p>
							</div>
							<div class="panel-body">
								<?= $class->showField("EMAIL", "", "fa fa-envelope", "", false, $data["hfFBMail"], false, "9,9,12", ($data["hfFBMail"] == "" ? "" : "readonly=\"readonly\"")) ?>
								<?= $class->showField("THE_PASSWORD", "", "fa fa-unlock-alt", "", false, $data["txtPassword"], false, "9,9,12", ($data["txtPassword"] == "" ? "" : "readonly=\"readonly\"")) ?>
								<div class="form-group">
									<label for="txtConfirmPassword"><?= $_SESSION["CONFIRM_PASSWORD"] ?> <span class="required">*</span></label>
									<div class="input-group mb-2"><div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-lock"></i></div></div>
										<input type="password" name="txtConfirmPassword" id="txtConfirmPassword" class="form-control" placeholder="<?= $_SESSION["CONFIRM_PASSWORD"] ?>" required autocomplete="off" />
									</div>
								</div>				
							</div>
							<div class="panel-footer">
								<button type="button" title="<?= $_SESSION["SAVE"] ?>" id="btnSave" name="btnSave" class="btn btn-success pull-right" >
									<i class="fa fa-floppy-o"></i>
									<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"> <?= $_SESSION["SAVE"] ?></span>
								</button>
								<input type="hidden" name="txtUser" id="txtUser" value="<?= $data["txtUser"] ?>" />
								<input type="hidden" name="hfFBID" id="hfFBID" value="<?= $data["hfFBID"] ?>" />
								<input type="hidden" name="hfFBMail" id="hfFBMail" value="<?= $data["hfFBMail"] ?>" />
								<input type="hidden" name="hfFBFirstName" id="hfFBFirstName" value="<?= $data["hfFBFirstName"] ?>" />
								<input type="hidden" name="hfFBLastName" id="hfFBLastName" value="<?= $data["hfFBLastName"] ?>" />
								<input type="hidden" name="hfFBCity" id="hfFBCity" value="<?= $data["hfFBCity"] ?>" />
								<input type="hidden" name="hfFBAddress" id="hfFBAddress" value="<?= $data["hfFBAddress"] ?>" />
								
							</div>
						</div>
<?
	}
	else if($cname == "client") {
?>
						<div class="panel panel-primary setup-content" id="step-1">
							<div class="panel-heading">
								<h3 class="panel-title"><?= $_SESSION["COMPLETE_REGISTER_TITLE_1"] ?></h3>
								<p class="panel-title"><?= $_SESSION["COMPLETE_REGISTER_SUBTITLE_1"] ?></p>
							</div>
							<div class="panel-body">
								<?= $class->showField("CLIENT_NAME", "", "fa fa-user", "", false, strtoupper($data["txtCompany"]), false, "9,9,12", ($data["txtCompany"] == "" ? "" : "readonly=\"readonly\"")) ?>
								<?= $class->showField("IDENTIFICATION", "", "", "", false, "", false, "9,9,12", "", $doc_type->getDataToForm()) ?>
								<?= $class->showField("PHONE", "", "fa fa-phone", "", false, "", false, "9,9,12", "data-custom=\"NotRequired\"") ?>
								<?= $class->showField("CELLPHONE", "", "fa fa-mobile", "", false, "", false, "9,9,12", "") ?>				
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
								<h3 class="panel-title"><?= $_SESSION["COMPLETE_REGISTER_TITLE_2"] ?></h3>
								<p class="panel-title"><?= $_SESSION["COMPLETE_REGISTER_SUBTITLE_2"] ?></p>
							</div>
							<div class="panel-body">
								<?= $class->showField("ADDRESS", "", "", "", false, "", false, "9,9,12", "") ?>
								<?= $class->showField("CELLPHONE_ALT", "", "fa fa-mobile", "", false, "", false, "9,9,12", "") ?>				
								<?= $class->showField("PHONE_ALT", "", "fa fa-phone", "", false, "", false, "9,9,12", "data-custom=\"NotRequired\"") ?>
								<div class="form-group">
									<label><?= $class->arrColComments["CITY_ID"] ?> *</label>
									<select class="form-control" id="cbCity" name="cbCity">
										<?= $class->city->showAllOptionList() ?>
									</select>
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
								<h3 class="panel-title"><?= $_SESSION["COMPLETE_REGISTER_TITLE_3"] ?></h3>
								<p class="panel-title"><?= $_SESSION["COMPLETE_REGISTER_SUBTITLE_3"] ?></p>
							</div>
							<div class="panel-body">
								<?= $class->showField("EMAIL", "", "fa fa-envelope", "", false, $data["txtCompanyEmail"], false, "9,9,12", ($data["txtCompanyEmail"] == "" ? "" : "readonly=\"readonly\"")) ?>
								<?= $class->showField("EMAIL_ALT", "", "fa fa-envelope", "", false, "", false, "9,9,12", "") ?>
								<?= $class->showField("CONTACT_NAME", "", "fa fa-user", "", false, "", false, "9,9,12", "") ?>
								<div class="form-group">
									<label for="txtTHE_PASSWORD"><?= $_SESSION["PASSWORD"] ?> <span class="required">*</span></label>
									<div class="input-group mb-2"><div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-unlock-alt"></i></div></div>
										<input type="password" name="txtTHE_PASSWORD" id="txtTHE_PASSWORD" class="form-control" placeholder="<?= $_SESSION["PLACEHOLDER_PASSWORD"] ?>" required autocomplete="off" value="<?= $data["txtPasswordCompany"] ?>" <?= ($data["txtPasswordCompany"] == "" ? "" : "readonly=\"readonly\"") ?>/>
									</div>
								</div>				
								<div class="form-group">
									<label for="txtConfirmPassword"><?= $_SESSION["CONFIRM_PASSWORD"] ?> <span class="required">*</span></label>
									<div class="input-group mb-2"><div class="input-group-prepend"><div class="input-group-text"><i class="fa fa-lock"></i></div></div>
										<input type="password" name="txtConfirmPassword" id="txtConfirmPassword" class="form-control" placeholder="<?= $_SESSION["CONFIRM_PASSWORD"] ?>" required autocomplete="off" />
									</div>
								</div>				
							</div>
							<div class="panel-footer">
								<button type="button" title="<?= $_SESSION["SAVE"] ?>" id="btnSave" name="btnSave" class="btn btn-success pull-right" >
									<i class="fa fa-floppy-o"></i>
									<span class="d-none d-sm-none d-md-none d-lg-block d-xl-inline-block"> <?= $_SESSION["SAVE"] ?></span>
								</button>
							</div>
						</div>
<?
	}
?>
						<input type="hidden" id="gReCaptchaToken" name="gReCaptchaToken" value="" />
					</form>
				</div>
			</div>
		</div>
		<div class="col-md-3">&nbsp;</div>
	</div>

	<!-- jQuery -->
	<script src="plugins/jquery/jquery.min.js"></script>
	<script src="plugins/jquery/jquery.easing.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- iCheck -->
	<script src="plugins/iCheck/icheck.min.js"></script>
	<!-- bootstrap toogle -->
	<script src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>	
	<!-- form-step -->
	<script src="plugins/step-wizard/js/step-wizard.js"></script>	
	<!-- Resources -->
	<script src="js/resources.js"></script>	
	<!-- reCaptcha -->
	<script src="<?= $captcha_url . $captcha_key ?>"></script>

	<script>
		$(document).ready(function() {
			$("#btnSave").on("click", function(e) {
				var form = document.getElementById('frmCompleteRegister');
				var noty;
				if (form.checkValidity() === false) {
					window.event.preventDefault();
					window.event.stopPropagation();
					notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
					return false;
				}
				if($("#txtTHE_PASSWORD").val() != $("#txtConfirmPassword").val()) {
					notify("", "danger", "", "<?= $_SESSION["PASSWORDS_NOT_EQUAL"] ?>", "");
					$("#txtTHE_PASSWORD").focus();
					return false;
				}
				grecaptcha.ready(function() {
					grecaptcha.execute('<?= $captcha_key ?>', {action: 'home'}).then(function(token) {
						$("#gReCaptchaToken").val(token);
					});
				});			
				var title = "<?= $_SESSION["REGISTER"] . " " . $_SESSION["AS"] . " " . ($cname == "users" ? $_SESSION["USER"] : $_SESSION["CLIENT"]) ?>";
				var url = "core/actions/_save/__completeRegister.php";
				var $frm = $("#frmCompleteRegister");
				var datasObj = $frm.serializeObject();
				datasObj["cbTBL_SYSTEM_USER_IDENTIFICATION"] = $("#hfDocType_" + $("#cbTBL_SYSTEM_USER_IDENTIFICATION").val()).val();
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
						data: { 
							strModel: datas,
							"class": "<?= $cname ?>"
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
			});
		});
	
		function validateForm() {
			var form = document.getElementById('frmCompleteRegister');
			for (var i = 0; i < form.elements.length; i++) {
				var e = form.elements[i];
				if (e.dataset.custom != "NotRequired") {
					if (e.value == "") {
						var placeholder = $("#" + e.id).attr("placeholder");
						notify("", "danger", "", "<?= $_SESSION["MUST_SELECT"] ?>" + placeholder, "");
						e.classList.add('error');
						return false;
					}
				}
				else {
					break;
				}
			}
			return true;
			
		}
		grecaptcha.ready(function() {
			grecaptcha.execute('<?= $captcha_key ?>', {action: 'home'}).then(function(token) {
				$("#gReCaptchaToken").val(token);
			});
		});			
	</script>
<?
	include("core/templates/__modals.tpl");
	include("core/templates/__mapModal.tpl");
	include("core/templates/__messages.tpl");
?>
</body>
</html>
