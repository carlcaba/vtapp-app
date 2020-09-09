<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'quotas.php');
					
	//Realiza la operacion
	require_once("../../classes/quota.php");

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
		$result['message'] = $payment == "false" ? $_SESSION["QUOTA_REGISTERED"] : $quota->ID;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>