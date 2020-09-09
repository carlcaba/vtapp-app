<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'client-partner.php');

	
	//Captura las variables
	if(!isset($_POST['strModel'])) {
		//Verifica el GET
		if(!isset($_GET['strModel'])) {
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
		
		//Realiza la operacion
		require_once("../../classes/partner_client.php");
		
		//Asigna la informacion
		$partner = new partner_client();
		$partner->ID = $datas->txtID;
		//Consulta la informacion
		$partner->__getInformation();
		//Si hay error
		if($partner->nerror > 0) {
			$result["message"] = $partner->error;
			$result["sql"] = $partner->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Asigna la nueva informacion|
		$partner->setClient($datas->cbClient);
		$partner->setPartner($datas->cbPartner);
		$partner->setEmployee($datas->cbEmployee);

		//Verifica la informacion
		if($partner->checkDuplicateRecord(true)) {
			$result["message"] = $_SESSION["MSG_DUPLICATED_RECORD"];
			$result["sql"] = $partner->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		$partner->IS_BLOCKED = $datas->cbBlocked == "" ? "FALSE" : strtoupper($datas->cbBlocked);
		
		//Lo Modifica
		$partner->_modify();
		
		//Si hay error
		if($partner->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $partner->error;
			$result["sql"] = $partner->sql;
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["PARTNER_CLIENT_MODIFIED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}

	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>