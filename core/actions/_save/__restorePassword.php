<?
    //Inicio de sesion
    session_name('vtappcorp_session');
    session_start();

    //Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');

    //Variable del codigo
    $result = array('success' => false,
        'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);

    //Captura las variables
    if(empty($_POST['txtEMAIL'])) {
        if(empty($_GET['txtEMAIL'])) {
            //Termina
            $result = utf8_converter($result);
            exit(json_encode($result));
        }
        else {
            $id = $_GET['txtEMAIL'];
        }
    }
    else {
        $id = $_POST['txtEMAIL'];
    }
	
    //Si es un acceso autorizado
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        //Realiza la operacion
        require_once("../../classes/users.php");
		//Asigna la informacion
		$user = new users();
		//asigna la informacion
		$user->EMAIL = $id;
		//Consulta la informacion
		$user->getInfoByMail();
		//Si hay error
		if ($user->nerror > 0) {
			$result["message"] = $user->error;
            $result = utf8_converter($result);
            exit(json_encode($result));
		}
		//Realiza el cambio
		$user->changePassword($user->generatePassword(),true,true);
		//Si hay error
		$error = false;

		//Si hay error
		if($user->nerror > 0) {
			//Si es error de correo
			if($user->nerror != 18)
				//Confirma mensaje al usuario
				$result['message'] = $user->nerror . ". " . $user->error;
			else 
				$result['message'] = $user->nerror . ". " . $user->error;
			$error = true;
		}

        //Cambia el resultado
        $result['success'] = !$error;
        $result['message'] = $result["success"] ? $_SESSION["FORGOT_PASSWORD_MESSAGE"] : $result["message"];
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>