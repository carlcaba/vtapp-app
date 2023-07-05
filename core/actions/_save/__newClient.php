<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'clients.php');
					
	//Realiza la operacion
	require_once("../../classes/client.php");
	
	//Captura las variables
	if(empty($_POST['strModel'])) {
		//Verifica el GET
		if(empty($_GET['strModel'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$strmodel = $_GET['strModel'];
		}
	}
	else {
		$strmodel = $_POST['strModel'];
	}
	
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$datas = json_decode($strmodel);
		
		//Asigna la informacion
		$client = new client();
		
		//Verifica el user id
		$client->CLIENT_NAME = $datas->txtCLIENT_NAME;
		//Consulta la informacion
		$client->getInformationByOtherInfo();
		//Si hay error
		if($client->nerror == 0) {
			$result["message"] = $_SESSION["MSG_DUPLICATED_RECORD"];
			$result["sql"] = $client->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Verifica el email
		$client->EMAIL = $datas->txtEMAIL;
		//Consulta la informacion
		$client->getInformationByOtherInfo("EMAIL");
		//Si hay error
		if($client->nerror == 0) {
			$result["message"] = $_SESSION["MSG_DUPLICATED_RECORD"];
			$result["sql"] = $client->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Actualiza la información
		$client->setClientType($datas->cbClientType);
		$client->setClientPaymentType($datas->cbClientPaymentType);
		$client->IDENTIFICATION = $datas->cbTBL_CLIENT_IDENTIFICATION . "-" . $datas->txtTBL_CLIENT_IDENTIFICATION;
		$client->ADDRESS = $datas->txtADDRESS;
		$client->PHONE = $datas->txtPHONE;
		$client->PHONE_ALT = $datas->txtPHONE_ALT;
		$client->CELLPHONE = $datas->txtCELLPHONE;
		$client->CELLPHONE_ALT = $datas->txtCELLPHONE_ALT;
		$client->setCity($datas->cbCity);
		$client->LATITUDE = $datas->hfLATITUDE;
		$client->LONGITUDE = $datas->hfLONGITUDE;
		$client->EMAIL_ALT = $datas->txtEMAIL_ALT;
		$client->CONTACT_NAME = $datas->txtCONTACT_NAME;
		$client->EMAIL_CONTACT = $datas->txtEMAIL_CONTACT;
		$client->PHONE_CONTACT = $datas->txtPHONE_CONTACT;
		$client->CELLPHONE_CONTACT = $datas->txtCELLPHONE_CONTACT;
		$client->IS_BLOCKED = $datas->cbBlocked == "" ? "FALSE" : strtoupper($datas->cbBlocked);

		//Lo adiciona
		$client->_add();

		//Si hay error
		if($client->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $client->error;
			$result["sql"] = $client->sql;
			$result = utf8_converter($result);
			//Termina
			exit(json_encode($result));
		}
		
		$error = false;
		//Si tiene un aliado asociado
		if($datas->hfIdAliado != "" && $datas->hfPartner) {
			//Realiza la operacion
			require_once("../../classes/partner_client.php");
			$partner = new partner_client();
			//Asigna la informacion
			$partner->setClient($client->ID);
			$partner->setPartner($datas->hfIdAliado);
			
			//Lo adiciona
			$partner->_add();

			//Si hay error
			if($partner->nerror > 0) {
				//Confirma mensaje al usuario
				$result['message'] = $partner->error;
				$result["sql"] = $partner->sql;
				$error = true;
			}
		}
		
		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["CLIENT_REGISTERED"];
		$result['message'] .= ($error ? $partner->error : "");
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>