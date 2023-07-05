<?
    //Inicio de sesion
    session_name('vtappcorp_session');
	session_start();

    date_default_timezone_set('America/Bogota');

	$log_file = "./my-errors.log"; 
	ini_set('display_errors', '0');
	ini_set("log_errors", TRUE);  
	ini_set('_error_log', $log_file); 

	$_SESSION["vtappcorp_userid"] = "admin";
	
    //Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');
	
	$uid = uniqid();

    //Variable del codigo
    $result = array('success' => false,
        'message' => "");

	//Realiza la operacion
	require_once("../core/classes/users.php");
	require_once("../core/classes/logs.php");
	//Carga los recursos
    include("../core/__load-resources.php");
	
    //Variable del codigo
    $result = array('success' => false,
					'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					"description" => "");
					
	$reso = new resources();
	$result["description"] = $reso->getResourceByName(explode(".",basename(__FILE__))[0],2);

	$idws = addTraceWS(explode(".",basename(__FILE__))[0], json_encode($_REQUEST), $uid, json_encode($result));

	$usua = new users();

	_error_log("$uid - " . "Getting connected users " . date("Ymd H:i:s"));

	$pid = "";
	//Captura las variables
	if(empty($_POST['pid']))
		//Verifica el GET
		if(!empty($_GET['pid']))
			$pid = $_GET['pid'];
	else
		$pid = $_POST['pid'];

	//Obtiene la informacion de los usuarios conectados
	$usuarios = $usua->getOnlineWS($pid);
	
	//Si no hay servicios
	if(count($usuarios) < 1) {
		$log = new logs("No users online");
		$log->USER_ID = "admin";
		$log->_add();
		_error_log("$uid - " . $log->TEXT_TRANSACTION, $usua->sql);
		$result["message"] = $log->TEXT_TRANSACTION;
		//Termina
		goto _Exit;
	}
	
	$count = 0;
	_error_log("$uid - " . "Returning " . count($usuarios) . " records " . date("Ymd H:i:s"));
	
	//Cambia el resultado
	$result['success'] = true;
	$result['message'] = $usuarios;
    $result = utf8_converter($result);
	goto _Exit;
	
	_Exit:
	$idws = updateTraceWS($idws, json_encode($result));	
	//Termina
	exit(json_encode($result));
?>