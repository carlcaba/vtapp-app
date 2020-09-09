<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'profile.php');

	
	//Captura las variables
	if(!isset($_POST['strModel'])) {
		//Verifica el GET
		if(!isset($_GET['strModel'])) {
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
        $datas = json_decode($strmodel);
		
		//Realiza la operacion
		require_once("../../classes/employee.php");
		require_once("../../classes/users.php");
		
		//Asigna la informacion
		$empl = new employee();
		$empl->ID = $datas->txtID;
		//Consulta la informacion
		$empl->__getInformation();
		//Si hay error
		if($empl->nerror > 0) {
			$result["message"] = $empl->error;
			$result["sql"] = $empl->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		$user = new users($empl->ID_USER);
		
		//Actualiza la información
		$empl->FIRST_NAME = $datas->txtFIRST_NAME;
		$empl->LAST_NAME = $datas->txtLAST_NAME;
		$empl->IDNUMBER = $datas->txtIDNUMBER;
		$empl->EMAIL = $datas->txtEMAIL;
		$empl->setArea($datas->cbArea);
		
		//Lo Modifica
		$empl->_modify();
		
		//Si hay error
		if($empl->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $empl->error;
			$result["sql"] = $empl->sql;
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Verifica si el usuario existe
		if($user->nerror == 0) {
			//Actualiza la informacion
			$user->THE_PASSWORD = Encriptar($user->THE_PASSWORD);
			$user->NAME = $empl->FIRST_NAME;
			$user->LAST_NAME = $empl->LAST_NAME;
			$user->EMAIL = $empl->EMAIL;
			
			//Lo Modifica
			$user->_modify();
			
			//Si hay error
			if($user->nerror > 0) {
				//Confirma mensaje al usuario
				$result['message'] = $user->error;
				$result["sql"] = $user->sql;
				//Termina
				$result = utf8_converter($result);
				exit(json_encode($result));
			}
		}
		
		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["PROFILE_MODIFIED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}

	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>