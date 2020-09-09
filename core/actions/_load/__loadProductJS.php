<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/product.php");
	
	$product = new product();
	
	exit(json_encode($product->showOptionJSON()));
	
?>