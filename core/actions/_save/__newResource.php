<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/resources.php");
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'resourcesman.php');
	
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
		$reso = new resources();
		$reso->RESOURCE_NAME = $datas->txtRESOURCE_NAME;
		//Verifica la informacion
		$reso->getResourceObjByName("",$datas->cbLanguage);
		//Si hay error
		if($reso->nerror == 0) {
			$result["message"] = $_SESSION["MSG_DUPLICATED_NAME"];
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Actualiza la información
		$reso->LANGUAGE_ID = $datas->cbLanguage;
		$reso->RESOURCE_TEXT = htmlentities(htmlspecialchars($datas->txtRESOURCE_TEXT));
		$reso->IS_SYSTEM = empty($datas->chkSystem) ? "FALSE" : (($datas->chkSystem == "on") ? "TRUE" : "FALSE");
		$reso->IS_BLOCKED = $datas->cbBlocked;

		$reso->_add();

		//Si hay error
		if($reso->nerror > 0) {
			$result['message'] = $reso->nerror . ". " . $reso->error;
		}

		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["RESOURCE_REGISTERED"];
		$result["link"] = $link;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>