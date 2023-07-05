<?
	//Web service que registra un usuario desde portal
	//LOGICA ESTUDIO 2023
	
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET, POST, PUT');	
	header('Content-Type: application/json');	

	$uid = uniqid();
	
	//Incluye las clases necesarias
	require_once("../core/classes/resources.php");
	require_once("../core/classes/interfaces.php");
	
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
	$fullname = "";
	$phone = "";
	$email = "";
	$company = "";
	$ident = "";
	$partner = "";
	$type = "";
	$brand = "";
	$partnerbrand = "";
	$namepartner = "";
	$emailpartner = "";
	$phonepartner = "";
	$city = "";
	$cities = "";
	$employees = "";
	$contract = "";
	$clients = false;
	$pack = "";
	$dtype = "";
	
	$config = new configuration("DEBUGGING");
	$debug = $config->verifyValue();
	
	$idws = addTraceWS(explode(".",basename(__FILE__))[0], json_encode($_REQUEST), $uid, json_encode($result));
	
	//Captura las variables
	if($_SERVER['REQUEST_METHOD'] != 'PUT') {
		if(!isset($_POST['strModel'])) {
			if(!isset($_GET['strModel'])) {
				//Termina
				goto _Exit;
			}
			else {
				$strModel = $_GET["strModel"];
			}
		}
		else {
			$strModel = $_POST["strModel"];
		}
	} 
	else {
		//Captura las variables
		parse_str(file_get_contents("php://input"),$vars);
		$strModel = $vars["strModel"];
	}

	$errMsg = "";

	//Asigna la informacion
	$datas = json_decode($strModel);

	switch(json_last_error()) {
		case JSON_ERROR_NONE:
			$errMsg = "";
			break;
		case JSON_ERROR_DEPTH:
			$errMsg = 'Excedido tamaño máximo de la pila';
			break;
		case JSON_ERROR_STATE_MISMATCH:
			$errMsg = 'Desbordamiento de buffer o los modos no coinciden';
			break;
		case JSON_ERROR_CTRL_CHAR:
			$errMsg = 'Encontrado carácter de control no esperado';
			break;
		case JSON_ERROR_SYNTAX:
			$errMsg = 'Error de sintaxis, JSON mal formado';
			break;
		case JSON_ERROR_UTF8:
			$errMsg = 'Caracteres UTF-8 malformados, posiblemente codificados de forma incorrecta';
			break;
		default:
			$errMsg = 'Error desconocido en JSON';
			break;
	}

	if($errMsg != "") {
		$result["message"] = "Ocurrio un error al deserializar la información enviada. $errMsg";
		goto _Exit;
	}

	//Ajusta la información de acuerdo a la peticion
	$fullname = $datas->txtName;
	$name = explode(" ",$datas->txtName)[0];
	$lname = explode(" ",$datas->txtName)[1];
	$phone = $datas->txtPhone;
	$email = $datas->txtEmail;
	$company = $datas->txtCompany;
	$ident = $datas->txtId;
	$partner = $datas->txtPartner;
	$type = $datas->hfType;
	$brand = $datas->txtBrand;
	$partnerbrand = $datas->txtBrandPartner;
	$namepartner = $datas->txtNamePartner;
	$emailpartner = $datas->txtEmailPartner;
	$phonepartner = $datas->txtPhonePartner;
	$cities = $datas->chkCity;
	$city = $datas->cbMainCity;
	$employees = $datas->cbEmployee;
	$contract = $datas->cbContract;
	$clients = filter_var($datas->cbClients,FILTER_VALIDATE_BOOLEAN);
	$pack = $datas->cbPackage;
	$dtype = $datas->cbType;

	//Verifica la informacion
	if(empty($fullname)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["NAME_EMPTY"];
		//Termina
		goto _Exit;
	}
	if(empty($phone)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["PHONE_EMPTY"];
		//Termina
		goto _Exit;
	}
	if(empty($email)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["EMAIL_EMPTY"];
		//Termina
		goto _Exit;
	}
	$createUser = true;
	$createClient = true;
	$createPartner = false;
	$userId = $ident;
	$pymttype = "5";
	$clitype = "2";
	
	$checkUser = true;
	$checkClient = true;
	$checkPartner = false;
	switch($type) {
		case "afilia-una-empresa": {
			if(empty($company)) {
				//Confirma mensaje al usuario
				$result['message'] = $_SESSION["COMPANY_EMPTY"];
				//Termina
				goto _Exit;
			}
			if(empty($ident)) {
				//Confirma mensaje al usuario
				$result['message'] = $_SESSION["IDENTIFICATION_EMPTY"];
				//Termina
				goto _Exit;
			}
			if(empty($partner)) {
				//Confirma mensaje al usuario
				$result['message'] = $_SESSION["PARTNER_EMPTY"];
				//Termina
				goto _Exit;
			}
			if(empty($namepartner)) {
				//Confirma mensaje al usuario
				$result['message'] = $_SESSION["CONTACT_PARTNER_EMPTY"];
				//Termina
				goto _Exit;
			}
			if(empty($emailpartner)) {
				//Confirma mensaje al usuario
				$result['message'] = $_SESSION["EMAIL_PARTNER_EMPTY"];
				//Termina
				goto _Exit;
			}
			if(empty($phonepartner)) {
				//Confirma mensaje al usuario
				$result['message'] = $_SESSION["PHONE_PARTNER_EMPTY"];
				//Termina
				goto _Exit;
			}
			$createPartner = true;
			$checkPartner = true;
			$clitype = "1";
			break;
		}
		case "quiero-ser-aliado": {
			if(empty($company)) {
				//Confirma mensaje al usuario
				$result['message'] = $_SESSION["COMPANY_EMPTY"];
				//Termina
				goto _Exit;
			}
			if(empty($ident)) {
				//Confirma mensaje al usuario
				$result['message'] = $_SESSION["IDENTIFICATION_EMPTY"];
				//Termina
				goto _Exit;
			}
			$createClient = false;
			$createPartner = true;
			$checkPartner = true;
			$clitype = "1";
			break;
		}
		case "envios-por-demanda-usuario": {
			if(empty($ident)) {
				//Confirma mensaje al usuario
				$result['message'] = $_SESSION["IDENTIFICATION_EMPTY"];
				//Termina
				goto _Exit;
			}
			$company = $fullname;
			$pymttype = "3";
			break;
		}
		case "envios-por-demanda": {
			if(empty($company)) {
				//Confirma mensaje al usuario
				$result['message'] = $_SESSION["COMPANY_EMPTY"];
				//Termina
				goto _Exit;
			}
			$userId = explode("@",$email)[0];
			$pymttype = "3";
			break;
		}
		case "mensajeria-a-la-medida": {
			if(empty($company)) {
				//Confirma mensaje al usuario
				$result['message'] = $_SESSION["COMPANY_EMPTY"];
				//Termina
				goto _Exit;
			}
			$userId = explode("@",$email)[0];
			$pymttype = "4";
			$clitype = "3";
			break;
		}
		default: {
			//Confirma mensaje al usuario
			$result['message'] = $_SESSION["ERRORS_ON_INFORMATION"];
			//Termina
			goto _Exit;
			break;
		}
	}

	//PLAN B
	$result["data"] = array("createUser" =>	 $createUser,
							"createClient" => $createClient,
							"createPartner" => $createPartner,
							"userId" => $userId,
							"pymType" => $pymttype,
							"cliType" => $clitype,
							"checkUser" => $checkUser,
							"checkClient" => $checkClient,
							"checkPartner" => $checkPartner);

	$result["message"] = $_SESSION["USER_REGISTERED"];
	$result["success"] = true;
						
	goto _Exit;

	//End PLAN B

	//Verifica la longitud de la contraseña
	require_once("../core/classes/configuration.php");
	$conf = new configuration("INIT_PASSWORD");
	$password = $conf->verifyValue();
	
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
		goto _Exit;
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
		goto _Exit;
	}
	
	$clien->IDENTIFICATION = $ident;
	$clien->getInformationByOtherInfo("IDENTIFICATION", true);
	//Si hay error
	if($clien->nerror == 0) {
		//Asigna el mensaje
		$result["message"] = $_SESSION["CLIENT_IDENTIFICATION_REGISTERED"];
		//Termina
		goto _Exit;
	}
	
	//Verifica el aliado
	if($checkPartner) {
		require_once("../core/classes/partner.php");
		$partn = new partner();
		$partn->PARTNER_NAME = strtoupper($partner);
		$partn->getInformationByOtherInfo();
		$createPartner = $partn->nerror > 0;
	}
	
	$type = strpos("Natural",$type) > -1 ? "CC" : "NIT";
	
	//Crea el cliente
	$clien = new client();
	$clien->CLIENT_NAME = strtoupper($company);
	$clien->setClientPaymentType(1);
	$clien->CLIENT_PAYMENT_TYPE_ID = 4;
	$clien->IDENTIFICATION = $type . "-" . $ident; 
	$clien->ADDRESS = "NOT DEFINED";
	$clien->CELLPHONE = $phone;
	$clien->setCity(1);
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
		goto _Exit;
	}

	//Asigna los datos
	$usua = new users();
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
		goto _Exit;
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

	_Exit:
	$idws = updateTraceWS($idws, json_encode($result));	
	//Termina
	exit(json_encode($result));
?>