<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/quota_employee.php");
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);
	
	$userId = "";
	$valor = 0;
	//Captura las variables
	if(empty($_POST['user'])) {
		//Verifica el GET
		if(empty($_GET['user'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$userId = $_GET['user'];
			$valor = floatval($_GET['value']);
		}
	}
	else {
		$userId = $_POST['user'];
		$valor = floatval($_POST['value']);
	}
		
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$quota = new quota_employee($userId);
		
		$row = $quota->getInformationByOtherInfo();
		if($quota->nerror > 0) {
			$result["message"] = $_SESSION["EMPLOYEE_NO_QUOTA"];
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		if(($row[13] - $row[14]) < $valor) {
			$result["message"] = $_SESSION["NO_FUNDS"];
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		$data = array("id" => $row[0],
						"cc" => $row[16],
						"cn" => $row[17],
						"de" => $row[18],
						"ex" => $row[19],
						"cl" => $row[9],
						"ci" => $row[10],
						"fn" => $row[23],
						"ac" => $row[24],
						"ai" => $row[25],
						"an" => $row[26],
						"ap" => true);
		
		$result["messages"] = $data;
		$result["success"] = true;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));

	
?>