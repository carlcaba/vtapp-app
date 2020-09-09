<?

	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');

	$class = "configuration";
	$file = "resources";
	$link = "resourcesman.php";
	if(!empty($_GET['class'])) {
		$class = $_GET['class'];
	}
	if(!empty($_GET['file'])) {
		$file = $_GET['file'];
	}
	if(!empty($_GET['link'])) {
		$link = $_GET['link'];
	}
	
	//Variable del codigo
	$result = array('success' => false,
                    'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
                    'link' => $link);

	require_once("../../classes/configuration.php");
	require_once("../../classes/$class.php");

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
		$root = str_replace('\/','/',$root);
	}
	
	$ds = $root . $sep;
	$fname = $file . "_" . $_SESSION["vtappcorp_userid"] . "_" . date("YmdHis") . "." . pathinfo($_FILES['file2Upload']['name'], PATHINFO_EXTENSION);
	$storeFolder = 'uploads';
	
	try {
		// Undefined | Multiple Files | $_FILES Corruption Attack
		// If this request falls under any of them, treat it invalid.
		if (!isset($_FILES['file2Upload']['error']) ||	is_array($_FILES['file2Upload']['error'])) {
			throw new RuntimeException($_SESSION["FILE_INVALID_PARAMETERS"]);
		}
		
		// Check $_FILES['file2Upload']['error'] value.
		switch ($_FILES['file2Upload']['error']) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				throw new RuntimeException($_SESSION["FILE_NO_FILE_SENT"]);
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new RuntimeException($_SESSION["FILE_EXCEEDED_SIZE"]);
			case UPLOAD_ERR_NO_TMP_DIR:
				throw new RuntimeException($_SESSION["FILE_NO_TMP_DIR"]);
			case UPLOAD_ERR_CANT_WRITE:
				throw new RuntimeException($_SESSION["FILE_CANT_WRITE"]);
			default:
				throw new RuntimeException($_SESSION["FILE_UNKNOW_ERROR"]);
		}

		// You should also check filesize here. 
		if ($_FILES['file2Upload']['size'] > 1000000) {
			throw new RuntimeException($_SESSION["FILE_EXCEEDED_SIZE"]);
		}

		/*
		// NOT IMPLEMENTED REQUIRES FILEINFO PHP EXTENSION
		// DO NOT TRUST $_FILES['file2Upload']['mime'] VALUE !!
		// Check MIME Type by yourself.
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		if (false === $ext = array_search($finfo->file($_FILES['file2Upload']['tmp_name']), array('xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'xls' => 'application/vnd.ms-excel', 'csv' => 'text/csv'), true)) {
			throw new RuntimeException($_SESSION["FILE_INVALID_FORMAT"]);
		}
		*/

		$tempFile = $_FILES['file2Upload']['tmp_name'];
		$targetPath = $ds. $storeFolder . $sep;
		$targetFile =  $targetPath . $fname;

		// You should name it uniquely.
		// DO NOT USE $_FILES['file2Upload']['name'] WITHOUT ANY VALIDATION !!
		// On this example, obtain safe unique name from its binary data.
		if (!move_uploaded_file($tempFile,$targetFile)) {
			throw new RuntimeException(sprintf($_SESSION["FILE_MOVEUPLOADED_FAILED"],$tempFile,$targetFile));
		}

		$result["success"] = true;
		$result["link"] = $fname;
		$result["targetFile"] = $targetFile;
		$result["message"] = $_SESSION["FILE_UPLOADED_TO_SERVER"];

	} 
	catch (RuntimeException $e) {
		$result["message"] = $_SESSION["AN_ERROR_OCCURRED"] . " " . $e->getMessage() . " " . $fname . " " . print_r($_FILES,true);
		$result["success"] = false;
		$result["link"] = is_dir($targetPath); //substr(sprintf('%o', fileperms($targetPath)), -4);
		$result["targetFile"] = $targetFile;
		$result["targetPath"] = $targetPath;
	}	
	
	$preview = array("caption" => "file.jpg",
					"url" =>  $fname,
					"key" => 100, 
					"extra" => array("id" => 100)
					);
	
	$result = array("error" => $result["success"] ? "" : $result["message"] ,
					"initialPreview" => array(),
					"initialPreviewConfig" => array(),
					"initialPreviewThumbTags" => $preview,
					"append" => true);
	//Termina
	exit(json_encode($result));
?>