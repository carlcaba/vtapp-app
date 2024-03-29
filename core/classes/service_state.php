<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");

class service_state extends table {
	var $resources;
	var $view;
	var $ceroState;
	var $dueState;
	
	//Constructor
	function __constructor($service_state = "") {
		$this->service_state($service_state);
	}
	
	//Constructor anterior
	function service_state($service_state = '') {
		//Llamado al constructor padre
		parent::table("TBL_SERVICE_STATE");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->RESOURCE_NAME = $service_state;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
		$this->view = "VIE_SERVICE_STATE_SUMMARY";
		$this->ceroState = "SERVICE_STATE_0";
		$this->dueState = 16;
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
	
	//Funcion que busca el nombre del estado
	function getResourceById($id) {
        //Lenguaje establecido
        $lang = $_SESSION["LANGUAGE"];
	    //Arma la sentencia SQL
        $this->sql = "SELECT R.RESOURCE_TEXT FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
            "ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE AND A.ID = '$id'";
		_error_log($this->sql);
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
        $this->sql = "SELECT A.ID FROM $this->table A WHERE A.RESOURCE_NAME = '$resource' AND IS_BLOCKED = FALSE LIMIT 1";
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
	function getIdByStep($step) {
        //Lenguaje establecido
        $lang = $_SESSION["LANGUAGE"];
	    //Arma la sentencia SQL
        $this->sql = "SELECT A.ID FROM $this->table A WHERE A.STEP_ID = $step AND IS_BLOCKED = FALSE LIMIT 1";
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
	
	//Funcion que obtiene los estados
	function getArray() {
		//Arma la sentencia SQL
		$this->sql = "SELECT SERVICE_STATE_ID FROM $this->view " .
					"WHERE IS_BLOCKED = FALSE ORDER BY ID_STATE";
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			array_push($return, $row[0]);
		}
		//Retorna
		return $return;
		
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
    function getInformationByOtherInfo($value = "", $field = "SERVICE_STATE_NAME", $lang = 0) {
		if($lang == 0)
			$lang = $_SESSION["LANGUAGE"]; 			
        //Arma la sentencia SQL
        $this->sql = "SELECT SERVICE_STATE_ID FROM $this->view WHERE $field = '$value' AND LANGUAGE_ID = $lang AND IS_BLOCKED = FALSE";
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

	//Funcion para buscar el siguiente estado
	function getNextState($lang = 0, $jump = 0) {
		if($lang == 0)
			$lang = $_SESSION["LANGUAGE"];
		//Busca la informacion actual
		$this->__getInformation();
		//Obtiene el valor actual
		$actual = intval($this->_checkDataType("STEP_ID"));
		//Verifica si hay algun salto
		if($jump > 0)
			$actual+=$jump;
		else 
			$actual++; 
		//Arma la sentencia SQL
		$this->sql = "SELECT ID FROM $this->table WHERE STEP_ID = $actual AND IS_BLOCKED = FALSE LIMIT 1";
		_error_log("Control get Next state ", $this->sql);
        //Obtiene los resultados
        $row = $this->__getData();
		//Retorno
		$return = "";
        //Registro no existe
        if($row) {
            //Asigna el ID
            $return = $row[0];
        }
		//Retorna
		return $return;
	}

	//Funcion para buscar el primer estado
	function _getFirstState() {
		if($lang == 0)
			$lang = $_SESSION["LANGUAGE"]; 			
        //Arma la sentencia SQL
        $this->sql = "SELECT RESOURCE_NAME FROM $this->view WHERE ID_STATE = 1 AND LANGUAGE_ID = $lang AND IS_BLOCKED = FALSE";
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if(!$row) {
			return $this->ceroState;
        }
        else {
            return $row[0];
        }
	}

	//Funcion para buscar el estado vencido
	function getDueState($step = false) {
		$field = "ID";
		if($step)
			$field = "STEP_ID";
        //Arma la sentencia SQL
        $this->sql = "SELECT $field FROM $this->table WHERE IS_BLOCKED = FALSE ORDER BY STEP_ID DESC LIMIT 1";
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if(!$row) {
			return $this->dueState;
        }
        else {
            return $row[0];
        }
	}

	
	//Funcion para buscar el primer estado
	function getFirstState($second = false) {
		//Asigna el valor
		//$this->RESOURCE_NAME = $second ? $this->_getFirstState() : $this->ceroState;
		$this->STEP_ID = $second ? 1 : 0;
        //Arma la sentencia SQL
        //$this->sql = "SELECT ID FROM $this->table WHERE RESOURCE_NAME = " . $this->_checkDataType("RESOURCE_NAME");
		$this->sql = "SELECT ID FROM $this->table WHERE STEP_ID = " . $this->_checkDataType("STEP_ID") . " AND IS_BLOCKED = FALSE";
        //Obtiene los resultados
        $row = $this->__getData();
		//Retorno
		$return = "";
        //Registro no existe
        if($row) {
            //Asigna el ID
            $return = $row[0];
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
		$this->sql = "SELECT SERVICE_STATE_ID, SERVICE_STATE_NAME, ID_STATE, LANGUAGE_ID FROM $this->view " .
					"WHERE LANGUAGE_ID = $lang";
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("id" => $row[0],
							"service_state" => $row[1],
							"language" => $row[2],
							"order" => $row[3]);
			array_push($return, $data);
		}
		//Retorna
		return $return;
	}	
	
}

?>