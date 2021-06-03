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

	require_once("../../classes/service_log.php");

	$id = "";
	//Captura las variables
	if(!isset($_POST['strModel'])) {
		//Verifica el GET
		if(!isset($_GET['service'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$id = $_GET['service'];
		}
	}
	else {
		$id = $_POST['service'];
	}
	
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$serv = new service();
		//Asigna el ID
		$serv->ID = $id;
		//Consulta la informacion
		$serv->__getInformation();
		
		//Si no existe
		if($serv->nerror > 0) {
			$result["message"] = $serv->error;
			$result["sql"] = $serv->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Verifica si ya esta pago
		if($serv->isPayed()) {
			$result["message"] = $_SESSION["ALREADY_PAYED"];
			$result["sql"] = $serv->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Define el estado
		$state = new service_state();
		$state->ID = $serv->STATE_ID;

		//Cambia el estado
		$serv->updateState($serv->state->getNextState());
		//Si se genera error
		if($serv->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["SERVICES"] . ": " . $serv->error . " -> " . $serv->sql; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Agrega el log del servicio
		$sLog = new service_log();

		//Log
		$sLog->setService($serv->ID);
		//Asigna el ultimo estado
		$sLog->setInitialState($state->ID);
		$sLog->setFinalState($serv->state->getNextState());
		//Limpia los campos no requeridos
		$sLog->ID = "UUID()";
		$sLog->EMPLOYEE_INITIAL_ID = "NULL";
		$sLog->EMPLOYEE_FINAL_ID = "NULL";
		$sLog->VEHICLE_INITIAL_ID = "";
		$sLog->VEHICLE_FINAL_ID = "";
		
		//Adiciona el log
		$sLog->__add();
		
		//Si se genera error
		if($sLog->nerror > 0) {
			$result["errorlog"] = $_SESSION["ERROR"] . " LOG " . $_SESSION["SERVICES"] . ": " . $sLog->error . " -> " . $sLog->sql;
		}
		
		//Realiza el pago
		require_once("../../classes/payment.php");		
		//Asigna la informacion
		$payment = new payment();
		
		//Adiciona la informacion
		$payment->setClient($serv->CLIENT_ID);
		$payment->REFERENCE_ID = $serv->ID;
		$payment->setType(4);
		$payment->setState(1);
		$payment->TRANSACTION_ID = "123456";
		$payment->GATEWAY = "Automatic";
		$payment->URL_GATEWAY = "";
		$payment->IP_CLIENT = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
		$payment->RISK = "";
		$payment->RESPONSE = "";
		$payment->RESPONSE_TRACE = "";
		$payment->PAYMENT_METHOD = "AutoMoney";
		$payment->PAYMENT_METHOD_TYPE = "Auto";
		$payment->PAYMENT_REQUESTED = $serv->PRICE;
		$payment->PAYMENT_VALUE = $serv->PRICE;
		$payment->PAYMENT_TAX_PERCENT= 0;
		$payment->PAYMENT_TAX= 0;
		$payment->PAYMENT_VALUE_ADD = 0;
		$payment->AUTHORIZATION_CODE= "123465";
		$payment->AUTHORIZATION_ADDITIONAL_CODE= "123456";
		$payment->PAYMENT_ENTITY= "Automatic";
		
		$payment->PAYER_EMAIL = $serv->REQUESTED_EMAIL;
		$payment->PAYER_NAME = $serv->REQUESTED_BY;
		$payment->PAYER_IDENTIFICATION = "";
		$payment->PAYER_PHONE = $serv->REQUESTED_PHONE;

		$payment->OBSERVATION = "Automatic Payment by god user";
		$payment->IS_BLOCKED = "FALSE";
		//Lo adiciona
		$payment->_add();

		//Si hay error
		if($payment->nerror > 0) {
			//Confirma mensaje al usuario
			$result["errorpayment"] = $_SESSION["ERROR"] . " PAYMENT " . $_SESSION["SERVICES"] . ": " . $payment->error . " -> " . $payment->sql;
		}
		
		$result["success"] = true;
		$result["message"] = $_SESSION["SERVICE"] . " " . $_SESSION["PAYED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>
