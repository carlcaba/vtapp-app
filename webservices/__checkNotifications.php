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
	require_once("../core/classes/user_notification.php");
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
	
	$user = "";
	$token = "";
	
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
		exit(json_encode($result));
	}

	if(empty($token)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["TOKEN_EMPTY"];
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
		$result["message"] = $usua->error;
		//Termina
		exit(json_encode($result));
	}
	
	//Resultado de las notificaciones
	$result["user"] = $usua->ID;
	$result["name"] = $usua->getFullName();

	$usnot = new user_notification();
	$usnot->setUser($usua->ID);
	$result["notifications"] = $usnot->getUnread();
	$result["data"] = $usnot->getDataUnread();

	/*
	*** TESTING
	$result["notifications"] = rand(5, 75);
	*/
	
	//Realiza la accion
	$result["success"] = $usua->error == 0;

	if($result["success"] == true) {
		$result["message"] = $_SESSION["STATE_UPDATED"];
	}
	else {
		$result["message"] = $usnot->error;
	}
	
	//Termina
	exit(json_encode($result));
?>