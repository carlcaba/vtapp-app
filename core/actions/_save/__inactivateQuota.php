<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	require_once("../../classes/quota.php");

	//Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'quotas.php');
	
	$id = "";
	$reason = "";
	
	//Captura las variables
	if(!isset($_POST['id'])) {
		//Verifica el GET
		if(!isset($_GET['id'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$id = $_GET['id'];
			$reason = $_GET['reason'];
		}
	}
	else {
		$id = $_POST['id'];
		$reason = $_POST['reason'];
	}
	
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$quota = new quota();
		//Asigna la informacion
		$quota->ID = $id;
		//Consulta la informacion
		$quota->__getInformation();
		//Si hay error
		if($quota->nerror > 0) {
			$result["message"] = $quota->error;
			$result["sql"] = $quota->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Verifica que sea una eliminación
		if($reason != "") {
			$result["message"] = $_SESSION["NO_DATA_FOR_VALIDATE"];
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Lo Modifica
		$quota->_delete();
		
		//Si hay error
		if($quota->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $quota->error;
			$result["sql"] = $quota->sql;
			$result = utf8_converter($result);
			//Termina
			exit(json_encode($result));
		}
		
		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["QUOTA_DELETED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>