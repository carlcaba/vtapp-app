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

		$service = new service();

		//Agrega el log del servicio
		$slog = new service_log();

		//Asigna el ID
		$service->ID = $datas->id;
		//Verifica la informacion
		$service->__getInformation();
		//Si hay error
		if($service->nerror > 0) {
			$result["message"] = $service->error;
            $result = utf8_converter($result);
            exit(json_encode($result));
		}

		/*
		//Log
		$slog->setService($service->ID);
		$slog->setInitialState($service->STATE_ID);
		*/
		
		$valor = floatval($datas->price);

		//Asigna la informacion enviada
		$service->setRequestZone($datas->zone_req);
		$service->setDeliverZone($datas->zone_del);
		$service->PRICE = $valor;
		$service->MODIFIED_BY = $_SESSION["vtappcorp_userid"];
		
		//Verifica si debe actualizar el cliente
		if($datas->changeclient == "true") {
			$service->setClient($datas->client);
		}
		
		$idstate = $service->state->getIdByStep(3);
		//Si hay nuevo estado
		if($idstate != "") {
			$service->setState($idstate);
		}
		
		/*
		$slog->setFinalState($service->STATE_ID);
		*/
		
		//Lo actualiza
		$service->_modify();

		//Si se genera error
		if($service->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["SERVICES"] . ": " . $service->error . " -> " . $service->sql; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		$payment = $service->client->CLIENT_PAYMENT_TYPE_ID != "1";
		
		//Si debe verificar el cupo
		if($payment && $service->client->CLIENT_PAYMENT_TYPE_ID == "2") {
			//Realiza la operacion
			require_once("../../classes/quota_employee.php");
			
			//Asigna la informacion
			$quota = new quota_employee($service->MODIFIED_BY);
			//Verifica la informacion
			$row = $quota->getInformationByOtherInfo();
			//Si no hay error
			if($quota->nerror == 0) {
				$valor = $quota->quota->type->discountType() ? $valor : 1;
				//Si el cupo es suficiente
				if(($row[13] - $row[14]) >= $valor) {
					//Actualiza la informacion
					$quota->useQuota($amount);
					if($quota->nerror > 0) {
						error_log("Error applying quota on Client Quota: " . $quota->error . "\nTrace:" . $quota->sql . " " . print_r(debug_backtrace(2), true)); 
					}
					//Asigna el valor a devolver
					$data = array("id" => $row[0],
									"cc" => $row[16],
									"cn" => $row[17],
									"de" => $row[18],
									"ex" => $row[19],
									"cl" => $row[9],
									"ci" => $row[10],
									"fn" => $row[23],
									"ac" => $row[24],
									"ai" => $row[25],
									"an" => $row[26],
									"ap" => true);
					$result["data_payment"] = $data;
					$payment = false;
				}
			}
		}
		if($payment)
			//Actualiza el servicio
			$service->updateState($service->state->getIdByStep(4));
		
		/*
		//Si hay registrado un pago
		if($payment) {
			$slog->OBSERVATION = $result["data_payment"];
		}
		//Adiciona el log
		$slog->__add();
		
		//Si se genera error
		if($slog->nerror > 0) {
			$result["errorlog"] = $_SESSION["ERROR"] . " LOG " . $_SESSION["SERVICES"] . ": " . $slog->error . " -> " . $slog->sql; 
		}
		*/
		
        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = str_replace("%d", "", $_SESSION["SAVED"]);
		$result["payment"] = $payment;
		$result["counter"] = $datas->counter;
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>