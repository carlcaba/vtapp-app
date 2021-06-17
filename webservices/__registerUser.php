<?
	//Web service que registra un usuario desde portal
	//LOGICA ESTUDIO 2021
	
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET, POST, PUT');	
	header('Content-Type: application/json');	
	
	//Incluye las clases necesarias
	require_once("../core/classes/resources.php");
	require_once("../core/classes/interfaces.php");
	require_once("../core/classes/external_session.php");
	
	//Carga los recursos
    include("../core/__load-resources.php");
	
    //Variable del codigo
    $result = array('success' => false,
					'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					'link' => '',
					"description" => "");
					
	$reso = new resources();
	$result["description"] = $reso->getResourceByName(explode(".",basename(__FILE__))[0],2);
	
	$name = "";
	$lname = "";
	$phone = "";
	$email = "";
	$company = "";
	$ident = "";
	$partner = "";
	$pack = "";
	$type = "";
	$docs = "";
	$password = "";
	$confirm = "";
	
	$config = new configuration("DEBUGGING");
	$debug = $config->verifyValue();
	
	//Captura las variables
	if($_SERVER['REQUEST_METHOD'] != 'PUT') {
		if(!isset($_POST['name'])) {
			if(!isset($_GET['name'])) {
				//Termina
				exit(json_encode($result));
			}
			else {
				$name = $_GET["name"];
				$lname = $_GET["lname"];
				$phone = $_GET["phone"];
				$email = $_GET["email"];
				$company = $_GET["company"];
				$ident = $_GET["ident"];
				$partner = $_GET["partner"];
				$pack = $_GET["pack"];
				$type = $_GET["type"];
				$docs = $_GET["docs"];
				$password = $_GET["password"];
				$confirm = $_GET["confirm"];
			}
		}
		else {
			$name = $_POST["name"];
			$lname = $_POST["lname"];
			$phone = $_POST["phone"];
			$email = $_POST["email"];
			$company = $_POST["company"];
			$ident = $_POST["ident"];
			$partner = $_POST["partner"];
			$pack = $_POST["pack"];
			$type = $_POST["type"];
			$docs = $_POST["docs"];
			$password = $_POST["password"];
			$confirm = $_POST["confirm"];
		}
	} 
	else {
		//Captura las variables
		parse_str(file_get_contents("php://input"),$vars);
		$name = $var["name"];
		$lname = $var["lname"];
		$phone = $var["phone"];
		$email = $var["email"];
		$company = $var["company"];
		$ident = $var["ident"];
		$partner = $var["partner"];
		$pack = $var["pack"];
		$type = $var["type"];
		$docs = $var["docs"];
		$password = $var["password"];
		$confirm = $var["confirm"];
	}
	
	//Verifica la informacion
	if(empty($name)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["NAME_EMPTY"];
		//Termina
		exit(json_encode($result));
	}
	if(empty($phone)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["PHONE_EMPTY"];
		//Termina
		exit(json_encode($result));
	}
	if(empty($email)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["EMAIL_EMPTY"];
		//Termina
		exit(json_encode($result));
	}
	if(empty($company)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["COMPANY_EMPTY"];
		//Termina
		exit(json_encode($result));
	}
	if(empty($ident)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["IDENTIFICATION_EMPTY"];
		//Termina
		exit(json_encode($result));
	}
	if(empty($password)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["PASSWORD_EMPTY_2"];
		//Termina
		exit(json_encode($result));
	}
	if(strlen($confirm) ) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["CONFIRM_PASSWORD_EMPTY"];
		//Termina
		exit(json_encode($result));
	}

	//Verifica la longitud de la contraseña
	require_once("../core/classes/configuration.php");
	$conf = new configuration("PASSWORD_MIN_LEN");
	if(strlen($password) < $conf->verifyValue()) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["PASSWORD_LENGTH"];
		//Termina
		exit(json_encode($result));
	}
	
	$site = $conf->verifyValue("WEB_SITE") . $conf->verifyValue("SITE_ROOT");

	//Verifica el email enviado
	require_once("../core/classes/users.php");
	$usua = new users();
	$usua->EMAIL = $email;
	$usua->getInfoByMail();
	//Si hay error
	if($usua->nerror == 0) {
		//Asigna el mensaje
		$result["message"] = $_SESSION["USER_EMAIL_REGISTERED"];
		//Termina
		exit(json_encode($result));
	}
	
	//Verifica el cliente
	require_once("../core/classes/client.php");
	$clien = new client();
	$clien->CLIENT_NAME = strtoupper($company);
	$clien->getInformationByOtherInfo();
	//Si hay error
	if($clien->nerror == 0) {
		//Asigna el mensaje
		$result["message"] = $_SESSION["CLIENT_NAME_REGISTERED"];
		//Termina
		exit(json_encode($result));
	}
	
	$clien->IDENTIFICATION = $ident;
	$clien->getInformationByOtherInfo("IDENTIFICATION", true);
	//Si hay error
	if($clien->nerror == 0) {
		//Asigna el mensaje
		$result["message"] = $_SESSION["CLIENT_IDENTIFICATION_REGISTERED"];
		//Termina
		exit(json_encode($result));
	}
	
	//Verifica el aliado
	if($partner != "") {
		require_once("../core/classes/partner.php");
		$partn = new partner();
		$partn->PARTNER_NAME = strtoupper($partner);
		$partn->getInformationByOtherInfo();
		//Si hay error
		if($partn->nerror != 0) {
			//Asigna el mensaje
			$result["message"] = $_SESSION["PARTNER_NAME_NOT_REGISTERED"];
			//Termina
			exit(json_encode($result));
		}
	}
	
	$type = strpos("Natural",$type) > -1 ? "CC" : "NIT";
	
	//Crea el cliente
	$clien = new client();
	$clien->CLIENT_NAME = strtoupper($company);
	$clien->PAYMENT_TYPE_ID = 1;
	$clien->CLIENT_PAYMENT_TYPE_ID = 4;
	$clien->IDENTIFICATION = $type . "-" . $ident; 
	$clien->ADDRESS = "NOT DEFINED";
	$clien->CELLPHONE = $phone;
	$clien->CITY_ID = 1;
	$clien->EMAIL = strtolower($email);
	$clien->CONTACT_NAME = strtoupper($name) . " " . strtoupper($lname);
	$clien->EMAIL_CONTACT = $clien->EMAIL;
	$clien->PHONE_CONTACT = $clien->EMAIL;
	$clien->CELLPHONE_CONTACT = $phone;
	//Lo agrega
	$clien->_add();
	//Si hay error
	if($clien->nerror == 0) {
		//Asigna el mensaje
		$result["message"] = $clien->error;
		//Termina
		exit(json_encode($result));
	}

	//Asigna los datos
	$usua = new user();
	$usua->ID = explode("@",strtolower($email))[0];
	$usua->PASSWORD = $password;
	$usua->FIRST_NAME = strtoupper($name);
	$usua->LAST_NAME = strtoupper($lname);
	$usua->IDENTIFICATION = $type . "-" . $ident; 
	$usua->EMAIL = strtolower($email);
	$usua->ADDRESS = "NOT DEFINED";
	$usua->CELLPHONE = $phone;
	$usua->CITY_ID = 1;
	$usua->ACCESS_ID = 40;	//Administrador cliente
	$usua->CHANGE_PASSWORD = "FALSE";
	$usua->ON_LINE = "TRUE";
	$usua->IS_BLOCKED = "FALSE";
	$usua->REFERENCE = $clien->ID;
	
	$usua->__add();
	//Si hay error
	if($usua->nerror == 0) {
		//Elimina el cliente
		$clien->_deleteForever();
		//Asigna el mensaje
		$result["message"] = $usua->error;
		//Termina
		exit(json_encode($result));
	}
	
	//Guarda las variables de sesion
	$_SESSION["vtappcorp_user_message"] = $_SESSION["USER_PASSWORD_OK"];
	$_SESSION['vtappcorp_username'] = $usua->FIRST_NAME . " " . $usua->LAST_NAME;
	$_SESSION['vtappcorp_userid'] = $usua->ID;
    $_SESSION['vtappcorp_useraccessid'] = $usua->ACCESS_ID;
	$_SESSION["vtappcorp_appname"] = $usua->access->APP_TITLE;
	$_SESSION["vtappcorp_skin"] = "bg-white navbar-light ,sidebar-dark-primary,";
    $_SESSION['vtappcorp_useraccess'] = $usua->access->PREFIX;
    $_SESSION['vtappcorp_referenceid'] = $usua->REFERENCE;
	if (!defined('LANGUAGE')) {
		define("LANGUAGE", 2);
	}
	$_SESSION["LANGUAGE"] = 2;
	$result["link"] = ($result["link"] != "") ? $site . "dashboard.php" : $site . $usua->access->LINK_TO;

	//Actualiza la hora de acceso
	require_once("classes/interfaces.php");
	$inter = new interfaces();
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
	}

	//Crea el nuevo LOG
	require_once("classes/logs.php");
	$log = new logs("LOGIN");
	//Adiciona la transaccion
	$log->Login();

	//Cambia estado de usuario
	$usua->setOnline(true);

	$result["message"] = $_SESSION["USER_REGISTERED"];
	$result["success"] = true;

	//Termina
	exit(json_encode($result));
?>