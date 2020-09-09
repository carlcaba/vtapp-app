<?
    //Inicio de sesion
    session_name('vtappcorp_session');
    session_start();

    //Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');

    //Variable del codigo
    $result = array('success' => false,
        'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);

	//Realiza la operacion
	require_once("../../classes/resources.php");

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

    //Si es un acceso autorizado
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        //Asigna la informacion
        $datas = json_decode($strmodel);
		
		$resource = new resources();
		$resource->ID = $datas->hfId;
		$resource->__getInformation();
		//Si hay error
		if($resource->nerror > 0) {
			$result["message"] = $resource->error;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Lo adiciona
		$resource->RESOURCE_NAME = $datas->txtRESOURCE_NAME;
		$resource->RESOURCE_TEXT = str_replace("'","\'",$datas->txtRESOURCE_TEXT);
		$resource->RESOURCE_TEXT = htmlentities($resource->RESOURCE_TEXT);		
		$resource->IS_SYSTEM = empty($datas->chkSystem) ? "FALSE" : (($datas->chkSystem == "on") ? "TRUE" : "FALSE");
		$resource->LANGUAGE_ID = $datas->cbLanguage;
		$resource->IS_BLOCKED = $datas->cbBlocked;

		//Lo modifica
		$resource->_modify();

		//Si se genera error
		if($resource->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["RESOURCES"] . ": " . $resource->error . " -> " . $resource->sql; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = str_replace("%d", "", $_SESSION["SAVED"]);
		$result["link"] = "resourcesman.php";
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>