<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/zone.php");
	
    //Captura las variables
    if(empty($_POST['id'])) {
        //Verifica el GET
        if(empty($_GET['id'])) {
            exit();
		}
		else {
            $term = $_GET['id'];
		}
    }
    else {
		$term = $_POST['id'];
    }	

	$zone = new zone();
	
	exit(json_encode($zone->getZonesForCity($term)));
	
?>