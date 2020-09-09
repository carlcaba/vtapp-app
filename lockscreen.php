<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	//Verifica si debe redireccionarse a otro script
	if(empty($_GET['ref'])) {
		$link = "";
		$username = "Unidentified user";	
	}
	else {
		$link = $_GET['ref'];	
		$username = $_GET['usr'];	
	}
	
    $user = new users($username);
    $user->__getInformation();
	
	$_SESSION["vtappcorp_user_message"] = $_SESSION["SESSION_ENDED_LOCK"];		
	
?>
<!DOCTYPE html>
<html>
<head>
<?
	include("core/templates/__header.tpl");
?>
</head>
<body class="hold-transition lockscreen">
	<!-- Automatic element centering -->
	<div class="lockscreen-wrapper">
		<div class="lockscreen-logo">
			<a href="index.php"><img src="img/logo/logo_app.png"></a>
		</div>
		<!-- User name -->
		<div class="lockscreen-name"><?= $user->getFullName() ?></div>
		<!-- START LOCK SCREEN ITEM -->
		<div class="lockscreen-item">
			<!-- lockscreen image -->
			<div class="lockscreen-image">
				<img src="<?= $user->getUserPicture() ?>" alt="<?= $user->ID ?>">
			</div>
			<!-- /.lockscreen-image -->
			<!-- lockscreen credentials (contains the form) -->
			<form class="lockscreen-credentials" id="frmLockScreen" name="frmLockScreen" method="POST" novalidate>			
				<div class="input-group">
					<input type="password" name="txtPassword" id="txtPassword" class="form-control" placeholder="<?= $_SESSION["PLACEHOLDER_PASSWORD"] ?>" required autocomplete="off" />
					<input type="hidden" id="hfLink" name="hfLink" value="<?= $link ?>" />					
					<input type="hidden" id="txtUser" name="txtUser" value="<?= $user->ID ?>" />					
					<div class="input-group-append" style="margin-left: 0px !important;">
						<button type="button" class="btn" id="btnLockScreen" name="btnLockScreen" onclick="submitForm();"><i class="fa fa-arrow-right text-muted"></i></button>
					</div>
				</div>
			</form>
			<!-- /.lockscreen credentials -->
		</div>
		<!-- /.lockscreen-item -->
		<div class="help-block text-center">
			<?= $_SESSION["LOCK_SCREEN_TEXT"] ?>
		</div>
		<div class="text-center">
			<a href="index.php"><?= $_SESSION["OR_SIGN_IN"] ?></a>
		</div>
		<div class="lockscreen-footer text-center">
			Copyright &copy; 2019 <b><a href="http://www.vtapp.com" class="text-black" target="_blank">VtappCorp</a></b><br>
			All rights reserved
		</div>
	</div>
	<!-- /.center -->

	<!-- jQuery -->
	<script src="plugins/jquery/jquery.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script>
		$(function () {
			$("#txtPassword").focus();
		});
		function submitForm() {
			var form = document.getElementById('frmLockScreen');
			var noty;
			if (form.checkValidity() === false) {
				window.event.preventDefault();
				window.event.stopPropagation();
				notify("", "danger", "", "<?= $_SESSION["ERRORS_ON_INFORMATION"] ?>", "");
				$("#txtPassword").focus();
				return false;
			}
			$.ajax({
				url: 'core/__validate.php',
				type: 'POST',
				data: { 
					txtUser: $("#txtUser").val(),
					txtPassword: $("#txtPassword").val(),
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
						location.href = obj.link;
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
