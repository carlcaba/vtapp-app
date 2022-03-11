<?
	//Web service que genera el login del usuario
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
	require_once("../core/classes/configuration.php");
	
	//Carga los recursos
    include("../core/__load-resources.php");
	
    //Variable del codigo
    $result = array('success' => false,
					'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					"description" => "");
					
	$reso = new resources(basename(__FILE__));
	$result["description"] = $reso->getResourceByName(explode(".",basename(__FILE__))[0],2);
						
	$user = "";
	$token = "";
	
	$config = new configuration("DEBUGGING");
	$debug = $config->verifyValue();
	
	$idws = addTraceWS(explode(".",basename(__FILE__))[0], json_encode($_REQUEST), " ", json_encode($result));
	
	//Captura las variables
	if($_SERVER['REQUEST_METHOD'] != 'PUT') {
		if(!isset($_POST['user'])) {
			if(!isset($_GET['user'])) {
				header("HTTP/1.1 400 Bad Request " . $result["message"]);
				exit;		
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
		header("HTTP/1.1 400 Bad Request " . $_SESSION["USERNAME_EMPTY"]);
		exit;		
	}
	if(empty($token)) {
		header("HTTP/1.1 400 Bad Request " . $_SESSION["TOKEN_EMPTY"]);
		exit;		
	}

	//Instancia la clase usuario
	$usua = new users($user);
	$conf = new configuration("WEB_SITE");
	$website = $conf->verifyValue();
	$siteroot = $conf->verifyValue("SITE_ROOT");

	//Si hay error
	if($usua->nerror > 0) {
		header("HTTP/1.1 403 Forbidden " . $usua->error);
		exit;		
	}

	$exts = new external_session($token);
	//Busca la informacion
	$exts->__getInformation();
	
	//Si hay error
	if($exts->nerror > 0) {
		$result["message"] = $exts->error;
		//Termina
		exit(json_encode($result));
	}
	
	//Verifica el usuario
	if($exts->USER_ID == $usua->ID) {
		//Actualiza la sesión externa
		$exts->logOut();
		//Usuario sale de enlinea
		$usua->setOnline(false);
	}
	
	//Crea el nuevo LOG
	$log = new logs("LOGOUT");
	//Adiciona la transaccion
	$log->Logout();
	//Si hubo error
	if($log->nerror > 0)
		//Confirma al usuario
		$result['message'] = $log->error;
	
	//Cambia el resultado
	$result['success'] = true;
	$result['message'] = $_SESSION["LOGOUT_OK"];
	
	$idws = updateTraceWS($idws, json_encode($result));
	//Termina
	exit(json_encode($result));
?>