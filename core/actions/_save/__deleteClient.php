<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	require_once("../../classes/client.php");

	//Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'clients.php');
	
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
		$client = new client();
		//Asigna la informacion
		$client->ID = $datas->txtID;
		//Consulta la informacion
		$client->__getInformation();
		//Si hay error
		if($client->nerror > 0) {
			$result["message"] = $client->error;
			$result["sql"] = $client->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Lo Modifica
		$client->_delete();
		
		//Si hay error
		if($client->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $client->error;
			$result["sql"] = $client->sql;
			$result = utf8_converter($result);
			//Termina
			exit(json_encode($result));
		}
		
		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["CLIENT_DELETED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>