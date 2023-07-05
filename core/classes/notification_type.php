<?

// LOGICA ESTUDIO 2016

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");

class notification_type extends table {
	var $resources;
	
	//Constructor
	function __constructor($type = "") {
		$this->notification_type($type);
	}
	
	//Constructor anterior
	function notification_type($type = '') {
		//Llamado al constructor padre
		parent::table("TBL_SYSTEM_NOTIFICATION_TYPE");
		$this->RESOURCE_NAME = $type;
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relacion con otras clases
		$this->resources = new resources();
	}

	//Funcion que muestra el texto del resource
	function getResource() {
        //Lenguaje establecido
        $lang = $_SESSION["LANGUAGE"];
	    //Arma la sentencia SQL
        $this->sql = "SELECT R.RESOURCE_TEXT FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
            "ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.ID = " . $this->_checkDataType("ID");
        //Variable a retornar
        $result = "";
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if($row) {
            $result = $row[0];
        }
        //Retorna
        return $result;
	}
	
	//Funcion que despliega los valores en un option
	function showOptionList($type = "_UNREAD_", $tabs = 8,$selected = 0, $lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT A.ID, R.RESOURCE_TEXT FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
				"ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE AND A.RESOURCE_NAME LIKE '%$type%' ORDER BY A.ID";
		//Variable a retornar
		$return = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Si la opcion se encuentra seleccionada
			if($row[0] == $selected)
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' selected>$row[1]</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "'>$row[1]</option>\n";
		}
		//Retorna
		return $return;
	}
	
	//Funcion para cambiar el tipo de notificacion
	function changeType() {
		//Verifica si debe cambiarlo
		if(strpos($this->RESOURCE_NAME,"_UNREAD_") !== false) {
			//Arma la sentencia sql
			$this->sql = "SELECT ID FROM $this->table WHERE RESOURCE_NAME = '" . str_replace("_UNREAD_","_READ_",$this->RESOURCE_NAME) . "' LIMIT 1";
			//Obtiene los resultados
			$row = $this->__getData();
			//Registro no existe
			if($row) {
				$this->ID = $row[0];
				$this->__getInformation();
			}
		}
	}
	
	//Funcion para buscar un tipo de notificacion por otra informacion
    function getInformationByOtherInfo($field = "TEXT_TYPE") {
        //Arma la sentencia SQL
        $this->sql = "SELECT ID FROM $this->table WHERE $field = " . $this->_checkDataType($field);
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if(!$row) {
            //Asigna el ID
            $this->ID = "UUID()";
            //Genera el error
            $this->nerror = 10;
            $this->error = $_SESSION["NOT_REGISTERED"];
        }
        else {
            //Asigna el ID
            $this->ID = $row[0];
            //Llama el metodo
            $this->__getInformation();
            //Limpia el error
            $this->nerror = 0;
            $this->error = "";
        }
    }
	
	//Funcion que despliega los valores para el webservice
	function listData($lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT A.ID, R.RESOURCE_TEXT, R.LANGUAGE_ID, A.TEXT_TYPE, A.ICON FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
				"ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE ORDER BY A.ID";
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("id" => $row[0],
							"notification_type" => $row[1],
							"language" => $row[2],
							"text_type" => $row[3],
							"icon" => $row[4]);
			array_push($return, $data);
		}
		//Retorna
		return $return;
	}	
	
}

?>