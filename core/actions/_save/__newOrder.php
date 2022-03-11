<?
    //Inicio de sesion
    session_name('vtappcorp_session');
    session_start();

    //Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');

    //Variable del codigo
    $result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'orders.php');

	//Realiza la operacion
	require_once("../../classes/order_detail.php");

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

		$order = new order_detail();

		//Adiciona la informacion
		$order->_order->INTERNAL_NUMBER = $datas["txtINTERNAL_NUMBER"];
		$order->_order->REGISTERED_ON = $datas["txtREGISTERED_ON"];
		
		//Verifica el numero
		$order->_order->getInformationByOtherInfo();
		
		//Si no hay error
		if($order->_order->nerror == 0) {
			$result["message"] = $_SESSION["DUPLICATED_INFO_WARNING"];
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		$order->_order->setClient($datas["cbClient"]);
		$order->_order->setState(1);
		//Agrega el registro
		$order->_order->_add();
		
		//Si se genera error
		if($order->_order->nerror > 0) {
			$result["message"] = $order->_order->error;
			$result["sql"] = $order->_order->sql;
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		$error = 0;
		$total = 0;
		$errmsg = "";
		
		foreach($datas["Items"] as $value) {
			$total++;
			//Nueva instancia
			$mdet = new order_detail();
			
			//Asigna la cantidad
			$mdet->setOrder($order->_order->ID);
			$mdet->setProduct($value["Id"]);
			$mdet->QUANTITY = $value["Quantity"];
			$medt->QTY_PROCESSED = 0;
			$medt->QTY_DELIVERED = 0;
			$mdet->PRICE = $value["Price"];
			$mdet->FACTOR = $value["Factor"];
			$mdet->money-bill-1_FACTOR = $value["money-bill-1Factor"];
			$mdet->UNIT = $value["Unit"];
			$mdet->PROCESSED = "FALSE";
			$mdet->DELIVERED = "FALSE";
			$mdet->ID_ORDER_ROW = $total;
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
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>