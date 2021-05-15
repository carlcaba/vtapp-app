<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");

class payment_state extends table {
	var $resources;
	var $view;
	
	//Constructor
	function __constructor($payment_state = "") {
		$this->payment_state($payment_state);
	}
	
	//Constructor anterior
	function payment_state($payment_state = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_PAYMENT_STATE");
		//Inicializa los atributos
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->RESOURCE_NAME = $payment_state;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
		//Vista
		$this->view = "VIE_PAYMENT_STATE_SUMMARY";
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
	
	//Funcion que busca el nombre del tipo de pago
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
	
	//Funcion para obtener la informacion del tipo de pago
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
		$this->sql = "SELECT A.ID, R.RESOURCE_TEXT FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
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
				$return .= "$stabs<option value='" . $row[0] . "' selected>" . $row[1] . "</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "'>" . $row[1] . "</option>\n";
		}
		//Retorna
		return $return;
	}

	//Funcion que despliega los valores para el webservice
	function listData($lang = 0, $extra = "") {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		$label = "payment_type";
		$cont = 0;
		//Arma la sentencia SQL
		$this->sql = "SELECT A.ID, R.RESOURCE_TEXT, R.LANGUAGE_ID FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
				"ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE";

		//Verifica si es informacion extra
		if($extra == "si") {
			//Arma la sentencia SQL
			$this->sql .= " AND A.RESOURCE_NAME LIKE 'PAYMENT_STATE_99%' AND LENGTH(A.RESOURCE_NAME) > 16 ORDER BY ID";
			$label = "service_issue";
		}
		else if($extra == "ss") {
			//Arma la sentencia SQL
			$this->sql .= " AND A.RESOURCE_NAME LIKE 'PAYMENT_STATE_89%' AND LENGTH(A.RESOURCE_NAME) > 16 ORDER BY ID";
			$label = "service_state_update";
			$cont = 2;
		}
		
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("id" => (($extra == "ss") ? $cont++ : $row[0]),
							$label => $row[1],
							"language" => $row[2]);
			array_push($return, $data);
		}
		//Retorna
		return $return;
	}

	//Funcion que busca POR el nombre del tipo de pago
	function getStateByName($name) {
        //Lenguaje establecido
        $lang = $_SESSION["LANGUAGE"];
	    //Arma la sentencia SQL
        $this->sql = "SELECT PAYMENT_STATE_ID FROM $this->view WHERE PAYMENT_STATE_NAME = '$name' AND LANGUAGE_ID = $lang";
        //Variable a retornar
        $result = 0;
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if($row) {
            $result = $row[0];
        }
        //Retorna
        return $result;
	}


}

?>