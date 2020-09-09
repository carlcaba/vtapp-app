<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'cities.php');

	
	//Captura las variables
	if(!isset($_POST['strModel'])) {
		//Verifica el GET
		if(!isset($_GET['strModel'])) {
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
		
		//Realiza la operacion
		require_once("../../classes/city.php");
		
		//Asigna la informacion
		$city = new city();
		$city->ID = $datas->txtID;
		//Consulta la informacion
		$city->__getInformation();
		//Si hay error
		if($city->nerror > 0) {
			$result["message"] = $city->error;
			$result["sql"] = $city->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Actualiza la información
		$city->CITY_NAME = $datas->txtCITY_NAME;
		$city->setCountry($datas->cbCountry);
		$city->IS_BLOCKED = $datas->cbBlocked;
		
		//Lo Modifica
		$city->_modify();
		
		//Si hay error
		if($city->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $city->error;
			$result["sql"] = $city->sql;
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["CITY_MODIFIED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}

	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>