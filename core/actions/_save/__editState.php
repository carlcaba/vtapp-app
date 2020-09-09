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
	require_once("../../classes/order_state.php");

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
		
		$order = new order_state();
		$order->ID = $datas->txtID;
		$order->__getInformation();

		//Si hay error
		if($order->nerror > 0) {
			$result["message"] = $order->error;
			$result["sql"] = $order->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		$field = "hf" . $order->table . "_RESOURCE_NAME";
		$rname = $order->RESOURCE_NAME;
		
		foreach($datas as $key => $value) {
			//Si es un recurso
			if(substr($key, 0, strlen($field)) === $field) {
				$keyId = substr($key,0,strlen($field)+2);
				//Asigna la informacion
				$resources = new resources();
				$resources->getResourceObjByName($rname,substr($keyId,-1));
				//Si no existe
				if($resources->nerror > 0) {
					//Lo adiciona
					$resources->RESOURCE_NAME = $rname;
					$resources->RESOURCE_TEXT = str_replace("'","\'",$value);
					$resources->RESOURCE_TEXT = htmlentities($resources->RESOURCE_TEXT);		
					$resources->SYSTEM = "FALSE";
					$resources->LANGUAGE_ID = substr($keyId,-1);
					//Lo adiciona
					$resources->_add();
				}
				else {
					$resources->RESOURCE_TEXT = str_replace("'","\'",$value);
					$resources->RESOURCE_TEXT = htmlentities($resources->RESOURCE_TEXT);		
					//Lo modifica
					$resources->_modify();
				}
				
				//Si se genera error
				if($resources->nerror > 0) {
					$result["message"] = $resources->RESOURCE_NAME . ": " . $resources->error;
					$result["sql"] = $resources->sql;
					//Termina
					$result = utf8_converter($result);
					exit(json_encode($result));
				}
			}
		}
		
		//Completa la informacion de la opcion
		$order->RESOURCE_NAME = $rname;
		$order->BADGE = $datas->txtBADGE;
		$order->_modify();
		
		//Si se genera error
		if($order->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["STATES"] . ": " . $order->error . " -> " . $order->sql; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = str_replace("%d", "", $_SESSION["SAVED"]);
		$result["link"] = "states.php";
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>