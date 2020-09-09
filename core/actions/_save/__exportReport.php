<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();

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
	
	require_once("../../classes/configuration.php");
	require_once("../../classes/interfaces.php");
	require_once("../../classes/$rep.php");
	require_once("../../classes/PHPExcel.php");
	
	$inter = new interfaces();
	$report = new $rep();

	//Asigna la informacion
	$datas = json_decode($strmodel);
	
	//Asigna la informacion
	foreach($datas as $clave => $valor) {
		if(strpos($clave,"hf") === false && strpos($clave,"chk") === false)
			$report->$clave = $valor;
	}	
	
	$conf = new configuration("COMPANY_NAME");
	$company =  $conf->verifyValue();	
	$filename = "../../../templates/$rep.xlsx";
	
	// Create new PHPExcel object
	$objPHPExcel =  new PHPExcel();
	$objPHPExcel = PHPExcel_IOFactory::load($filename);
	
	//Generate the report
	$report->exportToExcel($objPHPExcel, $filename, $company);

	// Set document properties
	$objPHPExcel->getProperties()->setCreator("VTAPPCORP")
								 ->setLastModifiedBy($_SESSION["vtappcorp_userid"])
								 ->setTitle("Office 2007 XLSX $rep")
								 ->setSubject("Office 2007 XLSX $rep")
								 ->setDescription("$rep for Office 2007 XLSX")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Report file");
								 
	

	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="' . $rep . '_' . date("YmdHis") . '_.xlsx"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save('php://output');
	
	exit();
	
?>