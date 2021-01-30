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
	
	$type = "";
	$user = "";
	$token = "";
	$id = "";
	$pos = "";
	
	//Captura las variables
	if($_SERVER['REQUEST_METHOD'] != 'PUT') {
		if(!isset($_POST['user'])) {
			if(!isset($_GET['user'])) {
				//Termina
				exit(json_encode($result));
			}
			else {
				$user = $_GET['user'];
				$type = $_GET['type'];
				$token = $_GET['token'];
				$id = $_GET['id'];
				$pos = $_GET['pos'];
			}
		}
		else {
			$user = $_POST['user'];
			$type = $_POST['type'];
			$token = $_POST['token'];
			$id = $_POST['id'];
			$pos = $_POST['pos'];
		}
	} 
	else {
		//Captura las variables
		parse_str(file_get_contents("php://input"),$vars);
		$user = $vars['user'];
		$type = $vars['type'];
		$token = $vars['token'];
		$id = $vars['id'];
		$pos = $vars['pos'];
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
	
	if(empty($type)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["ACTION_TYPE_EMPTY"];
		//Termina
		exit(json_encode($result));
	}

	//Si NO es una de las acciones definidas
	if(!(intval($type) > 0 && intval($type) < 10)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["ACTION_NOT_DEFINED"];
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
		exit(json_encode($result));
	}
	
	$actions = json_decode($reso->getResourceByName("ACTIONS"));
	$action = $actions[intval($type)-1];

	$className = $action->class;
	$method = $action->method;
	
	//Verifica si hay error
	if($className == "") {
		//Asigna el mensaje
		$result["message"] = $_SESSION["NO_VALID_ACTION_TYPE"];
		//Termina
		exit(json_encode($result));
	}
	
	//Verifica si hay error
	if($method == "") {
		//Asigna el mensaje
		$result["message"] = $_SESSION["NO_VALID_METHOD_TYPE"];
		//Termina
		exit(json_encode($result));
	}

	if($action->require_position) {
		if(empty($pos)) {
			//Asigna el mensaje
			$result["message"] = $_SESSION["POSITION_REQUIRED"];
			//Termina
			exit(json_encode($result));
		}
		$action->position = $pos;
	}
	
	//Completa la informacion del objeto a enviar
	$action->id = $id;
	$action->token = $token;
	$action->user = $user;
	
	//Instancia la clase usuario
	require_once("../core/classes/" . $className . ".php");
	$class = new $className;

	//Realiza la accion
	$result["success"] = $class->$method($action);

	if($result["success"] == true) {
		$result["message"] = $_SESSION["INFORMATION"];
		$result["data"] = $action;
	}
	else {
		$result["message"] = $class->error;
		$result["data"] = $class->sql;
	}
	//Termina
	exit(json_encode($result));
?>