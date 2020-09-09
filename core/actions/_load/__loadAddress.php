<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');		

	$type = "false";

    //Captura las variables
    if(empty($_POST['type'])) {
        //Verifica el GET
        if(empty($_GET['type'])) {
            exit();
		}
		else {
            $type = $_GET['type'];
		}
    }
    else {
		$type = $_POST['type'];
    }	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);	
				
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		require_once("../../classes/user_address.php");

		$usera = new user_address();
		$datas = $usera->showTableDataJSON($type);
		$result["success"] = true;
		$result["data"] = $datas;
		$result["message"] = "ok";
	}
	else {
		$result = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}

	//Termina
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));

?>