<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");

class quota_type extends table {
	var $resources;
	var $view;
	
	//Constructor
	function __constructor($quota_type = "") {
		$this->quota_type($quota_type);
	}
	
	//Constructor anterior
	function quota_type($quota_type = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_QUOTA_TYPE");
		//Inicializa los atributos
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->RESOURCE_NAME = $quota_type;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
		//Vista
		$this->view = "VIE_QUOTA_TYPE_SUMMARY";
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
		$this->sql = "SELECT A.ID, R.RESOURCE_TEXT, A.AMOUNT, A.IS_MARCO FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
				"ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE";
		//Variable a retornar
		$return = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row[1], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
			//Si la opcion se encuentra seleccionada
			if($row[0] == $selected)
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' data-amount=\"$row[2]\" data-ismarco=\"$row[3]\" selected>" . $row[1] . "</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' data-amount=\"$row[2]\" data-ismarco=\"$row[3]\">" . $row[1] . "</option>\n";
		}
		//Retorna
		return $return;
	}

	//Funcion que despliega los valores en un option
	function showOptionListJson($lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la sentencia SQL
		$this->sql = "SELECT A.ID, R.RESOURCE_TEXT, A.AMOUNT, A.IS_MARCO FROM $this->table A INNER JOIN " . $this->resources->table . " R " .
				"ON (R.RESOURCE_NAME = A.RESOURCE_NAME) WHERE R.LANGUAGE_ID = $lang AND A.IS_BLOCKED = FALSE";
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row[1], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
			$data = array("id" => $row[0],
							"text" => $row[1],
							"amount" => number_format($row[2],2,".",""),
							"ismarco" => $row[3]);
			array_push($return,$data);
		}
		//Retorna
		return $return;
	}
	
	//Funcion que obtiene la cantidad
	function getAmount() {
		$text = $this->getResource();
		if(strpos($text,"Notificaciones") !== false) {
			return intval(str_replace(",","",$text));
		}
		else {
			return intval($this->AMOUNT);
		}			
	}
	
	//Funcion que obtiene la informacion de descuento
	//TRUE: Descontar dinero
	//FALSE: Descontar unidad notificacion
	function discountType() {
		$text = $this->getResource();
		if(strpos($text,"Notificaciones") !== false) {
			return false;
		}
		else {
			return true;
		}			
	}

}

?>