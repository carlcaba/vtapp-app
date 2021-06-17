<?
	//Web service que genera verifica los datos asociados a una entidad
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
	
	$config = new configuration("DEBUGGING");
	$debug = $config->verifyValue();
	
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
			}
		}
		else {
			$user = $_POST['user'];
			$type = $_POST['type'];
			$token = $_POST['token'];
		}
	} 
	else {
		//Captura las variables
		parse_str(file_get_contents("php://input"),$vars);
		$user = $vars['user'];
		$type = $vars['type'];
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
	if(empty($type)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["DATA_TYPE_EMPTY"];
		//Termina
		exit(json_encode($result));
	}

	include_once("__validateSession.php");
	
	$check = checkSession($user,$token);

	//Verifica si hay error
	if(!$check["success"]) {
		//Asigna el mensaje
		$result["message"] = $check["message"];
		//Termina
		exit(json_encode($result));
	}
	
	$conf = $reso->getResourceByName("DATA_TYPE_WEBSERVICE");
	$className = "";
	$options = "";
	
	foreach(explode(";",$conf) as $data) {
		$temp = explode("=",$data);
		if($temp[0] == $type) {
			$className = $temp[1];
			break;
		}
	}

	//Verifica si hay error
	if($className == "") {
		//Asigna el mensaje
		$result["message"] = $_SESSION["NO_VALID_DATA_TYPE"];
		//Termina
		exit(json_encode($result));
	}
	
	if(strpos($className,"%") !== false) {
		$options = explode("%",$className)[1];
		$className = explode("%",$className)[0];
	}
	
	//Instancia la clase usuario
	require_once("../core/classes/" . $className . ".php");
	$class = new $className;

	//Obtiene la informacion
	if($options != "")
		$datos = $class->listData(0,$options);
	else 
		$datos = $class->listData();
		
	$result["success"] = count($datos) > 0;

	if($result["success"] == true) {
		$result["message"] = $_SESSION["INFORMATION"];
		$result["data"] = $datos;
	}
	else {
		$result["message"] = $_SESSION["NO_INFORMATION"];
		if(boolval($debug))
			$result["data"] = $class->sql;
	}
	//Termina
	exit(json_encode($result));
?>