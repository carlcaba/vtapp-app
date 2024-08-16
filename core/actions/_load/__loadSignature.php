<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');		
				
	//Variable del codigo
	$result = array('success' => false,
		'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);

	$ref = "";
	$value = 0;
	$currency = "COP";

	//Captura las variables
	if(empty($_POST['ref'])) {
		//Verifica el GET
		if(!empty($_GET['ref'])) {
			$ref = $_GET['ref'];
			$value = !empty($_GET['value']) ? $_GET['value'] : $value;
			$currency = !empty($_GET['currency']) ? $_GET['currency'] : $currency;
		}
		else {
			//Termina
			exit($result);
		}
	}
	else {
		$ref = $_POST['ref'];		
		$value = !empty($_POST['value']) ? $_POST['value'] : $value;
		$currency = !empty($_POST['currency']) ? $_POST['currency'] : $currency;
}

	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		require_once("../_save/__wompiGatewayFunctions.php");

		$result["message"] = getIntegrityKey($ref,(round(floatval($value),2) * 100),$currency);
		$result["success"] = true;
	}
	else {
		$result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));

?>
