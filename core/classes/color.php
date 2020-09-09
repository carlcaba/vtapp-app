<?

// LOGICA ESTUDIO 2020

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");

class color extends table {
	var $resources;
	var $view;
	var $type;
	
	//Constructor
	function __constructor($type = "") {
		$this->notification($type);
	}
	
	//Constructor anterior
	function color ($color = "") {
		//Llamado al constructor padre
		parent::tabla("TBL_SYSTEM_COLOR");
		//Inicializa los atributos
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
        $this->resources = new resources();
        //Vista
		$this->view = "VIE_SYSTEM_COLOR_SUMMARY";		
	}

	//Funcion para buscar un color por clase
    function getInformationByClassName($class) {
        //Arma la sentencia SQL
        $this->sql = "SELECT ID FROM $this->table WHERE CLASS_NAME = '$class' LIMIT 1";
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if(!$row) {
            //Asigna el ID
            $this->ID = "0";
            //Genera el error
            $this->nerror = 10;
            $this->error = $_SESSION["NOT_REGISTERED"];
        }
        else {
            //Asigna el ID
            $this->ID = $row[0];
            //Llama el metodo generico
            parent::__getInformation();
            //Limpia el error
            $this->nerror = 0;
            $this->error = "";
        }
    }

    //Funcion que despliega los valores en una categoria
    function showOptionList($tabs = 8,$selected = "",$lang = 0) {
        //Arma la cadena con los tabs requeridos
        for($i=0;$i<$tabs;$i++)
            $stabs .= "\t";
        if($lang == 0) {
            //Lenguaje establecido
            $lang = $_SESSION["LANGUAGE"];
        }
        //Arma la sentencia SQL
        $this->sql = "SELECT COLOR_ID, COLOR_NAME, HEXADECIMAL, CLASS_NAME FROM $this->view WHERE LANGUAGE_ID = $lang ORDER BY 2"; 
        //Variable a retornar
        $return = "$stabs<option value=\"\">" . $_SESSION["SELECT_OPTION"] . "</option>\n";
        //Recorre los valores
        foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
            $options = "data-hexadecimal=\"" . $row[2] . "\" data-classname=\"" . $row[3] . "\"";
            //Si la opcion se encuentra seleccionada
            if($row[0] == $selected)
                //Ajusta al diseño segun GUI
                $return .= "$stabs<option value='" . $row[0] . "' selected $options>" . $row[1] . "</option>\n";
            else
                //Ajusta al diseño segun GUI
                $return .= "$stabs<option value='" . $row[0] . "' $options>" . $row[1] . "</option>\n";
        }
        //Retorna
        return $return;
    }

}

?>



