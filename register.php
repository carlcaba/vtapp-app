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

	include_once("core/classes/interfaces.php");
	include_once("core/classes/resources.php");
		
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
	
</head>


<body class="hold-transition register-page">
	<div class="register-box">
		<div class="register-logo">
			<a href="index.php"><img src="img/logo/logo_app.png" /></a>
		</div>
		<!-- /.login-logo -->
		<div class="card">
			<div class="card-body register-card-body register-person">
				<p class="login-box-msg"><?= $_SESSION["REGISTER_TEXT"] ?></p>
				<form id="frmRegister" name="frmRegister" method="POST" action="complete-register.php">
					<div class="form-group has-feedback">
						<input id="chkUserType" name="chkUserType" type="checkbox" class="form-control" checked data-toggle="toggle" data-on="<?= $_SESSION["REGISTER_PERSON"] ?>" data-off="<?= $_SESSION["REGISTER_ENTERPRISE"] ?>" data-onstyle="primary" data-offstyle="success" data-width="100%" />
					</div>
					<div id="divPerson">
						<div class="form-group">
							<div class="input-group has-feedback">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-user"></i></span>
								</div>
								<input type="text" class="form-control" id="txtUser" name="txtUser" placeholder="<?= $_SESSION["PLACEHOLDER_USER"] ?>" required autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />						
							</div>
						</div>
						<div class="form-group">
							<div class="input-group has-feedback">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-envelope"></i></span>
								</div>
								<input type="text" class="form-control" id="txtEmail" name="txtEmail" placeholder="<?= $_SESSION["EMAIL"] ?>" required autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />						
							</div>
						</div>
						<div class="form-group">
							<div class="input-group has-feedback">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-unlock-alt"></i></span>
								</div>
								<input type="password" name="txtPassword" id="txtPassword" class="form-control" placeholder="<?= $_SESSION["PLACEHOLDER_PASSWORD"] ?>" required autocomplete="off" />
							</div>
						</div>
						<div class="form-group">
							<div class="input-group has-feedback">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-lock"></i></span>
								</div>
								<input type="password" name="txtConfirmPassword" id="txtConfirmPassword" class="form-control" placeholder="<?= $_SESSION["CONFIRM_PASSWORD"] ?>" required autocomplete="off" />
							</div>
						</div>
						<div class="form-group">
							<div class="checkbox icheck">
								<label>
									<input type="checkbox" id="chkTerms" name="chkTerms" class="checkBox"> <?= $_SESSION["TERMS"] ?>
								</label>
							</div>
						</div>
						<div class="form-group">
							<button type="button" id="btnRegister" name="btnRegister" class="btn btn-primary" onclick="submitForm();"><?= $_SESSION["REGISTER"] ?></button>
						</div>
						<!--
						<div class="social-auth-links text-center mb-3">
							<p>- O -</p>
							<a href="#" onclick="fbAsyncInit();" class="btn btn-block btn-primary">
								<i class="fa fa-facebook mr-2"></i> <?= $_SESSION["REGISTER_WITH_FACEBOOK"] ?>
								<input type="hidden" name="hfFBID" id="hfFBID" value="" />
								<input type="hidden" name="hfFBMail" id="hfFBMail" value="" />
								<input type="hidden" name="hfFBFirstName" id="hfFBFirstName" value="" />
								<input type="hidden" name="hfFBLastName" id="hfFBLastName" value="" />
								<input type="hidden" name="hfFBCity" id="hfFBCity" value="" />
								<input type="hidden" name="hfFBAddress" id="hfFBAddress" value="" />
							</a>
							<a href="#" class="btn btn-block btn-danger">
								<i class="fa fa-google-plus mr-2"></i> <?= $_SESSION["REGISTER_WITH_GOOGLE"] ?>
							</a>
						</div>
						-->
					</div>
					<div id="divEnterprise" style="display: none;">
						<div class="form-group">
							<div class="input-group has-feedback">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-building"></i></span>
								</div>
								<input type="text" class="form-control" id="txtCompany" name="txtCompany" change2Upper placeholder="<?= $_SESSION["PLACEHOLDER_COMPANY"] ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />						
							</div>
						</div>
						<div class="form-group">
							<div class="input-group has-feedback">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-envelope"></i></span>
								</div>
								<input type="text" class="form-control" id="txtCompanyEmail" name="txtCompanyEmail" placeholder="<?= $_SESSION["EMAIL"] ?>" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />						
							</div>
						</div>
						<div class="form-group">
							<div class="input-group has-feedback">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-unlock-alt"></i></span>
								</div>
								<input type="password" name="txtPasswordCompany" id="txtPasswordCompany" class="form-control" placeholder="<?= $_SESSION["PLACEHOLDER_PASSWORD"] ?>" autocomplete="off" />
							</div>
						</div>
						<div class="form-group">
							<div class="input-group has-feedback">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-lock"></i></span>
								</div>
								<input type="password" name="txtConfirmPasswordCompany" id="txtConfirmPasswordCompany" class="form-control" placeholder="<?= $_SESSION["CONFIRM_PASSWORD"] ?>" autocomplete="off" />
							</div>
						</div>
						<div class="form-group">
							<div class="checkbox icheck">
								<label>
									<input type="checkbox" id="chkTermsCompany" name="chkTermsCompany" class="checkBox"> <?= $_SESSION["TERMS"] ?>
								</label>
							</div>
						</div>
						<div class="form-group">
							<button type="button" id="btnRegisterCompany" name="btnRegisterCompany" class="btn btn-success" onclick="submitForm();"><?= $_SESSION["REGISTER"] ?></button>
						</div>
					</div>				
					<input type="hidden" id="hfIPData" name="hfIPData" value="" />
					<input type="hidden" id="ref" name="ref" value="" />
				</form>
				<p class="mb-0">
					<a href="index.php" class="text-center"><?= $_SESSION["MEMBER"] ?></a>
				</p>
			</div>
			<!-- /.login-card-body -->
		</div>
	</div>
	<!-- /.login-box -->
	<!-- jQuery -->
	<script src="plugins/jquery/jquery.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- iCheck -->
	<script src="plugins/iCheck/icheck.min.js"></script>
	<!-- bootstrap toogle -->
	<script src="plugins/bootstrap-toggle/js/bootstrap-toggle.min.js"></script>	
	
	<script>
		$(function () {
			$("#txtUser").on('input', function(e) {
				console.log("txtUser change!");
				$("#hfFBID").val($(this).val());
			});
			$("#txtEmail").on('input', function(e) {
				console.log("txtEmail change!");
				$("#hfFBMail").val($(this).val());
			});
			$('#chkUserType').change(function() {
				var state = $(this).prop("checked");
				if(state) {
					$("#divEnterprise").hide("slow", function() {
						$("#divPerson").show("fast");
					});
					document.getElementById("txtCompany").removeAttribute("required");
					document.getElementById("txtCompanyEmail").removeAttribute("required");					
					document.getElementById("txtPasswordCompany").removeAttribute("required");					
					document.getElementById("txtConfirmPasswordCompany").removeAttribute("required");					
					document.getElementById("txtUser").setAttribute("required", "");
					document.getElementById("txtEmail").setAttribute("required", "");					
					document.getElementById("txtPassword").setAttribute("required", "");					
					document.getElementById("txtConfirmPassword").setAttribute("required", "");					
				}
				else {
					$("#divPerson").hide("slow", function() {
						$("#divEnterprise").show("fast");
					});
					document.getElementById("txtUser").removeAttribute("required");
					document.getElementById("txtEmail").removeAttribute("required");					
					document.getElementById("txtPassword").removeAttribute("required");					
					document.getElementById("txtConfirmPassword").removeAttribute("required");					
					document.getElementById("txtCompany").setAttribute("required", "");
					document.getElementById("txtCompanyEmail").setAttribute("required", "");					
					document.getElementById("txtPasswordCompany").setAttribute("required", "");					
					document.getElementById("txtConfirmPasswordCompany").setAttribute("required", "");					
				}
			})
			$('.checkBox').iCheck({
				checkboxClass: 'icheckbox_square-blue',
				radioClass   : 'iradio_square-blue',
				increaseArea : '20%' // optional
			});
			ipLookUp();
			$("#txtUser").focus();
		});
		function submitForm() {
			var form = document.getElementById('frmRegister');
			if($("#txtPassword").attr("disabled") != "disabled") {
				if (form.checkValidity() === false) {
					window.event.preventDefault();
					window.event.stopPropagation();
					var curInputs = form.elements;
					var placeHolder;
					for (var i = 0; i < curInputs.length; i++) {
						if (!curInputs[i].validity.valid) {
							isValid = false;
							$(curInputs[i]).closest(".form-group").addClass("has-error");
							placeHolder = curInputs[i].placeholder;
						}
					}					
					notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?> (" + placeHolder + ")" , "");
					return false;
				}
			}
			$("#ref").val($("#chkUserType").prop("checked") ? "users" : "client");
			var $frm = $("#frmRegister");
			$frm.submit();
		};
		function ipLookUp () {
			$.ajax('http://ip-api.com/json')
				.then(
				function success(response) {
					$("#hfIPData").val(JSON.stringify(response));
				},
				function fail(data, status) {
					$("#hfIPData").val("");
				}
			);
		}
	</script>
<?
	include("core/templates/__messages.tpl");
	$parent = "register";
	include("core/templates/__facebook.tpl");
?>
</body>
</html>
