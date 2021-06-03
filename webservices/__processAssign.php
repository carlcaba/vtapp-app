<?
	//Web service que cambia el estado a un usuario
	//LOGICA ESTUDIO 2020
	
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
	require_once("../core/classes/users.php");
	require_once("../core/classes/external_session.php");
	require_once("../core/classes/configuration.php");
	require_once("../core/classes/user_notification.php");
	require_once("../core/classes/service_log.php");
	
	//Carga los recursos
    include("../core/__load-resources.php");
	
    //Variable del codigo
    $result = array('success' => false,
					'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					'continue' => false,
					"description" => "");
					
	$reso = new resources();
	$result["description"] = $reso->getResourceByName(explode(".",basename(__FILE__))[0],2);
	
	$user = "";
	$token = "";
	$step = 1;
	$id = "";
	$cancel = false;
	
	//Captura las variables
	if($_SERVER['REQUEST_METHOD'] != 'PUT') {
		if(!isset($_POST['user'])) {
			if(!isset($_GET['user'])) {
				//Termina
				exit(json_encode($result));
			}
			else {
				$user = $_GET['user'];
				$token = $_GET['token'];
				$step = $_GET['step'];
				$id = $_GET['id'];
				$cancel = (isset($_GET['cancel']) ? $_GET['cancel'] != "false" : $cancel);
			}
		}
		else {
			$user = $_POST['user'];
			$token = $_POST['token'];
			$step = $_POST['step'];
			$id = $_POST['id'];
			$cancel = (isset($_POST['cancel']) ? $_POST['cancel'] != "false" : $cancel);
		}
	} 
	else {
		//Captura las variables
		parse_str(file_get_contents("php://input"),$vars);
		$user = $vars['user'];
		$token = $vars['token'];
		$step = $vars['step'];
		$id = $vars['id'];
		$cancel = (isset($vars['cancel']) ? boolval($vars['cancel']) : $cancel);
	}
	
	//Verifica la informacion
	if(empty($user)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["USERNAME_EMPTY"];
		//Termina
		exit(json_encode($result));
	}

	if(empty($token)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["TOKEN_EMPTY"];
		//Termina
		exit(json_encode($result));
	}

	if(empty($id)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["ID_SERVICE_EMPTY"];
		//Termina
		exit(json_encode($result));
	}

	//Verifica la sesion
	include_once("__validateSession.php");
	
	$check = checkSession($user,$token);

	//Verifica si hay error
	if(!$check["success"]) {
		//Asigna el mensaje
		$result["message"] = $check["message"];
		//Termina
		exit(json_encode($result));
	}

	$usua = new users($user);
	$usua->__getInformation();
	//Si hay error
	if($usua->nerror > 0) {
		//Asigna el mensaje
		$result["message"] = "User: " . $usua->error;
		//Termina
		exit(json_encode($result));
	}
	
	$service = new service();
	$service->ID = $id;
	$service->__getInformation();
	//Si hay error
	if($service->nerror > 0) {
		//Asigna el mensaje
		$result["message"] = "Service: " . $service->error;
		//Termina
		exit(json_encode($result));
	}

	//Verifica el estado de notificacion
	$usnot = new user_notification();
	$usnot->setUser($usua->ID);
	$usnot->setService($service->ID);
	$usnot->getInformationByUserService();
	if($usnot->nerror > 0) {
		//Asigna el mensaje
		$result["message"] = "Notification: " . $usnot->error;
		//Termina
		exit(json_encode($result));
	}
	
	//Verifica el proceso
	if(intval($usnot->STEP) != intval($step)) {
		//Asigna el mensaje
		$result["message"] = $_SESSION["SERVICE_STEP_WRONG"];
		//Termina
		exit(json_encode($result));
	}
	if(boolval($usnot->IS_BLOCKED)) {
		//Asigna el mensaje
		$result["message"] = $_SESSION["SERVICE_STEP_BLOCKED"];
		//Termina
		exit(json_encode($result));
	}

	//Si desea cancelar el proceso
	if($cancel) {
		$usnot->decline();
		$result["message"] = $_SESSION["ASSIGN_CANCELED"];
		//Termina
		exit(json_encode($result));
	}
	
	if($step == 1 && $service->state->STEP_ID == 5)
		//Actualiza el estado del servicio
		$service->updateState();
	
	//Busca la informacion
	$result["data"] = $service->processAssign(intval($step));
	
	//Realiza la accion
	$result["success"] = $service->error == 0;

	if($result["success"] == true) {
		$result["message"] = $_SESSION["INFORMATION_UPDATED"];
		$usnot->updateStep($step+1);
		$result["continue"] = $usnot->nerror == 0;
	}
	else {
		$result["message"] = $service->error;
		$usnot->decline();
	}
	
	//Verifica si el proceso de asignación termina
	if($step == 4 && $result["success"]) {
		$curState = $service->STATE_ID;
		//Actualiza el estado del servicio
		$service->updateState($service->state->getIdByStep(7));
		//Si no ha ocurrido un error
		if($service->nerror > 0) {
			$result["continue"] = false;
			$result["message"] .= "<br/>" . $_SESSION["NO_SERVICE_UPDATED"];
			error_log("SQL No service Updated " . $service->sql . " -> " . $service->error);
		}
		else {
			//Obtiene la informacion del vehiculo
			$vehicle = new vehicle();
			$vehicle->getInformationByUserId($user);
			if($vehicle->nerror > 0) {
				error_log("SQL No vehicle found " . $vehicle->sql . " -> " . $vehicle->error);
			}
			//Instancia nuevo log
			$sLog = new service_log();
			//Log
			$sLog->setService($service->ID);
			//Busca el ultimo log
			$sLog->getLastLog();
			//Limpia los campos no requeridos
			//$sLog->ID = "UUID()";
			$employee = new employee();
			$employee->USER_ID = $usua->ID;
			$employee->getInformationByOtherInfo("USER_ID");
			//Asigna el ultimo estado
			$sLog->setInitialState($curState);
			$sLog->setFinalState($service->STATE_ID);
			$sLog->EMPLOYEE_INITIAL_ID = $sLog->EMPLOYEE_FINAL_ID;
			$sLog->setFinalEmployee($employee->ID);
			$sLog->VEHICLE_INITIAL_ID = $sLog->VEHICLE_FINAL_ID;
			if($vehicle->nerror > 0)
				$sLog->VEHICLE_FINAL_ID = "NULL";
			else
				$sLog->setFinalVehicle($vehicle->ID);
			$sLog->MODIFIED_BY = $sLog->REGISTERED_BY;
			$sLog->MODIFIED_ON = "NOW()";
			//Adiciona el log
			$sLog->_modify();
			//Si se genera error
			if($sLog->nerror > 0) {
				$result["message"] .= "<br/>" . $_SESSION["ERROR"] . " " . $_SESSION["SERVICES"] . ": " . $sLog->error;
				$result["sql_DEBUGGER"] = $sLog->sql; 
			}
			else {
				$result["message"] .= "<br/>" . $_SESSION["SERVICE_ASSIGNED"];
			}
			error_log("Log update " . $sLog->sql);
		}
	}
	
	//Termina
	exit(json_encode($result));
?>