<?
    //Inicio de sesion
    session_name('vtappcorp_session');
	session_start();

    date_default_timezone_set('America/Bogota');

	$log_file = "./my-errors.log"; 
	ini_set('display_errors', '0');
	ini_set("log_errors", TRUE);  
	ini_set('_error_log', $log_file); 

	$_SESSION["vtappcorp_userid"] = "admin";
	
    //Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');

    //Variable del codigo
    $result = array('success' => false,
        'message' => "");

	//Realiza la operacion
	require_once("../core/classes/quota.php");

	_error_log("Starting job " . basename(__FILE__) . " at " . date("Ymd H:i:s"));

	$quota = new quota();

	_error_log("Getting quota with past due " . date("Ymd H:i:s"));

	//Obtiene la informacion de los cupos vencidos
	$quotas = $quota->getQuotaRepeated();
	
	//Si no hay cupos
	if(count($quotas) < 1) {
		$log = new logs("No quotas past dued");
		$log->USER_ID = "admin";
		$log->_add();
		_error_log($log->TEXT_TRANSACTION, $serv->sql);
		$result["message"] = $log->TEXT_TRANSACTION;
		exit(json_encode($result));
	}

	_error_log("Getting configuration " . date("Ymd H:i:s"));

	require_once("../core/classes/configuration.php");
	$conf = new configuration("PAYMENT_GATEWAY");
	$gate = $conf->verifyValue();

	$pubkey = $conf->verifyValue("PAYMENT_WOMPI_PUBLIC_KEY");
	$prvkey = $conf->verifyValue("PAYMENT_WOMPI_PRIVATE_KEY");
	
	$urlToken = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_GETTOKEN_URL");
	$urlAccToken = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_GET_ACCEPTANCE_TOKEN");
	$urlTranx = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_CHECK_TRANSACTION");
	$urlCheck = $conf->verifyValue("PAYMENT_WOMPI_URL") . $conf->verifyValue("PAYMENT_WOMPI_CHECK_TRANSACTION");
	$urlRet = $conf->verifyValue("WEB_SITE") . $conf->verifyValue("SITE_ROOT");
	
	//Libreria requerida
	require_once("../core/classes/ws_query.php");
	require_once("../core/actions/_save/__wompiGatewayFunctions.php");
	require_once("../core/classes/payment.php");
	
	$count = 0;
	$err = 0;	
	_error_log("Processing quotas " . date("Ymd H:i:s"));

	$success = false;
	$message = "";

	foreach($quotas as $quot) {
		$count++;
		_error_log("Processing quota " . $quot["qid"] . " start at " . date("Ymd H:i:s"));
		//Consulta la informacion del cupo
		$quota->ID = $quot["qid"];
		//Busca la informacion
		$quota->__getInformation();
		//Si hay error
		if($quota->nerror > 0) {
			$log = new logs("Quota " . $quot["qid"] . " not found -> " . $quota->error);
			$log->USER_ID = "admin";
			$log->_add();
			_error_log($log->TEXT_TRANSACTION, $quota->sql);
			$err++;
			//continua
			continue;
		}
		
		//Verifica el vencimiento del medio de pago
		$arrDate = explode("/",$quota->DATE_EXPIRATION);
		$duedate = strtotime(intval($arrDate[1]) + 2000 . "-" . $arrDate[0] . "-01");
		$today = strtotime(date("Y-m-d"));
		//Si hay error
		if($duedate <= $today) {
			$msg = "Quota " . $quot["qid"] . " card " . $quota->CREDIT_CARD_NUMBER . " expired on " . $quota->DATE_EXPIRATION;
			$quota->_delete();
			if($quota->nerror > 0) {
				$msg .= " -- " . $quota->error;
			}
			$log = new logs($msg);
			$log->USER_ID = "admin";
			$log->_add();
			_error_log($log->TEXT_TRANSACTION, $quota->sql);
			$err++;
			//continua
			continue;
		}
		
		$tokenRet = null;

		$accTokRet = null;
		$accTok = null;
		$createTx = null;
		
		//Verifica la pasarela
		if($gate == "WOMPI") {
			_error_log("Processing quota " . $quot["qid"] . " payment step 1 " . date("Ymd H:i:s"));
			//Step 1
			//Obtiene el token de la tarjeta
			$tokenRet = getCardToken($quota,$urlToken,$pubkey);
			//Actualiza la respuesta
			$success = $tokenRet["success"];
			$message = $tokenRet["message"];
			
			//Step 2
			//Proceso de solicitud de acceptance token
			//Verifica si hay no error, para obtener el acceptance token
			if($success && $tokenRet["status"] == "CREATED") {
				_error_log("Processing quota " . $quot["qid"] . " payment step 2 " . date("Ymd H:i:s"));
				//Obtiene el acceptance token
				$accTokRet = getAcceptanceToken($urlAccToken, $pubkey);

				//Actualiza la respuesta
				$success = $accTokRet["success"];
				$message = $accTokRet["message"];

				//Si no es null
				if($accTokRet["token"] != null && ($accTokRet["status"] == "CREATED" || $accTokRet["status"] == "Ok")) {
					//property_exists($accTokRet,"data") && property_exists($accTokRet["data"],"presigned_acceptance")) {
					$accTok = $accTokRet["token"]->data->presigned_acceptance->acceptance_token;
				}
				else 
					$message .= " - No token or status wrong";
			}
			else {
				$log = new logs("Quota " . $quot["qid"] . " error on payment step 1 -> " . $message);
				$log->USER_ID = "admin";
				$log->_add();
				_error_log($log->TEXT_TRANSACTION);
				$err++;
				//continua
				continue;
			}

			//Step 3 y 4
			//Generar transaccion
			//Verifica si hay no error, para generar la transaccion
			if($success) {
				_error_log("Processing quota " . $quot["qid"] . " payment step 3 and 4 " . date("Ymd H:i:s"));
				//Genera la transaccion
				$createTx = generateTransaction($quota, $tokenRet["token"], $accTok, $urlTranx, $prvkey, $urlRet);
				//Actualiza la respuesta
				$success = $createTx["success"];
				$message = $createTx["message"];
			}
			else {
				$log = new logs("Quota " . $quot["qid"] . " error on payment step 2 -> " . $message);
				$log->USER_ID = "admin";
				$log->_add();
				_error_log($log->TEXT_TRANSACTION);
				$err++;
				//continua
				continue;
			}
			
			//Step 5
			//Guarda registro del pago
			if($success) {
				_error_log("Processing quota " . $quot["qid"] . " create payment record " . date("Ymd H:i:s"));
				//Asigna la informacion
				$transObj = $createTx["transaction"];
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
				$payment->OBSERVATION = "REPEATED QUOTA:" . $quota->type->getResource();
				$payment->IS_BLOCKED = "FALSE";
				//Lo adiciona
				$payment->_add();

				//Si hay error
				if($payment->nerror > 0) {
					//Confirma mensaje al usuario
					$message = $payment->error;
					$log = new logs("Quota " . $quot["qid"] . " error registering payment step 5 -> " . $message);
					$log->USER_ID = "admin";
					$log->_add();
					_error_log($log->TEXT_TRANSACTION);
					$err++;
				}
				//Verifica el estado de la transaccion
				$checkTrx = checkTransaction($createTx["TrxId"],$urlCheck,$pubkey,$prvkey);
				$success = $checkTrx["success"];
				$message = $checkTrx["message"];
				$checkTranx = $checkTrx;
				
				if($success) {
					$quota->IS_PAYED = "TRUE";
					$quota->LAST_DATE = "NOW()";
					$quota->_modify();
					//Si hay error
					if($quota->nerror > 0) {
						//Confirma mensaje al usuario
						$message .= $quota->error;
						$result["sql"] = $quota->sql;
					}
				}
				else {
					$log = new logs("Quota " . $quot["qid"] . " error on payment step 5 -> " . $message);
					$log->USER_ID = "admin";
					$log->_add();
					_error_log($log->TEXT_TRANSACTION);
					$err++;
					//continua
					continue;
				}
			}
			else {
				$log = new logs("Quota " . $quot["qid"] . " error on payment step 3 and 4 -> " . $message);
				$log->USER_ID = "admin";
				$log->_add();
				_error_log($log->TEXT_TRANSACTION);
				$err++;
				//continua
				continue;
			}
		}
	}

	$reso = new resources();
	$reso->RESOURCE_NAME = "PROCESS_COMPLETED";

	//Cambia el resultado
	$result['success'] = $success;
	$result['message'] = $reso->getResourceByName() . " ($count cupos procesados $err errores)";
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>