<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'services.php');
					
	//Realiza la operacion
	require_once("../../classes/service_log.php");
	
	$link = "";

	//Captura las variables
	if(empty($_POST['strModel'])) {
		//Verifica el GET
		if(empty($_GET['strModel'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$strmodel = $_GET['strModel'];
			$link = $_GET['link'];
		}
	}
	else {
		$strmodel = $_POST['strModel'];
		$link = $_POST['link'];
	}
	
	$result["link"] = $link;
	
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$datas = json_decode($strmodel);
		
        //Instancia la clase
        $service = new service(); 
        $service->ID = $datas->hfID;
        
        //Busca la informacion
        $service->__getInformation();
        //Si hay error
        if($service->nerror > 0) {
            $result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["SERVICES"] . ": " . $service->error;
            $result["sql"] = $service->sql; 
			$result = utf8_converter($result);
			exit(json_encode($result));
        }
        
        //Busca el siguiente estado
        $idstate = $service->state->getNextState();

        //Agrega el log del servicio
        $sLog = new service_log();

        //Log
        $sLog->setService($service->ID);
        //Busca el ultimo log
        $sLog->getLastLog();
        //Asigna el ultimo estado
        $sLog->setInitialState($service->STATE_ID);
        //Limpia los campos no requeridos
        $sLog->ID = "UUID()";
        $sLog->EMPLOYEE_INITIAL_ID = "NULL";
        $sLog->setFinalEmployee($datas->cbEmployee);
        $sLog->VEHICLE_INITIAL_ID = "NULL";
        $sLog->setFinalVehicle($datas->hfIdVehicle);
        
        //Asigna la informacion
        //Si hay nuevo estado
        if($idstate != "") {
            $service->setState($idstate);
        }
        $sLog->setFinalState($service->STATE_ID);
        $service->MODIFIED_BY = $_SESSION["vtappcorp_userid"];

        //Si debe cambiar el tipo de vehiculo
        if($datas->hfChangeVehicle) {
            $service->setVehicle($datas->hfVehicleTypeId);
        }
        
        //Actualiza el servicio
        $service->_modify();

        //Si se genera error
        if($service->nerror > 0) {
            $result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["SERVICES"] . ": " . $service->error;
            $result["sql"] = $service->sql; 
			$result = utf8_converter($result);
			exit(json_encode($result));
        }			

        $sLog->MODIFIED_BY = "";
        $sLog->MODIFIED_ON = "NULL";

        //Adiciona el log
        $sLog->__add();
        
        //Si se genera error
        if($sLog->nerror > 0) {
            $result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["SERVICES"] . ": " . $sLog->error;
            $result["sql"] = $sLog->sql; 
			$result = utf8_converter($result);
			exit(json_encode($result));
        }

        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = $_SESSION["SERVICE_ASSIGNED"];
		$result["link"] = $link;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>);