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
	require_once("../../classes/service_log.php");

	//Captura las variables
    if(empty($_POST['strModel'])) {
        //Verifica el GET
        if(empty($_GET['strModel'])) {
            $result = utf8_converter($result);
            exit(json_encode($result));
        }
        else {
            $strmodel = $_GET['strModel'];
			$strpay = $_GET['strPayment'];
        }
    }
    else {
        $strmodel = $_POST['strModel'];
		$strpay = $_POST['strPayment'];
    }

    //Si es un acceso autorizado
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        //Asigna la informacion
        $datas = json_decode($strmodel);
		$data = json_decode($strpay);

		$state = new service_state($datas->hfState);
		$state->ID = $state->getIdByResource($datas->hfState);
		
		$service = new service();
		
		$service->setUser($datas->txtUSER_ID);
		$service->setClient($datas->cbClient);
		$service->REQUESTED_BY = $datas->txtREQUESTED_BY;
		$service->REQUESTED_EMAIL = $datas->txtREQUESTED_EMAIL;
		$service->REQUESTED_PHONE = $datas->txtREQUESTED_PHONE;
		$service->REQUESTED_CELLPHONE = $datas->txtREQUESTED_CELLPHONE;
		$service->REQUESTED_IP = $datas->txtREQUESTED_IP;
		$service->REQUESTED_ADDRESS = $datas->txtREQUESTED_ADDRESS;
		$service->setRequestZone($datas->cbZoneRequestSub);
		$service->DELIVER_DESCRIPTION = $datas->txtDELIVER_DESCRIPTION;
		$service->OBSERVATION = $datas->txtOBSERVATION;
		$service->DELIVER_TO = $datas->txtDELIVER_TO;
		$service->DELIVER_EMAIL = $datas->txtDELIVER_EMAIL;
		$service->DELIVER_PHONE = $datas->txtDELIVER_PHONE;
		$service->DELIVER_CELLPHONE = $datas->txtDELIVER_CELLPHONE;
		$service->DELIVER_ADDRESS = $datas->txtDELIVER_ADDRESS;
		$service->setDeliverZone($datas->cbZoneDeliverSub);
		$service->setDeliveryType($datas->cbDeliverType);
		$service->PRICE = $datas->hfPRICE;
		$service->setState($state->ID);

		$service->setVehicle($datas->cbVehicleType);
		$service->QUANTITY = $datas->txtQUANTITY;
		$service->TIME_START_TO_DELIVER = $datas->hfTimeStart;
		$service->TIME_FINISH_TO_DELIVER = $datas->hfTimeEnd;
		
		$service->FRAGILE = $datas->cbFragile ? "TRUE" : "FALSE";
		$service->ROUND_TRIP = $datas->cbRoundTrip ? "TRUE" : "FALSE";
		
		$service->_add();

		//Si se genera error
		if($service->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["SERVICES"] . ": " . $service->error . " -> " . $service->sql; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Agrega el log del servicio
		$sLog = new service_log();

		//Log
		$sLog->setService($service->ID);
		//Asigna el ultimo estado
		$sLog->setInitialState($service->state->getFirstState());
		$sLog->setFinalState($service->STATE_ID);
		//Limpia los campos no requeridos
		$sLog->ID = "UUID()";
		$sLog->EMPLOYEE_INITIAL_ID = "NULL";
		$sLog->EMPLOYEE_FINAL_ID = "NULL";
		$sLog->VEHICLE_INITIAL_ID = "";
		$sLog->VEHICLE_FINAL_ID = "";
		
		//Adiciona el log
		$sLog->__add();
		
		//Si se genera error
		if($sLog->nerror > 0) {
			$result["errorlog"] .= $_SESSION["ERROR"] . " LOG " . $_SESSION["SERVICES"] . ": " . $sLog->error . " -> " . $sLog->sql;
		}

		//Verifica si hubo pago
		if($datas->PAYED) {
			//Cambia el estado
			$service->updateState($service->service->getNextState());
			//Si se genera error
			if($service->nerror > 0) {
				$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["SERVICES"] . ": " . $service->error . " -> " . $service->sql; 
				//Termina
				$result = utf8_converter($result);
				exit(json_encode($result));
			}

			//Log
			$sLog->setService($service->ID);
			//Asigna el ultimo estado
			$sLog->setInitialState($state->ID);
			$sLog->setFinalState($service->service->getNextState());
			//Limpia los campos no requeridos
			$sLog->ID = "UUID()";
			$sLog->EMPLOYEE_INITIAL_ID = "NULL";
			$sLog->EMPLOYEE_FINAL_ID = "NULL";
			$sLog->VEHICLE_INITIAL_ID = "";
			$sLog->VEHICLE_FINAL_ID = "";
			
			//Adiciona el log
			$sLog->__add();
			
			//Si se genera error
			if($sLog->nerror > 0) {
				$result["errorlog"] .= $_SESSION["ERROR"] . " LOG " . $_SESSION["SERVICES"] . ": " . $sLog->error . " -> " . $sLog->sql;
			}
		}

		//Verifica el estado del servicio
		//EN caso que el servicio ya sea pagado
		if($datas->hfIsMarco == "off") {
			$state = new service_state($datas->hfState);
			$state->ID = $state->getIdByResource("SERVICE_STATE_2");
		}
		
        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = str_replace("%d", "", $_SESSION["SAVED"]);
		$result["link"] = "services.php";
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>