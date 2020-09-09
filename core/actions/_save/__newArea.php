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
	require_once("../../classes/area.php");

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

		$area = new area();
		//Verifica la informacion
		$area->getInformationByName($datas->txtAREA_NAME);
		//Si hay error
		if($area->nerror == 0) {
			$result["message"] = $_SESSION["MSG_DUPLICATED_RECORD"];
			$result["sql"] = $area->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}		
				
		//Agrega la pregunta
		$area->AREA_NAME = $datas->txtAREA_NAME;
		$area->TITLE = $datas->txtTITLE;
		$area->COSTCENTER = $datas->txtCOSTCENTER;
		$area->AREA_TYPE_ID = (intval($datas->cbAreaType) > 0 ? $datas->cbAreaType : "NULL");
		$area->setClient($datas->cbClient);
		$area->IS_BLOCKED = $datas->cbBlocked;
		
		$area->_add();

		//Si se genera error
		if($area->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["AREAS"] . ": " . $area->error . " -> " . $area->sql; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = str_replace("%d", "", $_SESSION["SAVED"]);
		$result["link"] = "areas.php";
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>