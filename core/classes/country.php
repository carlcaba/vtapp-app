<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");

class country extends table {

	//Constructor de la clase
	function __constructor($pais = "") {
		$this->country($pais);
	}
	
	//Constructor anterior
	function country($pais = "") {
		//Llamado al constructor padre
		parent::tabla("TBL_SYSTEM_COUNTRY");
		//Valores por defecto
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION["vtappcorp_userid"];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
	}

    //Funcion que despliega los valores en un option
    function showOptionList($tabs = 8,$selected = 0) {
        //Arma la cadena con los tabs requeridos
        for($i=0;$i<$tabs;$i++)
            $stabs .= "\t";
        //Arma la sentencia SQL
        $this->sql = "SELECT ID, COUNTRY FROM " . $this->table . " ORDER BY COUNTRY";
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

	//Funcion para listar los departamentos disponibles
	function showList($selected, $tabs = 8) {
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			@$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT ID, COUNTRY FROM " . $this->table . " ORDER BY COUNTRY";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			//Verifica que no este seleccionado
			if($row[0]==$selected)
				//Ajusta al diseño segun GUI
				print "$stabs<option value='$row[0]' selected>$row[1]</option>\n";
			else
				//Ajusta al diseño segun GUI
				print "$stabs<option value='$row[0]'>$row[1]</option>\n";
		}
	}
	
    //Funcion para generar el JSON 
    function showListJSON() {
		//Arma la sentencia SQL
        $this->sql = "SELECT ID, COUNTRY FROM " . $this->table . " ORDER BY COUNTRY";
		//Variable a retornar
		$return = array(array("text" => $_SESSION["SELECT_OPTION"],
						"value" => ""));
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("text" => $row[1],
							"value" => $row[0]);
			array_push($return,$data);
		}
		//Retorna
		return $return;
    }
	
}	

?>