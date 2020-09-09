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



?>

