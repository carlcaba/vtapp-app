<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");

class delivery_type extends table {
	var $resources;
	var $view;
	
	//Constructor
	function __constructor($delivery_type = "") {
		$this->delivery_type($delivery_type);
	}
	
	//Constructor anterior
	function delivery_type($delivery_type = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_DELIVERY_TYPE");
		//Inicializa los atributos
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->RESOURCE_NAME = $delivery_type;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
		$this->view = "VIE_DELIVERY_TYPE_SUMMARY";
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
	
	//Funcion que busca el nombre del tipo entrega
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
	
	//Funcion para obtener la informacion del tipo entrega
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
	
	//Funcion para buscar un tipo de entrega por otra informacion
    function getInformationByOtherInfo($field = "DELIVERY_TYPE_NAME", $value = "") {
        //Lenguaje establecido
        $lang = $_SESSION["LANGUAGE"];
        //Arma la sentencia SQL
        $this->sql = "SELECT DELIVERY_TYPE_ID FROM $this->view WHERE $field = '$value' AND LANGUAGE_ID = $lang";
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

	//Funcion que despliega los valores en una zona
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
		$this->sql = "SELECT DELIVERY_TYPE_ID, DELIVERY_TYPE_NAME FROM $this->view WHERE LANGUAGE_ID = $lang ORDER BY 2"; 
		//Variable a retornar
		$return = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
			$adds = "data-block=\"false\"";
			//Verifica si es sobre
			if($row[0] == 1) {
				$adds = "data-block=\"true\" data-height=\"28\" data-width=\"22\" data-weight=\"5\" data-length=\"10\"";
			}
			//Si la opcion se encuentra seleccionada
			if($row[0] == $selected)
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' selected $adds>" . $row[1] . "</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' $adds>" . $row[1] . "</option>\n";
		}
		//Retorna
		return $return;
	}

	//Funcion que despliega los valores para el webservice
	function listData($lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT DELIVERY_TYPE_ID, DELIVERY_TYPE_NAME, LANGUAGE_ID FROM $this->view WHERE LANGUAGE_ID = $lang ORDER BY 2"; 
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("id" => $row[0],
							"delivery_type" => $row[1],
							"language" => $row[2]);
			array_push($return, $data);
		}
		//Retorna
		return $return;
	}

}

?>