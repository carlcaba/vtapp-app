<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					'link' => 'services-complete.php');
					
	$strdata = "";
	$gate = "";
	$pay = "";
	$ref = "";
	//Captura las variables
	if(empty($_POST['datas'])) {
		//Verifica el GET
		if(empty($_GET['datas'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$strdata = $_GET["datas"];
			$gate = $_GET["gate"];
			$pay = $_GET["payment"];
			$ref = $_GET["ref"];
		}
	}
	else {
		$strdata = $_POST["datas"];
		$gate = $_POST["gate"];
		$pay = $_POST["payment"];
		$ref = $_POST["ref"];
	}

	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        //Asigna la informacion
        $datas = json_decode($strdata);
		$pymt = json_decode($pay);
		$errors = array();
		
		//Realiza la operacion
		require_once("../../classes/service.php");
		require_once("../../classes/payment.php");
		require_once("../../classes/configuration.php");
		$conf = new configuration();

		$urlTranx = $conf->verifyValue("WEB_SITE") . $conf->verifyValue("SITE_ROOT") . $conf->verifyValue("PAYMENT_WOMPI_REDIRECT");
		
		foreach($datas as $serv) {
			if(filter_var($serv->payed, FILTER_VALIDATE_BOOLEAN))
				continue;
			//Asigna la informacion
			$service = new service();
			//Actualiza la información
			$service->ID = $serv->id;
			$service->__getInformation();
			//Si hay error
			if($service->nerror > 0) {
				//Genera el mensaje de error
				$errdat = array("service_id" => $serv->id,
								"error" => $service->error,
								"sql" => $service->sql);
				array_push($errors,$errdat);
				continue;
			}
			//Verifica la pasarela
			if($gate == "WOMPI") {
				//Asigna la informacion
				$payment = new payment();
				//Adiciona la informacion
				$payment->setClient($service->CLIENT_ID);
				$payment->REFERENCE_ID = $serv->id;
				$payment->setType($service->client->PAYMENT_TYPE_ID);
				$payment->setState(intval($payment->state->getStateByName($pymt->status)));
				$payment->TRANSACTION_ID = $pymt->id;
				$payment->GATEWAY = $gate;
				$payment->URL_GATEWAY = $urlTranx;
				$payment->IP_CLIENT = $_SERVER["REMOTE_ADDR"];
				$payment->RISK = "";
				$payment->RESPONSE = $pymt->status;
				$payment->RESPONSE_TRACE = $strdata;
				$payment->PAYMENT_METHOD = $pymt->paymentMethod->type;
				$payment->PAYMENT_METHOD_TYPE = $pymt->paymentMethodType;
				$payment->PAYMENT_REQUESTED = ($pymt->amountInCents / 100);
				$payment->PAYMENT_VALUE = ($pymt->amountInCents / 100);
				$payment->PAYMENT_TAX_PERCENT = 0;
				$payment->PAYMENT_TAX = 0;
				$payment->PAYMENT_VALUE_ADD = 0;
				$payment->AUTHORIZATION_CODE = $pymt->id;
				$payment->AUTHORIZATION_ADDITIONAL_CODE = $pymt->sessionId;
				$payment->PAYMENT_ENTITY = $pymt->paymentMethod->extra->brand;
				
				$payment->PAYER_EMAIL = $pymt->customerEmail;
				$payment->PAYER_NAME = $pymt->customerData->fullName;
				$payment->PAYER_IDENTIFICATION = $service->client->IDENTIFICATION;
				$payment->PAYER_PHONE = $pymt->customerData->phoneNumber;
				$payment->OBSERVATION = "SERVICE COMPLETED PAYED:" . $service->ID . " REF: " . $ref;
				$payment->IS_BLOCKED = "FALSE";
				//Lo adiciona
				$payment->_add();

				//Si hay error
				if($payment->nerror > 0) {
					//Genera el mensaje de error
					$errdat = array("service_id" => $serv->id,
									"error" => "PAYMENT ADD: " . $payment->error,
									"sql" => $payment->sql);
					array_push($errors,$errdat);
				}
				else {
					$service->updateState($service->state->getNextState());
					//Si hay error
					if($service->nerror > 0) {
						//Genera el mensaje de error
						$errdat = array("service_id" => $serv->id,
										"error" => "SERVICE UPDATE: " . $service->error,
										"sql" => $service->sql);
						array_push($errors,$errdat);
					}
				}
				$result["success"] = true;
			}
		}
		_error_log(print_r($errors, true) . " " . print_r(debug_backtrace(2), true));
		//Cambia el resultado
		$result["message"] = ($result["success"] ? $_SESSION["PAYMENT_SUCCESSFUL"] : $_SESSION["ERROR_ON_PAYMENT"] . " GATEWAY:$gate<br>" . $result["message"]);
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>