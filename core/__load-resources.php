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
	
	$parentFileName = null;

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

	function _http_response_code($msg, $code = NULL) {
		if ($code !== NULL) {
			switch ($code) {
				case 100: $text = 'Continue'; break;
				case 101: $text = 'Switching Protocols'; break;
				case 200: $text = 'OK'; break;
				case 201: $text = 'Created'; break;
				case 202: $text = 'Accepted'; break;
				case 203: $text = 'Non-Authoritative Information'; break;
				case 204: $text = 'No Content'; break;
				case 205: $text = 'Reset Content'; break;
				case 206: $text = 'Partial Content'; break;
				case 300: $text = 'Multiple Choices'; break;
				case 301: $text = 'Moved Permanently'; break;
				case 302: $text = 'Moved Temporarily'; break;
				case 303: $text = 'See Other'; break;
				case 304: $text = 'Not Modified'; break;
				case 305: $text = 'Use Proxy'; break;
				case 400: $text = 'Bad Request'; break;
				case 401: $text = 'Unauthorized'; break;
				case 402: $text = 'Payment Required'; break;
				case 403: $text = 'Forbidden'; break;
				case 404: $text = 'Not Found'; break;
				case 405: $text = 'Method Not Allowed'; break;
				case 406: $text = 'Not Acceptable'; break;
				case 407: $text = 'Proxy Authentication Required'; break;
				case 408: $text = 'Request Time-out'; break;
				case 409: $text = 'Conflict'; break;
				case 410: $text = 'Gone'; break;
				case 411: $text = 'Length Required'; break;
				case 412: $text = 'Precondition Failed'; break;
				case 413: $text = 'Request Entity Too Large'; break;
				case 414: $text = 'Request-URI Too Large'; break;
				case 415: $text = 'Unsupported Media Type'; break;
				case 419: $text = 'Unauthorized'; break;
				case 500: $text = 'Internal Server Error'; break;
				case 501: $text = 'Not Implemented'; break;
				case 502: $text = 'Bad Gateway'; break;
				case 503: $text = 'Service Unavailable'; break;
				case 504: $text = 'Gateway Time-out'; break;
				case 505: $text = 'HTTP Version not supported'; break;
				default:
					exit('Unknown http status code "' . htmlentities($code) . '"');
				break;
			}
			$text .= " " . $msg;
			$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
			header($protocol . ' ' . $code . ' ' . $text);
			$GLOBALS['http_response_code'] = $code;

		} 
		else {
			$code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
		}

		return $code;
	}

?>