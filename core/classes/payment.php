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
		$this->IP_CLIENT = $_SERVER["SERVER_ADDR"];
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
	
	//Funcion que retorna el resumen por categoria
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit,$options = "") {
		$fields = ["PAYMENT_ID", "CLIENT_NAME", "DELIVER_ADDRESS", "REQUESTED_BY", "REQUESTED_DATE", "DELIVER_TO", "SERVICE_STATE_NAME", "PRICE", "ICON"];
		//Verifica las opciones
		if($options != "") {
			$sWhere .= " WHERE CLIENT_ID = '$options'";
		}
		if($sWhere != "")
			$sWhere .= " AND SERVICE_ID IS NOT NULL ";
		else
			$sWhere = " WHERE SERVICE_ID IS NOT NULL "; 
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(DISTINCT " . $fields[0] . ") FROM $this->view $sWhere";
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro no existe
        if(!$row) {
            return array();
        }
		$iTotal = $row[0];

		$output = array(
			"recordsTotal" => $iTotal,
			"recordsFiltered" => $iTotal,
			"data" => array(),
			"sql" => "");
		
		//Arma la sentencia SQL
		$this->sql = "SELECT DISTINCT " . str_replace(" , "," ",implode(", ",$aColumnsBD)) . " FROM $this->view $sWhere $sOrder $sLimit";
		$output["sql"] = $this->sql;
		//Recoge los resultados
		foreach($this->__getAllData() as $aRow) {
			//$row = array_fill_keys($aColumnsDB,'');
			$row = array_fill_keys($aColumnsBD,'');
			for($i = 0;$i < count($aColumnsBD);$i++) {
				/*
				if(strpos($aColumnsBD[$i],"_ID") !== false) {
					if($aColumnsBD[$i] == $fields[0]) {
						//Verifica el estado para activar o desactivar
						if($aRow[7])
							$activate = "<button type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["ACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',true,'" . $aRow[1] . "');\"><i class=\"fa fa-unlock\"></i></button>";
						else 
							$activate = "<button type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["DEACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',false,'" . $aRow[1] . "');\"><i class=\"fa fa-lock\"></i></button>";
						
						$view = "<button type=\"button\" class=\"btn btn-info\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $aRow[$i] . "','view');\"><i class=\"fa fa-eye\"></i></button>";
						$edit = "<button type=\"button\" class=\"btn btn-warning\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('" . $aRow[$i] . "','edit');\"><i class=\"fa fa-pen-to-square\"></i></button>";
						$delete = "<button type=\"button\" class=\"btn btn-danger\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('" . $aRow[$i] . "','delete');\"><i class=\"fa fa-trash\"></i></button>";
						if(!$aRow[4])
							$pay = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["PAY"] . "\" onclick=\"payment('" . $aRow[$i] . "','" . $aRow[2] . "');\"><i class=\"fa fa-credit-card-alt\"></i></button>";
						else
							$pay = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["PAYED"] . "\" ><i class=\"fa fa-check-circle\"></i></button>";
						$list = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["LIST_SERVICES"] . "\" onclick=\"list('" . $aRow[$i] . "');\"><i class=\"fa fa-list-alt\"></i></button>";
												
						$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $pay . $list . $view . $edit . $delete . "</div></div>";
						$row[$aColumnsBD[$i]] = $aRow[$i];
						$row[$aColumnsBD[count($aColumnsBD)-1]] = $action;
					}
				}
				else if($aColumnsBD[$i] == "ID") {
					$first = "<input type=\"checkbox\" class=\"flat\" name=\"table_records\" value=\"" . $this->inter->Encriptar($aRow[0]) . "\" data-name=\"$aRow[1]\">";
					$row[$aColumnsBD[$i]] = $first;
				}
				else if($aColumnsBD[$i] == "IS_BLOCKED") {
					$row[$aColumnsBD[$i]] = ($aRow[$i] == "1") ? $_SESSION["MSG_NO"] : $_SESSION["MSG_YES"];
				}
				else if($aColumnsBD[$i] != ' ') {
					$row[$aColumnsBD[$i]] = $aRow[$i];
				}
				*/
				$row[$aColumnsBD[$i]] = $aRow[$i];
			}
			array_push($output['data'],$row);
		}
		return $output;
	}
	
	function makePayment() {
		//Realiza las asignaciones
		$this->setClient($this->CLIENT_ID);
		$this->setState($this->PAYMENT_STATE_ID);
		$this->setType($this->PAYMENT_TYPE_ID);
		$this->setReference($this->REFERENCE_ID);
		
		parent::_add();
		
	}
}
?>