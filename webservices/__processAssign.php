<?
	//Web service que cambia el estado a un usuario
	//LOGICA ESTUDIO 2020
	
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET, POST, PUT');	

	//Carga los recursos
    include("../core/__load-resources.php");
	
    //Variable del codigo
    $result = array('success' => false,
					'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					'continue' => false,
					"description" => "");

	require_once("../core/classes/resources.php");
	$reso = new resources();
	$result["description"] = $reso->getResourceByName(explode(".",basename(__FILE__))[0],2);

	require_once("../core/classes/configuration.php");
	$config = new configuration("DEBUGGING");
	$debug = $config->verifyValue();
	
	$idws = addTraceWS(explode(".",basename(__FILE__))[0], json_encode($_REQUEST), " ", json_encode($result));

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
				goto _Exit;
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
		$cancel = (isset($vars['cancel']) ? filter_var($vars['cancel'], FILTER_VALIDATE_BOOLEAN) : $cancel);
	}	
	
	$filemode = 'w';
	$file = './lockedServices.cas';
	$addedService = false;
	try {
		//Verificar que no esté bloqueado el servicio
		if(intval($step) == 4) {
			header('Content-Type: text/plain');
			//Verifica si existe el archivo
			if(file_exists($file)) {
				$contents = file_get_contents($file);
				//Busca el valor en el archivo
				if(strpos($contents, $id) !== false) {
					//Confirma mensaje al usuario
					$result['message'] = $_SESSION["SERVICE_ALREADY_ASSIGNED"];
					//Termina
					goto _Exit;
				}
				else 
					$filemode = 'a';
			}			
			else
				goto _CreateFile;
		}
		else 
			goto _ContinueProcess;

		_CreateFile:
		//Abrir el archivo
		$fp = fopen($file,$filemode);
		//Escribir el ID
		fwrite($fp,$id);
		//Cerrar el archivo
		fclose($fp);
		$addedService = true;
	}
	catch (Exception $ex) {
		_error_log("Error during file " . $file . ": " . $ex->getMessage());
	}
	
	_ContinueProcess:
	header('Content-Type: application/json');	
	
	//Incluye las clases necesarias
	require_once("../core/classes/interfaces.php");
	require_once("../core/classes/users.php");
	require_once("../core/classes/external_session.php");
	require_once("../core/classes/user_notification.php");
	require_once("../core/classes/service_log.php");
	require_once("../core/classes/assign_service.php");
		
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

	//Verifica la sesion
	include_once("__validateSession.php");
	
	$check = checkSession($user,$token);

	//Verifica si hay error
	if(!$check["success"]) {
		//Asigna el mensaje
		$result["message"] = $check["message"];
		//Termina
		goto _Exit;
	}
	
	$usua = new users($user);
	$usua->__getInformation();
	//Si hay error
	if($usua->nerror > 0) {
		//Asigna el mensaje
		$result["message"] = "User: " . $usua->error;
		//Termina
		goto _Exit;
	}
	
	$service = new service();
	$service->ID = $id;
	$service->__getInformation();
	//Si hay error
	if($service->nerror > 0) {
		//Asigna el mensaje
		$result["message"] = "Service: " . $service->error;
		//Termina
		goto _Exit;
	}
	
	_error_log("Service actual state " . print_r($service->state,true));
	
	//Verifica el estado del servicio
	if(intval($service->state->ID_STATE) >= 7) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["SERVICE_ALREADY_ASSIGNED"];
		//Termina
		goto _Exit;
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
		goto _Exit;
	}
	
	//Verifica el proceso
	if(intval($usnot->STEP) != intval($step)) {
		//Asigna el mensaje
		$result["message"] = $_SESSION["SERVICE_STEP_WRONG"];
		//Termina
		goto _Exit;
	}
	if(filter_var($usnot->IS_BLOCKED, FILTER_VALIDATE_BOOLEAN)) {
		//Asigna el mensaje
		$result["message"] = $_SESSION["SERVICE_STEP_BLOCKED"];
		//Termina
		goto _Exit;
	}

	//Si desea cancelar el proceso
	if($cancel) {
		$usnot->decline();
		$result["message"] = $_SESSION["ASSIGN_CANCELED"];
		//Termina
		goto _Exit;
	}
	
	$sstate = $service->state->getArray();
	
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
		$service->updateState($sstate[7]);
		//Si no ha ocurrido un error
		if($service->nerror > 0) {
			$result["continue"] = false;
			$result["message"] .= "<br/>" . $_SESSION["NO_SERVICE_UPDATED"];
			_error_log($service->error, $service->sql);
		}
		else {
			_error_log("Service updated", $service->sql);
			//Obtiene la informacion del vehiculo
			$vehicle = new vehicle();
			$vehicle->getInformationByUserId($user);
			if($vehicle->nerror > 0)
				_error_log("No vehicle found " . $vehicle->error, $vehicle->sql);
			else 
				_error_log("Vehicle found: " . print_r(get_object_vars($vehicle),true), $vehicle->sql);
			//Instancia nuevo log
			$sLog = new service_log();
			//Log
			$sLog->setService($service->ID);
			//Busca el ultimo log
			$sLog->getLastLog();
			_error_log("Last log found: " . print_r(get_object_vars($sLog),true), $sLog->sql);
			//Limpia los campos no requeridos
			$employee = new employee();
			$employee->USER_ID = $usua->ID;
			$employee->getInformationByOtherInfo("USER_ID");
			_error_log("Employee found: " . print_r(get_object_vars($employee),true), $sLog->sql);
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
				if(filter_var($debug, FILTER_VALIDATE_BOOLEAN))
					$result["sql_DEBUGGER"] = $sLog->sql; 
			}
			else {
				$result["message"] .= "<br/>" . $_SESSION["SERVICE_ASSIGNED"];
				
				$assi = new assign_service();
				$assi->setService($service->ID);
				$assi->getInformationByService();
				if($assi->nerror > 0) {
					_error_log("Could'nt assign service TBL_ASSIGN_SERVICE: Not Found!", $assi->sql);
				}
				else {
					$chng = false;
					if($employee->nerror == 0) {
						$assi->setEmployee($employee->ID);
						$chng = true;
					}
					if($assi->nerror > 0) {
						_error_log("Could'nt assign employee TBL_ASSIGN_SERVICE: Employee not Found " . $employee->ID . "! " . $assi->error, $assi->sql);
					}
					if($vehicle->nerror == 0) {
						$assi->setVehicle($vehicle->ID);
						$chng = true;
					}
					if($assi->nerror > 0) {
						_error_log("Could'nt assign vehicle TBL_ASSIGN_SERVICE: Vehicle not Found " . $vehicle->ID . "! " . $assi->error, $assi->sql);
					}
					if($chng) {
						$assi->_modify();
						if($assi->nerror > 0) {
							_error_log("Could'nt assign data TBL_ASSIGN_SERVICE: " . $assi->error, $assi->sql);
						}
					}
				}
				
			}
			_error_log("Log update:" . print_r(get_object_vars($sLog),true), $sLog->sql);
		}
	}
	
	_Exit:
	if($addedService) {
		//Modifica la cabecera
		header('Content-Type: text/plain');
		//Lee el archivo
		$contents = file_get_contents($file);
		//Elimina la informacion del servicio
		$contents = str_replace($id,'',$contents);
		//Lo sobreescribe
		file_put_contents($file,$contents);
		//Cambia la cabecera
		header('Content-Type: application/json');	
	}
	
	$idws = updateTraceWS($idws, json_encode($result));	
	//Termina
	exit(json_encode($result));
?>