<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	require_once("../../classes/configuration.php");
	require_once("../../classes/kushki/autoload.php");
	
	$conf = new configuration("PAYMENT_MERCHANT_ID");
	$merchId = $conf->verifyValue();
	
	
?>