<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	$id = "";
	$action = "new";
	$class = "";
	$class2 = "";
	$encr = false;
	
	//Verifica los datos
	if(!isset($_POST['txtId'])) {
		//Verifica el GET
		if(isset($_GET['txtId'])) {
			$id = $_GET['txtId'];
			$action = $_GET['txtAction'];
            $class = $_GET['txtClass'];
			$encr = $_GET['encrypted'];
			$form = $_GET['txtForm'];
		}
	}
	else {
		$id = $_POST['txtId'];
		$action = $_POST['txtAction'];
		$class = $_POST['txtClass'];
		$encr = $_POST['encrypted'];
		$form = $_POST['txtForm'];
	}

	if(empty($class)) {
		exit();
	}
	
	require_once("../../classes/" . $class . ".php");
	
	//Asigna la informacion
	$sxemp = new $class();
	
	if($id != "") {
		if($encr) 
			$id = Desencriptar($id);
		$sxemp->ID = $id;
		$sxemp->__getInformation();
	}
	else if($id > 0) {
		$sxemp->ID = $id;
		$sxemp->__getInformation();
	}
	
	if($form == "")
		exit($sxemp->showForm($action));
	else
		exit($sxemp->showUserForm($action));
	
?>