<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");

class rate extends table {
	
	//Constructor
	function __constructor($rate = "") {
		$this->rate($rate);
	}
	
	//Constructor anterior
	function rate ($rate  = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_SYSTEM_RATE");
		//Inicializa los atributos
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
	}

	//Funcion para obtener la informacion de la tarifa
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

	//Funcion para buscar una tarifa por distancia
    function getValueByDistance($distance) {
        //Arma la sentencia SQL
        $this->sql = "SELECT ID FROM $this->table WHERE $distance BETWEEN DISTANCE_INITIAL AND DISTANCE_FINAL AND IS_BLOCKED = FALSE";
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if(!$row) {
			$this->getValueByDistance(50000);
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
	
	
}

?>
