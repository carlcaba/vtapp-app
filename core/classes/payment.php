<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");
require_once("service.php");
require_once("client.php");
require_once("payment_type.php");
require_once("payment_state.php");

class payment extends table {
	var $service;
	var $client;
	var $type;
	var $state;
	var $view;
	
	//Constructor
	function __constructor($payment = "") {
		$this->payment($payment);
	}
	
	//Constructor anterior
	function payment ($payment  = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_PAYMENT");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->REFERENCE_ID = $payment;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->type = new payment_type();
		$this->client = new client();
		$this->service = new service();
		$this->state = new payment_state();
		$this->view = "VIE_PAYMENT_SUMMARY";		
	}
	
	//Funcion para Set el tipo
	function setType($value) {
		//Asigna la informacion
		$this->type->ID = $value;
		//Verifica la informacion
		$this->type->__getInformation();
		//Si no hubo error
		if($this->type->nerror == 0) {
			//Asigna el valor
			$this->PAYMENT_TYPE_ID = $value;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->PAYMENT_TYPE_ID = 0;
			//Genera error
			$this->nerror = 20;
			$this->error = $_SESSION["NOT_REGISTERED"];
		}
	}
	
	//Funcion para Get el tipo
	function getType() {
		//Asigna el valor del tipo
		$this->PAYMENT_TYPE_ID = $this->type->ID;
		//Busca la informacion
		$this->type->__getInformation();
	}

	//Funcion para Set el estado
	function setState($value) {
		//Asigna la informacion
		$this->state->ID = $value;
		//Verifica la informacion
		$this->state->__getInformation();
		//Si no hubo error
		if($this->state->nerror == 0) {
			//Asigna el valor
			$this->PAYMENT_STATE_ID = $value;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->PAYMENT_STATE_ID = 0;
			//Genera error
			$this->nerror = 20;
			$this->error = $_SESSION["NOT_REGISTERED"];
		}
	}
	
	//Funcion para Get el estado
	function getState() {
		//Asigna el valor del tipo
		$this->PAYMENT_STATE_ID = $this->state->ID;
		//Busca la informacion
		$this->state->__getInformation();
	}


	//Funcion para Set el cliente
	function setClient($value) {
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
			$this->CLIENT_ID = "UUID()";
			//Genera error
			$this->nerror = 20;
			$this->error = $_SESSION["NOT_REGISTERED"];
		}
	}
	
	//Funcion para Get el cliente
	function getClient() {
		//Asigna el valor del cliente
		$this->CLIENT_ID = $this->client->ID;
		//Busca la informacion
		$this->client->__getInformation();
	}

	//Funcion para Set la referencia
	function setReference($value) {
		//Asigna la informacion
		$this->service->ID = $value;
		//Verifica la informacion
		$this->service->__getInformation();
		//Si no hubo error
		if($this->service->nerror == 0) {
			//Asigna el valor
			$this->REFERENCE_ID = $value;
			//Genera error
			$this->nerror = 0;
			$this->error = "";
		}
		else {
			//Asigna valor por defecto
			$this->REFERENCE_ID = "UUID()";
			//Genera error
			$this->nerror = 20;
			$this->error = $_SESSION["NOT_REGISTERED"];
		}
	}
	
	//Funcion para Get el servicio
	function getReference() {
		//Asigna el valor del servicio
		$this->REFERENCE_ID = $this->service->ID;
		//Busca la informacion
		$this->service->__getInformation();
	}
	
	//Funcion para obtener la informacion del pago
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
			$this->setType($this->PAYMENT_TYPE_ID);
			$this->setState($this->PAYMENT_STATE_ID);
			$this->setClient($this->CLIENT_ID);
			$this->setService($this->REFERENCE_ID);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}		
	
	//Funcion para buscar un empleado por otra informacion
    function getInformationByOtherInfo($field = "CLIENT_ID") {
        //Arma la sentencia SQL
        $this->sql = "SELECT ID FROM $this->table WHERE $field = " . $this->_checkDataType($field);
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

	function dataForm($action, $tabs = 5) {
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array("", "", 
								"", "", "",
								"", "", "",
								"", "", 
								"", "", "");
			$actiontext = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newQuota.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "disabled", 
								"", "", "", 
								"", "", "",
								"", "", 
								"", "", "",
								"disabled", "disabled", "disabled", "disabled");
			$actiontext = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editQuota.php";
		}
		else {
			$readonly = array("disabled", "disabled", 
							"disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled", 
							"disabled", "disabled", 
							"disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled", "disabled");
			$actiontext = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteQuota.php";
		}

		//Variable a regresar
		$return = array("tabs" => $stabs,
						"readonly" => $readonly,
						"actiontext" => $actiontext,
						"link" => $link,
						"showvalue" => true);
		//Retorna
		return $return;
	}
		
}
?>