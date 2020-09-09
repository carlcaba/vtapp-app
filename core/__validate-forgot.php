<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	//incluye las clases necesarias
	require_once("classes/resources.php");
	require_once("classes/users.php");
	require_once("classes/interfaces.php");
	require_once("classes/configuration.php");
	//Instancia las clases necesarias
	$inter = new interfaces();
	$conf = new configuration("SITE_ROOT");

	//Carga los valores de la configuraci�n
	$site_root = $conf->verifyValue();
	
	$conf = new configuration("INIT_PASSWORD");
	$initpass = $conf->verifyValue();

	//Variable del codigo
	$link = "";
	$datas = "";
	$result = array('success' => false,
					'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					'link' => $link);
	
	//Captura las variables
	if(!isset($_POST['txtUser'])) {
		if(!isset($_GET['txtUser'])) {
			//Termina
			exit(json_encode($result));
		}
		else {
			$user = $_GET['txtUser'];
			$link = $_GET['hfLink'];
		}
	}
	else {
		$user = $_POST['txtUser'];
		$link = $_POST['hfLink'];
	}
	
	//Verifica la informacion
	if(empty($user)) {
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["EMAIL_EMPTY"];
		//Termina
		exit(json_encode($result));
	}
	
	//Instancia la clase usuario
	$usua = new users();
	
	//Busca la informacion del usuario
	$usua->EMAIL = $user;
	$usua->getInfoByMail();
	
	//Si hay error
	if($usua->nerror > 0) {
		//Confirma mensaje al usuario
		$result['message'] = $usua->error;
		//Termina
		exit(json_encode($result));
	}
	
	//Restaurar la contraseña
	$usua->changePassword($initpass);

	//Si hay error
	if($usua->nerror > 0) {
		//Confirma mensaje al usuario
		$result['message'] = $usua->error . "<br />";
		//Veriica el error
		if($usua->nerror != 18)
			//Termina
			exit(json_encode($result));
	}

	//Crea el nuevo LOG
	$log = new logs("REMEMBER PASSWORD");
	//Si hubo error
	if($log->nerror > 0)
		//Confirma al usuario
		$result['message'] = $log->error;
	
	//Cambia el resultado
	$result['success'] = true;
	$result['message'] .= $_SESSION["REMEMBER_PASSWORD"];
	
	$_SESSION["vtappcorp_user_message"] = $_SESSION["REMEMBER_PASSWORD"];
	
	//Si hay algun link	
	$result['link'] = $site_root . $link;
	
	//Termina
	exit(json_encode($result));

?>