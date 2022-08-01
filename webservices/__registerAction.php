<?
	//Web service que genera la accion sobre un servicio
	//LOGICA ESTUDIO 2019
	
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
	require_once("../core/classes/configuration.php");
	
	//Carga los recursos
    include("../core/__load-resources.php");
	
    //Variable del codigo
    $result = array('success' => false,
					'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					"description" => "");
					
	$reso = new resources();
	$result["description"] = $reso->getResourceByName(explode(".",basename(__FILE__))[0],2);
	
	$tipo = "";
	$user = "";
	$token = "";
	$id = "";
	$pos = "";
	$det = "";
	$note = "";
	
	$config = new configuration("DEBUGGING");
	$debug = $config->verifyValue();
	
	$idws = addTraceWS(explode(".",basename(__FILE__))[0], json_encode($_REQUEST), " ", json_encode($result));
	
	//Captura las variables
	if($_SERVER['REQUEST_METHOD'] != 'PUT') {
		if(!isset($_POST['user'])) {
			if(!isset($_GET['user'])) {
				//Termina
				goto _Exit;
			}
			else {
				$user = $_GET['user'];
				$tipo = $_GET['type'];
				$token = $_GET['token'];
				$id = $_GET['id'];
				$pos = $_GET['pos'];
				$det = $_GET['details'];
				$note = $_GET['notes'];
			}
		}
		else {
			$user = $_POST['user'];
			$tipo = $_POST['type'];
			$token = $_POST['token'];
			$id = $_POST['id'];
			$pos = $_POST['pos'];
			$det = $_POST['details'];
			$note = $_POST['notes'];
		}
	} 
	else {
		//Captura las variables
		parse_str(file_get_contents("php://input"),$vars);
		$user = $vars['user'];
		$tipo = $vars['type'];
		$token = $vars['token'];
		$id = $vars['id'];
		$pos = $vars['pos'];
		$det = $vars['details'];
		$note = $vars['notes'];
	}
	
	//Verifica la informacion
	if(empty($user)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["USERNAME_EMPTY"];
		//Termina
		goto _Exit;
	}

	if(empty($token)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["TOKEN_EMPTY"];
		//Termina
		goto _Exit;
	}
	if(empty($id)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["ID_SERVICE_EMPTY"];
		//Termina
		goto _Exit;
	}
	
	if(empty($tipo)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["ACTION_TYPE_EMPTY"];
		//Termina
		goto _Exit;
	}

	//Si NO es una de las acciones definidas
	if(!(intval($tipo) > 0 && intval($tipo) < 11)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["ACTION_NOT_DEFINED"];
		//Termina
		goto _Exit;
	}

	//Si no tiene los parametros para la accion
	//Actualizar posicion
	if(intval($tipo) == 1 && $pos == "") {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["POSITION_REQUIRED"];
		//Termina
		goto _Exit;
	}

	//Si no tiene los parametros para la accion
	//Cancelar
	if(intval($tipo) == 8 && $det == "") {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["NO_DETAILS_FOR_CANCEL"];
		//Termina
		goto _Exit;
	}

	//Si no tiene los parametros para la accion
	//Actualizar posicion
	if(intval($tipo) == 4 && $pos == "") {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["POSITION_REQUIRED"];
		//Termina
		goto _Exit;
	}

	/*
	//Actualizar posicion
	if(intval($tipo) == 10 && $pos == "") {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["POSITION_REQUIRED"];
		//Termina
		goto _Exit;
	}
	*/
	
	//Si no tiene los parametros para la accion
	//Cancelar
	if(intval($tipo) == 10 && $det == "") {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["NO_DETAILS_FOR_DELAY"];
		//Termina
		goto _Exit;
	}
	
	//Verifica la sesion
	include_once("__validateSession.php");
	
	$check = checkSession($user,$token);

	//Verifica si hay error
	if(!$check["success"]) {
		header("HTTP/1.1 401 Unauthorized " . $_SESSION["SESSION_ENDED"]);
		exit;		
	}

	//Verifica el ID de servicio
	require_once("../core/classes/service.php");
	$service = new service();
	$service->ID = $id;
	$service->__getInformation();
	//Si hay error
	if($service->nerror > 0) {
		//Asigna el mensaje
		$result["message"] = $service->error;
		//Termina
		goto _Exit;
	}
	
	//Verifica quien recibe el paquete
	if(intval($tipo) == 6 && $det == "") {
		$det = $service->DELIVER_TO;
	}
	
	$actions = json_decode($reso->getResourceByName("ACTIONS"));
	$action = $actions[intval($tipo)-1];

	$className = $action->class;
	$method = $action->method;
	
	//Verifica si hay error
	if($className == "") {
		//Asigna el mensaje
		$result["message"] = $_SESSION["NO_VALID_ACTION_TYPE"];
		//Termina
		goto _Exit;
	}
	
	//Verifica si hay error
	if($method == "") {
		//Asigna el mensaje
		$result["message"] = $_SESSION["NO_VALID_METHOD_TYPE"];
		//Termina
		goto _Exit;
	}

	if($action->require_position) {
		if(empty($pos)) {
			//Asigna el mensaje
			$result["message"] = $_SESSION["POSITION_REQUIRED"];
			//Termina
			goto _Exit;
		}
		$action->position = $pos;
	}
	
	//Completa la informacion del objeto a enviar
	$action->id = $id;
	$action->token = $token;
	$action->user = $user;
	$action->{"collect"} = false;
	$action->{"price"} = 0;
	$action->{"details"} = $det;
	$action->{"notes"} = $note;
	$action->{"notification"} = "";
	
	//Instancia la clase definida
	require_once("../core/classes/" . $className . ".php");
	$class = new $className;

	$result["data"] = $action;
	$result["post"] = "";

	//Realiza la accion
	$result["success"] = $class->$method($action);

	if($result["success"] == true) {
		$result["message"] = "Información";
		
		if(intval($tipo) == 6) {
			$action->collect = $service->CheckToCollect();
			$action->price = $service->PRICE;
		}
		elseif(intval($tipo) == 10) {
			require_once("../core/classes/users.php");
			$usua = new users();
			$body = sprintf($config->verifyValue("DELAY_MESSAGE"),$det,$service->type->getResource());
			$to = implode(";",[$service->REQUESTED_EMAIL . "," . $service->REQUESTED_BY, $service->DELIVER_EMAIL . "," . $service->DELIVER_TO]);
			$subject = $config->verifyValue("DELAY_SUBJECT");
			$usua->sendMail($body, $to, $subject);
			if($usua->nerror > 0) {
				$action->{"notification"} = $usua->error;
				_error_log($usua->error);
			}
			else {
				$action->{"notification"} = $reso->getResourceByName("NOTIFICATION_SENT") . " (TO: $to SUBJECT: $subject BODY: $body)"; 
			}
		}
		
		//Verifica si debe realizar una accion posterior
		if(intval($tipo) != $action->action) {
			//Instancia la clase definida
			require_once("../core/classes/" . $action->class . ".php");
			$class = new $action->class;
			
			foreach($action->position as $key => $val) {
				if(strpos($val,"{{USER_ID}}") !== false)
					$val = str_replace("{{USER_ID}}", $user, $val);
				$class->$key = $val;
			}
			
			$method = $action->method;
			$class->$method();
			
			if($class->nerror > 0) 
				$result["message"] .= "<br/>Action Post error: " . $class->error . " SQL " . $class->sql;
			else 
				$result["message"] .= "<br/>Action Post: OK";				

			$result["post"] = $action;
		}
		
	}
	else {
		$result["message"] = $class->error;
		if(filter_var($debug, FILTER_VALIDATE_BOOLEAN))
			$result["data"] = $class->sql;
	}

	_Exit:
	$idws = updateTraceWS($idws, json_encode($result));	
	//Termina
	exit(json_encode($result));
?>