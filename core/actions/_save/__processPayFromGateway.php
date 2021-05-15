<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					'link' => 'services.php');
					
	$id = "";
	$strdata = "";
	$gate = "";
	$ref = "";
	//Captura las variables
	if(empty($_POST['id'])) {
		//Verifica el GET
		if(empty($_GET['id'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$id = $_GET['id'];
			$strdata = $_GET["strdata"];
			$gate = $_GET["gate"];
			$ref = $_GET["ref"];
		}
	}
	else {
		$id = $_POST['id'];
		$strdata = $_POST["strdata"];
		$gate = $_POST["gate"];
		$ref = $_POST["ref"];
	}

	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        //Asigna la informacion
        $datas = json_decode($strdata);
		
		//Realiza la operacion
		require_once("../../classes/service.php");
		//Asigna la informacion
		$service = new service();
		
		//Actualiza la información
		$service->ID = $id;
		$service->__getInformation();
		//Si hay error
		if($service->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $service->error;
			$result["sql"] = $service->sql;
			$result = utf8_converter($result);
			//Termina
			exit(json_encode($result));
		}

		require_once("../../classes/payment.php");
		require_once("../../classes/configuration.php");
		$conf = new configuration();

		$urlTranx = $conf->verifyValue("WEB_SITE") . $conf->verifyValue("SITE_ROOT") . $conf->verifyValue("PAYMENT_WOMPI_REDIRECT");

		//Verifica la pasarela
		if($gate == "WOMPI") {
			//Asigna la informacion
			$payment = new payment();
		
			//Adiciona la informacion
			$payment->setClient($service->CLIENT_ID);
			$payment->REFERENCE_ID = $id;
			$payment->setType($service->client->PAYMENT_TYPE_ID);
			$payment->setState(intval($payment->state->getStateByName($datas->status)));
			$payment->TRANSACTION_ID = $datas->id;
			$payment->GATEWAY = $gate;
			$payment->URL_GATEWAY = $urlTranx;
			$payment->IP_CLIENT = $_SERVER["REMOTE_ADDR"];
			$payment->RISK = "";
			$payment->RESPONSE = $datas->status;
			$payment->RESPONSE_TRACE = $strdata;
			$payment->PAYMENT_METHOD = $datas->paymentMethod->type;
			$payment->PAYMENT_METHOD_TYPE = $datas->paymentMethodType;
			$payment->PAYMENT_REQUESTED = ($datas->amountInCents / 100);
			$payment->PAYMENT_VALUE = ($datas->amountInCents / 100);
			$payment->PAYMENT_TAX_PERCENT = 0;
			$payment->PAYMENT_TAX = 0;
			$payment->PAYMENT_VALUE_ADD = 0;
			$payment->AUTHORIZATION_CODE = $datas->id;
			$payment->AUTHORIZATION_ADDITIONAL_CODE = $datas->sessionId;
			$payment->PAYMENT_ENTITY = $datas->paymentMethod->extra->brand;
			
			$payment->PAYER_EMAIL = $datas->customerEmail;
			$payment->PAYER_NAME = $datas->customerData->fullName;
			$payment->PAYER_IDENTIFICATION = $service->client->IDENTIFICATION;
			$payment->PAYER_PHONE = $datas->customerData->phoneNumber;
			$payment->OBSERVATION = "SERVICE BY DEMAND PAYED:" . $service->ID . " REF: " . $ref;
			$payment->IS_BLOCKED = "FALSE";
			//Lo adiciona
			$payment->_add();

			//Si hay error
			if($payment->nerror > 0) {
				//Confirma mensaje al usuario
				$result['message'] = "PAYMENT INSERT: " . $payment->error . " SQL: " . $payment->sql;
				$result["sql"] = $payment->sql;
				$result['success'] = false;
			}
			else {
				$result['success'] = true;
				$service->updateState($service->state->getNextState());
				//Si hay error
				if($service->nerror > 0) {
					//Confirma mensaje al usuario
					$result['message'] = "SERVICE UPDATE: " . $service->error . " SQL: " . $service->sql;
					$result["sql"] = $service->sql;
				}
			}
		}
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