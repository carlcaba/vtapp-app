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

	$id = "";
	//Captura las variables
	if(empty($_POST['id'])) {
		//Verifica el GET
		if(empty($_GET['id'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$id = $_GET['id'];
		}
	}
	else {
		$id = $_POST['id'];
	}

	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$quota = new quota();
		
		//Actualiza la información
		$quota->ID = $id;
		$quota->__getInformation();
		//Si hay error
		if($quota->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $quota->error;
			$result["sql"] = $quota->sql;
			$result = utf8_converter($result);
			//Termina
			exit(json_encode($result));
		}

		//Si ya esta pago
		if($quota->IS_PAYED) {
			//Confirma mensaje al usuario
			$result['message'] = $_SESSION["QUOTA_ALREADY_PAYED"];
			$result = utf8_converter($result);
			//Termina
			exit(json_encode($result));
		}

		require_once("../../classes/configuration.php");
		$conf = new configuration("PAYMENT_GATEWAY");
		$gate = $conf->verifyValue();
		
		//Verifica la pasarela
		if($gate == "WOMPI") {
			//Libreria requerida
			require_once("__wompiGatewayFunctions.php");

			$pubkey = $conf->verifyValue("PAYMENT_WOMPI_PUBLIC_KEY");
			
			$urlToken = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_GETTOKEN_URL");
			$urlAccToken = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_GET_ACCEPTANCE_TOKEN");
			$urlTranx = $conf->verifyValue("PAYMENT_WOMPI_CHECK_TRANSACTION");
			
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
			
			//Step 2
			//Proceso de solicitud de acceptance token
			//Verifica si hay no error, para obtener el acceptance token
			if($tokRet['success'] && $tokRet["status"] == "CREATED") {
				//Obtiene el acceptance token
				$accTokRet = getAcceptanceToken($urlAccToken, $pubkey);

				//Actualiza la respuesta
				$result["success"] = $accTokRet["success"];
				$result["message"] = $accTokRet["message"];

				//Si no es null
				if($accTokRet["token"] != null && $accTok["status"] == "CREATED") {
					$accTok = $accTokRet["token"];
				}
			}

			$result["accTok"] = $accTokRet;

			//Step 3 y 4
			//Generar transaccion
			//Verifica si hay no error, para generar la transaccion
			if($result['success']) {
				$createTx = generateTransaction($quota, $tokRet["token"], $accTok, $urlTranx);
				//Actualiza la respuesta
				$result["success"] = $tokRet["success"];
				$result["message"] = $tokRet["message"];
			}

			$result["createTx"] = $createTx;
			
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
			}
			$result["continue"] = false;
		}
		else {
			//Arma la respuesta
			$data = array("id" => $quota->ID,
							"client" => $quota->CLIENT_ID,
							"type" => $quota->QUOTA_TYPE_ID,
							"amount" => $quota->AMOUNT,
							"cc" => $quota->CREDIT_CARD_NUMBER,
							"cn" => $quota->CREDIT_CARD_NAME,
							"dex" => $quota->DATE_EXPIRATION,
							"cv" => $quota->VERIFICATION_CODE,
							"df" => $quota->DIFERRED_TO);
			//Cambia el resultado
			$result['success'] = true;
			$result['message'] = $data;
		}
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>