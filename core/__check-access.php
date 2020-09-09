<?
	
	//Inicio de sesion
	session_name('paramed_session');
	session_start();
		
	//Incluye las clases requeridas
	require_once("classes/config.php");
	require_once("classes/interfaz.php");
	require_once("classes/usuario.php");
	
	//Instancia las clases requeridas
	$inter = new interfaz();
	$conf = new configuracion("SITE_ROOT");
	$site_root = $conf->verifyValue();
	//Instancia el usuario
	$usua = new usuario($_SESSION['paramed_userid']);
	$usua->__getInformation();
	
	//Obtiene la URL
	$url = $_SERVER['REQUEST_URI'];
	//Obtiene el acceso
	$urlArr = explode("/",$url);
	//Obtiene el acceso
	$acceso = intval($urlArr[2]);
	
	//Verifica si ya inicio sesion
	if($usua->ID_ACCESO < $acceso) {
		//Configura el mensaje
		$_SESSION['paramed_user_alert'] = "No tiene acceso para este sitio!";
		//envia al usuario a la pag. de autenticacion
		$inter->redirect($site_root . $usua->ID_ACCESO . "/");				
	}
	
?>