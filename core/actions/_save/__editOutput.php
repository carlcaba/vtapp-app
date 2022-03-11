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
		
		//Asigna el Id
		$move->setMovement($datas["txtID"]);
		//Registro no encontrado
		if($move->nerror > 0) {
			$result["message"] = $_SESSION["NOT_REGISTERED"];
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		$check = false;
		//Modifica la informacion
		$check |= ($datas["txtINTERNAL_NUMBER"] != $move->movement->INTERNAL_NUMBER);
		if($datas["txtINTERNAL_NUMBER"] != $move->movement->INTERNAL_NUMBER)
			$move->movement->INTERNAL_NUMBER = $datas["txtINTERNAL_NUMBER"];
		
		$check |= ($move->movement->MOVE_DATE != $datas["txtMOVE_DATE"]);
		if($move->movement->MOVE_DATE != $datas["txtMOVE_DATE"])
			$move->movement->MOVE_DATE = $datas["txtMOVE_DATE"];
		
		$check |= ($datas["cbEmployee"] != $move->movement->ID_EMPLOYEE);
		if($datas["cbEmployee"] != $move->movement->ID_EMPLOYEE)
			$move->movement->setEmployee($datas["cbEmployee"]);		
		
		//Si debe verificar la informacion
		if($check) {
			//Verifica el numero
			$move->movement->getInformationByOtherInfo();
			
			//Si no hay error
			if($move->movement->nerror == 0) {
				$result["message"] = $_SESSION["DUPLICATED_INFO_WARNING"];
				//Termina
				$result = utf8_converter($result);
				exit(json_encode($result));
			}
			//Reasigna el ID
			$move->movement->ID = $datas["txtID"];
			//Modifica el registro
			$move->movement->_modify();
		}
		
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
		$entry = $move->type->getInformationByMovement("MOVEMENT_TYPE_1");
		
		foreach($datas["Items"] as $value) {
			$total++;
			//Nueva instancia
			$mdet = new movement_detail();
			//Verifica si es un nuevo item
			if($value["IdDetail"] == 0) {
				//Asigna la cantidad
				$mdet->QUANTITY = $value["Quantity"];
				$mdet->FACTOR = $value["Factor"];
				$mdet->money-bill-1_FACTOR = $value["money-bill-1Factor"];
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
			}
			else {
				//Asigna el ID
				$mdet->ID = $value["IdDetail"];
				//Consulta la información
				$mdet->__getInformation();
				
				//Verifica si hay cambios
				//Cantidad
				if($mdet->QUANTITY != $value["Quantity"]) {
					//Verifica si fue aplicado
					if($mdet->APPLIED) {
						//Intenta devolver el inventario
						$mdet->product->registerMove($entry, $mdet->QUANTITY, $mdet->FACTOR);
						//Si se genera error
						if($mdet->product->nerror > 0) {
							$warning++;
							$warmsg .= $_SESSION["ERROR_UNREGISTERING_MOVE"] . " (" . $mdet->product->getResource() . ") Qty: " . $mdet->QUANTITY . " Fct: " . $mdet->FACTOR . " Exs: " . $mdet->product->QUANTITY . "<br />";
							$mdet->APPLIED = "FALSE";
						}
						else {
							//Intenta devolver el inventario
							$mdet->product->registerMove($move->movement->ID, $value["Quantity"], $value["Factor"]);

							//Si se genera error
							if($mdet->product->nerror > 0) {
								$warning++;
								$warmsg .= $_SESSION["ERROR_TRYING_UPDATE"] . " (" . $mdet->product->getResource() . ") Qty: " . $value["Quantity"] . " Fct: " . $value["Factor"] . " Exs: " . $mdet->product->QUANTITY . "<br />";
								$mdet->APPLIED = "FALSE";
							}
							else 
								$mdet->APPLIED = "TRUE";
						}
					}
					else {
						//Intenta devolver el inventario
						$mdet->product->registerMove($move->movement->ID, $value["Quantity"], $value["Factor"]);

						//Si se genera error
						if($mdet->product->nerror > 0) {
							$warning++;
							$warmsg .= $_SESSION["ERROR_TRYING_UPDATE"] . " (" . $mdet->product->getResource() . ") Qty: " . $value["Quantity"] . " Fct: " . $value["Factor"] . " Exs: " . $mdet->product->QUANTITY . "<br />";
							$mdet->APPLIED = "FALSE";
						}
						else 
							$mdet->APPLIED = "TRUE";
					}
					//Cambia la cantidad
					$mdet->QUANTITY = $value["Quantity"];
					//Modifica el registro
					$mdet->_modify();
				}
			}
			
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