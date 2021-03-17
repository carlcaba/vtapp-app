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
	
	$state = "true";	//Activar a disponible
	$user = "";
	$token = "";
	$pos = "";
	$id = "";
	
	//Captura las variables
	if($_SERVER['REQUEST_METHOD'] != 'PUT') {
		if(!isset($_POST['user'])) {
			if(!isset($_GET['user'])) {
				//Termina
				exit(json_encode($result));
			}
			else {
				$user = $_GET['user'];
				$state = $_GET['state'];
				$token = $_GET['token'];
				$pos = $_GET['pos'];
				$id = $_GET['id'];				
			}
		}
		else {
			$user = $_POST['user'];
			$state = $_POST['state'];
			$token = $_POST['token'];
			$pos = $_POST['pos'];
			$id = $_POST['id'];				
		}
	} 
	else {
		//Captura las variables
		parse_str(file_get_contents("php://input"),$vars);
		$user = $vars['user'];
		$token = $vars['token'];
		$state = $vars['state'];
		$pos = $vars['pos'];
		$id = $vars['id'];
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
	
	$usua->updatePosition(boolval($state),$pos,$id);
	
	//Realiza la accion
	$result["success"] = $usua->error == 0;
	//Regresa el estado solicitado
	$result["available"] = boolval($state);

	if($result["success"] == true) {
		$result["message"] = $_SESSION["STATE_UPDATED"];
	}
	else {
		$result["message"] = $usua->error;
	}
	$result["sql"] = $usua->sql;
	
	//Termina
	exit(json_encode($result));
?>