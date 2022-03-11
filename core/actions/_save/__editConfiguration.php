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
	require_once("../../classes/configuration.php");

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
		
		$conf = new configuration();
		$conf->ID = $datas->hfId;
		$conf->__getInformation();
		//Si hay error
		if($conf->nerror > 0) {
			$result["message"] = $conf->error;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Lo edita
		$conf->ENCRYPTED = empty($datas->chkEncrypted) ? "FALSE" : (($datas->chkEncrypted == "on") ? "TRUE" : "FALSE");
		if($conf->ENCRYPTED == "TRUE")
			$conf->KEY_VALUE = Encriptar($datas->txtKEY_VALUE);
		else
			$conf->KEY_VALUE = $datas->txtKEY_VALUE;			
		$conf->ACCESS_TO = $_SESSION['vtappcorp_useraccessid'];
		$conf->IS_BLOCKED = $datas->cbBlocked;

		//Lo modifica
		$conf->_modify();

		//Si se genera error
		if($conf->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["MENU_8"] . ": " . $conf->error . " -> " . $conf->sql; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = str_replace("%d", "", $_SESSION["SAVED"]);
		$result["link"] = "configman.php";
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>