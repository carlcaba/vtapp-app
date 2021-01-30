<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");
require_once("city.php");
require_once("client_payment_type.php");
require_once("client_payment.php");

class client extends table {
	var $resources;
	var $view;
	var $city;
	var $client_type;
	var $client_payment;
	
	//Constructor
	function __constructor($client = "") {
		$this->client($client);
	}
	
	//Constructor anterior
	function client ($client  = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_CLIENT");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->CLIENT_NAME = $client;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->city = new city();
		$this->client_type = new client_payment_type();
		$this->client_payment = new client_payment();
		$this->view = "VIE_CLIENT_SUMMARY";		
	}

    //Funcion para Set la ciudad
    function setCity($city) {
		//Si esta establecida
		if($city != "") {
			//Asigna la informacion
			$this->city->ID = $city;
			//Verifica la informacion
			$this->city->__getInformation();
			//Si no hubo error
			if($this->city->nerror == 0) {
				//Asigna el valor
				$this->CITY_ID = $city;
				//Genera error
				$this->nerror = 0;
				$this->error = "";
			}
			else {
				//Asigna valor por defecto
				$this->CITY_ID = "NULL";
				//Genera error
				$this->nerror = 20;
				$this->error = "City " . $_SESSION["NOT_REGISTERED"];
			}
		}
    }
	
    //Funcion para Get la ciudad
    function getCity() {
		if($this->CITY_ID != "") {
			//Asigna el valor del escenario
			$this->CITY_ID = $this->city->ID;
			//Busca la informacion
			$this->city->__getInformation();
		}
    }	

    //Funcion para Set el tipo de pago del cliente
    function setClientType($type) {
        //Asigna la informacion
        $this->client_type->ID = $type;
        //Verifica la informacion
        $this->client_type->__getInformation();
        //Si no hubo error
        if($this->client_type->nerror == 0) {
            //Asigna el valor
            $this->PAYMENT_TYPE_ID = $type;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->PAYMENT_TYPE_ID = "0";
            //Genera error
            $this->nerror = 20;
            $this->error = "Tipo de pago de cliente " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el tipo de pago del cliente
    function getClientType() {
        //Asigna el valor del escenario
        $this->PAYMENT_TYPE_ID = $this->client_type->ID;
        //Busca la informacion
        $this->client_type->__getInformation();
    }	

    //Funcion para Set el tipo de pago del cliente
    function setClientPaymentType($type) {
        //Asigna la informacion
        $this->client_payment->ID = $type;
        //Verifica la informacion
        $this->client_payment->__getInformation();
        //Si no hubo error
        if($this->client_payment->nerror == 0) {
            //Asigna el valor
            $this->CLIENT_PAYMENT_TYPE_ID = $type;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->CLIENT_PAYMENT_TYPE_ID = "0";
            //Genera error
            $this->nerror = 20;
            $this->error = "Tipo de pago de cliente " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el tipo de pago del cliente
    function getClientPaymentType() {
        //Asigna el valor del escenario
        $this->CLIENT_PAYMENT_TYPE_ID = $this->client_payment->ID;
        //Busca la informacion
        $this->client_payment->__getInformation();
    }	

	//Funcion para obtener la informacion del cliente
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
			$this->setCity($this->CITY_ID);
			$this->setClientType($this->PAYMENT_TYPE_ID);
			$this->setClientPaymentType($this->CLIENT_PAYMENT_TYPE_ID);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}		
	
	//Verifica el cliente por mail
	function getInfoByMail() {
		//Arma la sentencia SQL
		$this->sql = "SELECT ID FROM $this->table WHERE EMAIL = " . $this->_checkDataType("EMAIL");
		//Obtiene los resultados
		$row = $this->__getData();
		//Valida el resultado
		if(!$row) {
			//Genera el error
			$this->nerror = 10;
			$this->error = $_SESSION["NOT_REGISTERED"];
		}
		else {
			//Asigna los atributos
			$this->ID = $row[0];
			//Obtiene la informacion
			$this->__getInformation();
		}
	}

	//Verifica el cliente por default
	function getInfoByDefault() {
		//Arma la sentencia SQL
		$this->sql = "SELECT ID FROM $this->table WHERE CLIENT_NAME = 'NO DEFINIDO'";
		//Obtiene los resultados
		$row = $this->__getData();
		//Valida el resultado
		if(!$row) {
			//Genera el error
			$this->nerror = 10;
			$this->error = $_SESSION["NOT_REGISTERED"];
		}
		else {
			//Asigna los atributos
			$this->ID = $row[0];
			//Obtiene la informacion
			$this->__getInformation();
		}
	}

	//Funcion que despliega los valores en una categoria
	function showOptionList($tabs = 8,$selected = "", $payment = false) {
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT DISTINCT CLIENT_ID, CLIENT_NAME, COUNTRY, CLIENT_PAYMENT_TYPE_ID, CLIENT_PAYMENT_TYPE FROM $this->view WHERE IS_BLOCKED = FALSE";
		//Verifica el acceso del usuario
		if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "CL") {
			$this->sql .= " AND CLIENT_ID = '" . $_SESSION["vtappcorp_referenceid"] . "'";
		}
		else if(substr($_SESSION["vtappcorp_useraccess"],0,2) == "AL") {
			$this->sql .= " AND PARTNER_ID = '" . $_SESSION["vtappcorp_referenceid"] . "'";
		}
		//Completa la sentencia SQL
		$this->sql .= " ORDER BY 2"; 
		//Variable a retornar
		$return = "";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
			$pay = $row[3] == 1 ? "off" : "on";
			$opt = $payment ? $row[4] : $row[2];
			//Si la opcion se encuentra seleccionada
			if($row[0] == $selected)
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' selected data-optionpy='" . $pay . "'>" . $row[1] . " ($opt)</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' data-optionpy='" . $pay . "'>" . $row[1] . " ($opt)</option>\n";
		}
		//Retorna
		return $return;
	}

	//Funcion para buscar un empleado por otra informacion
    function getInformationByOtherInfo($field = "CLIENT_NAME") {
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

	//Funcion para contar los clientes
	function getTotalCount() {
		//Arma la sentencia SQL
		$this->sql = "SELECT COUNT(ID) FROM $this->table WHERE IS_BLOCKED = FALSE";
        //Obtiene los resultados
        $row = $this->__getData();
		//Numero a retornar
		$return = 0;
        //Registro existe
        if($row)
			$return = $row[0];
			
		return $return;	
	}
	
	//Funcion que activa o habilita a un empleado
	function activate($activate) {
		//Ajusta la informacion
		$this->IS_BLOCKED = ($activate == "true") ? "0" : "1";
		//Realiza la actualizacion
		parent::_modify();
		//Verifica si no hubo error
		if($this->nerror > 0) {
			return false;
		}
		//Retorna 
		return true;
	}

	function dataForm($action, $tabs = 5) {
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
		$stabs = "";
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array("", "", 
								"", "disabled", "", "",
								"", "", "", "",
								"", "", 
								"", "", "");
			$actiontext = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newClient.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "disabled", 
								"", "disabled", "", "", 
								"", "", "", "",
								"", "", 
								"", "", "",
								"disabled", "disabled", "disabled", "disabled");
			$actiontext = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editClient.php";
		}
		else {
			$readonly = array("disabled", "disabled", 
							"disabled", "disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled", "disabled", 
							"disabled", "disabled", 
							"disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled", "disabled");
			$actiontext = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteClient.php";
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
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		$fields = ["CLIENT_ID", "CLIENT_NAME", "ADDRESS", "PHONE", "COUNTRY", "CONTACT_NAME", "EMAIL_CONTACT", "IS_BLOCKED", "COUNTRY_ID"];
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(" . $fields[0] . ") FROM $this->view $sWhere";
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
			for($i = 0;$i < count($aColumnsBD)-1;$i++) {
				if(strpos($aColumnsBD[$i],"_ID") !== false) {
					if($aColumnsBD[$i] == $fields[0]) {
						//Verifica el estado para activar o desactivar
						if($aRow[7])
							$activate = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["ACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',true,'" . $aRow[1] . "');\"><i class=\"fa fa-unlock\"></i></button>";
						else 
							$activate = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["DEACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',false,'" . $aRow[1] . "');\"><i class=\"fa fa-lock\"></i></button>";
						
						$view = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $aRow[$i] . "','view');\"><i class=\"fa fa-eye\"></i></button>";
						$edit = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('" . $aRow[$i] . "','edit');\"><i class=\"fa fa-pencil-square-o\"></i></button>";
						$delete = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('" . $aRow[$i] . "','delete');\"><i class=\"fa fa-trash\"></i></button>";
												
						$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $activate . $view . $edit . $delete . "</div></div>";
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
			}
			array_push($output['data'],$row);
		}
		return $output;
	}
	
	//Funcion que muestra la forma
	function showForm($action, $tabs = 5) {
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
		//Cadena a retornar
		$return = "";
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array("readonly=\"readonly\"", "", "", "", "", "", "", "", "", "", "", "disabled","disabled","");
			$action = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newClient.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "", "", "", "", "", "", "", "", "", "", "disabled", "disabled", "");			
			$action = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editClient.php";
		}
		else {
			$readonly = array("readonly=\"readonly\"", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled" );
			$viewData = ($action == "view");
			$action = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteClient.php";
		}
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		//variable a retornar
		$return = "$stabs<form id=\"frmClient\" name=\"frmClient\" role=\"form\">\n";
		//Muestra la GUI
		
		if($viewData) {
			$return .= $this->showField("ID", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		}
		else {
			$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"txtID\" id=\"txtID\" value=\"" . $this->ID . "\" required=\"required\" />\n";
			$cont++;
		}

		$return .= $this->showField("CLIENT_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("ADDRESS", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		
		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["ID_COUNTRY"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbCountry\" name=\"cbCountry\" " . $readonly[$cont++] . ">\n";
		$return .= $this->city->country->showOptionList(8,$showvalue ? $this->ID_COUNTRY : "");
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		if($this->CITY_ID == "") {
			$readonly[$cont] = "disabled";
		}
		
		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["CITY_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbCity\" name=\"cbCity\" " . $readonly[$cont++] . ">\n";
		$return .= $this->city->showOptionList(8,$showvalue ? $this->CITY_ID : "");
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= $this->showField("PHONE_1", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("PHONE_2", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("EMAIL_1", "$stabs\t", "fa fa-envelope", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("EMAIL_2", "$stabs\t", "fa fa-envelope", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("CONTACT_NAME_1", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("CONTACT_NAME_2", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		
		if($viewData) {
			$return .= $this->showField("REGISTERED_ON", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
			$return .= $this->showField("REGISTERED_BY", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		}
		else {
			$cont++;
			$cont++;
		}

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["IS_BLOCKED"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbBlocked\" name=\"cbBlocked\" " . $readonly[$cont++] . ">\n";
		$return .= "$stabs\t\t\t\t<option value=\"FALSE\"" . ($this->IS_BLOCKED ? "" : " selected") . ">" . $_SESSION["ACTIVE"] . "</option>\n";
		$return .= "$stabs\t\t\t\t<option value=\"TRUE\"" . ($this->IS_BLOCKED ? " selected" : "") . ">" . $_SESSION["IS_BLOCKED"] . "</option>\n";
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";
		
		$return .= "$stabs\t<p>" . $_SESSION["REQUIRED_FIELDS"] . "</p>\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfAction\" name=\"hfAction\" value=\"$action\" >\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfLinkAction\" name=\"hfLinkAction\" value=\"$link\" >\n";
		$return .= "$stabs</form>\n";
		//Retorna
		return $return;
	}

    //Funcion para generar el JSON 
    function showListJSON($ref) {
		$return = array();		
		//Arma la sentencia SQL
        $this->sql = "SELECT ID, CLIENT_NAME FROM " . $this->table . " WHERE IS_BLOCKED = FALSE ";
		//Agrega la referencia si hay
		if ($ref != "") {
			$this->sql .= "AND ID = '$ref' ";
		}
		else {
			//Variable a retornar
			$return = array(array("text" => $_SESSION["SELECT_OPTION"],
							"value" => "",
							"selected" => true));
		}
		//Completa la sentencia
		$this->sql .= "ORDER BY CLIENT_NAME";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("text" => $row[1],
							"value" => $row[0],
							"selected" => ($ref == $row[0]));
			array_push($return,$data);
		}
		//Retorna
		return $return;
    }	
	
}

?>
