<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");

class journey extends table {
	var $resources;
	var $view;
	
	//Constructor
	function __constructor($journey = "") {
		$this->journey($journey);
	}
	
	//Constructor anterior
	function journey($journey = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_JOURNEY");
		//Inicializa los atributos
		$this->ID = 0;
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->RESOURCE_NAME = $journey;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
		$this->view = "VIE_JOURNEY_SUMMARY";
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
	
	//Funcion que busca el nombre de la jornada
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
	
	//Funcion que busca el nombre del estado
	function getIdByResource($resource) {
        //Lenguaje establecido
        $lang = $_SESSION["LANGUAGE"];
	    //Arma la sentencia SQL
        $this->sql = "SELECT A.ID FROM $this->table A WHERE A.RESOURCE_NAME = '$resource'";
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

	//Funcion para obtener la informacion del estado
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
	
	//Funcion para buscar un estado por otra informacion
    function getInformationByOtherInfo($value = "", $field = "JOURNEY_NAME", $lang = 0) {
		if($lang == 0)
			$lang = $_SESSION["LANGUAGE"]; 			
        //Arma la sentencia SQL
        $this->sql = "SELECT JOURNEY_ID FROM $this->view WHERE $field = '$value' AND LANGUAGE_ID = $lang";
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if(!$row) {
            //Asigna el ID
            $this->ID = 0;
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

	//Funcion que despliega los valores en una categoria
	function showOptionList($tabs = 8,$selected = "", $lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT JOURNEY_ID, JOURNEY_NAME FROM $this->view WHERE IS_BLOCKED = FALSE AND LANGUAGE_ID = $lang ORDER BY 2"; 
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
				$return .= "$stabs<option value='" . $row[0] . "' selected>" . $row[1] . "</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "'>" . $row[1] . "</option>\n";
		}
		//Retorna
		return $return;
	}
}

?>