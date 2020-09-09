<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'payments.php');
					
	//Realiza la operacion
	require_once("../../classes/payment.php");

	$payment = "false";
	
	//Captura las variables
	if(empty($_POST['strModel'])) {
		//Verifica el GET
		if(empty($_GET['strModel'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$strmodel = $_GET['strModel'];
			$payment = $_GET['payment'];
		}
	}
	else {
		$strmodel = $_POST['strModel'];
		$payment = $_POST['payment'];
	}
	
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$datas = json_decode($strmodel);
		
		//Asigna la informacion
		$payment = new payment();
		
		//Llamado desde payments
		if($payment == "false") {
			//Actualiza la información
			$payment->setType($datas->cbQuotaType);
			$payment->setClient($datas->cbClient);
			$payment->AMOUNT = $datas->txtAMOUNT;
			$payment->USED = 0;
			$payment->CREDIT_CARD_NUMBER = str_replace(" ","",$datas->txtCREDIT_CARD_NUMBER);
			$payment->CREDIT_CARD_NAME = $datas->txtCREDIT_CARD_NAME;
			$payment->DATE_EXPIRATION = $datas->txtDATE_EXPIRATION;
			$payment->VERIFICATION_CODE = $datas->txtVERIFICATION_CODE;
			$payment->DIFERRED_TO = $datas->txtDIFERRED_TO;
			$payment->PAYMENT_ID = "";
			$payment->IS_PAYED = "FALSE";
			$payment->IS_VERIFIED = strtoupper($datas->hfValidCard);
		}
		//Llamado callback desde la pasarela de pagos
		else {
			$result['link'] = 'quotas.php';

			require_once("../../classes/configuration.php");
			$conf = new configuration("PAYMENT_REQUEST_CHARGE");
			$urlCharge = $conf->verifyValue();

			require_once("../../classes/quota.php");
			
			$quota = new quota();
			$quota->ID = $datas->details->transactionReference;
			$quota->__getInformation();
			if($quota->nerror > 0) {
				//Confirma mensaje al usuario
				$result['message'] = $quota->error;
				$result["sql"] = $quota->sql;
				$result = utf8_converter($result);
				//Termina
				exit(json_encode($result));
			}
			
			//Adiciona la informacion
			$payment->setClient($quota->CLIENT_ID);
			$payment->REFERENCE_ID = $quota->ID;
			$payment->setType($quota->client->PAYMENT_TYPE_ID);
			$payment->setState(intval($data->details->responseCode));
			$payment->TRANSACTION_ID = $data->details->transactionId;
			$payment->GATEWAY = "Kushki";
			$payment->URL_GATEWAY = $urlCharge;
			$payment->IP_CLIENT = $data->details->ip;
			$payment->RISK = "";
			$payment->RESPONSE = "";
			$payment->RESPONSE_TRACE = $strmodel;
			$payment->PAYMENT_METHOD = "CreditCard";
			$payment->PAYMENT_METHOD_TYPE = $datas->details->paymentBrand;
			$payment->PAYMENT_REQUESTED = $datas->details->requestAmount;
			$payment->PAYMENT_VALUE = $datas->details->approvedTransactionAmount;
			$payment->PAYMENT_TAX_PERCENT= 0;
			$payment->PAYMENT_TAX= $datas->details->ivaValue;
			$payment->PAYMENT_VALUE_ADD= $datas->details->iceValue;
			$payment->AUTHORIZATION_CODE= $datas->details->ticketNumber;
			$payment->AUTHORIZATION_ADDITIONAL_CODE= $datas->details->processorId;
			$payment->PAYMENT_ENTITY= $datas->details->acquirerBank;
			
			$payment->PAYER_EMAIL = $quota->client->EMAIL;
			$payment->PAYER_NAME = $quota->client->CLIENT_NAME;
			$payment->PAYER_IDENTIFICATION = $quota->client->IDENTIFICATION;
			$payment->PAYER_PHONE = $quota->client->PHONE;
			$payment->IS_PAYED = "TRUE";
			$payment->OBSERVATION = "QUOTA:" . $quota->type->getResource();
			
		}
		$payment->IS_BLOCKED = "FALSE";
		//Lo adiciona
		$payment->_add();

		//Si hay error
		if($payment->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $payment->error;
			$result["sql"] = $payment->sql;
			$result = utf8_converter($result);
			//Termina
			exit(json_encode($result));
		}
		
		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $payment == "false" ? $_SESSION["PAYMENT_REGISTERED"] : $payment->ID;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>);