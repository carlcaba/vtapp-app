<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	//Verifica los datos
	if(!isset($_POST['idUser'])) {
		//Verifica el GET
		if(isset($_GET['idUser'])) {
			$id = $_GET['idUser'];
		}
	}
	else {
		$id = $_POST['idUser'];
	}

	require_once("../../classes/directchat.php");
	
	//Asigna la informacion
	$chat = new directchat();
	
	exit($chat->showForm($id));
	
?>