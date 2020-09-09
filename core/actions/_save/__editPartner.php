<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'partners.php');

	
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
		require_once("../../classes/partner.php");
		
		//Asigna la informacion
		$partner = new partner();
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
		
		//Actualiza la información
		$partner->EMAIL = $datas->txtEMAIL;
		$partner->IDENTIFICATION = $datas->cbTBL_PARTNER_IDENTIFICATION . "-" . $datas->txtTBL_PARTNER_IDENTIFICATION;
		$partner->ADDRESS = $datas->txtADDRESS;
		$partner->PHONE = $datas->txtPHONE;
		$partner->PHONE_ALT = $datas->txtPHONE_ALT;
		$partner->CELLPHONE = $datas->txtCELLPHONE;
		$partner->CELLPHONE_ALT = $datas->txtCELLPHONE_ALT;
		$partner->setCity($datas->cbCity);
		$partner->LATITUDE = $datas->hfLATITUDE;
		$partner->LONGITUDE = $datas->hfLONGITUDE;
		$partner->EMAIL_ALT = $datas->txtEMAIL_ALT;
		$partner->CONTACT_NAME = $datas->txtCONTACT_NAME;
		$partner->EMAIL_CONTACT = $datas->txtEMAIL_CONTACT;
		$partner->PHONE_CONTACT = $datas->txtPHONE_CONTACT;
		$partner->CELLPHONE_CONTACT = $datas->txtCELLPHONE_CONTACT;
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
		$result['message'] = $_SESSION["PARTNER_MODIFIED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}

	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>