<?
    //Inicio de sesion
    session_name('vtappcorp_session');
    session_start();

    //Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');

    //Variable del codigo
    $result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'outputs.php');

	//Realiza la operacion
	require_once("../../classes/movement_detail.php");

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

		$move = new movement_detail();

		//Adiciona la informacion
		$move->movement->INTERNAL_NUMBER = $datas["txtINTERNAL_NUMBER"];
		$move->movement->MOVE_DATE = $datas["txtMOVE_DATE"];
		
		//Verifica el numero
		$move->movement->getInformationByOtherInfo();
		
		//Si no hay error
		if($move->movement->nerror == 0) {
			$result["message"] = $_SESSION["DUPLICATED_INFO_WARNING"];
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		$move->movement->setEmployee($datas["cbEmployee"]);
		//Agrega el registro
		$move->movement->_add();
		
		//Si se genera error
		if($move->movement->nerror > 0) {
			$result["message"] = $move->movement->error;
			$result["sql"] = $move->movement->sql;
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		$error = 0;
		$warning = 0;
		$total = 0;
		$errmsg = "";
		$warmsg = "";
		
		foreach($datas["Items"] as $value) {
			$total++;
			//Nueva instancia
			$mdet = new movement_detail();
			
			//Asigna la cantidad
			$mdet->QUANTITY = $value["Quantity"];
			$mdet->FACTOR = $value["Factor"];
			$mdet->MONEY_FACTOR = $value["MoneyFactor"];
			$mdet->APPLIED = "TRUE";
			$mdet->ID_ORDER = $total;
			$mdet->UNIT = $value["Unit"];
			//Asigna el producto
			$mdet->setProduct($value["Id"]);
			
			//Verifica la cantidad
			if(($mdet->QUANTITY * $mdet->FACTOR) > $mdet->product->QUANTITY) {
				$warning++;
				$warmsg .= $_SESSION["NOT_ENOUGH_EXISTENCE"] . " (" . $mdet->product->getResource() . ") Qty: " . $mdet->QUANTITY . " Fct: " . $mdet->FACTOR . " Exs: " . $mdet->product->QUANTITY . "<br />";
				$mdet->APPLIED = "FALSE";
			}
			
			//Asigna la informacion
			$mdet->setType($datas["hfMovement"]);
			$mdet->setMovement($move->movement->ID);
			$mdet->setEmployee($datas["cbEmployee"]);
			$mdet->PRICE = $value["Price"];
			
			//Intenta actualizar el inventario
			$mdet->product->registerMove($move->movement->ID, $mdet->QUANTITY, $mdet->FACTOR);
			
			//Si se genera error
			if($mdet->product->nerror > 0) {
				$warning++;
				$warmsg .= $_SESSION["ERROR_TRYING_UPDATE"] . " (" . $mdet->product->getResource() . ") Qty: " . $mdet->QUANTITY . " Fct: " . $mdet->FACTOR . " Exs: " . $mdet->product->QUANTITY . "<br />";
				$mdet->APPLIED = "FALSE";
			}
			
			//Agrega el registro
			$mdet->_add();
			
			//Si se genera error
			if($mdet->nerror > 0) {
				$error++;
				$errmsg .= $mdet->error . " (" . $mdet->sql . ")<br />";
			}
		}
		
        //Cambia el resultado
        $result['success'] = $error == 0;
        $result['message'] = ($error == 0) ? str_replace("%d", $total, $_SESSION["SAVED"]) : sprintf($_SESSION["SAVED_RECORDS_ERROR"], ($total - $error), $total, $errmsg);
		//Si hay warnings
		$result['message'] .= ($warning == 0) ? "" : "<br />" . $_SESSION["WARNING"] . "<br />$warmsg";
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>