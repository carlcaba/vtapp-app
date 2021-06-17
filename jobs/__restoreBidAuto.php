<?
    //Inicio de sesion
    session_name('vtappcorp_session');
	session_start();

    date_default_timezone_set('America/Bogota');

	$log_file = "./my-errors.log"; 
	ini_set('display_errors', '0');
	ini_set("log_errors", TRUE);  
	ini_set('error_log', $log_file); 

	error_log("Starting job " . basename(__FILE__) . " at " . date("Ymd H:i:s"));

	$_SESSION["vtappcorp_userid"] = "admin";
	
    //Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');

    //Variable del codigo
    $result = array('success' => false,
        'message' => "");

	//Realiza la operacion
	require_once("../core/classes/user_notification.php");
	require_once("../core/classes/logs.php");
	require_once("../core/classes/configuration.php");

	$conf = new configuration("AUTOBID_ACTIVATED");
	$active = $conf->verifyValue();
	
	if(!boolval($active)) {
		error_log("AUTOBID_ACTIVATED disabled $active " . date("Ymd H:i:s"));
		$result["message"] = "AUTOBID_ACTIVATED disabled $active";
		exit(json_encode($result));
	}

	$serv = new service();

	error_log("Getting services not bidded " . date("Ymd H:i:s"));

	//Obtiene la informacion de los servicios por notificar
	$services = $serv->getNotBidded();
	
	//Si no hay servicios
	if(count($services) < 1) {
		$log = new logs("No services not bidded");
		$log->USER_ID = "admin";
		$log->_add();
		error_log($log->TEXT_TRANSACTION . " -> SQL: " . $serv->sql);
		$result["message"] = $log->TEXT_TRANSACTION;
		exit(json_encode($result));
	}
	
	$count = 0;
	$err = 0;
	$reso = new resources();
	
	error_log("Processing " . count($services) . " record(s) " . date("Ymd H:i:s"));
	
	foreach($services as $srv) {
		$count++;
		//Verifica el servicio
		$service = new service();
		$service->ID = $srv["sid"];
		$service->__getInformation();
		//Si hay error
		if($service->nerror > 0) {
			$log = new logs("Service " . $srv["sid"] . " not found -> " . $service->error);
			$log->USER_ID = "admin";
			$log->_add();
			error_log($log->TEXT_TRANSACTION . " -> SQL: " . $service->sql);
			$err++;
			//continua
			continue;
		}
		//Verifica el estado
		if($service->STATE_ID != $srv["stateid"]) {
			$log = new logs("Service " . $srv["id"] . " wrong state -> " . $service->STATE_ID . " <> " . $srv["stateid"]);
			$log->USER_ID = "admin";
			$log->_add();
			error_log($log->TEXT_TRANSACTION);
			$err++;
			//continua
			continue;
		}
		
		//Actualiza el estado
		$service->updateState($srv["new_stateid"]);
		
		//Si hay error
		if($service->nerror > 0) {
			$log = new logs("Service " . $srv["sid"] . " Error on update -> " . $service->error);
			$log->USER_ID = "admin";
			$log->_add();
			error_log($log->TEXT_TRANSACTION . " -> SQL: " . $service->sql);
			$err++;
			continue;
		}
		else {
			$log = new logs("Service " . $srv["sid"] . " Updated ok  -> State:" . $srv["service_state"] . " Reg:" . $srv["registered_on"] . " Not: " . $srv["notified_on"] . " Mins: " . $srv["minutes"]);
			$log->USER_ID = "admin";
			$log->_add();
			error_log($log->TEXT_TRANSACTION . " -> SQL: " . $service->sql);
		}
		
		//Verifica 
		$usnot = new user_notification();
		$usnot->SERVICE_ID = $srv["sid"];
		$usnot->disableNotifications();
		//Si hay error
		if($usnot->nerror > 0) {
			$log = new logs("User notification service -> " . $srv["sid"] . " error disabling -> " . $usnot->error);
			$log->USER_ID = "admin";
			$log->_add();
			error_log($log->TEXT_TRANSACTION . " -> SQL: " . $usnot->sql);
			$err++;
		}
	}
	$reso->RESOURCE_NAME = "PROCESS_COMPLETED";

	error_log($reso->getResourceByName() . "(" . $count . "/" . $err . ") at " . date("Ymd H:i:s"));

	//Cambia el resultado
	$result['success'] = true;
	$result['message'] = $reso->getResourceByName() . "(" . $count . "/" . $err . ")";
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>