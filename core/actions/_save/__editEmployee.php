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
		require_once("../../classes/employee.php");
		require_once("../../classes/users.php");
		
		//Asigna la informacion
		$empl = new employee();
		$empl->ID = $datas->txtID;
		//Consulta la informacion
		$empl->__getInformation();
		//Si hay error
		if($empl->nerror > 0) {
			$result["message"] = $empl->error;
			$result["sql"] = $empl->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		$user = new users($empl->USER_ID);
		
		//Actualiza la información del empleado
		$empl->EMAIL = $datas->txtEMAIL;
		$empl->IDENTIFICATION = $datas->cbTBL_EMPLOYEE_IDENTIFICATION . "-" . $datas->txtTBL_EMPLOYEE_IDENTIFICATION;
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
		
		//Lo Modifica
		$empl->_modify();
		
		//Si hay error
		if($empl->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $empl->error;
			$result["sql"] = $empl->sql;
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Verifica si el usuario existe
		if($user->nerror == 0) {
			//Actualiza la información del usuario
			$usua->EMAIL = $datas->txtEMAIL;
			$usua->setAccess($datas->cbAccess);
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
			$usua->IS_BLOCKED = $datas->cbBlocked == "" ? "FALSE" : strtoupper($datas->cbBlocked);
			
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
		}
		
		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["EMPLOYEE_MODIFIED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}

	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>