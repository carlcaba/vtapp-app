<?php
// Define a 32-byte (64 character) hexadecimal encryption key
// Note: The same encryption key used to encrypt the data must be used to decrypt the data

function Encriptar($cadena) {
	$key = "logicaestudio.com";
	// # return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
	//   return openssl_encrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv);
	if(function_exists("mcrypt_encrypt"))
		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $cadena, MCRYPT_MODE_CBC, md5(md5($key))));
	else if(function_exists("openssl_encrypt"))
		$encrypted = base64_encode(openssl_encrypt($cadena, 'AES-256-CBC-HMAC-SHA256', md5($key), OPENSSL_RAW_DATA, md5(md5($key))));
	return $encrypted;
}

function Desencriptar($cadena) {
	$key = "logicaestudio.com";
	//# return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, $iv);
	//  return openssl_decrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv);
	if(function_exists("mcrypt_decrypt"))
		$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($cadena), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
	else if(function_exists("openssl_decrypt"))
		$decrypted = rtrim(openssl_decrypt(base64_decode($cadena), 'AES-256-CBC-HMAC-SHA256', md5($key), OPENSSL_RAW_DATA, md5(md5($key))), "\0");
	return $decrypted;
}	

function Encriptar2($cadena) {
	$key = "logicaestudio.com";
	$encrypted = base64_encode(openssl_encrypt($cadena, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $key));
	return $encrypted;
}

function Desencriptar2($cadena) {
	$key = "logicaestudio.com";
	$decrypted = rtrim(openssl_decrypt(base64_decode($cadena), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $key), "\0");
	return $decrypted;
}	

// Create The First Key
echo base64_encode(openssl_random_pseudo_bytes(32)) . "<br>";

// Create The Second Key
echo base64_encode(openssl_random_pseudo_bytes(64)) . "<br>";
// Save The Keys In Your Configuration File
define('FIRSTKEY','Lk5Uz3slx3BrAghS1aaW5AYgWZRV0tIX5eI0yPchFz4=');
define('SECONDKEY','EZ44mFi3TlAey1b2w4Y7lVDuqO+SRxGXsa7nctnr/JmMrA2vN6EJhrvdVZbxaQs5jpSe34X3ejFK/o9+Y5c83w==');

function secured_encrypt($data) {
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
		
echo '<h1>Rijndael 256-bit CBC Encryption Function</h1>';

// Conectarse a y seleccionar una base de datos de MySQL llamada sakila
// Nombre de host: 127.0.0.1, nombre de usuario: tu_usuario, contraseña: tu_contraseña, bd: sakila
//$mysqli = new mysqli('127.0.0.1', "vtappcorp_u", "Vt4ppC0rp0r1t3$", "vtappcorp");
/*
										"db" => "logicaad_vtapp",
										"host" => "162.215.248.225",
										"port" => 3306,
										"user" => "logicaad_vtapp_u",
										"pass" => "Vt4ppC0rp0r1t3$")
*/
$mysqli = new mysqli("162.215.248.225", "logicaad_vtapp_u", "Vt4ppC0rp0r1t3$", "logicaad_vtapp");
// ¡Oh, no! Existe un error 'connect_errno', fallando así el intento de conexión
if ($mysqli->connect_errno) {
    // La conexión falló. ¿Que vamos a hacer? 
    // Se podría contactar con uno mismo (¿email?), registrar el error, mostrar una bonita página, etc.
    // No se debe revelar información delicada

    // Probemos esto:
    echo "Lo sentimos, este sitio web está experimentando problemas.";

    // Algo que no se debería de hacer en un sitio público, aunque este ejemplo lo mostrará
    // de todas formas, es imprimir información relacionada con errores de MySQL -- se podría registrar
    echo "Error: Fallo al conectarse a MySQL debido a: \n";
    echo "Errno: " . $mysqli->connect_errno . "\n";
    echo "Error: " . $mysqli->connect_error . "\n";
    
    // Podría ser conveniente mostrar algo interesante, aunque nosotros simplemente saldremos
    exit;
}

echo "Connected to logicaad_vtapp => logicaad_vtapp_u@162.215.248.225 <br>";

// Realizar una consulta SQL
$sql = "SELECT KEY_NAME, KEY_VALUE FROM TBL_SYSTEM_CONFIGURATION WHERE KEY_NAME = 'SUPERADMIN_PASSWORD'";
if (!$resultado = $mysqli->query($sql)) {
    // ¡Oh, no! La consulta falló. 
    echo "Lo sentimos, este sitio web está experimentando problemas.";

    // De nuevo, no hacer esto en un sitio público, aunque nosotros mostraremos
    // cómo obtener información del error
    echo "Error: La ejecución de la consulta falló debido a: \n";
    echo "Query: " . $sql . "\n";
    echo "Errno: " . $mysqli->errno . "\n";
    echo "Error: " . $mysqli->error . "\n";
    exit;
}

$passwd = $resultado->fetch_assoc();

echo "Encrypted password: " . $passwd["KEY_VALUE"] . "<br>";
echo "Decrypted password: " . Desencriptar($passwd["KEY_VALUE"]) . "<br>";

$test = secured_encrypt(Desencriptar($passwd["KEY_VALUE"]));
echo "Another Encrypted password: " . $test . "<br>";
echo "Another Decrypted password: " . secured_decrypt($test) . "<br>";

echo "Updating... <br>";
/*
$sql = "UPDATE TBL_SYSTEM_CONFIGURATION SET KEY_VALUE = '$test' WHERE KEY_NAME = 'SUPERADMIN_PASSWORD'";
printf($sql. "<br />");
if($mysqli->query($sql))
	printf("SYSTEM CONFIGURATION updated successfully.<br />");
if ($mysqli->errno)
	printf("Could not update table: %s<br />", $mysqli->error);
*/
$sql = "SELECT ID, THE_PASSWORD FROM TBL_SYSTEM_USER";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		printf("Id: %s, Pass: %s <br />", $row["ID"], $row["THE_PASSWORD"]);
		//$test = secured_encrypt(Desencriptar($row["THE_PASSWORD"]));
		$test = $row["THE_PASSWORD"];
		//$sql = "UPDATE TBL_SYSTEM_USER SET THE_PASSWORD = '$test' WHERE ID = '" . $row["ID"] . "'";
		//printf($sql. "<br />");
		//if($mysqli->query($sql))
		//	printf("USER " . $row["ID"] . " updated successfully.<br />");
		//if ($mysqli->errno)
		//	printf("Could not update table USER " . $row["ID"] . ": %s<br />", $mysqli->error);
		printf("Id: %s, Pass: %s <br />", $row["ID"], secured_decrypt($test));
	}
}
else {
	printf('No record found.<br />');
}
mysqli_free_result($result);
$mysqli->close();

/*
E03kBhdsMA1gVr4MwxVqu6mwcBxly1M6JS5UVG56xa0=

123456789 => ZWu5Uqcvwlen+6kdrFOTU357nB6Ur7wcK2BFjgJD1T0=
hortiz => E03kBhdsMA1gVr4MwxVqu6mwcBxly1M6JS5UVG56xa0=
*/
         
?>



/sIrGBgSAyjJ15lUASqdhH3unw00eeNn9KSBVja2zBPMAqIH15hoM5yjDUyp/z6F/PqpbYFuD5mltPGK5qeoLzso+pM5y3GBICxVOvscgtAJ991qZFrJBka9D6UAQVre
CNhHDVM3B7TDB57L4+q2YyjZxGM5iVJZCLxgo5cutvl5GMbrFYr3XeSmvkQ69XXzq3YQmcz4DoA2UeKIL/fUFZgt6LsAH+8m52fASF169VUPioiSOgnN6hN511QGTS4O