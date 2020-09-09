<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'areas.php');

	require_once("../../classes/area.php");

	//Captura las variables
	if(!isset($_POST['strModel'])) {
		//Verifica el GET
		if(!isset($_GET['strModel'])) {
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		else {
			$strmodel = $_GET['strModel'];
		}
	}
	else {
		$strmodel = $_POST['strModel'];
	}
	
	//Si es un acceso autorizado
	if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Asigna la informacion
		$datas = json_decode($strmodel);

		require_once("../../classes/configuration.php");
		require_once("../../classes/users.php");
		require_once("../../classes/PHPExcel/IOFactory.php");
		require_once("../../classes/resources.php");
		$reso = new resources();
		$headers = explode(",",$reso->getResourceByName("AREAS_HEADER"));
		
		$area = new area();
		$usua = new users($_SESSION["vtappcorp_userid"]);
		$conf = new configuration();
		
		//Verifica si es un cliente
		if(substr($usua->access->PREFIX,0,2) == "CL") {
			$idcli = $usua->REFERENCE;
			$area->setClient($idcli);
			if($area->nerror > 0) {
				$result["message"] = $_SESSION["USER_NOT_CLIENT"];
				$result = utf8_converter($result);
				exit(json_encode($result));
			}
		}
		//Instancia las clases necesarias
		$config = new configuration("SITE_ROOT");
		$root = $_SERVER["DOCUMENT_ROOT"] . $config->getSiteRoot();
		
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
			$sep = "\/";
		else {
			$sep = "/";
			$root = realpath("../../../");
			if(strpos($root,"vtappcorp.com/") !== false)
				$root = str_replace("vtappcorp.com/","",$root);
			$root = str_replace('\/','/',$root) . $sep;
		}
	
		$storeFolder = $root . 'uploads' . $sep;
		$respFolder = $root . 'uploads' . $sep . 'response' . $sep;
		$errors = 0;
		$counter = 0;
		$arrErr = array();
		
		// Create response file
		$objResponse = new PHPExcel();

		$text = str_replace("{0}", "Area", $_SESSION["TITLE_RESPONSE_TEXT"]);
		// Set document properties
		$objResponse->getProperties()->setCreator($_SESSION["vtappcorp_userid"])
									 ->setLastModifiedBy($_SESSION["vtappcorp_userid"])
									 ->setTitle($text)
									 ->setSubject($text)
									 ->setDescription($text)
									 ->setCategory($text);

		$responseHeader = explode(",",$_SESSION["AREAS_RESPONSE_HEADER"]);
		// Add some data
		$objResponse->setActiveSheetIndex(0)
					->setCellValue('A1', $responseHeader[0])
					->setCellValue('B1', $responseHeader[1])
					->setCellValue('C1', $responseHeader[2])
					->setCellValue('D1', $responseHeader[3])
					->setCellValue('E1', $responseHeader[4]);


		// Rename worksheet
		$objResponse->getActiveSheet()->setTitle('Response');
		$rowResponse = 2;
		$error = "";

		try {
			foreach($datas as $key => $value) {
				if(strpos($key,"hfFile") !== false) {
					$fname = $storeFolder . $value;
					if(file_exists($fname)) {
						$objReader = PHPExcel_IOFactory::createReaderForFile($fname);
						$objReader->setReadDataOnly(true);
						$objPHPExcel = $objReader->load($fname);
						$objWorksheet = $objPHPExcel->getActiveSheet();
						$highestRow = $objWorksheet->getHighestRow(); 
						$highestColumn = $objWorksheet->getHighestColumn(); 
						$nomatch = false;
						$cont = 0;
						//Verifica las cabeceras
						foreach(range('A',$highestColumn) as $col) {
							if($headers[$cont] != $objWorksheet->getCell($col . '1')->getValue()) {
								$nomatch = true;
							}
							$cont++;
						}
						if($nomatch) {
							$errors++;
							array_push($arrErr,"File: $value :" . $_SESSION["COLUMNS_NOT_MATCH"]);
							continue;
						}
						//Recorre los resultados
						for($i = 2; $i <= $highestRow; $i++) {
							$counter++;
							//Asigna la informacion
							$area->AREA_NAME = $objWorksheet->getCellByColumnAndRow(0,$i)->getValue();
							$area->TITLE = $objWorksheet->getCellByColumnAndRow(1,$i)->getValue();
							$area->COSTCENTER = $objWorksheet->getCellByColumnAndRow(2,$i)->getValue();
							$area->setClient($idcli);
							$area->IS_BLOCKED = "FALSE";
							//Verifica la informacion relacionada
							$area->type->getInformationByName($objWorksheet->getCellByColumnAndRow(3,$i)->getValue());
							//Si no existe el empleado
							if($area->type->nerror > 0) {
								$errors++;
								$error = "File: $value - " . $area->AREA_NAME . " : Sigla " . $area->TITLE . " Area error : " . $area->error;
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$area->AREA_NAME);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$area->TITLE);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$area->COSTCENTER);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$objWorksheet->getCellByColumnAndRow(3,$i)->getValue());
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(4,$rowResponse,$error);
								//Aumenta la linea
								$rowResponse++;
								continue;
							}
							$area->setAreaType($area->type->ID);
							//Agrega el area
							$area->_add();
							//Verifica si hubo error
							if($area->nerror > 0) {
								$errors++;
								$error = "File: $value - " . $area->AREA_NAME . " : Sigla " . $area->TITLE . " Area error : " . $area->error;
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$area->AREA_NAME);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$area->TITLE);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$area->COSTCENTER);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$objWorksheet->getCellByColumnAndRow(3,$i)->getValue());
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(4,$rowResponse,$error);
								//Aumenta la linea
								$rowResponse++;
								continue;
							}
						}
					}
					else {
						$errors++;
						array_push($arrErr,"File: $value - " . $_SESSION["FILE_NOT_FOUND"]);
						continue;
					}
				}
			}
			$msgerror = sprintf($_SESSION["SOME_ERROR_OCURRED"],($counter-$errors),$errors);
			$result["to_download"] = false;
			//Verifica si hubo errores
			if($errors > 0) {
				// Set active sheet index to the first sheet, so Excel opens this as the first sheet
				$objResponse->setActiveSheetIndex(0);		
				//Crea el archivo de resultados
				$objWriter = PHPExcel_IOFactory::createWriter($objResponse, 'Excel2007');
				$fname = $respFolder . "Response_" . date("YmdHis") . "_" . $_SESSION["vtappcorp_userid"] . ".xlsx";
				$objWriter->save($fname);
				$msgerror .= "<br /><a href=\"$fname\">" . $_SESSION["CLICK_HERE_TO_DOWNLOAD"] . "</a>";
				$result["to_download"] = true;
			}
			
			$result["message"] = ($errors > 0) ? $msgerror : str_replace("%d", "", $_SESSION["SAVED"]);
			$result["success"] = $errors == 0;
		}
		catch (Exception $e) {
			$result["message"] = $e->getMessage();
			$result["success"] = false;
		}
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>
