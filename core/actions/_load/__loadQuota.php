<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/quota_type.php");
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);
	
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$quota = new quota_type();
		
		$result["message"] = $quota->showOptionListJson();
		
		if($quota->nerror > 0) {
			$result["message"] = $quota->error;
		}
		$result["success"] = $quota->nerror == 0;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));

?>