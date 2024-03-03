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
                    'link' => 'users-manager.php?src=');
	
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
		$result["link"] = $link . "?src=" . $datas->src;

		//TODO Nativapps
		if (isset($_POST['dataSubscription'])) {
			//Realiza la operacion
			require_once("../../classes/affiliation_rate.php");
			require_once("../../classes/affiliate_subscription.php");
			require_once("../../classes/client.php");

			$dataSubscription = json_decode($_POST['dataSubscription']);

			/** Datos de facturación */
			$client_id = $dataSubscription->dataBillingData->client_id;
			$legal_representative = $dataSubscription->dataBillingData->legal_representative;
			
			/** Datos de la tarjeta */
			$credit_card_number = $dataSubscription->dataCardDetails->txtCREDIT_CARD_NUMBER;
			$valid_card = $dataSubscription->dataCardDetails->hfValidCard;
			$credit_card_name = $dataSubscription->dataCardDetails->txtCREDIT_CARD_NAME;
			$date_expiration = $dataSubscription->dataCardDetails->txtDATE_EXPIRATION;
			$verification_code = $dataSubscription->dataCardDetails->txtVERIFICATION_CODE;
			$total_subscription = $dataSubscription->totalSubscription;

			/** Guardando suscripción */
			$affiliate_subscription = new affiliate_subscription();
			$affiliate_subscription->CLIENT_ID = $client_id;
			$affiliate_subscription->DETAILED_PLAN = '';
			$affiliate_subscription->AMOUNT = $total_subscription;
			$affiliate_subscription->START_DATE = 'NOW()';
			$affiliate_subscription->CREDIT_CARD_NUMBER = str_replace(' ', '', $credit_card_number);
			$affiliate_subscription->CREDIT_CARD_NAME = $credit_card_name;
			$affiliate_subscription->DATE_EXPIRATION = $date_expiration;
			$affiliate_subscription->VERIFICATION_CODE = $verification_code;
			$affiliate_subscription->CARD_STATUS = $valid_card === 'true' ? 'valid' : 'invalid';
			$affiliate_subscription->_add("", LANGUAGE);

			/** Detalles de la suscripción */
			foreach ($dataSubscription->dataPersonalizePlan as $key => $rates) {
				$affiliation_rate = new affiliation_rate();
				$affiliation_rate->RESOURCE_NAME = $rates->resource_name;
				$affiliation_rate->CLIENT_ID = $client_id;
				$affiliation_rate->SUBSCRIPTION_ID = $affiliate_subscription->ID;
				$affiliation_rate->QUANTITY_USERS = $rates->quantities;
				$affiliation_rate->COST = $rates->unit_value;
				$affiliation_rate->_add("", LANGUAGE);
			}

			//Agrega representante legal al cliente
			$client = new client();
			$client->ID = $client_id;
			//Consulta la información
			$client->__getInformation();

			$client->LEGAL_REPRESENTATIVE = $legal_representative;
			$client->_modify();


			// error_log(date('d.m.Y h:i:s') . " - " . print_r($dataSubscription, true) . PHP_EOL, 3, 'my-errors.log');

			$result["link"] = $result["link"]."&subscribed_customer=".$client_id;
		}
		///////////////////////////////

	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>