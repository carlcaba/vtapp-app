<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");
    require_once("core/classes/interfaces.php");

    $inter = new interfaces();

    //Define el menu
    $_SESSION["menu_id"] = $inter->getMenuId(basename(__FILE__));
	
	//Incluye verificar la sesion
	include("core/__check-session.php");
	
	//Clases requeridas
	require_once("core/classes/configuration.php");
	
	//Instancia las clases
	$conf = new configuration("PASSWORD_MIN_LEN");
	$minlen = $conf->verifyValue();
	
	//Determina si debe leer o no el correo
	$readonly = "readonly";
	$email = "";
	$link = "index.php";
	$focus = "txtOldPassword";
	
	//Captura las variables
	if(!isset($_POST['txtUser'])) {
		if(!isset($_GET['txtUser'])) {
			$readonly = "";
			$focus = "txtUser";
		}
		else {
			$email = $_GET['txtUser'];
			$link = $_GET['hfLink'];
		}
	}
	else {
		$email = $_POST['txtUser'];
		$link = $_POST['hfLink'];
	}
	
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
	<!-- iCheck -->
	<link rel="stylesheet" href="plugins/iCheck/square/blue.css">
	<!-- Google Font: Source Sans Pro -->
	<link href="css/fonts.css" rel="stylesheet">
</head>
<body class="hold-transition register-page">
	<div class="register-box">
		<div class="register-logo">
			<a href="index.php"><img src="img/logo/logo_app.png" /></a>
		</div>
		<div class="card">
			<div class="card-body register-card-body">
				<p class="login-box-msg"><?= $_SESSION["CHANGE_PASSWORD"] ?></p>
				<form id="frmChangePassword" name="frmChangePassword" method="POST" novalidate>			
					<div class="form-group has-feedback">
						<input type="text" class="form-control" id="txtUser" name="txtUser" placeholder="<?= $_SESSION["PLACEHOLDER_USER"] ?>" required autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" <?= $readonly ?> value="<?= $email ?>"/>
						<span class="fa fa-user form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" name="txtOldPassword" id="txtOldPassword" class="form-control" placeholder="<?= $_SESSION["OLD_PASSWORD"] ?>" required autocomplete="off" />
						<span class="fa fa-lock form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" name="txtNewPassword" id="txtNewPassword" class="form-control" placeholder="<?= $_SESSION["NEW_PASSWORD"] ?>" required autocomplete="off" />
						<span class="fa fa-lock form-control-feedback"></span>
					</div>
					<div class="form-group has-feedback">
						<input type="password" name="txtConfirmPassword" id="txtConfirmPassword" class="form-control" placeholder="<?= $_SESSION["CONFIRM_PASSWORD"] ?>" autocomplete="off" />
						<span class="fa fa-lock form-control-feedback"></span>
					</div>
					<div class="row">
						<div class="col-8">
							<div class="checkbox icheck">
								<label>
									<input type="checkbox"> <?= $_SESSION["SEND_EMAIL"] ?></a>
								</label>
							</div>
						</div>
						<!-- /.col -->
						<div class="col-4">
							<button type="button" class="btn btn-primary btn-block btn-flat" id="btnChangePassword" name="btnChangePassword" onclick="submitForm();"><?= $_SESSION["UPDATE_PASSWORD"] ?></button>
							<input type="hidden" name="hfLink" id="hfLink" value="<?= $link ?>" />
							<input type="hidden" name="isEmail" id="isEmail" value="false" />
						</div>
						<!-- /.col -->
					</div>
				</form>
				<a href="<?= $link ?>" class="text-center"><?= ($link == "") ? $_SESSION["ENTER_BUTTON"] : $_SESSION["CANCEL"] ?></a>
			</div>
			<!-- /.form-box -->
		</div><!-- /.card -->
	</div>
	<!-- /.register-box -->
	
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
			$("#txtUser").on("change", function(e) {
				var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);					
				$("#isEmail").val(pattern.test($(this).val()));
			});
			$("#<?= $focus ?>").focus();			
		});
		function submitForm() {
			var form = document.getElementById('frmChangePassword');
			var noty;
			if (form.checkValidity() === false) {
				window.event.preventDefault();
				window.event.stopPropagation();
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				return false;
			}
			if($('#txtNewPassword').val() != $('#txtConfirmPassword').val()){
				notify("", "danger", "", "<?= $_SESSION["PASSWORDS_NOT_EQUAL"] ?>", "");
				$("#txtNewPassword").focus();
				return false;
			}				
			var noty;
			$.ajax({
				url: 'core/__change-password.php',
				type: 'POST',
				data: { 
					txtUser: $("#txtUser").val(),
					txtOldPassword: $("#txtOldPassword").val(),
					txtNewPassword: $("#txtNewPassword").val(),
					txtConfirmPassword: $("#txtConfirmPassword").val(),
					isEmail: $("#isEmail").val(),
					hfLink: $("#hfLink").val()
				},
				beforeSend: function (xhrObj) {
					var message = "<i class=\"fa fa-refresh fa-spin\"></i> <?= $_SESSION["MSG_CHECKING"] ?>";
					noty = notify("", "dark", "", message, "", false);												
				},
				error: function () {
					var message = "<?= $_SESSION["AN_ERROR_OCCURRED"] ?>";
					notify("", "danger", "", message, "", false);												
				},
				success: function (data) {
					noty.close();
					var obj = eval("(" + data + ")");
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
		
	</script>
<?
	include("core/templates/__messages.tpl");
?>
</body>
</html>
