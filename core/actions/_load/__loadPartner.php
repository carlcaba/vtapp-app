<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

	$result = array("success" => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);

	$ref = "";
	
    //Captura las variables
    if(empty($_POST['ref'])) {
        //Verifica el GET
        if(!empty($_GET['ref'])) 
            $ref = $_GET['ref'];
    }
    else
		$ref = $_POST['ref'];
	
	require_once("../../classes/partner.php");
	

	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Instancia la clase
		$part = new partner();
		$result["message"] = "";
		$result["data"] = $part->showListJSON($ref);
		$result["success"] = $part->nerror == 0;
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	//Termina
	exit(json_encode($result));

	
?>
