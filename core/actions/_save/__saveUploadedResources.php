<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'resourcesman.php');

	require_once("../../classes/resources.php");

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
		require_once("../../classes/PHPExcel/IOFactory.php");
		
		$lang = new language();
		$reso = new resources();
		$headers = explode(",",$reso->getResourceByName("RESOURCES_HEADER"));
		
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

		$text = str_replace("{0}", "Resource", $_SESSION["TITLE_RESPONSE_TEXT"]);
		// Set document properties
		$objResponse->getProperties()->setCreator($_SESSION["vtappcorp_userid"])
									 ->setLastModifiedBy($_SESSION["vtappcorp_userid"])
									 ->setTitle($text)
									 ->setSubject($text)
									 ->setDescription($text)
									 ->setCategory($text);

		$responseHeader = explode(",",$_SESSION["RESOURCE_RESPONSE_HEADER"]);
		// Add some data
		$objResponse->setActiveSheetIndex(0)
					->setCellValue('A1', $responseHeader[0])
					->setCellValue('B1', $responseHeader[1])
					->setCellValue('C1', $responseHeader[2])
					->setCellValue('D1', $responseHeader[3]);

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
							array_push($arrErr,"File: $value - " . $reso->RESOURCE_NAME . ":" . $_SESSION["COLUMNS_NOT_MATCH"]);
							continue;
						}
						//Recorre los resultados
						for($i = 2; $i <= $highestRow; $i++) {
							$counter++;
							//Asigna el nombre del recurso
							$reso->RESOURCE_NAME = $objWorksheet->getCellByColumnAndRow(0,$i)->getValue();
							//Define el lenguaje
							$lengua = $lang->getInformationByName($objWorksheet->getCellByColumnAndRow(5,$i)->getValue());
							//Si no existe el lenguaje
							if($lang->nerror > 0) {
								//Crea el recurso
								$res2 = new resources();
								$res2->RESOURCE_NAME = "LANGUAGE_";
								$res2->getNextResource();
								
								$res2->RESOURCE_TEXT = str_replace("'","\'",$objWorksheet->getCellByColumnAndRow(5,$i)->getValue());
								$res2->RESOURCE_TEXT = htmlentities($res2->RESOURCE_TEXT);		
								$res2->SYSTEM = "FALSE";
								$res2->LANGUAGE_ID = $_SESSION["LANGUAGE"];
								//Lo adiciona
								$res2->_add();
								
								$lang = new language();
								$lang->LANGUAGE_NAME = $res2->RESOURCE_NAME;
								$lang->ABBRV = $objWorksheet->getCellByColumnAndRow(4,$i)->getValue();
								$lang->BLOCKED = "FALSE";
								//Lo adiciona
								$lang->_add();
								
								if($lang->nerror > 0) {
									$errors++;
									$error = "File: $value - " . $reso->RESOURCE_NAME . ": lang " . $objWorksheet->getCellByColumnAndRow(5,$i)->getValue() . " " . $lang->error;
									array_push($arrErr,$error);
									//Adiciona al response
									$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$reso->RESOURCE_NAME);
									$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$objWorksheet->getCellByColumnAndRow(3,$i)->getValue());
									$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$objWorksheet->getCellByColumnAndRow(5,$i)->getValue());
									$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$error);
									//Aumenta la linea
									$rowResponse++;
									continue;
								}
							}
							//Verifica que el lenguaje este activo
							if($lang->BLOCKED == 1)
								$lang->activate();
							//Verifica la accion
							$isAdd = (array_search($lang->ID,$reso->getLanguageArray()) === FALSE);
							//Verifica que este registrado
							$reso->getResourceObjByName("",$lang->ID);
							//Si no esta registrado
							if($reso->nerror > 0) {
								$errors++;
								$error = "File: $value - " . $reso->RESOURCE_NAME . ":" . $_SESSION["NOT_REGISTERED"];
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$reso->RESOURCE_NAME);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$objWorksheet->getCellByColumnAndRow(3,$i)->getValue());
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$objWorksheet->getCellByColumnAndRow(5,$i)->getValue());
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$error);
								//Aumenta la linea
								$rowResponse++;
								continue;
							}
							//Asigna los valores
							$reso->RESOURCE_TEXT = htmlentities(htmlspecialchars($objWorksheet->getCellByColumnAndRow(3,$i)->getValue()));
							//Si no tiene valor
							if($reso->RESOURCE_TEXT == "")
								continue;
							$reso->LANGUAGE_ID = $lengua;
							//Completa la informacion
							$reso->BLOCKED = "FALSE";
							//Verifica la accion
							if($isAdd) {
								//ACtualiza el ID
								$reso->ID = 0;
								//Agrega el recurso
								$reso->_add();
							}
							else
								$reso->_modify();
							//Verifica si hubo error
							if($reso->nerror > 0) {
								$errors++;
								$error = "File: $value - " . $reso->RESOURCE_NAME . ": lang " . $objWorksheet->getCellByColumnAndRow(5,$i)->getValue() . " " . $reso->error;
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$reso->RESOURCE_NAME);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$reso->RESOURCE_TEXT);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$objWorksheet->getCellByColumnAndRow(5,$i)->getValue());
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$error);
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
	exit(json_encode($result));?>


