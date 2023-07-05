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
	
	include_once("../../__load-resources.php");
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => '../index.php',
					'error' => null,
					'access' => null);
	
	//Captura las variables
	if(empty($_POST['strModel'])) {
		//Verifica el GET
		if(empty($_GET['strModel'])) {
			goto FinalEnd;
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
		
		//Realiza la operacion
		require_once("../../classes/users.php");
		require_once("../../classes/client.php");
		
		$conf = new configuration();
		
		//Asigna la informacion
		$usua = new users(explode("@",$datas->txtEmail)[0]);
		//Consulta la informacion
		$usua->__getInformation();
		//Si hay error
		while($usua->nerror == 0) {
			$num = rand(pow(10, 1), pow(10, 2)-11);
			$usua->ID = explode("@",$datas->txtEmail)[0] . $num;
			//Consulta la informacion
			$usua->__getInformation();
		}
		
		$usua->EMAIL = $datas->txtEmail;
		//Consulta el email
		$usua->getInfoByMail();
		//Si hay error
		if($usua->nerror == 0) {
			$result["message"] = $_SESSION["MSG_DUPLICATED_EMAIL"];
			$result["sql"] = $usua->sql;
			goto FinalEnd;
		}
		
		$result["access"] = array("user" => $usua->ID,
									"pass" => $datas->txtPassword);
		
		//Actualiza la información
		$usua->setAccess(40);
		$usua->setCity($datas->cbCity);
		$usua->IDENTIFICATION = "";
		$usua->LATITUDE = "";
		$usua->LONGITUDE = "";
		$usua->ADDRESS = $datas->txtAddress;
		$usua->CELLPHONE = $datas->txtPhone;
		$usua->FIRST_NAME = $datas->txtName;
		$usua->LAST_NAME = $datas->txtLastName;
		$usua->PHONE = $datas->txtPHONE;
		$usua->REFERENCE = "";
		$usua->THE_PASSWORD = $datas->txtPassword;
		$usua->CHANGE_PASSWORD = "FALSE";
		$usua->IS_BLOCKED = "FALSE";
		
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

		//Agrega el cliente
		$clie = new client();
		$clie->CLIENT_NAME = $datas->txtCompany;
		$clie->setClientType($datas->ClientType);
		$clie->setClientPaymentType($datas->PaymentType);
		$clie->IDENTIFICATION = $usua->IDENTIFICATION;
		$clie->ADDRESS = $usua->ADDRESS;
		$clie->PHONE = $usua->PHONE;
		$clie->CELLPHONE = $usua->CELLPHONE;
		$clie->setCity($datas->cbCity);
		$clie->EMAIL = $usua->EMAIL;
		$clie->CONTACT_NAME = $usua->FIRST_NAME . " " . $usua->LAST_NAME;
		$clie->EMAIL_CONTACT = $usua->EMAIL;
		$clie->PHONE_CONTACT = $usua->PHONE;
		$clie->CELLPHONE_CONTACT= $usua->CELLPHONE_CONTACT;
		$clie->EXPIRES_ON = date('Y-m-d', strtotime('+1 years'));
		
		//Lo adiciona
		$clie->_add();
		//Verifica si hubo error
		if($clie->nerror > 0) {
			$result["message"] = "Hubo un error creando el cliente. Por favor reportelo a UBIO con la siguiente información: " . $clie->error;
			$result["sql"] = $clie->sql;
			$result["error"] = true;
			goto FinalEnd;
		}
		
		//Actualiza la referencia del usuario
		$usua->REFERENCE = $clie->ID;
		$usua->_modify();

		//Verifica si hubo error
		if($usua->nerror > 0) {
			$result["message"] = "Hubo un error asociando el cliente al usuario. Por favor reportelo a UBIO con la siguiente información: " . $usua->error;
			$result["sql"] = $usua->sql;
			$result["error"] = true;
			goto FinalEnd;
		}
		
		//Envia los correos requeridos
		$clie->sendRegistration($usua,$datas->txtPassword);

		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = ($error) ? $_SESSION["USER_REGISTERED"] . "<br />" . $result['message'] : $_SESSION["USER_REGISTERED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	
FinalEnd:
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>