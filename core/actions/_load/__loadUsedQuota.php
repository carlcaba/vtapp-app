<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');		

	$id = "";

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
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);	
				
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		require_once("../../classes/quota.php");

		$quota = new quota();
		$datas = $quota->showQuotaUsed($id);
		$result["success"] = true;
		$result["data"] = $datas;
		$result["message"] = "ok";
		$result["sql"] = $quota->sql;
	}
	else {
		$result = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}

	//Termina
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));

?>