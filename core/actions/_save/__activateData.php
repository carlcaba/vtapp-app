<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');		

	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'index.php');
	
	//Captura las variables
	if(!isset($_POST['txtId'])) {
		if(!isset($_GET['txtId'])) {
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$id = $_GET['txtId'];
			$activate = $_GET['activate'];
            $class = $_GET['txtClass'];
            $link = $_GET['txtLink'];
			$pre = $_GET['txtPre'];
		}
	}
	else {
		$id = $_POST['txtId'];
		$activate = $_POST['activate'];
		$class = $_POST['txtClass'];
		$link = $_POST['txtLink'];
		$pre = $_POST['txtPre'];
	}

	if(empty($class)) {
		exit();
	}
	
	require_once("../../classes/" . $class . ".php");
	
	$result["link"] = $link;
	
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {

		//Asigna la informacion
		$sxemp = new $class();
		$sxemp->ID = $id;
		
		//Consulta la informacion
		$sxemp->__getInformation();
		//Si hay error
		if($sxemp->nerror > 0) {
			$result["message"] = $sxemp->error;
			$result["sql"] = $sxemp->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Lo modifica
		$sxemp->activate($activate);

		//Si hay error
		if($sxemp->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $sxemp->error;
			$result["sql"] = $sxemp->sql;
			$result = utf8_converter($result);
			//Termina
			exit(json_encode($result));
		}

		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $activate ? $_SESSION[$pre . "_ACTIVATED"] : $_SESSION[$pre . "_DEACTIVATED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>