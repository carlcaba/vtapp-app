<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

    //Variable del codigo
    $result = array('success' => false,
        'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);
	
	//Captura las variables
    if(empty($_POST['strModel'])) {
        //Verifica el GET
        if(empty($_GET['strModel'])) {
            $result = utf8_converter($result);
            exit(json_encode($result));
        }
        else {
            $strmodel = $_GET['strModel'];
			$class = $_GET['txtClass'];
        }
    }
    else {
        $strmodel = $_POST['strModel'];
		$class = $_POST['txtClass'];
    }

    //Si es un acceso autorizado
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        //Asigna la informacion
        $datas = json_decode($strmodel);
		
		require_once("../../classes/" . $class . ".php");

		//Asigna la informacion
		$sxemp = new $class();

		//Asigna la informacion
		foreach($datas as $clave => $valor) {
			if(strpos($clave,"hf") === false && strpos($clave,"chk") === false)
				$sxemp->$clave = $valor;
		}
		
		$filter = $sxemp->filterData();
		
        //Cambia el resultado
        $result['success'] = count($filter) > 0;
        $result['message'] = count($filter) > 0 ? "" : $_SESSION["NO_DATA"];
		$result["sql"] = $sxemp->sql;
		$result["columns"] = count($sxemp->arrColDatas);
        $result['datas'] = $filter;
    }
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));
?>