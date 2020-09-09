<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/rate.php");
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE_RATES"]);
	
	$distance = "0";
	$round = "false";
	//Captura las variables
	if(empty($_POST['distance'])) {
		//Verifica el GET
		if(empty($_GET['distance'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$distance = $_GET['distance'];
			$round = $_GET['round'];
		}
	}
	else {
		$distance = $_POST['distance'];
		$round = $_POST['round'];
	}
		
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$rate = new rate();
		
		$rate->getValueByDistance($distance);
		
		if($rate->nerror > 0) {
			$result["message"] = $_SESSION["NO_INFORMATION"];
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		$result["message"] = $round == "true" ? $rate->ROUND_TRIP : $rate->RATE;
		$result["success"] = true;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));

?>