<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/quota_employee.php");
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);
	
	//Captura las variables
	if(empty($_POST['datas'])) {
		//Verifica el GET
		if(empty($_GET['datass'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$strmodel = $_GET['datas'];
		}
	}
	else {
		$strmodel = $_POST['datas'];
	}
		
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		require_once("../../classes/payment.php");

        //Asigna la informacion
        $datas = json_decode($strmodel);
		$quota = new quota_employee();
		$errs = 0;
		$count = 0;
		
		foreach($datas->services as $serv) {
			$disc = 0;
			$count++;
			$amount = floatval($serv->price);
			//Asigna la informacion
			$quota->USER_ID = $serv->user;
			//Busca la informacion del usuario
			$row = $quota->getInformationByOtherInfo();
			if($quota->nerror > 0) {
				$errs++;
				continue;
			}
			else {
				$disc = -1;
			}
			
			if($disc == 1) {
				$amount = $quota->quota->type->discountType() ? $amount : 1;
				$quota->quota->useQuota($amount);
				if($quota->quota->nerror > 0) {
					error_log("Error applying quota on Client Quota: " . $quota->quota->error . "\nTrace:" . $quota->quota->sql . " " . print_r(debug_backtrace(2), true)); 
					$errs++;
					continue;
				}
				$serv->payed = true;
			}
			if($disc == -1) {
				$amount = $quota->quota->type->discountType() ? $amount : 1;
				$quota->useQuota($amount);
				if($quota->nerror > 0) {
					error_log("Error applying quota on Quota Employee: " . $quota->error . "\nTrace:" . $quota->sql . " " . print_r(debug_backtrace(2), true)); 
					$errs++;
					continue;
				}
				$serv->payed = true;
			}
			if($disc != 0) {
				//Obtiene informacion del servicio
				$service = new service();
				$service->ID = $serv->id;
				$service->__getInformation();
				//Registro del pago
				$pymt = new payment();
				$pymt->setType(4);
				$pymt->setState(1);
				$pymt->setClient($service->CLIENT_ID);
				$pymt->setReference($serv->id);
				$pymt->GATEWAY = "quota";
				$pymt->URL_GATEWAY = $_SERVER['PHP_SELF'];
				$pymt->TRANSACTION_ID = $quota->ID;
				$pymt->PAYMENT_METHOD = "QUOTA";
				$pymt->PAYMENT_REQUESTED = floatval($serv->price);
				$pymt->PAYMENT_VALUE = $amount;
				$pymt->PAYMENT_TAX_PERCENT = 0;
				$pymt->PAYMENT_TAX = 0;
				$pymt->PAYMENT_VALUE_ADD = 0;
				$pymt->PAYER_EMAIL = $service->REQUESTED_EMAIL;
				$pymt->PAYER_NAME = $service->REQUESTED_BY;
				$pymt->PAYER_IDENTIFICATION = $service->client->IDENTIFICATION;
				$pymt->PAYER_PHONE = $service->REQUESTED_CELLPHONE;
				$pymt->OBSERVATION = "Service by Quota. UID " . $datas->id;
				$pymt->_add();
				if($pymt->nerror > 0) {
					error_log("Error creating payment on quota: " . $pymt->error . "\nTrace:" . $pymt->sql . " " . print_r(debug_backtrace(2), true)); 
					$errs++;
				}
				else {
					//Pasar estado a Asignacion
					$service->updateState($service->state->getNextState());
					$serv->payed = true;
				}
			}
			//Actualiza el objeto de regreso
			if($serv->payed) {
				$serv->datapayment = array("id" => $row[0],
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
				$serv->qid = $quota->ID;
				$serv->quota = true;
			}
		}
		
		$result["messages"] = "Counter: $count -> Errors: $errs";
		$result["success"] = true;
		$result["objPay"] = $datas->services;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));

	
?>