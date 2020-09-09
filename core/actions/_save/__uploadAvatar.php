<?
	//Inicio de sesion
	session_name('vtappcorp_session');
	session_start();
	
	//Inicializa la cabecera
    header('Content-Type: text/plain; charset=utf-8');
	
    include_once("../../classes/upload/class.upload.php");
	include_once("../../classes/logs.php");

	//Funcion para realizar el proceso de conversion
	function processImage(&$handle, &$result, $size = 160, $title = 'file1') {
		global $dir, $dirUp;
		$id = substr($title,-1);

		$handle->file_overwrite = true;
		$handle->image_resize = true;
		$handle->image_ratio_y = true;
		$handle->image_convert = jpg;
		$handle->image_x = $size;
				
		$handle->process($dir);

		if ($handle->processed) {
			$result[$title] = $dirUp . $handle->file_dst_name;
			$result["result" . $id] = sprintf($_SESSION["AVATAR_CREATED"],($size . "x" . $size));
			$result["success" . $id] = true;
		}
		else {
			$result[$title] = "";
			$result["result" . $id] = sprintf($_SESSION["AVATAR_NOT_CREATED"],($size . "x" . $size),$handle->error);
			$result["success" . $id] = false;
		}
	}	
	
    //Variable del codigo
    $result = array('success' => false,
					'message' => $_SESSION["NO_DATA_FOR_VALIDATE"],
					'file1' => "",
					'result1' => "",
					'success1' => false,
					'file2' => "",
					'result2' => "",
					'success2' => false);
		
	//Define los directorios
	$dir = "../../../img/users/";
	$dirUp = "img/users/";
	//Define el nombre de la imagen
	$idUser = $_GET["hfIdUser"];
	
	//Verifica si el archivo existe y lo elimina
	foreach (glob($dir . $idUser . ".*") as $filename) {
		//Elimina la imagen
		unlink($filename);
	}	

	//Si es una llamada vía AJAX
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
		//Verifica la entrada del archivo
		if (isset($_SERVER['HTTP_X_FILE_NAME']) && isset($_SERVER['CONTENT_LENGTH'])) {
			$handle = new upload('php:'.$_SERVER['HTTP_X_FILE_NAME']);
		} 
		else {
			$handle = new upload($_FILES['fiAvatar']);
		}
		
		//Si el archivo se cargo correctamente
		if ($handle->uploaded) {
			//Imagen 160
			$handle->file_new_name_body = $idUser . "_160x160";
			//Procesa la imagen con las caracteristicas especificadas
			processImage($handle, $result);
			
			//Imagen 128
			$handle->file_new_name_body = $idUser;
			//Procesa la imagen con las caracteristicas especificadas
			processImage($handle, $result, 128, "file2");
			//Log de auditoria
			$log = new logs("Change profile image 128. Result: " . $result["result2"]. " File: " . $result["file2"]);
			$log->_add();

			//Delete temporary files
			$handle->clean();

			$result["success"] = ($result["success1"] && $result["success2"]);
			$result["message"] = sprintf($_SESSION["AVATAR_CREATED"],"");
			$_SESSION["vtappcorp_user_message"] = $result["message"];
		}
		else {
			$result["message"] = sprintf($_SESSION["AVATAR_FILE_NOT_UPLOADED"],$handle->error);
		}
	}
    else {
        $result["message"] = $_SESSION["ACCESS_NOT_AUTHORIZED"];
    }
	
    $result = utf8_converter($result);
    //Termina
    exit(json_encode($result));		
	
?>