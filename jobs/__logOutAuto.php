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

    //Variable del codigo
    $result = array('success' => false,
        'message' => "");

	//Realiza la operacion
	require_once("../core/classes/users.php");
	require_once("../core/classes/logs.php");
	require_once("../core/classes/external_session.php");

	_error_log("Starting job " . basename(__FILE__) . " at " . date("Ymd H:i:s"));

	$usua = new users();

	_error_log("Getting connected users " . date("Ymd H:i:s"));

	//Obtiene la informacion de los usuarios conectados
	$usuarios = $usua->getConnectedUsers();
	
	//Si no hay servicios
	if(count($usuarios) < 1) {
		$log = new logs("No users for logout");
		$log->USER_ID = "admin";
		$log->_add();
		_error_log($log->TEXT_TRANSACTION, $serv->sql);
		$result["message"] = $log->TEXT_TRANSACTION;
		exit(json_encode($result));
	}
	
	$count = 0;
	$err = 0;	
	_error_log("Processing records " . date("Ymd H:i:s"));
	
	foreach($usuarios as $usr) {
		if($usr["action"] == "LOGOUT_EXTERNAL_SESSION") {
			$count++;
			$exte = new external_session($usr["exid"]);
			//Busca la informacion
			$exte->__getInformation();
			//Si hay error
			if($exte->nerror > 0) {
				$log = new logs("User " . $usr["uid"] . " not found external session -> " . $exte->error);
				$log->USER_ID = "admin";
				$log->_add();
				_error_log($log->TEXT_TRANSACTION, $exte->sql);
				$err++;
				//continua
				continue;
			}
			//Actualiza el resultado
			$exte->logOut();
			//Si hay error
			if($exte->nerror > 0) {
				$log = new logs("User " . $usr["uid"] . " external session couldn't be updated -> " . $exte->error);
				$log->USER_ID = "admin";
				$log->_add();
				_error_log($log->TEXT_TRANSACTION, $exte->sql);
				$err++;
				//continua
				continue;
			}
			
			$log = new logs("User " . $usr["uid"] . " external session closed. Registered time: " . $usr["time"]);
			$log->USER_ID = "admin";
			$log->_add();
			
			//Flag para realizar otra accion
			$count--;
			$usr["action"] = "LOGOUT";
		}
		//Si debe realizar el logout
		if($usr["action"] == "LOGOUT") {
			$count++;
			//Verifica el usuario
			$usua->ID = $usr["uid"];
			$usua->__getInformation();
			//Si hay error
			if($usua->nerror > 0) {
				$log = new logs("User " . $usr["uid"] . " not found -> " . $usua->error);
				$log->USER_ID = "admin";
				$log->_add();
				_error_log($log->TEXT_TRANSACTION, $usua->sql);
				$err++;
				//continua
				continue;
			}
			//Verifica el estado
			if(!filter_var($usua->ON_LINE, FILTER_VALIDATE_BOOLEAN)) {
				$log = new logs("User " . $usr["uid"] . " not online -> " . $usua->ONLINE);
				$log->USER_ID = "admin";
				$log->_add();
				_error_log($log->TEXT_TRANSACTION);
				$err++;
				//continua
				continue;
			}
			
			$usua->setOnline(false);
			if($usua->nerror > 0) {
				$log = new logs("User " . $usr["uid"] . " couldn't be updated -> " . $usua->error . ":" . $usua->sql);
				$log->USER_ID = "admin";
				$log->_add();
				_error_log($log->TEXT_TRANSACTION, $usua->sql);
				$err++;
				//continua
				continue;
			}
			
			$log = new logs("User " . $usr["uid"] . " logout registered time: " . $usr["time"]);
			$log->USER_ID = "admin";
			$log->_add();
		}
	}

	$reso = new resources();
	$reso->RESOURCE_NAME = "LOGOUT_OK";

	//Cambia el resultado
	$result['success'] = true;
	$result['message'] = $reso->getResourceByName() . " ($count usuarios $err errores)";
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>