<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    //Captura las variables
    if(empty($_POST['id'])) {
        //Verifica el GET
        if(empty($_GET['id'])) {
            exit();
		}
		else {
            $id = $_GET['id'];
		}
    }
    else {
		$id = $_POST['id'];
    }
	
	require_once("../../classes/partner_client.php");
	
	$result = array("success" => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);

	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Instancia la clase
		$party = new partner_client();
		
		//Asigna el cliente
		$party->setClient($id);
		
		$result["success"] = true;
		$result["message"] = "";
		$result["data"] = $party->showAssignedEmployees();
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	//Termina
	exit(json_encode($result));

	
?>
