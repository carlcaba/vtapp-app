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
	require_once("../../classes/faq.php");

	$id = 0;
	//Captura las variables
    if(empty($_POST['id'])) {
        //Verifica el GET
        if(empty($_GET['id'])) {
            $result = utf8_converter($result);
            exit(json_encode($result));
        }
        else {
            $id = intval($_GET['id']);
        }
    }
    else {
        $id = intval($_POST['id']);
    }
	
	if($id == 0) {
		$result = utf8_converter($result);
		exit(json_encode($result));
	}

    //Si es un acceso autorizado
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		$faqs = new faq($id);
		$faqs->addView();

		//Si se genera error
		if($faqs->nerror > 0) {
			$result["message"] = $_SESSION["ERROR_ON_UPDATE"] . " view FAQ $id"; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = $_SESSION["INFORMATION_UPDATED"];
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>