<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");

class ws_query extends table {
	
	//Constructor
	function __constructor($wsqy = "") {
		$this->ws_query();
	}
	
	//Constructor anterior
	function ws_query($wsqy = "") {
		//Llamado al constructor padre
		parent::tabla("TBL_SYSTEM_WS_QUERY");
	}

	//Modifica el resultado
	function updateResult() {
		$this->RETURNED = str_replace("'","\'",$this->RETURNED);
		$this->sql = "UPDATE $this->table SET RETURNED = " . $this->_checkDataType("RETURNED") . 
						", MODIFIED_ON = " . $this->_checkDataType("MODIFIED_ON") . 
						", MODIFIED_BY = " . $this->_checkDataType("MODIFIED_BY") .
						" WHERE ID = " . $this->_checkDataType("ID");
		$this->executeQuery();
		
		if($this->nerror > 0) {
			_error_log("Error updating ws_query: " . $this->error, $this->sql);
		}
	}
	
}

?>