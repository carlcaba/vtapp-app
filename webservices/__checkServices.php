<?
	//Web service que verifica los servicios asociados a un empleado
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
	require_once("../core/classes/users.php");
	require_once("../core/classes/interfaces.php");
	require_once("../core/classes/external_session.php");
	require_once("../core/classes/service_log.php");
	
	//Carga los recursos
    include("../core/__load-resources.php");
	
    //Variable del codigo
    $result = array('success' => false,
					'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					"description" => "");
					
	$reso = new resources();
	$result["description"] = $reso->getResourceByName(explode(".",basename(__FILE__))[0],2);
	
	$token = "";
	$user = "";
	
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
				$token = $_GET['token'];
			}
		}
		else {
			$user = $_POST['user'];
			$token = $_POST['token'];
		}
	} 
	else {
		//Captura las variables
		parse_str(file_get_contents("php://input"),$vars);
		$user = $vars['user'];
		$token = $vars['token'];
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

	include_once("__validateSession.php");

	$check = checkSession($user,$token);

	//Verifica si hay error
	if(!$check["success"]) {
		//Asigna el mensaje
		$result["message"] = $check["message"];
		//Termina
		goto _Exit;
	}

	//Instancia la clase usuario
	$usua = new users($user);
	$serv = new service_log();
	
	//Busca el ID del empleado
	$empl = new employee();
	//Busca el ID del empleado
	$empl->USER_ID = $user;
	$empl->getInformationByOtherInfo("USER_ID");
	//Verifica si existe
	if($empl->nerror > 0) {
		//Asigna el mensaje
		$result["message"] = "Employee: " . $empl->error;
		//Termina
		goto _Exit;
	}
	$usid = $empl->ID;
	
	//Busca el estado de asignacion
	$state = new service_state();
	$sid = $state->getIdByStep(7);
	//Verifica si existe
	if($sid == "") {
		//Asigna el mensaje
		$result["message"] = "State: " . $_SESSION["NOT_REGISTERED"];
		//Termina
		goto _Exit;
	}
	
	//Asigna la informacion
	$serv->setFinalEmployee($usid);
	
	//Obtiene la informacion
	$datos = $serv->listServices($sid,$usid,$user,false,0,$debug);
	$result["data"] = array();
	
	if(isset($datos["success"])) {
		$result["message"] = $datos["message"];
	}
	else {
		$result["success"] = true;
		$result["message"] = $_SESSION["WEBSERVICE_SUCCESS"];
		foreach($datos as $dat)
			array_push($result["data"],$dat);
	}
	if(filter_var($debug, FILTER_VALIDATE_BOOLEAN))
		$result["sql"] = $serv->sql;

	goto _Exit;
	
	_Exit:
	$idws = updateTraceWS($idws, json_encode($result));	
	//Termina
	exit(json_encode($result));
?>