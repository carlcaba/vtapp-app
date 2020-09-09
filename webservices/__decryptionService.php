<?
	//Web service que facilita la desencripcion de datos
	//LOGICA ESTUDIO 2019
	
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Incluye las clases necesarias
	require_once("../core/classes/resources.php");

	//Carga los recursos
    include("../core/__load-resources.php");
	
    //Variable del codigo
    $result = array('success' => false,
        'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
		"description" => "");
					
	$reso = new resources(basename(__FILE__));
	$result["description"] = $reso->getResourceByName(explode(".",basename(__FILE__))[0],2);
	
	$data = "";

	//Captura las variables
	if($_SERVER['REQUEST_METHOD'] != 'PUT') {
		if(!isset($_POST['data'])) {
			if(!isset($_GET['data'])) {
				//Termina
				exit(json_encode($result));
			}
			else {
				$data = $_GET['data'];
			}
		}
		else {
			$data = $_POST['data'];
		}
	} 
	else {
		//Captura las variables
		parse_str(file_get_contents("php://input"),$vars);
		$data = $vars['data'];
	}
	
	//Si no hay datos para validar
	if($data == "") {
		exit(json_encode($result));
	}

	$data = str_replace(" ","+",$data);

	$result["message"] = Desencriptar($data);
	$result["success"] = true;

	exit(json_encode($result));
	
?>