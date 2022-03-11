<?
	//Web service que verifica la sesion del usuario
	//LOGICA ESTUDIO 2019
	
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET, POST, PUT');	

	//Incluye las clases necesarias
	require_once("../core/classes/resources.php");
	require_once("../core/classes/external_session.php");
	require_once("../core/classes/configuration.php");

	//Verifica si esta habilitado el debug
	if(!defined("DEBUG")) {
		$conf = new configuration("DEBUGGING");
		$debug = $conf->verifyValue();
		if($debug === 0)
			$debug = false;
		define("DEBUG", $debug); 
	}
	
	function checkSession($login, $txid) {
		//Carga los recursos
		include_once("../core/__load-resources.php");
		
		//Variable del codigo
		$resultado = array('success' => false,
						'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
						"partner_id" => "",
						"client_id" => "",
						"description" => "");
					
		$reso = new resources(basename(__FILE__));
		$result["description"] = $reso->getResourceByName(explode(".",basename(__FILE__))[0],2);
						
		$external = new external_session($txid);
		//Busca la informacion
		$external->__getInformation();
		//Si hay error
		if($external->nerror > 0) {
			$resultado["message"] = $_SESSION["NO_VALID_SESSION"];
			//Termina
			return $resultado;
		}
		
		//Verifica el usuario
		if($external->USER_ID != $login) {
			$resultado["message"] = $_SESSION["NO_VALID_USER_SESSION"];
			//Termina
			return $resultado;
		}

		//Verifica el tiempo de la sesion
		$conf = new configuration("SESSION_EXPIRATION");
		$max_time = $conf->verifyValue();
		
		//Toma el dateStamp del servidor
		$now = date("Y-n-j H:i:s");
		//Verifica la fecha de login
		$logtime = $external->MODIFIED_ON == null ? $external->REGISTERED_ON : $external->MODIFIED_ON;

		//Calcula la diferencia
		$time = (strtotime($now)-strtotime($logtime));

		//Si ya expiro la sesion
		if($time >= $max_time) {
			//Cierra la sesión
			$external->logOut();
			//Actualiza el resultado
			$resultado["message"] = $_SESSION["SESSION_EXPIRED"];
			//Termina
			return $resultado;
		}

		//Actualiza
		$external->USER_ID = $login;
		$external->MODIFIED_BY = "Vtapp.WS";
		$external->MODIFIED_ON = "NOW()";
		$external->_modify();

		if($external->nerror > 0) {
			$resultado["error"] = $external->error;
		}

		$resultado["partner_id"] = $external->PARTNER_ID;
		$resultado["client_id"] = $external->CLIENT_ID;
		$resultado["success"] = true;
		$resultado["message"] = $_SESSION["VALIDATION_OK"];
		
		$_SESSION["vtappcorp_userid"] = "WSUser";
		$_SESSION['vtappcorp_username'] = "Webservice User";
		$_SESSION['vtappcorp_useraccessid'] = 90;
		$_SESSION['vtappcorp_useraccess'] = "ADM";
		
		//Termina
		return $resultado;
	}
?>