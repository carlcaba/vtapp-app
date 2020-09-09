<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	$id = "";
	
	//Verifica los datos
	if(!isset($_POST['txtId'])) {
		//Verifica el GET
		if(isset($_GET['txtId'])) {
			$id = $_GET['txtId'];
			$code = $_GET['type'];
		}
	}
	else {
		$id = $_POST['txtId'];
		$code = $_POST['type'];
	}

	require_once("../../classes/product.php");
	
	//Asigna la informacion
	$product = new product();
	
	if($id != "") {
		$product->ID = $id;
		$product->__getInformation();
	}
	
	if($code == "QR")
		exit($product->generateQRCode());
	else if($code == "BAR")
		exit($product->generateBarCode());
	
?>