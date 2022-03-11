<?
    //Inicio de sesion
	if (session_status() === PHP_SESSION_NONE) {
		session_name('vtappcorp_session');
		session_start();
	}	

	//Incluye las clases requeridas
	include_once("classes/configuration.php");
	include_once("classes/interfaces.php");
	include_once("classes/users.php");

	//Verifica si esta habilitado el debug
	if(!defined("DEBUG")) {
		$conf = new configuration("DEBUGGING");
		$debug = $conf->verifyValue();
		if($debug === 0)
			$debug = false;
		define("DEBUG", $debug); 
	}
	
	function checkSession($caller,$check = false) {
		$link = $caller;
			
		$result = array('success' => true, 
						'message' => $_SESSION["NO_INFORMATION"],
						'link' => '');
			
		//Instancia las clases requeridas
		$inter = new interfaces();
		$conf = new configuration("SITE_ROOT");
		//Carga los valores de la configuración
		$site_root = $conf->verifyValue();
		
		$conf = new configuration("SESSION_EXPIRATION");
		$sesexp = $conf->verifyValue();

		if (!defined('APP_NAME')) {
			$conf = new configuration("APP_NAME");
			define("APP_NAME", $conf->verifyValue());
		}	

		if(!$check)
			$inter->updateLastAccess();
		
		if($inter->verifySession($sesexp)) {
			if(!isset($_SESSION)) {
				include("__getLastUser.php");
				include("__load-resources.php");
			}
			$result["success"] = false;
			$result["message"] = $_SESSION["SESSION_EXPIRED"];
			$_SESSION["vtappcorp_user_alert"] = $_SESSION["SESSION_EXPIRED"];
			//envia al usuario a la pag. de autenticacion
			$result["link"] = $site_root . "index.php?ref=" . $link;
			$result["adds"] = "Error en verifySession";
			$result["last_access"] = $_SESSION['vtappcorp_lastAccess'];
			$result["max_time"] = $sesexp;
			$result["error"] = $inter->error;
			$result["user"] = $_SESSION['vtappcorp_userid'];
			//Termina
			return $result;
		}

		$user = new users($_SESSION['vtappcorp_userid']);
		
		//Verifica el usuario
		$user->__getInformation();
		
		//Verifica si debe revisar el acceso al menu
		if($check && intval($_SESSION['vtappcorp_useraccessid']) < 100) {
			//Verifica el acceso del usuario
			if(!$user->checkAccess($_SESSION["menu_id"])) {
				//Si el menu no está registrado
				if($user->nerror != 50) {
					$result["success"] = false;
					$result["message"] = $user->error;
					$_SESSION["vtappcorp_user_alert"] = $user->error . " - $caller" ;
					//envia al usuario a la pag. de no autorizado
					$result["link"] = "unauthorized.php";	
				}
				else {
					$_SESSION["vtappcorp_user_message"] = $user->error . " - $caller";
				}
			} 
		}
		
		return $result;
	}
?>