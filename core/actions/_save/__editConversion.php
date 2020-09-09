<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'conversions.php');

	
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
		require_once("../../classes/conversion.php");
		
		//Asigna la informacion
		$conversion = new conversion();
		$conversion->ID = $datas->txtID;
		//Consulta la informacion
		$conversion->__getInformation();
		//Si hay error
		if($conversion->nerror > 0) {
			$result["message"] = $conversion->error;
			$result["sql"] = $conversion->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Actualiza la información
		$conversion->MATERIALTYPE = $datas->txtMATERIALTYPE;
		$conversion->WIDTH = $datas->txtWIDTH;
		$conversion->WIDTHUNIT = $datas->txtWIDTHUNIT;
		$conversion->FACTORUNIT = $datas->txtHEIGHT;
		$conversion->FACTORUNIT = $datas->txtHEIGHTUNIT;
		$conversion->FACTORUNIT = $datas->txtWEIGHT;
		$conversion->FACTORUNIT = $datas->txtWEIGHTUNIT;
		$conversion->FACTORUNIT = $datas->txtFACTOR;
		$conversion->FACTORUNIT = $datas->txtFACTORUNIT;
		$conversion->BLOCKED = $datas->cbBlocked;
		
		//Lo Modifica
		$conversion->_modify();
		
		//Si hay error
		if($conversion->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $conversion->error;
			$result["sql"] = $conversion->sql;
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["CONVERSION_MODIFIED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}

	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>