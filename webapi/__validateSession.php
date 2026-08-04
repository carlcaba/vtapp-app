<?
	//Servicio que verifica la sesion del usuario
	//LOGICA ESTUDIO 2025
	
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET, POST, PUT');	

	//Incluye las clases necesarias
	require_once("../core/classes/resources.php");
	require_once("../core/classes/external_session.php");
	require_once("../core/classes/configuration.php");

	//Verifica si esta habilitado el debug
	if(!defined("DEBUG")) {
		$conf = new configuration("DEBUGGING");
		$debug = $conf->verifyValue();
		if($debug === 0)
			$debug = false;
		define("DEBUG", $debug); 
	}
	
	function checkSession($server) {
		try {
			//Carga los recursos
			include_once("../core/__load-resources.php");
			
			//Variable del codigo
			$resultado = array('success' => false,
							'message' => "No data for validate",
							"login" => "",
							"description" => "");
							
			switch(true) {
				case array_key_exists('HTTP_AUTHORIZATION', $server) :
					$authHeader = $server['HTTP_AUTHORIZATION'];
					break;
				case array_key_exists('Authorization', $server) :
					$authHeader = $server['Authorization'];
					break;
				case function_exists('apache_request_headers') :
					$requestHeaders = apache_request_headers();
					// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
					$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
					//print_r($requestHeaders);
					if (isset($requestHeaders['Authorization'])) {
						$authHeader = trim($requestHeaders['Authorization']);
					}
					break;
				default :
					$authHeader = null;
					break;
			}
			preg_match('/Bearer\s(\S+)/', $authHeader, $matches);
			if(!isset($matches[1])) {
				throw new Exception('No Bearer Token');
			}						
						
			$reso = new resources(basename(__FILE__));
			$result["description"] = $reso->getResourceByName(explode(".",basename(__FILE__))[0],2);
			
			$validate = validateJWTToken($matches[1]);
							
			if(!$validate["success"]) {
				throw new Exception($validate["message"]);
			}
			
			$txid = $validate["login"];
			
			$external = new external_session();
			$external->ID = $txid;
			//Busca la informacion
			$external->__getInformation();
			//Si hay error
			if($external->nerror > 0) {

				_error_log(print_r($validate,true));
				
				$resultado["message"] = "Session not valid";
				//Termina
				return $resultado;
			}
			
			$login = $external->USER_ID;

			//Verifica el tiempo de la sesion
			$conf = new configuration("SESSION_EXPIRATION_MESSENGER");
			$max_time = $conf->verifyValue();
			
			//Toma el dateStamp del servidor
			$ahorita = date_create("now",timezone_open(date_default_timezone_get()));
			$now = date("Y-n-j H:i:s", $ahorita->getTimestamp());
			//Verifica la fecha de login
			$logtime = $external->MODIFIED_ON == null ? $external->REGISTERED_ON : $external->MODIFIED_ON;

			//Calcula la diferencia
			$time = (strtotime($now)-strtotime($logtime));

			//Si ya expiro la sesion
			if($time >= $max_time) {
				//Cierra la sesión
				$external->logOut();
				//Actualiza el resultado
				$resultado["message"] = "Session expired";
				//Termina
				return $resultado;
			}

			//Actualiza
			$external->USER_ID = $login;
			$external->MODIFIED_BY = "API.Service";
			$external->MODIFIED_ON = "NOW()";
			$external->_modify();

			if($external->nerror > 0) {
				$resultado["error"] = $external->error;
			}

			$resultado["login"] = $login;
			$resultado["success"] = true;
			$resultado["message"] = "Validation ok";
			
			$_SESSION["vtappcorp_userid"] = "APIUser";
			$_SESSION['vtappcorp_username'] = "WebAPI User";
			$_SESSION['vtappcorp_useraccessid'] = 90;
			$_SESSION['vtappcorp_useraccess'] = "ADM";
			
			//Termina
			return $resultado;
		}
		catch (Exception $ex) {
			_error_log("Error during token validation: " . $ex->getMessage());			
			return false;
		}		
	}
	
	function base64UrlEncode($text)
	{
		return str_replace(
			['+', '/', '='],
			['-', '_', ''],
			base64_encode($text)
		);
	}

	function generateJWTToken($uuid, $idws) {
		//Verifica el tiempo de la sesion
		$conf = new configuration("API_SECRET_KEY");
		$key = $conf->verifyValue();
		$expTime = $conf->verifyValue("SESSION_EXPIRATION_MESSENGER");
		/*
		$headers = [ "alg" => "HS512"];
		$headers_encoded = base64UrlEncode(json_encode($headers));
		$issuedAt = time();
		$payload =  [
			"id" => $uuid, 
			"sub"=> "APIService", 
			"exp"=> $issuedAt + 30,
			"iss"=> "UBIO Inc",
			"iat"=> $issuedAt,
			"PAYLOAD"=> "<COMMAND><TYPE>REQUEST</TYPE><INTERFACE>TESTACCOUNT</INTERFACE> <REQUESTID>123</REQUESTID></COMMAND>"
		];
		$payload_encoded = base64UrlEncode(json_encode($payload));
		$signature = hash_hmac('sha512',"$headers_encoded.$payload_encoded",$key,true);
		$signature_encoded = base64UrlEncode($signature);
		$token = "$headers_encoded.$payload_encoded.$signature_encoded";
		return $token;
		*/
		
		// get the local secret key
		$secret = $key;
		$issuedAt = time();
		// Create the token header
		$headers = json_encode([
						'typ' => 'JWT',
						'alg' => 'HS512'
					]);

		// Create the token payload
		$payload = json_encode([
			'id' => $uuid,
			'user_id' => $idws,
			'role' => 'API.Service',
			'exp' => time() + $exptime
		]);
		// Encode Header
		$base64UrlHeader = base64UrlEncode($headers);
		// Encode Payload
		$base64UrlPayload = base64UrlEncode($payload);
		// Create Signature Hash
		$signature = hash_hmac('sha512', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
		// Encode Signature to Base64Url String
		$base64UrlSignature = base64UrlEncode($signature);
		// Create JWT
		$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;		
		return $jwt;
	}
	
	function validateJWTToken($jwt) {
		//Verifica el tiempo de la sesion
		$conf = new configuration("API_SECRET_KEY");
		$secret = $conf->verifyValue();
		// split the token
		$tokenParts = explode('.', $jwt);
		$header = base64_decode($tokenParts[0]);
		$payload = base64_decode($tokenParts[1]);
		$signatureProvided = $tokenParts[2];

		// check the expiration time - note this will cause an error if there is no 'exp' claim in the token
		$pyObj = json_decode($payload);
		
		//_error_log(print_r(json_last_error_msg(),true));
		//Variable del codigo
		$result = array('success' => false,
						'message' => "No data for validate",
						"login" => "",
						"idws" => 0);		
		
		$timestamp = $pyObj->exp;
		$expiration = new DateTime();
		$expiration->setTimestamp($timestamp);		

		$now = new DateTime();
		$ahorita = date_create("now",timezone_open(date_default_timezone_get()));
		$now->setTimestamp($ahorita->getTimestamp());

		//$expiration = Carbon::createFromTimestamp(json_decode($payload)->exp);
		//$tokenExpired = (Carbon::now()->diffInSeconds($expiration, false) < 0);
		//$tokenExpired = ($now->diffInSeconds($expiration, false) < 0);
		$interval = $now->diff($expiration);
		$tokenExpired = ($interval->days * 86400 + 
						$interval->h * 3600 +    
						$interval->i * 60 +
						$interval->s) < 0;

		// build a signature based on the header and payload using the secret
		$base64UrlHeader = base64UrlEncode($header);
		$base64UrlPayload = base64UrlEncode($payload);
		$signature = hash_hmac('sha512', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
		$base64UrlSignature = base64UrlEncode($signature);

		// verify it matches the signature provided in the token
		$signatureValid = ($base64UrlSignature === $signatureProvided);

		/*
		_error_log("Header:\n" . $header);
		_error_log("Payload:\n" . $payload);
		_error_log("Timestamp. " . $timestamp);
		_error_log("Now. " . print_r($now,true));
		_error_log("Expiration. " . print_r($expiration,true));
		_error_log("PyObj. " . print_r($pyObj,true));
		_error_log("Difference. " . ($interval->days * 86400 + 
						$interval->h * 3600 +    
						$interval->i * 60 +
						$interval->s));
		*/
		if ($tokenExpired) {
			$result["message"] = "Token has expired. " . print_r($expiration,true);
		}
		else if (!$signatureValid) {
			$result["message"] = "The signature is NOT valid.";
		}
		else {
			$result["success"] = true;
			$result["login"] = $pyObj->id;
			$result["idws"] = $pyObj->user_id;
		}
		
		return $result;
	}
?>