<?

// LOGICA ESTUDIO 2019

//Incluye las clases dependientes
require_once("table.php");
require_once("resources.php");
require_once("area.php");
require_once("access.php");
require_once("partner.php");
require_once("city.php");

class employee extends table {
	var $resources;
	var $view;
	var $area;
	var $access;
	var $partner;
	var $city;
	
	//Constructor
	function __constructor($employee = "") {
		$this->employee($employee);
	}
	
	//Constructor anterior
	function employee ($employee  = '') {
		//Llamado al constructor padre
		parent::table("TBL_EMPLOYEE");
		//Inicializa los atributos
		$this->ID = "UUID()";
		$this->REGISTERED_ON = "NOW()";
		$this->REGISTERED_BY = $_SESSION['vtappcorp_userid'];
		$this->IDENTIFICATION = $employee;
		//Especifica los valores unicos
		$this->_addUniqueColumn("ID");
		//Relaciones con otras clases
		$this->resources = new resources();
		$this->area = new area();
		$this->access = new access();
		$this->partner = new partner();
		$this->city = new city();
		$this->view = "VIE_EMPLOYEE_SUMMARY";		
	}

    //Funcion para Set el asociado
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
            $this->error = "Asociado " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el asociado
    function getPartner() {
        //Asigna el valor del escenario
        $this->PARTNER_ID = $this->partner->ID;
        //Busca la informacion
        $this->partner->__getInformation();
    }


    //Funcion para Set el area
    function setArea($area) {
        //Asigna la informacion
        $this->area->ID = $area;
        //Verifica la informacion
        $this->area->__getInformation();
        //Si no hubo error
        if($this->area->nerror == 0) {
            //Asigna el valor
            $this->AREA_ID = $area;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->AREA_ID = "NULL";
            //Genera error
            $this->nerror = 20;
            $this->error = "Area " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el area
    function getArea() {
        //Asigna el valor del escenario
        $this->AREA_ID = $this->area->ID;
        //Busca la informacion
        $this->area->__getInformation();
    }
	
    //Funcion para Set el acceso
    function setAccess($access) {
        //Asigna la informacion
        $this->access->ID = $access;
        //Verifica la informacion
        $this->access->__getInformation();
        //Si no hubo error
        if($this->access->nerror == 0) {
            //Asigna el valor
            $this->ACCESS_ID = $access;
            //Genera error
            $this->nerror = 0;
            $this->error = "";
        }
        else {
            //Asigna valor por defecto
            $this->ACCESS_ID = "";
            //Genera error
            $this->nerror = 20;
            $this->error = "Category " . $_SESSION["NOT_REGISTERED"];
        }
    }
	
    //Funcion para Get el acceso
    function getAccess() {
        //Asigna el valor del escenario
        $this->ACCESS_ID = $this->access->ID;
        //Busca la informacion
        $this->access->__getInformation();
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
	
	//Funcion para obtener la informacion de la categoria
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
			$this->setArea($this->AREA_ID);
			$this->setPartner($this->PARTNER_ID);
			//Limpia el error
			$this->error = "";
			$this->nerror = 0;
		}
	}
	
	//Funcion para mostrar los empleados
	function showEmployees() {
		//Arma la sentencia SQL
		$this->sql = "SELECT EMPLOYEE_ID, FULL_NAME FROM $this->view WHERE PARTNER_ID = " . $this->_checkDataType("PARTNER_ID");
		//Variable a retornar
		$return = array();
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$selected = $row[0] == $this->ID;
			$data = array("id" => $row[0],
							"name" => $row[1],
							"selected" => $selected);
			array_push($return,$data);
		}
		return $return;	
	}
	
	
	//Funcion que despliega los valores en un empleado
	function showOptionList($tabs = 8,$selected = "", $lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT DISTINCT EMPLOYEE_ID, FULL_NAME, AREA_NAME, PARTNER_ID, PARTNER_NAME FROM $this->view WHERE IS_BLOCKED = FALSE AND (LANGUAGE_ID = $lang OR LANGUAGE_ID IS NULL) ORDER BY 2"; 
		//Variable a retornar
		$return = "$stabs<option value=\"\">" . $_SESSION["SELECT_OPTION"] . "</option>\n";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$area = "";
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
			if($row[2]!="") {
				$area = " (" . $row[2] . ")";
			}
			else {
				$area = " (" . $row[4] . ")";
			}
			//Si la opcion se encuentra seleccionada
			if($row[0] == $selected)
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' data-partnerid=\"$row[3]\" selected>" . $row[1] . $area . "</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' data-partnerid=\"$row[3]\">" . $row[1] . $area . "</option>\n";
		}
		//Retorna
		return $return;
	}

	//Funcion que despliega los valores en un empleado
	function showOptionListWithVehicle($tabs = 8,$selected = "", $lang = 0) {
		//Verifica el lenguaje
		if($lang == 0) {
			//Lenguaje establecido
			$lang = $_SESSION["LANGUAGE"];
		}
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Arma la sentencia SQL
		$this->sql = "SELECT DISTINCT E.EMPLOYEE_ID, E.FULL_NAME, E.AREA_NAME, E.PARTNER_ID, E.PARTNER_NAME," .
					"V.VEHICLE_ID, V.VEHICLE_TYPE_ID, V.VEHICLE_TYPE_NAME, V.PLATE " . 
				"FROM $this->view E LEFT JOIN VIE_VEHICLE_SUMMARY V ON (E.EMPLOYEE_ID = V.EMPLOYEE_ID AND V.LANGUAGE_ID = E.LANGUAGE_ID) " .
				"WHERE E.IS_BLOCKED = FALSE AND (E.LANGUAGE_ID = $lang OR E.LANGUAGE_ID IS NULL) ORDER BY 2"; 
		//Variable a retornar
		$return = "$stabs<option value=\"\">" . $_SESSION["SELECT_OPTION"] . "</option>\n";
		//Recorre los valores
		foreach($this->__getAllData() as $row) {
			$area = "";
			$vehicle = "";
            if(!mb_detect_encoding($row["1"], 'utf-8', true)) {
                //Guarda la informacion en GLOBALS
                $row[1] = utf8_encode($row[1]);
            }
			if($row[2] != "") {
				$area = " (" . $row[2] . ")";
			}
			else {
				$area = " (" . $row[4] . ")";
			}
			$area .=  " " . $row[7] . " " . $row[8];
			$vehicle = "data-vehicletype=\"" . $row[6] . "\" data-vehicleid=\"" . $row[5] . "\"";
			//Si la opcion se encuentra seleccionada
			if($row[0] == $selected)
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' data-partnerid=\"$row[3]\" $vehicle selected>" . $row[1] . $area . "</option>\n";
			else
				//Ajusta al diseño segun GUI
				$return .= "$stabs<option value='" . $row[0] . "' data-partnerid=\"$row[3]\" $vehicle>" . $row[1] . $area . "</option>\n";
		}
		//Retorna
		return $return;
	}
	

	//Funcion para buscar un empleado por otra informacion
    function getInformationByOtherInfo($field = "EMAIL") {
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
	
	//Funcion para buscar un empleado por identificacion
    function getInformationByIdentification() {
        //Arma la sentencia SQL
        $this->sql = "SELECT ID FROM $this->table WHERE IDENTIFICATION LIKE '%" . $this->IDENTIFICATION . "' LIMIT 1";
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
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array("", "", "",
								"", "", "",
								"", "disabled", "",
								"", "", 
								"", "", "", "", "");
			$actiontext = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newEmployee.php";
		}
		else if($action == "edit") {
			$readonly = array("", "", "",
								"disabled", "disabled", "", 
								"disabled", "", "",
								"", "", 
								"", "", "", "", "",
								"disabled", "disabled", "disabled", "disabled");
			$actiontext = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editEmployee.php";
		}
		else {
			$readonly = array("disabled", "disabled", 
							"disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled", 
							"disabled", "disabled", 
							"disabled", "disabled", "disabled", "disabled", "disabled", "disabled",
							"disabled", "disabled", "disabled", "disabled");
			$actiontext = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteEmployee.php";
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
		
	//Funcion que retorna el resumen por empleado
	function showSummary($aColumnsBD,$sWhere,$sOrder,$sLimit) {
		//var fields = ["EMPLOYEE_ID", "FULL_NAME", "PARTNER_NAME", "AREA_NAME", "ADDRESS", "CELLPHONE", "CITY", "EMAIL", "VEHICLES", "IS_BLOCKED"];
		//Cuenta el total de filas
		$this->sql = "SELECT COUNT(EMPLOYEE_ID) FROM $this->view $sWhere";
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
		$this->sql = "SELECT " . str_replace(" , "," ",implode(", ",$aColumnsBD)) . " FROM $this->view $sWhere $sOrder $sLimit";
		$output["sql"] = $this->sql;
		//Recoge los resultados
		foreach($this->__getAllData() as $aRow) {
			//$row = array_fill_keys($aColumnsDB,'');
			$row = array_fill_keys($aColumnsBD,'');
			for($i = 0;$i < count($aColumnsBD)-1;$i++) {
				if(strpos($aColumnsBD[$i],"_ID") !== false) {
					if($aColumnsBD[$i] == "EMPLOYEE_ID") {
						//Verifica el estado para activar o desactivar
						if($aRow[8])
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
		return $output;
	}
	
	//Funcion que muestra la forma
	function showForm($action, $tabs = 5) {
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
		//Cadena a retornar
		$return = "";
		$valcode = "";
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es nuevo registro o es edicion
		if($action == "new") {
			$readonly = array("readonly=\"readonly\"", "", "", "", "readonly=\"readonly\"", "", "", "", "", "", "disabled", "disabled", "");
			$action = $_SESSION["MENU_NEW"];
			$link = "core/actions/_save/__newEmployee.php";
		}
		else if($action == "edit") {
			$readonly = array("readonly=\"readonly\"", "", "", "readonly=\"readonly\"", "readonly=\"readonly\"", "", "", "", "", "", "disabled", "disabled", "");
			$action = $_SESSION["EDIT"];
			$link = "core/actions/_save/__editEmployee.php";
		}
		else {
			$readonly = array("readonly=\"readonly\"", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled", "disabled" );
			$viewData = ($action == "view");
			$action = ($action == "view") ? $_SESSION["INFORMATION_OF"] : $_SESSION["DELETE"];
			$link = "core/actions/_save/__deleteEmployee.php";
		}
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		//variable a retornar
		$return = "$stabs<form id=\"frmEmployee\" name=\"frmEmployee\" role=\"form\">\n";
		//Muestra la GUI
		
		if($viewData) {
			$return .= $this->showField("ID", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		}
		else {
			$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"txtID\" id=\"txtID\" value=\"" . $this->ID . "\" required=\"required\" />\n";
			$cont++;
		}

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["AREA_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbArea\" name=\"cbArea\" " . $readonly[$cont++] . ">\n";
		$return .= $this->area->showOptionList(8,$showvalue ? $this->AREA_ID : "");
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["ACCESS_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbAccess\" name=\"cbAccess\" " . $readonly[$cont++] . ">\n";
		$return .= $this->access->showOptionList(8,$showvalue ? $this->ACCESS_ID : "");
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= $this->showField("ID_USER", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("CODE", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("FIRST_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("LAST_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("IDNUMBER", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("EMAIL", "$stabs\t", "fa fa-envelope", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		$return .= $this->showField("COSTCENTER", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $readonly[$cont++]);
		
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

	//Funcion que muestra la forma
	function showProfileForm($disabled, $action = "edit", $tabs = 5) {
		$resources = new resources();
		//Verifica los recursos
		$this->completeResources();
		//Cadena a retornar
		$return = "";
		$valcode = "";
		//Arma la cadena con los tabs requeridos
		for($i=0;$i<$tabs;$i++)
			$stabs .= "\t";
		//Verifica si es edicion
		$action = $_SESSION["EDIT"];
		$link = "core/actions/_save/__editProfile.php";
		
		//Inicia el contador
		$cont = 0;
		$showvalue = true;
		//variable a retornar
		$return = "";
		//Muestra la GUI
		$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"txtID\" id=\"txtID\" value=\"" . $this->ID . "\" required=\"required\" />\n";
		$return .= "$stabs\t\t\t\t<input type=\"hidden\" name=\"txtID_USER\" id=\"txtID_USER\" value=\"" . $this->ID_USER . "\" required=\"required\" />\n";

		$return .= $this->showField("FIRST_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $disabled);
		$return .= $this->showField("LAST_NAME", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $disabled);
		$return .= $this->showField("IDNUMBER", "$stabs\t", "", "", $showvalue, "", false, "9,9,12", $disabled);
		$return .= $this->showField("EMAIL", "$stabs\t", "fa fa-envelope", "", $showvalue, "", false, "9,9,12", $disabled);
		
		$return .= "$stabs\t<div class=\"form-group\">\n";
		$return .= "$stabs\t\t<label>" . $this->arrColComments["AREA_ID"] . " *</label>\n";
		$return .= "$stabs\t\t\t<select class=\"form-control\" id=\"cbArea\" name=\"cbArea\" $disabled>\n";
		$return .= $this->area->showOptionList(8,$showvalue ? $this->AREA_ID : "");
		$return .= "$stabs\t\t\t</select>\n";
		$return .= "$stabs\t</div>\n";

		$return .= "$stabs\t<p>" . $_SESSION["REQUIRED_FIELDS"] . "</p>\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfLinkAction\" name=\"hfLinkAction\" value=\"$link\" >\n";
		$return .= "$stabs\t<input type=\"hidden\" id=\"hfType\" name=\"hfType\" value=\"employee\" >\n";

		//Retorna
		return $return;
	}

	
}

?>
