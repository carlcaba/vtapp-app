<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

    include("core/__load-resources.php");

	require_once("core/classes/interfaces.php");
	
	$inter = new interfaces();

	$rep = "report1";
	$link = "reports.php";
	//Captura las variables
    if(empty($_POST['strModel'])) {
        //Verifica el GET
        if(!empty($_GET['strModel'])) {
            $strmodel = $_GET['strModel'];
            $rep = $_GET['rep'];
			$link .= "?rep=$rep"; 
        }
    }
    else {
        $strmodel = $_POST['strModel'];
        $rep = $_POST['rep'];
		$link .= "?rep=$rep"; 
    }
	
	//Define el menu
	$_SESSION["menu_id"] = $inter->getMenuId($link);
	
	require_once("core/__check-session.php");
	
	$result = checkSession($link,true);

	if($result["success"] !== true) 
		$inter->redirect($result["link"]);

	require_once("core/classes/$rep.php");
	require_once("core/classes/configuration.php");
	
	$report = new $rep();
	//Asigna la informacion
	$datas = json_decode($strmodel);
	
	//Asigna la informacion
	foreach($datas as $clave => $valor) {
		if(strpos($clave,"hf") === false && strpos($clave,"chk") === false)
			$report->$clave = $valor;
	}	
	
	$conf = new configuration("ORDER_PREFIX");
	$prefix =  $conf->verifyValue();	

	$conf = new configuration("COMPANY_NAME");
	$company =  $conf->verifyValue();	

	$conf = new configuration("CURRENCY");
	$currency =  $conf->verifyValue();	

	$leng = "en";
	if($_SESSION["LANGUAGE"] != 1)
		$leng = $_SESSION["LANGUAGE"] == 2 ? "es-es" : "de-de";
	
?>
<!DOCTYPE html>
<html>
<head>
<?
	include("core/templates/__header.tpl");
?>
	<!-- DataTables -->
	<link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap4.css">
	<link rel="stylesheet" href="plugins/datatables/extensions/Responsive/css/responsive.bootstrap4.min.css">
    <!-- datapicker CSS -->
    <link rel="stylesheet" href="plugins/datapicker/datepicker3.css">
	<!-- Select2 -->
	<link rel="stylesheet" href="plugins/select2/select2.css">
</head>
<body class="hold-transition sidebar-mini" onload="window.print();">
	<div class="wrapper">
		<!-- Main content -->
		<section class="invoice">
<?= $report->showPrint($company) ?>		
		</section>
		<!-- /.content -->
	</div>
	<!-- ./wrapper -->
</body>
</html>