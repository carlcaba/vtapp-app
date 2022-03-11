<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'users-manager.php');
	
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
		require_once("../../classes/configuration.php");
		$conf = new configuration("INIT_PASSWORD");
		
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

		_error_log(print_r($datas,true));

		//Actualiza la información
		$user->setAccess($datas->cbAccess);
		$user->IS_BLOCKED = $datas->cbBlocked == "" ? "FALSE" : strtoupper($datas->cbBlocked);
		$user->CHANGE_PASSWORD = strtoupper($datas->cbChangePassword);
		$user->setCity($datas->cbCity);
		$user->IDENTIFICATION = $datas->cbTBL_SYSTEM_USER_IDENTIFICATION . "-" . $datas->txtTBL_SYSTEM_USER_IDENTIFICATION;
		if($datas->hfSocialNetwork == "true") {
			if($datas->chkUserType == "true") {
				$user->FACEBOOK_USER = $datas->txID;
				$user->GOOGLE_USER = "";
			}
			else {
				$user->FACEBOOK_USER = "";
				$user->GOOGLE_USER = $datas->txID;
			}
		}
		else {
			$user->FACEBOOK_USER = "";
			$user->GOOGLE_USER = "";
		}
		$user->LATITUDE = $datas->hfLATITUDE;
		$user->LONGITUDE = $datas->hfLONGITUDE;
		$user->ADDRESS = $datas->txtADDRESS;
		$user->CELLPHONE = $datas->txtCELLPHONE;
		$user->FIRST_NAME = $datas->txtFIRST_NAME;
		$user->LAST_NAME = $datas->txtLAST_NAME;
		$user->PHONE = $datas->txtPHONE;
		$user->THE_PASSWORD = $conf->verifyValue("INIT_PASSWORD");

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