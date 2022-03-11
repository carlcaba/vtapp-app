<?
    //Inicio de sesion
	if (session_status() === PHP_SESSION_NONE) {
		session_name('vtappcorp_session');
		session_start();
	}	

    include_once("classes/interfaces.php");
    include_once("classes/resources.php");
    include_once("classes/users.php");

	//Verifica las variables globales
	if(!defined("APP_NAME")) {
		$config = new configuration("APP_NAME");
		define("APP_NAME", $config->verifyValue());
	}
	
	//Verifica el lenguage
	$lang = new language();
	$config = new configuration("DEFAULT_LANGUAGE");
	
	if(!defined("LANGUAGE")) {
		//$lid = $lang->getInformationByAbbr(locale_get_primary_language(null));
		$lid = $lang->getInformationByAbbr(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
		if($lid < 0)
			$lid =  $config->verifyValue();
		if(empty($_SESSION["LANGUAGE"])) {
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
	
	$config = new configuration("DIRECT_CHAT_TIME");
	$_SESSION["DIRECT_CHAT_TIME"] = $config->verifyValue();
	
	//Realiza la carga del lenguaje
    $resources = new resources();
    $resources->loadResources(LANGUAGE);

	//Funcion para agregar el trace de los webservices
	function addTraceWS($script, $params, $source, $result) {
		include_once("classes/ws_query.php");
		$ws = new ws_query();
		$ws->WEBSERVICE = $script;
		$ws->PARAMS = $params;
		$ws->CALLED_FROM = $source;
		$ws->RETURNED = $result;
		$ws->REGISTERED_ON = "NOW()";
		$ws->REGISTERED_BY = "wsconsume";
		$ws->_add();
		if($ws->nerror == 0)
			return $ws->ID;
		else 
			return -1;
	}

	//Funcion para actualizar el trace de los webservices
	function updateTraceWS($id, $result) {
		include_once("classes/ws_query.php");
		$ws = new ws_query();
		$ws->ID = $id;
		$ws->RETURNED = $result;
		$ws->MODIFIED_ON = "NOW()";
		$ws->MODIFIED_BY = "wsconsume";
		$ws->updateResult();
		if($ws->nerror == 0)
			return $ws->ID;
		else 
			return -1;
	}

?>