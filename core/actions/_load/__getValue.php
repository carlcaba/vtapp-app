<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/configuration.php");
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);
	
	//Captura las variables
	if(empty($_POST['value'])) {
		//Verifica el GET
		if(empty($_GET['value'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$value = $_GET['value'];
		}
	}
	else {
		$distance = $_POST['value'];
	}
		
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$conf = new configuration($value);
		
		$result["message"] = $conf->verifyValue();
		
		if($conf->nerror > 0) {
			$result["message"] = $conf->error;
		}
		$result["success"] = $conf->nerror == 0;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));

?>