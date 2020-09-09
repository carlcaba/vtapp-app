<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	$result = array('success' => false, 
					'message' => $_SESSION["NO_INFORMATION"],
					'link' => '');
	
	//Verifica los datos
	if(!isset($_POST['lng'])) {
		//Verifica el GET
		if(!isset($_GET['lng'])) {
			exit(json_encode($result));
		}
		else {
			$lng = $_GET['lng'];
		}
	}
	else {
		$lng = $_POST['lng'];
	}

	require_once("__check-session.php");
	
	$result = checkSession("dashboard.php",false);
	
	if(!$result["success"])
		exit(json_encode($result));
	
	$result = array('success' => false, 
					'message' => $_SESSION["NO_INFORMATION"],
					'link' => '');
	
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Realiza el cambio de lenguaje
		include_once("classes/interfaces.php");
		include_once("classes/resources.php");
		
		$config = new configuration("APP_NAME");
		define("APP_NAME", $config->verifyValue());
		//Cambia el lenguaje
		if(!defined('LANGUAGE'))
			define("LANGUAGE", $lng);
		$_SESSION["LANGUAGE"] = $lng;
		$resources = new resources();
		$resources->loadResources($_SESSION["LANGUAGE"]);

		//Exitosa
		$result["success"] = true; 
		$result["message"] = $_SESSION["LANGUAGE_CHANGED"];
		
		$_SESSION["vtappcorp_user_message"] = $result["message"];
	}
	else {
		$result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
		
		$_SESSION["vtappcorp_user_alert"] = $result["message"];
	}
	//Termina
	exit(json_encode($result));

?>