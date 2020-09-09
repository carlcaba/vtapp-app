<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Incluye las clases requeridas
	require_once("classes/resources.php");
	require_once("classes/logs.php");
	require_once("classes/interfaces.php");
	require_once("classes/configuration.php");
	require_once("classes/users.php");
	
	$lock = false;
	$link = "";
	$username = "";
	
	//Captura las variables
	if(!isset($_POST['lockscreen'])) {
		if(isset($_GET['lockscreen'])) {
			$lock = $_GET['lockscreen'];
			$link = $_GET['ref'];
			$username = $_SESSION['vtappcorp_userid'];
		}
	}
	else {
		$lock = $_POST['lockscreen'];
		$link = $_POST['ref'];
		$username = $_SESSION['vtappcorp_userid'];
	}
	
	//Inicializa la clase
	$inter = new interfaces();
	$resources = new resources();
	$conf = new configuration("RETURN_HOME");

	//Obtiene el site_root
	$site_root = $conf->verifyValue();

	//Verifica si el usuario esta loggeado	
	if(isset($_SESSION['vtappcorp_userid'])) {
		//Completa el LOG
		$log = new logs("SESSION END");
		$log->_add();
	}
	
	//Instancia la clase usuario
	$usua = new users($username);	
	
	$usua->setOnline(false);
	
	//Elimina las variables de sesion
	unset($_SESSION['vtappcorp_userid']);
	unset($_SESSION["vtappcorp_user_message"]);
	unset($_SESSION["vtappcorp_user_alert"]);
	unset($_SESSION['vtappcorp_username']);
	unset($_SESSION['vtappcorp_lastAccess']);
    unset($_SESSION['vtappcorp_useraccessid']);
	unset($_SESSION["vtappcorp_facebook_user"]);
	unset($_SESSION["vtappcorp_appname"]);
    unset($_SESSION['vtappcorp_useraccess']);
    unset($_SESSION['vtappcorp_referenceid']);
    unset($_SESSION['vtappcorp_location']);
	unset($_SESSION['vtappcorp_skin']);

	$url = $site_root;

	//Elimina las variables cargadas al iniciar
	$conf->unloadValues();
	
	//Destruye la sesion e inicia una nueva
	session_destroy();
	session_unset();
	//Regenera el numero
	session_regenerate_id();	
	//Inicia la sesion
	session_name("vtappcorp_session");
	session_start();
	
	//Recarga los recursos
	$resources->loadResources();

	//Confirma al usuario
	$_SESSION["vtappcorp_user_message"] = $lock ? $_SESSION["SESSION_ENDED_LOCK"] : $_SESSION["SESSION_ENDED"];	

	//Verifica si hubo algun error
	if($log->nerror > 0)
		$_SESSION["vtappcorp_user_alert"] = $log->error;

	if($lock)
		$url = $site_root . "lockscreen.php?ref=" . $link . "&usr=" . $username;
	
	//Lo redirecciona
	$inter->redirect($url);
?>