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
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>