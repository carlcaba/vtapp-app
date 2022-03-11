<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'trm.php');

	
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
		require_once("../../classes/money-bill-1_converter.php");
		
		//Asigna la informacion
		$money-bill-1 = new money-bill-1_converter();
		$money-bill-1->ID = $datas->txtID;
		//Consulta la informacion
		$money-bill-1->__getInformation();
		//Si hay error
		if($money-bill-1->nerror > 0) {
			$result["message"] = $money-bill-1->error;
			$result["sql"] = $money-bill-1->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Actualiza la información
		//Agrega la conversion
		$money-bill-1->money-bill-1_FROM = $datas->txtmoney-bill-1_FROM;
		$money-bill-1->money-bill-1_TO = $datas->txtmoney-bill-1_TO;
		$money-bill-1->VALUE_TO = $datas->txtVALUE_TO;
		$money-bill-1->DATERATE = $datas->txtDATERATE;
		$money-bill-1->BLOCKED = $datas->cbBlocked;
		
		//Lo Modifica
		$money-bill-1->_modify();
		
		//Si hay error
		if($money-bill-1->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $money-bill-1->error;
			$result["sql"] = $money-bill-1->sql;
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["TRM_MODIFIED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}

	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>