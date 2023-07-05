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
	require_once("../core/classes/vehicle.php");
	
	//Carga los recursos
    include("../core/__load-resources.php");
	
    //Variable del codigo
    $result = array('success' => false,
					'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					'changepassword' => false,
					"description" => "");
					
	$reso = new resources(basename(__FILE__));
	$result["description"] = $reso->getResourceByName(explode(".",basename(__FILE__))[0],2);
						
	$usuario = "";
	$pass = "";
	
	$config = new configuration("DEBUGGING");
	$debug = $config->verifyValue();
	
	$idws = addTraceWS(explode(".",basename(__FILE__))[0], json_encode($_REQUEST), $uid, json_encode($result));
	
	//Captura las variables
	if($_SERVER['REQUEST_METHOD'] != 'PUT') {
		if(!isset($_POST['user'])) {
			if(!isset($_GET['user'])) {
				header("HTTP/1.1 400 Bad Request " . $result["message"]);
				exit;		
				/*
				//Termina
				exit(json_encode($result));
				*/
			}
			else {
				$usuario = $_GET['user'];
				$pass = $_GET['pass'];
			}
		}
		else {
			$usuario = $_POST['user'];
			$pass = $_POST['pass'];
		}
	} 
	else {
		//Captura las variables
		parse_str(file_get_contents("php://input"),$vars);
		$usuario = $vars['user'];
		$pass = $vars['pass'];
	}
	
	//Verifica la informacion
	if(empty($usuario)) {
		$result['message'] = $_SESSION["USERNAME_EMPTY"];
		header("HTTP/1.1 400 Bad Request " . $_SESSION["USERNAME_EMPTY"]);
		goto _Exit;		
	}
	if(empty($pass)) {
		$result['message'] = $_SESSION["PASSWORD_EMPTY"];
		header("HTTP/1.1 400 Bad Request " . $_SESSION["PASSWORD_EMPTY"]);
		goto _Exit;		
	}

	//Instancia la clase usuario
	$usua = new users($usuario);
	$conf = new configuration("WEB_SITE");
	$website = $conf->verifyValue();
	$siteroot = $conf->verifyValue("SITE_ROOT");

	//Asigna los valores
	$usua->THE_PASSWORD = $pass;

	_error_log(print_r($usua->arrColDatas,true));

		
	//Valida la contrase�a
	$usua->check(true);
	
	//Si hay error
	if($usua->nerror > 0) {
		$result['message'] = $usua->error;
		goto _Exit;		
	}
	
	_error_log("Verificar estado del usuario: " . print_r($usua->arrColDatas,true),$usua->sql);
	
	_error_log(filter_var($usua->ON_LINE, FILTER_VALIDATE_BOOLEAN));
	
	if(filter_var($usua->ON_LINE, FILTER_VALIDATE_BOOLEAN) || intval($usua->ON_LINE) == 1) {
		$result['message'] = $_SESSION["MESSENGER_LOGGED_ON"];
		goto _Exit;		
	}
	
	_error_log(filter_var($usua->LOGGED, FILTER_VALIDATE_BOOLEAN));

	if(filter_var($usua->LOGGED, FILTER_VALIDATE_BOOLEAN) || intval($usua->LOGGED) == 1) {
		$result['message'] = $_SESSION["MESSENGER_ALREADY_LOGGED"];
		goto _Exit;		
	}
	
	//Crea el nuevo LOG
	$log = new logs("LOGIN");
	$log->USER_ID = $usuario;
	//Adiciona la transaccion
	$log->Login();
	//Si hubo error
	if($log->nerror > 0)
		//Confirma al usuario
		$result['message'] = $log->error;
	
	//Cambia el resultado
	$result['success'] = true;
	$result['message'] = $_SESSION["USER_PASSWORD_OK"];
	
	//Si el usuario debe cambiar la contrase�a
	if($usua->THE_PASSWORD == $conf->verifyValue("INIT_PASSWORD") || $usua->THE_PASSWORD == $usua->ID || $usua->CHANGE_PASSWORD == 1) {
		//Confirma al usuario
		$result['changepassword'] = true;
		$result['message'] = $_SESSION["CHANGE_PASSWORD_REQUIRED"];
	}
	
	$exts = new external_session();
	
	//Actualiza la informacion
	$exts->USER_ID = $usua->ID;
	$exts->setAccess($usua->ACCESS_ID);
	$exts->REQUESTED_BY = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
	$exts->REGISTERED_BY = "Vtapp.WS";

	//Verifica si esta registrado como empleado
	require_once("../core/classes/employee.php");
	$employee = new employee();
	$employee->USER_ID = $usua->ID;
	$employee->getInformationByOtherInfo("USER_ID");
	if($employee->nerror == 0) {
		$exts->PARTNER_ID = $employee->PARTNER_ID;
	}
	
	$exts->_add();
	
	$vehi = new vehicle();
	$dataV = $vehi->showJSONListByUser($usua->IDENTIFICATION);
		
	$result["token"] = $exts->ID;
	$result["user_data"] = array("user_id" => $usua->ID,
								 "full_name" => $usua->getFullName(),
								 "personal_id" => $usua->IDENTIFICATION,
								 "email" => $usua->EMAIL,
								 "address" => $usua->ADDRESS,
								 "phone" => $usua->PHONE,
								 "cellphone" => $usua->CELLPHONE,
								 "image" => $website . $siteroot . $usua->getUserPicture(true),
								 "vehicles" => $dataV);
	
	_Exit:
	$idws = updateTraceWS($idws, json_encode($result));	
	//Termina
	exit(json_encode($result));
?>