<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');

	require_once("../../classes/city.php");
	
    //Captura las variables
    if(empty($_POST['idCountry'])) {
        //Verifica el GET
        if(empty($_GET['idCountry'])) {
            exit();
		}
		else {
            $id = $_GET['idCountry'];
		}
    }
    else {
		$id = $_POST['idCountry'];
    }	
	
	$city = new city();
	$city->setCountry($id);
	
	exit(json_encode($city->showListJSON()));
	
?>