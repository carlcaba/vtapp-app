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
	require_once("../../classes/notification.php");

	//Captura las variables
    if(empty($_POST['strModel'])) {
        //Verifica el GET
        if(empty($_GET['strModel'])) {
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
		
        $datas = $strmodel;

		$notification = new notification($datas["User"]);

		//Verifica el tipo
		$notification->type->TEXT_TYPE = $datas["Type"];
		$notification->type->getInformationByOtherInfo();
		//Si hay error
		if($notification->type->nerror != 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["NOTIFICATIONS"] . ": " . $notifications->type->error . " -> " . $notifications->type->sql; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Agrega la notificacion
		$notification->setType($notification->type->ID);
		$notification->MESSAGE = str_replace("'","\'",$datas["Message"]);
		$notification->SOURCE = str_replace("'","\'",$datas["Source"]);
		
		$notification->_add(false,false);

		//Si se genera error
		if($notification->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["NOTIFICATIONS"] . ": " . $notification->error . " -> " . $notification->sql; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = $notification->sql;

    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>