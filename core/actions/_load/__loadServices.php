<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    //Captura las variables
    if(empty($_POST['id'])) {
        //Verifica el GET
        if(empty($_GET['id'])) {
            exit();
		}
		else {
            $id = $_GET['id'];
		}
    }
    else {
		$id = $_POST['id'];
    }
	
	require_once("../../classes/service.php");
	
	$result = array("success" => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);

	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Instancia la clase
		$service = new service();
		
		//Asigna el cliente
		$service->setClient($id);
		
		if($service->nerror > 0) {
			$result["message"] = $service->error;
			$result["sql"] = $service->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		$result["success"] = true;
		$result["message"] = "";
		$result["data"] = $service->showToAssign();
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	//Termina
	exit(json_encode($result));

	
?>
