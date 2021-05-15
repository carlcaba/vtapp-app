<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'employees.php');
					
	//Realiza la operacion
	require_once("../../classes/employee.php");
	require_once("../../classes/users.php");
	require_once("../../classes/configuration.php");
	
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
		$empl = new employee();
		$usua = new users();
		$conf = new configuration();
		
		//Verifica el user id
		$empl->USER_ID = $datas->hfIdUser;
		//Consulta la informacion
		$empl->getInformationByOtherInfo("USER_ID");
		//Si hay error
		if($empl->nerror == 0) {
			$result["message"] = $_SESSION["MSG_DUPLICATED_RECORD"];
			$result["sql"] = $empl->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Verifica el user id
		$usua->ID = $datas->hfIdUser;
		//Consulta la informacion
		$usua->__getInformation();
		//Si no es usuario nuevo
		if($datas->newEmployee) {
			//Si hay error
			if($usua->nerror == 0) {
				$result["message"] = $_SESSION["USER"] . " " . $_SESSION["MSG_DUPLICATED_RECORD"];
				$result["sql"] = $usua->sql;
				$result = utf8_converter($result);
				exit(json_encode($result));
			}
			
			$usua->EMAIL = $datas->txtEMAIL;
			//Consulta el email
			$usua->getInfoByMail();
			//Si hay error
			if($usua->nerror == 0) {
				$result["message"] = $_SESSION["USER"] . " " . $_SESSION["MSG_DUPLICATED_EMAIL"];
				$result["sql"] = $usua->sql;
				$result = utf8_converter($result);
				exit(json_encode($result));
			}
		}
		
		//Verifica el email
		$empl->EMAIL = $datas->txtEMAIL;
		//Consulta la informacion
		$empl->getInformationByOtherInfo();
		//Si hay error
		if($empl->nerror == 0) {
			$result["message"] = $_SESSION["MSG_DUPLICATED_RECORD"];
			$result["sql"] = $empl->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Verifica la identificacion
		$empl->IDENTIFICATION = $datas->cbTBL_EMPLOYEE_IDENTIFICATION . "-" . $datas->txtTBL_EMPLOYEE_IDENTIFICATION;
		//Consulta la informacion
		$empl->getInformationByOtherInfo("IDENTIFICATION");
		//Si hay error
		if($empl->nerror == 0) {
			$result["message"] = $_SESSION["MSG_DUPLICATED_RECORD"];
			$result["sql"] = $empl->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
				
		//Actualiza la información del empleado
		$empl->FIRST_NAME = $datas->txtFIRST_NAME;
		$empl->MIDDLE_NAME = $datas->txtMIDDLE_NAME;
		$empl->LAST_NAME = $datas->txtLAST_NAME;
		$empl->ADDRESS = $datas->txtADDRESS;
		$empl->PHONE = $datas->txtPHONE;
		$empl->CELLPHONE = $datas->txtCELLPHONE;
		$empl->LATITUDE = $datas->hfLATITUDE;
		$empl->LONGITUDE = $datas->hfLONGITUDE;
		$empl->setPartner($datas->cbPartner);
		$empl->setAccess($datas->cbAccess);
		$empl->setArea($datas->cbArea);
		$empl->setCity($datas->cbCity);
		$empl->RECORDS = $datas->cbRecords == "" ? "FALSE" : strtoupper($datas->cbRecords);
		$empl->IMEI = $datas->cbIMEI == "" ? "FALSE" : strtoupper($datas->cbIMEI);
		$empl->PENALTIES = $datas->cbPenalties == "" ? "FALSE" : strtoupper($datas->cbPenalties);
		$empl->IS_BLOCKED = $datas->cbBlocked == "" ? "FALSE" : strtoupper($datas->cbBlocked);

		
		if($datas->newEmployee) {
			//Actualiza la información del usuario
			$usua->setAccess($datas->cbAccess);
			$usua->CHANGE_PASSWORD = "TRUE";
			$usua->setCity($datas->cbCity);
			$usua->IDENTIFICATION = $empl->IDENTIFICATION;
			$usua->FACEBOOK_USER = "";
			$usua->GOOGLE_USER = "";
			$usua->LATITUDE = $datas->hfLATITUDE;
			$usua->LONGITUDE = $datas->hfLONGITUDE;
			$usua->ADDRESS = $datas->txtADDRESS;
			$usua->CELLPHONE = $datas->txtCELLPHONE;
			$usua->FIRST_NAME = $datas->txtFIRST_NAME;
			$usua->LAST_NAME = $datas->txtLAST_NAME;
			$usua->PHONE = $datas->txtPHONE;
			$usua->THE_PASSWORD = $conf->verifyValue("INIT_PASSWORD");
			$usua->IS_BLOCKED = $datas->cbBlocked == "" ? "FALSE" : strtoupper($datas->cbBlocked);
			
			$usua->__add("",LANGUAGE);
		}
		
		$error = false;

		//Si hay error
		if($usua->nerror > 0) {
			$result["sql"] = $usua->sql;
			//Si es error de correo
			if($usua->nerror == 18) {
				//Confirma mensaje al usuario
				$result['message'] = $usua->nerror . ". " . $usua->error;
			}
			else if($usua->nerror == 30) {
				//Confirma mensaje al usuario
				$result['message'] = $usua->nerror . ". " . $usua->error;
			}
			else {
				$result['message'] = $usua->nerror . ". " . $usua->error;
				$error = true;
			}
		}

		if($error) {
			//Confirma mensaje al usuario
			$result = utf8_converter($result);
			//Termina
			exit(json_encode($result));
		}
		
		//Lo adiciona
		$empl->_add();

		//Si hay error
		if($empl->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] .= "<br />" . $empl->error;
			$result["sql"] .= $empl->sql;
			$result = utf8_converter($result);
			//Termina
			exit(json_encode($result));
		}

		$usua->setReference($datas->cbPartner, true);

		//Cambia el resultado
		$result['success'] = true;
		$msg = $_SESSION["EMPLOYEE_REGISTERED"] . "<br />" . $_SESSION["USER_REGISTERED"];
		$result['message'] = ($error) ? $msg . "<br />" . $result['message'] : $msg;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>