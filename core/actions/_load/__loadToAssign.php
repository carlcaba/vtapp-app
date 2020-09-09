<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

	//Id de servicio
	$id = "";
	$src = "";
    //Captura las variables
    if(empty($_POST['id'])) {
        //Verifica el GET
        if(empty($_GET['id'])) {
            exit();
		}
		else {
            $id = $_GET['id'];
            $src = $_GET['src'];
		}
    }
    else {
		$id = $_POST['id'];
		$src = $_POST['src'];
    }
    
	require_once("../../classes/service.php");
	require_once("../../classes/partner_client.php");
	
	$result = array("success" => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);

	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        //Instancia el servicio
        $serv = new service();
        $serv->ID = $id;
        $serv->__getInformation();

        //Verifica si se encuentra el servicio
        if($serv->nerror > 0) {
			$result["message"] = $_SESSION["NO_DATA"] . "\n" . $serv->error;
			$result["sql"] = $serv->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
        }

		$dataForm = $serv->dataForm("new");

		//Instancia los mensajeros
		$partner = new partner_client();

		//SI LA FUENTE ES CLIENTE DEBE MOSTRAR LOS MENSAJEROS Y EMPRESAS ASOCIADAS AL CLIENTE SI NO HAY ASOCIACION NO MUESTRA NADA
		//SI EL PERFIL ES ADMINISTRADOR MUESTRA TODO
		//SI EL PERFIL ES ALIADO SOLO MUESTRA EMPLEADOS ALIADO
		//SI EL PERFIL ES CLIENTE SOLO MUESTRA EMPLEADOS CLIENTE
		if($_SESSION["vtappcorp_useraccessid"] == 100) {
			$partner->setClient($serv->CLIENT_ID);
			switch($src) {
				case "cli": {
					$src = "CLIENT"; 
					break;
				}
				case "ali": {
					$src = "PARTNER";
					break;
				}
				default: 
					$src = "";
			}
		}
		else if(substr($_SESSION['vtappcorp_useraccess'],0,2) == "CL") {
			if($serv->CLIENT_ID == $_SESSION["vtappcorp_referenceid"]) {
				$partner->setClient($serv->CLIENT_ID);
				$src = "CLIENT";
			}
			else {
				$result["message"] = $_SESSION["NO_PERMISSION"];
				$result = utf8_converter($result);
				exit(json_encode($result));
			}
		}
		else if(substr($_SESSION['vtappcorp_useraccess'],0,2) == "AL") {
			if($_SESSION["vtappcorp_referenceid"] != "") {
				$partner->setPartner($_SESSION["vtappcorp_referenceid"]);
				$src = "PARTNER";
			}
			else {
				$result["message"] = $_SESSION["NO_PERMISSION"];
				$result = utf8_converter($result);
				exit(json_encode($result));
			}
		}

		$dataForm = $partner->dataForm("new",$src);

		//Genera la forma
		//variable a retornar
		$form = $dataForm["tabs"] . "<form id=\"frmAssignService\" name=\"frmAssignService\" role=\"form\">\n";

		$cont = 1;

		//ID servicio
		$form .= $dataForm["tabs"] . "<input type=\"hidden\" name=\"hfID\" id=\"hfID\" value=\"" . $serv->ID . "\" required />\n";
		$cont++;
		//Destino
		$form .= $serv->showField("DELIVER_TO", $dataForm["tabs"] . "\t", "", "", false, $serv->DELIVER_TO . " - " . $serv->DELIVER_ADDRESS, false, "9,9,12", "disabled");

		$form .= $dataForm["tabs"] . "\t<div class=\"form-group\">\n";
		$form .= $dataForm["tabs"] . "\t\t<label>" . $serv->arrColComments["VEHICLE_TYPE_ID"] . " *</label>\n";
		$form .= $dataForm["tabs"] . "\t\t\t<select class=\"form-control\" id=\"cbVehicle\" name=\"cbVehicle\" " . $dataForm["readonly"][$cont++] . " required " . ($serv->vehicle->isForSelect() ? "" : "disabled") . ">\n";
		$form .= $serv->vehicle->showOptionList(8,$serv->VEHICLE_TYPE_ID);
		$form .= $dataForm["tabs"] . "\t\t\t</select>\n";
		$form .= $dataForm["tabs"] . "\t\t\t<input type=\"hidden\" id=\"hfNoVehicle\" name=\"hfNoVehicle\" value=\"" . $serv->vehicle->IDForSelect() . "\" />\n";
		$form .= $dataForm["tabs"] . "\t\t\t<input type=\"hidden\" id=\"hfChangeVehicle\" name=\"hfChangeVehicle\" value=\"" . ($serv->VEHICLE_TYPE_ID == $serv->vehicle->IDForSelect() ? "true" : "false") . "\" />\n";
		$form .= $dataForm["tabs"] . "\t\t\t<input type=\"hidden\" id=\"hfVehicleTypeId\" name=\"hfVehicleTypeId\" value=\"\" />\n";
		$form .= $dataForm["tabs"] . "\t</div>\n";

		$form .= $dataForm["tabs"] . "\t<div class=\"form-group\">\n";
		$form .= $dataForm["tabs"] . "\t\t<label>" . $partner->arrColComments["PARTNER_ID"] . " *</label>\n";
		$form .= $dataForm["tabs"] . "\t\t\t<select class=\"form-control\" id=\"cbPartner\" name=\"cbPartner\" " . $dataForm["readonly"][$cont++] . " required>\n";
		$form .= $partner->partner->showOptionList(8,$partner->PARTNER_ID);
		$form .= $dataForm["tabs"] . "\t\t\t</select>\n";
		$form .= $dataForm["tabs"] . "\t</div>\n";

		$form .= $dataForm["tabs"] . "\t<div class=\"form-group\">\n";
		$form .= $dataForm["tabs"] . "\t\t<label>" . $partner->arrColComments["EMPLOYEE_ID"] . " *</label>\n";
		$form .= $dataForm["tabs"] . "\t\t\t<select class=\"form-control\" id=\"cbEmployee\" name=\"cbEmployee\" " . $dataForm["readonly"][$cont++] . " required>\n";
		$form .= $partner->employee->showOptionListWithVehicle(8,$partner->EMPLOYEE_ID);
		$form .= $dataForm["tabs"] . "\t\t\t</select>\n";
		$form .= $dataForm["tabs"] . "\t</div>\n";

		$form .= $dataForm["tabs"] . "\t<p>" . $_SESSION["REQUIRED_FIELDS"] . "</p>\n";
		$form .= $dataForm["tabs"] . "\t<input type=\"hidden\" id=\"hfAction\" name=\"hfAction\" value=\"$viewData\" >\n";
		$form .= $dataForm["tabs"] . "\t<input type=\"hidden\" id=\"hfLinkAction\" name=\"hfLinkAction\" value=\"" . $dataForm["link"] . "\" >\n";
		$form .= $dataForm["tabs"] . "</form>\n";
		
		$data = array("form" => $form,
						"title" => $dataForm["title"],
						"icon" => $dataForm["icon"]);
		
		$result["message"] = $data;
		$result["success"] = true;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	//Termina
	exit(json_encode($result));

	
?>
