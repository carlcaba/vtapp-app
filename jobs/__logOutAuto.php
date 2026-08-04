<?
    //Inicio de sesion
    session_name('vtappcorp_session');
	session_start();

    date_default_timezone_set('America/Bogota');

	$_SESSION["vtappcorp_userid"] = "admin";
	
    //Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');
	
	$uid = uniqid();

    //Variable del codigo
    $result = array('success' => false,
        'message' => "");
		
	$location = explode(DIRECTORY_SEPARATOR,__DIR__);
	$removed = array_pop($location);
	array_push($location,"core","classes");
	$classDir = implode(DIRECTORY_SEPARATOR,$location);		

	//Realiza la operacion
	require_once($classDir . DIRECTORY_SEPARATOR . "users.php");
	require_once($classDir . DIRECTORY_SEPARATOR . "logs.php");
	require_once($classDir . DIRECTORY_SEPARATOR . "external_session.php");

	$log_file = __DIR__ . DIRECTORY_SEPARATOR . "my-errors.log"; 
	ini_set('display_errors', '0');
	ini_set("log_errors", TRUE);  
	ini_set('error_log', $log_file); 

	_error_log("$uid - " . "Starting job " . basename(__FILE__) . " at " . date("Ymd H:i:s"));

	$usua = new users();

	_error_log("$uid - " . "Getting connected users " . date("Ymd H:i:s"));

	//Obtiene la informacion de los usuarios conectados
	$usuarios = $usua->getConnectedUsers();
	
	//Si no hay servicios
	if(count($usuarios) < 1) {
		$log = new logs("No users for logout");
		$log->USER_ID = "admin";
		$log->_add();
		_error_log("$uid - " . $log->TEXT_TRANSACTION, $usua->sql);
		$result["message"] = $log->TEXT_TRANSACTION;
		exit(json_encode($result));
	}
	
	$count = 0;
	$err = 0;	
	_error_log("$uid - " . "Processing records " . date("Ymd H:i:s"));
	
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
				_error_log("$uid - " . $log->TEXT_TRANSACTION, $exte->sql);
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
				_error_log("$uid - " . $log->TEXT_TRANSACTION, $exte->sql);
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
		else if($usr["action"] == "LOGOUT" || $usr["action"] == "FORCE_LOGOUT") {
			$count++;
			//Verifica el usuario
			$usua->ID = $usr["uid"];
			$usua->__getInformation();
			//Si hay error
			if($usua->nerror > 0) {
				$log = new logs("User " . $usr["uid"] . " not found -> " . $usua->error);
				$log->USER_ID = "admin";
				$log->_add();
				_error_log("$uid - " . $log->TEXT_TRANSACTION, $usua->sql);
				$err++;
				//continua
				continue;
			}
			//Verifica el estado
			if(!filter_var($usua->ON_LINE, FILTER_VALIDATE_BOOLEAN)) {
				$log = new logs("User " . $usr["uid"] . " not online -> " . $usua->ONLINE);
				$log->USER_ID = "admin";
				$log->_add();
				_error_log("$uid - " . $log->TEXT_TRANSACTION);
				$err++;
				//continua
				continue;
			}
			
			$usua->setOnline(false);
			if($usua->nerror > 0) {
				$log = new logs("User " . $usr["uid"] . " couldn't be updated -> " . $usua->error . ":" . $usua->sql);
				$log->USER_ID = "admin";
				$log->_add();
				_error_log("$uid - " . $log->TEXT_TRANSACTION, $usua->sql);
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