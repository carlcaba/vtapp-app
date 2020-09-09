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
	require_once("../../classes/product.php");

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
        $datas = json_decode($strmodel);

		$product = new product();
		
		//Obtiene el nombre del recurso
		$resources = new resources();
		$resources->RESOURCE_NAME = "PRODUCT_";
		$resources->getNextResource();
		
		$rname = $resources->RESOURCE_NAME;
		
		$field = "hf" . $product->table . "_RESOURCE_NAME";
		
		foreach($datas as $key => $value) {
			//Si es un recurso
			if(substr($key, 0, strlen($field)) === $field) {
				//Asigna la informacion
				$resources = new resources();
				$resources->RESOURCE_NAME = $rname;
				$resources->RESOURCE_TEXT = str_replace("'","\'",$value);
				$resources->RESOURCE_TEXT = htmlentities($resources->RESOURCE_TEXT);		
				$resources->SYSTEM = "FALSE";
				$resources->LANGUAGE_ID = substr($key,-1);
				//Lo adiciona
				$resources->_add();
				
				//Si se genera error
				if($resources->nerror > 0) {
					$result["message"] = $resources->RESOURCE_NAME . ": " . $resources->error;
					$result["sql"] = $resources->sql;
					//Termina
					$result = utf8_converter($result);
					exit(json_encode($result));
				}
			}
		}
		
		//Agrega el producto
		$product->CODE = $product->getNextCode();
		$product->RESOURCE_NAME = $rname;
		$product->SPECIFICATION = $datas->txtSPECIFICATION;
		$product->TRADE = $datas->txtTRADE;
		$product->QUANTITY = $datas->txtQUANTITY;
		$product->PRICE = $datas->txtPRICE;
		$product->MONEYTYPE = $datas->txtMONEYTYPE;
		$product->OBSERVATION = $datas->txtOBSERVATION;
		$product->FIELD = $datas->txtFIELD;
		$product->setArea($datas->cbArea);
		$product->setCategory($datas->cbCategory);
		
		$product->_add();

		//Si se genera error
		if($product->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["PRODUCTS"] . ": " . $product->error . " -> " . $product->sql; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = str_replace("%d", "", $_SESSION["SAVED"]);
		$result["link"] = "products.php";
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>