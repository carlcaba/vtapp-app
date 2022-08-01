<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'service_management.php',
					"continue" => true);
					
	//Realiza la operacion
	require_once("../../classes/quota_employee.php");

	$qid = "";
	$sid = "";
	$qut = false;
	$value = 0;
	
	//Captura las variables
	if(empty($_POST['qid'])) {
		//Verifica el GET
		if(empty($_GET['qid'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$qid = $_GET['qid'];
			$sid = $_GET['sid'];
			$value = $_GET['value'];
			$qut = filter_var($_GET['payment'], FILTER_VALIDATE_BOOLEAN);
		}
	}
	else {
		$qid = $_POST['qid'];
		$sid = $_POST['sid'];
		$value = $_POST['value'];
		$qut = filter_var($_POST['payment'], FILTER_VALIDATE_BOOLEAN);
	}
	
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Verifica el tipo de transaccion
		if($qut) 
			//Asigna la informacion
			$quota = new quota();
		else 
			//Asigna la informacion
			$quota = new quota_employee();
			
		//Asigna la informacion
		$quota->ID = $qid;
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
		
		//Verifica el monto
		if($quota->AMOUNT - $quota->USED <= 0) {
			$result["message"] = $_SESSION["CLIENT_QUOTA_EMPTY"];
			$result["sql"] = $quota->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));			
		}
		
		if($quota->USED + floatval($value) > $quota->AMOUNT) {
			$result["message"] = $_SESSION["CLIENT_QUOTA_EMPTY"];
			$result["sql"] = $quota->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));			
		}
		
		//Verifica el vencimiento del medio de pago
		$arrDate = explode("/",$quota->DATE_EXPIRATION);
		$duedate = strtotime(intval($arrDate[1]) + 2000 . "-" . $arrDate[0] . "-01");
		$today = strtotime(date("Y-m-d"));
		//Si hay error
		if($duedate <= $today) {
			$result["message"] = $_SESSION["PAYMENT_METHOD_EXPIRED"] . " " . $quota->CREDIT_CARD_NUMBER . " vence en " . $quota->DATE_EXPIRATION;
			$result["sql"] = $quota->sql;

			$quota->_delete();
			$result = utf8_converter($result);
			exit(json_encode($result));			
		}		
		
		$dbg = debug_backtrace();
		$script = end($dbg);
		$result["link"] = $script["file"];
		$result["sid"] = $sid;
		$result["payment"] = "";
		
		require_once("../../classes/configuration.php");
		$conf = new configuration("PAYMENT_GATEWAY");
		$gate = $conf->verifyValue();
		
		if($qut)
			$dataObj = $quota;
		else
			$dataObj = $quota->quota;
		
		//Verifica la pasarela
		if($gate == "WOMPI") {
			//Libreria requerida
			require_once("../../classes/ws_query.php");
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
			$tokRet = getCardToken($dataObj,$urlToken,$pubkey);
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
				$result["accTok"] = $accTokRet;

				//Si no es null
				if($accTokRet["token"] != null && ($accTokRet["status"] == "CREATED" || $accTokRet["status"] == "Ok")) {
					//property_exists($accTokRet,"data") && property_exists($accTokRet["data"],"presigned_acceptance")) {
					$accTok = $accTokRet["token"]->data->presigned_acceptance->acceptance_token;
				}
			}

			//Step 3 y 4
			//Generar transaccion
			//Verifica si hay no error, para generar la transaccion
			if($result['success']) {
				$createTx = generateTransaction($dataObj, $tokRet["token"], $accTok, $urlTranx, $prvkey, $urlRet, $value);
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
				$payment->setClient($dataObj->CLIENT_ID);
				$payment->REFERENCE_ID = $sid;
				$payment->setType($dataObj->client->PAYMENT_TYPE_ID);
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
				$payment->PAYMENT_REQUESTED = $dataObj->AMOUNT;
				$payment->PAYMENT_VALUE = ($transObj->datas->amount_in_cents / 100);
				$payment->PAYMENT_TAX_PERCENT = 0;
				$payment->PAYMENT_TAX = 0;
				$payment->PAYMENT_VALUE_ADD = 0;
				$payment->AUTHORIZATION_CODE = $transObj->datas->id;
				$payment->AUTHORIZATION_ADDITIONAL_CODE = $transObj->datas->reference;
				$payment->PAYMENT_ENTITY = $transObj->datas->payment_method->type;
				
				$payment->PAYER_EMAIL = $dataObj->client->EMAIL;
				$payment->PAYER_NAME = $dataObj->client->CLIENT_NAME;
				$payment->PAYER_IDENTIFICATION = $dataObj->client->IDENTIFICATION;
				$payment->PAYER_PHONE = $dataObj->client->PHONE;
				$payment->OBSERVATION = "QUOTA PAYED BY DEMAND: $sid";
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
					$result["payment"] = $payment->ID;
					
					if($result["success"]) {
						if($dataObj->PERIOD != "" && $dataObj->PERIOD != "N") {
							$dataObj->LAST_DATE = "NOW()";
							$dataObj->_modify();
							//Si hay error
							if($dataObj->nerror > 0) {
								//Confirma mensaje al usuario
								$result['message'] = $dataObj->error;
								$result["sql"] = $dataObj->sql;
							}
						}
					}
				}
			}
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