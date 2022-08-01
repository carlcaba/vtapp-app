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
	require_once("../../classes/directchat.php");
	require_once("../../classes/users.php");
	
	$users = new users($_SESSION["vtappcorp_userid"]);
	$access = $users->ACCESS_ID;

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

		$chat = new directchat();

		//Asigna la informacion
		$chat->DESTINY = $datas["txtDESTINY"];
		$chat->MESSAGE = $datas["txtMESSAGE"];
		$chat->DELIVERED = "TRUE";
		$chat->DELIVERED_ON = "NOW()";
		$chat->SENDER = !is_null($datas["hfSENDER"]) ? $datas["hfSENDER"] : $chat->SENDER;
		//Si es un cliente
		$chat->PRIORITY = ($access <= 50) ? "TRUE" : "FALSE";
		
		//Adiciona el registro
		$chat->_add(false,false);

		//Si se genera error
		if($chat->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["MESSAGES"] . ": " . $chat->error . " -> " . $chat->sql . " - $access" ; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = $chat->sql;
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>