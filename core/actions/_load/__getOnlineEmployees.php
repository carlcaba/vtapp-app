<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/users.php");
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE_RATES"]);
	
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$usua = new users();
		$online = $usua->getOnline($_SESSION['vtappcorp_referenceid']);
		$result["success"] = count($online) > 0;
		$result["data"] = $online;
		$result["counter"] = count($online);
		$result["message"] = $result["success"] ? sprintf($_SESSION["ACTIVATE_BID"],count($online)) : $_SESSION["NONE_ONLINE"];
		$result["btnText"] = $_SESSION["START_BID"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));

?>