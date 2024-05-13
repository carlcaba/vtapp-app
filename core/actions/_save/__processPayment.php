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
	$gate = "";
	$token = "";
	//Captura las variables
	if(empty($_POST['id'])) {
		//Verifica el GET
		if(empty($_GET['id'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$id = $_GET['id'];
			$gate = $_GET["gate"];
			$token = $_GET["token"];
		}
	}
	else {
		$id = $_POST['id'];
		$gate = $_POST["gate"];
		$token = $_POST["token"];
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

		//Verifica la pasarela
		if($gate == "WOMPI") {
			//Libreria requerida
			require_once("../../classes/ws_query.php");
			require_once("__wompiGatewayFunctions.php");

			require_once("../../classes/configuration.php");
			$conf = new configuration("PAYMENT_WOMPI_PUBLIC_KEY");
			$pubkey = $conf->verifyValue("");
			$prvkey = $conf->verifyValue("PAYMENT_WOMPI_PRIVATE_KEY");
			
			$urlToken = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_GETTOKEN_URL");
			$urlCheck = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_CHECK_TRANSACTION");
			$urlTranx = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_CHECK_TRANSACTION");
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
			if($token == "") {
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
					if($accTokRet["token"] != null && ($accTokRet["status"] == "CREATED" || $accTokRet["status"] == "Ok")) {
						//property_exists($accTokRet,"data") && property_exists($accTokRet["data"],"presigned_acceptance")) {
						$accTok = $accTokRet["token"]->data->presigned_acceptance->acceptance_token;
					}
				}
			}
			else {
				$accTok = $token;
			}

			//Step 3 y 4
			//Generar transaccion
			//Verifica si hay no error, para generar la transaccion
			if($result['success']) {
				$createTx = generateTransaction($quota, $tokRet["token"], $accTok, $urlTranx, $pubkey, $urlRet, $prvkey);
				//$createTx = generateTransaction($quota, $tokRet["token"], $accTok, $urlTranx, $prvkey, $urlRet);
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
				$payment->PAYMENT_METHOD_TYPE = $transObj->data->payment_method_type;
				$payment->PAYMENT_REQUESTED = $quota->AMOUNT;
				$payment->PAYMENT_VALUE = ($transObj->data->amount_in_cents / 100);
				$payment->PAYMENT_TAX_PERCENT = 0;
				$payment->PAYMENT_TAX = 0;
				$payment->PAYMENT_VALUE_ADD = 0;
				$payment->AUTHORIZATION_CODE = $transObj->data->id;
				$payment->AUTHORIZATION_ADDITIONAL_CODE = $transObj->data->reference;
				$payment->PAYMENT_ENTITY = $transObj->data->payment_method->type;
				
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