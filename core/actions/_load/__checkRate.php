<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/partner_rate.php");
	require_once("../../classes/partner_client.php");
	require_once("../../classes/quota_employee.php");

	_error_log("__checkRate start " . date("Y-m-d h:i:s"));

	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE_RATES"]);
	
	$distance = "0";
	$client = "";
	$round = "false";
	$select = "true";
	$action = "view";
	$pid = "";
	$gtw = true;
	$bid = true;
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
			$gtw = filter_var($_GET["gtw"], FILTER_VALIDATE_BOOLEAN);
			$bid = filter_var($_GET["bid"], FILTER_VALIDATE_BOOLEAN);
		}
	}
	else {
		$distance = $_POST['distance'];
		$round = $_POST['round'];
		$client = $_POST["client"];
		$select = !empty($_POST['select']) ? $_POST['select'] : $select;
		$action = $_POST['action'];
		$pid = $_POST["pid"];
		$gtw = filter_var($_POST["gtw"], FILTER_VALIDATE_BOOLEAN);
		$bid = filter_var($_POST["bid"], FILTER_VALIDATE_BOOLEAN);
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

		$gateway = "<button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . $_SESSION["GO_TO_PAY"] . "\" id=\"btnPayment\" name=\"btnPayment\" class=\"btn btn-warning pull-right\" onclick=\"payment();\">\n";
		$gateway .= "<i class=\"fa fa-money-bill-1\"></i>\n";
		$gateway .= "<span class=\"d-none d-sm-none d-md-none d-lg-block d-xl-inline-block\">" . $_SESSION["GO_TO_PAY"] . "</span>\n";
		$gateway .= "</button>\n";

		$ondeliver = "<button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . $_SESSION["SYSTEM_PAYMENT_TYPE_6"] . "\" id=\"btnOnDeliver\" name=\"btnOnDeliver\" class=\"btn btn-info pull-right\" onclick=\"onDeliver();\">\n";
		$ondeliver .= "<i class=\"fa fa-gift\"></i>\n";
		$ondeliver .= "<span class=\"d-none d-sm-none d-md-none d-lg-block d-xl-inline-block\">" . $_SESSION["SYSTEM_PAYMENT_TYPE_6"] . "</span>\n";
		$ondeliver .= "</button>\n";		

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

			_error_log("getMyPartners " . date("Y-m-d h:i:s"));

			//Obtiene los aliados
			$partners = $ptcl->getMyPartners();

			_error_log("getMyPartners finish " . date("Y-m-d h:i:s"));

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
			
			$pmnttype = $ptcl->client->PAYMENT_TYPE_ID;
			
			if(!$gtw) {
				$quot = new quota_employee();
				$quot->getInformationByOtherInfo("USER_ID", $_SESSION["vtappcorp_userid"],"CLIENT_ID",$client);
				if($quot->nerror == 0) {
					if($quot->AMOUNT > $quot->USED) {
						$quota = $quot->ID;
						$balance = $quot->AMOUNT - $quot->USED;
					}
					else {
						$buttons = $gateway;
						$change = true;
					}
				}
				/*
				else {
					$buttons = $gateway;
				}
				*/
			}
			else {
				$buttons = $gateway;
				$pmnttype = 3;
			}
			
			if($ptcl->client->client_type->CLIENT_TYPE_ID == 4) {
				$buttons = "<button type=\"button\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . $_SESSION["SYSTEM_PAYMENT_TYPE_6"] . "\" id=\"btnOnDeliver\" name=\"btnOnDeliver\" class=\"btn btn-info pull-right\" onclick=\"onDeliver();\">\n";
				$buttons .= "<i class=\"fa fa-gift\"></i>\n";
				$buttons .= "<span class=\"d-none d-sm-none d-md-none d-lg-block d-xl-inline-block\">" . $_SESSION["SYSTEM_PAYMENT_TYPE_6"] . "</span>\n";
				$buttons .= "</button>\n";
			}
		}
		
		_error_log("selectPartner " . date("Y-m-d h:i:s"));

		$datos = $rate->selectPartner($distance,$round == "true",$filter,$quota,$balance,(($quot == null) ? $quot : $quot->quota->type->discountType()),$pmnttype);
		
		_error_log("selectPartner finish " . date("Y-m-d h:i:s"), $rate->sql);

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
		$result["notification"] = $datos["notification"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}

	_error_log("__checkRate finish " . date("Y-m-d h:i:s"));

	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>