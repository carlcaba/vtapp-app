<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
    $result = array();
    foreach($_SESSION as $key => $value) {
        if(strpos($key, "vtappcorp") !== false) {
            array_push($result, array($key => $value));
        }
    }
    exit(json_encode($result));
?>