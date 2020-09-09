<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/zone.php");
	
    //Captura las variables
    if(empty($_POST['term'])) {
        //Verifica el GET
        if(empty($_GET['term'])) {
            exit();
		}
		else {
            $term = $_GET['term'];
		}
    }
    else {
		$term = $_POST['idCountry'];
    }	
	
	$zone = new zone();
	
	exit(json_encode($zone->showAutocompleteOptionList($term)));
	
?>