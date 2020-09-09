<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/directchat.php");
	
	$id = "";
	//Verifica los datos
	if(!isset($_POST['idUserDestiny'])) {
		//Verifica el GET
		if(isset($_GET['idUserDestiny'])) {
			$id = $_GET['idUserDestiny'];
		}
	}
	else {
		$id = $_POST['idUserDestiny'];
	}	
	
	//Asigna la informacion
	$chat = new directchat($id);
	
	exit($chat->showLastChats());
	
?>