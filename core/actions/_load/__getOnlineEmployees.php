<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/users.php");
	require_once("../../classes/service.php");
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE_RATES"]);
	
	$id = "";
	//Captura las variables
	if($_SERVER['REQUEST_METHOD'] != 'PUT') {
		if(!isset($_POST['id'])) {
			if(!isset($_GET['id'])) {
				//Termina
				goto _Exit;
			}
			else {
				$id = $_GET['id'];
			}
		}
		else {
			$id = $_POST['id'];
		}
	} 
	else {
		//Captura las variables
		parse_str(file_get_contents("php://input"),$vars);
		$id = $vars['id'];
	}

	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$serv = new service();
		$partner = $serv->getPartner($id);

		//Asigna la informacion
		$usua = new users();
		if($_SESSION["vtappcorp_useraccess"] == "GOD" || $_SESSION["vtappcorp_useraccess"] == "ADM")
			$online = $usua->getOnline("",$partner);
		else
			$online = $usua->getOnline($_SESSION['vtappcorp_referenceid'],$partner);
		$result["success"] = count($online) > 0;
		$result["data"] = $online;
		$result["counter"] = count($online);
		$result["message"] = $result["success"] ? sprintf($_SESSION["ACTIVATE_BID"],count($online)) : $_SESSION["NONE_ONLINE"];
		$result["btnText"] = $_SESSION["START_BID"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}

	_Exit:
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));

?>