<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');		
	
	//incluye las clases necesarias
	require_once("classes/resources.php");
	require_once("classes/users.php");
	require_once("classes/interfaces.php");
	require_once("classes/configuration.php");
	
	//Instancia las clases necesarias
	$inter = new interfaces();
	$conf = new configuration("SITE_ROOT");
	//Carga los valores de la configuraci�n
	$site_root = $conf->verifyValue();

	$conf = new configuration("SERVER_MODE");
	$server_mode = $conf->verifyValue();
	
	$link = "";
	$result = array('success' => false,
					'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					'change' => false,
					'link' => $link);
	
	//Captura las variables
	if(!isset($_POST['txtUser'])) {
		if(!isset($_GET['txtUser'])) {
			exit(json_encode($result));
		}
		else {
			$email = $_GET['txtUser'];
			$passw = $_GET['txtOldPassword'];
			$pass1 = $_GET['txtNewPassword'];
			$pass2 = $_GET['txtConfirmPassword'];
			$link2 = $_GET['hfLink'];
			$isemail = $_GET['isEmail'];
		}
	}
	else {
		$email = $_POST['txtUser'];
		$passw = $_POST['txtOldPassword'];
		$pass1 = $_POST['txtNewPassword'];
		$pass2 = $_POST['txtConfirmPassword'];
		$link2 = $_POST['hfLink'];
		$isemail = $_POST['isEmail'];
	}

	//Instancia la clase usuario
	$usua = new users();
	//Asigna los valores
	if($isemail == "true") {
		$usua->EMAIL = $email;
		//Verifica el usuario
		$usua->getInfoByMail();
	}
	else {
		$usua->ID = $email;
		//Verifica el usuario
		$usua->__getInformation();
	}
	
	//Actualiza la hora de acceso
	$inter->updateLastAccess();
	
	//Complementa el link
	$link = $site_root . "change-password.php?txtUser=$email&hfLink=$link2";
	$result['link'] = $link;
	
	//Si hay error
	if($usua->nerror > 0) {
		//Confirma mensaje al usuario
		$result['message'] = $usua->error;
	}
	else if(!$usua->checkUser($passw)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["ERROR_PASSWORD"];
	}
	else if($chng = $usua->changePassword($pass1) > 0){
		//Si hubo un problema con el correo electronico
		if($chng == 18)
			//Complementa el link
			$link = $link2;
		//Confirma mensaje al usuario
		$result['message'] = $usua->error;
		$result['link'] = $link;
        $result["success"] = true;
	}
	else {
		//Complementa el link
		$link = $link2;
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["CHANGE_PASSWORD_SUCCESSFULL"];
		$result['link'] = $link;
		$result["success"] = true;
	}
	
	//Termina
	exit(json_encode($result));
?>