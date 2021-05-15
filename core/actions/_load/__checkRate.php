<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/partner_rate.php");
	require_once("../../classes/partner_client.php");
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE_RATES"]);
	
	$distance = "0";
	$client = "";
	$round = "false";
	$select = "true";
	//Captura las variables
	if(empty($_POST['distance'])) {
		//Verifica el GET
		if(empty($_GET['distance'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$distance = $_GET['distance'];
			$client = $_GET['client'];
			$round = $_GET['round'];
			$select = empty($_GET['select']) ? $_GET['select'] : $select;
		}
	}
	else {
		$distance = $_POST['distance'];
		$round = $_POST['round'];
		$client = $_POST["client"];
		$select = empty($_POST['select']) ? $_POST['select'] : $select;
	}
		
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$rate = new partner_rate();
		
		$filter = "";
		$result["filtered"] = false;
		$result["employee"] = false;		
		$result["employee_id"] = "";
		if($client != "") {
			$ptcl = new partner_client();
			$ptcl->setClient($client);
			//Obtiene los aliados
			$partners = $ptcl->getMyPartners();
			//Crea el filtro
			$arrFilter = array();
			$emp = array(); 
			//Verifica el resultado
			foreach($partners as $part) {
				array_push($arrFilter,$part["id"]);
				array_push($emp,$part["employee"]);
			}
			//Genera la cadena de filtro
			if(!empty($arrFilter)) {
				$filter = implode(",",$partners);
				$result["filtered"] = true;
			}
			//Genera la cadena de filtro
			if(!empty($emp)) {
				$result["employee"] = true;
				$result["employee_id"] = true;
			}
		}
		
		
		$datos = $rate->selectPartner($distance,$round == "true",$filter);
		
		if($rate->nerror > 0) {
			$result["message"] = $_SESSION["NO_INFORMATION"];
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		$result["message"] = $select = "true" ? $datos["html"] : floatval($datos["max"]);
		$result["min"] = floatval($datos["min"]);
		$result["max"] = floatval($datos["max"]);
		$result["sql"] = $datos["sql"];
		$result["success"] = true;
		
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));

?>