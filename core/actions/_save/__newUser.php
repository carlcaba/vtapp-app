<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

    include_once("../../classes/interfaces.php");
    include_once("../../classes/resources.php");
	include_once("../../classes/configuration.php");

	//Verifica las variables globales
	if(!defined("APP_NAME")) {
		$config = new configuration("APP_NAME");
		define("APP_NAME", $config->verifyValue());
	}
	//Verifica el lenguage
	$lang = new language();
	$config = new configuration("DEFAULT_LANGUAGE");
	
	if(!defined("LANGUAGE")) {
		$lid = $lang->getInformationByAbbr(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
		if($lid < 0)
			$lid =  $config->verifyValue();
		if(empty($_SESSION["LANGUAGE"])) {
			if(!defined('LANGUAGE'))
				define("LANGUAGE", $lid);
			$_SESSION["LANGUAGE"] = $lid;
		}
		else {
			if(!defined('LANGUAGE'))
				define("LANGUAGE", $_SESSION["LANGUAGE"]);
		}
	}
	else {
		if(empty($_SESSION["LANGUAGE"])) {
			$_SESSION["LANGUAGE"] = LANGUAGE;
		}
	}	
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'users.php');
	
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
	
	$link = "users-manager.php";
	
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$datas = json_decode($strmodel);
		
		//Realiza la operacion
		require_once("../../classes/users.php");
		
		$conf = new configuration();
		
		//Asigna la informacion
		$usua = new users($datas->txtID);
		//Consulta la informacion
		$usua->__getInformation();
		//Si hay error
		if($usua->nerror == 0) {
			$result["message"] = $_SESSION["MSG_DUPLICATED_RECORD"];
			$result["sql"] = $usua->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		$usua->EMAIL = $datas->txtEMAIL;
		//Consulta el email
		$usua->getInfoByMail();
		//Si hay error
		if($usua->nerror == 0) {
			$result["message"] = $_SESSION["MSG_DUPLICATED_EMAIL"];
			$result["sql"] = $usua->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Actualiza la información
		$usua->setAccess($datas->cbAccess);
		$usua->setCity($datas->cbCity);
		$usua->IDENTIFICATION = $datas->cbTBL_SYSTEM_USER_IDENTIFICATION . "-" . $datas->txtTBL_SYSTEM_USER_IDENTIFICATION;
		if($datas->hfSocialNetwork != "disabled") {
			if($datas->chkUserType == "true") {
				$usua->FACEBOOK_USER = $datas->txID;
				$usua->GOOGLE_USER = "";
			}
			else {
				$usua->FACEBOOK_USER = "";
				$usua->GOOGLE_USER = $datas->txID;
			}
		}
		else {
			$usua->FACEBOOK_USER = "";
			$usua->GOOGLE_USER = "";
		}
		$usua->LATITUDE = $datas->hfLATITUDE;
		$usua->LONGITUDE = $datas->hfLONGITUDE;
		$usua->ADDRESS = $datas->txtADDRESS;
		$usua->CELLPHONE = $datas->txtCELLPHONE;
		$usua->FIRST_NAME = $datas->txtFIRST_NAME;
		$usua->LAST_NAME = $datas->txtLAST_NAME;
		$usua->PHONE = $datas->txtPHONE;
		$usua->REFERENCE = $datas->cbReference;
		$usua->THE_PASSWORD = $conf->verifyValue("INIT_PASSWORD");
		$usua->CHANGE_PASSWORD = $datas->cbChangePassword == "" ? "FALSE" : strtoupper($datas->cbChangePassword);
		$usua->IS_BLOCKED = $datas->cbBlocked == "" ? "FALSE" : strtoupper($datas->cbBlocked);
		
		$usua->__add("",LANGUAGE);

		$error = false;

		//Si hay error
		if($usua->nerror > 0) {
			$result["sql"] = $usua->sql;
			//Si es error de correo
			if($usua->nerror != 18)
				//Confirma mensaje al usuario
				$result['message'] = $usua->nerror . ". " . $usua->error;
			else 
				$result['message'] = $usua->nerror . ". " . $usua->error;
			$error = true;
		}

		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = ($error) ? $_SESSION["USER_REGISTERED"] . "<br />" . $result['message'] : $_SESSION["USER_REGISTERED"];
		$result["link"] = $link;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>