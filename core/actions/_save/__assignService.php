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
	require_once("../../classes/service.php");

	//Captura las variables
    if(empty($_POST['strModel'])) {
        //Verifica el GET
        if(empty($_GET['strModel'])) {
            $result = utf8_converter($result);
            exit(json_encode($result));
        }
        else {
            $strmodel = $_GET['strModel'];
        }
    }
    else {
        $strmodel = $_POST['strModel'];
    }

    //Si es un acceso autorizado
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        //Asigna la informacion
        $datas = json_decode($strmodel);

		//Instancia la clase
		require_once("../../classes/service_log.php");

		$total = intval($datas->hfCounterData);
		$errors = 0;
		$counter = 0;
		for($i = 0; $i <= $total; $i++) {
			//Verifica los datos
			if($datas->{"hfIdEmployee_" . $i} == "")
				continue;

			$counter++;

			//Instancia la clase
			$service = new service(); 
			$service->ID = $datas->{"hfIdService_" . $i};
			
			//Busca la informacion
			$service->__getInformation();
			//Si hay error
			if($service->nerror > 0) {
				$result["errorlog"] .= $_SESSION["ERROR"] . " " . $_SESSION["SERVICES"] . ": " . $service->error . " -> " . $service->sql; 
				$errors++;
				continue;
			}
			
			//Busca el siguiente estado
			$idstate = $service->state->getNextState();

			//Agrega el log del servicio
			$sLog = new service_log();

			//Log
			$sLog->setService($service->ID);
			//Busca el ultimo log
			$sLog->getLastLog();
			//Asigna el ultimo estado
			$sLog->setInitialState($service->STATE_ID);
			//Limpia los campos no requeridos
			$sLog->ID = "UUID()";
			$sLog->EMPLOYEE_INITIAL_ID = "NULL";
			$sLog->setFinalEmployee($datas->{"hfIdEmployee_" . $i});
			$sLog->VEHICLE_INITIAL_ID = "";
			$sLog->VEHICLE_FINAL_ID = "";
			
			//Asigna la informacion
			//Si hay nuevo estado
			if($idstate != "") {
				$service->setState($idstate);
			}
			$sLog->setFinalState($service->STATE_ID);
			$service->MODIFIED_BY = $_SESSION["vtappcorp_userid"];
			
			//Actualiza el servicio
			$service->_modify();

			//Si se genera error
			if($service->nerror > 0) {
				$result["errorlog"] .= $_SESSION["ERROR"] . " " . $_SESSION["SERVICES"] . ": " . $service->error . " -> " . $service->sql; 
				$errors++;
				continue;
			}			

			//Adiciona el log
			$sLog->__add();
			
			//Si se genera error
			if($sLog->nerror > 0) {
				$result["errorlog"] .= $_SESSION["ERROR"] . " LOG " . $_SESSION["SERVICES"] . ": " . $sLog->error . " -> " . $sLog->sql;
				$error++;
			}
		}

		//Verifica el total de registros
		if($counter - $errors > 0) {
			require_once("../../classes/notification.php");
			//Agrega las notificaciones
			$notification = new notification($_SESSION["vtappcorp_userid"]);

			//Verifica el tipo
			$notification->type->TEXT_TYPE = "info";
			$notification->type->getInformationByOtherInfo();
			//Agrega la notificacion
			$notification->setType($notification->type->ID);
			$notification->MESSAGE = str_replace("{0}",($counter-$errors),$_SESSION["SERVICE_ASSIGNED_MESSAGE"]);
			$notification->MESSAGE = str_replace("{1}","services.php",$notification->MESSAGE);
			$notification->SOURCE = "myclients.php";
			//La agrega
			$notification->_add(false,false);
		}

        //Cambia el resultado
        $result['success'] = $errors == 0;
        $result['message'] = sprintf($_SESSION["UPDATED_RECORDS"],($counter - $errors), $counter);
		$result["link"] = "myclients.php";
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>