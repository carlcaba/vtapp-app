<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/zone.php");
	
    //Captura las variables
    if(empty($_POST['q'])) {
        //Verifica el GET
        if(empty($_GET['q'])) {
            exit();
		}
		else {
            $term = $_GET['q'];
		}
    }
    else {
		$term = $_POST['q'];
    }	

	$term = $term == "all" ? "" : $term;
	
	$zone = new zone();
	
	exit(json_encode($zone->showAutocompleteOptionList($term)));
	
?>