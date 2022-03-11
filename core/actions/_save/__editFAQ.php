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

	//Captura las variables
    if(empty($_POST['id'])) {
        //Verifica el GET
        if(empty($_GET['id'])) {
            $result = utf8_converter($result);
            exit(json_encode($result));
        }
        else {
			$id = $_GET['id'];
            $strFAQ = $_GET['strFAQ'];
        }
    }
    else {
        $id = $_POST['id'];
        $strFAQ = $_POST['strFAQ'];
    }

    //Si es un acceso autorizado
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		$faqs = new faq($id);
		
		//Asigna la informacion
		$faqs->FAQ_ANSWER = $strFAQ;
		$faqs->addAnswer();

		//Si se genera error
		if($faqs->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["FAQS"] . ": " . $faqs->error . " -> " . $faqs->sql; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = $_SESSION["FAQS"] . " " . $_SESSION["SAVED"];
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>