<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'vehicles.php');

	
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
		require_once("../../classes/vehicle.php");
		
		//Asigna la informacion
		$vehicle = new vehicle();
		$vehicle->ID = $datas->txtID;
		//Consulta la informacion
		$vehicle->__getInformation();
		//Si hay error
		if($vehicle->nerror > 0) {
			$result["message"] = $vehicle->error;
			$result["sql"] = $vehicle->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Actualiza la información
		$vehicle->setEmployee($datas->cbEmployee);
		$vehicle->INSURANCE_NUMBER = $datas->txtINSURANCE_NUMBER;
		$vehicle->INSURANCE_COMPANY = $datas->txtINSURANCE_COMPANY;
		$vehicle->EXPIRATION_DATE = $datas->txtEXPIRATION_DATE;
		
		$vehicle->LICENCE_NUMBER = $datas->txtLICENCE_NUMBER;
		$vehicle->LICENCE_CATEGORY_ID = $datas->txtLICENCE_CATEGORY_ID;
		$vehicle->LICENCE_EXPIRATION = $datas->txtLICENCE_EXPIRATION;
		$vehicle->TECHNICAL_REVISION = $datas->cbTechRevision == "" ? "FALSE" : strtoupper($datas->cbTechRevision);		
		$vehicle->TECHNICAL_REVISION_EXPIRATION = $datas->txtTECHNICAL_REVISION_EXPIRATION;
		$vehicle->EXPERIENCE_YEARS = $datas->txtEXPERIENCE_YEARS;
		$vehicle->setJourney($datas->cbJourney);
		$vehicle->IS_BLOCKED = $datas->cbBlocked == "" ? "FALSE" : strtoupper($datas->cbBlocked);		
		
		$vehicle->IS_BLOCKED = $datas->cbBlocked == "" ? "FALSE" : strtoupper($datas->cbBlocked);		
		
		//Lo Modifica
		$vehicle->_modify();
		
		//Si hay error
		if($vehicle->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $vehicle->error;
			$result["sql"] = $vehicle->sql;
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["VEHICLE_MODIFIED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}

	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>