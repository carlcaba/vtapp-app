<?php

    header('charset=utf-8');

/*
 * PHP mcrypt - Basic encryption and decryption of a string
 */
$string = "Some text to be encrypted";
$secret_key = "LogicaEstudioMet";

$text = "Estaba la pájara pinta a la sombra del verde limón";
echo html_entity_decode($text, ENT_COMPAT, 'UTF-8') . "<br />\n";
echo $text . "<br />\n";
echo htmlentities(htmlspecialchars($text)). "<br />\n";

echo locale_get_primary_language(null) . "<br />\n";


// Create the initialization vector for added security.
$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);

// Encrypt $string
$encrypted_string = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $secret_key, $string, MCRYPT_MODE_CBC, $iv);

// Decrypt $string
$decrypted_string = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $secret_key, $encrypted_string, MCRYPT_MODE_CBC, $iv);

//Encriptar 2
$enc2 = encriptar("andres.cabrera@logicaestudio.com"); 
$enc2 = "administrador";


echo "Original string : " . $string . "<br />\n";
echo "Encrypted string : " . $encrypted_string . "<br />\n";
echo "Decrypted string : " . $decrypted_string . "<br />\n";

echo "<br />Encrypt 2 : " . $enc2 . "<br />\n";
echo "Decrypt 2 : " . encriptar($enc2) . "<br />\n";

$thehmldata = "<input type=\"checkbox\" name=\"chkAdvance\" id=\"chkAdvance\" value=\"true\" /> Advance on select";

$htmlcode = htmlentities($thehmldata);
echo $htmlcode;

echo "<br/>\n";

$htmlcode = htmlentities(htmlspecialchars($thehmldata));
echo $htmlcode;

echo "<br/>\n";

echo html_entity_decode(htmlspecialchars_decode($htmlcode));

echo "<br/>\n";

echo realpath("../../") . "\n";

echo rawurlencode("employees.php?uid=" . $enc2);
echo "employees.php?uid=" . rawurlencode($enc2);





echo "hernanortiz -> " . desencriptar("UnclA4kclYgC3djP0kq3Y2a89Wd29tpo5rGmGSt+jZM=");
echo "jorgeortiz -> " . desencriptar("ZWu5Uqcvwlen+6kdrFOTU357nB6Ur7wcK2BFjgJD1T0=");

echo "<br /><br />3105698526 -> " . secured_encrypt("3105698526");
echo "<br /><br />3002764204 -> " . secured_encrypt("3002764204");




function encriptar($cadena){
	$key = 'logicaestudio.com';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
	$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cadena, MCRYPT_MODE_CBC, md5(md5($key))));
	return $encrypted; //Devuelve el string encriptado
}
 
function desencriptar($cadena){
	$key = 'logicaestudio.com';  // Una clave de codificacion, debe usarse la misma para encriptar y desencriptar
	$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	return $decrypted;  //Devuelve el string desencriptado
}


function secured_encrypt($data) {
	if(!defined('FIRSTKEY'))
		define('FIRSTKEY','Lk5Uz3slx3BrAghS1aaW5AYgWZRV0tIX5eI0yPchFz4=');			
	if(!defined("SECONDKEY")) 
		define('SECONDKEY','EZ44mFi3TlAey1b2w4Y7lVDuqO+SRxGXsa7nctnr/JmMrA2vN6EJhrvdVZbxaQs5jpSe34X3ejFK/o9+Y5c83w==');
	$first_key = base64_decode(FIRSTKEY);
	$second_key = base64_decode(SECONDKEY);   
	$method = "aes-256-cbc";   
	$iv_length = openssl_cipher_iv_length($method);
	$iv = openssl_random_pseudo_bytes($iv_length);
	$first_encrypted = openssl_encrypt($data,$method,$first_key, OPENSSL_RAW_DATA ,$iv);   
	$second_encrypted = hash_hmac('sha512', $first_encrypted, $second_key, TRUE);
	$output = base64_encode($iv.$second_encrypted.$first_encrypted);   
	return $output;       
}

function secured_decrypt($input) {
	if(!defined('FIRSTKEY'))
		define('FIRSTKEY','Lk5Uz3slx3BrAghS1aaW5AYgWZRV0tIX5eI0yPchFz4=');			
	if(!defined("SECONDKEY")) 
		define('SECONDKEY','EZ44mFi3TlAey1b2w4Y7lVDuqO+SRxGXsa7nctnr/JmMrA2vN6EJhrvdVZbxaQs5jpSe34X3ejFK/o9+Y5c83w==');
	$first_key = base64_decode(FIRSTKEY);
	$second_key = base64_decode(SECONDKEY);           
	$mix = base64_decode($input);
	$method = "aes-256-cbc";   
	$iv_length = openssl_cipher_iv_length($method);
	$iv = substr($mix,0,$iv_length);
	$second_encrypted = substr($mix,$iv_length,64);
	$first_encrypted = substr($mix,$iv_length+64);
	$data = openssl_decrypt($first_encrypted,$method,$first_key,OPENSSL_RAW_DATA,$iv);
	$second_encrypted_new = hash_hmac('sha512', $first_encrypted, $second_key, TRUE);
	if (hash_equals($second_encrypted,$second_encrypted_new))
		return $data;
	return false;
}


?>

