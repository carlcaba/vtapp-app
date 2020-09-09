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
	require_once("../../classes/area_type.php");

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
		
		$area = new area_type();
		$area->ID = $datas->txtID;
		$area->__getInformation();

		//Si hay error
		if($area->nerror > 0) {
			$result["message"] = $area->error;
			$result["sql"] = $area->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Completa la informacion de la opcion
		$area->AREA_TYPE_NAME = $datas->txtAREA_TYPE_NAME;
		$area->PARENT_ID = (intval($datas->cbParent) > 0 ? $datas->cbParent : "NULL");
		
		$area->_modify();
		
		//Si se genera error
		if($area->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["AREA_TYPES"] . ": " . $area->error . " -> " . $area->sql; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = str_replace("%d", "", $_SESSION["SAVED"]);
		$result["link"] = "area_types.php";
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>