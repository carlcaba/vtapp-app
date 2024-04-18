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

	_error_log("Validate start at " . date("Y-m-d h:i:s"));

	//Instancia las clases necesarias
	$inter = new interfaces();
	$conf = new configuration("SITE_ROOT");

	//Carga los valores de la configuraci�n
	$site_root = $conf->verifyValue();
	
    include("__load-resources.php");
	
	//Variable del codigo
	$link = "";
	$datas = "";
	$fbuser = false;
	$fbid = "";
	$fbemail = "";
	$result = array('success' => false,
					'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					'change' => false,
					'link' => $link);
	
	//Captura las variables
	if($_SERVER['REQUEST_METHOD'] != 'PUT') {
		if(!isset($_POST['txtUser'])) {
			if(!isset($_GET['txtUser'])) {
				//Termina
				exit(json_encode($result));
			}
			else {
				$user = $_GET['txtUser'];
				$pass = $_GET['txtPassword'];
				$link = $_GET['hfLink'];
				$datas = $_GET["hfIPData"];
				$fbid = $_GET["hfFBID"];
				$fbuser = $_GET["hfIsFB"];
				if(isset($_GET['hfFBMail']))
					$fbemail = $_GET["hfFBMail"];
			}
		}
		else {
			$user = $_POST['txtUser'];
			$pass = $_POST['txtPassword'];
			$link = $_POST['hfLink'];
			$datas = $_POST["hfIPData"];
			$fbid = $_POST["hfFBID"];
			$fbuser = $_POST["hfIsFB"];
			if(isset($_POST['hfFBMail']))
				$fbemail = $_POST["hfFBMail"];
		}
	} 
	else {
		//Captura las variables
		parse_str(file_get_contents("php://input"),$vars);
		$user = $vars['txtUser'];
		$pass = $vars['txtPassword'];
		$link = $vars['hfLink'];
		$datas = $vars["hfIPData"];
		$fbid = $vars["hfFBID"];
		$fbuser = $vars["hfIsFB"];
		if(isset($vars['hfFBMail']))
			$fbemail = $vars["hfFBMail"];
	}
	
	//Verifica la informacion
	if(empty($user)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["USERNAME_EMPTY"];
		//Termina
		exit(json_encode($result));
	}
	if(empty($pass)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["PASSWORD_EMPTY"];
		//Termina
		exit(json_encode($result));
	}
	
	//Instancia la clase usuario
	$usua = new users($user);

	//Si es usuario de facebook
	if($fbuser) {
		$usua->FACEBOOK_USER = $fbid;
	}
	//Asigna los valores
	$usua->THE_PASSWORD = $pass;
		
	//Valida la contrase�a
	$usua->check();
	
	//Si hay error
	if($usua->nerror > 0) {
		//Confirma mensaje al usuario
		$result['message'] = $usua->error;
		//Termina
		exit(json_encode($result));
	}
	
	$ipdata = json_decode($datas);
	
	//Elimina mensajes anteriores
	unset($_SESSION["vtappcorp_user_alert"]);
	//Guarda las variables de sesion
	$_SESSION["vtappcorp_user_message"] = $_SESSION["USER_PASSWORD_OK"];
	$_SESSION['vtappcorp_username'] = $usua->FIRST_NAME . " " . $usua->LAST_NAME;
	$_SESSION['vtappcorp_userid'] = $usua->ID;
    $_SESSION['vtappcorp_useraccessid'] = $usua->ACCESS_ID;
	$_SESSION["vtappcorp_facebook_user"] = $fbid;
	$_SESSION["vtappcorp_appname"] = $usua->access->APP_TITLE;
	$_SESSION["vtappcorp_skin"] = "bg-white navbar-light ,sidebar-dark-primary,";
    $_SESSION['vtappcorp_useraccess'] = $usua->access->PREFIX;
    $_SESSION['vtappcorp_referenceid'] = $usua->REFERENCE;
    $_SESSION['vtappcorp_location'] = $ipdata->lat . "," . $ipdata->lon;
	if (!defined('LANGUAGE')) {
		define("LANGUAGE", 2);
	}
	$_SESSION["LANGUAGE"] = 2;
	$link = ($link != "") ? $link : $usua->access->LINK_TO;

	//Actualiza la hora de acceso
	$inter->updateLastAccess();
	
	//Verifica el acceso del usuario
	if ($usua->REFERENCE != "") {
		if (substr($usua->access->PREFIX,0,2) == "CL") {
			require_once("classes/partner_client.php");
			$clie = new partner_client();
			$clie->setClient($usua->REFERENCE);
			$clie->getInformationByClient();
			if ($clie->nerror == 0 && $clie->partner->SKIN != "") {
				$_SESSION["vtappcorp_skin"] = $clie->partner->SKIN;
			}
			else {
				$_SESSION["vtappcorp_lastsql"] = $clie->sql;
			}
		}
		else if(substr($usua->access->PREFIX,0,2) == "AL") {
			require_once("classes/partner.php");
			$part = new partner();
			$part->ID = $usua->REFERENCE;
			$part->__getInformation();
			if ($part->nerror == 0 && $part->SKIN != "") {
				$_SESSION["vtappcorp_skin"] = $part->SKIN;
			}
			else {
				$_SESSION["vtappcorp_lastsql"] = $part->sql;
			}
		}
	}

	//Crea el nuevo LOG
	$log = new logs("LOGIN");
	$log->USER_ID = $user;
	//Adiciona la transaccion
	$log->Login($datas);
	//Si hubo error
	if($log->nerror > 0)
		//Confirma al usuario
		$result['message'] = $log->error;
		
	$result['sql'] = $log->sql;
	
	$usua->setOnline(true);
	
	//Cambia el resultado
	$result['success'] = true;
	$result['message'] = $_SESSION["vtappcorp_user_message"];
	
	//Si el usuario debe cambiar la contrase�a
	if($usua->THE_PASSWORD == $conf->verifyValue("INIT_PASSWORD") || $usua->THE_PASSWORD == $usua->ID || $usua->CHANGE_PASSWORD == 1) {
		//Confirma al usuario
		$result['change'] = true;
		$result['message'] = $_SESSION["CHANGE_PASSWORD_REQUIRED"];
		//Redirecciona
		$result['link'] = $site_root . "change-password.php?txtUser=" . $usua->ID . "&hfLink=" . $site_root . $link;
	}
	else {
		//Si hay algun link	
		$result['link'] = $site_root . $link;
	}

	_error_log("Validate finishes at " . date("Y-m-d h:i:s"));
	
	//Termina
	exit(json_encode($result));

?>