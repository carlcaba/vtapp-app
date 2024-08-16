<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/quota_employee.php");

	_error_log("__loadClientQuota start " . date("Y-m-d h:i:s"));
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					"quota_id" => "");
	
	$client = "";
	$user = "";
	$id = "";
	//Captura las variables
	if(empty($_POST['client'])) {
		//Verifica el GET
		if(empty($_GET['client'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$client = $_GET["client"];
			$user = $_GET["user"];
		}
	}
	else {
		$client = $_POST["client"];
		$user = $_POST["user"];
	}
		
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		if($client == "") {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		if($user != "" && $_SESSION["vtappcorp_useraccess"] != "GOD" && $_SESSION["vtappcorp_useraccess"] != "ADM") {
			$usua = new users($user);
			if (!preg_match('/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/', strtoupper($id))) {
				$id = $usua->REFERENCE;
				if($id != $client) {
					$result["message"] = $_SESSION["CLIENT_NOT_REFERENCE"];
					$result = utf8_converter($result);
					exit(json_encode($result));
				}
			}
		}
		
		_error_log("getInformationByOtherInfo2 " . date("Y-m-d h:i:s"));

		//Asigna la informacion
		$quota = new quota_employee();
		//Verifica la fuente
		if($user != "") {
			$row = $quota->getInformationByOtherInfo2("CLIENT_ID",$client,"USER_ID",$user);
		}
		else {
			$row = $quota->getInformationByOtherInfo2("CLIENT_ID",$client);
		}
		
		_error_log("getInformationByOtherInfo2 finish " . date("Y-m-d h:i:s"));
				
		//Si no hay cupo asignado
		if($quota->nerror > 0) {
			$result["message"] = $_SESSION["CLIENT_NO_QUOTA"];
			$result["sql"] = $quota->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Si el cupo ya esta completo
		if($quota->AMOUNT - $quota->USED <= 0) {
			$result["message"] = $_SESSION["CLIENT_QUOTA_EMPTY"];
			$result["sql"] = $quota->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}

		$result["quota_id"] = $row["id"];
		$result["repeated"] = filter_var($row["repeated"], FILTER_VALIDATE_BOOLEAN);
		$result["period"] = $row["period"];
		$result["lastDate"] = $row["last_date"];
		$result["is_quota"] = $row["quota"];
		$result["message"] = "";
		$result["success"] = true;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}

	_error_log("__loadClientQuota finish " . date("Y-m-d h:i:s"));

	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));

	
?>