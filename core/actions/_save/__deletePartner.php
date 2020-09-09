<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	require_once("../../classes/partner.php");

	//Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'partners.php');
	
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
		
		//Asigna la informacion
		$partner = new partner();
		//Asigna la informacion
		$partner->ID = $datas->txtID;
		//Consulta la informacion
		$partner->__getInformation();
		//Si hay error
		if($partner->nerror > 0) {
			$result["message"] = $partner->error;
			$result["sql"] = $partner->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Lo Modifica
		$partner->_delete();
		
		//Si hay error
		if($partner->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $partner->error;
			$result["sql"] = $partner->sql;
			$result = utf8_converter($result);
			//Termina
			exit(json_encode($result));
		}
		
		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["PARTNER_DELETED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>