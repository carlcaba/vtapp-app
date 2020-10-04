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
	require_once("../../classes/user_address.php");

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
		
		$zone = new zone();
		$zone->ZONE_NAME = "NO DEFINIDA";
		$zone->getInformationByOtherInfo();

		$address = new user_address();
		
		$address->setUser($_SESSION['vtappcorp_userid']);
		$address->ADDRESS_NAME = $datas->txtADDRESS_NAME;
		$address->ADDRESS = trim($datas->AddressType . " " . $datas->txtAddress01 . " # " . $datas->txtAddress02 . " - " . $datas->txtAddress03 . " " . $datas->txtAddress04);
		$address->setCity($datas->cbCity);
		if($datas->ZoneVisible == "true") {
			$address->setZone($datas->cbZoneSub);
		}
		else {
			$address->setZone($zone->ID);
		}
		$address->LATITUDE = $datas->hfLATITUDE_USER_ADDRESS;
		$address->LONGITUDE = $datas->hfLONGITUDE_USER_ADDRESS;
		$address->IS_ORIGIN = strtoupper($datas->IsOrigin);
		$address->IS_BLOCKED = "FALSE";
		
		$address->_add();

		//Si se genera error
		if($address->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["USER_ADDRESSES"] . ": " . $address->error . " -> " . $address->sql; 
			$result["link"] = "";
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = str_replace("%d", "", $_SESSION["SAVED"]);
		$result["link"] = '$("#mdlAddress").modal("hide")';
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>