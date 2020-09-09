<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

    include_once("../../classes/interfaces.php");
    include_once("../../classes/resources.php");
	include_once("../../classes/configuration.php");

	//Verifica las variables globales
	if(!defined("APP_NAME")) {
		$configig = new configuration("APP_NAME");
		define("APP_NAME", $configig->verifyValue());
	}
	//Verifica el lenguage
	$lang = new language();
	$configig = new configuration("DEFAULT_LANGUAGE");
	
	if(!defined("LANGUAGE")) {
		$lid = $lang->getInformationByAbbr(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
		if($lid < 0)
			$lid =  $configig->verifyValue();
		if(empty($_SESSION["LANGUAGE"])) {
			if(!defined('LANGUAGE'))
				define("LANGUAGE", $lid);
			$_SESSION["LANGUAGE"] = $lid;
		}
		else {
			if(!defined('LANGUAGE'))
				define("LANGUAGE", $_SESSION["LANGUAGE"]);
		}
	}
	else {
		if(empty($_SESSION["LANGUAGE"])) {
			$_SESSION["LANGUAGE"] = LANGUAGE;
		}
	}	
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'index.php');
	
	$class = "users.php";
	
	//Captura las variables
	if(empty($_POST['strModel'])) {
		//Verifica el GET
		if(empty($_GET['strModel'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$strmodel = $_GET['strModel'];
			$class = $_GET["class"];
		}
	}
	else {
		$strmodel = $_POST['strModel'];
		$class = $_POST["class"];
	}
	
	$link = "index.php";
	
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Clase configuracion
		$config = new configuration("RECAPTCHA_API_SECRET");
		$secretKey = $config->verifyValue();
		$config = new configuration("RECAPTCHA_URL_VERIFY");
		$url = $config->verifyValue();
	
		//Asigna la informacion
		$datas = json_decode($strmodel);
		
		$ip = $_SERVER['REMOTE_ADDR'];

		// post request to server
		$data = array('secret' => $secretKey, 'response' => $datas->gReCaptchaToken);
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)
			)
		);
		
		$context  = stream_context_create($options);
		$response = file_get_contents($url, false, $context);
		$responseKeys = json_decode($response,true);
		
		//Verifica la respuesta
		if(!$responseKeys["success"]) {
			$result["message"] = $_SESSION["CAPTCHA_FAILED"] . "<br />" . $responseKeys["error-codes"][0];
			$result["error"] = $responseKeys;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Realiza la operacion
		require_once("../../classes/" . $class . ".php");
		require_once("../../classes/users.php");

		//Define las clases
		$usua = new users();
		$inter = new interfaces();
		
		//Verifica si es usuario o cliente
		if($class == "users") {
			//Asigna la informacion
			$usua->FIRST_NAME = $datas->txtFIRST_NAME;
			$usua->LAST_NAME = $datas->txtLAST_NAME;
			//Verifica si viene ID
			if($datas->txtUser == "") {
				$datas->txtUser = $usua->generateUserID();
			}
			$usua->ID = $datas->txtUser;
			//Consulta la informacion
			$usua->__getInformation();
			//Si hay error
			if($usua->nerror == 0) {
				$result["message"] = $_SESSION["MSG_DUPLICATED_RECORD"];
				$result["sql"] = $usua->sql;
				$result = utf8_converter($result);
				exit(json_encode($result));
			}
			$usua->EMAIL = $datas->txtEMAIL;
			//Consulta el email
			$usua->getInfoByMail();
			//Si hay error
			if($usua->nerror == 0) {
				$result["message"] = $_SESSION["MSG_DUPLICATED_EMAIL"];
				$result["sql"] = $usua->sql;
				$result = utf8_converter($result);
				exit(json_encode($result));
			}
			
			//Actualiza la información
			$usua->setAccess(10);
			$usua->CHANGE_PASSWORD = "FALSE";
			$usua->setCity($datas->cbCity);
			$usua->IDENTIFICATION = $datas->cbTBL_SYSTEM_USER_IDENTIFICATION . "-" . $datas->txtTBL_SYSTEM_USER_IDENTIFICATION;
			$usua->FACEBOOK_USER = $datas->hfFBID;
			$usua->GOOGLE_USER = $datas->hfGOID;
			$usua->LATITUDE = $datas->hfLATITUDE;
			$usua->LONGITUDE = $datas->hfLONGITUDE;
			$usua->ADDRESS = $datas->txtADDRESS;
			$usua->CELLPHONE = $datas->txtCELLPHONE;
			$usua->PHONE = $datas->txtPHONE;
			$usua->THE_PASSWORD = $datas->THE_PASSWORD;
			$usua->REGISTERED_BY = $usua->txtID;
			$usua->IS_BLOCKED = "FALSE";
			
			$usua->__add("",LANGUAGE);

			$error = false;

			//Si hay error
			if($usua->nerror > 0) {
				$result["sql"] = $usua->sql;
				//Si es error de correo
				if($usua->nerror != 18)
					//Confirma mensaje al usuario
					$result['message'] = $usua->nerror . ". " . $usua->error;
				else 
					$result['message'] = $usua->nerror . ". " . $usua->error;
				$error = true;
			}

			//Elimina mensajes anteriores
			unset($_SESSION["vtappcorp_user_alert"]);
			//Guarda las variables de sesion
			$_SESSION["vtappcorp_user_message"] = $_SESSION["USER_PASSWORD_OK"];
			$_SESSION['vtappcorp_username'] = $usua->FIRST_NAME . " " . $usua->LAST_NAME;
			$_SESSION['vtappcorp_userid'] = $usua->ID;
			$_SESSION['vtappcorp_useraccessid'] = $usua->ACCESS_ID;

			//Actualiza la hora de acceso
			$inter->updateLastAccess();
			//Crea el nuevo LOG
			$log = new logs("LOGIN");
			//Adiciona la transaccion
			$log->Login($strmodel);
			//Si hubo error
			if($log->nerror > 0)
				//Confirma al usuario
				$result['message'] = $log->error;
			
			$result["link"] = $usua->access->LINK_TO; //"dashboard.php";
			$result['message'] = ($error) ? $_SESSION["USER_REGISTERED"] . "<br />" . $result['message'] : $_SESSION["USER_REGISTERED"];
		}
		else if($class == "client") {
			//Define la clase
			$client = new client();

			$client->EMAIL = $datas->txtEMAIL;
			//Consulta el email
			$client->getInfoByMail();
			//Si hay error
			if($client->nerror == 0) {
				$result["message"] = $_SESSION["MSG_DUPLICATED_EMAIL"];
				$result["sql"] = $client->sql;
				$result = utf8_converter($result);
				exit(json_encode($result));
			}
			
			//Asigna la informacion
			$client->CLIENT_NAME = $datas->txtCLIENT_NAME;
			$client->IDENTIFICATION = $datas->cbTBL_CLIENT_IDENTIFICATION . "-" . $datas->txtTBL_CLIENT_IDENTIFICATION;
			$client->PHONE = $dtas->txtPHONE;
			$client->CELLPHONE = $datas->txtCELLPHONE;
			$client->PHONE_ALT = $datas->txtPHONE_ALT;
			$client->CELLPHONE_ALT = $datas->txtCELLPHONE_ALT;
			$client->ADDRESS = $datas->txtADDRESS;
			$client->CELLPHONE = $datas->txtCELLPHONE;
			$client->LATITUDE = $datas->hfLATITUDE;
			$client->LONGITUDE = $datas->hfLONGITUDE;
			$client->setCity($datas->cbCity);
			$client->EMAIL_ALT = $datas->txtEMAIL_ALT;
			$client->CONTACT_NAME = strtoupper($datas->txtCONTACT_NAME);
			$client->REGISTERED_BY = "self-register";
			//Basic Systemic
			$client->setClientType(2);
						
			//Asigna la informacion
			$usua->FIRST_NAME = $datas->txtFIRST_NAME;
			$usua->LAST_NAME = $datas->txtLAST_NAME;
			//Verifica si viene ID
			$usua->ID = str_replace(" ","_",strtolower($datas->txtCLIENT_NAME));
			//Consulta la informacion
			$usua->__getInformation();
			//Si hay error
			if($usua->nerror == 0) {
				$result["message"] = $_SESSION["MSG_DUPLICATED_RECORD"];
				$result["sql"] = $usua->sql;
				$result = utf8_converter($result);
				exit(json_encode($result));
			}
			$usua->EMAIL = $datas->txtEMAIL;
			//Consulta el email
			$usua->getInfoByMail();
			//Si hay error
			if($usua->nerror == 0) {
				//Verifica el correo alternativo
				if($datas->txtEMAIL_ALT != "") {
					$usua->EMAIL = $datas->txtEMAIL_ALT;
					//Consulta el email
					$usua->getInfoByMail();
				}
				//Si hay error
				if($usua->nerror == 0) {
					$result["message"] = $_SESSION["MSG_DUPLICATED_EMAIL"];
					$result["sql"] = $usua->sql;
					$result = utf8_converter($result);
					exit(json_encode($result));
				}
			}
			$error = false;
			
			//Agrega el cliente
			$client->_add();
			//Si hay error
			if($client->nerror > 0) {
				$result["message"] = $client->error;
				$result["sql"] = $client->sql;
				$result = utf8_converter($result);
				$error = true;
			}
			
			//Si hay error al guardar el cliente
			if($error) {
				exit(json_encode($result));
			}
			
			//Verifica el nombre del contacto
			$names = explode(" ",strtoupper($datas->CONTACT_NAME));
			//Actualiza la información
			$usua->FIRST_NAME = $names[0];
			$usua->LAST_NAME = count($names) > 1 ? $names[1] : "";
			$usua->setAccess(40);
			$usua->CHANGE_PASSWORD = "FALSE";
			$usua->setCity($datas->cbCity);
			$usua->IDENTIFICATION = $datas->cbTBL_CLIENT_IDENTIFICATION . "-" . $datas->txtTBL_CLIENT_IDENTIFICATION;
			$usua->FACEBOOK_USER = "";
			$usua->GOOGLE_USER = "";
			$usua->LATITUDE = $datas->hfLATITUDE;
			$usua->LONGITUDE = $datas->hfLONGITUDE;
			$usua->ADDRESS = $datas->txtADDRESS;
			$usua->CELLPHONE = $datas->txtCELLPHONE;
			$usua->PHONE = $datas->txtPHONE;
			$usua->THE_PASSWORD = $datas->THE_PASSWORD;
			$usua->REGISTERED_BY = $usua->txtID;
			$usua->IS_BLOCKED = "TRUE";
			
			$usua->__add("",LANGUAGE, $client->CLIENT_NAME);

			//Si hay error
			if($usua->nerror > 0) {
				$result["sql"] = $usua->sql;
				//Si es error de correo
				if($usua->nerror != 18)
					//Confirma mensaje al usuario
					$result['message'] = $usua->nerror . ". " . $usua->error;
				else 
					$result['message'] = $usua->nerror . ". " . $usua->error;
				$error = true;
			}
			
			//Crea el nuevo LOG
			$log = new logs("NEW CLIENT");
			//Adiciona la transaccion
			$log->Login($strmodel);
			//Si hubo error
			if($log->nerror > 0)
				//Confirma al usuario
				$result['message'] = $log->error;

			$result["link"] = $link;
			$result['message'] = (($error) ? $_SESSION["CLIENT_REGISTERED"] . "<br />" . $result['message'] : $_SESSION["CLIENT_REGISTERED"]);
			$_SESSION["vtappcorp_user_message"] = $_SESSION["CLIENT_REGISTER_CONFIRMATION"];
		}

		//Cambia el resultado
		$result['success'] = true;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>