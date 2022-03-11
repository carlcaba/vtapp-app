<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("partner.php");
require_once("client.php");
require_once("employee.php");

class partner_client extends table {
	var $partner;
	var $client;
	var $employee;
	var $view;
	var $myclients;
	var $assigned;
	
	//Constructor
	function __constructor($partner_client = "") {
		$this->partner_client($partner_client);
	}
	
	//Constructor anterior
	function partner_client($partner_client = '') {
		//Llamado al constructor padre
		parent::tabla("TBL_PARTNER_CLIENT");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->client = new client();
		$this->partner = new partner();
		$this->employee = new employee();
		//Vista
		$this->view = "VIE_PARTNER_CLIENT_SUMMARY";
		$this->myclients = "VIE_MY_CLIENTS";
		$this->assigned = "VIE_EMPLOYEE_ASSIGNED_SUMMARY";
	}

    //Funcion para Set el cliente
    function setClient($cliente) {
        //Asigna la informacion
        $this->client->ID = $cliente;
        //Verifica la informacion
        $this->client->__getInformation();
        //Si no hubo error
        if($this->client->nerror == 0) {
            //Asigna el valor
            $this->CLIENT_ID = $cliente;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->CLIENT_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Cliente " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el cliente
    function getClient() {
        //Asigna el valor del escenario
        $this->CLIENT_ID = $this->client->ID;
        //Busca la informacion
        $this->client->__getInformation();
    }

    //Funcion para Set el partner
    function setPartner($partner) {
        //Asigna la informacion
        $this->partner->ID = $partner;
        //Verifica la informacion
        $this->partner->__getInformation();
        //Si no hubo error
        if($this->partner->nerror == 0) {
            //Asigna el valor
            $this->PARTNER_ID = $partner;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->PARTNER_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Aliado " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el aliado
    function getPartner() {
        //Asigna el valor del escenario
        $this->PARTNER_ID = $this->partner->ID;
        //Busca la informacion
        $this->partner->__getInformation();
    }

    //Funcion para Set el empleado
    function setEmployee($employee) {
		if($employee != "") {
			//Asigna la informacion
			$this->employee->ID = $employee;
			//Verifica la informacion
			$this->employee->__getInformation();
			//Si no hubo error
			if($this->employee->nerror == 0) {
				//Asigna el valor
				$this->EMPLOYEE_ID = $employee;
				//Genera error
				$this->nerror = 0;
				$this->error = "";
			}
			else {
				//Asigna valor por defecto
				$this->EMPLOYEE_ID = "NULL";
				//Genera error
				$this->nerror = 20;
				$this->error = "Empleado " . $_SESSION["NOT_REGISTERED"];
			}
		}
    }
	
    //Funcion para Get el empleado
    function getEmployee() {
		if($this->employee->ID != "") {
			//Asigna el valor del escenario
			$this->EMPLOYEE_ID = $this->employee->ID;
			//Busca la informacion
			$this->employee->__getInformation();
		}
    }

	//Funcion para obtener la informacion del cliente aliado
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
			$this->setClient($this->CLIENT_ID);
			$this->setPartner($this->PARTNER_ID);
			$this->setEmployee($this->EMPLOYEE_ID);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}
	
	//Funcion para verificar duplicidad
	function checkDuplicateRecord($checkId = false) {
		$result = false;
		//Arma la sentencia SQL
		$this->sql = "SELECT PARTNER_CLIENT_ID FROM $this->table WHERE PARTNER_ID = " . $this->_checkDataType("PARTNER_ID") . 
					" AND CLIENT_ID = " . $this->_checkDataType("CLIENT_ID");
		//Verifica si hay un empleado asociado
		if($this->EMPLOYEE_ID != "") {
			$this->sql .= " AND EMPLOYEE_ID = " . $this->_checkDataType("EMPLOYEE_ID");
		}
		//Verifica si hay un Id asociado
		if($checkId) {
			$this->sql .= " AND PARTNER_CLIENT_ID <> " . $this->_checkDataType("PARTNER_CLIENT_ID");
		}
        //Obtiene los resultados
        $row = $this->__getData();
        //Registro existe
        if($row)
			$result = true;
		return $result;
	}

	//Funcion que despliega los valores en un option
	function showOptionList($tabs = 8,$selected = 0) {
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT A.PARTNER_ID, A.PARTNER_NAME, A.CLIENT_ID, A.CLIENT_NAME FROM $this->view WHERE A.IS_BLOCKED = FALSE";
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
		return $return == "" ? $this->sql : $return ;
	}

	//Funcion para mostrar los empleados
	function showEmployees($tabs = 8, $selected = 0) {
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT E.EMPLOYEE_ID, E.FULL_NAME FROM VIE_EMPLOYEE_SUMMARY E WHERE E.IS_BLOCKED = FALSE AND E.PARTNER_ID = " . $this->_checkDataType("PARTNER_ID") .
				" AND E.ACCESS_ID = 70 ORDER BY E.LAST_NAME, E.FIRST_NAME";
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
		return $return == "" ? $this->sql : $return ;
	}
	
	//Funcion para mostrar los clientes
	function showMyClients() {
		//Arma la sentencia SQL
		$this->sql = "SELECT * FROM $this->myclients WHERE PARTNER_ID = " . $this->_checkDataType("PARTNER_ID");
		//Variable a retornar
		$return = "";
		$counter = 0;
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$return .= "<tr>\n";
			//Nombre cliente
			$return .= "<td>$row[6]</td>\n";
			//Direccion
			$return .= "<td>$row[8]</td>\n";
			//Ciudad
			$return .= "<td>$row[9]</td>\n";
			//Mensajeros asignados
			$return .= "<td align=\"center\">$row[10]</td>\n";
			//Servicios activos
			$return .= "<td align=\"center\">$row[11]</td>\n";
			//Servicios pendientes
			$return .= "<td align=\"center\">$row[12]</td>\n";
			//Servicios completados
			$return .= "<td align=\"center\">$row[13]</td>\n";
			//Actions
			$asignar = "<button type=\"button\" class=\"btn btn-default\" title=\"" . $_SESSION["EMPLOYEE_ASSIGNED"] . "\" onclick=\"showAssign('" . $row[5] . "');\" id=\"btnAssign_$counter\" name=\"btnAssign_$counter\"><i class=\"fa fa-motorcycle\"></i></button>";
			$disabled = ($row[11] > 0 ? "" : "disabled");
			$service = "<button type=\"button\" $disabled class=\"btn btn-default\" title=\"" . $_SESSION["ASSIGN_SERVICE"] . "\" onclick=\"assign('" . $row[5] . "','service');\"><i class=\"fa fa-paper-plane\"></i></button>";
			$edit = "<button type=\"button\" class=\"btn btn-warning\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('" . $row[5] . "','edit');\"><i class=\"fa fa-pencil\"></i></button>";
			$delete = "<button type=\"button\" class=\"btn btn-danger\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('" . $row[5] . "','delete');\"><i class=\"fa fa-trash\"></i></button>";
			$action = "<div class=\"btn-toolbar\" role=\"toolbar\"><div class=\"btn-group\">" . $asignar . $service . $edit . $delete . "</div></div>";
			//acciones
			$return .= "<td>$action</td></tr>\n";
			//Incrementa contador
			$counter++;
		}
		echo $return;	
	}

	//Funcion para mostrar los empleados asignados
	function showAssignedEmployees() {
		//Flag
		$found = false;
		//Arma la sentencia SQL
		$this->sql = "SELECT * FROM $this->assigned WHERE CLIENT_ID = " . $this->_checkDataType("CLIENT_ID");
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("id" => $row[0],
							"name" => $row[1],
							"partner_id" => $row[6],
							"partner" => $row[7],
							"services" => $row[2],
							"registered_on" => $row[3],
							"registered_by" => $row[4]
							);
			array_push($return,$data);
			$found = true;
		}
		//Si se encuentran resultados
		if($found)
			return $return;	

		//Verifica la opción de empleados con partner asignado
		//Arma la sentencia SQL
		$this->sql = "SELECT * FROM $this->view WHERE CLIENT_ID = " . $this->_checkDataType("CLIENT_ID") . " AND (EMPLOYEE_ID IS NULL OR EMPLOYEE_ID = '')";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("id" => $row[0],
							"name" => $row[1],
							"partner_id" => $row[6],
							"partner" => $row[7],
							"services" => 0,
							"registered_on" => $row[3],
							"registered_by" => $row[4]
							);
			array_push($return,$data);
			$found = true;
		}
	}

	//Funcion que retorna el resumen por categoria
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		//	var fields = ["PARTNER_NAME", "IDENTIFICATION", "ADDRESS", "CITY", "CLIENT_NAME", "FULL_NAME", "PARTNER_CLIENT_ID"];
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(PARTNER_CLIENT_ID) FROM $this->view $sWhere";
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
			"data" => array());
		
		//Arma la sentencia SQL
		$this->sql = "SELECT " . str_replace(" , "," ",implode(", ",$aColumnsBD)) . " FROM $this->view $sWhere $sOrder $sLimit";
		//Recoge los resultados
		foreach($this->__getAllData() as $aRow) {
			//$row = array_fill_keys($aColumnsDB,'');
			$row = array_fill_keys($aColumnsBD,'');
			for($i = 0;$i < count($aColumnsBD)-1;$i++) {
				if(strpos($aColumnsBD[$i],"PARTNER_CLIENT_ID") !== false) {
					if($aColumnsBD[$i] == "PARTNER_CLIENT_ID") {
						//Verifica el estado para activar o desactivar
						if($aRow[7])
							$activate = "<button type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["ACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',true,'" . $aRow[1] . "');\"><i class=\"fa fa-unlock\"></i></button>";
						else 
							$activate = "<button type=\"button\" class=\"btn btn-primary\" title=\"" . $_SESSION["DEACTIVATE"] . "\" onclick=\"activate('" . $aRow[$i] . "',false,'" . $aRow[1] . "');\"><i class=\"fa fa-lock\"></i></button>";
						
						$view = "<button type=\"button\" class=\"btn btn-info\" title=\"" . $_SESSION["VIEW"] . "\" onclick=\"show('" . $aRow[$i] . "','view');\"><i class=\"fa fa-eye\"></i></button>";
						$edit = "<button type=\"button\" class=\"btn btn-warning\" title=\"" . $_SESSION["EDIT"] . "\" onclick=\"show('" . $aRow[$i] . "','edit');\"><i class=\"fa fa-pen-to-square\"></i></button>";
						$delete = "<button type=\"button\" class=\"btn btn-danger\" title=\"" . $_SESSION["DELETE"] . "\" onclick=\"show('" . $aRow[$i] . "','delete');\"><i class=\"fa fa-trash\"></i></button>";
												
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
		array_push($output['sql'],$this->sql);
		return $output;
	}
	
	//Funcion para buscar un aliado por cliente
    function getInformationByClient() {
        //Arma la sentencia SQL
        $this->sql = "SELECT PARTNER_CLIENT_ID FROM $this->view WHERE CLIENT_ID = " . $this->_checkDataType("CLIENT_ID");
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
			$readonly = array("readonly=\"readonly\"", "", "", "", "");
			$action = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newPartnerClient.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "", "", "", "disabled", "disabled", "", "");
			$action = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editPartnerClient.php";
		}
		else {
			$readonly = array("readonly=\"readonly\"", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled");
			$viewData = ($action == "view");
			$action = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deletePartnerClient.php";
		}
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		//variable a retornar
		$return = "$stabs<form id=\"frmPartnerClient\" name=\"frmPartnerClient\" role=\"form\">\n";
		//Muestra la GUI
		
		if($viewData) {
			$return .= $this->showField("ID", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		}
		else {
			$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"txtID\" id=\"txtID\" value=\"" . $this->ID . "\" required=\"required\" />\n";
			$cont++;
		}

		//Verifica si hay alguna referencia
		if($_SESSION["vtappcorp_referenceid"] != "") {
			if(substr($_SESSION['vtappcorp_useraccess'],0,2) == "CL") {
				$this->CLIENT_ID = $_SESSION["vtappcorp_referenceid"];
				$readonly[1] = "disabled";
				$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"cbClient\" id=\"cbClient\" value=\"" . $this->CLIENT_ID . "\" required=\"required\" />\n";
			}
			else if (substr($_SESSION['vtappcorp_useraccess'],0,2) == "AL") {
				$this->PARTNER_ID = $_SESSION["vtappcorp_referenceid"];				
				$readonly[2] = "disabled";
				$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"cbPartner\" id=\"cbPartner\" value=\"" . $this->PARTNER_ID . "\" required=\"required\" />\n";
			}
		}
		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["CLIENT_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbClient\" name=\"cbClient\" " . $readonly[$cont++] . ">\n";
		$return .= $this->client->showOptionList(9,$this->CLIENT_ID);
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["PARTNER_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbPartner\" name=\"cbPartner\" " . $readonly[$cont++] . ">\n";
		$return .= $this->partner->showOptionList(9,$this->PARTNER_ID);
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["EMPLOYEE_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbEmployee\" name=\"cbEmployee\" " . $readonly[$cont++] . ">\n";
		if($action == $_SESSION["MENU_NEW"]) {
			$return .= $this->employee->showOptionList(9,"");
		}
		else {
			$return .= $this->employee->showOptionList(9,$this->EMPLOYEE_ID);
		}
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		if($viewData) {
			$return .= $this->showField("REGISTERED_ON", "$stabs\t", "", "", $showvalue, "", false, "6,6,12", $readonly[$cont++]);
			$return .= $this->showField("REGISTERED_BY", "$stabs\t", "", "", $showvalue, "", false, "6,6,12", $readonly[$cont++]);
			$return .= $this->showField("MODIFIED_ON", "$stabs\t", "", "", $showvalue, "", false, "6,6,12", $readonly[$cont++]);
			$return .= $this->showField("MODIFIED_BY", "$stabs\t", "", "", $showvalue, "", false, "6,6,12", $readonly[$cont++]);
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
	
	function dataForm($action, $source, $tabs = 5) {
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array(	//ID
								"",
								//Deliver to
								"disabled", 
								//Vehicle
								"",
								//Partner
								"", 
								//Employee
								"");
			$actiontext = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newAssignService.php";
		}
		else if($action == "edit") {
			$readonly = array(	//ID
								"",
								//Deliver to
								"disabled", 
								//Vehicle
								"",
								//Partner
								"", 
								//Employee
								"");
			$actiontext = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editAssignService.php";
		}
		else {
			$readonly = array(	//ID
								"",
								//Deliver to
								"disabled", 
								//Vehicle
								"",
								//Partner
								"", 
								//Employee
								"");
			$actiontext = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteAssignService.php";
		}

		//Ajusta los campos de acuerdo a la fuente
		switch($source) {
			case "CLIENT":{
				$readonly[0] = "disabled";
				break;
			}
			case "PARTNER": {
				$readonly[2] = "disabled";
				break;
			}
		}
		//Variable a regresar
		$return = array("tabs" => $stabs,
						"readonly" => $readonly,
						"actiontext" => $actiontext,
						"link" => $link,
						"showvalue" => true,
						"icon" => "<i class=\"fa fa-motorcycle\"></i> ",
						"title" => "<span id=\"actionId\"> " . $actiontext . "</span> " . $_SESSION["ASSIGN_SERVICE"] . " <small>" . $_SESSION[$source] . "</small>");
		//Retorna
		return $return;
	}
	
	//Funcion para mostrar los aliados de un cliente
	function getMyPartners() {
		//Arma la sentencia SQL
		$this->sql = "SELECT DISTINCT PARTNER_ID, PARTNER_NAME, EMPLOYEE_ID FROM $this->view WHERE CLIENT_ID = " . $this->_checkDataType("CLIENT_ID");
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$data = array("id" => "'" . $row[0] . "'",
							"employee" => $row[2]);
			array_push($return,$data);
		}
		return $return;	
	}
	
}

?>