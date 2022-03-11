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
	
	//Carga los recursos
    include("../core/__load-resources.php");
	
    //Variable del codigo
    $result = array('success' => false,
					'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					"description" => "");
					
	$reso = new resources();
	$result["description"] = $reso->getResourceByName(explode(".",basename(__FILE__))[0],2);
	
	$estado = "true";	//Activar a disponible
	$user = "";
	$token = "";
	$pos = "";
	$id = "";
	$dbg = "";

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
				$estado = $_GET['state'];
				$token = $_GET['token'];
				$pos = $_GET['pos'];
				$id = $_GET['id'];
				_error_log(print_r($_GET,true));
			}
		}
		else {
			$user = $_POST['user'];
			$estado = $_POST['state'];
			$token = $_POST['token'];
			$pos = $_POST['pos'];
			$id = $_POST['id'];				
			_error_log(print_r($_POST,true));
		}
	} 
	else {
		//Captura las variables
		parse_str(file_get_contents("php://input"),$vars);
		$user = $vars['user'];
		$token = $vars['token'];
		$estado = $vars['state'];
		$pos = $vars['pos'];
		$id = $vars['id'];
		_error_log(print_r($vars,true));
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
		$result["message"] = $usua->error;
		//Termina
		goto _Exit;
	}
	
	if(gettype($estado) == "string")
		$estado = ($estado === "true");

	if(boolval($debug)) {
		if(is_bool($estado) === false)
			$result["warning"] = "$estado No es bool";
		else if(is_bool($estado) === true)
			$result["warning"] = "$estado es bool";
	}
	
	$usua->updatePosition($estado,$pos,$id);
	
	//Realiza la accion
	$result["success"] = $usua->nerror == 0;
	//Regresa el estado solicitado
	$result["available"] = $estado;

	if($result["success"] == true) {
		$result["message"] = $_SESSION["STATE_UPDATED"];
	}
	else {
		$result["message"] = $usua->error;
	}
	if(boolval($debug)) 
		$result["sql"] = $usua->sql;
	
	_Exit:
	$idws = updateTraceWS($idws, json_encode($result));	
	//Termina
	exit(json_encode($result));
?>