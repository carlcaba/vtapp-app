<?
	//Web service que genera el login del usuario
	//LOGICA ESTUDIO 2019
	
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Access-Control-Allow-Methods: GET, POST, PUT');	
	header('Content-Type: application/json');	
	
	//Incluye las clases necesarias
	require_once("../core/classes/resources.php");
	require_once("../core/classes/users.php");
	require_once("../core/classes/interfaces.php");
	require_once("../core/classes/external_session.php");
	require_once("../core/classes/configuration.php");
	require_once("../core/classes/vehicle.php");
	
	//Carga los recursos
    include("../core/__load-resources.php");
	
    //Variable del codigo
    $result = array('success' => false,
					'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					'changepassword' => false,
					"description" => "");
					
	$reso = new resources(basename(__FILE__));
	$result["description"] = $reso->getResourceByName(explode(".",basename(__FILE__))[0],2);
						
	$user = "";
	$pass = "";
	
	$config = new configuration("DEBUGGING");
	$debug = $config->verifyValue();
	
	//Captura las variables
	if($_SERVER['REQUEST_METHOD'] != 'PUT') {
		if(!isset($_POST['user'])) {
			if(!isset($_GET['user'])) {
				header("HTTP/1.1 400 Bad Request " . $result["message"]);
				exit;		
				/*
				//Termina
				exit(json_encode($result));
				*/
			}
			else {
				$user = $_GET['user'];
				$pass = $_GET['pass'];
			}
		}
		else {
			$user = $_POST['user'];
			$pass = $_POST['pass'];
		}
	} 
	else {
		//Captura las variables
		parse_str(file_get_contents("php://input"),$vars);
		$user = $vars['user'];
		$pass = $vars['pass'];
	}
	
	//Verifica la informacion
	if(empty($user)) {
		header("HTTP/1.1 400 Bad Request " . $_SESSION["USERNAME_EMPTY"]);
		exit;		
		/*
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["USERNAME_EMPTY"];
		//Termina
		exit(json_encode($result));
		*/
	}
	if(empty($pass)) {
		header("HTTP/1.1 400 Bad Request " . $_SESSION["PASSWORD_EMPTY"]);
		exit;		
		/*
		//Confirma mensaje al usuario
		$result['message'] = $_SESSION["PASSWORD_EMPTY"];
		//Termina
		exit(json_encode($result));
		*/
	}

	//Instancia la clase usuario
	$usua = new users($user);
	$conf = new configuration("WEB_SITE");
	$website = $conf->verifyValue();
	$siteroot = $conf->verifyValue("SITE_ROOT");

	//Asigna los valores
	$usua->THE_PASSWORD = $pass;
		
	//Valida la contrase�a
	$usua->check(true);
	
	//Si hay error
	if($usua->nerror > 0) {
		header("HTTP/1.1 401 Unauthorized " . $usua->error);
		exit;		
		/*
		//Confirma mensaje al usuario
		$result['message'] = $usua->error;
		//Termina
		exit(json_encode($result));
		*/
	}
	
	//Crea el nuevo LOG
	$log = new logs("LOGIN");
	//Adiciona la transaccion
	$log->Login();
	//Si hubo error
	if($log->nerror > 0)
		//Confirma al usuario
		$result['message'] = $log->error;
	
	//Cambia el resultado
	$result['success'] = true;
	$result['message'] = $_SESSION["USER_PASSWORD_OK"];
	
	//Si el usuario debe cambiar la contrase�a
	if($usua->THE_PASSWORD == $conf->verifyValue("INIT_PASSWORD") || $usua->THE_PASSWORD == $usua->ID || $usua->CHANGE_PASSWORD == 1) {
		//Confirma al usuario
		$result['changepassword'] = true;
		$result['message'] = $_SESSION["CHANGE_PASSWORD_REQUIRED"];
	}
	
	$exts = new external_session();
	
	//Actualiza la informacion
	$exts->USER_ID = $usua->ID;
	$exts->setAccess($usua->ACCESS_ID);
	$exts->REQUESTED_BY = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
	$exts->REGISTERED_BY = "Vtapp.WS";

	//Verifica si esta registrado como empleado
	require_once("../core/classes/employee.php");
	$employee = new employee();
	$employee->USER_ID = $usua->ID;
	$employee->getInformationByOtherInfo("USER_ID");
	if($employee->nerror == 0) {
		$exts->PARTNER_ID = $employee->PARTNER_ID;
	}
	
	$exts->_add();
	
	$vehi = new vehicle();
	$dataV = $vehi->showJSONListByUser($usua->IDENTIFICATION);
		
	$result["token"] = $exts->ID;
	$result["user_data"] = array("user_id" => $usua->ID,
								 "full_name" => $usua->getFullName(),
								 "personal_id" => $usua->IDENTIFICATION,
								 "email" => $usua->EMAIL,
								 "address" => $usua->ADDRESS,
								 "phone" => $usua->PHONE,
								 "cellphone" => $usua->CELLPHONE,
								 "image" => $website . $siteroot . $usua->getUserPicture(true),
								 "vehicles" => $dataV);
	
	//Termina
	exit(json_encode($result));
?>