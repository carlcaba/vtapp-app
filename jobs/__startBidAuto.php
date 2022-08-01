<?
    //Inicio de sesion
    session_name('vtappcorp_session');
	session_start();

    date_default_timezone_set('America/Bogota');

	$log_file = "./my-errors.log"; 
	ini_set('display_errors', '0');
	ini_set("log_errors", TRUE);  
	ini_set('_error_log', $log_file); 

	$_SESSION["vtappcorp_userid"] = "jbStartAutoBid";
	
    //Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');

    //Variable del codigo
    $result = array('success' => false,
        'message' => "");

	//Realiza la operacion
	require_once("../core/classes/user_notification.php");
	require_once("../core/classes/logs.php");
	require_once("../core/classes/configuration.php");

	_error_log("Starting job " . basename(__FILE__) . " at " . date("Ymd H:i:s"));
	
	$log = new logs("Starting job " . basename(__FILE__) . " at " . date("Ymd H:i:s"));
	$log->USER_ID = "jbStartAutoBid";
	$log->_add();

	$conf = new configuration("AUTOBID_ACTIVATED");
	$active = $conf->verifyValue();
	
	if(!filter_var($active, FILTER_VALIDATE_BOOLEAN)) {
		_error_log("AUTOBID_ACTIVATED disabled $active " . date("Ymd H:i:s"));
		$result["message"] = "AUTOBID_ACTIVATED disabled $active";
		exit(json_encode($result));
	}

	$usua = new users();
	$serv = new user_notification();

	_error_log("Getting services " . date("Ymd H:i:s"));
	
	$log = new logs("Getting services " . date("Ymd H:i:s"));
	$log->USER_ID = "jbStartAutoBid";
	$log->_add();

	//Obtiene la informacion de los servicios por notificar
	$services = $serv->getServicesAutoBid();
	
	//Si no hay servicios
	if(count($services) < 1) {
		$log = new logs("No services for auto-notify");
		$log->USER_ID = "jbStartAutoBid";
		$log->_add();
		_error_log($log->TEXT_TRANSACTION, $serv->sql);
		$result["message"] = $log->TEXT_TRANSACTION;
		exit(json_encode($result));
	}
	
	_error_log("Getting online users " . date("Ymd H:i:s"));

	$log = new logs("Getting online users " . date("Ymd H:i:s"));
	$log->USER_ID = "jbStartAutoBid";
	$log->_add();
	
	//Obtiene la información de los usuarios en linea
	$usersTotal = $usua->getOnline("");
	
	//Si no hay usuarios en linea
	if(count($usersTotal) < 1) {
		$log = new logs("No uses online for auto-notify");
		$log->USER_ID = "jbStartAutoBid";
		$log->_add();
		_error_log($log->TEXT_TRANSACTION, $usua->sql);
		$result["message"] = $log->TEXT_TRANSACTION;
		exit(json_encode($result));
	}

	$state = $serv->service->state->getIdByStep(4);
	$count = 0;
	$err = 0;
	$reso = new resources();
	$reso->RESOURCE_NAME = "NEW_NOTIFICATION";
	
	_error_log("Processing records " . date("Ymd H:i:s"));

	$log = new logs(json_encode($usersTotal));
	$log->USER_ID = "jbStartAutoBid";
	$log->_add();

	$log = new logs(json_encode($services));
	$log->USER_ID = "jbStartAutoBid";
	$log->_add();
	
	
	foreach($services as $srv) {
		if($srv["time_elapsed"] > $srv["time_to_notify"]) {
			$count++;
			//Verifica el servicio
			$service = new service();
			$service->ID = $srv["id"];
			$service->__getInformation();
			//Si hay error
			if($service->nerror > 0) {
				$log = new logs("Service " . $srv["id"] . " not found -> " . $service->error);
				$log->USER_ID = "jbStartAutoBid";
				$log->_add();
				_error_log($log->TEXT_TRANSACTION, $service->sql);
				$err++;
				//continua
				continue;
			}
			//Verifica el estado
			if($service->STATE_ID != $state) {
				$log = new logs("Service " . $srv["id"] . " wrong state -> " . $service->STATE_ID . " <> " . $state);
				$log->USER_ID = "jbStartAutoBid";
				$log->_add();
				_error_log($log->TEXT_TRANSACTION);
				$err++;
				//continua
				continue;
			}
			$usnot = new user_notification();
			$usrcount = 0;
			
			if($srv["partner_id"] != "") {
				//Obtiene la información de los usuarios en linea
				$users = $usua->getOnline($srv["partner_id"]);
				//Si no hay usuarios en linea
				if(count($users) < 1) {
					$log = new logs("No uses online for auto-notify PARTNER: " . $srv["partner_name"]);
					$log->USER_ID = "jbStartAutoBid";
					$log->_add();
					_error_log($log->TEXT_TRANSACTION, $usua->sql);
					$err++;
					//continua
					continue;
				}
			}
			else 
				$users = $usersTotal;
			
			foreach($users as $usr) {
				//Si usuario tiene mas notificaciones de las que debería tener
				if($usr["active_notifications"] > 0 && $usr["max"] >= $usr["active_notifications"]) {
					$log = new logs("Service " . $srv["id"] . " User " . $usr["uid"] . " Notifications active: " . $usr["active_notifications"] . " / " . $usr["max"]);
					$log->USER_ID = "jbStartAutoBid";
					$log->_add();
					_error_log($log->TEXT_TRANSACTION);
					continue;
				}
				$usrcount++;
				//Asigna la informacion
				$usnot->ID = 0;
				$usnot->setUser($usr["uid"]);
				$usnot->setService($service->ID);
				$usnot->TOKEN_ID = $usr["fbid"];
				$usnot->STEP = 1;
				$usnot->IS_READ = "FALSE";
				//Envia la notificacion a Firebase
				$notify = $usnot->user->sendGCM($reso->getResourceByName() . " ID:" . $service->ID, $service->ID); 
				//Verifica la informacion del firebase
				if(is_object($notify)) {
					if(property_exists($notify,"multicast_id")) {
						$usnot->MULTICAST_ID = $notify->multicast_id;
						$usnot->MESSAGE_ID = $notify->results[0]->message_id;
					}
				}
				$usnot->IS_BLOCKED = ($usnot->user->nerror == 0 ? "FALSE" : "TRUE");
				//Agrega la notificacion
				$usnot->_add();
				//Si ocurre un error
				if($usnot->user->nerror > 0) {
					$log = new logs("Service " . $srv["id"] . " Notification error: " . $usnot->user->error);
					$log->USER_ID = "jbStartAutoBid";
					$log->_add();
					_error_log($log->TEXT_TRANSACTION);
					$err++;
					continue;
				}
				else {
					//Si hay error
					if($usnot->nerror > 0) {
						$log = new logs("Service " . $srv["id"] . " Error add notification: " . $usnot->error . " -> SQL: " . $usnot->sql);
						$log->USER_ID = "jbStartAutoBid";
						$log->_add();
						_error_log($log->TEXT_TRANSACTION);
						$err++;
						continue;
					}
				}
			}
			$log = new logs("Service " . $srv["id"] . " Users Notified : " . $usrcount);
			$log->USER_ID = "jbStartAutoBid";
			$log->_add();
			//Si hay alguna actualizacion
			if($usrcount > 0) {
				$service->updateState();
			}
		}
	}
	$reso->RESOURCE_NAME = "NOTIFICATIONS_SENT";

	//Cambia el resultado
	$result['success'] = true;
	$result['message'] = sprintf($reso->getResourceByName(),$count, $err);
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>