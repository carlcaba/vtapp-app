<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("access.php");
require_once("client.php");
require_once("partner.php");

class external_session extends table {
	var $access;
	var $client;
	var $partner;
	var $view;
	
	//Constructor
	function __constructor($external_session = "") {
		$this->external_session($external_session);
	}
	
	//Constructor anterior
	function external_session ($external_session  = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_EXTERNAL_SESSION");
		//Inicializa los atributos
		$this->ID = $external_session == "" ? "UUID()" : $external_session;
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->access = new access();
		$this->client = new client();
		$this->partner = new partner();
		$this->view = "VIE_EXTERNAL_SESSION_SUMMARY";		
	}
	
	//Funcion para Set el acceso
	function setAccess($value) {
		//Asigna la informacion
		$this->access->ID = $value;
		//Verifica la informacion
		$this->access->__getInformation();
		//Si no hubo error
		if($this->access->nerror == 0) {
			//Asigna el valor
			$this->ACCESS_ID = $value;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->ACCESS_ID = 0;
			//Genera error
			$this->nerror = 20;
			$this->error = $_SESSION["NOT_REGISTERED"];
		}
	}
	
	//Funcion para Get el acceso
	function getAccess() {
		//Asigna el valor del tipo
		//Busca la informacion
		$this->ACCESS_ID = $this->access->ID;
		$this->access->__getInformation();
	}

	//Funcion para Set el cliente
	function setClient($value) {
		if($value != "") {
			//Asigna la informacion
			$this->client->ID = $value;
			//Verifica la informacion
			$this->client->__getInformation();
			//Si no hubo error
			if($this->client->nerror == 0) {
				//Asigna el valor
				$this->CLIENT_ID = $value;
				//Genera error
				$this->nerror = 0;
				$this->error = "";
			}
			else {
				//Asigna valor por defecto
				$this->CLIENT_ID = "";
				//Genera error
				$this->nerror = 20;
				$this->error = $_SESSION["NOT_REGISTERED"];
			}
		}
	}
	
	//Funcion para Get el cliente
	function getClient() {
		if($this->client->ID != "") {
			//Asigna el valor del cliente
			$this->CLIENT_ID = $this->client->ID;
			//Busca la informacion
			$this->client->__getInformation();
		}
	}

	//Funcion para Set el aliado
	function setPartner($value) {
		if($value != "") {
			//Asigna la informacion
			$this->partner->ID = $value;
			//Verifica la informacion
			$this->partner->__getInformation();
			//Si no hubo error
			if($this->partner->nerror == 0) {
				//Asigna el valor
				$this->PARTNER_ID = $value;
				//Genera error
				$this->nerror = 0;
				$this->error = "";
			}
			else {
				//Asigna valor por defecto
				$this->PARTNER_ID = "";
				//Genera error
				$this->nerror = 20;
				$this->error = $_SESSION["NOT_REGISTERED"];
			}
		}
	}
	
	//Funcion para Get el aliado
	function getPartner() {
		if($this->partner->ID != "") {
			//Asigna el valor del cliente
			$this->PARTNER_ID = $this->partner->ID;
			//Busca la informacion
			$this->partner->__getInformation();
		}
	}
	
	//Funcion para obtener la informacion del cupo
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
			//Asigna la informacion
			$this->setAccess($this->ACCESS_ID);
			$this->setClient($this->CLIENT_ID);
			$this->setPartner($this->PARTNER_ID);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}		
	
}
?>