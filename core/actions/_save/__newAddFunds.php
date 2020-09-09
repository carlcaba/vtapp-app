<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'payments.php');
					
	//Realiza la operacion
	require_once("../../classes/quota_employee.php");
	
	$link = "";

	//Captura las variables
	if(empty($_POST['strModel'])) {
		//Verifica el GET
		if(empty($_GET['strModel'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$strmodel = $_GET['strModel'];
			$link = $_GET['link'];
		}
	}
	else {
		$strmodel = $_POST['strModel'];
		$link = $_POST['link'];
	}
	
	$result["link"] = $link;
	
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$datas = json_decode($strmodel);
		
		//Asigna la informacion
		$quota = new quota_employee();
		
		//Asigna la informacion
		$quota->ID = $datas->txtID;
		$quota->__getInformation();
		
		//Si no hay cupo asignado
		if($quota->nerror > 0) {	
			$result["message"] = $_SESSION["NOT_REGISTERED"];
			$result["sql"] = $quota->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Actualiza el cupo usado
		$quota->AREA_ID = "NULL";
		$quota->USED += intval($datas->txtAMOUNT);
		//Modifica
		$quota->__modify(intval($datas->txtAMOUNT));
		
		//Si hay error
		if($quota->nerror > 0) {
			$result["message"] = $_SESSION["NO_DATA_FOR_UPDATE"] . "\n" . $quota->error;
			$result["sql"] = $quota->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Agrega el nuevo registro
		$quota->ID = "UUID()";
		$quota->AMOUNT = intval($datas->txtAMOUNT);
		//Verifica si es a nivel de usuario o area
		if($datas->cbUser != "") {
			$quota->setUser($datas->cbUser);
		}
		else if($datas->cbArea != "") {
			$quota->setArea($datas->cbArea);
		}
		$quota->USED = 0;
		$quota->IS_BLOCKED = "FALSE";
		
		//Agrega el registro
		$quota->_add();
			
		//Si hay error
		if($quota->nerror > 0) {
			$result["message"] = $_SESSION["NO_DATA_FOR_UPDATE"] . "\n" . $quota->error;
			$result["sql"] = $quota->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["QUOTA_REGISTERED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>);