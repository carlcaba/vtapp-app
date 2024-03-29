<?
    //Inicio de sesion
    session_name('vtappcorp_session');
    session_start();

    //Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');

    //Variable del codigo
    $result = array('success' => false,
        'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);

	//Realiza la operacion
	require_once("../../classes/user_notification.php");
	require_once("../../classes/logs.php");

	$users = "";
	$id = "";

	//Captura las variables
    if(empty($_POST['users'])) {
        //Verifica el GET
        if(empty($_GET['users'])) {
            $result = utf8_converter($result);
            exit(json_encode($result));
        }
        else {
            $users = $_GET['users'];
			$id = $_GET['id'];
        }
    }
    else {
		$users = $_POST['users'];
		$id = $_POST['id'];
    }

    //Si es un acceso autorizado
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        //Asigna la informacion
        $datas = json_decode($users);
		
		//Verifica el servicio
		$service = new service();
		$service->ID = $id;
		$service->__getInformation();
		//Si hay error
		if($service->nerror > 0) {
			//Asigna el mensaje
			$result["message"] = "Service: " . $service->error;
			//Termina
			exit(json_encode($result));
		}
		
		//Instancia la notificacion
		$usnot = new user_notification();
		$cont = 0;
		$err = 0;
		$result["errors"] = "";
		
		foreach($datas as $value) {
			//Asigna la informacion
			$usnot->ID = 0;
			$usnot->setUser($value->uid);
			$usnot->setService($service->ID);
			$usnot->TOKEN_ID = $value->fbid;
			$usnot->STEP = 1;
			$usnot->IS_READ = "FALSE";
			//Envia la notificacion a Firebase
			$notify = $usnot->user->sendGCM($_SESSION["NEW_NOTIFICATION"] . " ID:" . $id, $id); 
			//Verifica la informacion del firebase
			if(is_object($notify)) {
				if(property_exists($notify,"multicast_id")) {
					$usnot->MULTICAST_ID = $notify->multicast_id;
					$usnot->MESSAGE_ID = $notify->results[0]->message_id;
				}
			}
			
			_error_log(print_r($notify,true));
			
			$usnot->IS_BLOCKED = ($usnot->user->nerror == 0 ? "FALSE" : "TRUE");
			//Agrega la notificacion
			$usnot->_add();
			//Si ocurre un error
			if($usnot->user->nerror > 0) {
				$log = new logs($usnot->user->error);
				$log->_add();
				$err++;
				_error_log("Notification error: " . $usnot->user->error, $usnot->user->sql);
				$result["errors"] .= "Notification error: " . $usnot->user->error . "\nSQL: " . $usnot->user->sql . "\n";
			}
			else {
				//Si hay error
				if($usnot->nerror > 0) {
					_error_log("Error add notification: " . $usnot->error, $usnot->sql);
					$result["errors"] .= "Error add notification: " . $usnot->error . "\nSQL: " . $usnot->sql . "\n";
					$err++;
				}
			}
			$cont++;
		}
		
		//Si hay alguna actualizacion
		if($cont > 0) {
			$service->updateState();
		}

        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = sprintf($_SESSION["NOTIFICATIONS_SENT"],($cont - $err), $err);
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>