<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");

class service_state extends table {
	var $resources;
	var $view;
	
	//Constructor
	function __constructor($service_state = "") {
		$this->service_state($service_state);
	}
	
	//Constructor anterior
	function service_state($service_state = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_SERVICE_STATE");
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
    function getInformationByOtherInfo($value = "", $field = "SERVICE_STATE_NAME", $lang = 0) {
		if($lang == 0)
			$lang = $_SESSION["LANGUAGE"]; 			
        //Arma la sentencia SQL
        $this->sql = "SELECT SERVICE_STATE_ID FROM $this->view WHERE $field = '$value' AND LANGUAGE_ID = $lang";
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
	function getNextState($lang = 0) {
		if($lang == 0)
			$lang = $_SESSION["LANGUAGE"];
		//Busca la informacion actual
		$this->__getInformation();
		//Separa los valores
		$name = explode("_", $this->RESOURCE_NAME);
		//Genera el siguiente valores
		$number = intval(end($name));
		//Ajusta el nombre
		$name = array_slice($name,0,2);
		//Agrega el nuevo numero
		array_push($name,(++$number));
		//Asigna el valor
		$this->RESOURCE_NAME = implode("_",$name);
        //Arma la sentencia SQL
        $this->sql = "SELECT ID FROM $this->table WHERE RESOURCE_NAME = " . $this->_checkDataType("RESOURCE_NAME");
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
	function getFirstState($second = false) {
		//Asigna el valor
		$this->RESOURCE_NAME = $second ? "SERVICE_STATE_1" : "SERVICE_STATE_0";
        //Arma la sentencia SQL
        $this->sql = "SELECT ID FROM $this->table WHERE RESOURCE_NAME = " . $this->_checkDataType("RESOURCE_NAME");
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
		$this->sql = "SELECT A.SERVICE_STATE_ID, A.SERVICE_STATE_NAME, ID_STATE, LANGUAGE_ID FROM $this->view " .
					"WHERE A.LANGUAGE_ID = $lang";
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