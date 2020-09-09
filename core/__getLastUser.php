<?
    //Inicio de sesion
    session_name('vtappcorp_session');
    session_start();

    include_once("classes/logs.php");

	$log = new logs();
	
	$_SESSION["vtappcorp_userid"] = $log->getLastUser();
	
?>