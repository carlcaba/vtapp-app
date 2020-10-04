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
	$conf = new configuration("IP_REQUEST_URL");
	$ipurl = $conf->verifyValue();
	
	include_once("core/classes/interfaces.php");
	include_once("core/classes/resources.php");
		
	//Verifica si debe redireccionarse a otro script
	if(empty($_GET['ref']))
		$link = "";
	else
		$link = $_GET['ref'];	
	
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
	<link rel="stylesheet" href="css/register/adminlte.min.css">
	<!-- iCheck -->
	<link rel="stylesheet" href="plugins/iCheck/square/blue.css">
	<!-- Google Font: Source Sans Pro -->
	<link href="css/register/fonts.css" rel="stylesheet">
</head>
<body class="hold-transition login-page">
	<div class="login-box">
		<div class="login-logo">
			<a href="index.php"><img src="img/logo/logo_app.png" /></a>
		</div>
		<!-- /.login-logo -->
		<div class="card">
			<div class="card-body login-card-body">
				<p class="login-box-msg"><?= $_SESSION["LOGIN_TEXT"] ?></p>
				<form id="frmLogin" name="frmLogin" method="POST" novalidate>			
					<div class="form-group has-feedback">
						<input type="text" class="form-control" id="txtUser" name="txtUser" placeholder="<?= $_SESSION["PLACEHOLDER_USER"] ?>" required autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />						
						<span class="fa fa-user form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" name="txtPassword" id="txtPassword" class="form-control" placeholder="<?= $_SESSION["PLACEHOLDER_PASSWORD"] ?>" required autocomplete="off" />
						<span class="fa fa-lock form-control-feedback"></span>
					</div>
					<div class="row">
						<div class="col-8">
							<div class="checkbox icheck">
								<label>
									<input type="checkbox"> <?= $_SESSION["KEEP_ME_SIGNED"] ?>
								</label>
							</div>
						</div>
						<!-- /.col -->
						<div class="col-4">
							<button type="button" id="btnLogin" name="btnLogin" class="btn btn-primary btn-block btn-flat" onclick="submitForm();"><?= $_SESSION["ENTER_BUTTON"] ?></button>
							<input type="hidden" id="hfLink" name="hfLink" value="<?= $link ?>" />
							<input type="hidden" id="hfIPData" name="hfIPData" value="" />
							<input type="hidden" name="hfFBID" id="hfFBID" value="" />
							<input type="hidden" name="hfFBMail" id="hfFBMail" value="" />
							<input type="hidden" name="hfFBFirstName" id="hfFBFirstName" value="" />
							<input type="hidden" name="hfFBLastName" id="hfFBLastName" value="" />
							<input type="hidden" name="hfFBCity" id="hfFBCity" value="" />
							<input type="hidden" name="hfFBAddress" id="hfFBAddress" value="" />
						</div>
						<!-- /.col -->
					</div>
				</form>
				<!--
				<div class="social-auth-links text-center mb-3">
					<p>- O -</p>
					<a href="#" onclick="fbAsyncInit();" class="btn btn-block btn-primary">
						<i class="fa fa-facebook mr-2"></i> <?= $_SESSION["ENTER_WITH_FACEBOOK"] ?>
					</a>
					<a href="#" class="btn btn-block btn-danger">
						<i class="fa fa-google-plus mr-2"></i> <?= $_SESSION["ENTER_WITH_GOOGLE"] ?>
					</a>
				</div>
				-->
				<p class="mb-1">
					<a href="forgot-password.php"><?= $_SESSION["FORGOT_PASSWORD"] ?></a>
				</p>
				<p class="mb-0">
					<a href="register.php" class="text-center"><?= $_SESSION["REGISTER_MEMBERSHIP"] ?></a>
				</p>
			</div>
			<!-- /.login-card-body -->
		</div>
	</div>
	<!-- /.login-box -->
	
	<!-- Modal -->
	<div class="modal fade" id="modalChangePassword" tabindex="-1" role="dialog" aria-labelledby="h5ModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="h5ModalLabel"><?= $_SESSION["INFORMATION"] ?></h5>
				</div>
				<div class="modal-body" id="divBodyModalChangePassword"></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="btnChangePassword" name="btnChangePassword"><?= $_SESSION["ACCEPT"] ?></button>
				</div>
			</div>
		</div>
	</div>	

	<!-- jQuery -->
	<script src="plugins/jquery/jquery.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- iCheck -->
	<script src="plugins/iCheck/icheck.min.js"></script>
	
	<script>
		$(function () {
			$('input').iCheck({
				checkboxClass: 'icheckbox_square-blue',
				radioClass   : 'iradio_square-blue',
				increaseArea : '20%' // optional
			});
			$("#txtUser").keyup(function(event) {
				if(event.keyCode == 13) {
					if($(this).val() == "" || $("#txtPassword").val() == "") {
						notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");							
						return false;
					}
					submitForm();
				}
			});
			$("#txtPassword").keyup(function(event) {
				if(event.keyCode == 13) {
					if($(this).val() == "" || $("#txtUser").val() == "") {
						notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");							
						return false;
					}
					submitForm();
				}
			});
			ipLookUp();
			$("#txtUser").focus();
		});
		function submitForm() {
			var form = document.getElementById('frmLogin');
			var noty;
			if (form.checkValidity() === false) {
				window.event.preventDefault();
				window.event.stopPropagation();
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				return false;
			}
			$.ajax({
				url: 'core/__validate.php',
				type: 'POST',
				data: { 
					txtUser: $("#txtUser").val(),
					txtPassword: $("#txtPassword").val(),
					hfLink: $("#hfLink").val(),
					hfIPData: $("#hfIPData").val(),
					hfFBID: $("#hfFBID").val(),
					hfIsFB: $("#hfFBID").val() != "",
					hfFBEm: $("#hfFBMail").val()
				},
				beforeSend: function (xhrObj) {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_CHECKING"] ?>";
					noty = notify("", "dark", "", message, "", false);												
				},
				error: function (request, status, error) {
					var message = "<?= $_SESSION["AN_ERROR_OCCURRED"] ?> Status: " + status + " Err: " + error;
					notify("", "danger", "", message, "", false);												
				},
				success: function (data) {
					noty.close();
					var obj = eval("(" + data + ")");
					console.log(obj);
					if(!obj.success) {
						notify("", "info", "", obj.message, "");												
					}
					else {
						if(!obj.change) {
							location.href = obj.link;
						}
						else {
							$("#divBodyModalChangePassword").html(obj.message);
							$('#btnChangePassword').on('click', function (e) {
								location.href = obj.link;
							});
							$('#modalChangePassword').modal('toggle');
						}
					}
				}
			});
		};
		function ipLookUp () {
			var settings = {
				"crossDomain": true,
				"url": "<?= $ipurl ?>",
				"success": function(response) {
					$("#hfIPData").val(JSON.stringify(response));
				},
				"fail": function fail(data, status) {
					$("#hfIPData").val("");
				}
			};
			$.ajax(settings).done();
		}
	</script>
<?
	include("core/templates/__messages.tpl");
	$parent = "index";
	include("core/templates/__facebook.tpl");
?>
</body>
</html>
