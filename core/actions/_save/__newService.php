<?
    //Inicio de sesion
    session_name('vtappcorp_session');
    session_start();

    //Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');


    //Variable del codigo
    $result = array('success' => false,
        'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
		"errorlog" => "");

	//Realiza la operacion
	require_once("../../classes/service_log.php");

	$rep = false;
	$sid = 0;
	$pym = "";

	//Captura las variables
    if(empty($_POST['strModel'])) {
        //Verifica el GET
        if(empty($_GET['strModel'])) {
            $result = utf8_converter($result);
            exit(json_encode($result));
        }
        else {
            $strmodel = $_GET['strModel'];
			$rep = filter_var($_GET['repeated'], FILTER_VALIDATE_BOOLEAN);
			$sid = intval($_GET["sid"]);
			$pym = empty($_GET['payment']) ? "" : $_GET['payment'];
        }
    }
    else {
        $strmodel = $_POST['strModel'];
		$rep = filter_var($_POST['repeated'], FILTER_VALIDATE_BOOLEAN);
		$sid = intval($_POST["sid"]);
		$pym = empty($_POST['payment']) ? "" : $_POST['payment'];
    }
	
    //Si es un acceso autorizado
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        //Asigna la informacion
        $datas = json_decode($strmodel);
		$stpAss = false;
 
		$state = new service_state($datas->hfState);
		$state->ID = $state->getIdByStep($datas->hfState);
		
		$service = new service();
		
		$service->setUser($datas->txtUSER_ID);
		$service->setClient($datas->cbClient);
		$service->REQUESTED_BY = $datas->txtREQUESTED_BY;
		$service->REQUESTED_EMAIL = $datas->txtREQUESTED_EMAIL;
		$service->REQUESTED_PHONE = $datas->txtREQUESTED_PHONE;
		$service->REQUESTED_CELLPHONE = $datas->txtREQUESTED_CELLPHONE;
		$service->REQUESTED_IP = $_SERVER["REMOTE_ADDR"];
		$service->REQUESTED_ADDRESS = $datas->txtREQUESTED_ADDRESS;
		$service->setRequestZone($datas->cbZoneRequestedSub, true);
		$service->DELIVER_DESCRIPTION = $datas->txtDELIVER_DESCRIPTION;
		$service->OBSERVATION = $datas->txtOBSERVATION;
		$service->DELIVER_TO = $datas->txtDELIVER_TO;
		$service->DELIVER_EMAIL = $datas->txtDELIVER_EMAIL;
		$service->DELIVER_PHONE = $datas->txtDELIVER_PHONE;
		$service->DELIVER_CELLPHONE = $datas->txtDELIVER_CELLPHONE;
		$service->DELIVER_ADDRESS = $datas->txtDELIVER_ADDRESS;
		$service->REQUESTED_COORDINATES = $datas->hfLATITUDE_REQUESTED_ADDRESS . "," . $datas->hfLONGITUDE_REQUESTED_ADDRESS;
		$service->DELIVER_COORDINATES = $datas->hfLATITUDE_DELIVER_ADDRESS . "," . $datas->hfLONGITUDE_DELIVER_ADDRESS;
		$service->setDeliverZone($datas->cbZoneDeliverSub, true);
		$service->setDeliveryType($datas->cbDeliverType);
		$service->PRICE = $datas->hfPRICE;
		$service->setState($state->ID);

		$service->setVehicle($datas->cbVehicleType);
		$service->QUANTITY = $datas->txtQUANTITY;
		$service->TIME_START_TO_DELIVER = $datas->hfTimeStart;
		$service->TIME_FINISH_TO_DELIVER = $datas->hfTimeEnd;
		
		$service->TOTAL_WIDTH = $datas->txtTOTAL_WIDTH;
		$service->TOTAL_HEIGHT = $datas->txtTOTAL_HEIGHT;
		$service->TOTAL_LENGTH = $datas->txtTOTAL_LENGTH;
		$service->TOTAL_WEIGHT = $datas->txtTOTAL_WEIGHT;
		
		$service->FRAGILE = $datas->cbFragile ? "TRUE" : "FALSE";
		$service->ROUND_TRIP = $datas->cbRoundTrip ? "TRUE" : "FALSE";
		
		$service->_add();

		//Si se genera error
		if($service->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["SERVICES"] . ": " . $service->error . " -> " . $service->sql; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Verifica si hay un cupo asociado
		if($datas->hfQUOTAID != "") {
			$disc = 0;
			$amount = floatval($datas->hfPRICE);
			require_once("../../classes/quota_employee.php");
			$quota = new quota_employee();
			$quota->ID = $datas->hfQUOTAID;
			$quota->__getInformation();
			if($quota->nerror > 0) {
				$quota->quota->ID = $datas->hfQUOTAID;
				$quota->quota->__getInformation();
				$disc = $quota->quota->nerror == 0 ? 1 : $disc;
			}
			else {
				$disc = -1;
			}
			if($disc == 1) {
				$amount = $quota->quota->type->discountType() ? $amount : 1;
				$quota->quota->useQuota($amount);
				if($quota->quota->nerror > 0) {
					_error_log("Error applying quota on Client Quota: " . $quota->quota->error . "\nTrace:" . $quota->quota->sql . " " . print_r(debug_backtrace(2), true)); 
				}
				$datas->hfPayed = "true";
			}
			if($disc == -1) {
				$amount = $quota->quota->type->discountType() ? $amount : 1;
				$quota->useQuota($amount);
				if($quota->nerror > 0) {
					_error_log("Error applying quota on Quota Employee: " . $quota->error . "\nTrace:" . $quota->sql . " " . print_r(debug_backtrace(2), true)); 
				}
				$datas->hfPayed = "true";
			}
			if($disc != 0) {
				require_once("../../classes/payment.php");
				$pymt = new payment();
				$pymt->setType(4);
				$pymt->setState(1);
				$pymt->setClient($datas->cbClient);
				$pymt->setReference($service->ID);
				$pymt->GATEWAY = "quota";
				$pymt->URL_GATEWAY = $_SERVER['PHP_SELF'];
				$pymt->TRANSACTION_ID = $datas->hfQUOTAID;
				$pymt->PAYMENT_METHOD = "QUOTA";
				$pymt->PAYMENT_REQUESTED = floatval($datas->hfPRICE);
				$pymt->PAYMENT_VALUE = $amount;
				$pymt->PAYMENT_TAX_PERCENT = 0;
				$pymt->PAYMENT_TAX = 0;
				$pymt->PAYMENT_VALUE_ADD = 0;
				$pymt->PAYER_EMAIL = $datas->txtREQUESTED_EMAIL;
				$pymt->PAYER_NAME = $datas->txtREQUESTED_BY;
				$pymt->PAYER_IDENTIFICATION = $service->client->IDENTIFICATION;
				$pymt->PAYER_PHONE = $datas->txtREQUESTED_CELLPHONE;
				$pymt->OBSERVATION = "Service by Quota";
				$pymt->_add();
				if($pymt->nerror > 0) {
					_error_log("Error creating payment on quota: " . $pymt->error . "\nTrace:" . $pymt->sql . " " . print_r(debug_backtrace(2), true)); 
				}
				else {
					//Pasar estado a Asignacion
					$service->updateState($service->state->getNextState(0,3));
					$stpAss = true;
				}
			}
		}
		else {
			//Pasar estado a Registrado
			$service->updateState($service->state->getNextState());
		}
		
		//Si es un pago por demanda
		if($sid != 0 && $pym != "") {
			//Verifica la informacion del pago para actualizarlo
			require_once("../../classes/payment.php");
			$pymt = new payment();
			$pymt->ID = $pym;
			$pymt->__getInformation();
			
			$pymt->REFERENCE_ID = $service->ID;
			$pymt->OBSERVATION = "QUOTA PAYED BY DEMAND: $sid";
			
			$pymt->_modify();
			//Si hay error
			if($pymt->nerror > 0) {
				_error_log("Error creating payment on quota repeated $sid // $pym: " . $pymt->error . "\nTrace:" . $pymt->sql . " " . print_r(debug_backtrace(2), true)); 
				//Confirma mensaje al usuario
				$result['message'] = $pymt->error;
				$result["sql"] = $pymt->sql;
			}
		}

		//Verifica si hubo pago
		if(filter_var($datas->hfPayed, FILTER_VALIDATE_BOOLEAN)) {
			//Pasar estado a Asignacion
			if(!$stpAss) {
				$service->updateState($service->state->getNextState(0,3));
				//Si se genera error
				if($service->nerror > 0) {
					$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["SERVICES"] . ": " . $service->error . " -> " . $service->sql; 
					//Termina
					$result = utf8_converter($result);
					exit(json_encode($result));
				}
				$stpAss = true;
			}

		}

		//Verifica el estado del servicio
		//EN caso que el servicio ya sea pagado
		if($datas->hfIsMarco == "off") {
			$state = new service_state($datas->hfState);
			$state->ID = $state->getIdByStep(2);
		}
		
		//En caso que sea Contra entrega
		if(filter_var($datas->hfPayOnDeliver, FILTER_VALIDATE_BOOLEAN)) {
			//Pasar estado a Asignacion
			$service->updateState($service->state->getNextState(0,3));
			$stpAss = true;
		}
		
		$msg = "";
		//Verifica si se asigno un aliado
		if($datas->hfPartnerId != "") {
			$lastState = $service->STATE_ID;
			//Pasa el estado a Asignado
			if(!$stpAss) {
				$service->updateState($service->state->getNextState());
				if($service->nerror > 0) {
					$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["SERVICES"] . ": " . $service->error . " -> " . $service->sql; 
					//Termina
					$result = utf8_converter($result);
					exit(json_encode($result));
				}
				$stpAss = true;
			}

			
			require_once("../../classes/partner.php");
			require_once("../../classes/configuration.php");
			require_once("../../classes/users.php");
			require_once("../../classes/assign_service.php");

			$part = new partner();
			$part->ID = $datas->hfPartnerId;
			$part->__getInformation();

			$assi = new assign_service();
			
			$assi->setService($service->ID);
			$assi->setPartner($part->ID);
			
			$assi->_add();
			
			$usua = new users();
			
			$conf = new configuration("NOTIFICATE_NEW_SERVICE");
			$sendemail = $conf->verifyValue();
			$app = $conf->verifyValue("APP_NAME");
			
			if($sendemail) {
				$mailBody = sprintf($_SESSION["NEW_SERVICE_CREATED"], $service->client->CLIENT_NAME, $app, $service->REQUESTED_ADDRESS, $service->DELIVER_ADDRESS, $service->client->CLIENT_NAME);
				$usua->sendMail($mailBody, $part->EMAIL, sprintf($_SESSION["NEW_SERVICE_CREATED_SUBJECT"], $service->client->CLIENT_NAME));
				if($usua->nerror > 0) {
					$msg = $usua->error . "<br />";
				}
			}
			
			$sendemail = $conf->verifyValue("NOTIFICATE_CUSTOMER_NEW_SERVICE");
			if($sendemail) {
				$mailBody = sprintf($_SESSION["CUSTOMER_NEW_SERVICE_CREATED"], $service->type->getResource(), $app, $service->DELIVER_ADDRESS);
				$usua->sendMail($mailBody, $service->DELIVER_EMAIL, sprintf($_SESSION["CUSTOMER_NEW_SERVICE_CREATED_SUBJECT"], $service->REQUESTED_BY, $service->type->getResource()));
				if($usua->nerror > 0) {
					$msg = ($msg != "" ? $msg . $usua->error : $usua->error) . "<br />";
				}
			}
		}
		
        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = $msg . str_replace("%d", "", $_SESSION["SAVED"]);
		$result["link"] = "services.php";
		$result["srvid"] = $service->ID;
		
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
	
?>