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
		
	//Verifica si debe redireccionarse a otro script
	if(empty($_GET['ref']))
		$link = "index.php";
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
	<link rel="stylesheet" href="css/adminlte.min.css">
	<!-- Google Font: Source Sans Pro -->
	<link href="css/fonts.css" rel="stylesheet">
</head>
<body class="hold-transition login-page">
	<div class="login-box">
		<div class="login-logo">
			<a href="index.php"><img src="img/logo/logo_app.png" /></a>
		</div>
		<!-- /.login-logo -->
		<div class="card">
			<div class="card-body login-card-body">
				<p class="login-box-msg"><?= $_SESSION["RESET_PASSWORD_TEXT"] ?></p>
				<form id="frmReset" name="frmReset" method="POST" novalidate>			
					<div class="form-group has-feedback">
						<input type="email" class="form-control" id="txtEmail" name="txtEmail" placeholder="<?= $_SESSION["PLACEHOLDER_EMAIL"] ?>" required autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" />						
						<span class="fa fa-user form-control-feedback"></span>
					</div>
					<div class="row">
						<div class="col-12">
							<button type="button" id="btnReset" name="btnReset" class="btn btn-primary btn-block btn-flat" onclick="submitForm();"><?= $_SESSION["RESET_PASSWORD"] ?></button>
							<input type="hidden" id="hfLink" name="hfLink" value="<?= $link ?>" />
						</div>
						<!-- /.col -->
					</div>
				</form>
			</div>
			<!-- /.login-card-body -->
		</div>
	</div>
	<!-- /.login-box -->
	
	<!-- jQuery -->
	<script src="plugins/jquery/jquery.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	
	<script>
		$(function () {
			$("#txtEmail").keyup(function(event) {
				if(event.keyCode == 13) {
					if($(this).val() == "") {
						notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");							
						return false;
					}
					submitForm();
				}
			});
			$("#txtEmail").focus();
		});
		function submitForm() {
			var form = document.getElementById('frmReset');
			var noty;
			if (form.checkValidity() === false) {
				window.event.preventDefault();
				window.event.stopPropagation();
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				return false;
			}
			$.ajax({
				url: 'core/__validate-forgot.php',
				type: 'POST',
				data: { 
					txtUser: $("#txtEmail").val(),
					hfLink: $("#hfLink").val()
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
					if(obj.success) {
						notify("", "info", "", obj.message, "");
						setTimeout(function(){ location.href = obj.link; }, 4000);
					}
					else 
						notify("", "danger", "", obj.message, "");
				}
			});
		};
	</script>
<?
	include("core/templates/__messages.tpl");
?>
</body>
</html>
