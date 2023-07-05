<?

// LOGICA ESTUDIO 2016

//Incluye las clases dependientes
require_once("table.php");
require_once("notification.php");

class notification_file extends table {
	var $notification;
	
	//Constructor
	function __constructor($text = "") {
		$this->notification_file($text);
	}
	
	//Constructor anterior
	function notification_file($text = '') {
		//Llamado al constructor padre
		parent::table("TBL_SYSTEM_NOTIFICATION_FILE");
		//Inicializa los atributos
		$this->ID = 'UUID()';
		$this->DESCRIPTION = $text;
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->notification = new notification();
	}

	//Funcion para Set la notificacion
	function setNotification($notif) {
		//Asigna la informacion
		$this->notification->ID = $notif;
		//Verifica la informacion
		$this->notification->__getInformation();
		//Si no hubo error
		if($this->notification->nerror == 0) {
			//Asigna el valor
			$this->NOTIFICATION_ID = $notif;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->NOTIFICATION_ID = "";
			//Genera error
			$this->nerror = 20;
			$this->error = $_SESSION["NOT_REGISTERED"];
		}
	}
	
	//Funcion para Get la notificacion
	function getNotification() {
		//Asigna el valor del sistema
		$this->NOTIFICATION_ID = $this->notification->ID;
		//Busca la informacion
		$this->notification->__getInformation();
	}
	
	//Funcion para obtener la informacion
	function __getInformation() {
		//Llama el metodo generico
		parent::__getInformation();
		//Verifica la informacion
		if($this->nerror > 0) {
			//Asigna el error
			$this->error = $_SESSION["NOT_REGISTERED"];
			$this->nerror = 20;
		}
		else {
			//Asigna los otros valores
			$this->setNotification($this->NOTIFICATION_ID);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}
	
	
	function showAttachmentSummary() {
		//Arma la sentencia SQL
		$this->sql = "SELECT COUNT(ID) FROM $this->table  WHERE NOTIFICATION_ID = " . 
					$this->_checkDataType("NOTIFICATION_ID") . " AND IS_BLOCKED = FALSE";
	    //Variable a retornar
	    $result = $_SESSION["NO_ATTACHMENT"];
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro existe
        if($row) {
			if($row[0] > 0) {
				$result = "<span><i class=\"fa fa-paperclip\"></i> " . str_replace("{0}",$row[0],$_SESSION["QTY_ATTACHMENT"]) . " </span>
							<a href=\"#\" onclick=\"downloadAll('$this->NOTIFICATION_ID');\">" . $_SESSION["DOWNLOAD_ATTACHMENTS"] . "</a> |
							<a href=\"#\"  onclick=\"viewAll('$this->NOTIFICATION_ID');\">" . $_SESSION["VIEW_ALL_ATTACHMENTS"] . "</a>";
			}
        }
        //Retorna
        return $result;
		
	}
	
	function showAttachments() {
		//Arma la sentencia SQL
		$this->sql = "SELECT ID, DESCRIPTION, FILENAME, FILESIZE, FILEEXTENSION FROM $this->table WHERE NOTIFICATION_ID = " . 
					$this->_checkDataType("NOTIFICATION_ID") . " AND IS_BLOCKED = FALSE ORDER BY REGISTERED_ON";
		//Genera el resultado
		$result = "<ul>\n";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$size = intval($row[3] / 1024);
			$result .= "<li>
							<a href=\"#\" class=\"atch-thumb\"><img src=\"images/filetypes/" . $row[4] . ".png\" alt=\"filetype\" /></a>
							<div class=\"file-name\">$row[2]</div>
							<span>$size KB</span>
							<div class=\"links\">
								<a href=\"#\">" . $_SESSION["VIEW_ATTACHMENT"] . "</a> -
								<a href=\"#\">" . $_SESSION["DOWNLOAD_ATTACHMENT"] . "</a>
							</div>
						</li>\n";
		}
		//Verifica si hay resultado
		if($result == "<ul>\n")
			$result = "";
		else
			$result .= "</ul>\n";
		//Retorna
		return $result;
	}
}
	/*
	
		**** UPLOAD FILE ****
		$name = $dbLink->real_escape_string($_FILES['uploaded_file']['name']);
        $mime = $dbLink->real_escape_string($_FILES['uploaded_file']['type']);
        $data = $dbLink->real_escape_string(file_get_contents($_FILES  ['uploaded_file']['tmp_name']));
        $size = intval($_FILES['uploaded_file']['size']);

	
		**** DOWNLOAD FILE ****
        if($result) {
            // Make sure the result is valid
            if($result->num_rows == 1) {
            // Get the row
                $row = mysqli_fetch_assoc($result);
 
                // Print headers
                header("Content-Type: ". $row['mime']);
                header("Content-Length: ". $row['size']);
                header("Content-Disposition: attachment; filename=". $row['name']);
 
                // Print data
                echo $row['data'];
            }
            else {
                echo 'Error! No image exists with that ID.';
            }
 
            // Free the mysqli resources
            @mysqli_free_result($result);
        }
        else {
            echo "Error! Query failed: <pre>{$dbLink->error}</pre>";
        }
	*/
?>