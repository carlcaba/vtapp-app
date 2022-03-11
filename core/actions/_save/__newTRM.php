<?
    //Inicio de sesion
    session_name('vtappcorp_session');
    session_start();

    //Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');

    //Variable del codigo
    $result = array('success' => false,
        'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);

	//Realiza la operacion
	require_once("../../classes/money-bill-1_converter.php");

	//Captura las variables
    if(empty($_POST['strModel'])) {
        //Verifica el GET
        if(empty($_GET['strModel'])) {
            $result = utf8_converter($result);
            exit(json_encode($result));
        }
        else {
            $strmodel = $_GET['strModel'];
        }
    }
    else {
        $strmodel = $_POST['strModel'];
    }

    //Si es un acceso autorizado
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        //Asigna la informacion
        $datas = json_decode($strmodel);

		$money-bill-1 = new money-bill-1_converter();
		//Agrega la conversion
		$money-bill-1->money-bill-1_FROM = $datas->txtmoney-bill-1_FROM;
		$money-bill-1->money-bill-1_TO = $datas->txtmoney-bill-1_TO;
		$money-bill-1->VALUE_TO = $datas->txtVALUE_TO;
		$money-bill-1->DATERATE = $datas->txtDATERATE;
		$money-bill-1->BLOCKED = $datas->cbBlocked;

		$money-bill-1->_add();

		//Si se genera error
		if($money-bill-1->nerror > 0) {
			$result["message"] = $_SESSION["ERROR"] . " " . $_SESSION["TRM"] . ": " . $money-bill-1->error . " -> " . $money-bill-1->sql; 
			//Termina
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
        //Cambia el resultado
        $result['success'] = true;
        $result['message'] = str_replace("%d", "", $_SESSION["SAVED"]);
		$result["link"] = "trm.php";
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>