<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/partner_rate.php");
	require_once("../../classes/partner_client.php");
	require_once("../../classes/quota_employee.php");
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE_RATES"]);
	
	$distance = "0";
	$client = "";
	$round = "false";
	$select = "true";
	$action = "view";
	$pid = "";
	//Captura las variables
	if(empty($_POST['distance'])) {
		//Verifica el GET
		if(empty($_GET['distance'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$distance = $_GET['distance'];
			$client = $_GET['client'];
			$round = $_GET['round'];
			$select = !empty($_GET['select']) ? $_GET['select'] : $select;
			$action = $_GET['action'];
			$pid = $_GET["pid"];
		}
	}
	else {
		$distance = $_POST['distance'];
		$round = $_POST['round'];
		$client = $_POST["client"];
		$select = !empty($_POST['select']) ? $_POST['select'] : $select;
		$action = $_POST['action'];
		$pid = $_POST["pid"];
	}
		
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$rate = new partner_rate();
		$quot = false;
		
		$filter = "";
		$quota = "";
		$balance = 0;
		$change = false;
		$result["filtered"] = false;
		$result["employee"] = false;		
		$result["employee_id"] = "";
		$buttons = "<button type=\"button\" title=\"" . $_SESSION["SAVE"] . "\" id=\"btnSave\" name=\"btnSave\" class=\"btn btn-success pull-right\" onclick=\"Save();\">\n";
		$buttons .= "<i class=\"fa fa-floppy-o\"></i>\n";
		$buttons .= "<span class=\"d-none d-sm-none d-md-none d-lg-block d-xl-inline-block\"> " . $_SESSION["SAVE"] . "</span>\n";
		$buttons .= "</button>\n";
		if($pid != "" && $action == "view") {
			$buttons = "<button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . $_SESSION["CANCEL"] . "\" id=\"btnReturn\" name=\"btnReturn\" class=\"btn btn-warning pull-right\" onclick=\"history.back();\">\n";
			$buttons .= "<i class=\"fa fa-arrow-left\"></i>\n";
			$buttons .= "<span class=\"d-none d-sm-none d-md-none d-lg-block d-xl-inline-block\">" . $_SESSION["CANCEL"] . "</span>\n";
			$buttons .= "</button>\n";
			$filter = "'$pid'";
		}
		else if($client != "") {
			$ptcl = new partner_client();
			$ptcl->setClient($client);
			//Obtiene los aliados
			$partners = $ptcl->getMyPartners();
			//Crea el filtro
			$arrFilter = array();
			$emp = array(); 
			//Verifica el resultado
			foreach($partners as $part) {
				array_push($arrFilter,$part["id"]);
				array_push($emp,$part["employee"]);
			}
			//Genera la cadena de filtro
			if(!empty($arrFilter)) {
				$filter = implode(",",$arrFilter);
				$result["filtered"] = true;
			}
			//Genera la cadena de filtro
			if(!empty($emp)) {
				$result["employee"] = true;
				$result["employee_id"] = true;
			}
			
			$quot = new quota_employee();
			$quot->getInformationByOtherInfo("USER_ID", $_SESSION["vtappcorp_userid"],"CLIENT_ID",$client);
			if($quot->nerror == 0) {
				if($quot->AMOUNT > $quot->USED) {
					$quota = $quot->ID;
					$balance = $quot->AMOUNT - $quot->USED;
				}
				else {
					$buttons = "<button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . $_SESSION["GO_TO_PAY"] . "\" id=\"btnPayment\" name=\"btnPayment\" class=\"btn btn-warning pull-right\" onclick=\"payment();\">\n";
					$buttons .= "<i class=\"fa fa-money-bill-1\"></i>\n";
					$buttons .= "<span class=\"d-none d-sm-none d-md-none d-lg-block d-xl-inline-block\">" . $_SESSION["GO_TO_PAY"] . "</span>\n";
					$buttons .= "</button>\n";
					$change = true;
				}
			}
			else {
				$buttons = "<button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . $_SESSION["GO_TO_PAY"] . "\" id=\"btnPayment\" name=\"btnPayment\" class=\"btn btn-warning pull-right\" onclick=\"payment();\">\n";
				$buttons .= "<i class=\"fa fa-money-bill-1\"></i>\n";
				$buttons .= "<span class=\"d-none d-sm-none d-md-none d-lg-block d-xl-inline-block\">" . $_SESSION["GO_TO_PAY"] . "</span>\n";
				$buttons .= "</button>\n";
			}
			
			if($ptcl->client->CLIENT_PAYMENT_TYPE_ID == 3) {
				$buttons = "<button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . $_SESSION["SYSTEM_PAYMENT_TYPE_3"] . "\" id=\"btnOnDeliver\" name=\"btnOnDeliver\" class=\"btn btn-info pull-right\" onclick=\"onDeliver();\">\n";
				$buttons .= "<i class=\"fa fa-gift\"></i>\n";
				$buttons .= "<span class=\"d-none d-sm-none d-md-none d-lg-block d-xl-inline-block\">" . $_SESSION["SYSTEM_PAYMENT_TYPE_3"] . "</span>\n";
				$buttons .= "</button>\n";
			}
		}
		
		$datos = $rate->selectPartner($distance,$round == "true",$filter,$quota,$balance,(($quot == null) ? $quot : $quot->quota->type->discountType()));
		
		if($rate->nerror > 0) {
			$result["message"] = $_SESSION["NO_INFORMATION"];
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		$result["message"] = ($select == "true") ? $datos["html"] : floatval($datos["max"]);
		$result["buttons"] = $buttons;
		$result["min"] = floatval($datos["min"]);
		$result["max"] = floatval($datos["max"]);
		$result["sql"] = $datos["sql"];
		$result["success"] = true;
		$result["change"] = $change;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));

?>