<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');		
				
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		require_once("../../classes/users.php");
		$contact = new users();
		$result = $contact->showContactPanel();
	}
	else {
		$result = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	
	//Termina
	exit($result);

?>