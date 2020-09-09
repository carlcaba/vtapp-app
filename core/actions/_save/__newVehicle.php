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
					
	//Realiza la operacion
	require_once("../../classes/vehicle.php");
	
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
		
		$vehicle = new vehicle();

		//Verifica el user id
		$vehicle->PLATE = $datas->txtPLATE;
		//Consulta la informacion
		$vehicle->getInformationByOtherInfo();
		//Si hay error
		if($vehicle->nerror == 0) {
			$result["message"] = $_SESSION["MSG_DUPLICATED_RECORD"];
			$result["sql"] = $vehicle->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Verifica el email
		$vehicle->SERIAL_NUMBER = $datas->txtSERIAL_NUMBER;
		//Consulta la informacion
		$vehicle->getInformationByOtherInfo("SERIAL_NUMBER");
		//Si hay error
		if($vehicle->nerror == 0) {
			$result["message"] = $_SESSION["MSG_DUPLICATED_RECORD"];
			$result["sql"] = $vehicle->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Agrega el vehiculo
		$vehicle->setType($datas->cbVehicleType);
		$vehicle->setEmployee($datas->cbEmployee);
		$vehicle->BRAND = $datas->txtBRAND;
		$vehicle->MODEL = $datas->txtMODEL;
		$vehicle->YEAR = $datas->txtYEAR;
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

		$vehicle->_add();
		
		//Si hay error
		if($vehicle->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $vehicle->error;
			$result["sql"] = $vehicle->sql;
			$result = utf8_converter($result);
			//Termina
			exit(json_encode($result));
		}
		
		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["VEHICLE_REGISTERED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>
