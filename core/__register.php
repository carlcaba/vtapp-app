<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');		
	
	//incluye las clases necesarias
	require_once("classes/usuario.php");
	require_once("classes/interfaz.php");
	require_once("classes/config.php");
	
	//Instancia las clases necesarias
	$inter = new interfaz();
	$conf = new configuracion("SITE_ROOT");
	//Carga los valores de la configuración
	$site_root = $conf->verifyValue();
	
	//Variable del codigo
	$result = array('success' => false,
					'message' => 'No se ha ingresado datos para validar',
					'link' => "index.php");
	
	//Captura las variables
	if(!isset($_POST['txtUser'])) {
		if(!isset($_GET['txtUser'])) {
			//Termina
			exit(json_encode($result));
		}
		else {
			$user = $_GET['txtUser'];
			$pass = $_GET['txtPassword'];
			$email = $_GET['txtEmail'];
		}
	}
	else {
		$user = $_POST['txtUser'];
		$pass = $_POST['txtPassword'];
		$email = $_POST['txtEmail'];
	}
	
	//Verifica la informacion
	if(empty($user)) {
		//Confirma mensaje al usuario
		$result['message'] = "El nombre de usuario no puede estar vacío, intente de nuevo";
		//Termina
		exit(json_encode($result));
	}
	if(empty($pass)) {
		//Confirma mensaje al usuario
		$result['message'] = "La contraseña no puede estar vacía, intente de nuevo";
		//Termina
		exit(json_encode($result));
	}
	if(empty($email)) {
		//Confirma mensaje al usuario
		$result['message'] = "El correo electrónico no puede estar vacío, intente de nuevo";
		//Termina
		exit(json_encode($result));
	}
	
	//Instancia la clase usuario
	$usua = new usuario($user);
	//Asigna los valores
	$usua->EMAIL = $email;
	
	//Verifica la duplicidad
	$usua->__getInformation();
	//Si hay error
	if($usua->nerror == 0) {
		//Confirma mensaje al usuario
		$result['message'] = "El nombre de usuario ingresado ya está registrado, intente de nuevo";
		//Termina
		exit(json_encode($result));
	}
	
	//Verifica la duplicidad
	$usua->getInfoByMail();
	//Si hay error
	if($usua->nerror == 0) {
		//Confirma mensaje al usuario
		$result['message'] = "El correo electrónico ingresado ya está registrado, intente de nuevo";
		//Termina
		exit(json_encode($result));
	}
	
	//Asigna el acceso
	$usua->setAcceso(10);
	//Asigna los valores
	$usua->THE_PASSWORD = $pass;
	$usua->BLOCKED = "TRUE";

	//Intenta agregarlo
	$usua->__add();
	
	//Si hay error
	if($usua->nerror > 0) {
		//Si es error de correo
		if($usua->nerror != 18) {
			//Confirma mensaje al usuario
			$result['message'] = $usua->error;
			//Termina
			exit(json_encode($result));
		}
	}

	//Cambia el resultado
	$result['success'] = true;
	$result['message'] = "Usuario creado correctamente, espere una próxima comunicación a su correo electrónico con instrucciones!";
	
	//Termina
	exit(json_encode($result));
?>