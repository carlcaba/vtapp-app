<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');		

	$max = 6;

    //Captura las variables
    if(empty($_POST['id'])) {
        //Verifica el GET
        if(!empty($_GET['max'])) {
            $max = $_GET['max'];
		}
    }
    else {
		$max = $_POST['max'];
    }	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"]);	

	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		require_once("../../classes/service.php");
		$service = new service();

		$end = strtotime(date("Y-m-d"));
		$start = $month = strtotime("-" . ($max-1) . " month", $end);
		
		$labels = array();
		
		$step1 = array("label" => $_SESSION["SERVICE_STATE_GRAPH_1"],
						"backgroundColor" => 'rgba(60,141,188,0.9)',
						"borderColor" => 'rgba(60,141,188,0.8)',
						"pointRadius" => false,
						"pointColor" => '#3b8bba',
						"pointStrokeColor" => 'rgba(60,141,188,1)',
						"pointHighlightFill" => '#fff',
						"pointHighlightStroke" => 'rgba(60,141,188,1)',
						"data" => array());
		$step2 = array("label" => $_SESSION["SERVICE_STATE_GRAPH_2"],
						"backgroundColor" => 'rgba(210, 214, 222, 1)',
						"borderColor" => 'rgba(210, 214, 222, 1)',
						"pointRadius" => false,
						"pointColor" => '#c1c7d1',
						"pointStrokeColor" => 'rgba(210, 214, 222, 1)',
						"pointHighlightFill" => '#fff',
						"pointHighlightStroke" => 'rgba(210, 214, 222, 1)',
						"data" => array());
		$step3 = array("label" => $_SESSION["SERVICE_STATE_GRAPH_3"],
						"backgroundColor" => 'rgba(234, 213, 20, 1)',
						"borderColor" => 'rgba(234, 213, 20, 1)',
						"pointRadius" => false,
						"pointColor" => '#959AA1',
						"pointStrokeColor" => 'rgba(234, 213, 20, 1)',
						"pointHighlightFill" => '#fff',
						"pointHighlightStroke" => 'rgba(234, 213, 20, 1)',
						"data" => array());
		while($month <= $end) {
			array_push($labels,date('F/Y', $month));
			$datas = $service->DashboardGraphData(intval(date("m",$month)),intval(date("Y",$month)));
			array_push($step1["data"],intval($datas["process"]));
			array_push($step2["data"],intval($datas["on_road"]));
			array_push($step3["data"],intval($datas["finish"]));
			$month = strtotime("+1 month", $month);
		}
		$objData = array("labels" => $labels,
						"datasets" => array());
		array_push($objData["datasets"],$step1);
		array_push($objData["datasets"],$step2);
		array_push($objData["datasets"],$step3);
		$result["success"] = true;
		$result["data"] = $objData;
		$result["message"] = "ok";
	}
	else {
		$result = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	//Termina
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>