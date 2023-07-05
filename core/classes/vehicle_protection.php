<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");

class vehicle_protection extends table {
	var $resources;
	
	//Constructor
	function __constructor($vehicle_protection = "") {
		$this->vehicle_protection($vehicle_protection);
	}
	
	//Constructor anterior
	function vehicle_protection($vehicle_protection = '') {
		//Llamado al constructor padre
		parent::table("TBL_SYSTEM_VEHICLE_PROTECTION");
		//Inicializa los atributos
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->RESOURCE_NAME = $vehicle_protection;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
	}

	//Funcion que muestra el texto del resource
	function getResource() {
        //Lenguaje establecido
        $lang = $_SESSION["LANGUAGE"];
	    //Arma la sentencia SQL
        $this->sql = "SELECT R.RESOURCE_TEXT FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
            "ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE AND A.ID = " . $this->_checkDataType("ID");
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
	
	//Funcion que busca el nombre del vehiculo
	function getResourceById($id) {
        //Lenguaje establecido
        $lang = $_SESSION["LANGUAGE"];
	    //Arma la sentencia SQL
        $this->sql = "SELECT R.RESOURCE_TEXT FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
            "ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE AND A.ID = $id";
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
	
	//Funcion para obtener la informacion del vehiculo
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
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}

	//Funcion que despliega los valores en un option
	function showOptionList($tabs = 8,$selected = 0, $lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT A.ID, R.RESOURCE_TEXT, A.VEHICLE_TYPE_ID FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
				"ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE";
		//Variable a retornar
		$return = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
			//Si la opcion se encuentra seleccionada
			if($row[0] == $selected)
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' data-vehicletype=\"" . $row[2] . "\" selected>" . $row[1] . "</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' data-vehicletype=\"" . $row[2] . "\">" . $row[1] . "</option>\n";
		}
		//Retorna
		return $return;
	}
	
	//Funcion para buscar un tipo de vehiculo pro nombre
    function getInformationByName($name, $lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
        //Arma la sentencia SQL
		$this->sql = "SELECT A.ID FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
				"ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND R.RESOURCE_TEXT = '$name'";
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
		$this->sql = "SELECT A.ID, R.RESOURCE_TEXT, R.LANGUAGE_ID FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
				"ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE";
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("id" => $row[0],
							"vehicle_protection" => $row[1],
							"language" => $row[2]);
			array_push($return, $data);
		}
		//Retorna
		return $return;
	}	
	
	function dataForm() {
		$this->resources = new resources();
		//Verifica los recursos
		$this->completeResources();
	}
}

?>