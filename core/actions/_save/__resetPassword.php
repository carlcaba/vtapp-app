<?
    //Inicio de sesion
    session_name('vtappcorp_session');
    session_start();

    //Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');

    //Variable del codigo
    $result = array('success' => false,
        'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
        'link' => 'users.php');

    //Captura las variables
    if(empty($_POST['txtUser'])) {
        if(empty($_GET['txtUser'])) {
            //Termina
            $result = utf8_converter($result);
            exit(json_encode($result));
        }
        else {
            $id = $_GET['txtUser'];
			$mail = $_GET['sendMail'];
        }
    }
    else {
        $id = $_POST['txtUser'];
		$mail = $_POST['sendMail'];
    }
	
    //Si es un acceso autorizado
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        //Realiza la operacion
        require_once("../../classes/configuration.php");
        require_once("../../classes/users.php");
        require_once("../../classes/interfaces.php");
		
		$conf = new configuration("INIT_PASSWORD");
		$inter = new interfaces();

        $usr = explode(",",$id);
		$result["message"] = "";
        $errors = 0;
        foreach($usr as $var) {
            //$id = Desencriptar($var);
			$id = $inter->decrypt($var);
            //Asigna la informacion
            $user = new users($id);
            //Consulta la informacion
            $user->__getInformation();
            //Si hay error
            if ($user->nerror > 0) {
                $result["message"] .= $id . "=" . $user->error . "\n";
                $errors++;
				continue;
            }
			//Realiza el cambio
			$user->changePassword($conf->verifyValue(),$mail,true);
			
			//Si hay error
			if($user->nerror > 0) {
				//Si es error de correo
				if($user->nerror != 18) {
					//Confirma mensaje al usuario
					$result['message'] .= $id . "=" . $user->error . "\n";
					continue;
				}
			}
        }
        //Cambia el resultado
        $result['success'] = $errors == 0;
        $result['message'] = $result["success"] ? $_SESSION["INFORMATION_UPDATED"] : $result["message"];
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>