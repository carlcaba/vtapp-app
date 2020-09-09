<?
    //Inicio de sesion
    session_name('vtappcorp_session');
    session_start();

    //Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');

    //Variable del codigo
    $result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'outputs.php');

	//Realiza la operacion
	require_once("../../classes/movement_detail.php");

	//Captura las variables
    if(empty($_POST['txtId'])) {
        //Verifica el GET
        if(empty($_GET['txtId'])) {
            $result = utf8_converter($result);
            exit(json_encode($result));
        }
        else {
            $id = $_GET['txtId'];
        }
    }
    else {
        $id = $_POST['txtId'];
    }

    //Si es un acceso autorizado
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        //Asigna la informacion
		$move = new movement_detail();
		
		//Asigna el Id
		$move->setMovement($id);
		//Registro no encontrado
		if($move->nerror > 0) {
			$result["message"] = $_SESSION["NOT_REGISTERED"];
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Verifica si es aplicado
		if($move->isApplied()) {
			$result["message"] = $_SESSION["OUTPUT_NOT_APPLIED"];
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Los aplica
		$return = $move->applyToStock();
		
		$error = 0;
		$total = 0;
		
		foreach($return as $value) {
			$total++;
			if($value["Applied"] == "FALSE")
				$error++;
		}
		
		if($error > 0)
			$msg = sprintf($_SESSION["APPLIED_ERROR"],($total - $error), $total);
		else
			$msg = $_SESSION["APPLIED_CORRECTLY"];
		
        //Cambia el resultado
        $result['success'] = $error == 0;
        $result['message'] = $msg;
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>