
<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');
	
	
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'services-complete.php');

	require_once("../../classes/service.php");

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
		require_once("../../classes/service_state.php");
		require_once("../../classes/users.php");
		require_once("../../classes/PHPExcel/IOFactory.php");
		require_once("../../classes/resources.php");
		$reso = new resources();
		$headers = explode(",",$reso->getResourceByName("SERVICE_HEADER"));

		$serv = new service();
		$usua = new users($_SESSION["vtappcorp_userid"]);
		$usua->__getInformation();
		$conf = new configuration();
		$idcli = "";

		$state = new service_state("SERVICE_STATE_1");
		
		//Verifica si es un cliente
		if(substr($usua->access->PREFIX,0,2) == "CL") {
			$idcli = $usua->REFERENCE;
			$serv->setClient($idcli);
			if($serv->nerror > 0) {
				$result["message"] = $_SESSION["USER_NOT_CLIENT"];
				$result = utf8_converter($result);
				exit(json_encode($result));
			}
		}
		
		//Verifica el cliente
		if($idcli == "") {
			$serv->client->getInfoByDefault();
			$idcli = $serv->client->ID;
			if($serv->nerror > 0) {
				$result["message"] = $_SESSION["DEFAULT_CLIENT_NOT_FOUND"];
				$result = utf8_converter($result);
				exit(json_encode($result));
			}			
		}
		
		//Instancia las clases necesarias
		$config = new configuration("SITE_ROOT");
		$root = $_SERVER["DOCUMENT_ROOT"] . $config->verifyValue("SITE_ROOT");
		$webapi = $config->verifyValue("MAPS_API_GET_LOCATION");
		$apikey = $config->verifyValue("MAPS_API_KEY");
		
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
			$sep = "\/";
		else {
			$sep = "/";
			$root = realpath("../../../");
			if(strpos($root,"vtappcorp.com/") !== false)
				$root = str_replace("vtappcorp.com/","",$root);
			$root .= $sep;
		}
		$root = str_replace('/',$sep,$root);
	
		$storeFolder = $root . 'uploads' . $sep;
		$respFolder = $root . 'uploads' . $sep . 'response' . $sep;
		$downloadFolder = 'uploads/response/';
		$errors = 0;
		$counter = 0;
		$arrErr = array();
		
		// Create response file
		$objResponse = new PHPExcel();

		$text = str_replace("{0}", "Service", $_SESSION["TITLE_RESPONSE_TEXT"]);
		// Set document properties
		$objResponse->getProperties()->setCreator($_SESSION["vtappcorp_userid"])
									 ->setLastModifiedBy($_SESSION["vtappcorp_userid"])
									 ->setTitle($text)
									 ->setSubject($text)
									 ->setDescription($text)
									 ->setCategory($text);

		$responseHeader = explode(",",$_SESSION["SERVICE_RESPONSE_HEADER"]);
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
							//Reinicia el estado
							$state->ID = $state->getIdByStep(1);
							$counter++;
							//Asigna la informacion
							$serv->setUser($usua->ID);
							$serv->setClient($idcli);
							$serv->REQUESTED_BY = $usua->getFullName();
							$serv->REQUESTED_EMAIL = $usua->EMAIL;
							$serv->REQUESTED_PHONE = $usua->PHONE;
							$serv->REQUESTED_CELLPHONE = $usua->CELLPHONE;
							$serv->REQUESTED_IP = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
							$serv->REQUESTED_ADDRESS = $objWorksheet->getCellByColumnAndRow(1,$i)->getValue() . ", " . $objWorksheet->getCellByColumnAndRow(0,$i)->getValue() . ", Colombia";
							//Verifica la zona por default
							$serv->request_zone->ZONE_NAME = "NO DEFINIDA";
							$serv->request_zone->getInformationByOtherInfo();
							$serv->setRequestZone($serv->request_zone->ID);
							$serv->DELIVER_DESCRIPTION = $objWorksheet->getCellByColumnAndRow(7,$i)->getValue();
							$serv->DELIVER_TO = $objWorksheet->getCellByColumnAndRow(2,$i)->getValue();
							$serv->DELIVER_EMAIL = $objWorksheet->getCellByColumnAndRow(3,$i)->getValue();
							$serv->DELIVER_PHONE = $objWorksheet->getCellByColumnAndRow(4,$i)->getValue();
							$serv->DELIVER_CELLPHONE = $objWorksheet->getCellByColumnAndRow(5,$i)->getValue();
							$serv->DELIVER_ADDRESS = $objWorksheet->getCellByColumnAndRow(6,$i)->getValue() . ", " . $objWorksheet->getCellByColumnAndRow(0,$i)->getValue() . ", Colombia";
							$serv->setDeliverZone($serv->request_zone->ID);
							//Verifica el tipo de envio
							$serv->type->getInformationByOtherInfo("DELIVERY_TYPE_NAME", $objWorksheet->getCellByColumnAndRow(8,$i)->getValue());
							//Si no esta definida
							if($serv->type->nerror > 0) {
								$errors++;
								$error = "File: $value - " . $serv->REQUESTED_ADDRESS . " : destinatario " . $serv->DELIVER_TO . " Tipo de servicio error: " . $serv->type->error;
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$serv->REQUESTED_ADDRESS);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$serv->DELIVER_TO);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$serv->DELIVER_ADDRESS);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$error);
								//Aumenta la linea
								$rowResponse++;
								continue;
							}
							$serv->setDeliveryType($serv->type->ID);
							$serv->PRICE = 0;
							$serv->setState($state->ID);				
							
							$serv->ROUND_TRIP = $objWorksheet->getCellByColumnAndRow(9,$i)->getValue() == "Si" ? "TRUE" : "FALSE";
							$serv->OBSERVATION = $objWorksheet->getCellByColumnAndRow(10,$i)->getValue();
							$serv->FRAGILE = $objWorksheet->getCellByColumnAndRow(11,$i)->getValue() == "Si" ? "TRUE" : "FALSE";
							
							//Genera la hora a partir de la hora ingresada
							//$time = strtotime($objWorksheet->getCellByColumnAndRow(12,$i)->getFormattedValue());
							$formula = "(M" . $i . "-DATE(1970,1,1))*86400"; 
							$cell = PHPExcel_Cell::stringFromColumnIndex(15);
							$objWorksheet->setCellValue($cell . $i, "=" . $formula);
							$inttime = intval($objWorksheet->getCellByColumnAndRow(15,$i)->getCalculatedValue());
							$serv->TIME_START_TO_DELIVER = date('G', $inttime);
							$serv->TIME_START_TO_DELIVER = intval($serv->TIME_START_TO_DELIVER) - 1;
							
							//Genera la hora a partir de la hora ingresada
							//$time = strtotime($objWorksheet->getCellByColumnAndRow(13,$i)->getFormattedValue());
							$formula = "(N" . $i . "-DATE(1970,1,1))*86400"; 
							$cell = PHPExcel_Cell::stringFromColumnIndex(16);
							$objWorksheet->setCellValue($cell . $i, "=" . $formula);
							$inttime = intval($objWorksheet->getCellByColumnAndRow(16,$i)->getCalculatedValue());
							$serv->TIME_FINISH_TO_DELIVER = date('G', $inttime);
							$serv->TIME_FINISH_TO_DELIVER = intval($serv->TIME_FINISH_TO_DELIVER) - 1;
							
							//Verifica el tipo de vehiculo solicitado
							$serv->vehicle->getInformationByName($objWorksheet->getCellByColumnAndRow(14,$i)->getValue());
							//Si no esta definida
							if($serv->vehicle->nerror > 0) {
								$errors++;
								$error = "File: $value - " . $serv->REQUESTED_ADDRESS . " : destinatario " . $serv->DELIVER_TO . " Tipo de vehículo error: " . $serv->vehicle->error;
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$serv->REQUESTED_ADDRESS);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$serv->DELIVER_TO);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$serv->DELIVER_ADDRESS);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$error);
								//Aumenta la linea
								$rowResponse++;
								continue;
							}
							$serv->setVehicle($serv->vehicle->ID);
							//Actualiza el ID
							$serv->ID = "UUID()";
							
							//Actualiza las coordenadas
							$rqCoord = $serv->GetCoordinates($webapi, $apikey);
							$dvCoord = $serv->GetCoordinates($webapi, $apikey, "DELIVER");
							
							//Verifica
							if($rqCoord != null) {
								$serv->REQUESTED_COORDINATES = $rqCoord->results[0]->geometry->location->lat . "," . $rqCoord->results[0]->geometry->location->lng;
								
							}
							if($dvCoord != null) {
								$serv->DELIVER_COORDINATES = $dvCoord->results[0]->geometry->location->lat . "," . $dvCoord->results[0]->geometry->location->lng;
							}
							
							error_log(print_r($rqCoord,true) . " " . print_r(debug_backtrace(2), true));
							error_log(print_r($dvCoord,true) . " " . print_r(debug_backtrace(2), true));
							
							//Agrega el recurso
							$serv->_add();
							
							//Verifica si hubo error
							if($serv->nerror > 0) {
								$errors++;
								$error = "File: $value - " .
								$serv->REQUESTED_ADDRESS . " : destinatario " . $serv->DELIVER_TO . " Service error: " . $serv->error . " trc: " . $serv->sql;
								array_push($arrErr,$error);
								//Adiciona al response
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(0,$rowResponse,$serv->REQUESTED_ADDRESS);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(1,$rowResponse,$serv->DELIVER_TO);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(2,$rowResponse,$serv->DELIVER_ADDRESS);
								$objResponse->getActiveSheet()->setCellValueByColumnAndRow(3,$rowResponse,$error);
								//Aumenta la linea
								$rowResponse++;
								continue;
							}
							
							//Actualiza el registro al siguiente estado
							$serv->updateState($state->getNextState());
						}
					}
					else {
						$errors++;
						array_push($arrErr,"File: $value - " . $_SESSION["FILE_NOT_FOUND"]);
						continue;
					}
				}
			}
			$msgerror = sprintf($_SESSION["SOME_ERROR_OCURRED"],($counter-$errors),$errors) . "<br />" . implode("<br />", $arrErr);
			$result["to_download"] = false;
			//Verifica si hubo errores
			if($errors > 0) {
				// Set active sheet index to the first sheet, so Excel opens this as the first sheet
				$objResponse->setActiveSheetIndex(0);		
				//Crea el archivo de resultados
				$objWriter = PHPExcel_IOFactory::createWriter($objResponse, 'Excel2007');
				$fname = $respFolder . "Response_" . date("YmdHis") . "_" . $_SESSION["vtappcorp_userid"] . ".xlsx";
				$dname = $downloadFolder . "Response_" . date("YmdHis") . "_" . $_SESSION["vtappcorp_userid"] . ".xlsx";
				$objWriter->save($fname);
				$msgerror .= "<br /><a href=\"$dname\">" . $_SESSION["CLICK_HERE_TO_DOWNLOAD"] . "</a>";
				$result["to_download"] = true;
				$result["file_path"] = $fname;
			}
			
			$result["message"] = ($errors > 0) ? $msgerror : str_replace("%d", "", $_SESSION["SAVED"]);
			$result["success"] = $errors == 0;
			
			//Verifica el total de registros
			if($counter-$errors > 0) {
				require_once("../../classes/notification.php");
				//Agrega las notificaciones
				$notification = new notification($usua->ID);

				//Verifica el tipo
				$notification->type->TEXT_TYPE = "info";
				$notification->type->getInformationByOtherInfo();
				//Agrega la notificacion
				$notification->setType($notification->type->ID);
				$notification->MESSAGE = str_replace("{0}",($counter-$errors),$_SESSION["SERVICE_UPLOAD_MESSAGE"]);
				$notification->MESSAGE = str_replace("{1}",$result["link"],$notification->MESSAGE);
				$notification->MESSAGE .= implode("<br />", $arrErr);
				$notification->SOURCE = "services-management.php";
				//La agrega
				$notification->_add(false,false);
			}
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