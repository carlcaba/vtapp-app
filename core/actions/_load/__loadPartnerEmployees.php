<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

	//Id de empleado
	$empId = "";
	
    //Captura las variables
    if(empty($_POST['id'])) {
        //Verifica el GET
        if(empty($_GET['id'])) {
            exit();
		}
		else {
            $id = $_GET['id'];
			$empId = $_GET['id_emp'];
		}
    }
    else {
		$id = $_POST['id'];
		$empId = $_POST['id_emp'];
    }
	
	require_once("../../classes/employee.php");
	
	$result = array("success" => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);

	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Instancia la clase
		$empl = new employee();
		
		//Verifica si el empleado esta definido
		if($empId != "") {
			$empl->ID = $empId;
			$empl->__getInformation();
			if($empl->nerror > 0) {
				$result["message"] = $empl->error;
				//Asigna el aliado
				$empl->setPartner($id);
			}
		}
		else {
			//Asigna el aliado
			$empl->setPartner($id);
		}
		
		if($empl->nerror == 0) {
			$result["success"] = true;
			$result["message"] = "";
			$result["data"] = $empl->showEmployees();
		}
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	//Termina
	exit(json_encode($result));

	
?>
