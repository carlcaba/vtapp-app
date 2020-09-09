<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	$value = 0;
	$ref = "";
	
    //Captura las variables
    if(empty($_POST['value'])) {
        //Verifica el GET
        if(empty($_GET['value'])) {
            exit();
		}
		else {
            $value = intval($_GET['value']);
            $ref = $_GET['ref'];
		}
    }
    else {
		$value = intval($_POST['value']);
		$ref = $_POST['ref'];
	}
	
	//Verifica la referencia
	if($ref == "''")
		$ref = "";
	
	if($value != 0) {
		if ($value >= 20 && $value < 50) {
			require_once("../../classes/client.php");
			$class = new client();
		}
		elseif ($value >= 50 && $value < 90) {
			require_once("../../classes/partner.php");
			$class = new partner();
		}
		$result = $class->showListJSON($ref);
	}
	
	exit(json_encode($result));
	
?>