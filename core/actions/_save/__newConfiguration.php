<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/configuration.php");
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'configman.php');
	
	//Captura las variables
	if(empty($_POST['strModel'])) {
		//Verifica el GET
		if(empty($_GET['strModel'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$strmodel = $_GET['strModel'];
		}
	}
	else {
		$strmodel = $_POST['strModel'];
	}
	
	$link = "resourcesman.php";
	
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
        $datas = json_decode($strmodel);
		
		//Asigna la informacion
		$conf = new configuration();
		$conf->KEY_NAME = $datas->txtKEY_NAME;
		//Verifica la informacion
		if($conf->verifyValue($datas->txtKEY_NAME) != 0)
			$result["message"] = $_SESSION["MSG_DUPLICATED_NAME"];
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Actualiza la información
		$conf->ENCRYPTED = empty($datas->chkEncrypted) ? "FALSE" : (($datas->chkEncrypted == "on") ? "TRUE" : "FALSE");
		if($conf->ENCRYPTED == "TRUE")
			$conf->KEY_VALUE = Encriptar($datas->txtKEY_VALUE);
		else
			$conf->KEY_VALUE = $datas->txtKEY_VALUE;			
		$conf->KEY_TYPE = $datas->cbKeyType;
		$conf->LOAD_INIT = 0;
		$conf->ACCESS_TO = $_SESSION['vtappcorp_useraccessid'];
		$conf->IS_BLOCKED = $datas->cbBlocked;

		$conf->_add();

		//Si hay error
		if($conf->nerror > 0) {
			$result['message'] = $conf->nerror . ". " . $conf->error;
		}

		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["CONFIGURATION_REGISTERED"];
		$result["link"] = $link;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>