<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/quota_employee.php");
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);
	
	$source = "";
	$id = "";
	$area = "";
	$user = "";
	$viewData = "new";
	//Captura las variables
	if(empty($_POST['source'])) {
		//Verifica el GET
		if(empty($_GET['source'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$source = $_GET['source'];
			$id = $_GET["id"];
			$area = $_GET["area"];
			$user = $_GET["user"];
		}
	}
	else {
		$source = $_POST['source'];
		$id = $_POST["id"];
		$area = $_POST["area"];
		$user = $_POST["user"];
	}
		
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		if($user != "") {
			require_once("../../classes/interfaces.php");
			$inter = new interfaces();
			$user = $inter->decrypt($user);			
			$usua = new users($user);
			if (!preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', strtoupper($id))) {
				$id = $usua->REFERENCE;
			}
		}
		
		//Asigna la informacion
		$quota = new quota_employee();
		//Verifica la fuente
		$row = $quota->getInformationByOtherInfo("CLIENT_ID",$id);
		
		//Si no hay cupo asignado
		if($quota->nerror > 0) {
			$result["message"] = $_SESSION["CLIENT_NO_QUOTA"];
			$result["sql"] = $quota->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Si el cupo ya esta completo
		if($quota->quota->AMOUNT - $quota->quota->USED <= 0) {
			$result["message"] = $_SESSION["CLIENT_QUOTA_EMPTY"];
			$result["sql"] = $quota->sql;
			$result["data"] = print_r($quota,true);
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		if($area != "") {
			$quota->setArea($area);
		}
		
		if($user != "") {
			$quota->USER_ID = $user;
		}

		$quota->quota->setClient($id);
		
		//Datos de la forma
		$dataForm = $quota->dataForm($viewData,$source);
		
		//Genera la forma
		//variable a retornar
		$form = $dataForm["tabs"] . "<form id=\"frmAddFunds\" name=\"frmAddFunds\" role=\"form\">\n";

		$cont = 0;
		//Muestra la GUI
		if($viewData != "new") {
			$form .= $quota->showField("ID", $dataForm["tabs"] . "\t", "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]);
		}
		else {
			$form .= $dataForm["tabs"] . "\t\t\t\t<input type=\"hidden\" name=\"txtID\" id=\"txtID\" value=\"" . $quota->ID . "\" required=\"required\" />\n";
			$cont++;
		}

		$form .= $dataForm["tabs"] . "\t<div class=\"form-group\">\n";
		$form .= $dataForm["tabs"] . "\t\t<label>" . $quota->arrColComments["CLIENT_ID"] . " *</label>\n";
		$form .= $dataForm["tabs"] . "\t\t\t<select class=\"form-control\" id=\"cbClient\" name=\"cbClient\" " . $dataForm["readonly"][$cont++] . " required>\n";
		$form .= $quota->client->showOptionList(8,$quota->CLIENT_ID);
		$form .= $dataForm["tabs"] . "\t\t\t</select>\n";
		$form .= $dataForm["tabs"] . "\t</div>\n";

		$form .= $dataForm["tabs"] . "\t<div class=\"form-group\">\n";
		$form .= $dataForm["tabs"] . "\t\t<label>" . $quota->arrColComments["AREA_ID"] . " *</label>\n";
		$form .= $dataForm["tabs"] . "\t\t\t<select class=\"form-control\" id=\"cbArea\" name=\"cbArea\" " . $dataForm["readonly"][$cont++] . " required>\n";
		$form .= $quota->area->showOptionList(8,$quota->AREA_ID);
		$form .= $dataForm["tabs"] . "\t\t\t</select>\n";
		$form .= $dataForm["tabs"] . "\t</div>\n";

		$form .= $dataForm["tabs"] . "\t<div class=\"form-group\">\n";
		$form .= $dataForm["tabs"] . "\t\t<label>" . $quota->arrColComments["USER_ID"] . " *</label>\n";
		$form .= $dataForm["tabs"] . "\t\t\t<select class=\"form-control\" id=\"cbUser\" name=\"cbUser\" " . $dataForm["readonly"][$cont++] . " required>\n";
		$form .= $quota->user->showOptionList(8,$quota->USER_ID,$id);
		$form .= $dataForm["tabs"] . "\t\t\t</select>\n";
		$form .= $dataForm["tabs"] . "\t</div>\n";

		$form .= $dataForm["tabs"] . "\t<div class=\"form-group\">\n";
		$form .= $dataForm["tabs"] . "\t\t<label>" . $quota->arrColComments["QUOTA_ID"] . " *</label>\n";
		$form .= $dataForm["tabs"] . "\t\t\t<select class=\"form-control\" id=\"cbQuota\" name=\"cbQuota\" " . $dataForm["readonly"][$cont++] . " required>\n";
		$form .= $quota->quota->showOptionList(8,$quota->QUOTA_ID);
		$form .= $dataForm["tabs"] . "\t\t\t</select>\n";
		$form .= $dataForm["tabs"] . "\t</div>\n";

		$form .= $quota->showField("AMOUNT", $dataForm["tabs"] . "\t", "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]);
		$form .= $quota->showField("USED", $dataForm["tabs"] . "\t", "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]);
		
		$form .= $dataForm["tabs"] . "\t<div class=\"form-group\">\n";
		$form .= $dataForm["tabs"] . "\t\t<label>" . $quota->arrColComments["IS_BLOCKED"] . " *</label>\n";
		$form .= $dataForm["tabs"] . "\t\t\t<select class=\"form-control\" id=\"cbBlocked\" name=\"cbBlocked\" " . $dataForm["readonly"][$cont++] . ">\n";
		$form .= $dataForm["tabs"] . "\t\t\t\t<option value=\"FALSE\"" . ($quota->IS_BLOCKED ? "" : " selected") . ">" . $_SESSION["ACTIVE"] . "</option>\n";
		$form .= $dataForm["tabs"] . "\t\t\t\t<option value=\"TRUE\"" . ($quota->IS_BLOCKED ? " selected" : "") . ">" . $_SESSION["IS_BLOCKED"] . "</option>\n";
		$form .= $dataForm["tabs"] . "\t\t\t</select>\n";
		$form .= $dataForm["tabs"] . "\t</div>\n";

		if($viewData != "new") {
			$form .= $quota->showField("REGISTERED_ON", $dataForm["tabs"] . "\t", "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]);
			$form .= $quota->showField("REGISTERED_BY", $dataForm["tabs"] . "\t", "", "", $dataForm["showvalue"], "", false, "9,9,12", $dataForm["readonly"][$cont++]);
		}
		else {
			$cont++;
			$cont++;
		}
		
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
	
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));

	
?>