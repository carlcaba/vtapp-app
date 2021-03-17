<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'quotas.php',
					"continue" => true);
					
	//Realiza la operacion
	require_once("../../classes/quota.php");

	$paymentType = "false";
	
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
		$quota = new quota();
		
		//Actualiza la información
		$quota->setType($datas->cbQuotaType);
		$quota->setClient($datas->cbClient);
		$quota->AMOUNT = $datas->txtAMOUNT;
		$quota->USED = 0;
		$quota->CREDIT_CARD_NUMBER = str_replace(" ","",$datas->txtCREDIT_CARD_NUMBER);
		$quota->CREDIT_CARD_NAME = $datas->txtCREDIT_CARD_NAME;
		$quota->DATE_EXPIRATION = $datas->txtDATE_EXPIRATION;
		$quota->VERIFICATION_CODE = $datas->txtVERIFICATION_CODE;
		$quota->DIFERRED_TO = $datas->txtDIFERRED_TO;
		$quota->PAYMENT_ID = "";
		$quota->IS_PAYED = "FALSE";
		$quota->IS_VERIFIED = strtoupper($datas->hfValidCard);
		$quota->IS_BLOCKED = "FALSE";

		//Lo adiciona
		$quota->_add();

		//Si hay error
		if($quota->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $quota->error;
			$result["sql"] = $quota->sql;
			$result = utf8_converter($result);
			//Termina
			exit(json_encode($result));
		}
		
		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $paymentType == "false" ? $_SESSION["QUOTA_REGISTERED"] : $quota->ID;
		
		require_once("../../classes/configuration.php");
		$conf = new configuration("PAYMENT_GATEWAY");
		$gate = $conf->verifyValue();
		
		//Verifica la pasarela
		if($gate == "WOMPI") {
			//Libreria requerida
			require_once("__wompiGatewayFunctions.php");

			$pubkey = $conf->verifyValue("PAYMENT_WOMPI_PUBLIC_KEY");
			$prvkey = $conf->verifyValue("PAYMENT_WOMPI_PRIVATE_KEY");
			
			$urlToken = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_GETTOKEN_URL");
			$urlAccToken = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_GET_ACCEPTANCE_TOKEN");
			$urlTranx = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_CHECK_TRANSACTION");
			$urlCheck = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_CHECK_TRANSACTION");
			$urlRet = $conf->verifyValue("WEB_SITE") . $conf->verifyValue("SITE_ROOT") . $result["link"];
			
			$accTokRet = null;
			$accTok = null;
			$createTx = null;
			
			//Step 1
			//Obtiene el token de la tarjeta
			$tokRet = getCardToken($quota,$urlToken,$pubkey);
			//Actualiza la respuesta
			$result["success"] = $tokRet["success"];
			$result["message"] = $tokRet["message"];
			$result["tokenRet"] = $tokRet;
			
			//Si no hay acceptance token
			if(!property_exists($datas, "acceptance_token")) {
				//Step 2
				//Proceso de solicitud de acceptance token
				//Verifica si hay no error, para obtener el acceptance token
				if($tokRet['success'] && $tokRet["status"] == "CREATED") {
					//Obtiene el acceptance token
					$accTokRet = getAcceptanceToken($urlAccToken, $pubkey);

					//Actualiza la respuesta
					$result["success"] = $accTokRet["success"];
					$result["message"] = $accTokRet["message"];
					$result["accTok"] = $accTokRet;

					//Si no es null
					if($accTokRet["token"] != null && $accTok["status"] == "CREATED") {
						$accTok = $accTokRet["token"]->presigned_acceptance->acceptance_token;
					}
				}
			}
			else {
				$accTok = $datas->acceptance_token;
			}

			//Step 3 y 4
			//Generar transaccion
			//Verifica si hay no error, para generar la transaccion
			if($result['success']) {
				$createTx = generateTransaction($quota, $tokRet["token"], $accTok, $urlTranx, $prvkey, $urlRet);
				//Actualiza la respuesta
				$result["success"] = $createTx["success"];
				$result["message"] = $createTx["message"];
				$result["createTx"] = $createTx;
				$result["TrxId"] = $createTx["TrxId"];
			}
			
			//Step 5
			//Guarda registro del pago
			if($result["success"]) {
				$transObj = $createTx["transaction"];
				//Registra la transaccion
				require_once("../../classes/payment.php");
				//Asigna la informacion
				$payment = new payment();
			
				//Adiciona la informacion
				$payment->setClient($quota->CLIENT_ID);
				$payment->REFERENCE_ID = $quota->ID;
				$payment->setType($quota->client->PAYMENT_TYPE_ID);
				$payment->setState(intval($payment->state->getStateByName($transObj->data->status)));
				$payment->TRANSACTION_ID = $transObj->data->id;
				$payment->GATEWAY = $gate;
				$payment->URL_GATEWAY = $urlTranx;
				$payment->IP_CLIENT = $_SERVER["REMOTE_ADDR"];
				$payment->RISK = "";
				$payment->RESPONSE = $transObj->data->status;
				$payment->RESPONSE_TRACE = json_encode($transObj);
				$payment->PAYMENT_METHOD = "CreditCard";
				$payment->PAYMENT_METHOD_TYPE = $transObj->datas->payment_method_type;
				$payment->PAYMENT_REQUESTED = $quota->AMOUNT;
				$payment->PAYMENT_VALUE = ($transObj->datas->amount_in_cents / 100);
				$payment->PAYMENT_TAX_PERCENT = 0;
				$payment->PAYMENT_TAX = 0;
				$payment->PAYMENT_VALUE_ADD = 0;
				$payment->AUTHORIZATION_CODE = $transObj->datas->id;
				$payment->AUTHORIZATION_ADDITIONAL_CODE = $transObj->datas->reference;
				$payment->PAYMENT_ENTITY = $transObj->datas->payment_method->type;
				
				$payment->PAYER_EMAIL = $quota->client->EMAIL;
				$payment->PAYER_NAME = $quota->client->CLIENT_NAME;
				$payment->PAYER_IDENTIFICATION = $quota->client->IDENTIFICATION;
				$payment->PAYER_PHONE = $quota->client->PHONE;
				$payment->OBSERVATION = "QUOTA:" . $quota->type->getResource();
				$payment->IS_BLOCKED = "FALSE";
				//Lo adiciona
				$payment->_add();

				//Si hay error
				if($payment->nerror > 0) {
					//Confirma mensaje al usuario
					$result['message'] = $payment->error;
					$result["sql"] = $payment->sql;
				}
				else {
					//Verifica el estado de la transaccion
					$checkTrx = checkTransaction($result["TrxId"],$urlCheck,$pubkey,$prvkey);
					$result["success"] = $checkTrx["success"];
					$result["message"] = $checkTrx["message"];
					$result["checkTranx"] = $checkTrx;
					
					if($result["success"]) {
						$quota->IS_PAYED = "TRUE";
						$quota->_modify();
						//Si hay error
						if($quota->nerror > 0) {
							//Confirma mensaje al usuario
							$result['message'] = $quota->error;
							$result["sql"] = $quota->sql;
						}
					}
				}
			}
			$result["continue"] = false;
			$result["message"] = ($result["success"] ? $_SESSION["PAYMENT_SUCCESSFUL"] : $_SESSION["ERROR_ON_PAYMENT"] . " GATEWAY:$gate<br>" . $result["message"]);
		}
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>