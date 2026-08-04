<?php

	//API para integración con UBIO
	//LOGICA ESTUDIO 2025

    //Inicio de sesion
    session_name('vtappcorp_session');
	session_start();

	//Carga los recursos
    include("../core/__load-resources.php");

    date_default_timezone_set('America/Bogota');

	$log_file = "./api-errors.log"; 
	ini_set('display_errors', '0');
	ini_set("log_errors", TRUE);  
	ini_set('_error_log', $log_file); 
	
	/*
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET, POST, PUT');	
	header("Access-Control-Max-Age: 3600");
	header("Content-Type: application/json; charset=UTF-8");
	*/
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT");
	header("Access-Control-Max-Age: 3600");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
	

	$_SESSION["vtappcorp_userid"] = "Ubio.API.Service";
	$uid = uniqid();
	
	$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	$arrURI = explode('/', $uri);

    //Variable del codigo
    $result = array('success' => false,
        'message' => "No data for validation");

	//Verifica que se llame la API
	if (!in_array("api",$arrURI)) {
		header("HTTP/1.1 404 Not Found");
		exit();
	}

	$httpcode = NULL;
	//Verifica la sesion
	include_once("__validateSession.php");
	
	if(in_array("login",$arrURI)) {
		//Incluye las clases necesarias
		require_once("../core/classes/resources.php");
		require_once("../core/classes/users.php");
		require_once("../core/classes/interfaces.php");
		require_once("../core/classes/external_session.php");
		require_once("../core/classes/configuration.php");
		require_once("../core/classes/service_log.php");
		
		//Variable del codigo
		$result = array("success" => false,
						"message" => "No data for validation",
						"changepassword" => false,
						"description" => "",
						"logMessage" => "",
						"user_data" => NULL,
						"token" => NULL);
						
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
					$httpcode = 400;
					goto _Exit;
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
			$result['message'] = "Empty user";
			$httpcode = 400;
			goto _Exit;		
		}
		if(empty($pass)) {
			$result['message'] = "Empty password";
			$httpcode = 400;
			goto _Exit;		
		}

		//Instancia la clase usuario
		$usua = new users($usuario);
		$conf = new configuration("WEB_SITE");
		$website = $conf->verifyValue();
		$siteroot = $conf->verifyValue("SITE_ROOT");
		$max_time = $conf->verifyValue("SESSION_EXPIRATION");	

		//Asigna los valores
		$usua->THE_PASSWORD = $pass;

		//Valida la contraseña
		$usua->check();
		
		//Si hay error
		if($usua->nerror > 0) {
			$result['message'] = $usua->error;
			$httpcode = 400;
			goto _Exit;		
		}
		
		if(filter_var($usua->ON_LINE, FILTER_VALIDATE_BOOLEAN) || intval($usua->ON_LINE) == 1) {
			$result['message'] = "User already logged, use previous provided token";
			$httpcode = 419;
			goto _Exit;		
		}

		//Crea el nuevo LOG
		$log = new logs("LOGIN");
		$log->USER_ID = $usuario;
		//Adiciona la transaccion
		$log->Login();
		//Si hubo error
		if($log->nerror > 0) {
			//Confirma al usuario
			$result['logMessage'] = $log->error;
			$result['logSQL'] = $log->sql;
		}
		
		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = "User logged ok";
		
		//Si el usuario debe cambiar la contraseña
		if($usua->THE_PASSWORD == $conf->verifyValue("INIT_PASSWORD") || $usua->THE_PASSWORD == $usua->ID || $usua->CHANGE_PASSWORD == 1) {
			//Confirma al usuario
			$result['changepassword'] = true;
			$result['message'] = "Change pasword required";
		}
		
		//Actualiza la informacion
		$exts = new external_session();
		$exts->USER_ID = $usua->ID;
		$exts->setAccess($usua->ACCESS_ID);
		$exts->REQUESTED_BY = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
		$exts->REGISTERED_BY = "Ubio.API.Service";

		$exts->_add();
		
		if($exts->nerror > 0) {
			$result['success'] = false;
			$result['message'] = $exts->error;
			$result["sql"] = $exts->sql;
		}
		else {
			$result["token"] = generateJWTToken($exts->ID, $idws);
			$result["user_data"] = array("user_id" => $usua->ID,
										 "full_name" => $usua->getFullName(),
										 "personal_id" => $usua->IDENTIFICATION,
										 "email" => $usua->EMAIL,
										 "address" => $usua->ADDRESS,
										 "phone" => $usua->PHONE,
										 "cellphone" => $usua->CELLPHONE,
										 "image" => $website . $siteroot . $usua->getUserPicture(true));
		}
									 
		goto _Exit;
	}

	$check = checkSession($_SERVER);
	
	//Verifica si hay error
	if(!$check["success"]) {
		//Asigna el mensaje
		$result["message"] = $check["message"];
		$httpcode = 403;
		//Termina
		goto _Exit;
	}
	
	$user = $check["login"];
	$idws = $check["idws"];
	
	//Realiza la operacion
	require_once("../core/classes/service_log.php");
	require_once("../core/classes/logs.php");
	require_once("../core/classes/users.php");
	require_once("../core/classes/configuration.php");

	$usua = new users($user);
	$usua->__getInformation();
	//Si hay error
	if($usua->nerror > 0) {
		//Asigna el mensaje
		$result["message"] = "User: " . $usua->error;
		$httpcode = 403;
		//Termina
		goto _Exit;
	}

	$method = $_SERVER['REQUEST_METHOD'];
	$input = json_decode(file_get_contents('php://input'), true);
	
	switch ($method) {
		case 'GET':
			$service = new service();
			$field = array();
			$def = "";
			$qry = $_GET["qry"];
			//Verifica el tipo de consulta
			if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				//Consulta por correo electronico
				array_push($field,"REQUESTED_EMAIL");
				array_push($field,"DELIVER_EMAIL");
			}
			else if(ctype_digit($qry)){
				if(strlen($qry) == 10) {
					//Consulta por numero de telefono
					array_push($field,"REQUESTED_CELLPHONE");
					array_push($field,"DELIVER_CELLPHONE");
				}
			}	
			if(preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', strtoupper($qry))) {
				array_push($field,"ID");
				array_push($field,"");
			}		
			else {
				//Consulta por nombre
				array_push($field,"REQUESTED_BY");
				array_push($field,"DELIVER_TO");
			}
			$service->getInformationByOtherInfo($field[0],$qry);
			//Si hay error
			if($service->nerror > 0) {
				$service->getInformationByOtherInfo($field[1],$qry);
				//Si hay error
				if($service->nerror > 0) {
					//Asigna el mensaje
					$result["message"] = "No service with " . $field[0] . " or " . $field[1] . " equals to " . $qry . " - Service: " . $service->error;
					$stCode = 404;
					//Termina
					goto _Exit;
				}
				else {
					$def = $field[1];
				}
			}
			else {
				$def = $field[0];
			}
			$data = $service->getJSONInformation();
			if($data == NULL) {
				//Asigna el mensaje
				$result["message"] = "No service found ID " . $service->ID . ": " . $service->error;
				$stCode = 404;
				//Termina
				goto _Exit;
			}
			$service_log = new service_log();
			$data["mov"] = $service_log->getJSONInformation($service->ID);
			$result["data"] = json_encode($data);
			$result["success"] = true;
			$result["description"] = $method . " " . $def . " = qry: " . $qry;
			goto _Exit;
			break;

		case 'POST':
			$id = $_POST['id'];
			$move = $_POST['mv'];
			$text = $_POST['tx'];
			break;
		case 'PUT':
			break;
		case 'DELETE':
			break;
		default:
			echo json_encode(["message" => "Invalid request method"]);
			break;
	}

	
	_error_log("$uid - " . "Service actual state");	
	

	_Exit:
	$idws = updateTraceWS($idws, json_encode($result));	
	
	if($httpcode !== NULL) {
		_http_response_code($result["message"],$httpcode);
	}
	//Termina
	exit(json_encode($result));
	
?>