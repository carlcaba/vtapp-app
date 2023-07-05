<?

// LOGICA ESTUDIO 2016

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");

class directaccess extends table {
	var $resources;
	
	//Constructor
	function __constructor() {
		$this->directaccess();
	}
	
	//Constructor anterior
	function directaccess() {
		//Llamado al constructor padre
		parent::table("TBL_SYSTEM_DIRECT_ACCESS");
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relacion con otras clases
		$this->resources = new resources();
	}
	
	//Funcion para mostrar los accesos directos
	function getOptionList() {
		//Arma la sentencia SQL
		$this->sql = "SELECT D.ID, R.RESOURCE_TEXT, D.LINK, D.ICON, D.ORDER_ID, D.IS_BLOCKED " .
					"FROM " . $this->table . " D INNER JOIN " . $this->resources->table . " R ON (R.RESOURCE_NAME = D.RESOURCE_NAME) " .
					"WHERE R.LANGUAGE_ID = " . $_SESSION["LANGUAGE"] . " AND D.IS_BLOCKED = FALSE ORDER BY D.ORDER_ID";
		//Regresa
		return $this->__getAllData();
	}
	
}

?>