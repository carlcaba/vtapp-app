<?
	//Inicio de sesion
	session_name('paramed_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=ISO-8859-1');		
	
	//incluye las clases necesarias
	require_once("classes/usuario.php");
	require_once("classes/interfaz.php");
	require_once("classes/configuration.php");
	
	//Instancia las clases necesarias
	$inter = new interfaz();
	$conf = new configuracion("SITE_ROOT");
	//Carga los valores de la configuración
	$site_root = $conf->verifyValue();
	
	$conf = new configuracion("SERVER_MODE");
	$server_mode = $conf->verifyValue();
	
	//Captura las variables
	if(!isset($_POST['txEmail'])) {
		if(!isset($_GET['txEmail'])) {
			//Confirma mensaje al usuario
			$_SESSION['paramed_user_alert'] = "No ha ingresado informaci&oacute;n para validar";
			//Redirecciona
			$inter->redirect($site_root);
		}
		else {
			$email = $_GET['txEmail'];
			$texto = $_GET['txCaptcha'];
			$salto = $_GET['txCaptchaHash'];
		}
	}
	else {
		$email = $_POST['txEmail'];
		$texto = $_POST['txCaptcha'];
		$salto = $_POST['txCaptchaHash'];
	}

	//Instancia la clase usuario
	$usua = new usuario();
	//Asigna los valores
	$usua->EMAIL = $email;
	
	//Verifica el usuario
	$usua->getInfoByMail();
	
	$link = $site_root . "remember-password.php";
	
	//Si hay error
	if($usua->nerror > 0) {
		//Confirma mensaje al usuario
		$_SESSION['paramed_user_alert'] = $usua->error;
	}
	else if(rpHash($texto) != $salto) {
		//Confirma mensaje al usuario
		$_SESSION['paramed_user_alert'] = "El texto ingresado no coincide (" . $conf->verifyValue("SERVER_MODE") . " + " . rpHash($texto) . " != $salto)";
	}
	else if($chng = $usua->rememberPassword() > 0) {
		//Si hubo un problema con el correo electronico
		if($chng == 18)
			//cambia el link
			$link = $site_root;
		//Confirma mensaje al usuario
		$_SESSION['paramed_user_alert'] = $usua->error;
	}
	else {
		//cambia el link
		$link = $site_root;
		//Confirma mensaje al usuario
		$_SESSION['paramed_user_alert'] = "Se ha enviado un mensaje al correo electr&oacute;nico <i>$email</i> con los datos de la cuenta";
	}
	//Redirecciona
	$inter->redirect($link);

?>