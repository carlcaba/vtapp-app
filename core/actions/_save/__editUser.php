<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'users.php');
	
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
		require_once("../../classes/users.php");
		
		//Asigna la informacion
		$user = new users($datas->txtID);
		//Consulta la informacion
		$user->__getInformation();
		
		//Si hay error
		if($user->nerror > 0) {
			$result["message"] = $user->error;
			$result["sql"] = $user->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Actualiza la información
		$usua->setAccess($datas->cbAccess);
		$usua->IS_BLOCKED = $datas->cbBlocked == "" ? "FALSE" : strtoupper($datas->cbBlocked);
		$usua->CHANGE_PASSWORD = strtoupper($datas->cbChangePassword);
		$usua->setCity($datas->cbCity);
		$usua->IDENTIFICATION = $datas->cbTBL_SYSTEM_USER_IDENTIFICATION . "-" . $datas->txtTBL_SYSTEM_USER_IDENTIFICATION;
		if($datas->hfSocialNetwork == "true") {
			if($datas->chkUserType == "true") {
				$usua->FACEBOOK_USER = $datas->txID;
				$usua->GOOGLE_USER = "";
			}
			else {
				$usua->FACEBOOK_USER = "";
				$usua->GOOGLE_USER = $datas->txID;
			}
		}
		else {
			$usua->FACEBOOK_USER = "";
			$usua->GOOGLE_USER = "";
		}
		$usua->LATITUDE = $datas->hfLATITUDE;
		$usua->LONGITUDE = $datas->hfLONGITUDE;
		$usua->ADDRESS = $datas->txtADDRESS;
		$usua->CELLPHONE = $datas->txtCELLPHONE;
		$usua->FIRST_NAME = $datas->txtFIRST_NAME;
		$usua->LAST_NAME = $datas->txtLAST_NAME;
		$usua->PHONE = $datas->txtPHONE;
		$usua->THE_PASSWORD = $conf->verifyValue("INIT_PASSWORD");

		//Lo Modifica
		$user->_modify();
		
		//Si hay error
		if($user->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $user->error;
			$result["sql"] = $user->sql;
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["USER_MODIFIED"];
		
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}

	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>