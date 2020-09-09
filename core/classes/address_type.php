<?
// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");

class address_type extends table {
	//Relacion otras clases
	var $view;
	
	//Constructor de la clase
	function __constructor() {
		$this->address_type();
	}
	
	//Constructor anterior
	function address_type() {
		//Llamado al constructor padre
		parent::tabla("TBL_SYSTEM_ADDRESS_TYPE");
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		$this->view = "VIE_ADDRESS_TYPE_SUMMARY";		
	}
	

	//Funcion para obtener la informacion del municipio
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
	
	//Funcion para listar los tipos disponibles
	function showOptionList($tabs = 8,$selected = 0) {		
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			@$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT ABBREVIATION, NAME FROM " . $this->table . " WHERE IS_BLOCKED = FALSE ORDER BY NAME";
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