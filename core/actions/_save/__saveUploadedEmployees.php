<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'employees.php');

	require_once("../../classes/employee.php");

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
		require_once("../../classes/document_type.php");
		require_once("../../classes/users.php");
		require_once("../../classes/PHPExcel/IOFactory.php");
		require_once("../../classes/resources.php");
		$reso = new resources();
		$headers = explode(",",$reso->getResourceByName("EMPLOYEES_HEADER"));
		
		$lang = new language();
		$empl = new employee();
		$doctype = new document_type();
		$usua = new users();
		$conf = new configuration();
		
		//Instancia las clases necesarias
		$config = new configuration("SITE_ROOT");
		$root = $_SERVER["DOCUMENT_ROOT"] . $config->verifyValue("SITE_ROOT");

		
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

		$text = str_replace("{0}", "Employee", $_SESSION["TITLE_RESPONSE_TEXT"]);
		// Set document properties
		$objResponse->getProperties()->setCreator($_SESSION["vtappcorp_userid"])
									 ->setLastModifiedBy($_SESSION["vtappcorp_userid"])
									 ->setTitle($text)
									 ->setSubject($text)
									 ->setDescription($text)
									 ->setCategory($text);

		$responseHeader = explode(",",$_SESSION["EMPLOYEE_RESPONSE_HEADER"]);
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
							array_push($arrErr,"File: $value :" . $_SESSION["COLUMNS_NOT_MATCH"]);
							continue;
						}
						//Recorre los resultados
						for($i = 2; $i <= $highestRow; $i++) {
							$counter++;
							//Asigna la informacion
							$empl->partner->IDENTIFICATION = $objWorksheet->getCellByColumnAndRow(0,$i)->getValue();
							$empl->area->AREA_NAME = $objWorksheet->getCellByColumnAndRow(1,$i)->getValue();
							$empl->area->getInformationByName($empl->area->AREA_NAME, $empl->partner->IDENTIFICATION);
							$empl->access->getInformationByName("ALE");
							$doctype->RESOURCE_NAME = $objWorksheet->getCellByColumnAndRow(5,$i)->getValue();
							$doctype->getInformationByShortName("RESOURCE_NAME");
							$empl->city->CITY_NAME = $objWorksheet->getCellByColumnAndRow(10,$i)->getValue();
							$empl->city->getInformationByOtherInfo();
							$email = explode("@",$objWorksheet->getCellByColumnAndRow(11,$i)->getValue());

							$empl->USER_ID = $email[0];
							$empl->FIRST_NAME = $objWorksheet->getCellByColumnAndRow(2,$i)->getValue();
							$empl->MIDDLE_NAME = $objWorksheet->getCellByColumnAndRow(3,$i)->getValue();
							$empl->LAST_NAME = $objWorksheet->getCellByColumnAndRow(4,$i)->getValue();
							$empl->IDENTIFICATION = $objWorksheet->getCellByColumnAndRow(6,$i)->getValue();
							$empl->ADDRESS = $objWorksheet->getCellByColumnAndRow(7,$i)->getValue();
							$empl->PHONE = $objWorksheet->getCellByColumnAndRow(8,$i)->getValue();
							$empl->CELLPHONE = $objWorksheet->getCellByColumnAndRow(9,$i)->getValue();
							$empl->EMAIL = $objWorksheet->getCellByColumnAndRow(11,$i)->getValue();
							$empl->IS_BLOCKED = "FALSE";
							//Verifica la informacion relacionada
							$empl->partner->getInformationByIdentification();
							//Si no existe el empleado
							if($empl->partner->nerror > 0) {
								$errors++;
								$error = "File: $value - " . $empl->partner->IDENTIFICATION . " : empleado " . $empl->IDENTIFICATION . " Partner error: " . $empl->partner->error;
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$empl->partner->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$empl->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$empl->area->AREA_NAME);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$error);
								//Aumenta la linea
								$rowResponse++;
								continue;
							}
							//Si no existe el area
							if($empl->area->nerror > 0) {
								$errors++;
								$error = "File: $value - " . $empl->partner->IDENTIFICATION . " : empleado " . $empl->IDENTIFICATION . " Area error: " . $empl->area->error;
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$empl->partner->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$empl->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$empl->area->AREA_NAME);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$error);
								//Aumenta la linea
								$rowResponse++;
								continue;
							}
							//Si no existe el tipo de documento
							if($doctype->nerror > 0) {
								$errors++;
								$error = "File: $value - " . $empl->partner->IDENTIFICATION . " : empleado " . $empl->IDENTIFICATION . " Doc Type error: " . $doctype->error;
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$empl->partner->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$empl->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$empl->area->AREA_NAME);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$error);
								//Aumenta la linea
								$rowResponse++;
								continue;
							}
							//Si no existe la ciudad
							if($empl->city->nerror > 0) {
								$errors++;
								$error = "File: $value - " . $empl->partner->IDENTIFICATION . " : empleado " . $empl->IDENTIFICATION . " City error: " . $empl->city->error;
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$empl->partner->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$empl->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$empl->area->AREA_NAME);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$error);
								//Aumenta la linea
								$rowResponse++;
								continue;
							}
							
							//Verifica el user id
							$usua->ID = $empl->USER_ID;
							//Consulta la informacion
							$usua->__getInformation();
							//Si hay error
							if($usua->nerror == 0) {
								$errors++;
								$error = "File: $value - " . $empl->partner->IDENTIFICATION . " : empleado " . $empl->IDENTIFICATION . " User error: " . $_SESSION["USER"] . " " . $_SESSION["MSG_DUPLICATED_RECORD"];
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$empl->partner->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$empl->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$empl->area->AREA_NAME);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$error);
								//Aumenta la linea
								$rowResponse++;
								continue;
							}
							
							$usua->EMAIL = $datas->txtEMAIL;
							//Consulta el email
							$usua->getInfoByMail();
							//Si hay error
							if($usua->nerror == 0) {
								$errors++;
								$error = "File: $value - " . $empl->partner->IDENTIFICATION . " : empleado " . $empl->IDENTIFICATION . " User error: " . $_SESSION["USER"] . " " . $_SESSION["MSG_DUPLICATED_EMAIL"];
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$empl->partner->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$empl->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$empl->area->AREA_NAME);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$error);
								//Aumenta la linea
								$rowResponse++;
								continue;
							}

							//Consulta la informacion
							$empl->getInformationByOtherInfo();
							//Si hay error
							if($empl->nerror == 0) {
								$errors++;
								$error = "File: $value - " . $empl->partner->IDENTIFICATION . " : empleado " . $empl->IDENTIFICATION . " Employee error: " . $_SESSION["MSG_DUPLICATED_RECORD"];
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$empl->partner->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$empl->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$empl->area->AREA_NAME);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$error);
								//Aumenta la linea
								$rowResponse++;
								continue;
							}
							
							//Verifica la identificacion
							$empl->IDENTIFICATION = $doctype->ABBREVIATION . "-" . $empl->IDENTIFICATION;
							//Consulta la informacion
							$empl->getInformationByOtherInfo("IDENTIFICATION");
							//Si hay error
							if($empl->nerror == 0) {
								$errors++;
								$error = "File: $value - " . $empl->partner->IDENTIFICATION . " : empleado " . $empl->IDENTIFICATION . " Employee error: " . $_SESSION["MSG_DUPLICATED_RECORD"];
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$empl->partner->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$empl->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$empl->area->AREA_NAME);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$error);
								//Aumenta la linea
								$rowResponse++;
								continue;
							}
							
							//Actualiza la información del usuario
							$usua->setAccess($empl->access->ID);
							$usua->CHANGE_PASSWORD = "TRUE";
							$usua->setCity($empl->city->ID);
							$usua->IDENTIFICATION = $empl->IDENTIFICATION;
							$usua->FACEBOOK_USER = "";
							$usua->GOOGLE_USER = "";
							$usua->LATITUDE = "";
							$usua->LONGITUDE = "";
							$usua->ADDRESS = $empl->ADDRESS;
							$usua->CELLPHONE = $empl->CELLPHONE;
							$usua->FIRST_NAME = $empl->FIRST_NAME;
							$usua->LAST_NAME = $empl->LAST_NAME;
							$usua->PHONE = $empl->PHONE;
							$usua->THE_PASSWORD = $conf->verifyValue("INIT_PASSWORD");
							$usua->IS_BLOCKED = $empl->IS_BLOCKED;
							
							//Agrega el usuario
							$usua->__add("",LANGUAGE);
							
							//Si hay error
							if($usua->nerror > 0) {
								//Si es error de correo
								if($usua->nerror == 18) {
									$errors = $errors;
								}
								else if($usua->nerror == 30) {
									$errors = $errors;
								}
								else {
									$errors++;
									$error = "File: $value - " . $empl->partner->IDENTIFICATION . " : empleado " . $empl->IDENTIFICATION . " User error: " . $usua->error;
									array_push($arrErr,$error);
									//Adiciona al response
									$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$empl->partner->IDENTIFICATION);
									$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$empl->IDENTIFICATION);
									$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$empl->area->AREA_NAME);
									$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$error);
									//Aumenta la linea
									$rowResponse++;
									continue;
								}
							}
							
							//Actualiza el ID
							$empl->ID = "UUID()";
							//Agrega el recurso
							$empl->_add();
							//Verifica si hubo error
							if($empl->nerror > 0) {
								$errors++;
								$error = "File: $value - " . $empl->partner->IDENTIFICATION . " : empleado " . $empl->IDENTIFICATION . " Employee error : " . $empl->error;
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$empl->partner->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$empl->IDENTIFICATION);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$empl->area->AREA_NAME);
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
	exit(json_encode($result));
?>


