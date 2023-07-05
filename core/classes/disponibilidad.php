<?

// LOGICA ESTUDIO 2016

//Incluye las clases dependientes
require_once("tabla.php");
require_once("cancha.php");

class disponibilidad extends tabla {
	var $cancha;
	var $view;
	
	//Constructor
	function disponibilidad() {
		//Llamado al constructor padre
		parent::table("TBL_DISPONIBILIDAD");
		//Inicializa los atributos
		$this->FECHA_REGISTRO = "NOW()";
		$this->USUARIO_REGISTRO = $_SESSION['metrofutbol_userid'];
		$this->FECHA_ASIGNADO = "NULL";
		$this->USUARIO_ASIGNA = "NULL";
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->cancha = new cancha();
		//Define la vista
		$this->view = "VIE_DISPONIBILIDAD";
	}

	//Funcion para Set el cancha
	function setCancha($cancha) {
		//Asigna la informacion
		$this->cancha->ID = $cancha;
		//Verifica la informacion
		$this->cancha->__getInformation();
		//Si no hubo error
		if($this->cancha->nerror == 0) {
			//Asigna el valor
			$this->ID_CANCHA = $cancha;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->ID_CANCHA = 0;
			//Genera error
			$this->nerror = 20;
			$this->error = "cancha no registrado!";
		}
	}
	
	//Funcion para Get el cancha
	function getCancha() {
		//Asigna el valor del sistema
		$this->ID_CANCHA = $this->cancha->ID;
		//Busca la informacion
		$this->cancha->__getInformation();
	}
	
	//Cuenta el total de canchas disponibles para esta semana
	function getTotalAvailableScenario() {
		//Variable a retornar
		$return = 0;
		try {
			//Arma la sentencia SQL
			$this->sql = "SELECT COUNT(ID) FROM " . $this->table . " WHERE FECHA_ASIGNADO IS NULL AND " .
						"FECHA_INICIO <= DATE_ADD(CURDATE(), INTERVAL (8 - IF(DAYOFWEEK(CURDATE()) = 1, 7, DAYOFWEEK(CURDATE()))) DAY) AND " .
						"FECHA_FIN >= DATE_ADD(CURDATE(), INTERVAL (7 - IF(DAYOFWEEK(CURDATE()) = 1, 6, DAYOFWEEK(CURDATE()))) DAY)";
			//Obtiene los resultados
			$row = $this->__getData();
			//Valida el resultado
			if($row) {
				$return = $row[0];
			}
		}
		catch (Exception $e) {
			$this->error = $e->getMessage();
			$this->nerror = 40;
		}
		return $return;
	}
	
	//Verifica la disponibilidad de las canchas
	function getAvailableScenario() {
		//Variable a retornar
		$result = array();
		try {
			//Arma la sentencia SQL
			$this->sql = "SELECT DISTINCT ID_CANCHA, ID_ESCENARIO, NOMBRE_ESCENARIO, CANCHA " .
					"FROM $this->view WHERE ID_ESCENARIO = " . $this->cancha->escenario->_checkDataType("ID") .
					" AND FECHA_INICIO = DATE(" . $this->_checkDataType("FECHA_INICIO") . ")";
			foreach($this->__getAllData() as $row) {
				array_push($result, array("fieldId" => $row[0],
											"field" => $row[3]));
			}
		}
		catch (Exception $e) {
			$this->error = $e->getMessage();
			$this->nerror = 40;
		}
		if(count($result) > 0) {
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			$this->nerror = 10;
			$this->error = "No hay escenarios disponibles para el torneo!";
		}
		return $result;
	}

	//Verifica la disponibilidad de las canchas
	function getAvailableTime() {
		//Variable a retornar
		$result = array();
		try {
			//Arma la sentencia SQL
			$this->sql = "SELECT ID_DISPONIBILIDAD, ID_CANCHA, ID_ESCENARIO, FECHA_INICIO, HORA_INICIO, NOMBRE_ESCENARIO, CANCHA " .
					"FROM $this->view WHERE ID_CANCHA = " . $this->cancha->_checkDataType("ID") .
					" AND FECHA_INICIO = DATE(" . $this->_checkDataType("FECHA_INICIO") . ")";
			foreach($this->__getAllData() as $row) {
				array_push($result, array("id" => $row[0],
											"fieldId" => $row[1],
											"start" => $row[3],
											"hourstart" => $row[4],
											"scenario" => $row[5],
											"field" => $row[6]));
			}
		}
		catch (Exception $e) {
			$this->error = $e->getMessage();
			$this->nerror = 40;
		}
		if(count($result) > 0) {
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			$this->nerror = 10;
			$this->error = "No hay horarios disponibles para este escenario!";
		}
		return $result;
	}
}

?>