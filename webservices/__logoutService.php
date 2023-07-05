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
	
	$uid = uniqid();
	
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
						
	$usuario = "";
	$token = "";
	
	$config = new configuration("DEBUGGING");
	$debug = $config->verifyValue();
	
	$idws = addTraceWS(explode(".",basename(__FILE__))[0], json_encode($_REQUEST), $uid, json_encode($result));
	
	//Captura las variables
	if($_SERVER['REQUEST_METHOD'] != 'PUT') {
		if(!isset($_POST['user'])) {
			if(!isset($_GET['user'])) {
				header("HTTP/1.1 400 Bad Request " . $result["message"]);
				goto _Exit;		
			}
			else {
				$usuario = $_GET['user'];
				$token = $_GET['token'];
			}
		}
		else {
			$usuario = $_POST['user'];
			$token = $_POST['token'];
		}
	} 
	else {
		//Captura las variables
		parse_str(file_get_contents("php://input"),$vars);
		$usuario = $vars['user'];
		$token = $vars['token'];
	}
	
	//Verifica la informacion
	if(empty($usuario)) {
		$result["message"] = $_SESSION["USERNAME_EMPTY"];
		header("HTTP/1.1 400 Bad Request " . $_SESSION["USERNAME_EMPTY"]);
		goto _Exit;		
	}
	if(empty($token)) {
		$result["message"] = $_SESSION["TOKEN_EMPTY"];
		header("HTTP/1.1 400 Bad Request " . $_SESSION["TOKEN_EMPTY"]);
		goto _Exit;		
	}

	//Instancia la clase usuario
	$usua = new users($usuario);
	$conf = new configuration("WEB_SITE");
	$website = $conf->verifyValue();
	$siteroot = $conf->verifyValue("SITE_ROOT");

	//Si hay error
	if($usua->nerror > 0) {
		$result["message"] = $usua->error;
		header("HTTP/1.1 403 Forbidden " . $usua->error);
		//Termina
		goto _Exit;
	}

	$exts = new external_session($token);
	//Busca la informacion
	$exts->__getInformation();
	
	//Si hay error
	if($exts->nerror > 0) {
		$result["message"] = $exts->error;
		header("HTTP/1.1 400 Bad Request " . $exts->error);
		//Termina
		goto _Exit;
	}
	
	//Verifica el usuario
	if($exts->USER_ID == $usua->ID) {
		//Actualiza la sesión externa
		$exts->logOut();
	}
	
	//Cambia el resultado
	$result['success'] = true;
	$result['message'] = $_SESSION["LOGOUT_OK"];
	
	_Exit:
	//Crea el nuevo LOG
	$log = new logs("LOGOUT");
	$log->USER_ID = $usuario;
	//Adiciona la transaccion
	$log->Logout();
	//Si hubo error
	if($log->nerror > 0)
		//Confirma al usuario
		$result['message'] .= $log->error;

	//Usuario sale de enlinea
	$usua->setOnline(false);

	$idws = updateTraceWS($idws, json_encode($result));
	//Termina
	exit(json_encode($result));
?>