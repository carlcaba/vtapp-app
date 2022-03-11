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
    if(empty($_POST['strFAQ'])) {
        //Verifica el GET
        if(empty($_GET['strFAQ'])) {
            $result = utf8_converter($result);
            exit(json_encode($result));
        }
        else {
            $strFAQ = $_GET['strFAQ'];
        }
    }
    else {
        $strFAQ = $_POST['strFAQ'];
    }

    //Si es un acceso autorizado
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		$faqs = new faq();
		
		//Asigna la informacion
		$faqs->FAQ_TEXT = $strFAQ;
		$faqs->_add();

		//Si se genera error
		if($faqs->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["FAQS"] . ": " . $faqs->error . " -> " . $faqs->sql; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = str_replace("%d", "", $_SESSION["SAVED"]);
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>