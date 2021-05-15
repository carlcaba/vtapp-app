<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
	header('Content-Type: text/plain; charset=utf-8');
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => 'partners.php');
					
	//Realiza la operacion
	require_once("../../classes/partner.php");
	
	//Captura las variables
	if(empty($_POST['strModel'])) {
		//Verifica el GET
		if(empty($_GET['strModel'])) {
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
		
		//Asigna la informacion
		$partner = new partner();
		
		//Verifica el user id
		$partner->PARTNER_NAME = $datas->txtPARTNER_NAME;
		//Consulta la informacion
		$partner->getInformationByOtherInfo();
		//Si hay error
		if($partner->nerror == 0) {
			$result["message"] = $_SESSION["MSG_DUPLICATED_RECORD"];
			$result["sql"] = $partner->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Verifica el email
		$partner->EMAIL = $datas->txtEMAIL;
		//Consulta la informacion
		$partner->getInformationByOtherInfo("EMAIL");
		//Si hay error
		if($partner->nerror == 0) {
			$result["message"] = $_SESSION["MSG_DUPLICATED_RECORD"];
			$result["sql"] = $partner->sql;
			$result = utf8_converter($result);
			exit(json_encode($result));
		}
		
		//Actualiza la información
		$partner->IDENTIFICATION = $datas->cbTBL_PARTNER_IDENTIFICATION . "-" . $datas->txtTBL_PARTNER_IDENTIFICATION;
		$partner->ADDRESS = $datas->txtADDRESS;
		$partner->PHONE = $datas->txtPHONE;
		$partner->PHONE_ALT = $datas->txtPHONE_ALT;
		$partner->CELLPHONE = $datas->txtCELLPHONE;
		$partner->CELLPHONE_ALT = $datas->txtCELLPHONE_ALT;
		$partner->setCity($datas->cbCity);
		$partner->LATITUDE = $datas->hfLATITUDE;
		$partner->LONGITUDE = $datas->hfLONGITUDE;
		$partner->EMAIL_ALT = $datas->txtEMAIL_ALT;
		$partner->CONTACT_NAME = $datas->txtCONTACT_NAME;
		$partner->EMAIL_CONTACT = $datas->txtEMAIL_CONTACT;
		$partner->PHONE_CONTACT = $datas->txtPHONE_CONTACT;
		$partner->CELLPHONE_CONTACT = $datas->txtCELLPHONE_CONTACT;
		$partner->SKIN = $datas->cbSkin;
		$partner->IS_BLOCKED = $datas->cbBlocked == "" ? "FALSE" : strtoupper($datas->cbBlocked);

		//Lo adiciona
		$partner->_add();

		//Si hay error
		if($partner->nerror > 0) {
			//Confirma mensaje al usuario
			$result['message'] = $partner->error;
			$result["sql"] = $partner->sql;
			$result = utf8_converter($result);
			//Termina
			exit(json_encode($result));
		}
		
		//Verifica si debe copiar el archivo
		if ($datas->cbSkin != "" && $datas->FilePartnerImage != "") {
			require_once("../../classes/configuration.php");
		
			//Instancia las clases necesarias
			$config = new configuration("SITE_ROOT");
			$root = $_SERVER["DOCUMENT_ROOT"] . $config->verifyValue("SITE_ROOT");
			
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
				$sep = "/";
			else {
				$sep = "/";
				$root = realpath("../../../");
				if(strpos($root,"vtappcorp.com/") !== false)
					$root = str_replace("vtappcorp.com/","",$root);
				$root = str_replace('\/','/',$root);
			}			
			$storeFolder = 'uploads';
			try {
				//Copia el archivo del origen al destino final
				$imgToCopy = $root. $storeFolder . $sep . $datas->FilePartnerImage;
				$extension = strtolower(pathinfo($imgToCopy, PATHINFO_EXTENSION)); 
				switch ($extension) {
					case 'jpg':
					case 'jpeg':
					   $image = imagecreatefromjpeg($imgToCopy);
					break;
					case 'gif':
					   $image = imagecreatefromgif($imgToCopy);
					break;
					case 'png':
					   $image = imagecreatefrompng($imgToCopy);
					break;
				}				
				$result["extension"] = $extension;
				$result["loadimage"] = $imgToCopy;
				//Imagen a cadena
				$targetFile = $root. "img" . $sep . "partners" . $sep . $partner->ID . ".png";
				$result["targetfile"] = $targetFile;
				//Genera la nueva imagen en png
				$result["resultimage"] = imagepng($image, $targetFile);
			}
			catch (Exception $e) {
				$result["loadimage"] = $e->getMessage() . "\n" . $imgToCopy;
			}
		}

		//Cambia el resultado
		$result['success'] = true;
		$result['message'] = $_SESSION["PARTNER_REGISTERED"];
	}
	else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
	}
	$result = utf8_converter($result);
	//Termina
	exit(json_encode($result));
?>