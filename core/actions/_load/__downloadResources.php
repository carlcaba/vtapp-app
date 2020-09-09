<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Verifica los datos
	if(!isset($_POST['txtId'])) {
		//Verifica el GET
		if(isset($_GET['txtId'])) {
			$id = $_GET['txtId'];
		}
	}
	else {
		$id = $_POST['txtId'];
	}

	require_once("../../classes/resources.php");
	require_once("../../classes/PHPExcel.php");
	
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();

	// Set document properties
	$objPHPExcel->getProperties()->setCreator("VTAPP")
								 ->setLastModifiedBy($_SESSION["vtappcorp_userid"])
								 ->setTitle("Office 2007 XLSX Resources")
								 ->setSubject("Office 2007 XLSX Resources")
								 ->setDescription("Resources for Office 2007 XLSX")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Resources file");

	$reso = new resources();
	$headers = explode(",",$reso->getResourceByName("RESOURCES_HEADER"));
	$columns = range('A','Z');

	// Add some data
	$objPHPExcel->setActiveSheetIndex(0);
	for($i=0;$i<count($headers);$i++)
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue($columns[$i] . '1', $headers[$i]);

	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle('RESOURCES');
	
	$count = 2;
	foreach($reso->getAllResources($id) as $row) {
		//$row = utf8_converter($row);
		for($i=0;$i<count($row);$i++) {
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue($columns[$i] . $count, $row[$i]);
		}
		$count++;
	}

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Excel2007)
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="resources_' . date("Ymd") . "_" . $id . '.xlsx"');
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