<?php


/*
// Your ID and token
$authToken = 'Basic c29hcF91c2VyQGV0Yi50ZXN0OkIwZzB0QA==';

// The data to send to the API
$postData = array(
        "inventoryType"=>"106-00123",
		"serialNumber" =>  "862860101010101111",
        "quantity" => 1,
        "XI_macn" => "2763871910801",
        "invtype" => "106-00123"
);

// Setup cURL
$ch = curl_init('https://api.etadirect.com/rest/ofscCore/v1/resources/etb.tecnico2/inventories');
curl_setopt_array($ch, array(
    CURLOPT_POST => TRUE,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
        'Authorization: '.$authToken,
        'Content-Type: application/json'
    ),
    CURLOPT_POSTFIELDS => json_encode($postData)
));

// Send the request
$response = curl_exec($ch);

// Check for errors
if($response === FALSE){
    die(curl_error($ch));
}

// Decode the response
$responseData = json_decode($response, TRUE);

// Print the date from the response
print_r($responseData);

	echo $_SERVER['DOCUMENT_ROOT'];
	echo "\n";
	$err = 0;
	echo $err;
	
	print_r($_SERVER);

	$url = "https://maps.googleapis.com/maps/api/geocode/json?address=";
	$add1 = "Carrera 7 # 20-00, Bogotá, Colombia";
	$add2 = "Carrera 98A # 65-10, Bogotá, Colombia";
	$key = "AIzaSyCBb-VBQcPc0IEML65ouEFV24FJChPlaVM";
	
	echo rawurlencode ($add1);
	echo "<br>";
	echo rawurlencode ($add2);
	
	echo "<br>";

	echo $url . rawurlencode ($add1);
	
	echo intval(str_replace(",","","1,000 Notificaciones"));
	
	try {
		$url = sprintf($url,$this->OtherUrlEncode($this->arrColDatas[$field . "_ADDRESS"]),$key);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);			
		$result = curl_exec($ch);
		curl_close ($ch);
		$data = json_decode($result);
		switch(json_last_error()) {
			case JSON_ERROR_NONE:
				break;
			case JSON_ERROR_DEPTH:
				throw new Exception('JSON DECODE ERROR - Excedido tamaño máximo de la pila\n' . $result);
				break;
			case JSON_ERROR_STATE_MISMATCH:
				throw new Exception('JSON DECODE ERROR - Desbordamiento de buffer o los modos no coinciden\n' . $result);
				break;
			case JSON_ERROR_CTRL_CHAR:
				throw new Exception('JSON DECODE ERROR - Encontrado carácter de control no esperado\n' . $result);
				break;
			case JSON_ERROR_SYNTAX:
				throw new Exception('JSON DECODE ERROR - Error de sintaxis, JSON mal formado\n' . $result);
				break;
			case JSON_ERROR_UTF8:
				throw new Exception('JSON DECODE ERROR - Caracteres UTF-8 malformados, posiblemente codificados de forma incorrecta\n' . $result);
				break;
			default:
				throw new Exception('JSON DECODE ERROR - Error desconocido\n' . $result);
			break;
		}			
		if($data->status != "OK") {
			throw new Exception($data->error_message);
		}
		if(!property_exists($data[0],"geometry")) {
			throw new Exception("No geometry found in address from GoogleMaps");
		}
		if(!property_exists($data[0]->geometry,"location")) {
			throw new Exception("No geometry.location found in address from GoogleMaps");
		}
	}
	catch (Exception $ex) {
		$this->nerror = 110;
		$this->error = $ex->getMessage();
		_error_log("Error getting coordinates: " . $ex->getMessage() . "\n" . $url . "\n" . $result);
		$data = null;
	}
	return $data;
*/	

	echo "<br />";
	echo date("y-m-d h:i:sa");
	echo "<br />";
	date_default_timezone_set('America/Bogota');
	echo date("y-m-d h:i:sa");
	echo "<br />";
	
	function uuidToHex($uuid) {
		return str_replace('-', '', $uuid);
	}
	
	function hexToUuid($hex) {
		$regex = '/^([\da-f]{8})([\da-f]{4})([\da-f]{4})([\da-f]{4})([\da-f]{12})$/';
		return preg_match($regex, $hex, $matches) ?
			"{$matches[1]}-{$matches[2]}-{$matches[3]}-{$matches[4]}-{$matches[5]}" :
			FALSE;
	}
	
	function hexToIntegers($hex) {
		$bin = pack('h*', $hex);
		return unpack('L*', $bin);
	}
	
	function integersToHex($integers) {
		$args = $integers; 
		$args[0] = 'L*'; 
		ksort($args);
		$bin = call_user_func_array('pack', $args);
		$results = unpack('h*', $bin);
		return $results[1];
	}
	
	$uuid = '1968ec4a-2a73-11df-9aca-00012e27a270';
	var_dump($uuid);
	
	$integers = hexToIntegers(uuidToHex('1968ec4a-2a73-11df-9aca-00012e27a270'));
	var_dump($integers);


	
	$uuid = hexToUuid(integersToHex($integers));
	var_dump($uuid);	
	
?>